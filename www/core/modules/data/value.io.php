<?php
/**
 * @package data
 * @version 0.4.0.0
 * @author Roman Konertz <konertz@open-lims.org>
 * @copyright (c) 2008-2011 by Roman Konertz
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
	 * NEW
	 */
	public static function detail()
	{
		global $user;
		
		try
		{
			if ($_GET[value_id])
			{
				$value = Value::get_instance($_GET[value_id]);
	
				if ($value->is_read_access())
				{
					if ($_GET[version])
					{
						if ($value->exist_value_version($_GET[version]) == false)
						{
							throw new ValueVersionNotFoundException("",6);
						}
					}
					
					if ($_GET[version] and is_numeric($_GET[version]))
					{
						$value->open_internal_revision($_GET[version]);
					}
					
					if ($_GET[nextpage])
					{
						$noerror = true;
						
						$autofield_array = array();
						$counter = 0;
						
						foreach ($_POST as $fe_key => $fe_value)
						{
							if (strpos($fe_key, "af-") !== false)
							{
								if (strpos($fe_key, "-vartype") !== false)
								{
									$autofield_array[$counter][1] = $fe_value;
								}
								elseif(strpos($fe_key, "-name") !== false)
								{
									$autofield_array[$counter][0] = $fe_value;
									$counter++;
								}
								else
								{
									$autofield_array[$counter][2] = $fe_value;
								}
							}
						}
						
						$autofield_array_string = serialize($autofield_array);
						$value->set_autofield_array_string($autofield_array_string);
					}
					else
					{
						$noerror = false;
					}
					
					if ($noerror == false)
					{
						if ($value->get_type_id() == 2)
						{
							$template = new Template("template/data/value_project_description_detail.html");
						
							$value_version_array = $value->get_value_internal_revisions();
								
							if (is_array($value_version_array) and count($value_version_array) > 0)
							{		
								$result = array();
								$counter = 1;
							
								$result[0][version] = 0;
								$result[0][text] = "----------------------------------------------";
								
								foreach($value_version_array as $key => $fe_value)
								{
									$value_version = Value::get_instance($_GET[value_id]);
									$value_version->open_internal_revision($fe_value);
									
									$result[$counter][version] = $value_version->get_internal_revision();
									$result[$counter][text] = "Version ".$value_version->get_version()." - ".$value_version->get_datetime();
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
							$template = new Template("template/data/value_detail.html");
						
							$value_version_array = $value->get_value_internal_revisions();
								
							if (is_array($value_version_array) and count($value_version_array) > 0)
							{		
								$result = array();
								$counter = 1;
							
								$result[0][version] = 0;
								$result[0][text] = "----------------------------------------------";
								
								foreach($value_version_array as $key => $fe_value)
								{
									$value_version = Value::get_instance($_GET[value_id]);
									$value_version->open_internal_revision($fe_value);
									
									$result[$counter][version] = $value_version->get_internal_revision();
									$result[$counter][text] = "Version ".$value_version->get_version()." - ".$value_version->get_datetime();
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
							
							require_once("value_form.io.php");
							$value_form_io = new ValueFormIO($_GET[value_id]);
							$template->set_var("value",$value_form_io->get_content());
				
							$template->output();
						}
					}
					else
					{
						$paramquery = $_GET;
						unset($paramquery[action]);
						unset($paramquery[value_id]);
						$params = http_build_query($paramquery,'','&#38;');
			
						// Button prüfen
						if ($_POST[submitbutton] == "major")
						{
							$major = true;
						}
						else
						{
							$major = false;
						}
			
						if ($_GET[version])
						{
							$previous_version_id = $_GET[version];
						}
						else
						{
							$previous_version_id = null;
						}
						
						if (is_array($_POST) and count($_POST) >= 1)
						{
							$value_array = array();
							
							foreach ($_POST as $fe_key => $fe_value)
							{
								if ($fe_key != "template_data_type_id" and
								    $fe_key != "submitbutton" and
								    $fe_key != "description" and
								    $fe_key != "keywords")
								{
								    $value_array[$fe_key] = $fe_value;	
								}
							}
				
							if ($value->update($value_array, $previous_version_id, $major, true, false))
							{			
								Common_IO::step_proceed($params, "Value Update", "Value Update Successful" ,null);			
							}
							else
							{
								Common_IO::step_proceed($params, "Value Update", "Value Update Failed" ,null);			
							}
						}
						else
						{
							Common_IO::step_proceed($params, "Value Update", "Value Update Failed" ,null);	
						}
					}
				}
				else
				{
					$exception = new Exception("", 3);
					$error_io = new Error_IO($exception, 20, 40, 2);
					$error_io->display_error();
				}
			}
			else
			{
				$exception = new Exception("", 3);
				$error_io = new Error_IO($exception, 20, 40, 3);
				$error_io->display_error();			
			}
		}
		catch(ValueVersionNotFoundException $e)
		{
			$error_io = new Error_IO($e, 20, 40, 1);
			$error_io->display_error();
		}
	}	

	/**
	 * @todo error: no folder id
	 * NEW
	 */
	public static function add_value_item($type_array, $category_array, $organisation_unit_id, $folder_id)
	{
		global $user;
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
				$template = new Template("template/data/value_select_list.html");
				
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
					$template = new Template("template/data/value_add.html");
							
					$paramquery = $_GET;
					$paramquery[nextpage] = "2";
					$params = http_build_query($paramquery,'','&#38;');
					
					$template->set_var("params", $params);
					
					$template->set_var("title", $value_type->get_name());
					
					require_once("value_form.io.php");
					$value_form_io = new ValueFormIO(null, $type_id, $folder_id);
					$template->set_var("value",$value_form_io->get_content());
		
					$template->set_var("type_id", $type_id);
		
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
			// error
		}
	}

	/**
	 * NEW
	 */
	public static function history()
	{
		global $misc;
		
		if ($_GET[value_id])
		{
			$value_obj = Value::get_instance($_GET[value_id]);
			
			if ($value_obj->is_read_access())
			{
				$list = new ListStat_IO(Data_Wrapper::count_value_versions($_GET[value_id]), 20);

				$list->add_row("","symbol",false,"16px");
				$list->add_row("Name","name",true,null);
				$list->add_row("Version","version",false,null);
				$list->add_row("Date/Time","datetime",true,null);
				$list->add_row("User","user",true,null);
				$list->add_row("","delete",false,"16px");
				
				if ($_GET[page])
				{
					if ($_GET[sortvalue] and $_GET[sortmethod])
					{
						$result_array = Data_Wrapper::list_value_versions($_GET[value_id], $_GET[sortvalue], $_GET[sortmethod], ($_GET[page]*20)-20, ($_GET[page]*20));
					}
					else
					{
						$result_array = Data_Wrapper::list_value_versions($_GET[value_id], null, null, ($_GET[page]*20)-20, ($_GET[page]*20));
					}				
				}
				else
				{
					if ($_GET[sortvalue] and $_GET[sortmethod])
					{
						$result_array = Data_Wrapper::list_value_versions($_GET[value_id], $_GET[sortvalue], $_GET[sortmethod], 0, 20);
					}
					else
					{
						$result_array = Data_Wrapper::list_value_versions($_GET[value_id], null, null, 0, 20);
					}	
				}
				
				if (is_array($result_array) and count($result_array) >= 1)
				{
					foreach($result_array as $key => $value)
					{
						$paramquery = $_GET;
						$paramquery[action] = "value_detail";
						$paramquery[version] = $result_array[$key][internal_revision];
						$params = http_build_query($paramquery,'','&#38;');
						
						$result_array[$key][symbol][link]		= $params;
						$result_array[$key][symbol][content] 	= "<img src='images/icons/value.png' alt='N' border='0' />";
					
						$tmp_name = $result_array[$key][name];
						unset($result_array[$key][name]);
						$result_array[$key][name][link]		= $params;
						$result_array[$key][name][content] 	= $tmp_name;
						
						$datetime_handler = new DatetimeHandler($result_array[$key][datetime]);
						$result_array[$key][datetime] = $datetime_handler->get_formatted_string("dS M Y H:i");
						
						$user = new User($result_array[$key][owner_id]);
						$result_array[$key][user] = $user->get_full_name(false);
						
						$value_version_obj = clone $value_obj;
						$value_version_obj->open_internal_revision($value[internal_revision]);
						if ($value_version_obj->is_current() == true)
						{
							$result_array[$key][version] = $value_version_obj->get_version()." <span class='italic'>current</span>";
						}
						else
						{
							$result_array[$key][version] = $value_version_obj->get_version();
						}
					}
				}
				else
				{
					$list->override_last_line("<span class='italic'>No results found!</span>");
				}
				
				$template = new Template("template/data/value_history.html");
	
				$template->set_var("title",$value_obj->get_type_name());
				
				$template->set_var("table", $list->get_list($result_array, $_GET[page]));
				
				$paramquery = $_GET;
				$paramquery[action] = "value_detail";
				$params = http_build_query($paramquery,'','&#38;');	
				
				$template->set_var("back_link",$params);
				
				$template->output();
			}
			else
			{
				$exception = new Exception("", 3);
				$error_io = new Error_IO($exception, 20, 40, 2);
				$error_io->display_error();
			}	
		}
		else
		{
			$exception = new Exception("", 3);
			$error_io = new Error_IO($exception, 20, 40, 3);
			$error_io->display_error();	
		}
	}
	
	/**
	 * NEW
	 */
	public static function delete_version()
	{
		if ($_GET[value_id] and $_GET[version])
		{
			$value = Value::get_instance($_GET[value_id]);
			
			if ($value->is_delete_access())
			{
				if ($_GET[sure] != "true")
				{
					$template = new Template("template/data/value_delete_version.html");
					
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
				$exception = new Exception("", 3);
				$error_io = new Error_IO($exception, 20, 40, 2);
				$error_io->display_error();
			}
		}
		else
		{
			$exception = new Exception("", 3);
			$error_io = new Error_IO($exception, 20, 40, 3);
			$error_io->display_error();	
		}
	}
		
}

?>
