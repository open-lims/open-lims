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
 * Parameter Template Has Field Access Class
 * @package data
 */
class ParameterNonTemplateHasField_Access
{
	private $non_template_id;
	private $parameter_field_id;

	/**
	 * @param integer $non_template_id
	 * @param integer $parameter_field_id
	 */
	function __construct($non_template_id, $parameter_field_id)
	{
		global $db;
		
		if ($non_template_id == null or $parameter_field_id == null)
		{
			$this->non_template_id = null;
			$this->parameter_field_id = null;
		}
		else
		{
			$sql = "SELECT * FROM ".constant("PARAMETER_NON_TEMPLATE_HAS_FIELD_TABLE")." WHERE non_template_id='".$non_template_id."' AND parameter_field_id='".$parameter_field_id."'";
			$res = $db->db_query($sql);
			$data = $db->db_fetch_assoc($res);
			
			if ($data['non_template_id'] and $data['parameter_field_id'])
			{
				$this->non_template_id	= $non_template_id;
				$this->parameter_field_id 	= $parameter_field_id;
			}
			else
			{
				$this->non_template_id	= null;
				$this->parameter_field_id 	= null;
			}
		}
	}

	function __destruct()
	{
		if ($this->non_template_id and $this->parameter_field_id)
		{
			unset($this->non_template_id);
			unset($this->parameter_field_id);
		}
	}

	/**
	 * @param integer $non_template_id
	 * @param integer $parameter_field_id
	 * @return boolean
	 */
	public function create($non_template_id, $parameter_field_id)
	{
		global $db;
		
		if (is_numeric($non_template_id) and is_numeric($parameter_field_id))
		{	
			$sql_write = "INSERT INTO ".constant("PARAMETER_NON_TEMPLATE_HAS_FIELD_TABLE")." (non_template_id,parameter_field_id) " .
					"VALUES ('".$non_template_id."','".$parameter_field_id."')";
					
			$res_write = $db->db_query($sql_write);	

			if ($db->db_affected_rows($res_write) == 1)
			{
				self::__construct($non_template_id, $parameter_field_id);
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

		if ($this->non_template_id and $this->parameter_field_id)
		{
			$non_template_id_tmp = $this->non_template_id;
			$parameter_field_id_tmp = $this->parameter_field_id;
			
			
			$this->__destruct();
			
			$sql = "DELETE FROM ".constant("PARAMETER_NON_TEMPLATE_HAS_FIELD_TABLE")." WHERE non_template_id=".$non_template_id_tmp." AND parameter_field_id = ".$parameter_field_id_tmp."";
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
}
?>