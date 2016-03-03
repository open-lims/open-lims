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
 * 
 */
// require_once("interfaces/parameter_method.interface.php");

if (constant("UNIT_TEST") == false or !defined("UNIT_TEST"))
{
	require_once("access/parameter_method.access.php");
}

/**
 * Parameter Method Class
 * @package data
 */
class ParameterMethod // implements ParameterMethodInterface
{
	private $parameter_method_id;
	private $parameter_method;
	
	function __construct($parameter_method_id = null)
	{
		if (is_numeric($parameter_method_id))
		{
			if (ParameterMethod_Access::exist_id($parameter_method_id) == true)
			{
				$this->parameter_method_id = $parameter_method_id;
				$this->parameter_method = new ParameterMethod_Access($parameter_method_id);
			}
			else
			{
				// throw new ParameterMethodNotFoundException();
			}
		}
		else
		{
			$this->parameter_method_id = null;
			$this->parameter_method = new ParameterMethod_Access(null);
		}
	}
	
	function __destruct()
	{
		unset($this->parameter_method_id);
		unset($this->parameter_method);
	}
	
	public function create($name)
	{
		if ($name)
		{
			return $this->parameter_method->create($name);
		}
		else
		{
			return null;
		}
	}
	
	public function delete()
	{
		global $transaction;
		
		if ($this->parameter_method and $this->parameter_method_id)
		{
			$transaction_id = $transaction->begin();
			
			if (ParameterTemplate::delete_field_methods($this->parameter_method_id) === false)
			{
				if ($transaction_id != null)
				{
					$transaction->rollback($transaction_id);
				}
				return false;
			}

			if($this->parameter_method->delete() == true)
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
		if ($this->parameter_method and $this->parameter_method_id)
		{
			return $this->parameter_method->get_name();
		}
		else
		{
			return null;
		}
	}
	
	public function set_name($name)
	{
		if ($this->parameter_method and $this->parameter_method_id)
		{
			return $this->parameter_method->set_name($name);
		}
		else
		{
			return false;
		}
	}
	
	
	public static function list_methods()
	{
		return ParameterMethod_Access::list_methods();
	}
	
	/**
     * @param integer $parameter_method_id
     * @return bool
     */
    public static function is_deletable($parameter_method_id)
    {
    	return !Parameter::is_method_linked($parameter_method_id);
    }
}
?>