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
 * Parameter Template Access Class
 * @package data
 */
class ParameterTemplate_Access
{
	const PARAMETER_TEMPLATE_PK_SEQUENCE = 'core_data_parameter_templates_id_seq';

	private $template_id;
	
	private $internal_name;
	private $name;
	private $created_by;
	private $datetime;

	/**
	 * @param integer $template_id
	 */
	function __construct($template_id)
	{
		global $db;
		
		if ($template_id == null)
		{
			$this->template_id = null;
		}
		else
		{
			$sql = "SELECT * FROM ".constant("PARAMETER_TEMPLATE_TABLE")." WHERE id='".$template_id."'";
			$res = $db->db_query($sql);
			$data = $db->db_fetch_assoc($res);
			
			if ($data['id'])
			{
				$this->template_id		= $template_id;
				$this->internal_name	= $data['internal_name'];
				$this->name				= $data['name'];
				$this->created_by		= $data['created_by'];
				$this->datetime			= $data['datetime'];
			}
			else
			{
				$this->template_id		= null;
			}
		}
	}

	function __destruct()
	{
		if ($this->template_id)
		{
			unset($this->template_id);
			unset($this->internal_name);
			unset($this->name);
			unset($this->created_by);
			unset($this->datetime);
		}
	}

	/**
	 * @param string $internal_name
	 * @param stirng $name
	 * @param integer $created_by
	 * @return integer
	 */
	public function create($internal_name, $name, $created_by)
	{
		global $db;
		
		if ($internal_name and $name)
		{		
			if (is_numeric($created_by))
			{
				$created_by_insert = $created_by;
			}
			else
			{
				$created_by_insert = "NULL";
			}
			
			$datetime = date("Y-m-d H:i:s");
			
			$sql_write = "INSERT INTO ".constant("PARAMETER_TEMPLATE_TABLE")." (id,internal_name,name,created_by,datetime) " .
					"VALUES (nextval('".self::PARAMETER_TEMPLATE_PK_SEQUENCE."'::regclass),'".$internal_name."','".$name."',".$created_by_insert.",'".$datetime."')";
					
			$res_write = $db->db_query($sql_write);	

			if ($db->db_affected_rows($res_write) == 1)
			{
				$sql_read = "SELECT id FROM ".constant("PARAMETER_TEMPLATE_TABLE")." WHERE id = currval('".self::PARAMETER_TEMPLATE_PK_SEQUENCE."'::regclass)";
				$res_read = $db->db_query($sql_read);
				$data_read = $db->db_fetch_assoc($res_read);
									
				$this->__construct($data_read['id']);
				
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

		if ($this->template_id)
		{
			$template_id_tmp = $this->template_id;
			
			$this->__destruct();
			
			$sql = "DELETE FROM ".constant("PARAMETER_TEMPLATE_TABLE")." WHERE id = ".$template_id_tmp."";
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
	public function get_internal_name()
	{
		if ($this->internal_name)
		{
			return $this->internal_name;
		}
		else
		{
			return null;
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
	public function get_created_by()
	{
		if ($this->created_by)
		{
			return $this->created_by;
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
		if ($this->datetime)
		{
			return $this->datetime;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @param string $internal_name
	 * @return bool
	 */
	public function set_internal_name($internal_name)
	{	
		global $db;

		if ($this->template_id and $internal_name)
		{
			$sql = "UPDATE ".constant("PARAMETER_TEMPLATE_TABLE")." SET internal_name = '".$internal_name."' WHERE id = ".$this->template_id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->internal_name = $internal_name;
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
	 * @param string $name
	 * @return bool
	 */
	public function set_name($name)
	{	
		global $db;

		if ($this->template_id and $name)
		{
			$sql = "UPDATE ".constant("PARAMETER_TEMPLATE_TABLE")." SET name = '".$name."' WHERE id = ".$this->template_id."";
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
	 * @param integer $created_by
	 * @return bool
	 */
	public function set_created_by($created_by)
	{	
		global $db;

		if ($this->template_id and $created_by)
		{
			$sql = "UPDATE ".constant("PARAMETER_TEMPLATE_TABLE")." SET created_by = '".$created_by."' WHERE id = ".$this->template_id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->created_by = $created_by;
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
	 * @param string $datetime
	 * @return bool
	 */
	public function set_datetime($datetime)
	{	
		global $db;

		if ($this->template_id and $datetime)
		{
			$sql = "UPDATE ".constant("PARAMETER_TEMPLATE_TABLE")." SET datetime = '".$datetime."' WHERE id = ".$this->template_id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->datetime = $datetime;
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