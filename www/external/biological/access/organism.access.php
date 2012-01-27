<?php
/**
 * @package biological
 * @version 0.4.0.0
 * @author Roman Konertz <konertz@open-lims.org>
 * @copyright (c) 2008-2012 by Roman Konertz
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
 * @package biological
 */
class Organism_Access
{
	const ORGANISM_TABLE = 'bio_organisms';
	
	private $organism_id;
	
	private $identifer;
	private $longname;
	private $shortname;
	private $entry;
	
	/**
	 * @param integer $organism_id
	 */
	function __construct($organism_id)
	{
		global $db;
		
		if ($organism_id == null)
		{
			$this->organism_id = null;
		}
		else
		{
			$sql = "SELECT * FROM ".self::ORGANISM_TABLE." WHERE id = ".$organism_id."";
			$res = $db->db_query($sql);
			$data = $db->db_fetch_assoc($res);
			
			if ($data[id])
			{
				$this->organism_id		= $data[id];
				
				$this->identifer		= $data[identifer];
				$this->longname			= $data[longname];
				$this->shortname		= $data[shortname];
				$this->entry			= $data[entry];
			}
			else
			{
				$this->organism_id = null;
			}
		}
	}
	
	function __destruct()
	{
		if ($this->organism_id)
		{
			unset($this->organism_id);
					
			unset($this->identifer);
			unset($this->longname);
			unset($this->shortname);
			unset($this->entry);
		}
	}
	
	/**
	 * @return string
	 */
	public function get_identifer()
	{
		if ($this->identifer)
		{
			return $this->identifer;
		}
		else
		{
			return null;
		}		
	}
	
	/**
	 * @return stirng
	 */
	public function get_longname()
	{
		if ($this->longname)
		{
			return $this->longname;
		}
		else
		{
			return null;
		}		
	}
	
	/**
	 * @return string
	 */
	public function get_shortname()
	{
		if ($this->shortname)
		{
			return $this->shortname;
		}
		else
		{
			return null;
		}		
	}
	
	/**
	 * @return string
	 */
	public function get_entry()
	{
		if ($this->entry)
		{
			return $this->entry;
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
		
		$sql = "SELECT id FROM ".self::ORGANISM_TABLE." ORDER BY name";
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
	 * @return array
	 */
	public function get_entry_array()
	{
		global $db;
			
		$return_array = array();
			
		$sql = "SELECT id,longname FROM ".self::ORGANISM_TABLE." ORDER BY longname";
		$res = $db->db_query($sql);
		
		while ($data = $db->db_fetch_assoc($res))
		{
			$return_array[$data[id]] = $data[longname];
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
