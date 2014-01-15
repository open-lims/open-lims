<?php
/**
 * @package data
 * @version 0.4.0.0
 * @author Roman Konertz <konertz@open-lims.org>
 * @copyright (c) 2008-2014 by Roman Konertz
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
 * Parameter Template Admin IO Class
 * @package data
 */
class AdminParameterTemplateIO
{
	public static function home()
	{
		$list = new List_IO("DataAdminParameterTemplate", "ajax.php?nav=data", "admin_list_parameter_templates", "admin_count_parameter_templates", "0", "DataAdminParameterTemplate");
		
		$list->add_column("", "symbol", false, "20px");
		$list->add_column(Language::get_message("DataGeneralListColumnName", "general"), "name");
		$list->add_column(Language::get_message("DataGeneralListColumnInternalName", "general"), "internal_name");
		$list->add_column(Language::get_message("DataGeneralListColumnCreatedBy", "general"), "created_by");
		$list->add_column(Language::get_message("DataGeneralListColumnDateTime", "general"), "datetime");
		$list->add_column("", "delete", false, "20px");
		
		$template = new HTMLTemplate("data/admin/parameter_template/list.html");	
	
		$paramquery = $_GET;
		$paramquery['action'] = "add";
		unset($paramquery['nextpage']);
		$params = http_build_query($paramquery,'','&#38;');
		
		$template->set_var("add_params", $params);
	
		$template->set_var("list", $list->get_list());		
		
		$template->output();			
	}
	
	public static function add()
	{
		$template = new HTMLTemplate("data/admin/parameter_template/add.html");
		
		$paramquery = $_GET;
		unset($paramquery['action']);
		$params = http_build_query($paramquery, '', '&');
		
		$template->set_var("retrace", "index.php?".$params);
		$template->set_var("session_id", $_GET['session_id']);
		
		$result = array();
		$counter = 0;
		
		$measuring_unit_array = MeasuringUnit::get_categorized_list();
		
		if (is_array($measuring_unit_array) and count($measuring_unit_array) >= 1)
		{
			foreach($measuring_unit_array as $key => $value)
			{
				if ($value['headline'] == true)
				{
					$result[$counter]['disabled'] = "disabled='disabled'";
				}
				else
				{
					$result[$counter]['disabled'] = "";
				}
				
				$result[$counter]['value'] = $value['id']."-".$value['exponent'];
				
				$result[$counter]['selected'] = "";
				$result[$counter]['content'] = $value['name'];
				$counter++;
			}
		}
		
		$template->set_var("measuring_units",$result);
		
		$template->output();
	}
	
	public static function edit()
	{
		if (isset($_GET['id']) and is_numeric($_GET['id']))
		{			
			$parameter_template = new ParameterTemplate($_GET['id']);
			
			$template = new HTMLTemplate("data/admin/parameter_template/edit.html");
			
			$paramquery = $_GET;
			unset($paramquery['action']);
			unset($paramquery['id']);
			$params = http_build_query($paramquery, '', '&');
			
			$template->set_var("retrace", "index.php?".$params);
			$template->set_var("session_id", $_GET['session_id']);
			$template->set_var("name", $parameter_template->get_name());
			$template->set_var("internal_name", $parameter_template->get_internal_name());
			
			$measuring_unit_array = MeasuringUnit::get_categorized_list();
			
			$parameter_template_field_array = $parameter_template->get_fields();
			$parameter_template_limit_array = $parameter_template->get_limits();
			$output_template_field_array = array();
			$output_template_field_counter = 0;

			$parameter_template_limit_json = json_encode($parameter_template_limit_array);
			
			if(is_array($parameter_template_field_array) and count($parameter_template_field_array) >= 1)
			{
				foreach($parameter_template_field_array as $key => $value)
				{
					$output_template_field_array[$output_template_field_counter]['id'] = $key;
					$output_template_field_array[$output_template_field_counter]['pk']= $value['pk'];
					$output_template_field_array[$output_template_field_counter]['name'] = $value['name'];
					
					if (is_numeric($value['min']))
					{
						$output_template_field_array[$output_template_field_counter]['min'] = $value['min'];
					}
					else
					{
						$output_template_field_array[$output_template_field_counter]['min'] = "";
					}
					
					if (is_numeric($value['max']))
					{
						$output_template_field_array[$output_template_field_counter]['max'] = $value['max'];
					}
					else
					{
						$output_template_field_array[$output_template_field_counter]['max'] = "";
					}
					
					if (is_numeric($parameter_template_limit_array[0]['usl'][$key]))
					{
						$output_template_field_array[$output_template_field_counter]['usl'] = $parameter_template_limit_array[0]['usl'][$key];
					}
					else
					{
						$output_template_field_array[$output_template_field_counter]['usl'] = "";
					}
					
					if (is_numeric($parameter_template_limit_array[0]['lsl'][$key]))
					{
						$output_template_field_array[$output_template_field_counter]['lsl'] = $parameter_template_limit_array[0]['lsl'][$key];
					}
					else
					{
						$output_template_field_array[$output_template_field_counter]['lsl'] = "";
					}
						
					if ($key == 1)
					{
						$output_template_field_array[$output_template_field_counter]['class'] = "odd";
					}
					else
					{
						if (($output_template_field_counter % 2) == 0)
						{
							$output_template_field_array[$output_template_field_counter]['class'] = "odd DataParameterTemplateField";
						}
						else
						{
							$output_template_field_array[$output_template_field_counter]['class'] = "evan DataParameterTemplateField";
						}
					}

					if (is_array($measuring_unit_array) and count($measuring_unit_array) >= 1)
					{
						$measuring_unit_counter = 0;
						
						foreach($measuring_unit_array as $measuring_unit_key => $measuring_unit_value)
						{
							if ($measuring_unit_value['headline'] == true)
							{
								$output_template_field_array[$output_template_field_counter][$measuring_unit_counter]['disabled'] = "disabled='disabled'";
								$output_template_field_array[$output_template_field_counter][$measuring_unit_counter]['selected'] = "";
							}
							else
							{
								$output_template_field_array[$output_template_field_counter][$measuring_unit_counter]['disabled'] = "";
								
								if ($measuring_unit_value['id'] == $value['unit'] and $measuring_unit_value['exponent'] == $value['unit_exponent'])
								{
									$output_template_field_array[$output_template_field_counter][$measuring_unit_counter]['selected'] = "selected='selected'";
								}
								elseif ($measuring_unit_value['exponent'] == "" and $measuring_unit_value['id'] == $value['unit_ratio'])
								{
									$output_template_field_array[$output_template_field_counter][$measuring_unit_counter]['selected'] = "selected='selected'";
								}
								else
								{
									$output_template_field_array[$output_template_field_counter][$measuring_unit_counter]['selected'] = "";
								}
							}

							$output_template_field_array[$output_template_field_counter][$measuring_unit_counter]['value'] = $measuring_unit_value['id']."-".$measuring_unit_value['exponent'];
							$output_template_field_array[$output_template_field_counter][$measuring_unit_counter]['content'] = $measuring_unit_value['name'];
							$measuring_unit_counter++;
						}
					}
					
					if ($key == 1)
					{
						$output_template_field_array[$output_template_field_counter]['deletable'] = false;
					}
					else
					{
						$output_template_field_array[$output_template_field_counter]['deletable'] = true;
					}
										
					$output_template_field_counter++;
				}
			}
			
			$template->set_var("fields",$output_template_field_array);
			
			$template->set_var("limit_json", $parameter_template_limit_json);
			$template->set_var("limit_counter", (count($parameter_template_limit_array)-1));
			$template->set_var("line_counter", count($parameter_template_field_array));
			
			$template->set_var("id", $_GET['id']);
			
			$template->output();
		}
		else
		{
			// Exception
		}
	}
	
	public static function handler()
	{			
		switch($_GET['action']):
			
			case "add":
				self::add();
			break;
			
			case "edit":
				self::edit();
			break;
			
			case "delete":
				self::delete();
			break;
						
			default:
				self::home();
			break;
		
		endswitch;
	}
}
?>