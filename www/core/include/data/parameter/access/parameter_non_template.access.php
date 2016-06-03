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
 * Parameter Non Template Access Class
 * @package data
 */
class ParameterNonTemplate_Access
{
	const PARAMETER_NON_TEMPLATE_PK_SEQUENCE = 'core_data_parameter_non_templates_id_seq';

	private $parameter_non_template_id;
	private $datetime;

	/**
	 * @param integer $parameter_id
	 */
	function __construct($parameter_non_template_id)
	{
		global $db;
		
		if ($parameter_non_template_id == null)
		{
			$this->parameter_non_template_id = null;
		}
		else
		{
			$sql = "SELECT * FROM ".constant("PARAMETER_NON_TEMPLATE_TABLE")." WHERE id='".$parameter_non_template_id."'";
			$res = $db->db_query($sql);
			$data = $db->fetch($res);
			
			if ($data['id'])
			{
				$this->parameter_non_template_id	= $parameter_non_template_id;
				$this->datetime						= $data['datetime'];
			}
			else
			{
				$this->parameter_non_template_id	= null;
			}
		}
	}

	function __destruct()
	{
		if ($this->parameter_id)
		{
			unset($this->parameter_non_template_id);
			unset($this->datetime);
		}
	}

	/**
	 * @return integer
	 */
	public function create()
	{
		global $db;

		$datetime = date("Y-m-d H:i:s");
		
		$sql_write = "INSERT INTO ".constant("PARAMETER_NON_TEMPLATE_TABLE")." (id,datetime) " .
				"VALUES (nextval('".self::PARAMETER_NON_TEMPLATE_PK_SEQUENCE."'::regclass),'".$datetime."')";
				
		$res_write = $db->db_query($sql_write);	

		if ($db->row_count($res_write) == 1)
		{
			$sql_read = "SELECT id FROM ".constant("PARAMETER_NON_TEMPLATE_TABLE")." WHERE id = currval('".self::PARAMETER_NON_TEMPLATE_PK_SEQUENCE."'::regclass)";
			$res_read = $db->db_query($sql_read);
			$data_read = $db->fetch($res_read);
								
			self::__construct($data_read['id']);
			
			return $data_read['id'];
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

		if ($this->parameter_non_template_id)
		{
			$parameter_non_template_id_id_tmp = $this->parameter_non_template_id;
			
			$this->__destruct();
			
			$sql = "DELETE FROM ".constant("PARAMETER_NON_TEMPLATE_TABLE")." WHERE id = ".$parameter_non_template_id_id_tmp."";
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
	 * @param integer $datetime
	 * @return bool
	 */
	public function set_datetime($datetime)
	{	
		global $db;

		if ($this->parameter_non_template_id and $datetime)
		{
			$sql = "UPDATE ".constant("PARAMETER_NON_TEMPLATE_TABLE")." SET datetime = '".$datetime."' WHERE id = ".$this->parameter_non_template_id."";
			$res = $db->db_query($sql);
			
			if ($db->row_count($res))
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