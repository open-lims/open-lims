<?php
/**
 * @package item
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
 * Item Class Has Item Information Access Class
 * @package item
 */
class ItemInformation_Access
{
	const ITEM_INFORMATION_PK_SEQUENCE = 'core_item_information_id_seq';

	private $information_id;
	
	private $description;
	private $keywords;
	private $last_update;

	/**
	 * @param integer $information_id
	 */
    function __construct($information_id)
    {
    	global $db;
		
		if ($information_id == null)
		{
			$this->information_id = null;
		}
		else
		{
			$sql = "SELECT * FROM ".constant("ITEM_INFORMATION_TABLE")." WHERE id='".$information_id."'";
			$res = $db->db_query($sql);
			$data = $db->db_fetch_assoc($res);
			
			if ($data['id'])
			{
				$this->information_id	= $information_id;
				
				$this->description		= $data['description'];
				$this->keywords			= $data['keywords'];
				$this->last_update		= $data['last_update'];
			}
			else
			{
				$this->information_id	= null;
			}
		}
    }
    
    function __destruct()
    {
    	if ($this->information_id)
    	{
			unset($this->information_id);
			unset($this->description);
			unset($this->keywords);
			unset($this->last_update);
		}
    }
    
    /**
     * @param string $description
     * @param string $keywords
     * @return integer
     */
    public function create($description, $keywords) 
    {
    	global $db;
		
		if ($description or $keywords)
		{	
			if ($description)
			{
				$description_insert = "'".$description."'";
			}
			else
			{
				$description_insert = "NULL";
			}
			
			if ($keywords)
			{
				$keywords_insert 	= "'".$keywords."'";
			}
			else
			{
				$keywords_insert 	= "NULL";
			}
		
			$datetime = date("Y-m-d H:i:s");
			
			$sql_write = "INSERT INTO ".constant("ITEM_INFORMATION_TABLE")." (id,description,keywords,last_update,language_id) " .
					"VALUES (nextval('".self::ITEM_INFORMATION_PK_SEQUENCE."'::regclass),".$description_insert.",".$keywords_insert.",'".$datetime."',1)";
					
			$res_write = $db->db_query($sql_write);	
			
			if ($db->db_affected_rows($res_write) == 1)
			{
				$sql_read = "SELECT id FROM ".constant("ITEM_INFORMATION_TABLE")." WHERE id = currval('".self::ITEM_INFORMATION_PK_SEQUENCE."'::regclass)";
				$res_read = $db->db_query($sql_read);
				$data_read = $db->db_fetch_assoc($res_read);
									
				self::__construct($data_read['id']);
				
				return $data_read['id'];
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
	
		if ($this->information_id)
		{
			$information_id_tmp = $this->information_id;
			
			$this->__destruct();
			
			$sql = "DELETE FROM ".constant("ITEM_INFORMATION_TABLE")." WHERE id = ".$information_id_tmp."";
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
    public function get_description()
    {
    	if ($this->description)
    	{
			return $this->description;
		}
		else
		{
			return null;
		}
    }
    
    /**
     * @return string
     */
    public function get_keywords()
    {
    	if ($this->keywords)
    	{
			return $this->keywords;
		}
		else
		{
			return null;
		}
    }
    
    /**
     * @return string
     */
    public function get_last_update()
    {
    	if ($this->last_update)
    	{
			return $this->last_update;
		}
		else
		{
			return null;
		}
    }
    
    /**
     * @param string $description
     * @return bool
     */
    public function set_description($description)
    {
    	global $db;
		
		if ($this->information_id and $description)
		{
			$sql = "UPDATE ".constant("ITEM_INFORMATION_TABLE")." SET description = '".$description."' WHERE id = ".$this->information_id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->description = $description;
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
     * @param string $keywords
     * @return bool
     */
    public function set_keywords($keywords)
    {
    	global $db;
	
		if ($this->information_id and $keywords)
		{
			$sql = "UPDATE ".constant("ITEM_INFORMATION_TABLE")." SET keywords = '".$keywords."' WHERE id = ".$this->information_id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->keywords = $keywords;
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
     * @param string $last_update
     * @return bool
     */
    public function set_last_update($last_update)
    {
    	global $db;
	
		if ($this->information_id and $last_update)
		{
			$sql = "UPDATE ".constant("ITEM_INFORMATION_TABLE")." SET last_update = '".$last_update."' WHERE id = ".$this->information_id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->last_update = $last_update;
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
    public function set_description_text_search_vector($string, $language_name)
    {	
		global $db;

		if ($this->information_id and $string)
		{
			if ($language_name == null)
			{
				$language_name_insert = "default";
			}
			else
			{
				$language_name_insert = $language_name;
			}
			
			$sql = "UPDATE ".constant("ITEM_INFORMATION_TABLE")." SET description_text_search_vector = to_tsvector('".$language_name_insert."','".$string."') WHERE id = ".$this->information_id."";
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
     * @param string $string
     * @param string $language_name
     * @return bool
     */
	public function set_keywords_text_search_vector($string, $language_name)
	{		
		global $db;

			if ($this->information_id and $string)
			{
				if ($language_name == null)
				{
					$language_name_insert = "default";
				}
				else
				{
					$language_name_insert = $language_name;
				}
				
				$sql = "UPDATE ".constant("ITEM_INFORMATION_TABLE")." SET keywords_text_search_vector = to_tsvector('".$language_name_insert."','".$string."') WHERE id = ".$this->information_id."";
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
     * @return array
     */
    public static function list_entries()
    {
    	global $db;

		$return_array = array();
		
		$sql = "SELECT id FROM ".constant("ITEM_INFORMATION_TABLE")."";
		$res = $db->db_query($sql);
		
		while ($data = $db->db_fetch_assoc($res))
		{
			array_push($return_array,$data['id']);	
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