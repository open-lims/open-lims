<?php
/**
 * @package item
 * @version 0.4.0.0
 * @author Roman Konertz <konertz@open-lims.org>
 * @copyright (c) 2008-2016 by Roman Konertz
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
 * Item Holder Add Event
 * Called, if an Item Holder which is an Item too, adds an Item
 * @package item
 */
class ItemHolderAddEvent extends Event
{    
	private $item_holder_id;
	private $parent_item_id;
	private $item_id;
	private $id_array;
	
	function __construct($id_array, $parent_item_id, $item_id, $pos_id)
    {
    	if (is_array($id_array) and is_numeric($parent_item_id) and is_numeric($item_id) and is_numeric($pos_id))
    	{
    		parent::__construct();
    		$this->id_array = $id_array;
    		$this->parent_item_id = $parent_item_id;
    		$this->item_id = $item_id;
    		$this->pos_id = $pos_id;
    	}
    	else
    	{
    		$this->id_array = null;
    		$this->parent_item_id = null;
    		$this->item_id = null;
    		$this->pos_id = null;
    	}
    }
    
 	public function get_id_array()
    {
    	if ($this->id_array)
    	{
    		return $this->id_array;
    	}
    	else
    	{
    		return null;
    	}
    }
    
 	public function get_parent_item_id()
    {
    	if ($this->parent_item_id)
    	{
    		return $this->parent_item_id;
    	}
    	else
    	{
    		return null;
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
    
	public function get_pos_id()
    {
    	if (is_numeric($this->pos_id))
    	{
    		return $this->pos_id;
    	}
    	else
    	{
    		return null;
    	}
    }
}

?>