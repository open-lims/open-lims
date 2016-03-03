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
 * Parameter Access Class
 * @package data
 */
class Parameter_Access
{
	const PARAMETER_PK_SEQUENCE = 'core_data_parameters_id_seq';

	private $parameter_id;
	private $data_entity_id;

	/**
	 * @param integer $parameter_id
	 */
	function __construct($parameter_id)
	{
		global $db;
		
		if ($parameter_id == null)
		{
			$this->parameter_id = null;
		}
		else
		{
			$sql = "SELECT * FROM ".constant("PARAMETER_TABLE")." WHERE id='".$parameter_id."'";
			$res = $db->db_query($sql);
			$data = $db->db_fetch_assoc($res);
			
			if ($data['id'])
			{
				$this->parameter_id		= $parameter_id;
				$this->data_entity_id	= $data['data_entity_id'];
			}
			else
			{
				$this->parameter_id		= null;
			}
		}
	}

	function __destruct()
	{
		if ($this->parameter_id)
		{
			unset($this->parameter_id);
			unset($this->data_entity_id);
		}
	}

	/**
	 * @param integer $data_entity_id
	 * @return integer
	 */
	public function create($data_entity_id)
	{
		global $db;
		
		if (is_numeric($data_entity_id))
		{				
			$sql_write = "INSERT INTO ".constant("PARAMETER_TABLE")." (id,data_entity_id) " .
					"VALUES (nextval('".self::PARAMETER_PK_SEQUENCE."'::regclass),'".$data_entity_id."')";
					
			$res_write = $db->db_query($sql_write);	

			if ($db->db_affected_rows($res_write) == 1)
			{
				$sql_read = "SELECT id FROM ".constant("PARAMETER_TABLE")." WHERE id = currval('".self::PARAMETER_PK_SEQUENCE."'::regclass)";
				$res_read = $db->db_query($sql_read);
				$data_read = $db->db_fetch_assoc($res_read);
									
				self::__construct($data_read['id']);
				
				return $data_read['id'];
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

		if ($this->parameter_id)
		{
			$parameter_id_tmp = $this->parameter_id;
			
			$this->__destruct();
			
			$sql = "DELETE FROM ".constant("PARAMETER_TABLE")." WHERE id = ".$parameter_id_tmp."";
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
	 * @return integer
	 */
	public function get_data_entity_id()
	{
		if ($this->data_entity_id)
		{
			return $this->data_entity_id;
		}
		else
		{
			return null;
		}
	}
		
	/**
	 * @param integer $data_entity_id
	 * @return bool
	 */
	public function set_data_entity_id($data_entity_id)
	{	
		global $db;

		if ($this->parameter_id and is_numeric($data_entity_id))
		{
			$sql = "UPDATE ".constant("PARAMETER_TABLE")." SET data_entity_id = '".$data_entity_id."' WHERE id = ".$this->parameter_id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->data_entity_id = $data_entity_id;
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
	 * @param string $data_entity_id
	 * @return integer
	 */
	public static function get_entry_by_data_entity_id($data_entity_id)
	{
		global $db;

		if (is_numeric($data_entity_id))
		{
			$sql = "SELECT id FROM ".constant("PARAMETER_TABLE")." WHERE data_entity_id = '".$data_entity_id."'";

			$res = $db->db_query($sql);
			$data = $db->db_fetch_assoc($res);
			
			if ($data['id'])
			{
				return $data['id'];
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
	 * @param integer $parameter_id
	 * @return bool
	 */
	public static function exist_parameter_by_parameter_id($parameter_id)
	{
		global $db;
			
		if (is_numeric($parameter_id))
		{
			$sql = "SELECT id FROM ".constant("PARAMETER_TABLE")." WHERE id = ".$parameter_id."";
			$res = $db->db_query($sql);
			$data = $db->db_fetch_assoc($res);
			
			if ($data['id'])
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
}
?>