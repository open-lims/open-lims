<?php
/**
 * @package sample
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
 * Sample Access Class
 * @package sample
 */
class Sample_Access
{
	const SAMPLE_PK_SEQUENCE = 'core_samples_id_seq';
	
	private $sample_id;
	
	private $name;
	private $datetime;
	private $owner_id;
	private $template_id;
	private $supplier;
	private $available;
	private $deleted;
	private $comment;
	
	/**
	 * @param integer $sample_id
	 */
	function __construct($sample_id)
	{
		global $db;
			
		if ($sample_id == null)
		{
			$this->sample_id = null;
		}
		else
		{
			$sql = "SELECT * FROM ".constant("SAMPLE_TABLE")." WHERE id='".$sample_id."'";
			$res = $db->db_query($sql);			
			$data = $db->db_fetch_assoc($res);
			
			if ($data[id])
			{
				$this->sample_id 	= $sample_id;
				
				$this->name			= $data[name];
				$this->datetime		= $data[datetime];
				$this->owner_id		= $data[owner_id];
				$this->template_id	= $data[template_id];
				$this->supplier		= $data[supplier];
				$this->comment		= $data[comment];
				
				if ($data[deleted] == "t")
				{
					$this->deleted	= true;
				}
				else
				{
					$this->deleted	= false;
				}
				
				if ($data[available] == "t")
				{
					$this->available	= true;
				}
				else
				{
					$this->available	= false;
				}
			}
			else
			{
				$this->sample_id = null;
			}
		}
	}
	
	function __destruct()
	{
		if ($this->sample_id)
		{
			unset($this->sample_id);
	
			unset($this->name);
			unset($this->datetime);
			unset($this->owner_id);
			unset($this->template_id);
			unset($this->supplier);
			unset($this->available);
			unset($this->deleted);
			unset($this->comment);
		}
	}
	
	/**
	 * @param string $name
	 * @param integer $owner_id
	 * @param integer $template_id
	 * @param string $supplier
	 * @param string $comment
	 * @return integer
	 */
	public function create($name, $owner_id, $template_id, $supplier, $comment)
	{
		global $db;
		
		if ($name and is_numeric($owner_id) and is_numeric($template_id) and $supplier)
		{
			if (!$comment)
			{
				$comment_insert = "NULL";
			}
			else
			{
				$comment_insert = "'".$comment."'";
			}
			
			$datetime = date("Y-m-d H:i:s");
			
			$sql_write = "INSERT INTO ".constant("SAMPLE_TABLE")." (id, name, datetime, owner_id, template_id, supplier, available, deleted, comment, language_id) " .
					"VALUES (nextval('".self::SAMPLE_PK_SEQUENCE."'::regclass), '".$name."','".$datetime."',".$owner_id.",".$template_id.",'".$supplier."','t','f',".$comment_insert.",1)";
			$res_write = $db->db_query($sql_write);
			
			if ($db->db_affected_rows($res_write) == 1)
			{
				$sql_read = "SELECT id FROM ".constant("SAMPLE_TABLE")." WHERE id = currval('".self::SAMPLE_PK_SEQUENCE."'::regclass)";
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
		
		if ($this->sample_id)
		{
			$tmp_sample_id = $this->sample_id;
			
			$this->__destruct();
						
			$sql = "DELETE FROM ".constant("SAMPLE_TABLE")." WHERE id = ".$tmp_sample_id."";
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
	 * @return integer
	 */
	public function get_owner_id()
	{
		if ($this->owner_id)
		{
			return $this->owner_id;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return integer
	 */
	public function get_template_id()
	{
		if ($this->template_id)
		{
			return $this->template_id;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return string
	 */
	public function get_supplier()
	{
		if ($this->supplier)
		{
			return $this->supplier;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return bool
	 */
	public function get_deleted()
	{
		if (isset($this->deleted))
		{
			return $this->deleted;
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * @return bool
	 */
	public function get_available()
	{
		if (isset($this->available))
		{
			return $this->available;
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * @return string
	 */
	public function get_comment()
	{
		if ($this->comment)
		{
			return $this->comment;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @param string $name
	 * @return bool
	 */
	public function set_name($name)
	{
		global $db;
			
		if ($this->sample_id and $name)
		{
			$sql = "UPDATE ".constant("SAMPLE_TABLE")." SET name = '".$name."' WHERE id = '".$this->sample_id."'";
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
	 * @param string $datetime
	 * @return bool
	 */
	public function set_datetime($datetime)
	{
		global $db;
			
		if ($this->sample_id and $datetime)
		{
			$sql = "UPDATE ".constant(":SAMPLE_TABLE")." SET datetime = '".$datetime."' WHERE id = '".$this->sample_id."'";
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
	
	/**
	 * @param integer $owner_id
	 * @return bool
	 */
	public function set_owner_id($owner_id)
	{
		global $db;
			
		if ($this->sample_id and is_numeric($owner_id))
		{
			$sql = "UPDATE ".constant("SAMPLE_TABLE")." SET owner_id = '".$owner_id."' WHERE id = '".$this->sample_id."'";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->owner_id = $owner_id;
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
	 * @return bool
	 */
	public function set_template_id($template_id)
	{
		global $db;
			
		if ($this->sample_id and is_numeric($template_id))
		{
			$sql = "UPDATE ".constant("SAMPLE_TABLE")." SET template_id = '".$template_id."' WHERE id = '".$this->sample_id."'";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->template_id = $template_id;
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
	 * @param string $supplier
	 * @return bool
	 */
	public function set_supplier($supplier)
	{
		global $db;

		if ($this->sample_id and $supplier)
		{
			$sql = "UPDATE ".constant("SAMPLE_TABLE")." SET supplier = '".$supplier."' WHERE id = '".$this->sample_id."'";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->supplier = $supplier;
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
	 * @param bool $available
	 * @return bool
	 */
	public function set_available($available)
	{
		global $db;
			
		if ($this->sample_id and isset($available))
		{
			if ($available == true)
			{
				$available_insert = "t";
			}
			else
			{
				$available_insert = "f";
			}
			
			$sql = "UPDATE ".constant("SAMPLE_TABLE")." SET available = '".$available_insert."' WHERE id = '".$this->sample_id."'";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->available = $available;
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
	 * @param bool $deleted
	 * @return bool
	 */
	public function set_deleted($deleted)
	{
		global $db;
			
		if ($this->sample_id and isset($deleted))
		{
			if ($deleted == true)
			{
				$deleted_insert = "t";
			}
			else
			{
				$deleted_insert = "f";
			}
			
			$sql = "UPDATE ".constant("SAMPLE_TABLE")." SET deleted = '".$deleted_insert."' WHERE id = '".$this->sample_id."'";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->deleted = $deleted;
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
	 * @param string $comment
	 * @return bool
	 */
	public function set_comment($comment)
	{
		global $db;
			
		if ($this->sample_id and $comment)
		{
			$sql = "UPDATE ".constant("SAMPLE_TABLE")." SET comment = '".$comment."' WHERE id = '".$this->sample_id."'";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->comment = $comment;
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
	 * @param string $string
	 * @param string $language_name
	 * @return bool
	 */
	public function set_comment_text_search_vector($string, $language_name)
	{
		global $db;
			
		if ($this->sample_id and $string)
		{
			if ($language_name == null)
			{
				$language_name_insert = "default";
			}
			else
			{
				$language_name_insert = $language_name;
			}
			
			$sql = "UPDATE ".constant("SAMPLE_TABLE")." SET comment_text_search_vector = to_tsvector('".$language_name_insert."','".$string."') WHERE id = ".$this->sample_id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
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
	 * @param integer $owner_id
	 * @return array
	 */
	public static function list_entries_by_owner_id($owner_id)
	{
		global $db;
			
		if (is_numeric($owner_id))
		{
			$return_array = array();
			
			$sql = "SELECT id FROM ".constant("SAMPLE_TABLE")." WHERE owner_id = ".$owner_id."";
			$res = $db->db_query($sql);
			
			while ($data = $db->db_fetch_assoc($res))
			{
				array_push($return_array,$data[id]);
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
	 * @return array
	 */
	public static function list_entries_by_template_id($template_id)
	{
		global $db;
			
		if (is_numeric($template_id))
		{
			$return_array = array();
			
			$sql = "SELECT id FROM ".constant("SAMPLE_TABLE")." WHERE template_id = ".$template_id."";
			$res = $db->db_query($sql);
			
			while ($data = $db->db_fetch_assoc($res))
			{
				array_push($return_array,$data[id]);
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
		
		$sql = "SELECT id FROM ".constant("SAMPLE_TABLE")."";
		$res = $db->db_query($sql);
		
		while ($data = $db->db_fetch_assoc($res))
		{
			array_push($return_array,$data[id]);
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
	 * @param integer $sample_id
	 * @return bool
	 */
	public static function exist_sample_by_sample_id($sample_id)
	{
		global $db;
			
		if (is_numeric($sample_id))
		{
			
			$sql = "SELECT id FROM ".constant("SAMPLE_TABLE")." WHERE id = ".$sample_id."";
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
		else
		{
			return false;
		}
	}
		
}
?>
