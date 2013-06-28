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
 * 
 */
require_once("interfaces/parameter_template.interface.php");

if (constant("UNIT_TEST") == false or !defined("UNIT_TEST"))
{
	require_once("access/parameter_template.access.php");
	require_once("access/parameter_template_has_field.access.php");
	require_once("access/parameter_field.access.php");
	require_once("access/parameter_field_limit.access.php");
	require_once("access/parameter_field_has_method.access.php");
	require_once("access/parameter_limit.access.php");
}

/**
 * Parameter Template Class
 * @package data
 */
class ParameterTemplate implements ParameterTemplateInterface, EventListenerInterface
{
	private $parameter_template_id;
	private $parameter_template;
	
	function __construct($parameter_template_id = null)
	{
		if (is_numeric($parameter_template_id))
		{
			if (ParameterTemplate_Access::exist_id($parameter_template_id) == true)
			{
				$this->parameter_template_id = $parameter_template_id;
				$this->parameter_template = new ParameterTemplate_Access($parameter_template_id);
			}
			else
			{
				// throw new ParameterTemplateNotFoundException();
			}
		}
		else
		{
			$this->parameter_template_id = null;
			$this->parameter_template = new ParameterTemplate_Access(null);
		}
	}
	
	function __destruct()
	{
		unset($this->parameter_template_id);
		unset($this->parameter_template);
	}
	
	public function create($name, $internal_name, $field_array, $limit_array)
	{
		global $transaction, $user;
		
		if (is_array($field_array) and count($field_array) >= 1)
		{
			$transaction_id = $transaction->begin();
			
			$parameter_template = new ParameterTemplate_Access(null);
			if (($parameter_template_id = $parameter_template->create($internal_name, $name, $user->get_user_id())) === null)
			{
				if ($transaction_id != null)
				{
					$transaction->rollback($transaction_id);
				}
				return null;
			}
		
			$limit_counter = count($limit_array);
			$parameter_limit_id_array = array();
			
			for ($i=0;$i<=($limit_counter-1);$i++)
			{
				$parameter_limit = new ParameterLimit_Access(null);
				if (($parameter_limit_id_array[$i] = $parameter_limit->create($limit_array[$i]['name'])) == null)
				{
					if ($transaction_id != null)
					{
						$transaction->rollback($transaction_id);
					}
					return null;
				}
			}
			
			foreach($field_array as $key => $value)
			{
				foreach($value as $field_key => $field_value)
				{
					switch($field_key):
					
						case "name":
							$name = $field_value;	
						break;
						
						case "unit":
							$unit_array = explode("-", $field_value, 2);
							if (is_array($unit_array) and count($unit_array) === 2)
							{
								if (is_numeric($unit_array[1]))
								{
									$measuring_unit_id = $unit_array[0];
									$measuring_unit_exponent = $unit_array[1];
								}
								else
								{
									$measuring_unit_ratio_id = $unit_array[0];
								}
							}
							elseif(is_array($unit_array) and count($unit_array) === 1)
							{
								$measuring_unit_ratio_id = $unit_array[0];
							}
						break;
						
						case "min":
							$min_value = $field_value;	
						break;	
							
						case "max":
							$max_value = $field_value;	
						break;
							
						default:
		
						break;
					
					endswitch;
				}
				
				$parameter_field = new ParameterField_Access(null);
				if (($parameter_field_id = $parameter_field->create($name, $min_value, $max_value, $measuring_unit_id, $measuring_unit_exponent, $measuring_unit_ratio_id)) === null)
				{
					if ($transaction_id != null)
					{
						$transaction->rollback($transaction_id);
					}
					return null;
				}
				
				$parameter_template_has_field = new ParameterTemplateHasField_Access(null, null);
				if ($parameter_template_has_field->create($parameter_template_id, $parameter_field_id) === false)
				{
					if ($transaction_id != null)
					{
						$transaction->rollback($transaction_id);
					}
					return null;
				}
				
				for ($i=0;$i<=($limit_counter-1);$i++)
				{
					if (is_numeric($limit_array[$i]['usl'][$key]) or is_numeric($limit_array[$i]['lsl'][$key]))
					{
						$parameter_field_limit = new ParameterFieldLimit_Access(null, null);
						if ($parameter_field_limit->create($parameter_limit_id_array[$i], $parameter_field_id, $limit_array[$i]['usl'][$key], $limit_array[$i]['lsl'][$key]) == false)
						{
							if ($transaction_id != null)
							{
								$transaction->rollback($transaction_id);
							}
							return null;
						}
					}
				}
				
				unset($name);
				unset($min_value);
				unset($max_value);
				unset($measuring_unit_id);
				unset($measuring_unit_exponent);
				unset($measuring_unit_ratio_id);
			}
			
			if ($transaction_id != null)
			{
				$transaction->commit($transaction_id);
			}
			return $parameter_template_id;
		}
		else
		{
			return null;
		}
	}
	
	public function delete()
	{
		global $transaction;
		
		if ($this->parameter_template_id)
		{
			$transaction_id = $transaction->begin();
			
			$template_field_array = ParameterTemplateHasField_Access::list_fields_by_template_id($this->parameter_template_id);

			// Delete Limits
			$template_limit_array = ParameterFieldLimit_Access::list_parameter_limits_by_parameter_field_array($template_field_array);
			
			if (is_array($template_limit_array) and count($template_limit_array) >= 1)
			{
				foreach($template_limit_array as $key => $value)
				{
					if (ParameterFieldLimit_Access::delete_limits_by_parameter_limit_id($value) === false)
					{
						if ($transaction_id != null)
						{
							$transaction->rollback($transaction_id);
						}
						return false;
					}
					
					$parameter_limit = new ParameterLimit_Access($value);
					if ($parameter_limit->delete() === false)
					{
						if ($transaction_id != null)
						{
							$transaction->rollback($transaction_id);
						}
						return false;
					}
				}
			}
			
			// Delete Fields
			if (is_array($template_field_array) and count($template_field_array) >= 1)
			{
				foreach ($template_field_array as $key => $value)
				{					
					$parameter_template_field = new ParameterTemplateHasField_Access($this->parameter_template_id, $value);
					if ($parameter_template_field->delete() === false)
					{
						if ($transaction_id != null)
						{
							$transaction->rollback($transaction_id);
						}
						return false;
					}
					
					$parameter_field = new ParameterField_Access($value);
					if ($parameter_field->delete() === false)
					{
						if ($transaction_id != null)
						{
							$transaction->rollback($transaction_id);
						}
						return false;
					}
				}
			}
			
			// Delete Template
			if ($this->parameter_template->delete() === true)
			{
				if ($transaction_id != null)
				{
					$transaction->commit($transaction_id);
				}
				return true;
			}
			else
			{
				if ($transaction_id != null)
				{
					$transaction->rollback($transaction_id);
				}
				return false;
			}
		}
		else
		{
			return false;
		}
	}
		
	public function get_name()
	{
		if ($this->parameter_template_id)
		{
			return $this->parameter_template->get_name();
		}
		else
		{
			return null;
		}
	}
	
	public function get_internal_name()
	{
		if ($this->parameter_template_id)
		{
			return $this->parameter_template->get_internal_name();
		}
		else
		{
			return null;
		}
	}
	
	public function get_fields()
	{
		if ($this->parameter_template_id)
		{
			$template_field_array = ParameterTemplateHasField_Access::list_fields_by_template_id($this->parameter_template_id);
			
			if(is_array($template_field_array) and count($template_field_array) >= 1)
			{
				$return_array = array();
				$counter = 1;
				
				foreach($template_field_array as $key => $value)
				{
					$parameter_field = new ParameterField_Access($value);
					
					$return_array[$counter]['pk'] = $value;
					$return_array[$counter]['name'] = $parameter_field->get_name();
					$return_array[$counter]['unit'] = $parameter_field->get_measuring_unit_id();
					$return_array[$counter]['unit_exponent'] = $parameter_field->get_measuring_unit_exponent();
					$return_array[$counter]['unit_ratio'] = $parameter_field->get_measuring_unit_ratio_id();
					$return_array[$counter]['min'] = $parameter_field->get_min_value();
					$return_array[$counter]['max'] = $parameter_field->get_max_value();
					
					$counter++;
				}
				
				return $return_array;
			}
			else
			{
				return null;
			}
		}
		else
		{
			return null;
		}
	}
	
	public function get_limits($limit_id = null)
	{
		if ($this->parameter_template_id)
		{
			$template_field_array = ParameterTemplateHasField_Access::list_fields_by_template_id($this->parameter_template_id);
			
			if(is_array($template_field_array) and count($template_field_array) >= 1)
			{
				$limit_key_array = array();
				$limit_key_counter = 0;
				$return_array = array();
				$field_counter = 1;
				
				foreach($template_field_array as $key => $value)
				{
					$field_limit_array = ParameterFieldLimit_Access::list_field_limits_by_parameter_field_id($value);
					
					if (is_numeric($limit_id) and $field_limit_array[$limit_id])
					{						
						$return_array[0]['usl'][$field_counter] = $field_limit_array[$limit_id]['usl'];
						$return_array[0]['lsl'][$field_counter] = $field_limit_array[$limit_id]['lsl'];
					}
					else
					{
						foreach($field_limit_array as $limit_key => $limit_value)
						{
							if(is_numeric($limit_key_array[$limit_key]))
							{
								$limit_array_address = $limit_key_array[$limit_key];
							}
							else
							{
								$parameter_limit = new ParameterLimit_Access($limit_key);
								
								$limit_array_address = $limit_key_counter;
								$limit_key_array[$limit_key] = $limit_key_counter;
								$return_array[$limit_array_address]['name'] = $parameter_limit->get_name();
								$return_array[$limit_array_address]['pk'] = $limit_key;
								$return_array[$limit_array_address]['usl'][0] = null;
								$return_array[$limit_array_address]['lsl'][0] = null;
								$limit_key_counter++;
							}
							
							$return_array[$limit_array_address]['usl'][$field_counter] = $limit_value['usl'];
							$return_array[$limit_array_address]['lsl'][$field_counter] = $limit_value['lsl'];
						}
					}
					
					$field_counter++;
				}
				
				return $return_array;
			}
			else
			{
				return null;
			}
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * Returns an array with all possible methods
	 */
	public function get_methods()
	{
		if ($this->parameter_template_id)
		{
			$all_methods_array = ParameterMethod::list_methods();
			$template_field_array = ParameterTemplateHasField_Access::list_fields_by_template_id($this->parameter_template_id);
			
			if(is_array($template_field_array) and count($template_field_array) >= 1)
			{	
				$return_array = array();
								
				foreach($template_field_array as $key => $value)
				{
					$method_array = ParameterFieldFieldHasMethod_Access::list_methods_by_field_id($value);
					if (is_array($method_array) and count($method_array) >= 1)
					{
						$return_array[$value] = $method_array;
					}
					else
					{
						$return_array[$value] = $all_methods_array;
					}
				}
				
				return $return_array;
			}
			else
			{
				return null;
			}
		}
		else
		{
			return null;
		}
	}
	
	public function edit($name, $field_array, $limit_array)
	{
		global $transaction;
		
		if ($this->parameter_template_id and $name and is_array($field_array) and count($field_array) and is_array($limit_array))
		{			
			$transaction_id = $transaction->begin();
			
			if ($this->parameter_template->set_name($name) == false)
			{
				if ($transaction_id != null)
				{
					$transaction->rollback($transaction_id);
				}
				return false;
			}
						
			$limit_counter = count($limit_array);
			$parameter_limit_id_array = array();
			$current_limit_array = array();
			
			for ($i=0;$i<=($limit_counter-1);$i++)
			{
				if ($limit_array[$i] !== null)
				{
					if (isset($limit_array[$i]['pk']) and is_numeric($limit_array[$i]['pk']))
					{
						array_push($current_limit_array, $limit_array[$i]['pk']);
						
						/**
						 * @todo MINOR-IMPORTANT: insert a security check
						 */
						$parameter_limit_id_array[$i] = $limit_array[$i]['pk'];
						$parameter_limit = new ParameterLimit_Access($limit_array[$i]['pk']);
						if ($parameter_limit->set_name($limit_array[$i]['name']) == false)
						{
							if ($transaction_id != null)
							{
								$transaction->rollback($transaction_id);
							}
							return false;
						}
					}
					else
					{
						$parameter_limit = new ParameterLimit_Access(null);
						if (($parameter_limit_id_array[$i] = $parameter_limit->create($limit_array[$i]['name'])) == null)
						{
							if ($transaction_id != null)
							{
								$transaction->rollback($transaction_id);
							}
							return false;
						}
					}
				}
			}
			
			$current_field_array = array();
			$new_field_array = array();
			
			foreach($field_array as $key => $value)
			{
				foreach($value as $field_key => $field_value)
				{
					switch($field_key):
					
						case "name":
							$name = $field_value;	
						break;
						
						case "unit":
							$unit_array = explode("-", $field_value, 2);
							if (is_array($unit_array) and count($unit_array) === 2)
							{
								if (is_numeric($unit_array[1]))
								{
									$measuring_unit_id = $unit_array[0];
									$measuring_unit_exponent = $unit_array[1];
								}
								else
								{
									$measuring_unit_ratio_id = $unit_array[0];
								}
							}
							elseif(is_array($unit_array) and count($unit_array) === 1)
							{
								$measuring_unit_ratio_id = $unit_array[0];
							}
						break;
						
						case "min":
							$min_value = $field_value;	
						break;	
							
						case "max":
							$max_value = $field_value;	
						break;
							
						default:
		
						break;
					
					endswitch;
				}
				
				if (isset($value['pk']) and is_numeric($value['pk']))
				{
					$parameter_field_id = $value['pk'];
					
					array_push($current_field_array, $value['pk']);
					
					// Edit Field
					if (ParameterTemplateHasField_Access::field_exists_in_template($this->parameter_template_id, $value['pk']) == true)
					{
						$parameter_field = new ParameterField_Access($value['pk']);
						
						if ($parameter_field->set_name($name) == false)
						{
							if ($transaction_id != null)
							{
								$transaction->rollback($transaction_id);
							}
							return false;
						}
						
						if (is_numeric($measuring_unit_id) and is_numeric($measuring_unit_exponent))
						{
							if ($parameter_field->set_measuring_unit_id($measuring_unit_id) == false)
							{
								if ($transaction_id != null)
								{
									$transaction->rollback($transaction_id);
								}
								return false;
							}
							
							if ($parameter_field->set_measuring_unit_exponent($measuring_unit_exponent) == false)
							{
								if ($transaction_id != null)
								{
									$transaction->rollback($transaction_id);
								}
								return false;
							}
							
							if ($parameter_field->set_measuring_unit_ratio_id(null) == false)
							{
								if ($transaction_id != null)
								{
									$transaction->rollback($transaction_id);
								}
								return false;
							}
						}
						elseif (is_numeric($measuring_unit_ratio_id))
						{
							if ($parameter_field->set_measuring_unit_id(null) == false)
							{
								if ($transaction_id != null)
								{
									$transaction->rollback($transaction_id);
								}
								return false;
							}
							
							if ($parameter_field->set_measuring_unit_exponent(null) == false)
							{
								if ($transaction_id != null)
								{
									$transaction->rollback($transaction_id);
								}
								return false;
							}
							
							if ($parameter_field->set_measuring_unit_ratio_id($measuring_unit_ratio_id) == false)
							{
								if ($transaction_id != null)
								{
									$transaction->rollback($transaction_id);
								}
								return false;
							}
						}
						
						if (is_numeric($min_value))
						{
							if ($parameter_field->set_min_value($min_value) == false)
							{
								if ($transaction_id != null)
								{
									$transaction->rollback($transaction_id);
								}
								return false;
							}
						}
						
						if (is_numeric($max_value))
						{
							if ($parameter_field->set_max_value($max_value) == false)
							{
								if ($transaction_id != null)
								{
									$transaction->rollback($transaction_id);
								}
								return false;
							}
						}
					}
					else
					{
						// Exception: Security 
						if ($transaction_id != null)
						{
							$transaction->rollback($transaction_id);
						}
						return false;
					}
				}
				else
				{
					// New Field
					$parameter_field = new ParameterField_Access(null);
					if (($parameter_field_id = $parameter_field->create($name, $min_value, $max_value, $measuring_unit_id, $measuring_unit_exponent)) === null)
					{
						if ($transaction_id != null)
						{
							$transaction->rollback($transaction_id);
						}
						return false;
					}
					
					$parameter_template_has_field = new ParameterTemplateHasField_Access(null, null);
					if ($parameter_template_has_field->create($this->parameter_template_id, $parameter_field_id) === false)
					{
						if ($transaction_id != null)
						{
							$transaction->rollback($transaction_id);
						}
						return false;
					}
					
					array_push($new_field_array, $parameter_field_id);
				}
				
				for ($i=0;$i<=($limit_counter-1);$i++)
				{
					
					if (ParameterFieldLimit_Access::exist_field_limit($parameter_limit_id_array[$i], $parameter_field_id))
					{
						$parameter_field_limit = new ParameterFieldLimit_Access($parameter_limit_id_array[$i], $parameter_field_id);

						if (is_numeric($limit_array[$i]['lsl'][$key]))
						{
							if ($parameter_field_limit->set_lower_specification_limit($limit_array[$i]['lsl'][$key]) == false)
							{
								if ($transaction_id != null)
								{
									$transaction->rollback($transaction_id);
								}
								return false;
							}
						}
						
						if (is_numeric($limit_array[$i]['usl'][$key]))
						{
							if ($parameter_field_limit->set_upper_specification_limit($limit_array[$i]['usl'][$key]) == false)
							{
								if ($transaction_id != null)
								{
									$transaction->rollback($transaction_id);
								}
								return false;
							}
						}
					}
					else
					{
						if (is_numeric($limit_array[$i]['usl'][$key]) or ($limit_array[$i]['lsl'][$key]))
						{
							$parameter_field_limit = new ParameterFieldLimit_Access(null, null);
							if ($parameter_field_limit->create($parameter_limit_id_array[$i], $parameter_field_id, $limit_array[$i]['usl'][$key], $limit_array[$i]['lsl'][$key]) == false)
							{
								if ($transaction_id != null)
								{
									$transaction->rollback($transaction_id);
								}
								return false;
							}
						}
					}
				}
				
				unset($parameter_field_id);
				unset($name);
				unset($min_value);
				unset($max_value);
				unset($measuring_unit_id);
				unset($measuring_unit_exponent);
				unset($measuring_unit_ratio_id);
			}
			
			if (is_array($current_field_array) and count($current_field_array) >= 1)
			{
				$template_field_array = ParameterTemplateHasField_Access::list_fields_by_template_id($this->parameter_template_id);
				$field_delete_array = array_diff($template_field_array, $current_field_array);
				
				if (is_array($field_delete_array) and count($field_delete_array) >= 1)
				{
					foreach ($field_delete_array as $key => $value)
					{
						if (ParameterFieldLimit_Access::delete_limits_by_parameter_field_id($value) === false)
						{
							if ($transaction_id != null)
							{
								$transaction->rollback($transaction_id);
							}
							return false;
						}
						
						$parameter_template_field = new ParameterTemplateHasField_Access($this->parameter_template_id, $value);
						if ($parameter_template_field->delete() === false)
						{
							if ($transaction_id != null)
							{
								$transaction->rollback($transaction_id);
							}
							return false;
						}
						
						$parameter_field = new ParameterField_Access($value);
						if ($parameter_field->delete() === false)
						{
							if ($transaction_id != null)
							{
								$transaction->rollback($transaction_id);
							}
							return false;
						}
					}
				}
			}
			
			
			if (is_array($current_limit_array) and count($current_limit_array) >= 1)
			{
				$full_field_array = array_merge($new_field_array, $current_field_array);
				
				$template_limit_array = ParameterFieldLimit_Access::list_parameter_limits_by_parameter_field_array($full_field_array);
				$limit_delete_array = array_diff($template_limit_array, $current_limit_array);
				
				if (is_array($limit_delete_array) and count($limit_delete_array) >= 1)
				{
					foreach($limit_delete_array as $key => $value)
					{
						if (ParameterFieldLimit_Access::delete_limits_by_parameter_limit_id($value) === false)
						{
							if ($transaction_id != null)
							{
								$transaction->rollback($transaction_id);
							}
							return false;
						}
						
						$parameter_limit = new ParameterLimit_Access($value);
						if ($parameter_limit->delete() === false)
						{
							if ($transaction_id != null)
							{
								$transaction->rollback($transaction_id);
							}
							return false;
						}
					}
				}
			}
			
			
			if ($transaction_id != null)
			{
				$transaction->commit($transaction_id);
			}
			return true;
		}
		else
		{
			return false;
		}
	}


	public static function get_id_by_internal_name($internal_name)
	{
		return ParameterTemplate_Access::get_id_by_internal_name($internal_name);
	}
	
	/**
	 * @param string $internal_name
	 * @return bool
	 */
	public static function exist_internal_name($internal_name)
	{
		return ParameterTemplate_Access::exist_internal_name($internal_name);
	}
	
	/**
	 * @return array
	 */
	public static function list_templates($internal_name_array = null)
	{
		return ParameterTemplate_Access::list_templates($internal_name_array);
	}
	
	/**
	 * @see EventListenerInterface::listen_events()
     * @param object $event_object
     * @return bool
     */
    public static function listen_events($event_object)
    {
    	if ($event_object instanceof UserDeleteEvent)
    	{
    		/*if (ValueVersion_Access::set_owner_id_on_null($event_object->get_user_id()) == false)
    		{
    			return false;
    		}*/
    	}
    	
    	return true;
    }
    
    /**
     * @todo implement check if the parameter-tempalte is linked to any parameter
     * @param integer $parameter_template_id
     * @return bool
     */
    public static function is_deletable($parameter_template_id)
    {
    	return true;
    }
}
?>