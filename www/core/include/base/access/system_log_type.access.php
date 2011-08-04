<?php
/**
 * @package base
 * @version 0.4.0.0
 * @author Roman Konertz
 * @copyright (c) 2008-2011 by Roman Konertz
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
 * System Log Type Access Class
 * @package base
 */
class SystemLogType_Access
{
	private $log_type_id;
	private $name;
	
	/**
	 * @param integer $log_type_id
	 */
	function __construct($log_type_id)
	{
		global $db;
		
		if ($log_type_id == null)
		{
			$this->log_type_id = null;
		}
		else
		{
			$sql = "SELECT * FROM ".constant("SYSTEM_LOG_TYPE_TABLE")." WHERE id = ".$log_type_id."";
			$res = $db->db_query($sql);
			$data = $db->db_fetch_assoc($res);
			
			if ($data[id])
			{
				$this->log_type_id	= $data[id];
				$this->name			= $data[name];	
			}
			else
			{
				$this->log_type_id = null;
			}
		}
	}
	
	function __destruct()
	{
		if ($this->log_type_id)
		{
			unset($this->log_type_id);
			unset($this->name);
		}
	}
	
	/**
	 * @return string
	 */
	public function get_name()
	{
		if ($this->name)
		{
			return $this->name;
		}
		else
		{
			return null;
		}		
	}
	
	/**
	 * @return array
	 */
	public static function list_entries()
	{
		global $db;
			
		$return_array = array();
		
		$sql = "SELECT id FROM ".constant("SYSTEM_LOG_TYPE_TABLE")." ORDER BY id";
		$res = $db->db_query($sql);
		
		while ($data = $db->db_fetch_assoc($res))
		{
			array_push($return_array,$data[id]);
		}
		
		if (is_array($return_array))
		{
			return $return_array;
		}
		else
		{
			return null;
		}
	}
	
}

?>
