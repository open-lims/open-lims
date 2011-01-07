<?php
/**
 * @package method
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
require_once("interfaces/method.interface.php");

if (constant("UNIT_TEST") == false or !defined("UNIT_TEST"))
{
	require_once("access/method.access.php");
}

/**
 * Method Category Management Class
 * @package method
 */
class Method implements MethodInterface
{
	private $method_id;
	
	private $method;
	private $item_id;
	
	/**
	 * @param integer $method_id
	 */
	function __construct($method_id)
	{
		if ($method_id)
		{
			$this->method_id = $method_id;
			$this->method = new Method_Access($method_id);
			
			$this->item_id = Item::get_id_by_method_id($method_id);
		}
		else
		{
			$this->method_id = null;
			$this->method = new Method_Access(null);
		}
	}
	
	function __destruct()
	{
		unset($this->method_id);
		unset($this->method);
		unset($this->item_id);
	}
	
	/**
	 * Creates a new method
	 * @param integer $type_id
	 * @param integer $owner_id
	 * @return integer
	 */
	public function create($type_id, $owner_id)
	{
		global $transaction;
		
		if (is_numeric($type_id) and is_numeric($owner_id))
		{		
			$transaction_id = $transaction->begin();
			
			if (($method_id = $this->method->create($type_id, $owner_id)) != null)
			{
				
				$item = new Item(null);
				if ($item->create() == null)
				{
					if ($transaction_id != null)
					{
						$transaction->rollback($transaction_id);
					}
					return null;
				}
				
				if($item->link_method($method_id) == null)
				{
					if ($transaction_id != null)
					{
						$transaction->rollback($transaction_id);
					}
					return null;
				}
				else
				{
					if ($transaction_id != null)
					{
						$transaction->commit($transaction_id);
					}
					$this->__construct($method_id);
					return $method_id;
				}
			}
			else
			{
				if ($transaction_id != null)
				{
					$transaction->rollback($transaction_id);
				}
				return null;
			}
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * Deletes a method
	 * @return bool
	 */
	public function delete()
	{
   		global $transaction;
   		
   		if ($this->method_id and $this->method)
		{
			$transaction_id = $transaction->begin();
			
			$tmp_method_id = $this->method_id;
			
			// Delete Item
			if ($this->item_id) {
				$item = new Item($this->item_id);
				if ($item->delete() == false)
				{
					if ($transaction_id != null)
					{
						$transaction->rollback($transaction_id);
					}
					return false;
				}
			}else{
				if ($transaction_id != null)
				{
					$transaction->rollback($transaction_id);
				}
				return false;
			}
			
			if ($this->method->delete() == false)
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
	 * @return integer
	 */
	public function get_item_id()
	{
		if ($this->item_id)
		{
			return $this->item_id;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return integer
	 */
	public function get_type_id()
	{
		if ($this->method_id and $this->method)
		{
			return $this->method->get_type_id();
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return integer
	 */
	public function get_owner_id()
	{
		if ($this->method_id and $this->method)
		{
			return $this->method->get_owner_id();
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return string
	 */
	public function get_datetime()
	{
		if ($this->method_id and $this->method)
		{
			return $this->method->get_datetime();
		}
		else
		{
			return null;
		}
	}
	
	
	/**
	 * @return array
	 */
	public static function list_entries_by_user_id($user_id)
	{
		return Method_Access::list_entries_by_owner_id($user_id);
	} 
	
	/**
	 * @return array
	 */
	public static function list_entries_by_type_id($type_id)
	{
		return Method_Access::list_entries_by_type_id($type_id);
	}

}
?>