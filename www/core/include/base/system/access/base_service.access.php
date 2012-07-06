<?php
/**
 * @package base
 * @version 0.4.0.0
 * @author Roman Konertz <konertz@open-lims.org>
 * @copyright (c) 2008-2012 by Roman Konertz
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
 * Base Service Access Class
 * @package base
 */
class BaseService_Access
{
	const BASE_SERVICE_PK_SEQUENCE = 'core_services_id_seq';
	
	private $id;
	private $name;
	private $binary_id;
	private $status;
	private $last_lifesign;
	
	/**
	 * @param integer $id
	 */
	function __construct($id)
	{
		global $db;
		
		if ($id == null)
		{
			$this->id = null;
		}
		else
		{
			$sql = "SELECT * FROM ".constant("BASE_SERVICE_TABLE")." WHERE id='".$id."'";
			$res = $db->db_query($sql);
			$data = $db->db_fetch_assoc($res);
			
			if ($data[id])
			{
				$this->id 				= $id;
				$this->name				= $data['name'];
				$this->binary_id		= $data['binary_id'];
				$this->status			= $data['status'];
				$this->last_lifesign	= $data['last_lifesign'];
			}
			else
			{
				$this->id				= null;
			}				
		}
	}
	
	function __destruct()
	{
		if ($this->id)
		{
			unset($this->id);
			unset($this->name);
			unset($this->binary_id);
			unset($this->status);
			unset($this->last_lifesign);
		}
	}
	
	/**
	 * @param string $name
	 * @param integer $binary_id
	 * @return integer
	 */
	public function create($name, $binary_id)
	{
		global $db;

		if ($name and is_numeric($binary_id))
		{
	 		$sql_write = "INSERT INTO ".constant("BASE_SERVICE_TABLE")." (id, name, binary_id, status, last_lifesign) " .
								"VALUES (nextval('".self::BASE_SERVICE_PK_SEQUENCE."'::regclass),'".$name."','".$binary_id."',0,NULL)";		
				
			$res_write = $db->db_query($sql_write);
			
			if ($db->db_affected_rows($res_write) == 1)
			{
				$sql_read = "SELECT id FROM ".constant("BASE_SERVICE_TABLE")." WHERE id = currval('".self::BASE_SERVICE_PK_SEQUENCE."'::regclass)";
				$res_read = $db->db_query($sql_read);
				$data_read = $db->db_fetch_assoc($res_read);
							
				$this->__construct($data_read[id]);		
								
				return $data_read[id];
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
	 * @return bool
	 */
	public function delete()
	{
		global $db;

		if ($this->id)
		{
			$id_tmp = $this->id;
			
			$this->__destruct();

			$sql = "DELETE FROM ".constant("BASE_SERVICE_TABLE")." WHERE id = '".$id_tmp."'";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res) == 1)
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
	 * @return integer
	 */
	public function get_binary_id()
	{
		if ($this->include_id)
		{
			return $this->binary_id;
		}
		else
		{
			return null;
		}	
	}
	
	/**
	 * @return integer
	 */
	public function get_status()
	{
		if ($this->status)
		{
			return $this->status;
		}
		else
		{
			return null;
		}	
	}
	
	/**
	 * @return string
	 */
	public function get_last_lifesign()
	{
		if ($this->last_lifesign)
		{
			return $this->last_lifesign;
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
		global $db;

		if ($this->id and $name)
		{
			$sql = "UPDATE ".constant("BASE_SERVICE_TABLE")." SET name = '".$name."' WHERE id = ".$this->id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->name = $name;
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
	 * @param integer $binary_id
	 * @return bool
	 */
	public function set_binary_id($binary_id)
	{
		global $db;

		if ($this->id and is_numeric($binary_id))
		{
			$sql = "UPDATE ".constant("BASE_SERVICE_TABLE")." SET binary_id = '".$binary_id."' WHERE id = ".$this->id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->binary_id = $binary_id;
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
	 * @param integer $status
	 * @return bool
	 */
	public function set_status($status)
	{
		global $db;

		if ($this->id and is_numeric($status))
		{
			$sql = "UPDATE ".constant("BASE_SERVICE_TABLE")." SET status = '".$status."' WHERE id = ".$this->id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->status = $status;
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
	 * @param string $last_lifesign
	 * @return bool
	 */
	public function set_last_lifesign($last_lifesign)
	{
		global $db;

		if ($this->id and $last_lifesign)
		{
			$sql = "UPDATE ".constant("BASE_SERVICE_TABLE")." SET last_lifesign = '".$last_lifesign."' WHERE id = ".$this->id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->last_lifesign = $last_lifesign;
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
}
?>