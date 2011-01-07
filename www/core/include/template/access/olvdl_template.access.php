<?php
/**
 * @package template
 * @version 0.4.0.0
 * @author Roman Konertz
 * @copyright (c) 2008-2010 by Roman Konertz
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
 * OLVDL Template Access Class
 * @package template
 */
class OlvdlTemplate_Access
{
	const OLVDL_TEMPLATE_TABLE = 'core_olvdl_templates';
	const OLVDL_TEMPLATE_PK_SEQUENCE = 'core_olvdl_templates_id_seq';

	private $olvdl_id;
	private $object_id;
	
	/**
	 * @param integer $olvdl_id
	 */
	function __construct($olvdl_id)
	{
		global $db;
		
		if ($olvdl_id == null)
		{
			$this->olvdl_id = null;
		}
		else
		{
			$sql = "SELECT * FROM ".self::OLVDL_TEMPLATE_TABLE." WHERE id='".$olvdl_id."'";
			$res = $db->db_query($sql);
			$data = $db->db_fetch_assoc($res);
			
			if ($data[id])
			{
				$this->olvdl_id		= $olvdl_id;
				$this->object_id	= $data[object_id];
			}
			else
			{
				$this->olvdl_id		= null;
			}
		}
	}
	
	function __destruct()
	{
		if ($this->olvdl_id)
		{
			unset($this->olvdl_id);
			unset($this->object_id);
		}
	}
	
	/**
	 * @param integer $object_id
	 */
	public function create($object_id)
	{
		global $db;
		
		if (is_numeric($object_id))
		{
			$sql_write = "INSERT INTO ".self::OLVDL_TEMPLATE_TABLE." (id,object_id) " .
					"VALUES (nextval('".self::OLVDL_TEMPLATE_PK_SEQUENCE."'::regclass),".$object_id.")";
					
			$db->db_query($sql_write);	
			
			$sql_read = "SELECT id FROM ".self::OLVDL_TEMPLATE_TABLE." WHERE id = currval('".self::OLVDL_TEMPLATE_PK_SEQUENCE."'::regclass)";
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
	
	/**
	 * @return bool
	 */
	public function delete()
	{
		global $db;

		if ($this->olvdl_id)
		{
			$olvdl_id_tmp = $this->olvdl_id;
			
			$this->__destruct();
			
			$sql = "DELETE FROM ".self::OLVDL_TEMPLATE_TABLE." WHERE id = ".$olvdl_id_tmp."";
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
	public function get_object_id()
	{
		if ($this->object_id)
		{
			return $this->object_id;
		}
		else
		{
			return null;
		}
	}

	/**
	 * @param integer $object_id
	 * @return bool
	 */
	public function set_object_id($object_id)
	{	
		global $db;

		if ($this->olvdl_id and is_numeric($object_id))
		{
			$sql = "UPDATE ".self::OLVDL_TEMPLATE_TABLE." SET object_id = ".$object_id." WHERE id = ".$this->olvdl_id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->object_id = $object_id;
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
	 * @param integer $object_id
	 * @return bool
	 */
	public static function is_object_id($object_id)
	{
		global $db;
		
		if (is_numeric($object_id))
		{
			$sql = "SELECT * FROM ".self::OLVDL_TEMPLATE_TABLE." WHERE object_id='".$object_id."'";
			$res = $db->db_query($sql);
			$data = $db->db_fetch_assoc($res);
			
			if ($data[id])
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
