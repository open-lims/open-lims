<?php
/**
 * @package data
 * @version 0.4.0.0
 * @author Roman Konertz <konertz@open-lims.org>
 * @copyright (c) 2008-2014 by Roman Konertz
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
 * Parameter Template Has Field Access Class
 * @package data
 */
class ParameterTemplateHasField_Access
{
	private $template_id;
	private $parameter_field_id;

	/**
	 * @param integer $template_id
	 * @param integer $parameter_field_id
	 */
	function __construct($template_id, $parameter_field_id)
	{
		global $db;
		
		if ($template_id == null or $parameter_field_id == null)
		{
			$this->template_id = null;
			$this->parameter_field_id = null;
		}
		else
		{
			$sql = "SELECT * FROM ".constant("PARAMETER_TEMPLATE_HAS_FIELD_TABLE")." WHERE template_id='".$template_id."' AND parameter_field_id='".$parameter_field_id."'";
			$res = $db->db_query($sql);
			$data = $db->fetch($res);
			
			if ($data['template_id'] and $data['parameter_field_id'])
			{
				$this->template_id	= $template_id;
				$this->parameter_field_id 	= $parameter_field_id;
			}
			else
			{
				$this->template_id	= null;
				$this->parameter_field_id 	= null;
			}
		}
	}

	function __destruct()
	{
		if ($this->template_id and $this->parameter_field_id)
		{
			unset($this->template_id);
			unset($this->parameter_field_id);
		}
	}

	/**
	 * @param integer $template_id
	 * @param integer $parameter_field_id
	 * @return boolean
	 */
	public function create($template_id, $parameter_field_id)
	{
		global $db;
		
		if (is_numeric($template_id) and is_numeric($parameter_field_id))
		{	
			$sql_write = "INSERT INTO ".constant("PARAMETER_TEMPLATE_HAS_FIELD_TABLE")." (template_id,parameter_field_id) " .
					"VALUES ('".$template_id."','".$parameter_field_id."')";
					
			$res_write = $db->db_query($sql_write);	

			if ($db->row_count($res_write) == 1)
			{
				self::__construct($template_id, $parameter_field_id);
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

		if ($this->template_id and $this->parameter_field_id)
		{
			$template_id_tmp = $this->template_id;
			$parameter_field_id_tmp = $this->parameter_field_id;
			
			
			$this->__destruct();
			
			$sql = "DELETE FROM ".constant("PARAMETER_TEMPLATE_HAS_FIELD_TABLE")." WHERE template_id=".$template_id_tmp." AND parameter_field_id = ".$parameter_field_id_tmp."";
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
	 * @param integer $template_id
	 * @return array
	 */
	public static function list_fields_by_template_id($template_id)
	{
		global $db;
			
		if (is_numeric($template_id))
		{
			$return_array = array();
			
			$sql = "SELECT parameter_field_id FROM ".constant("PARAMETER_TEMPLATE_HAS_FIELD_TABLE")." WHERE template_id = ".$template_id."";
			$res = $db->db_query($sql);
			
			while ($data = $db->fetch($res))
			{
				array_push($return_array,$data['parameter_field_id']);
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
	
	/**
	 * @param integer $template_id
	 * @param integer $parameter_field_id
	 * @return bool
	 */
	public static function field_exists_in_template($template_id, $parameter_field_id)
	{
		global $db;
		
		if (is_numeric($template_id) and is_numeric($parameter_field_id))
		{
			$sql = "SELECT template_id FROM ".constant("PARAMETER_TEMPLATE_HAS_FIELD_TABLE")." WHERE template_id='".$template_id."' AND parameter_field_id='".$parameter_field_id."'";
			$res = $db->db_query($sql);
			$data = $db->fetch($res);
			
			if ($data['template_id'])
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