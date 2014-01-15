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
 * Item Add Event
 * @package item
 */
class ItemAddEvent extends Event
{    
	private $item_id;
	private $get_array;
	private $post_array;
	private $item_holder;
	private $item_holder_name;
	
	function __construct($item_id, $get_array, $post_array, $item_holder = false, $item_holder_name = null)
    {
    	if (is_numeric($item_id) and is_array($get_array))
    	{
    		parent::__construct();
    		$this->item_id = $item_id;
    		$this->get_array = $get_array;
    		if (is_array($post_array))
    		{
    			$this->post_array = $post_array;
    		}
    		else
    		{
    			$this->post_array = null;
    		}
    		$this->item_holder = $item_holder;
    		$this->item_holder_name = $item_holder_name;
    	}
    	else
    	{
    		$this->item_id = null;
    		$this->get_array = null;
    		$this->post_array = null;
    		$this->item_holder = false;
    		$this->item_holder_name = null;
    	}
    }
    
    public function get_item_id()
    {
    	if ($this->item_id)
    	{
    		return $this->item_id;
    	}
    	else
    	{
    		return null;
    	}
    }
    
	public function get_get_array()
    {
    	if ($this->get_array)
    	{
    		return $this->get_array;
    	}
    	else
    	{
    		return null;
    	}
    }
    
	public function get_post_array()
    {
    	if ($this->post_array)
    	{
    		return $this->post_array;
    	}
    	else
    	{
    		return null;
    	}
    }
    
	public function get_item_holder()
    {
    	if (isset($this->item_holder))
    	{
    		return $this->item_holder;
    	}
    	else
    	{
    		return null;
    	}
    }
    
	public function get_item_holder_name()
    {
    	if ($this->item_holder_name)
    	{
    		return $this->item_holder_name;
    	}
    	else
    	{
    		return null;
    	}
    }
}

?>