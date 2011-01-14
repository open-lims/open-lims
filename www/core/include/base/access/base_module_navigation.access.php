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
 * Base Module Navigation Access Class
 * @package base
 */
class BaseModuleNavigation_Access
{
	private $id;
	private $display_name;
	private $position;
	private $colour;
	
	/**
	 * @param string $id
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
			$sql = "SELECT * FROM ".constant("BASE_MODULE_NAVIGATION_TABLE")." WHERE id='".$id."'";
			$res = $db->db_query($sql);
			$data = $db->db_fetch_assoc($res);
			
			if ($data[id])
			{
				$this->id 			= $id;
				$this->display_name	= $data[display_name];
				$this->position		= $data[position];
				$this->colour		= $data[colour];
			}
			else
			{
				$this->id			= null;
			}				
		}
	}
	
	function __destruct()
	{
		if ($this->id)
		{
			unset($this->id);
			unset($this->display_name);
			unset($this->position);
			unset($this->colour);
		}
	}
	
	/**
	 * @param string $name
	 * @param string $folder
	 * @return bool
	 */
	public function create($id, $display_name, $position, $colour)
	{
		global $db;

		if (is_numeric($id) and $display_name and is_numeric($position) and $colour)
		{
	 		$sql_write = "INSERT INTO ".constant("BASE_MODULE_NAVIGATION_TABLE")." (id, display_name, position, colour) " .
								"VALUES ('".$id."','".$display_name."','".$position."','".$colour."')";		
				
			$res_write = $db->db_query($sql_write);
			
			if ($db->db_affected_rows($res_write) == 1)
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

		if ($this->id)
		{
			$id_tmp = $this->id;
			
			$this->__destruct();

			$sql = "DELETE FROM ".constant("BASE_MODULE_NAVIGATION_TABLE")." WHERE id = '".$id_tmp."'";
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
	public function get_display_name()
	{
		if ($this->display_name)
		{
			return $this->display_name;
		}
		else
		{
			return null;
		}	
	}
	
	/**
	 * @return integer
	 */
	public function get_position()
	{
		if ($this->position)
		{
			return $this->position;
		}
		else
		{
			return null;
		}	
	}
	
	/**
	 * @return string
	 */
	public function get_colour()
	{
		if ($this->colour)
		{
			return $this->colour;
		}
		else
		{
			return null;
		}	
	}
	
	/**
	 * @param string $display_name
	 * @return bool
	 */
	public function set_display_name($display_name)
	{
		global $db;

		if ($this->id and $display_name)
		{
			$sql = "UPDATE ".constant("BASE_MODULE_NAVIGATION_TABLE")." SET display_name = '".$display_name."' WHERE id = ".$this->id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->display_name = $display_name;
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
	 * @param integer $position
	 * @return bool
	 */
	public function set_position($position)
	{
		global $db;

		if ($this->id and is_numeric($position))
		{
			$sql = "UPDATE ".constant("BASE_MODULE_NAVIGATION_TABLE")." SET position = '".$position."' WHERE id = ".$this->id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->position = $position;
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
	 * @param string $colour
	 * @return bool
	 */
	public function set_colour($colour)
	{
		global $db;

		if ($this->id and colour)
		{
			$sql = "UPDATE ".constant("BASE_MODULE_NAVIGATION_TABLE")." SET colour = '".$colour."' WHERE id = ".$this->id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->colour = $colour;
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