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
 * Parameter Has Template Access Class
 * @package data
 */
class ParameterHasTemplate_Access
{
	private $parameter_id;
	private $template_id;	

	/**
	 * @param integer $parameter_id
	 * @param integer $template_id
	 */
	function __construct($parameter_id, $template_id)
	{
		global $db;
		
		if ($parameter_id == null or $template_id == null)
		{
			$this->parameter_id = null;
			$this->template_id = null;
		}
		else
		{
			$sql = "SELECT * FROM ".constant("PARAMETER_HAS_TEMPLATE_TABLE")." WHERE parameter_id='".$parameter_id."' AND template_id='".$template_id."'";
			$res = $db->db_query($sql);
			$data = $db->fetch($res);
			
			if ($data['parameter_id'] and $data['template_id'])
			{
				$this->parameter_id	= $parameter_id;
				$this->template_id 	= $template_id;
			}
			else
			{
				$this->parameter_id	= null;
				$this->template_id 	= null;
			}
		}
	}

	function __destruct()
	{
		if ($this->parameter_id and $this->template_id)
		{
			unset($this->parameter_id);
			unset($this->template_id);
		}
	}

	/**
	 * @param integer $parameter_id
	 * @param integer $template_id
	 * @return boolean
	 */
	public function create($parameter_id, $template_id)
	{
		global $db;
		
		if (is_numeric($parameter_id) and is_numeric($template_id))
		{	
			$sql_write = "INSERT INTO ".constant("PARAMETER_HAS_TEMPLATE_TABLE")." (parameter_id,template_id) " .
					"VALUES ('".$parameter_id."','".$template_id."')";
					
			$res_write = $db->db_query($sql_write);	

			if ($db->row_count($res_write) == 1)
			{
				self::__construct($parameter_id, $template_id);
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

		if ($this->template_id and $this->parameter_id)
		{
			$parameter_id_tmp = $this->parameter_id;
			$template_id_tmp = $this->template_id;
			
			
			$this->__destruct();
			
			$sql = "DELETE FROM ".constant("PARAMETER_HAS_TEMPLATE_TABLE")." WHERE parameter_id=".$parameter_id_tmp." AND template_id = ".$template_id_tmp."";
			$res = $db->db_query($sql);
			
			if ($db->row_count($res) == 1)
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
	 * @param integer $parameter_id
	 * @return integer
	 */
	public static function get_template_id_by_parameter_id($parameter_id)
	{
		global $db;
		
		if (is_numeric($parameter_id))
		{
			$sql = "SELECT template_id FROM ".constant("PARAMETER_HAS_TEMPLATE_TABLE")." WHERE parameter_id='".$parameter_id."'";
			$res = $db->db_query($sql);
			$data = $db->fetch($res);
			
			if ($data['template_id'])
			{
				return $data['template_id'];
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
	 * @param integer $template_id
	 * @return integer
	 */
	public static function list_parameter_ids_by_template_id($template_id)
	{
		global $db;
		
		if (is_numeric($template_id))
		{
			$return_array = array();
			
			$sql = "SELECT parameter_id FROM ".constant("PARAMETER_HAS_TEMPLATE_TABLE")." WHERE template_id='".$template_id."'";
			$res = $db->db_query($sql);
			
			while ($data = $db->fetch($res))
			{
				array_push($return_array, $data['parameter_id']);
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
		else
		{
			return null;
		}
	}
}
?>