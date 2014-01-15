<?php
/**
 * @package base
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
 * 
 */
require_once("interfaces/paper_size.interface.php");

if (constant("UNIT_TEST") == false or !defined("UNIT_TEST"))
{
	require_once("access/paper_size.access.php");
}

/**
 * Paper Size Class
 * @package base
 */
class PaperSize implements PaperSizeInterface
{
	private $paper_size_id;
	private $paper_size;
	
	/**
	 * @see PaperSizeInterface::__construct()
	 * @param $paper_size_id
	 */
	function __construct($paper_size_id)
	{
		if (is_numeric($paper_size_id))
    	{
    		$this->paper_size_id = $paper_size_id;
    		$this->paper_size = new PaperSize_Access($paper_size_id);
		}
		else
		{		
			$this->paper_size_id = null;
    		$this->paper_size = new PaperSize_Access(null);
    	}
	}
	
	function __destruct()
	{
		if ($this->paper_size_id)
    	{
    		unset($this->paper_size_id);
    		unset($this->paper_size);
    	}
	}
	
	/**
	 * @see PaperSizeInterface::create()
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
		if ($name and is_numeric($width) and is_numeric($height) and is_numeric($margin_left) and is_numeric($margin_right) and is_numeric($margin_top) and is_numeric($margin_bottom))
		{
			$width 			= str_replace(",",".",$width);
			$height 		= str_replace(",",".",$height);
			$margin_left 	= str_replace(",",".",$margin_left);
			$margin_right 	= str_replace(",",".",$margin_right);
			$margin_top 	= str_replace(",",".",$margin_top);
			$margin_bottom 	= str_replace(",",".",$margin_bottom);
			
			return $this->paper_size->create($name, $width, $height, $margin_left, $margin_right, $margin_top, $margin_bottom);
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * @see PaperSizeInterface::delete()
	 * @return bool
	 */
	public function delete()
	{
		if ($this->paper_size and $this->paper_size_id)
		{
			if ($this->get_base() == false)
			{
				return $this->paper_size->delete();
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
	 * @see PaperSizeInterface::get_name()
	 * @return string
	 */
	public function get_name()
	{
		if ($this->paper_size and $this->paper_size_id)
		{
			return $this->paper_size->get_name();
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @see PaperSizeInterface::get_width()
	 * @return float
	 */
	public function get_width()
	{
		if ($this->paper_size and $this->paper_size_id)
		{
			return $this->paper_size->get_width();
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @see PaperSizeInterface::get_height()
	 * @return float
	 */
	public function get_height()
	{
		if ($this->paper_size and $this->paper_size_id)
		{
			return $this->paper_size->get_height();
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @see PaperSizeInterface::get_margin_left()
	 * @return float
	 */
	public function get_margin_left()
	{
		if ($this->paper_size and $this->paper_size_id)
		{
			return $this->paper_size->get_margin_left();
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @see PaperSizeInterface::get_margin_right()
	 * @return float
	 */
	public function get_margin_right()
	{
		if ($this->paper_size and $this->paper_size_id)
		{
			return $this->paper_size->get_margin_right();
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @see PaperSizeInterface::get_margin_top()
	 * @return float
	 */
	public function get_margin_top()
	{
		if ($this->paper_size and $this->paper_size_id)
		{
			return $this->paper_size->get_margin_top();
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @see PaperSizeInterface::get_margin_bottom()
	 * @return float
	 */
	public function get_margin_bottom()
	{
		if ($this->paper_size and $this->paper_size_id)
		{
			return $this->paper_size->get_margin_bottom();
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @see PaperSizeInterface::get_base()
	 * @return float
	 */
	public function get_base()
	{
		if ($this->paper_size and $this->paper_size_id)
		{
			return $this->paper_size->get_base();
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @see PaperSizeInterface::set_name()
	 * @param string $name
	 * @return bool
	 */
	public function set_name($name)
	{
		if ($this->paper_size and $this->paper_size_id and $name)
		{
			if ($this->get_base() == false)
			{
				return $this->paper_size->set_name($name);
			}
			else
			{
				return true;
			}
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * @see PaperSizeInterface::set_width()
	 * @param float $width
	 * @return bool
	 */
	public function set_width($width)
	{
		if ($this->paper_size and $this->paper_size_id and $width)
		{
			if ($this->get_base() == false)
			{
				return $this->paper_size->set_width($width);
			}
			else
			{
				return true;
			}
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * @see PaperSizeInterface::set_height()
	 * @param float $height
	 * @return bool
	 */
	public function set_height($height)
	{
		if ($this->paper_size and $this->paper_size_id and $height)
		{
			if ($this->get_base() == false)
			{
				return $this->paper_size->set_height($height);
			}
			else
			{
				return true;
			}
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * @see PaperSizeInterface::set_margin_left()
	 * @param float $margin_left
	 * @return bool
	 */
	public function set_margin_left($margin_left)
	{
		if ($this->paper_size and $this->paper_size_id and $margin_left)
		{
			return $this->paper_size->set_margin_left($margin_left);
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * @see PaperSizeInterface::set_margin_right()
	 * @param float $margin_right
	 * @return bool
	 */
	public function set_margin_right($margin_right)
	{
		if ($this->paper_size and $this->paper_size_id and $margin_right)
		{
			return $this->paper_size->set_margin_right($margin_right);
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * @see PaperSizeInterface::set_margin_top()
	 * @param float $margin_top
	 * @return bool
	 */
	public function set_margin_top($margin_top)
	{
		if ($this->paper_size and $this->paper_size_id and $margin_top)
		{
			return $this->paper_size->set_margin_top($margin_top);
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * @see PaperSizeInterface::set_margin_bottom()
	 * @param float $margin_bottom
	 * @return bool
	 */
	public function set_margin_bottom($margin_bottom)
	{
		if ($this->paper_size and $this->paper_size_id and $margin_bottom)
		{
			return $this->paper_size->set_margin_bottom($margin_bottom);
		}
		else
		{
			return false;
		}
	}
	
	
	/**
	 * @see PaperSizeInterface::list_entries()
	 * @return array
	 */
	public static function list_entries()
	{
		return PaperSize_Access::list_entries();
	}
	
	/**
	 * @see PaperSizeInterface::get_size_by_id()
	 * @param integer $id
	 * @return array
	 */
	public static function get_size_by_id($id)
	{
		return PaperSize_Access::get_size_by_id($id);
	}
	
	/**
	 * @see PaperSizeInterface::get_standard_size()
	 * @param integer $id
	 * @return array
	 */
	public static function get_standard_size()
	{
		return PaperSize_Access::get_standard_size();
	}
}