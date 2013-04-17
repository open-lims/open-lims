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
require_once("interfaces/parameter_template_parameter.interface.php");

if (constant("UNIT_TEST") == false or !defined("UNIT_TEST"))
{
	require_once("access/parameter_has_template.access.php");
	require_once("access/parameter_field_value.access.php");
}

/**
 * Parameter Template Management Class
 * @package data
 */
class ParameterTemplateParameter extends Parameter implements ParameterTemplateParameterInterface
{
	
	
	function __construct($parameter_id)
	{
		parent::__construct($parameter_id);
	}
	
	function __destruct()
	{
		
	}
	
	public function create($folder_id, $owner_id, $template_id, $parameter_array)
	{
		global $transaction;
		
		if (is_numeric($folder_id) and is_numeric($owner_id) and is_numeric($template_id) and $parameter_array)
		{			
			if (is_array($parameter_array))
			{
				$transaction_id = $transaction->begin();
				
				if (($parameter_id = parent::create($folder_id, $owner_id)) == null)
				{
					if ($transaction_id != null)
					{
						$transaction->rollback($transaction_id);
					}
					return null;
				}
				
				$parameter_version_id = $this->parameter_version_id;
				
				$parameter_has_template = new ParameterHasTemplate_Access(null, null);
				if ($parameter_has_template->create($parameter_id, $template_id) == null)
				{
					if ($transaction_id != null)
					{
						$transaction->rollback($transaction_id);
					}
					return null;
				}
								
				foreach($parameter_array as $key => $value)
				{
					$parameter_field_value = new ParameterFieldValue_Access(null);
					if ($parameter_field_value->create($parameter_version_id, $key, $value['method'], $value['value']) == null)
					{
						if ($transaction_id != null)
						{
							$transaction->rollback($transaction_id);
						}
						return null;
					}
				}
												
				if ($transaction_id != null)
				{
					$transaction->commit($transaction_id);
				}
				return $parameter_id;
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
	
	public function update($value_array, $previous_version_id, $major, $current)
	{
		
	}
	
	
	public static function get_instance($parameter_id, $force_new_instance = false)
    {    
    	if (is_numeric($parameter_id) and $parameter_id > 0)
    	{
    		if ($force_new_instance == true)
    		{
    			return new ParameterTemplateParameter($parameter_id);
    		}
    		else
    		{
	    		if (self::$parameter_object_array[$parameter_id])
				{
					return self::$parameter_object_array[$parameter_id];
				}
				else
				{
					$parameter = new ParameterTemplateParameter($parameter_id);
					self::$parameter_object_array[$parameter_id] = $parameter;
					return $parameter;
				}
    		}
    	}
    	else
    	{
    		return new ParameterTemplateParameter(null);
    	}
    }
}
?>