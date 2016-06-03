<?php
/**
 * @package data
 * @version 0.4.0.0
 * @author Roman Konertz <konertz@open-lims.org>
 * @copyright (c) 2008-2016 by Roman Konertz
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
 * Value Form IO Class
 * @package data
 */
class ValueFormIO
{
	private $shape_array;
	private $content_array;
	
	private $field_prefix;
	private $field_class;
	private $table_class;
	
	private $current_format_max_column;
	
	private $folder_id;
	
	/**
	 * @param integer $value_id
	 * @param integer $type_id
	 * @param integer $folder_id
	 * @param array $value_obj_content_array
	 */
	function __construct($value_id = null, $type_id = null, $folder_id = null, $value_obj_content_array = null)
	{		
		if ($value_id)
		{
			$value_obj = Value::get_instance($value_id);
			
			if (is_array($value_obj_content_array))
			{
				$value_obj->set_content_array($value_obj_content_array);
			}
			
			$this->shape_array = $value_obj->get_value_shape();
			$this->content_array = $value_obj->get_value_content(false);
		}
		elseif($type_id)
		{
			$value_obj = Value::get_instance(null);
			
			if (is_array($value_obj_content_array))
			{
				$value_obj->set_content_array($value_obj_content_array);
			}
			
			$this->shape_array = $value_obj->get_value_shape($type_id, $folder_id);
			$this->content_array = $value_obj->get_value_content(false, $type_id, $folder_id);
		}
	}
	
	/**
	 * @param array $array
	 * @return string
	 */
	private function solve_entries($array)
	{
		global $regional;
		
		$return_string = "";
		
		if (is_array($array))
		{
			switch($array['type']):
				case "format":
					if (!$this->table_class)
					{
						$table_class = "";
					}
					else
					{
						$table_class = $this->table_class;
					}
					
					$return_string .= "<table class='".$table_class."'>";
					
					$this->current_format_max_column = $array['max_column'];
					
					foreach ($array as $key => $value)
					{
						if (is_numeric($key))
						{
							$return_string .= $this->solve_entries($value);
						}
					}
					
					$this->current_format_max_column = 0;
					
					$return_string .= "</table>";
				break;
				
				case "line":				
					$return_string .= "<tr>";
					
					$column_key_counter = 0;
					
					foreach ($array as $key => $value)
					{
						if (is_numeric($key))
						{
							if (is_array($array['colspan']) and count($array['colspan']) >= 1)
							{
								if ($array['colspan'][$column_key_counter])
								{
									$return_string .= "<td colspan='".$array['colspan'][$column_key_counter]."'>";
									$array['max_column'] = $array['max_column'] + ($array['colspan'][$column_key_counter]-1);
								}
								else
								{
									$return_string .= "<td>";
								}
							}
							else
							{
								$return_string .= "<td>";
							}
							
							$return_string .= $this->solve_entries($value);
							$return_string .= "</td>";
						}
					}
					
					if ($array['max_column'] < $this->current_format_max_column)
					{
						$column_diff = $this->current_format_max_column - $array['max_column'];
						for ($i=1;$i<=$column_diff;$i++)
						{
							$return_string .= "<td></td>";
						}
					}
					
					$return_string .= "</tr>";
				break;
				
				case "element":
					if (is_array($array['content']) and $array['element'])
					{
						switch($array['element']):
							case "print":
								if ($array['content']['format'])
								{
									$return_string .= "<span class='".$array['content']['format']."'>";
								}
								
								if (count($array['content']['value']) == 1 and $array['content']['value'][0])
								{
									$array['content']['value'][0] = str_replace("[high]","<sup>",$array['content']['value'][0]);
									$array['content']['value'][0] = str_replace("[low]","<sub>",$array['content']['value'][0]);
									$array['content']['value'][0] = str_replace("[/high]","</sup>",$array['content']['value'][0]);
									$array['content']['value'][0] = str_replace("[/low]","</sub>",$array['content']['value'][0]);
									
									$return_string .= $array['content']['value'][0];
								}
								
								if ($array['content']['format'])
								{
									$return_string .= "</span>";
								}
							break;
							
							case "field":
								if (is_array($this->content_array))
								{
									foreach($this->content_array as $key => $value)
									{
										if (trim(strtolower($value['name'])) == trim(strtolower($array['content']['name'])))
										{
											$element_content = $value['content'][0];
										}
									}
									
									if (!$element_content)
									{
										$element_content = $array['content']['default'];
									}
								}
								else
								{
									$element_content = $array['content']['default'];
								}
								
								if ($this->field_prefix)
								{
									$field_name = $this->field_prefix."-".$array['content']['name'];
								}
								else
								{
									$field_name = $array['content']['name'];
								}
								
								switch($array['content']['requirement']):
									case 1:
										$requirement_class = "DataValueFieldRequired";
									break;
									
									case 2:
										$requirement_class = "DataValueFieldRequired DataValueFieldRequiredImportant";
									break;
									
									default:
										$requirement_class = "";
									break;
								endswitch;
								
								switch($array['content']['vartype']):
									case "integer":
										$var_type_class = "DataValueFieldTypeInteger";
									break;
									
									case "float":
										$var_type_class = "DataValueFieldTypeFloat";
										if ($element_content)
										{											
											$element_content = str_replace(".", $regional->get_decimal_separator(), $element_content);
										}
									break;
									
									default:
										$var_type_class = "DataValueFieldTypeString";
									break;
								endswitch;
								
								if ($array['content']['min_value'])
								{
									$min_value_class = "DataValueFieldMinValue DataValueFieldMinValue-".$array['content']['min_value'];
								}
								
								if ($array['content']['max_value'])
								{
									$max_value_class = "DataValueFieldMaxValue DataValueFieldMaxValue-".$array['content']['max_value'];
								}
								
								switch($array['content']['type']):
									case "textfield":
										$return_string .= "<input type='textfield' name='".$field_name."' value='".$element_content."' size='".$array['content']['length']."' class='".$this->field_class." ".$requirement_class." ".$var_type_class." ".$min_value_class." ".$max_value_class."' />";
									break;
								
									case "textarea":
										$return_string .= "<textarea name='".$field_name."' cols='".$array['content']['size']['cols']."' rows='".$array['content']['size']['rows']."' class='".$this->field_class." ".$requirement_class." ".$var_type_class."'>".$element_content."</textarea>";
									break;
									
									case "checkbox":
										$return_string .= "<select type='checkbox' name='".$field_name."' class='".$this->field_class."' />\n";
									break;
									
									case "dropdown":
										$return_string .= "<select name='".$field_name."' class='".$this->field_class." ".$requirement_class."'>";
										
										if (is_array($array['content']['value']) and count($array['content']['value']) >= 1) {
			
											foreach ($array['content']['value'] as $value_var_key => $value_var_value)
											{
												if (!is_array($value_var_value))
												{
													if ($value_var_value == $element_content)
													{
														$return_string .= "<option selected='selected'>".$value_var_value."</option>";
													}
													else
													{
														$return_string .= "<option>".$value_var_value."</option>";
													}
												}
											}
										}
										
										$return_string .= "</select>";
									break;
								endswitch;
								
								unset($element_content);
								
							break;
						endswitch;
					}
				break;
				
			endswitch;
			
			return $return_string;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @param string $field_prefix
	 */
	public function set_field_prefix($field_prefix)
	{
		$this->field_prefix = $field_prefix;
	}
	
	/**
	 * @param string $field_prefix
	 */
	public function set_field_class($field_class)
	{
		$this->field_class = $field_class;
	}
	
	/**
	 * @param string $field_prefix
	 */
	public function set_table_class($table_class)
	{
		$this->table_class = $table_class;
	}
	
	/**
	 * @return $field_prefix
	 */
	public function get_content()
	{
		if(is_array($this->shape_array) and count($this->shape_array) >= 1)
		{
			foreach($this->shape_array as $key => $value)
			{
				$return_value = $this->solve_entries($value);
			}
		}
		
		return $return_value;
	}
}