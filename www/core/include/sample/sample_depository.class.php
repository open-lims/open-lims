<?php
/**
 * @package sample
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
require_once("interfaces/sample_depository.interface.php");

if (constant("UNIT_TEST") == false or !defined("UNIT_TEST"))
{
	require_once("exceptions/sample_depository_not_found_exception.class.php");
	
	require_once("access/sample_has_sample_depository.access.php");
	require_once("access/sample_depository.access.php");
}

/**
 * Sample Depository Management Class
 * @package sample
 */
class SampleDepository implements SampleDepositoryInterface
{
	private $sample_depository_id;
	private $sample_depository;

	/**
	 * @param integer $depository_id
	 */
    function __construct($depository_id)
    {
    	if ($depository_id == null)
    	{
			$this->sample_depository_id = null;
			$this->sample_depository = new SampleDepository_Access(null);
		}
		else
		{
			$this->sample_depository_id = $depository_id;
			$this->sample_depository = new SampleDepository_Access($depository_id);
		}
    }
    
    function __destruct()
    {
    	unset($this->sample_depository_id);
    	unset($this->sample_depository);
    }
 
 	/**
 	 * @param integer $parent_id
 	 * @param string $name
 	 * @return integer
 	 */
	public function create($parent_id, $name)
	{
		if ($name and $this->sample_depository)
		{
			return $this->sample_depository->create($parent_id, $name);
		}
		else
		{
			return null;
		}
	}
	
	/**
 	 * @return bool
 	 */
	public function delete()
	{
		global $transaction;
		
		if ($this->sample_depository_id and $this->sample_depository)
		{
			$transaction_id = $transaction->begin();
		
			$tmp_sample_depository_id = $this->sample_depository_id;
		
			// Sample Relations
			$sample_has_sample_depository_array = SampleHasSampleDepository_Access::list_entries_by_sample_depository_id($tmp_sample_depository_id);
			if (is_array($sample_has_sample_depository_array) and count($sample_has_sample_depository_array) >= 1)
			{
				foreach($sample_has_sample_depository_array as $key => $value)
				{
					$sample_has_sample_depository = new SampleHasSampleDepository_Access($value);
					if ($sample_has_sample_depository->set_sample_depository_id_on_null() == false)
					{
						if ($transaction_id != null)
						{
							$transaction->rollback($transaction_id);
						}
						return false;
					}
				}
			}
		
			if ($this->get_childs() != null)
			{
				if ($transaction_id != null)
				{
					$transaction->rollback($transaction_id);
				}
				return false;
			}
			else
			{
				if ($this->sample_depository->delete() == true)
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
		if ($this->sample_depository_id and $this->sample_depository)
		{
			return $this->sample_depository->get_name();
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return array
	 */
	public function get_childs()
	{
		if ($this->sample_depository_id and $this->sample_depository)
		{
			return SampleDepository_Access::list_entries_by_toid($this->sample_depository_id);
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @param string $name
	 * @return bool
	 */
	public function set_name($name)
	{
		if ($this->sample_depository_id and $this->sample_depository and $name)
		{
			return $this->sample_depository->set_name($name);
		}
		else
		{
			return false;
		}
	}
	
	
	/**
	 * @param integer $id
	 * @return bool
	 */
	public static function exist_id($id)
	{
		return SampleDepository_Access::exist_id($id);
	}
	
	/**
	 * @param string $name
	 * @return bool
	 */
	public static function exist_name($name)
	{
		return SampleDepository_Access::exist_name($name);
	}
	
	/**
	 * @return array
	 */
	public static function list_entries()
	{
		return SampleDepository_Access::list_entries();
	}
	
	/**
	 * @return array
	 */
	public static function list_root_entries()
	{
		return SampleDepository_Access::list_root_entries();
	}
    
}
?>