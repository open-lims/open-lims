<?php
/**
 * @package sample
 * @version 0.4.0.0
 * @author Roman Konertz <konertz@open-lims.org>
 * @copyright (c) 2008-2013 by Roman Konertz
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
 * Sample Create AJAX IO Class
 * @package sample
 */
class SampleCreateAjax
{	
	/**
	 * @param integer $page
	 * @return string
	 * @throws BaseAssistantRequestPageNotExistsException
	 */	
	public static function get_content($page)
	{
		global $session, $user;
		
		switch ($page):		
			case "1":
				$sample_organ_unit = $session->read_value("SAMPLE_ORGANISATION_UNIT");
				
				$template = new HTMLTemplate("sample/new_sample_page_1.html");	
	
				$result = array();
				$counter = 0;
					
				$organisation_unit_array = OrganisationUnit::list_entries();
				
				foreach($organisation_unit_array as $key => $value)
				{
					$organisation_unit = new OrganisationUnit($value);
			
					if ($organisation_unit->is_permission($user->get_user_id()) and $organisation_unit->get_stores_data() == true)
					{
						$result[$counter]['value'] = $value;
						$result[$counter]['content'] = $organisation_unit->get_name();		
	
						if ($sample_organ_unit == $value)
						{
							$result[$counter]['selected'] = "selected";
						}
						else
						{
							$result[$counter]['selected'] = "";
						}
						
						$result[$counter]['disabled'] = "";
						
						$counter++;
					}
				}
				
				if (!$result)
				{
					$result[$counter]['value'] = "0";
					$result[$counter]['content'] = "NO ORGANISATION UNIT FOUND!";
					$result[$counter]['selected'] = "";
					$result[$counter]['disabled'] = "disabled='disabled'";
				}
		
				$template->set_var("option",$result);
				
				return $template->get_string();
			break;
		
			case "2":
				$sample_template 	= $session->read_value("SAMPLE_TEMPLATE");
				$type_array 		= $session->read_value("SAMPLE_ITEM_TYPE_ARRAY");
				
				$template = new HTMLTemplate("sample/new_sample_page_2.html");	

				if (!is_array($type_array) or count($type_array) == 0)
				{
					$type_array = null;
				}
				
				$result = array();
				$counter = 0;
					
				$sample_template_array = SampleTemplate::list_entries();
				
				if (is_array($sample_template_array))
				{
					foreach($sample_template_array as $key => $value)
					{
						if ($type_array == null or in_array($value, $type_array))
						{
							$sample_sub_template = new SampleTemplate($value);
							
							$result[$counter]['value'] = $value;
							$result[$counter]['content'] = $sample_sub_template->get_name();		
		
							if ($sample_template == $value)
							{
								$result[$counter]['selected'] = "selected";
							}
							else
							{
								$result[$counter]['selected'] = "";
							}
							
							$result[$counter]['disabled'] = "";
							
							$counter++;
						}
					}
				}
				else
				{
					$result[$counter]['value'] = "0";
					$result[$counter]['content'] = "NO TEMPLATES FOUND!";
					$result[$counter]['selected'] = "";
					$result[$counter]['disabled'] = "disabled='disabled'";
				}
				$template->set_var("option",$result);
				
				if ($session->is_value("ADD_ITEM_TEMP_KEYWORDS_".$_GET['idk_unique_id']) == true)
				{
					$template->set_var("keywords", $session->read_value("ADD_ITEM_TEMP_KEYWORDS_".$_GET['idk_unique_id']));
				}
				else
				{
					$template->set_var("keywords", "");
				}
				
				if ($session->is_value("ADD_ITEM_TEMP_DESCRIPTION_".$_GET['idk_unique_id']) == true)
				{
					$template->set_var("description", $session->read_value("ADD_ITEM_TEMP_DESCRIPTION_".$_GET['idk_unique_id']));
				}
				else
				{
					$template->set_var("description", "");
				}
				
				return $template->get_string();
			break;
			
			case "3":
				$sample_template 			= $session->read_value("SAMPLE_TEMPLATE");
				$sample_name				= $session->read_value("SAMPLE_NAME");
				$sample_manufacturer		= $session->read_value("SAMPLE_MANUFACTURER_ID");	
				$sample_manufacturer_name	= $session->read_value("SAMPLE_MANUFACTURER_NAME");			
				$sample_location			= $session->read_value("SAMPLE_LOCATION");
				$sample_expiry				= $session->read_value("SAMPLE_EXPIRY");
				$sample_expiry_warning		= $session->read_value("SAMPLE_EXPIRY_WARNING");
				$sample_description			= $session->read_value("SAMPLE_DESCRIPTION");

				$sample_template_obj = new SampleTemplate($sample_template);
				$information_fields = $sample_template_obj->get_information_fields();

				$template = new HTMLTemplate("sample/new_sample_page_3.html");
				
				if ($information_fields['manufacturer']['name'] and $information_fields['manufacturer']['requirement'] != "optional")
				{
					$template->set_var("check_manufacturer", true);
				}
				else
				{
					$template->set_var("check_manufacturer", false);
				}
				
				if ($information_fields['expiry']['name'] and $information_fields['expiry']['requirement'] != "optional")
				{
					$template->set_var("check_expiry", true);
				}
				else
				{
					$template->set_var("check_expiry", false);
				}
				
				if ($information_fields['location']['name'] and $information_fields['location']['requirement'] != "optional")
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
					$template->set_var("name","");
				}
				
				if ($information_fields['manufacturer']['name'])
				{
					require_once("core/modules/manufacturer/io/manufacturer.io.php");
					$template->set_var("show_manufacturer",true);
					$template->set_var("manufacturer_html",ManufacturerIO::dialog());
				}
				else
				{
					$template->set_var("show_manufacturer",false);
					$template->set_var("manufacturer_html","");
				}
				
				if ($information_fields['expiry']['name'])
				{
					$template->set_var("show_expiry",true);
				}
				else
				{
					$template->set_var("show_expiry",false);
				}
				
				if ($information_fields['location']['name'])
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
											
							$result[$counter]['value'] = $value;
							$result[$counter]['content'] = $sample_location_obj->get_name(true);		
		
							if ($sample_location == $value)
							{
								$result[$counter]['selected'] = "selected";
							}
							else
							{
								$result[$counter]['selected'] = "";
							}
							$counter++;
						}
					}
					else
					{
						$result[$counter]['value'] = "0";
						$result[$counter]['content'] = "NO LOCATIONS FOUND!";
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
					$template->set_var("expiry_warning",(int)Registry::get_value("sample_default_expiry_warning"));
				}
				
				if ($sample_description)
				{
					$template->set_var("desc",$sample_description);
				}
				else
				{
					$template->set_var("desc","");
				}
				
				return $template->get_string();
			break;
			
			case "4":
				$sample_add_role				= $session->read_value("SAMPLE_ADD_ROLE");
				$sample_add_item_holder_class	= $session->read_value("SAMPLE_ADD_ITEM_HOLDER_CLASS");
				$sample_add_item_holder_id		= $session->read_value("SAMPLE_ADD_ITEM_HOLDER_ID");
				
				$sample_template				= $session->read_value("SAMPLE_TEMPLATE");
				$sample_template_data_type  	= $session->read_value("SAMPLE_TEMPLATE_DATA_TYPE");	
				$sample_template_data_type_id	= $session->read_value("SAMPLE_TEMPLATE_DATA_TYPE_ID");	
				$sample_template_data_array		= $session->read_value("SAMPLE_TEMPLATE_DATA_ARRAY");
				
				$sample_template_obj = new SampleTemplate($sample_template);
				$required_array = $sample_template_obj->get_required_requirements();
				
				if (is_array($required_array) and count($required_array) >= 1)
				{
					$value_type_id = 0;
					$sample_count = 0;
					$is_value = false;
					$is_sample = false;
					
					foreach($required_array as $key => $value)
					{						
						if ($value['xml_element'] == "item")
						{
							if ($value['type'] == "value")
							{
								$is_value = true;
							}
							elseif($value['type'] == "parentsample")
							{
								$is_sample = true;
								$sample_count++;
							}
						}
						
						if ($value['xml_element'] == "type" and !$value['close'] and $is_value == true)
						{
							$value_type_id = $value['id'];
						}
					} 
					
					if ($is_value == true xor $is_sample == true)
					{
						if ($is_value == true)
						{
							$template = new HTMLTemplate("sample/new_sample_page_4_value.html");
							
							require_once("core/modules/data/io/value_form.io.php");
							$value_form_io = new ValueFormIO(null, $value_type_id, null, $sample_template_data_array);
							$value_form_io->set_field_class("SampleCreateAssistantField");
							$template->set_var("content",$value_form_io->get_content());
							
							$template->set_var("template_data_type_id", $value_type_id);
							return $template->get_string();
						}
						else
						{
							$template = new HTMLTemplate("sample/new_sample_page_4_sample.html");
							
							if ($sample_count > 0)
							{
								$result = array();
																
								if ($sample_add_role == "item" and is_numeric($sample_add_item_holder_id) and class_exists($sample_add_item_holder_class))
								{									
									$item_holder_list_sql = $sample_add_item_holder_class::get_item_list_sql($sample_add_item_holder_id);
									
									if ($item_holder_list_sql)
									{
										$sample_array = Sample::list_samples_by_item_sql_list($item_holder_list_sql);
									}
									else
									{
										$sample_array = Sample::list_user_related_samples($user->get_user_id());
									}
								}
								else
								{
									$sample_array = Sample::list_user_related_samples($user->get_user_id());
								}
								
								for($i=0;$i<=$sample_count-1;$i++)
								{
									$result[$i]['id'] = $i+1;
									
									if ($sample_template_data_type == "sample")
									{
										if ($sample_template_data_array['sample-'.$result[$i]['id'].''])
										{
											$selected_id = $sample_template_data_array['sample-'.$result[$i]['id'].''];
										}
									}	
									
									if (is_array($sample_array) and count($sample_array) >= 1)
									{
										$counter = 0;
										
										foreach($sample_array as $key => $value)
										{
											$sample = new Sample($value);
											
											$result[$i][$counter]['value'] = $value;
											$result[$i][$counter]['content'] = $sample->get_name();
											if ($selected_id == $value)
											{
												$result[$i][$counter]['selected'] = "selected";
											}
											else
											{
												$result[$i][$counter]['selected'] = "";
											}
											
											$counter++;
										}
									}
									else
									{
										$result[$i][0]['value'] = 0;
										$result[$i][0]['content'] = "You have no samples";
										$result[$i][0]['selected'] = "";
									}
									unset($selected_id);
								}
								$template->set_var("sample", $result);
							}	
							return $template->get_string();
						}
					}
					else
					{
						$template = new HTMLTemplate("sample/new_sample_page_4_error.html");
						return $template->get_string();
					}
				}
				else
				{
					$template = new HTMLTemplate("sample/new_sample_page_4_error.html");	
					return $template->get_string();
				}		
			break;
			
			case "5":
				$sample_organ_unit 			= $session->read_value("SAMPLE_ORGANISATION_UNIT");
				$sample_template 			= $session->read_value("SAMPLE_TEMPLATE");
				$sample_name				= $session->read_value("SAMPLE_NAME");
				$sample_manufacturer		= $session->read_value("SAMPLE_MANUFACTURER_ID");	
				$sample_manufacturer_name	= $session->read_value("SAMPLE_MANUFACTURER_NAME");			
				$sample_location			= $session->read_value("SAMPLE_LOCATION");
				$sample_expiry				= $session->read_value("SAMPLE_EXPIRY");
				$sample_expiry_warning		= $session->read_value("SAMPLE_EXPIRY_WARNING");
				$sample_description			= $session->read_value("SAMPLE_DESCRIPTION");
				
				$template = new HTMLTemplate("sample/new_sample_page_5.html");
			
				$organisation_unit = new OrganisationUnit($sample_organ_unit);
				$template->set_var("sample_organisation_unit",$organisation_unit->get_name());
			
				$sample_template_obj = new SampleTemplate($sample_template);
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
				throw new BaseAssistantRequestedPageNotExistsException();
			break;
			
		endswitch;

	}

	/**
	 * @param integer $page
	 * @return integer
	 */
	public static function get_next_page($page)
	{
		global $session;
		
		if ($page == 3)
		{
			$sample_template		= $session->read_value("SAMPLE_TEMPLATE");
			$sample_template_obj 	= new SampleTemplate($sample_template);
			
			if ($sample_template_obj->is_required_requirements() == true)
			{
				return 4;
			}
			else
			{
				return 5;
			}
		}
		else
		{
			return ($page+1);
		}
	}
	
	/**
	 * @param integer $page
	 * @return integer
	 */
	public static function get_previous_page($page)
	{
		global $session;
		
		if ($page == 5)
		{
			$sample_template		= $session->read_value("SAMPLE_TEMPLATE");
			$sample_template_obj 	= new SampleTemplate($sample_template);
			
			if ($sample_template_obj->is_required_requirements() == true)
			{
				return 4;
			}
			else
			{
				return 3;
			}
		}
		else
		{
			return ($page-1);
		}
	}
	
	/**
	 * @param integer $page
	 * @param string $data
	 */
	public static function set_data($page, $data)
	{
		global $session;
		
		$data_array = json_decode($data);
		
		if (is_array($data_array) and count($data_array) >= 1)
		{
			switch($page):				
				case "1":
					foreach($data_array as $key => $value)
					{
						if ($value[0] == "sample_organisation_unit")
						{
							$session->write_value("SAMPLE_ORGANISATION_UNIT",$value[1],true);
						}
					}
				break;
				
				case "2":
					foreach($data_array as $key => $value)
					{
						if ($value[0] == "sample_template")
						{
							$session->write_value("SAMPLE_TEMPLATE",$value[1],true);
						}
					}
				break;
				
				case "3":
					foreach($data_array as $key => $value)
					{
						if ($value[0] == "sample_name")
						{
							$session->write_value("SAMPLE_NAME",$value[1],true);
						}
						if ($value[0] == "sample_manufacturer_name")
						{
							$session->write_value("SAMPLE_MANUFACTURER_NAME",$value[1],true);
						}
						if ($value[0] == "sample_manufacturer_id")
						{
							$session->write_value("SAMPLE_MANUFACTURER_ID",$value[1],true);
						}
						if ($value[0] == "sample_expiry")
						{
							$session->write_value("SAMPLE_EXPIRY",$value[1],true);
						}
						if ($value[0] == "sample_expiry_warning")
						{
							$session->write_value("SAMPLE_EXPIRY_WARNING",$value[1],true);
						}
						if ($value[0] == "sample_location")
						{
							$session->write_value("SAMPLE_LOCATION",$value[1],true);
						}
						if ($value[0] == "sample_description")
						{
							$session->write_value("SAMPLE_DESCRIPTION",$value[1],true);
						}
					}
				break;
				
				case "4":
					foreach($data_array as $key => $value)
					{
						switch($value[0]):
							
							case "sample_template_data_type_id":
								$session->write_value("SAMPLE_TEMPLATE_DATA_TYPE_ID", $value[1], true);	
							break;
							
							case "sample_template_data_type":
								$session->write_value("SAMPLE_TEMPLATE_DATA_TYPE", $value[1], true);	
							break;
								
							default:
								if (strpos($value[0], "-vartype") === false)
								{
									$template_data_array[$value[0]] = $value[1];
								}
							break;
						
						endswitch;
					}
					
					if (is_array($template_data_array) and count($template_data_array) >= 1)
					{
						$session->write_value("SAMPLE_TEMPLATE_DATA_ARRAY", $template_data_array, true);
					}
				break;
			endswitch;
		}
	}
	
	/**
	 * @param string $username
	 * @param string $session_id
	 * @return string
	 */
	public static function run($username, $session_id)
	{
		global $session, $user, $transaction;
			
		$sample_add_role				= $session->read_value("SAMPLE_ADD_ROLE");
		
		$sample_item_retrace 			= $session->read_value("SAMPLE_ITEM_RETRACE");
		$sample_item_get_array			= $session->read_value("SAMPLE_ITEM_GET_ARRAY");
		$sample_item_keywords			= $session->read_value("SAMPLE_ITEM_KEYWORDS");
		$sample_item_description		= $session->read_value("SAMPLE_ITEM_DESCRIPTION");
		
		$sample_organ_unit 				= $session->read_value("SAMPLE_ORGANISATION_UNIT");
		$sample_template				= $session->read_value("SAMPLE_TEMPLATE");
		$sample_name					= $session->read_value("SAMPLE_NAME");
		$sample_manufacturer			= $session->read_value("SAMPLE_MANUFACTURER_ID");	
		$sample_manufacturer_name		= $session->read_value("SAMPLE_MANUFACTURER_NAME");			
		$sample_location				= $session->read_value("SAMPLE_LOCATION");
		$sample_expiry					= $session->read_value("SAMPLE_EXPIRY");
		$sample_expiry_warning			= $session->read_value("SAMPLE_EXPIRY_WARNING");
		$sample_desc					= $session->read_value("SAMPLE_DESCRIPTION");
		$sample_template_data_type  	= $session->read_value("SAMPLE_TEMPLATE_DATA_TYPE");	
		$sample_template_data_type_id	= $session->read_value("SAMPLE_TEMPLATE_DATA_TYPE_ID");	
		$sample_template_data_array		= $session->read_value("SAMPLE_TEMPLATE_DATA_ARRAY");	

		$transaction_id = $transaction->begin();
		
		try
		{
			$sample = new Sample(null);
	
			$sample->set_template_data($sample_template_data_type, $sample_template_data_type_id, $sample_template_data_array);
	
			$sample_id = $sample->create($sample_organ_unit, $sample_template, $sample_name, $sample_manufacturer, $sample_location, $sample_desc, null, $sample_expiry, $sample_expiry_warning);
			
			if ($sample_add_role == "item" or $sample_add_role == "item_parent")
			{
				// Special Parent Sample Case
				if ($sample_add_role == "item_parent")
				{
					$parent_sample = new Sample($sample_item_get_array['sample_id']);
					$sample_item_get_array['sample_id'] = $sample_id;
					$sample_item_get_array['parent_sample'] = "1";
					$event_item_id = $parent_sample->get_item_id();
				}
				else
				{
					$event_item_id = $sample->get_item_id();
				}
				
				$post_array = array();
				$post_array['keywords'] = $sample_item_keywords;
				$post_array['description'] = $sample_item_description;
				
				$item_add_event = new ItemAddEvent($event_item_id, $sample_item_get_array, $post_array, true, "sample");
				$event_handler = new EventHandler($item_add_event);
				if ($event_handler->get_success() == true)
				{
					// Nothing
				}
			}
		}
		catch(BaseException $e)
		{
			if ($transaction_id != null)
			{
				$transaction->rollback($transaction_id);
			}
			throw $e;
		}
		
		$session->delete_value("SAMPLE_ADD_ROLE");
		$session->delete_value("SAMPLE_ADD_ITEM_HOLDER_CLASS");
		$session->delete_value("SAMPLE_ADD_ITEM_HOLDER_ID");
		
		$session->delete_value("SAMPLE_ITEM_RETRACE");
		$session->delete_value("SAMPLE_ITEM_GET_ARRAY");
		$session->delete_value("SAMPLE_ITEM_KEYWORDS");
		$session->delete_value("SAMPLE_ITEM_DESCRIPTION");
		$session->delete_value("SAMPLE_ITEM_TYPE_ARRAY");
		
		$session->delete_value("SAMPLE_ORGANISATION_UNIT");
		$session->delete_value("SAMPLE_TEMPLATE");
		$session->delete_value("SAMPLE_NAME");
		$session->delete_value("SAMPLE_MANUFACTURER_ID");
		$session->delete_value("SAMPLE_MANUFACTURER_NAME");
		$session->delete_value("SAMPLE_LOCATION");
		$session->delete_value("SAMPLE_EXPIRY");
		$session->delete_value("SAMPLE_EXPIRY_WARNING");
		$session->delete_value("SAMPLE_DESCRIPTION");		
		$session->delete_value("SAMPLE_TEMPLATE_DATA_TYPE");
		$session->delete_value("SAMPLE_TEMPLATE_DATA_TYPE_ID");	
		$session->delete_value("SAMPLE_TEMPLATE_DATA_ARRAY");
		
		if ($sample_add_role == "item" or $sample_add_role == "item_parent")
		{
			if ($transaction_id != null)
			{
				$transaction->commit($transaction_id);
			}
			
			if ($sample_item_retrace)
			{
				$params = http_build_query(Retrace::resolve_retrace_string($sample_item_retrace),'','&');
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
			if ($transaction_id != null)
			{
				$transaction->commit($transaction_id);
			}
			
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
}
?>