<?php
/**
 * @package sample
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
 * Sample Has Organisation Unit Access Class
 * @package sample
 */
class SampleHasOrganisationUnit_Access
{
	const SAMPLE_HAS_ORGANISATION_UNIT_PK_SEQUENCE = 'core_sample_has_organisation_units_primary_key_seq';

	private $primary_key;

	private $sample_id;
	private $organisation_unit_id;

	/**
	 * @param integer $primary_key
	 */
	function __construct($primary_key)
	{
		global $db;

		if ($primary_key == null)
		{
			$this->primary_key = null;
		}
		else
		{
			$sql = "SELECT * FROM ".constant("SAMPLE_HAS_ORGANISATION_UNIT_TABLE")." WHERE primary_key='".$primary_key."'";
			$res = $db->db_query($sql);			
			$data = $db->fetch($res);
			
			if ($data['primary_key'])
			{
				$this->primary_key 			= $primary_key;
				
				$this->sample_id			= $data['sample_id'];
				$this->organisation_unit_id	= $data['organisation_unit_id'];
			}
			else
			{
				$this->primary_key = null;
			}
		}			
	}
	
	function __destruct()
	{
		if ($this->primary_key)
		{
			unset($this->primary_key);
			
			unset($this->sample_id);
			unset($this->organisation_unit_id);
		}
	}
	
	/**
	 * @param integer $sample_id
	 * @param integer $organisation_unit_id
	 * @return integer
	 */
	public function create($sample_id, $organisation_unit_id)
	{
		global $db;
		
		if (is_numeric($sample_id) and is_numeric($organisation_unit_id))
		{
			$sql_write = "INSERT INTO ".constant("SAMPLE_HAS_ORGANISATION_UNIT_TABLE")." (primary_key,sample_id,organisation_unit_id) " .
					"VALUES (nextval('".self::SAMPLE_HAS_ORGANISATION_UNIT_PK_SEQUENCE."'::regclass),".$sample_id.",".$organisation_unit_id.")";
			$res_write = $db->db_query($sql_write);
			
			if ($db->row_count($res_write) == 1)
			{
				$sql_read = "SELECT primary_key FROM ".constant("SAMPLE_HAS_ORGANISATION_UNIT_TABLE")." WHERE primary_key = currval('".self::SAMPLE_HAS_ORGANISATION_UNIT_PK_SEQUENCE."'::regclass)";
				$res_read = $db->db_query($sql_read);
				$data_read = $db->fetch($res_read);
				
				self::__construct($data_read['primary_key']);
				
				return $data_read['primary_key'];
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
		
		if ($this->primary_key)
		{
			$tmp_primary_key = $this->primary_key;
			
			$this->__destruct();
						
			$sql = "DELETE FROM ".constant("SAMPLE_HAS_ORGANISATION_UNIT_TABLE")." WHERE primary_key = ".$tmp_primary_key."";
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
	public function get_organisation_unit_id()
	{
		if ($this->organisation_unit_id)
		{
			return $this->organisation_unit_id;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @param integer $sample_id
	 * @return bool
	 */
	public function set_sample_id($sample_id)
	{
		global $db;

		if ($this->primary_key and is_numeric($sample_id))
		{
			$sql = "UPDATE ".constant("SAMPLE_HAS_ORGANISATION_UNIT_TABLE")." SET sample_id = '".$sample_id."' WHERE primary_key = '".$this->primary_key."'";
			$res = $db->db_query($sql);
			
			if ($db->row_count($res))
			{
				$this->sample_id = $sample_id;
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
	 * @param integer $organisation_unit_id
	 * @return bool
	 */
	public function set_organisation_unit_id($organisation_unit_id)
	{
		global $db;

		if ($this->primary_key and is_numeric($organisation_unit_id))
		{
			$sql = "UPDATE ".constant("SAMPLE_HAS_ORGANISATION_UNIT_TABLE")." SET organisation_unit_id = '".$organisation_unit_id."' WHERE primary_key = '".$this->primary_key."'";
			$res = $db->db_query($sql);
			
			if ($db->row_count($res))
			{
				$this->organisation_unit_id = $organisation_unit_id;
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
	 * @param integer $sample_id
	 * @param integer $organisation_unit_id
	 * @return integer
	 */
	public static function get_entry_by_sample_id_and_organisation_unit_id($sample_id, $organisation_unit_id)
	{
		
		global $db;
			
		if (is_numeric($sample_id) and is_numeric($organisation_unit_id))
		{
			$return_array = array();
			
			$sql = "SELECT primary_key FROM ".constant("SAMPLE_HAS_ORGANISATION_UNIT_TABLE")." WHERE sample_id = ".$sample_id." AND organisation_unit_id = ".$organisation_unit_id."";
			$res = $db->db_query($sql);
			$data = $db->fetch($res);

			if ($data['primary_key'])
			{
				return $data['primary_key'];
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
	 * @param integer $sample_id
	 * @return array
	 */
	public static function list_entries_by_sample_id($sample_id)
	{
		global $db;
			
		if (is_numeric($sample_id))
		{
			$return_array = array();
			
			$sql = "SELECT primary_key FROM ".constant("SAMPLE_HAS_ORGANISATION_UNIT_TABLE")." WHERE sample_id = ".$sample_id."";
			$res = $db->db_query($sql);
			
			while ($data = $db->fetch($res))
			{
				array_push($return_array,$data['primary_key']);
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
	 * @param integer $organisation_unit_id
	 * @return array
	 */
	public static function list_entries_by_organisation_unit_id($organisation_unit_id)
	{
		global $db;

		if (is_numeric($organisation_unit_id))
		{
			$return_array = array();
			
			$sql = "SELECT primary_key FROM ".constant("SAMPLE_HAS_ORGANISATION_UNIT_TABLE")." WHERE organisation_unit_id = ".$organisation_unit_id."";
			$res = $db->db_query($sql);
			
			while ($data = $db->fetch($res))
			{
				array_push($return_array,$data['primary_key']);
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
	 * @return array
	 */
	public static function list_entries()
	{
		global $db;
			
		$return_array = array();
		
		$sql = "SELECT primary_key FROM ".constant("SAMPLE_HAS_ORGANISATION_UNIT_TABLE")."";
		$res = $db->db_query($sql);
		
		while ($data = $db->fetch($res))
		{
			array_push($return_array,$data['primary_key']);
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
	
	/**
	 * @param integer $organisation_unit_id
	 * @return bool
	 */
	public static function delete_by_organisation_unit_id($organisation_unit_id)
	{
		global $db;
		
		if (is_numeric($organisation_unit_id))
		{
			$sql = "DELETE FROM ".constant("SAMPLE_HAS_ORGANISATION_UNIT_TABLE")." WHERE organisation_unit_id = ".$organisation_unit_id."";
			$res = $db->db_query($sql);
			
			return true;
		}
		else
		{
			return false;
		}
	}
	
}
?>
