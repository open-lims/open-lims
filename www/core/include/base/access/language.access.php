<?php
/**
 * @package base
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
 * Language Access Class
 * @package base
 */
class Language_Access
{
	const LANGUAGE_PK_SEQUENCE = 'core_languages_id_seq';
	
	private $language_id;
	
	private $english_name;
	private $language_name;
	private $tsvector_name;
	private $iso_639;
	private $iso_3166;
	
	/**
	 * @param integer $language_id
	 */
	function __construct($language_id)
	{
		global $db;
		
		if ($language_id == null)
		{
			$this->language_id = null;
		}
		else
		{
			$sql = "SELECT * FROM ".constant("LANGUAGE_TABLE")." WHERE id='".$language_id."'";
			$res = $db->db_query($sql);
			$data = $db->db_fetch_assoc($res);

			if ($data[id])
			{
				$this->language_id		= $language_id;
			
				$this->english_name 	= $data[english_name];
				$this->language_name	= $data[language_name];
				$this->tsvector_name	= $data[tsvector_name];
				$this->iso_639			= $data[iso_639];
				$this->iso_3166			= $data[iso_3166];
			}
			else
			{
				$this->language_id	= null;
			}
		}
	}
	
	function __destruct()
	{
		if ($this->language_id)
		{
			unset($this->language_id);
				
			unset($this->english_name);
			unset($this->language_name);
			unset($this->tsvector_name);
			unset($this->iso_639);
			unset($this->iso_3166);
		}
	}
	
	/**
	 * @param string $english_name
	 * @param string $language_name
	 * @param string $tsvector_name
	 * @param string $iso_639
	 * @param string $iso_3166
	 * @return integer
	 */
	public function create($english_name, $language_name, $tsvector_name, $iso_639, $iso_3166)
	{
		global $db;
		
		if ($english_name and $language_name and $tsvector_name and $iso_639 and $iso_3166)
		{
	 		$sql_write = "INSERT INTO ".constant("LANGUAGE_TABLE")." (id, english_name, language_name, tsvector_name, iso_639, iso_3166) " .
								"VALUES (nextval('".self::LANGUAGE_PK_SEQUENCE."'::regclass),'".$english_name."','".$language_name."','".$tsvector_name."','".$iso_639."','".$iso_3166."')";		
				
			$res_write = $db->db_query($sql_write);
		
			if ($db->db_affected_rows($res_write) == 1)
			{
				$sql_read = "SELECT id FROM ".constant("LANGUAGE_TABLE")." WHERE id = currval('".self::LANGUAGE_PK_SEQUENCE."'::regclass)";
				$res_read = $db->db_query($sql_read);
				$data_read = $db->db_fetch_assoc($res_read);
				
				$this->__construct($data_read[id]);
								
				return $data_read[id];
			}
			else
			{
				return 0;
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

			$sql = "DELETE FROM ".constant("LANGUAGE_TABLE")." WHERE id = '".$id_tmp."'";
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
	public function get_english_name()
	{
		if ($this->english_name)
		{
			return $this->english_name;
		}
		else
		{
			return null;
		}	
	}
	
	/**
	 * @return string
	 */
	public function get_language_name()
	{
		if ($this->language_name)
		{
			return $this->language_name;
		}
		else
		{
			return null;
		}	
	}
	
	/**
	 * @return string
	 */
	public function get_tsvector_name()
	{
		if ($this->tsvector_name)
		{
			return $this->tsvector_name;
		}
		else
		{
			return null;
		}	
	}

	/**
	 * @return string
	 */
	public function get_iso_639()
	{
		if ($this->iso_639)
		{
			return $this->iso_639;
		}
		else
		{
			return null;
		}	
	}
	
	/**
	 * @return string
	 */
	public function get_iso_3166()
	{
		if ($this->iso_3166)
		{
			return $this->iso_3166;
		}
		else
		{
			return null;
		}	
	}
	
	/**
	 * @param string $english_name
	 * @return bool
	 */
	public function set_english_name($english_name)
	{
		global $db;

		if ($this->language_id and $english_name)
		{
			$sql = "UPDATE ".constant("LANGUAGE_TABLE")." SET english_name = '".$english_name."' WHERE id = ".$this->language_id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->english_name = $english_name;
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
	 * @param string $language_name
	 * @return bool
	 */
	public function set_language_name($language_name)
	{
		global $db;

		if ($this->language_id and $language_name)
		{
			$sql = "UPDATE ".constant("LANGUAGE_TABLE")." SET language_name = '".$language_name."' WHERE id = ".$this->language_id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->language_name = $language_name;
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
	 * @param string $tsvector_name
	 * @return bool
	 */
	public function set_tsvector_name($tsvector_name)
	{
		global $db;

		if ($this->language_id and $tsvector_name)
		{
			$sql = "UPDATE ".constant("LANGUAGE_TABLE")." SET tsvector_name = '".$tsvector_name."' WHERE id = ".$this->language_id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->tsvector_name = $tsvector_name;
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
	 * @param string $iso_639
	 * @return bool
	 */
	public function set_iso_639($iso_639)
	{
		global $db;
			
		if ($this->language_id and $iso_639)
		{
			$sql = "UPDATE ".constant("LANGUAGE_TABLE")." SET iso_639 = '".$iso_639."' WHERE id = ".$this->language_id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->iso_639 = $iso_639;
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
	 * @param string $iso_3166
	 * @return bool
	 */
	public function set_iso_3166($iso_3166)
	{
		global $db;

		if ($this->language_id and $iso_3166)
		{
			$sql = "UPDATE ".constant("LANGUAGE_TABLE")." SET iso_3166 = '".$iso_3166."' WHERE id = ".$this->language_id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->iso_3166 = $iso_3166;
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
	 * @return array
	 */
	public static function list_entries()
	{
		global $db;
		
		$return_array = array();
		
		$sql = "SELECT id FROM ".constant("LANGUAGE_TABLE")." ORDER BY english_name";
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
	
}

?>
