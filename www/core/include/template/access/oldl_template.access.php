<?php
/**
 * @package template
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
 * OLDL Template Access Class
 * @package template
 */
class OldlTemplate_Access
{
	const OLDL_TEMPLATE_PK_SEQUENCE = 'core_oldl_templates_id_seq';

	private $oldl_id;
	private $data_entity_id;
	
	/**
	 * @param integer $oldl_id
	 */
	function __construct($oldl_id)
	{
		global $db;
		
		if ($oldl_id == null)
		{
			$this->oldl_id = null;
		}
		else
		{
			$sql = "SELECT * FROM ".constant("OLDL_TEMPLATE_TABLE")." WHERE id='".$oldl_id."'";
			$res = $db->db_query($sql);
			$data = $db->fetch($res);
			
			if ($data['id'])
			{
				$this->oldl_id		= $oldl_id;
				$this->data_entity_id	= $data['data_entity_id'];
			}
			else
			{
				$this->oldl_id		= null;
			}
		}
	}
	
	function __destruct()
	{
		if ($this->oldl_id)
		{
			unset($this->oldl_id);
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
			$sql_write = "INSERT INTO ".constant("OLDL_TEMPLATE_TABLE")." (id,data_entity_id) " .
					"VALUES (nextval('".self::OLDL_TEMPLATE_PK_SEQUENCE."'::regclass),".$data_entity_id.")";
					
			$db->db_query($sql_write);	
			
			$sql_read = "SELECT id FROM ".constant("OLDL_TEMPLATE_TABLE")." WHERE id = currval('".self::OLDL_TEMPLATE_PK_SEQUENCE."'::regclass)";
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

		if ($this->oldl_id)
		{
			$oldl_id_tmp = $this->oldl_id;
			
			$this->__destruct();
			
			$sql = "DELETE FROM ".constant("OLDL_TEMPLATE_TABLE")." WHERE id = ".$oldl_id_tmp."";
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
			
		if ($this->oldl_id and is_numeric($data_entity_id))
		{
			$sql = "UPDATE ".constant("OLDL_TEMPLATE_TABLE")." SET data_entity_id = ".$data_entity_id." WHERE id = ".$this->oldl_id."";
			$res = $db->db_query($sql);
			
			if ($db->row_count($res))
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
	 * @param integer $data_entity_id
	 * @return bool
	 */
	public static function is_data_entity_id($data_entity_id)
	{
		global $db;
		
		if (is_numeric($data_entity_id))
		{
			$sql = "SELECT * FROM ".constant("OLDL_TEMPLATE_TABLE")." WHERE data_entity_id='".$data_entity_id."'";
			$res = $db->db_query($sql);
			$data = $db->fetch($res);
			
			if ($data['id'])
			{
				return true;
			}
			else
			{
				return false;
			}
		}
	}
}
?>
