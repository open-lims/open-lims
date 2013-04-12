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
	/**
	 * @throws FolderIDMissingException
	 */
	public static function add_parameter_item($type_array, $category_array, $holder_class, $holder_id, $position_id)
	{
		$parameter_template_id = 23;
		
		if (class_exists($holder_class))
		{
			$item_holder = new $holder_class($holder_id);
			
			if ($item_holder instanceof ItemHolderInterface)
			{
				$folder_id = $item_holder->get_item_holder_value("folder_id", $position_id);
			}
		}
		
		$parameter_template = new ParameterTemplate($parameter_template_id);
		
		$parameter_template_field_array = $parameter_template->get_fields();
		$parameter_template_limit_array = $parameter_template->get_limits();
		
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

				// Ratio
				
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
		
		$template->set_var("name", $parameter_template->get_name());
		$template->set_var("fields", $output_template_field_array);
		
		$template->output();
	}
}
?>