<?php
/**
 * @package data
 * @version 0.4.0.0
 * @author Roman Konertz <konertz@open-lims.org>
 * @copyright (c) 2008-2012 by Roman Konertz
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
 * Value IO Class
 * @package data
 */
class ValueIO
{		
	/**
	 * @throws ValueIDMissingException
	 * @throws DataSecurityAccessDeniedException
	 */
	public static function detail()
	{
		global $user;
		
		if ($_GET[value_id])
		{
			$value = Value::get_instance($_GET[value_id]);
	
			if ($value->is_read_access())
			{								
				if ($_GET[version] and is_numeric($_GET[version]))
				{
					$value->open_internal_revision($_GET[version]);
				}
				
				if ($value->get_type_id() == 2)
				{
					$template = new HTMLTemplate("data/value_project_description_detail.html");
				
					$value_version_array = $value->get_value_internal_revisions();
						
					if (is_array($value_version_array) and count($value_version_array) > 0)
					{		
						$result = array();
						$counter = 1;
					
						$result[0][version] = 0;
						$result[0][text] = "----------------------------------------------";
						
						foreach($value_version_array as $key => $fe_value)
						{
							$value_version = Value::get_instance($_GET[value_id], true);
							$value_version->open_internal_revision($fe_value);
							
							$result[$counter][version] = $value_version->get_internal_revision();
							$result[$counter][text] = "Version ".$value_version->get_version()." - ".$value_version->get_version_datetime();
							$counter++;
						}
						$template->set_var("version_option",$result);
					}
					
					$result = array();
					$counter = 0;
					
					foreach($_GET as $key => $fe_value)
					{
						if ($key != "version")
						{
							$result[$counter][value] = $fe_value;
							$result[$counter][key] = $key;
							$counter++;
						}
					}
					
					$template->set_var("get",$result);
					
					$template->set_var("version",$value->get_version());
					$template->set_var("version_datetime",$value->get_datetime());
				
					$paramquery = $_GET;
					$paramquery[action] = "permission";
					$params = http_build_query($paramquery,'','&#38;');	
					$template->set_var("change_permission_params",$params);
					
					if ($value->is_control_access() == true or $value->get_owner_id() == $user->get_user_id())
					{
						$template->set_var("change_permission",true);
					}
					else
					{
						$template->set_var("change_permission",false);
					}
				
					if ($value->is_write_access() == true or $value->get_owner_id() == $user->get_user_id())
					{
						$template->set_var("write_permission",true);
					}
					else
					{
						$template->set_var("write_permission",false);
					}
				
					$paramquery = $_GET;
					$paramquery[action] = "value_history";
					$params = http_build_query($paramquery,'','&#38;');	
					
					$template->set_var("version_list_link",$params);
				
					$paramquery = $_GET;
					$paramquery[nextpage] = "1";
					$paramquery[version] = $value->get_internal_revision();
					$params = http_build_query($paramquery,'','&#38;');
					
					$template->set_var("params", $params);
					
					$template->set_var("title", $value->get_type_name());
					
					$value_string = unserialize($value->get_value());
					
					$template->set_var("desc", $value_string);
					$template->set_var("error","");

					$template->output();
				}
				else
				{
					$template = new HTMLTemplate("data/value_detail.html");
				
					$value_version_array = $value->get_value_internal_revisions();
						
					if (is_array($value_version_array) and count($value_version_array) > 0)
					{		
						$result = array();
						$counter = 1;
					
						$result[0][version] = 0;
						$result[0][text] = "----------------------------------------------";
						
						foreach($value_version_array as $key => $fe_value)
						{
							$value_version = Value::get_instance($_GET[value_id], true);
							$value_version->open_internal_revision($fe_value);
							
							$result[$counter][version] = $value_version->get_internal_revision();
							$result[$counter][text] = "Version ".$value_version->get_version()." - ".$value_version->get_version_datetime();
							$counter++;
						}
						$template->set_var("version_option",$result);
					}
					
					$result = array();
					$counter = 0;
					
					foreach($_GET as $key => $fe_value)
					{
						if ($key != "version")
						{
							$result[$counter][value] = $fe_value;
							$result[$counter][key] = $key;
							$counter++;
						}
					}
					
					$template->set_var("get",$result);
					
					$template->set_var("version",$value->get_version());
					$template->set_var("version_datetime",$value->get_datetime());
				
					$paramquery = $_GET;
					$paramquery[action] = "permission";
					$params = http_build_query($paramquery,'','&#38;');	
					$template->set_var("change_permission_params",$params);
				
					if ($value->is_control_access() == true or $value->get_owner_id() == $user->get_user_id())
					{
						$template->set_var("change_permission",true);
					}
					else
					{
						$template->set_var("change_permission",false);
					}
					
					if ($value->is_write_access() == true or $value->get_owner_id() == $user->get_user_id())
					{
						$template->set_var("write_permission",true);
					}
					else
					{
						$template->set_var("write_permission",false);
					}
				
					$paramquery = $_GET;
					$paramquery[action] = "value_history";
					$params = http_build_query($paramquery,'','&#38;');	
					
					$template->set_var("version_list_link",$params);

					$paramquery = $_GET;
					unset($paramquery[action]);
					unset($paramquery[value_id]);
					$params = http_build_query($paramquery);
					
					$template->set_var("retrace", "index.php?".$params);
					
					$template->set_var("session_id", $_GET['session_id']);
					$template->set_var("internal_revision", $value->get_internal_revision());
					$template->set_var("value_id", $_GET['value_id']);
											
					$template->set_var("title", $value->get_name());
					
					require_once("value_form.io.php");
					$value_form_io = new ValueFormIO($_GET['value_id']);
					$value_form_io->set_field_class("DataValueUpdateValues");
					
					$template->set_var("value",$value_form_io->get_content());
					$template->set_var("autofield_string",$value->get_autofield_array());
					
					$template->output();
				}
			}
			else
			{
				throw new DataSecurityAccessDeniedException();
			}
		}
		else
		{
			throw new ValueIDMissingException();		
		}
	}	

	/**
	 * @throws FolderIDMissingException
	 */
	public static function add_value_item($type_array, $category_array, $holder_class, $holder_id, $position_id)
	{
		global $user;
		
		if (class_exists($holder_class))
		{
			$item_holder = new $holder_class($holder_id);
			
			if ($item_holder instanceof ItemHolderInterface)
			{
				$folder_id = $item_holder->get_item_holder_value("folder_id", $position_id);
			}
		}
		
		if (is_numeric($folder_id))
		{
			if (count($type_array) != 1 and !$_POST[type_id])
			{
				$result = array();
				$counter = 0;
				
				if (count($type_array) == 0)
				{
					$value_obj = Value::get_instance(null);
					$value_type_array = ValueType::list_entries();
					
					foreach($value_type_array as $key => $value)
					{
						$value_type = new ValueType($value);
						$result[$counter][value] = $value;
						$result[$counter][content] = $value_type->get_name();
						
						$counter++;
					}
				}
				else
				{
					foreach($type_array as $key => $value)
					{
						$value_type = new ValueType($value);
						$result[$counter][value] = $value;
						$result[$counter][content] = $value_type->get_name();
						
						$counter++;
					}
				}
			}
			elseif(count($type_array) != 1 and $_POST[type_id])
			{
				$type_id = $_POST[type_id];
			}
			else
			{
				$type_id = $type_array[0];
			}
			
			if (!$type_id)
			{
				$template = new HTMLTemplate("data/value_select_list.html");
				
				$paramquery = $_GET;
				$paramquery[nextpage] = "1";
				$params = http_build_query($paramquery,'','&#38;');
				
				$template->set_var("params", $params);
				
				$template->set_var("select",$result);
				
				if ($_POST[keywords])
				{
					$template->set_var("keywords", $_POST[keywords]);
				}
				else
				{
					$template->set_var("keywords", "");
				}
				
				if ($_POST[description])
				{
					$template->set_var("description", $_POST[description]);
				}
				else
				{
					$template->set_var("description", "");	
				}
				
				$template->output();
			}
			else
			{	
				$value = Value::get_instance(null);
				$value_type = new ValueType($type_id);
				
				if (!$_GET[nextpage] or $_GET[nextpage] == "1")
				{	
					$template = new HTMLTemplate("data/value_add.html");
					
					$template->set_var("session_id", $_GET['session_id']);
					$template->set_var("folder_id", $folder_id);
					$template->set_var("type_id", $type_id);
					$template->set_var("get_array", serialize($_GET));
					
					if ($_GET['retrace'])
					{
						$template->set_var("retrace", "index.php?".http_build_query(Retrace::resolve_retrace_string($_GET['retrace'])));
					}
					else
					{
						$template->set_var("retrace", "index.php?username=".$_GET['username']."&session_id=".$_GET['session_id']);
					}
					
					
					$template->set_var("title", $value_type->get_name());
					
					require_once("value_form.io.php");
					$value_form_io = new ValueFormIO(null, $type_id, $folder_id);
					$value_form_io->set_field_class("DataValueAddValues");
					
					$template->set_var("value",$value_form_io->get_content());
				
					if ($_POST[keywords])
					{
						$template->set_var("keywords", $_POST[keywords]);
					}
					else
					{
						$template->set_var("keywords", "");
					}
					
					if ($_POST[description])
					{
						$template->set_var("description", $_POST[description]);
					}
					else
					{
						$template->set_var("description", "");	
					}
		
					$template->output();
				}
				else
				{
					$value_add_successful = $value->create($folder_id, $user->get_user_id(), $type_id, $_POST);

					if ($value_add_successful == true)
					{
						return $value->get_item_id();
					}
					else
					{
						return false;
					}
				}
			}
		}
		else
		{
			throw new FolderIDMissingException();
		}
	}

	/**
	 * @throws ValueIDMissingException
	 * @throws DataSecurityAccessDeniedException
	 */
	public static function history()
	{
		if ($_GET[value_id])
		{
			$value_obj = Value::get_instance($_GET[value_id]);
			
			if ($value_obj->is_read_access())
			{
				$argument_array = array();
				$argument_array[0][0] = "value_id";
				$argument_array[0][1] = $_GET[value_id];
	
				$list = new List_IO("DataValueVersionHistory", "ajax.php?nav=data", "value_list_versions", "value_count_versions", $argument_array, "DataValueVersionHistory");

				$list->add_column("","symbol",false,"16px");
				$list->add_column("Name","name",true,null);
				$list->add_column("Version","version",false,null);
				$list->add_column("Date/Time","datetime",true,null);
				$list->add_column("User","user",true,null);
				$list->add_column("","delete",false,"16px");
				
				$template = new HTMLTemplate("data/value_history.html");
	
				$template->set_var("title",$value_obj->get_type_name());
				$template->set_var("list", $list->get_list());

				$template->output();
			}
			else
			{
				throw new DataSecurityAccessDeniedException();
			}	
		}
		else
		{
			throw new ValueIDMissingException();
		}
	}
	
	/**
	 * @throws ValueIDMissingException
	 * @throws ValueVersionIDMissingException
	 * @throws DataSecurityAccessDeniedException
	 */
	public static function delete_version()
	{
		if ($_GET[value_id])
		{
			if ($_GET[version])
			{
				$value = Value::get_instance($_GET[value_id]);
				
				if ($value->is_delete_access())
				{
					if ($_GET[sure] != "true")
					{
						$template = new HTMLTemplate("data/value_delete_version.html");
						
						$paramquery = $_GET;
						$paramquery[sure] = "true";
						$params = http_build_query($paramquery);
						
						$template->set_var("yes_params", $params);
								
						$paramquery = $_GET;
						$paramquery[action] = "value_history";
						unset($paramquery[sure]);
						$params = http_build_query($paramquery);
						
						$template->set_var("no_params", $params);
						
						$template->output();
					}
					else
					{
						if (($return_value = $value->delete_version($_GET[version])) != 0)
						{
							if ($return_value == 1)
							{
								$paramquery = $_GET;
								$paramquery[action] = "value_history";
								unset($paramquery[sure]);
								unset($paramquery[version]);
								$params = http_build_query($paramquery);
							}
							else
							{
								$paramquery = $_GET;
								unset($paramquery[sure]);
								unset($paramquery[action]);
								unset($paramquery[value_id]);
								$params = http_build_query($paramquery);
							}					
							Common_IO::step_proceed($params, "Delete Value", "Operation Successful" ,null);
						}
						else
						{
							$paramquery = $_GET;
							unset($paramquery[sure]);
							unset($paramquery[action]);
							unset($paramquery[value_id]);
							$params = http_build_query($paramquery);
									
							Common_IO::step_proceed($params, "Delete Value", "Operation Failed" ,null);
						}			
					}
				}
				else
				{
					throw new DataSecurityAccessDeniedException();
				}
			}
			else
			{
				throw new ValueVersionIDMissingException();
			}
		}
		else
		{
			throw new ValueIDMissingException();
		}
	}
		
}

?>
