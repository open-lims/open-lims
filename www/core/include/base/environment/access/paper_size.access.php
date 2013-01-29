<?php
/**
 * @package base
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
 * Paper Size Access Class
 * @package base
 */
class PaperSize_Access
{
	const PAPER_SIZE_PK_SEQUENCE = 'core_paper_sizes_id_seq';
	
	private $id;
	
	private $name;
	private $width;
	private $height;
	private $margin_left;
	private $margin_right;
	private $margin_top;
	private $margin_bottom;
	private $base;
	private $standard;
	
	/**
	 * @param integer $id
	 */
	function __construct($id)
	{
		global $db;
		
		if ($id == null)
		{
			$this->id = null;
		}
		else
		{	
			$sql = "SELECT * FROM ".constant("PAPER_SIZE_TABLE")." WHERE id = ".$id."";
			$res = $db->db_query($sql);
			$data = $db->db_fetch_assoc($res);
			
			if ($data['id'])
			{
				$this->id				= $data['id'];
				
				$this->name				= $data['name'];
				$this->width			= $data['width'];
				$this->height			= $data['height'];
				$this->margin_left		= $data['margin_left'];
				$this->margin_right		= $data['margin_right'];
				$this->margin_top		= $data['margin_top'];
				$this->margin_bottom	= $data['margin_bottom'];
				
				if ($data['base'] == 't')
				{
					$this->base = true;
				}
				else
				{
					$this->base = false;
				}
				
				if ($data['standard'] == 't')
				{
					$this->standard = true;
				}
				else
				{
					$this->standard = false;
				}
			}
			else
			{
				$this->id = null;
			}
		}
	}
	
	function __destruct()
	{
		if ($this->id)
		{
			unset($this->id);
			unset($this->name);
			unset($this->width);
			unset($this->height);
			unset($this->margin_left);
			unset($this->margin_right);
			unset($this->margin_top);
			unset($this->margin_bottom);
			unset($this->base);
			unset($this->standard);
		}
	}
	
	/**
	 * @param string $name
	 * @param float $width
	 * @param float $height
	 * @param float $margin_left
	 * @param float $margin_right
	 * @param float $margin_top
	 * @param float $margin_bottom
	 * @return integer
	 */
	public function create($name, $width, $height, $margin_left, $margin_right, $margin_top, $margin_bottom)
	{
		global $db;
		
		if ($name and is_numeric($width) and is_numeric($height) and is_numeric($margin_left) and is_numeric($margin_right) and is_numeric($margin_top) and is_numeric($margin_bottom))
		{			
			$sql_write = "INSERT INTO ".constant("PAPER_SIZE_TABLE")." (id,name,width,height,margin_left,margin_right,margin_top,margin_bottom,base,standard) " .
							"VALUES (nextval('".self::PAPER_SIZE_PK_SEQUENCE."'::regclass),'".$name."','".$width."','".$height."','".$margin_left."','".$margin_right."','".$margin_top."','".$margin_bottom."','f','f')";
			
			$res_write = $db->db_query($sql_write);
			
			if ($db->db_affected_rows($res_write) == 1)
			{
				$sql_read = "SELECT id FROM ".constant("PAPER_SIZE_TABLE")." WHERE id = currval('".self::PAPER_SIZE_PK_SEQUENCE."'::regclass)";
				$res_read = $db->db_query($sql_read);
				$data_read = $db->db_fetch_assoc($res_read);
				
				$this->__construct($data_read['id']);
								
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
    	
    	if ($this->id)
    	{
    		$tmp_id = $this->id;
    		
    		$this->__destruct();

    		$sql = "DELETE FROM ".constant("PAPER_SIZE_TABLE")." WHERE id = ".$tmp_id."";
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
	 * @return float
	 */
	public function get_width()
	{
		if ($this->width)
		{
			return $this->width;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return float
	 */
	public function get_height()
	{
		if ($this->height)
		{
			return $this->height;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return float
	 */
	public function get_margin_left()
	{
		if ($this->margin_left)
		{
			return $this->margin_left;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return float
	 */
	public function get_margin_right()
	{
		if ($this->margin_right)
		{
			return $this->margin_right;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return float
	 */
	public function get_margin_top()
	{
		if ($this->margin_top)
		{
			return $this->margin_top;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return float
	 */
	public function get_margin_bottom()
	{
		if ($this->margin_bottom)
		{
			return $this->margin_bottom;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return bool
	 */
	public function get_base()
	{
		if (isset($this->base))
		{
			return $this->base;
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * @return bool
	 */
	public function get_standard()
	{
		if (isset($this->standard))
		{
			return $this->standard;
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * @param string $name
	 * @return bool
	 */
	public function set_name($name)
	{
		global $db;

		if ($this->id and $name)
		{
			$sql = "UPDATE ".constant("PAPER_SIZE_TABLE")." SET name = '".$name."' WHERE id = ".$this->id."";
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
	 * @param float $width
	 * @return bool
	 */
	public function set_width($width)
	{
		global $db;

		if ($this->id and is_numeric($width))
		{
			$sql = "UPDATE ".constant("PAPER_SIZE_TABLE")." SET width = '".$width."' WHERE id = ".$this->id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->width = $width;
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
	 * @param float $height
	 * @return bool
	 */
	public function set_height($height)
	{
		global $db;

		if ($this->id and is_numeric($height))
		{
			$sql = "UPDATE ".constant("PAPER_SIZE_TABLE")." SET height = '".$height."' WHERE id = ".$this->id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->height = $height;
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
	 * @param float $margin_left
	 * @return bool
	 */
	public function set_margin_left($margin_left)
	{
		global $db;

		if ($this->id and is_numeric($margin_left))
		{
			$sql = "UPDATE ".constant("PAPER_SIZE_TABLE")." SET margin_left = '".$margin_left."' WHERE id = ".$this->id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->margin_left = $margin_left;
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
	 * @param float $margin_right
	 * @return bool
	 */
	public function set_margin_right($margin_right)
	{
		global $db;

		if ($this->id and is_numeric($margin_right))
		{
			$sql = "UPDATE ".constant("PAPER_SIZE_TABLE")." SET margin_right = '".$margin_right."' WHERE id = ".$this->id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->margin_right = $margin_right;
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
	 * @param float $margin_top
	 * @return bool
	 */
	public function set_margin_top($margin_top)
	{
		global $db;

		if ($this->id and is_numeric($margin_top))
		{
			$sql = "UPDATE ".constant("PAPER_SIZE_TABLE")." SET margin_top = '".$margin_top."' WHERE id = ".$this->id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->margin_top = $margin_top;
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
	 * @param float $margin_bottom
	 * @return bool
	 */
	public function set_margin_bottom($margin_bottom)
	{
		global $db;

		if ($this->id and is_numeric($margin_bottom))
		{
			$sql = "UPDATE ".constant("PAPER_SIZE_TABLE")." SET margin_bottom = '".$margin_bottom."' WHERE id = ".$this->id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->margin_bottom = $margin_bottom;
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
	 * @param bool $base
	 * @return bool
	 */
	public function set_base($base)
	{
		global $db;

		if ($this->id and isset($base))
		{
			if ($base == true)
			{
				$base_insert = "t";
			}
			else
			{
				$base_insert = "f";
			}
			
			$sql = "UPDATE ".constant("PAPER_SIZE_TABLE")." SET base = '".$base_insert."' WHERE id = ".$this->id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->base = $base;
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
	 * @param bool $standard
	 * @return bool
	 */
	public function set_standard($standard)
	{
		global $db;

		if ($this->id and isset($standard))
		{
			if ($standard == true)
			{
				$standard_insert = "t";
			}
			else
			{
				$standard_insert = "f";
			}
			
			$sql = "UPDATE ".constant("PAPER_SIZE_TABLE")." SET base = '".$standard_insert."' WHERE id = ".$this->id."";
			$res = $db->db_query($sql);
			
			if ($db->db_affected_rows($res))
			{
				$this->standard = $standard;
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
		
		$sql = "SELECT id,name FROM ".constant("PAPER_SIZE_TABLE")." ORDER BY standard DESC,name";
		$res = $db->db_query($sql);
		
		while ($data = $db->db_fetch_assoc($res))
		{
			$temp_array = array();
			$temp_array['id'] = $data['id'];
			$temp_array['name'] = $data['name'];
			array_push($return_array,$temp_array);
			unset($temp_array);
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
	 * @param integer $id
	 * @return array
	 */
	public static function get_size_by_id($id)
	{
		global $db;
			
		if (is_numeric($id))
		{
			$sql = "SELECT width,height,margin_left,margin_right,margin_top,margin_bottom FROM ".constant("PAPER_SIZE_TABLE")." WHERE id = '".$id."'";
			$res = $db->db_query($sql);
			$data = $db->db_fetch_assoc($res);
			
			$return_array = array();
			$return_array['width'] 			= $data['width'];
			$return_array['height'] 		= $data['height'];
			$return_array['margin_left'] 	= $data['margin_left'];
			$return_array['margin_right'] 	= $data['margin_right'];
			$return_array['margin_top'] 	= $data['margin_top'];
			$return_array['margin_bottom'] 	= $data['margin_bottom'];
			
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
	public static function get_standard_size()
	{
		global $db;
			
		$sql = "SELECT width,height,margin_left,margin_right,margin_top,margin_bottom FROM ".constant("PAPER_SIZE_TABLE")." WHERE standard = 't'";
		$res = $db->db_query($sql);
		$data = $db->db_fetch_assoc($res);
		
		$return_array = array();
		$return_array['width'] 			= $data['width'];
		$return_array['height'] 		= $data['height'];
		$return_array['margin_left'] 	= $data['margin_left'];
		$return_array['margin_right'] 	= $data['margin_right'];
		$return_array['margin_top'] 	= $data['margin_top'];
		$return_array['margin_bottom'] 	= $data['margin_bottom'];
		
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