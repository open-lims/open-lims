<?php
/**
 * @package sample
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
 * 
 */
$GLOBALS['autoload_prefix'] = "../";
require_once("../../base/ajax.php");

/**
 * Sample Clone AJAX IO Class
 * @package sample
 */
class SampleCloneAjax extends Ajax
{	
	function __construct()
	{
		parent::__construct();
	}
	
	private function check_name($name)
	{
		global $session;
		
		$sample_source_sample 	= $session->read_value("SAMPLE_CLONE_SOURCE_SAMPLE");
		$sample_name_warning	= $session->read_value("SAMPLE_CLONE_NAME_WARNING");
		
		if ($sample_source_sample and !$sample_name_warning)
		{
			$source_sample = new Sample($sample_source_sample);
			
			if (trim(strtolower($source_sample->get_name())) == trim(strtolower($name)))
			{
				return "1";
			}
			else
			{
				return "0";
			}
		}
		else
		{
			return "0";
		}	
	}
	
	private function get_content($page, $form_field_name)
	{
		global $session, $user;
		
		switch ($page):		
			case "1":
				$sample_source_sample 	= $session->read_value("SAMPLE_CLONE_SOURCE_SAMPLE");
				$sample_type_array 		= $session->read_value("SAMPLE_CLONE_TYPE_ARRAY");
				
				$template = new Template("../../../../template/samples/clone_sample_page_1.html");	
	
				$result = array();
				$counter = 0;
					
				$user_sample_array = Sample::list_user_related_samples($user->get_user_id());
				
				if (!is_array($sample_type_array) or count($sample_type_array) == 0)
				{
					$sample_type_array = null;
				}
				
				if (is_array($user_sample_array) and count($user_sample_array) >= 1)
				{
					foreach($user_sample_array as $key => $value)
					{
						$sample = new Sample($value);
						
						if ($sample_type_array == null or in_array($sample->get_template_id(), $sample_type_array))
						{
							$result[$counter][value] = $value;
							$result[$counter][content] = $sample->get_name();
							if ($sample_source_sample == $value)
							{
								$result[$counter][selected] = "selected";
							}
							else
							{
								$result[$counter][selected] = "";
							}
							
							$result[$counter][disabled] = "";
							
							$counter++;
						}
					}
				}
				
				if (!$result)
				{
					$result[$counter][value] = "0";
					$result[$counter][content] = "NO SAMPLE FOUND!";
					$result[$counter][selected] = "";
					$result[$counter][disabled] = "disabled='disabled'";
				}
		
				$template->set_var("option",$result);
				
				return $template->get_string();
			break;
		
			case "2":
				$sample_source_sample 		= $session->read_value("SAMPLE_CLONE_SOURCE_SAMPLE");
				$sample_name				= $session->read_value("SAMPLE_CLONE_NAME");
				$sample_manufacturer		= $session->read_value("SAMPLE_CLONE_MANUFACTURER_ID");	
				$sample_manufacturer_name	= $session->read_value("SAMPLE_CLONE_MANUFACTURER_NAME");			
				$sample_location			= $session->read_value("SAMPLE_CLONE_LOCATION");
				$sample_expiry				= $session->read_value("SAMPLE_CLONE_EXPIRY");
				$sample_expiry_warning		= $session->read_value("SAMPLE_CLONE_EXPIRY_WARNING");
				$sample_description			= $session->read_value("SAMPLE_CLONE_DESCRIPTION");

				$source_sample = new Sample($sample_source_sample);
				$sample_template_obj = new SampleTemplate($source_sample->get_template_id());
				$information_fields = $sample_template_obj->get_information_fields();

				$template = new Template("../../../../template/samples/clone_sample_page_2.html");
				
				if ($information_fields[manufacturer][name] and $information_fields[manufacturer][requirement] != "optional")
				{
					$template->set_var("check_manufacturer", true);
				}
				else
				{
					$template->set_var("check_manufacturer", false);
				}
				
				if ($information_fields[expiry][name] and $information_fields[expiry][requirement] != "optional")
				{
					$template->set_var("check_expiry", true);
				}
				else
				{
					$template->set_var("check_expiry", false);
				}
				
				if ($information_fields[location][name] and $information_fields[location][requirement] != "optional")
				{
					$template->set_var("check_location", true);
				}
				else
				{
					$template->set_var("check_location", false);
				}
				
				if ($sample_name)
				{
					$template->set_var("name",$sample_name);
				}
				else
				{
					if ($sample_name = $source_sample->get_name())
					{
						$template->set_var("name",$sample_name);
					}
					else
					{
						$template->set_var("name","");
					}
				}
				
				if ($information_fields[manufacturer][name])
				{
					require_once("../../../../core/modules/manufacturer/manufacturer.io.php");
					$template->set_var("show_manufacturer",true);
					$template->set_var("manufacturer_html",ManufacturerIO::dialog());
				}
				else
				{
					$template->set_var("show_manufacturer",false);
					$template->set_var("manufacturer_html","");
				}
				
				if ($information_fields[expiry][name])
				{
					$template->set_var("show_expiry",true);
				}
				else
				{
					$template->set_var("show_expiry",false);
				}
				
				if ($information_fields[location][name])
				{
					$template->set_var("show_location",true);
					
					$result = array();
					$counter = 0;
						
					$sample_location_array = Location::list_entries();
					
					if (is_array($sample_location_array) and count($sample_location_array) >= 1)
					{
						foreach($sample_location_array as $key => $value)
						{
							$sample_location_obj = new Location($value);
											
							$result[$counter][value] = $value;
							$result[$counter][content] = $sample_location_obj->get_name(true);		
		
							if ($sample_location == $value)
							{
								$result[$counter][selected] = "selected";
							}
							else
							{
								$result[$counter][selected] = "";
							}
							$counter++;
						}
					}
					else
					{
						$result[$counter][value] = "0";
						$result[$counter][content] = "NO LOCATIONS FOUND!";
					}
					$template->set_var("location",$result);
				}
				else
				{
					$template->set_var("show_location",false);
				}
				
				if ($sample_manufacturer)
				{
					$template->set_var("manufacturer",$sample_manufacturer);
				}
				else
				{
					$template->set_var("manufacturer","");
				}
				
				if ($sample_manufacturer_name)
				{
					$template->set_var("manufacturer_name",$sample_manufacturer_name);
				}
				else
				{
					$template->set_var("manufacturer_name","");
				}
				
				if ($sample_expiry)
				{
					$template->set_var("expiry",$sample_expiry);
				}
				else
				{
					$template->set_var("expiry","");
				}
				
				if ($sample_expiry_warning)
				{
					$template->set_var("expiry_warning",$sample_expiry_warning);
				}
				else
				{
					$template->set_var("expiry_warning",constant("SAMPLE_EXPIRY_WARNING"));
				}
				
				if ($sample_description)
				{
					$template->set_var("desc",$sample_description);
				}
				else
				{
					if ($sample_description = $source_sample->get_description())
					{
						$template->set_var("desc",$sample_description);
					}
					else
					{
						$template->set_var("desc","");
					}
				}
				
				return $template->get_string();
			break;
			
			case "3":
				$sample_source_sample 	= $session->read_value("SAMPLE_CLONE_SOURCE_SAMPLE");
				$sample_template_array 	= $session->read_value("SAMPLE_CLONE_TEMPLATE_ARRAY");
				
				if (is_array($sample_template_array) and count($sample_template_array) >= 1)
				{
					foreach($sample_template_array as $key => $value)
    				{
    					$key = str_replace("value-","",$key);
    					$key_array = explode("-", $key, 2);
    					
    					if ($key_array[0] == "item")
    					{
    						$value_item_array[$key_array[1]] = $value;
    					}
    					elseif(is_numeric($key_array[0]))
    					{
    						$value_data_array[$key_array[0]][$key_array[1]] = $value;
    					}
    				}
				}
				
				$sample_item = new SampleItem($sample_source_sample);
				$sample_item_array = $sample_item->get_sample_items();
				
				$value_array = array();
				
				if (is_array($sample_item_array) and count($sample_item_array) >= 1)
				{
					foreach($sample_item_array as $key => $value)
					{
						if(DataEntity::is_kind_of("value", $value))
						{
							$data_entity_id = DataEntity::get_entry_by_item_id($value);
							array_push($value_array,Value::get_value_id_by_data_entity_id($data_entity_id));
						}
					}
				}
												
				$template = new Template("../../../../template/samples/clone_sample_page_3.html");	
				
				if (is_array($value_array) and count($value_array) >= 1)
				{
					$content_array = array();
					$content_counter = 0;
					
					require_once("../../../../core/modules/data/value_form.io.php");
					
					foreach($value_array as $key => $value)
					{
						$value_obj = Value::get_instance($value);
						$value_form_io = new ValueFormIO($value, null, null, $value_data_array[$key]);
						$value_form_io->set_field_prefix("value-".$key);
						$value_form_io->set_field_class("SampleCloneAssistantField");
						
						$content_array[$content_counter]['headline'] = $value_obj->get_name();
						$content_array[$content_counter]['html'] = $value_form_io->get_content();
						$content_array[$content_counter]['item_name'] = "value-item-".$key;
						$content_array[$content_counter]['item_value'] = $value_obj->get_item_id();
						$content_counter++;
					}
					
					$template->set_var("no_value", false);
					$template->set_var("content", $content_array);
				}
				else
				{
					$template->set_var("no_value", true);
				}
				
				return $template->get_string();
			break;
			
			case "4":
				$sample_source_sample 		= $session->read_value("SAMPLE_CLONE_SOURCE_SAMPLE");
				
				$source_sample = new Sample($sample_source_sample);
				
				$template = new Template("../../../../template/samples/clone_sample_page_4.html");	
				
				$module_dialog_array = ModuleDialog::list_dialogs_by_type("item_assistant_list");
		
				if (is_array($module_dialog_array) and count($module_dialog_array) >= 1)
				{
					foreach ($module_dialog_array as $key => $value)
					{		
						if (file_exists("../../../../".$value[class_path]))
						{	
							require_once("../../../../".$value[class_path]);
							
							if (class_exists($value['class']) and method_exists($value['class'], $value[method]))
							{
								echo $value['class']::$value[method]("sample", $sample_source_sample, false, true, $form_field_name);
							}
							else
							{
								// Error
							}
						}
						else
						{
							// Error
						}
					}
				}
				
				$module_dialog_array = ModuleDialog::list_dialogs_by_type("item_parent_assistant_list");
		
				if (is_array($module_dialog_array) and count($module_dialog_array) >= 1)
				{
					foreach ($module_dialog_array as $key => $value)
					{		
						if (file_exists("../../../../".$value[class_path]))
						{	
							require_once("../../../../".$value[class_path]);
							
							if (class_exists($value['class']) and method_exists($value['class'], $value[method]))
							{
								echo $value['class']::$value[method]($source_sample->get_item_id(), true, $form_field_name);
							}
							else
							{
								// Error
							}
						}
						else
						{
							// Error
						}
					}
				}
				
				return $template->get_string();
			break;
			
			case "5":
				$sample_source_sample 		= $session->read_value("SAMPLE_CLONE_SOURCE_SAMPLE");
				$sample_name				= $session->read_value("SAMPLE_CLONE_NAME");
				$sample_manufacturer		= $session->read_value("SAMPLE_CLONE_MANUFACTURER_ID");	
				$sample_manufacturer_name	= $session->read_value("SAMPLE_CLONE_MANUFACTURER_NAME");			
				$sample_location			= $session->read_value("SAMPLE_CLONE_LOCATION");
				$sample_expiry				= $session->read_value("SAMPLE_CLONE_EXPIRY");
				$sample_expiry_warning		= $session->read_value("SAMPLE_CLONE_EXPIRY_WARNING");
				$sample_description			= $session->read_value("SAMPLE_CLONE_DESCRIPTION");
				
				$template = new Template("../../../../template/samples/clone_sample_page_5.html");
			
				$organisation_unit = new OrganisationUnit($sample_organ_unit);
				$template->set_var("sample_organisation_unit",$organisation_unit->get_name());
			
				$source_sample = new Sample($sample_source_sample);
				$sample_template_obj = new SampleTemplate($source_sample->get_template_id());
				$template->set_var("sample_template",$sample_template_obj->get_name());
			
				$template->set_var("sample_name",$sample_name);
				
				if ($sample_manufacturer)
				{
					$template->set_var("sample_manufacturer",$sample_manufacturer_name);
				}
				else
				{
					$template->set_var("sample_manufacturer",false);
				}
				
				if ($sample_location)
				{
					$sample_location_obj = new Location($sample_location);
					$template->set_var("sample_location",$sample_location_obj->get_name(true));
				}
				else
				{
					$template->set_var("sample_location",false);
				}
			
				if ($sample_expiry)
				{
					$template->set_var("sample_date_of_expiry",$sample_expiry);
				}
				else
				{
					$template->set_var("sample_date_of_expiry",false);
				}
				
				if ($sample_desc)
				{
					$sample_desc_display = str_replace("\n", "<br />", $sample_desc);
					$template->set_var("sample_description",$sample_desc_display);
				}
				else
				{
					$template->set_var("sample_description","<span class='italic'>None</span>");
				}
		
				return $template->get_string();
			break;
			
			default:
				return "Error: The requested page does not exist!";
			break;
			
		endswitch;

	}

	private function get_next_page($page)
	{
		return ($page+1);
	}
	
	private function get_previous_page($page)
	{
		return ($page-1);
	}
	
	private function set_data($page, $data)
	{
		global $session;
		
		$data_array = json_decode($data);
		
		if (is_array($data_array) and count($data_array) >= 1)
		{
			switch($page):				
				case "1":
					foreach($data_array as $key => $value)
					{
						if ($value[0] == "sample_source_sample")
						{
							$session->write_value("SAMPLE_CLONE_SOURCE_SAMPLE",$value[1],true);
						}
					}
				break;
				
				case "2":
					foreach($data_array as $key => $value)
					{
						if ($value[0] == "sample_name")
						{
							$session->write_value("SAMPLE_CLONE_NAME",$value[1],true);
						}
						if ($value[0] == "sample_name_warning")
						{
							if (trim($value[1]) == "1")
							{
								$session->write_value("SAMPLE_CLONE_NAME_WARNING",true,true);
							}
						}
						if ($value[0] == "sample_manufacturer_name")
						{
							$session->write_value("SAMPLE_CLONE_MANUFACTURER_NAME",$value[1],true);
						}
						if ($value[0] == "sample_manufacturer_id")
						{
							$session->write_value("SAMPLE_CLONE_MANUFACTURER_ID",$value[1],true);
						}
						if ($value[0] == "sample_expiry")
						{
							$session->write_value("SAMPLE_CLONE_EXPIRY",$value[1],true);
						}
						if ($value[0] == "sample_expiry_warning")
						{
							$session->write_value("SAMPLE_CLONE_EXPIRY_WARNING",$value[1],true);
						}
						if ($value[0] == "sample_location")
						{
							$session->write_value("SAMPLE_CLONE_LOCATION",$value[1],true);
						}
						if ($value[0] == "sample_description")
						{
							$session->write_value("SAMPLE_CLONE_DESCRIPTION",$value[1],true);
						}
					}
				break;
				
				case "3":
					foreach($data_array as $key => $value)
					{
						if (strpos($value[0], "-vartype") === false and strpos($value[0], "value") !== false)
						{
							$template_array[$value[0]] = $value[1];
						}
					}
					
					if (is_array($template_array) and count($template_array) >= 1)
					{
						$session->write_value("SAMPLE_CLONE_TEMPLATE_ARRAY", $template_array, true);
					}
				break;
				
				case "4":
					$session->write_value("SAMPLE_CLONE_ITEM_ARRAY",$data_array,true);
				break;
			endswitch;
		}
	}
	
	private function run($username, $session_id)
	{
		global $session, $user;
		
		$sample_clone_role				= $session->read_value("SAMPLE_CLONE_ROLE");
		
		$sample_item_retrace 			= $session->read_value("SAMPLE_ITEM_RETRACE");
		$sample_item_get_array			= $session->read_value("SAMPLE_ITEM_GET_ARRAY");
		$sample_item_keywords			= $session->read_value("SAMPLE_ITEM_KEYWORDS");
		$sample_item_description		= $session->read_value("SAMPLE_ITEM_DESCRIPTION");
		
		$sample_source_sample 			= $session->read_value("SAMPLE_CLONE_SOURCE_SAMPLE");
		
		$sample_name					= $session->read_value("SAMPLE_CLONE_NAME");
		$sample_manufacturer			= $session->read_value("SAMPLE_CLONE_MANUFACTURER_ID");	
		$sample_manufacturer_name		= $session->read_value("SAMPLE_CLONE_MANUFACTURER_NAME");			
		$sample_location				= $session->read_value("SAMPLE_CLONE_LOCATION");
		$sample_expiry					= $session->read_value("SAMPLE_CLONE_EXPIRY");
		$sample_expiry_warning			= $session->read_value("SAMPLE_CLONE_EXPIRY_WARNING");
		$sample_description				= $session->read_value("SAMPLE_CLONE_DESCRIPTION");
		$sample_template_array			= $session->read_value("SAMPLE_CLONE_TEMPLATE_ARRAY");
		$sample_item_array				= $session->read_value("SAMPLE_CLONE_ITEM_ARRAY");

		
		try
		{
			$sample = new Sample(null);
			
			if (($sample_id = $sample->clone_sample($sample_source_sample, $sample_name, $sample_manufacturer, $sample_location, $sample_description, null, $sample_expiry, $sample_expiry_warning, $sample_template_array, $sample_item_array)) != null)
			{	
				$session->delete_value("SAMPLE_CLONE_ROLE");
			
				$session->delete_value("SAMPLE_ITEM_RETRACE");
				$session->delete_value("SAMPLE_ITEM_GET_ARRAY");
				$session->delete_value("SAMPLE_ITEM_KEYWORDS");
				$session->delete_value("SAMPLE_ITEM_DESCRIPTION");
				$session->delete_value("SAMPLE_ITEM_TYPE_ARRAY");
				
				$session->delete_value("SAMPLE_CLONE_SOURCE_SAMPLE");
				
				$session->delete_value("SAMPLE_CLONE_TYPE_ARRAY");
				$session->delete_value("SAMPLE_CLONE_CATEGORY_ARRAY");
				$session->delete_value("SAMPLE_CLONE_NAME");
				$session->delete_value("SAMPLE_CLONE_MANUFACTURER_ID");
				$session->delete_value("SAMPLE_CLONE_MANUFACTURER_NAME");
				$session->delete_value("SAMPLE_CLONE_LOCATION");
				$session->delete_value("SAMPLE_CLONE_EXPIRY");
				$session->delete_value("SAMPLE_CLONE_EXPIRY_WARNING");
				$session->delete_value("SAMPLE_CLONE_DESCRIPTION");
				$session->delete_value("SAMPLE_CLONE_TEMPLATE_ARRAY");		
				$session->delete_value("SAMPLE_CLONE_ITEM_ARRAY");
				$session->delete_value("SAMPLE_CLONE_NAME_WARNING");
				
				if ($sample_clone_role == "item" or $sample_add_role == "item_parent")
				{
					// Special Parent Sample Case
					if ($sample_add_role == "item_parent")
					{
						$parent_sample = new Sample($sample_item_get_array['sample_id']);
						$sample_item_get_array['sample_id'] = $sample_id;
						$sample_item_get_array['key'] = ($sample_item_get_array['key']*-1);
						$event_item_id = $parent_sample->get_item_id();
					}
					else
					{
						$event_item_id = $sample->get_item_id();
					}
					
					$post_array = array();
					$post_array['keywords'] = $sample_item_keywords;
					$post_array['description'] = $sample_item_description;
					
					$item_add_event = new ItemAddEvent($event_item_id, $sample_item_get_array, $post_array);
					$event_handler = new EventHandler($item_add_event);
					if ($event_handler->get_success() == true)
					{
						if ($sample_item_retrace)
						{
							$params = http_build_query(Retrace::resovle_retrace_string($sample_item_retrace),'','&');
							return "index.php?".$params;
						}
						else
						{
							$paramquery['username'] = $username;
							$paramquery['session_id'] = $session_id;
							$paramquery['nav'] = "home";
							$params = http_build_query($paramquery,'','&');
							return "index.php?".$params;
						}
					}
					else
					{
						return "0";
					}
				}
				else
				{
					$paramquery = array();
					$paramquery['username'] = $username;
					$paramquery['session_id'] = $session_id;
					$paramquery['nav'] = "sample";
					$paramquery['run'] = "detail";
					$paramquery['sample_id'] = $sample_id;
					$params = http_build_query($paramquery, '', '&');
					
					return "index.php?".$params;
				}
			}
			else
			{				
				return "0";
			}
		}
		catch (SampleCloneFailedException $e)
		{
			return "0";
		}
	}
	
	public function handler()
	{
		global $session;
		
		if ($session->is_valid())
		{
			switch($_GET['run']):
			
				case "check_name":
					echo $this->check_name($_GET['name']);
				break;
			
				case "get_content":
					echo $this->get_content($_GET['page'], $_GET['form_field_name']);
				break;
				
				case "get_next_page":
					echo $this->get_next_page($_GET['page']);
				break;
				
				case "get_previous_page":
					echo $this->get_previous_page($_GET['page']);
				break;
				
				case "set_data":
					echo $this->set_data($_POST['page'], $_POST['data']);
				break;
				
				case "run":
					echo $this->run($_GET['username'], $_GET['session_id']);
				break;
				
			endswitch;
		}
	}
}

$sample_clone_ajax = new SampleCloneAjax();
$sample_clone_ajax->handler();

?>