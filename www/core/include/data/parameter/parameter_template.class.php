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
}

/**
 * Parameter Template Class
 * @package data
 */
class ParameterTemplate implements ParameterTemplateInterface, EventListenerInterface
{
	function __construct($parameter_template_id = null)
	{
		
	}
	
	function __destruct()
	{
		
	}
	
	public function create($internal_name, $name, $field_array)
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
		
			foreach($field_array as $key => $value)
			{
				foreach($value as $field_key => $field_value)
				{
					switch($field_key):
					
						case "name":
							$name = $field_value;	
						break;
						
						case "unit":
							$unit_array = explode("-", $field_value);
							if (is_array($unit_array) and count($unit_array) === 2)
							{
								$measuring_unit_id = $unit_array[0];
								$measuring_unit_exponent = $unit_array[1];
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
				if (($parameter_field_id = $parameter_field->create($name, $min_value, $max_value, $measuring_unit_id, $measuring_unit_exponent)) === null)
				{
					echo "2";
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
				
				unset($name);
				unset($min_value);
				unset($max_value);
				unset($measuring_unit_id);
				unset($measuring_unit_exponent);
			}
			
			print_r($field_array);
			
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
		
	}
	
	public function update($field_array)
	{
		
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
}
?>