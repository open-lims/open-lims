<?php
/**
 * @package data
 * @version 0.4.0.0
 * @author Roman Quiring <quiring@open-lims.org>
 * @author Roman Konertz <konertz@open-lims.org>
 * @copyright (c) 2008-2013 by Roman Konertz, Roman Quiring
 * @license GPLv3
 * 
 * This file is part of Open-LIMS
 * Available at http://www.open-lims.org
 * 
 * This program is free software;
 * you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation;
 * version 3 of the License.
 * 
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
 * See the GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Parameter AJAX IO Class
 * @package data
 */
class ParameterAjax
{
	/**
	 * @param string $action
	 * @return string
	 */
	public static function get_data_browser_link_html_and_button_handler($action) 
	{
		global $regional;
		
		$html;
		$html_caption;
		$button_handler;
		$button_handler_caption;
		$template;
		$paramquery = $_GET;	
		unset($paramquery['run']);
		switch($action):
			case "parameter_delete":
				if(isset($_POST['sure']))
				{
					self::delete($_POST['parameter_id']);
				}
				else
				{
					$template = new HTMLTemplate("data/parameter_delete_window.html");
					$button_handler_template = new JSTemplate("data/js/parameter_delete_window.js");
					$button_handler_template->set_var("session_id", $_GET['session_id']);
					$button_handler_template->set_var("parameter_id", $_POST['parameter_id']);
					$button_handler = $button_handler_template->get_string();
					$button_handler_caption = "Delete";
					$html_caption = "Delete Parameter";
					$html = $template->get_string();
				}
			break;
			case "permission":
				require_once("data.ajax.php");
				if(isset($_POST['permissions']))
				{
					$success = DataAjax::change_permission(json_decode($_POST['permissions']), "Parameter");
					return $success;
				}
				else
				{
					$permission = DataAjax::permission_window();
					$button_handler_template = new JSTemplate("data/js/parameter_permission_window.js");
					$button_handler_template->set_var("session_id", $_GET['session_id']);
					$button_handler_template->set_var("parameter_id", $_POST['parameter_id']);
					$button_handler = $button_handler_template->get_string();
					$button_handler_caption = "Change";
					$html_caption = "Change permission";
					$html = $permission;	
				}
			break;
		endswitch;
		$array = array("content"=>$html , "content_caption"=>$html_caption , "handler"=>$button_handler , "handler_caption"=>$button_handler_caption);
		return json_encode($array);
	}
	
/**
	 * @param string $json_column_array
	 * @param string $json_argument_array
	 * @param string $get_array
	 * @param string $css_page_id
	 * @param string $css_row_sort_id
	 * @param string $entries_per_page
	 * @param string $page
	 * @param string $sortvalue
	 * @param string $sortmethod
	 * @return string
	 * @throws DataSecurityAccessDeniedException
	 * @throws ParameterIDMissingException
	 */
	public static function list_versions($json_column_array, $json_argument_array, $get_array, $css_page_id, $css_row_sort_id, $entries_per_page, $page, $sortvalue, $sortmethod)
	{
		if ($get_array)
		{
			$_GET = unserialize($get_array);	
		}
		
		$argument_array = json_decode($json_argument_array);
		$parameter_id = $argument_array[1];
		
		if (is_numeric($parameter_id))
		{
			$parameter = Parameter::get_instance($parameter_id);
			
			if ($parameter->is_read_access())
			{
				$list_request = new ListRequest_IO();
				$list_request->set_column_array($json_column_array);
			
				if (!is_numeric($entries_per_page) or $entries_per_page < 1)
				{
					$entries_per_page = 20;
				}
					
				$list_array = Data_Wrapper::list_parameter_versions($parameter_id, $sortvalue, $sortmethod, ($page*$entries_per_page)-$entries_per_page, ($page*$entries_per_page));
			
				if (is_array($list_array) and count($list_array) >= 1)
				{
					foreach($list_array as $key => $value)
					{
						$paramquery = $_GET;
						$paramquery['action'] = "parameter_detail";
						$paramquery['version'] = $list_array[$key]['internal_revision'];
						$params = http_build_query($paramquery,'','&#38;');
						
						$list_array[$key]['symbol']['link']		= $params;
						$list_array[$key]['symbol']['content'] 	= "<img src='images/fileicons/16/unknown.png' alt='N' border='0' />";
					
						$tmp_name = $list_array[$key]['name'];
						unset($list_array[$key]['name']);
						$list_array[$key]['name']['link']		= $params;
						$list_array[$key]['name']['content'] 	= $tmp_name;
						
						$datetime_handler = new DatetimeHandler($list_array[$key]['datetime']);
						$list_array[$key]['datetime'] = $datetime_handler->get_datetime(false);
						
						$user = new User($list_array[$key]['owner_id']);
						$list_array[$key]['user'] = $user->get_full_name(false);
						
						$parameter_version_obj = clone $parameter;
						$parameter_version_obj->open_internal_revision($value['internal_revision']);
						if ($parameter_version_obj->is_current() == true)
						{
							$list_array[$key]['version'] = $parameter_version_obj->get_version()." <span class='italic'>current</span>";
						}
						else
						{
							$list_array[$key]['version'] = $parameter_version_obj->get_version();
						}
					}
				}
				else
				{
					$list_request->empty_message("<span class='italic'>No results found!</span>");
				}
				
				$list_request->set_array($list_array);
			
				return $list_request->get_page($page);
			}
			else
			{
				throw new DataSecurityAccessDeniedException();
			}
		}
		else
		{
			throw new ParameterIDMissingException();
		}
	}
	
	/**
	 * @param string $json_argument_array
	 * @throws ParameterIDMissingException
	 */
	public static function count_versions($json_argument_array)
	{
		$argument_array = json_decode($json_argument_array);
		$parameter_id = $argument_array[1];
		
		if (is_numeric($parameter_id))
		{
			return Data_Wrapper::count_parameter_versions($parameter_id);
		}
		else
		{
			throw new ParameterIDMissingException();
		}
	}
	
	/**
	 * @todo business logic exceptions
	 * @param integer $folder_id
	 * @param integer $type_id
	 * @param string $parameter_array
	 * @param string $get_array
	 * @return string
	 */
	public static function add_as_item($folder_id, $type_id, $limit_id, $parameter_array, $get_array)
	{
		global $user, $transaction;
		
		$parent_folder = Folder::get_instance($folder_id);
		
		if ($parent_folder->is_write_access())
		{
			$transaction_id = $transaction->begin();
			
			$parameter_array = json_decode($parameter_array, true);

			$parameter = ParameterTemplateParameter::get_instance(null);
			$parameter_add_successful = $parameter->create($folder_id, $user->get_user_id(), $type_id, $limit_id, $parameter_array);
			
			if ($parameter_add_successful)
			{				
				$item_id = $parameter->get_item_id();
				
				$item_add_event = new ItemAddEvent($item_id, unserialize($get_array), null);
				$event_handler = new EventHandler($item_add_event);
				if ($event_handler->get_success() == true)
				{
					if ($transaction_id != null)
					{
						$transaction->commit($transaction_id);
					}
					return "1";
				}
				else
				{
					if ($transaction_id != null)
					{
						$transaction->rollback($transaction_id);
					}
					throw new BaseException();
				}
			}
			else
			{
				if ($transaction_id != null)
				{
					$transaction->rollback($transaction_id);
				}
				throw new BaseException();
			}
		}
		else
		{
			throw new DataSecurityAccessDeniedException();
		}
	}

	public static function update($parameter_id, $parameter_array,  $limit_id, $major, $current)
	{
		if (is_numeric($parameter_id))
		{
			$parameter = ParameterTemplateParameter::get_instance($parameter_id);
			$parameter_array = json_decode($parameter_array, true);

			$parameter->update($parameter_array, $limit_id, null, $major, $current);
			return "1";
		}
		else
		{
			throw new ParameterIDMissingException();
		}
	}
	
	public static function get_limits($parameter_template_id, $parameter_limit_id)
	{
		if (is_numeric($parameter_template_id) and is_numeric($parameter_limit_id))
		{
			$parameter_template = new ParameterTemplate($parameter_template_id);
			
			return json_encode($parameter_template->get_limits($parameter_limit_id));
		}
		else
		{
			throw new ParameterIDMissingException();
		}
	}
	
	public static function get_methods()
	{
		return json_encode(ParameterMethod::list_methods());
	}
	
	/**
	 * @param integer $parameter_id
	 * @return string
	 * @throws DataSecurityAccessDeniedException
	 */
	private static function delete($parameter_id)
	{
		$parameter = Parameter::get_instance($parameter_id);
		if ($parameter->is_delete_access())
		{
			$parameter->delete();
			return "1";
		}
		else
		{
			throw new DataSecurityAccessDeniedException();
		}
	}
}
?>