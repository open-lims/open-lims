<?php
/**
 * @package sample
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
 * Sample Is Item Access Class
 * @package sample
 */
class SampleIsItem_Access
{
	private $sample_id;
	private $item_id;
	
	/**
	 * @param integer $sample_id
	 */
	function __construct($sample_id)
	{
		global $db;
		
		if ($sample_id == null)
		{
			$this->sample_id = null;
		}
		else
		{
			$sql = "SELECT * FROM ".constant("SAMPLE_IS_ITEM_TABLE")." WHERE sample_id= :sample_id";
			$res = $db->prepare($sql);
			$db->bind_value($res, ":sample_id", $sample_id, PDO::PARAM_INT);
			$db->execute($res);	
			$data = $db->fetch($res);
			
			if ($data['sample_id'])
			{
				$this->sample_id	= $sample_id;
				$this->item_id		= $data['item_id'];
			}
			else
			{
				$this->sample_id	= null;
				$this->item_id		= null;
			}
		}	
	}
	
	function __destruct()
	{
		unset($this->sample_id);
		unset($this->item_id);
	}
	
	/**
	 * @param integer $sample_id
	 * @param integer $item_id
	 * @return boolean
	 */
	public function create($sample_id, $item_id)
	{
		global $db;
		
		if (is_numeric($sample_id) and is_numeric($item_id))
		{	
			$sql_write = "INSERT INTO ".constant("SAMPLE_IS_ITEM_TABLE")." (sample_id,item_id) " .
					"VALUES (:sample_id, :item_id)";
					
			$res_write = $db->prepare($sql_write);
			$db->bind_value($res_write, ":sample_id", $sample_id, PDO::PARAM_INT);
			$db->bind_value($res_write, ":item_id", $item_id, PDO::PARAM_INT);
			$db->execute($res_write);
			
			if ($db->row_count($res_write) == 1)
			{	
				return true;
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
	 * @return bool
	 */
	public function delete()
	{
		global $db;
			
		if ($this->sample_id and $this->item_id)
		{
			$sql = "DELETE FROM ".constant("SAMPLE_IS_ITEM_TABLE")." WHERE sample_id = :sample_id AND item_id = :item_id";
			$res = $db->prepare($sql);
			$db->bind_value($res, ":sample_id", $this->sample_id, PDO::PARAM_INT);
			$db->bind_value($res, ":item_id", $this->item_id, PDO::PARAM_INT);
			$db->execute($res);
			
			if ($db->row_count($res) == 1)
			{
				$this->__destruct();
				return true;
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
	 * @return integer
	 */
	public function get_sample_id()
	{
		if ($this->sample_id)
		{
			return $this->sample_id;
		}
		else
		{
			return null;
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
	 * @param integer $item_id
	 * @return integer
	 */
	public static function get_entry_by_item_id($item_id)
	{
		global $db;
		
		if (is_numeric($item_id))
		{	
			$sql = "SELECT sample_id FROM ".constant("SAMPLE_IS_ITEM_TABLE")." WHERE item_id= :item_id";
			$res = $db->prepare($sql);
			$db->bind_value($res, ":item_id", $item_id, PDO::PARAM_INT);
			$db->execute($res);
			$data = $db->fetch($res);
			
			if ($data['sample_id'])
			{
				return $data['sample_id'];
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
	 * @param string $sql
	 * @return array
	 */
	public static function list_samples_by_item_sql_list($sql, $prepared_statement_array = null)
	{
		global $db;

   		if ($sql)
   		{
   			$return_array = array();
   			
   			/**
   			 * @todo $sql and prepared statements?
   			 */
   			
   			$sql = "SELECT sample_id " .
						"FROM ".constant("SAMPLE_IS_ITEM_TABLE")." " .
						"WHERE item_id IN (".$sql.")";
			
   			$res = $db->prepare($sql);
			$db->execute($res);

			while ($data = $db->fetch($res))
			{
				array_push($return_array, $data['sample_id']);
			}

			return $return_array;
		}
		else
		{
			return null;
		}
	}
}
	
?>
