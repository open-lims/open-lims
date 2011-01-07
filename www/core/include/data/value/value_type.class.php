<?php
/**
 * @package data
 * @version 0.4.0.0
 * @author Roman Konertz
 * @copyright (c) 2008-2010 by Roman Konertz
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
require_once("interfaces/value_type.interface.php");

if (constant("UNIT_TEST") == false or !defined("UNIT_TEST"))
{
	require_once("access/value_type.access.php");
}

/**
 * Value Type Management Class
 * @package data
 */
class ValueType implements ValueTypeInterface {

	private $value_type;
	private $value_type_id;

	/**
	 * @param integer $value_type_id
	 */
	function __construct($value_type_id)
	{
		if ($value_type_id == null)
		{
			$this->value_type_id = null;
			$this->value_type = new ValueType_Access(null);
		}
		else
		{
			$this->value_type_id = $value_type_id;
			$this->value_type = new ValueType_Access($value_type_id);
		}
	}   
	
	function __destruct()
	{
		unset($this->value_type);
		unset($this->value_type_id);
	} 
	
	/**
	 * Creates a new value-type
	 * @param integer $object_id
	 * @return integer
	 */
	public function create($object_id)
	{
		global $transaction;
		
		if ($this->value_type and is_numeric($object_id))
		{
			$xml_cache = new XmlCache($object_id);
    		$xml_array = $xml_cache->get_xml_array();
			
			$olvdl_found = false;
			$title_found = false;
			$id_found = false;
			$id = null;
			$title = "";
			
			if (is_array($xml_array) and count($xml_array) >= 1)
			{
				foreach($xml_array as $key => $value)
				{
					$value[1] = trim(strtolower($value[1]));
					$value[2] = trim($value[2]);
					
					if ($value[1] == "olvdl" and $value[2] != "#")
					{
						$olvdl_found = true;
					}
					
					if ($value[1] == "title" and $value[2] != "#")
					{
						if ($value[2])
						{
							$title = trim($value[2]);
							$title_found = true;
						}
					}
					
					if ($value[1] == "id" and $value[2] != "#")
					{
						if ($value[2])
						{
							if (is_numeric(trim($value[2])))
							{
								$id = (int)trim($value[2]);
								$id_found = true;
							}
						}
					}
				}
				
				if ($olvdl_found == false or $title_found == false or $id_found == false)
				{
					return false;
				}
				
				$transaction_id = $transaction->begin();
				
				$olvdl = new Olvdl(null);
				if (($olvdl_id = $olvdl->create($object_id)) == null)
				{
					if ($transaction_id != null)
					{
						$transaction->rollback($transaction_id);
					}
					return false;
				}
		
				if ($this->value_type->create($id, $title, $olvdl_id) == false)
				{
					if ($transaction_id != null)
					{
						$transaction->rollback($transaction_id);
					}
					return false;
				}
				else
				{
					if ($transaction_id != null)
					{
						$transaction->commit($transaction_id);
					}
					return true;
				}	
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * Deletes a value-type
	 * @return bool
	 */
	public function delete()
	{
		global $transaction;
		
		if ($this->value_type and $this->value_type_id)
		{
			$value_array = Value::list_entries_by_type_id($this->value_type_id);
			if (is_array($value_array))
			{
				if (count($value_array) != 0)
				{
					return false;
				}
			}
			
			$transaction_id = $transaction->begin();
				
			$olvdl = new Olvdl($this->value_type->get_template_id());
			
			if ($this->value_type->delete() == false)
			{
				if ($transaction_id != null)
				{
					$transaction->rollback($transaction_id);
				}
				return false;
			}
			
			if ($olvdl->delete() == false)
			{
				if ($transaction_id != null)
				{
					$transaction->rollback($transaction_id);
				}
				return false;
			}
			else
			{
				if ($transaction_id != null)
				{
					$transaction->commit($transaction_id);
				}
				return true;
			}
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * @return string
	 */
	public function get_name()
	{
		if ($this->value_type) {
			return $this->value_type->get_name();
		}else{
			return null;
		}
	}
	
	/**
	 * @return integer
	 */
	public function get_template_id()
	{
		if ($this->value_type) {
			return $this->value_type->get_template_id();
		}else{
			return null;
		}
	}

	/**
	 * @param integer $id
	 * @return bool
	 */
	public static function exist_id($id)
	{
		return ValueType_Access::exist_id($id);
	}
	
	/**
	 * @return array
	 */
	public static function list_entries()
	{
		return ValueType_Access::list_entries();
	}

}
?>