<?php
/**
 * @package data
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
 * Value IO Class
 * @package data
 */
class ParameterIO
{	
	public static function detail($parameter = null, $target_params = null, $display_header = true)
	{
		global $regional;
		
		if (is_object($parameter) and $target_params)
		{
			$retrace = "index.php?".$target_params;		
			$parameter_id = $parameter->get_id();	
		}
		elseif(is_numeric($_GET['parameter_id']))
		{
			$parameter_id = $_GET['parameter_id'];
			$parameter = ParameterTemplateParameter::get_instance($parameter_id);
			
			$paramquery = $_GET;
			unset($paramquery['action']);
			unset($paramquery['parameter_id']);
			$params = http_build_query($paramquery);
			$retrace = "index.php?".$params;
			
			if ($_GET['version'] and is_numeric($_GET['version']))
			{
				$parameter->open_internal_revision($_GET['version']);
			}
		}
		else
		{
			throw new ParameterIDMissingException();
		}

		if ($parameter->is_read_access())
		{	
			$parameter_template_id = $parameter->get_template_id();
			$parameter_template = new ParameterTemplate($parameter_template_id);
			
			$parameter_template_field_array = $parameter_template->get_fields();
			$parameter_template_limit_array = $parameter_template->get_limits();
			$parameter_possible_methods_array = $parameter_template->get_methods();
			
			$parameter_value_array = $parameter->get_values();
			$parameter_method_array = $parameter->get_methods();
			$parameter_limit_array = $parameter->get_limits();
			
			
			$output_template_limit_array = array();
			$output_template_limit_counter = 0;
			
			if(is_array($parameter_template_limit_array) and count($parameter_template_limit_array) >= 1)
			{
				$current_limit = $parameter->get_limit_id();
				
				foreach($parameter_template_limit_array as $key => $value)
				{
					$output_template_limit_array[$output_template_limit_counter]['value'] = $value['pk'];
					$output_template_limit_array[$output_template_limit_counter]['content'] = $value['name'];
					
					if ($value['pk'] == $current_limit)
					{
						$output_template_limit_array[$output_template_limit_counter]['selected'] = "selected='selected'";
					}
					else
					{
						$output_template_limit_array[$output_template_limit_counter]['selected'] = "";
					}
					
					$output_template_limit_counter++;
				}
			}
			
			
			$output_template_field_array = array();
			$output_template_field_counter = 0;
				
			if(is_array($parameter_template_field_array) and count($parameter_template_field_array) >= 1)
			{
				foreach($parameter_template_field_array as $key => $value)
				{
					$output_template_field_array[$output_template_field_counter]['id'] = $key;
					$output_template_field_array[$output_template_field_counter]['pk']= $value['pk'];
					$output_template_field_array[$output_template_field_counter]['name'] = $value['name'];
					
					if (is_numeric($parameter_value_array[$value['pk']]))
					{
						$regionalized_value = str_replace(".", $regional->get_decimal_separator(), $parameter_value_array[$value['pk']]);
						$output_template_field_array[$output_template_field_counter]['value'] = $regionalized_value;
					}
					else
					{
						$output_template_field_array[$output_template_field_counter]['value'] = "";
					}
					
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
					
					if ($parameter_limit_array[$value['pk']])
					{
						if (is_numeric($parameter_limit_array[$value['pk']]['usl']))
						{
							$output_template_field_array[$output_template_field_counter]['usl'] = $parameter_limit_array[$value['pk']]['usl'];
						}
						else
						{
							$output_template_field_array[$output_template_field_counter]['usl'] = "";
						}
						
						if (is_numeric($parameter_limit_array[$value['pk']]['lsl']))
						{
							$output_template_field_array[$output_template_field_counter]['lsl'] = $parameter_limit_array[$value['pk']]['lsl'];
						}
						else
						{
							$output_template_field_array[$output_template_field_counter]['lsl'] = "";
						}
					}
					else
					{
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
					
					if (is_numeric($value['unit']))
					{
						if ($value['unit_exponent'] < 0)
						{
							$unit_exponent = $value['unit_exponent']*-1;
							$unit_prefix = MeasuringUnit::get_prefix($unit_exponent, false);
						}
						else
						{
							$unit_prefix = MeasuringUnit::get_prefix( $value['unit_exponent'], true);
						}
						
						$measuring_unit = new MeasuringUnit($value['unit']);
						
						$output_template_field_array[$output_template_field_counter]['unit'] = $unit_prefix[1]."".$measuring_unit->get_unit_symbol();
					}
					elseif (is_numeric($value['unit_ratio']))
					{
						$measuring_unit_ratio = new MeasuringUnitRatio($value['unit_ratio']);
						$output_template_field_array[$output_template_field_counter]['unit'] = $measuring_unit_ratio->get_symbol();
					}
					else
					{
						$output_template_field_array[$output_template_field_counter]['unit'] = "";
					}
					
					$method_counter = 1;
					
					$output_template_field_array[$output_template_field_counter][0]['value'] = "0";
					$output_template_field_array[$output_template_field_counter][0]['content'] = "none";
					
					if (is_array($parameter_possible_methods_array) and count($parameter_possible_methods_array) >= 1)
					{						
						foreach($parameter_possible_methods_array as $method_key => $method_value)
						{
							$output_template_field_array[$output_template_field_counter][$method_counter]['value'] = $method_key;
							$output_template_field_array[$output_template_field_counter][$method_counter]['content'] = $method_value;
							$method_counter++;
						}
					}
					
					$output_template_field_counter++;
				}
			}
				
			$template = new HTMLTemplate("data/parameter_detail.html");
						
			$template->set_var("session_id", $_GET['session_id']);
			$template->set_var("parameter_id", $parameter_id);
			$template->set_var("type_id", $parameter_template_id);
			
			$template->set_var("thousand_separator", $regional->get_thousand_separator());
			$template->set_var("decimal_separator", $regional->get_decimal_separator());
			
			$template->set_var("name", $parameter_template->get_name());
			$template->set_var("limits", $output_template_limit_array);
			$template->set_var("fields", $output_template_field_array);
			
			$template->set_var("retrace", $retrace);
			
			$template->output();
		}
		else
		{
			throw new DataSecurityAccessDeniedException();
		}
	}
	
	/**
	 * @throws FolderIDMissingException
	 */
	public static function add_parameter_item($type_array, $category_array, $holder_class, $holder_id, $position_id)
	{
		global $regional;
		
		if (class_exists($holder_class))
		{
			$item_holder = new $holder_class($holder_id);
			
			if ($item_holder instanceof ItemHolderInterface)
			{
				$folder_id = $item_holder->get_item_holder_value("folder_id", $position_id);
			}
		}
		
		if(count($type_array) != 1 and $_POST['type_id'])
		{
			$parameter_template_id = $_POST['type_id'];
		}
		elseif(count($type_array) == 1 and $type_array[0] !== "")
		{
			$parameter_template_id = ParameterTemplate::get_id_by_internal_name($type_array[0]);
		}
		else
		{
			$parameter_template_id = null;
		}
			
		
		if (!is_numeric($parameter_template_id))
		{
			$template = new HTMLTemplate("data/parameter_select_list.html");
				
			$paramquery = $_GET;
			$params = http_build_query($paramquery,'','&#38;');
			
			$template->set_var("params", $params);
			
			if (count($type_array) > 1)
			{
				$template->set_var("select", ParameterTemplate::list_templates($type_array));
			}
			else
			{
				$template->set_var("select", ParameterTemplate::list_templates());
			}
			
			if ($_POST['keywords'])
			{
				$template->set_var("keywords", $_POST['keywords']);
			}
			else
			{
				$template->set_var("keywords", "");
			}
			
			if ($_POST['description'])
			{
				$template->set_var("description", $_POST['description']);
			}
			else
			{
				$template->set_var("description", "");	
			}
			
			$template->output();
		}
		else
		{
			$parameter_template = new ParameterTemplate($parameter_template_id);
			
			$parameter_template_field_array = $parameter_template->get_fields();
			$parameter_template_limit_array = $parameter_template->get_limits();
			$parameter_possible_methods_array = $parameter_template->get_methods();
			
			$output_template_limit_array = array();
			$output_template_limit_counter = 0;
			
			if(is_array($parameter_template_limit_array) and count($parameter_template_limit_array) >= 1)
			{
				foreach($parameter_template_limit_array as $key => $value)
				{
					$output_template_limit_array[$output_template_limit_counter]['value'] = $value['pk'];
					$output_template_limit_array[$output_template_limit_counter]['selected'] = "";
					$output_template_limit_array[$output_template_limit_counter]['content'] = $value['name'];
					$output_template_limit_counter++;
				}
			}
			
			
			$output_template_field_array = array();
			$output_template_field_counter = 0;
			
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
					
					if (is_numeric($value['unit']))
					{
						if ($value['unit_exponent'] < 0)
						{
							$unit_exponent = $value['unit_exponent']*-1;
							$unit_prefix = MeasuringUnit::get_prefix($unit_exponent, false);
						}
						else
						{
							$unit_prefix = MeasuringUnit::get_prefix( $value['unit_exponent'], true);
						}
						
						$measuring_unit = new MeasuringUnit($value['unit']);
						
						$output_template_field_array[$output_template_field_counter]['unit'] = $unit_prefix[1]."".$measuring_unit->get_unit_symbol();
					}
					elseif (is_numeric($value['unit_ratio']))
					{
						$measuring_unit_ratio = new MeasuringUnitRatio($value['unit_ratio']);
						$output_template_field_array[$output_template_field_counter]['unit'] = $measuring_unit_ratio->get_symbol();
					}
					else
					{
						$output_template_field_array[$output_template_field_counter]['unit'] = "";
					}
					
					$method_counter = 1;
					
					$output_template_field_array[$output_template_field_counter][0]['value'] = "0";
					$output_template_field_array[$output_template_field_counter][0]['content'] = "none";
					
					if (is_array($parameter_possible_methods_array) and count($parameter_possible_methods_array) >= 1)
					{						
						foreach($parameter_possible_methods_array as $method_key => $method_value)
						{
							$output_template_field_array[$output_template_field_counter][$method_counter]['value'] = $method_key;
							$output_template_field_array[$output_template_field_counter][$method_counter]['content'] = $method_value;
							$method_counter++;
						}
					}
					
					$output_template_field_counter++;
				}
			}
			
			
			$template = new HTMLTemplate("data/parameter_add.html");
			
			if ($_GET['retrace'])
			{
				$template->set_var("retrace", "index.php?".http_build_query(Retrace::resolve_retrace_string($_GET['retrace'])));
			}
			else
			{
				$template->set_var("retrace", "index.php?username=".$_GET['username']."&session_id=".$_GET['session_id']);
			}
			
			$template->set_var("session_id", $_GET['session_id']);
			$template->set_var("folder_id", $folder_id);
			$template->set_var("type_id", $parameter_template_id);
			$template->set_var("get_array", serialize($_GET));
			
			$template->set_var("thousand_separator", $regional->get_thousand_separator());
			$template->set_var("decimal_separator", $regional->get_decimal_separator());
			
			$template->set_var("name", $parameter_template->get_name());
			$template->set_var("limits", $output_template_limit_array);
			$template->set_var("fields", $output_template_field_array);
			
			$template->output();
		}
	}

	public static function edit_parameter_item($item_id)
	{
		if (is_numeric($item_id))
		{
			$data_entity_id = DataEntity::get_entry_by_item_id($item_id);
			$parameter_id = Parameter::get_parameter_id_by_data_entity_id($data_entity_id);
		
			$parameter = Parameter::get_instance($parameter_id);
	
			if ($parameter->is_read_access())
			{		
				self::detail($parameter, http_build_query(Retrace::resolve_retrace_string($_GET['retrace'])), false);
			}
		}
		else
		{
			throw new ItemIDMissingException();
		}
	}
	
	public static function history()
	{
		
	}
}
?>