<?php
/**
 * @package item
 * @version 0.4.0.0
 * @author Roman Konertz <konertz@open-lims.org>
 * @copyright (c) 2008-2011 by Roman Konertz
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
class ItemAddHolderEvent extends Event
{    
	private $id;
	private $type;
	private $item_id;
	private $gid;
	
	function __construct($id, $type, $item_id, $gid = null)
    {
    	if (is_numeric($id) and $type and is_numeric($item_id))
    	{
    		$this->id = $id;
    		$this->type = $type;
    		$this->item_id = $item_id;
    		$this->gid = $gid;
    	}
    	else
    	{
    		$this->id = null;
    		$this->type = null;
    		$this->item_id = null;
    		$this->gid = null;
    	}
    }
    
	public function get_id()
    {
    	if ($this->id)
    	{
    		return $this->id;
    	}
    	else
    	{
    		return null;
    	}
    }
    
	public function get_type()
    {
    	if ($this->type)
    	{
    		return $this->type;
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
    
	public function get_gid()
    {
    	if ($this->gid)
    	{
    		return $this->gid;
    	}
    	else
    	{
    		return null;
    	}
    }
}

?>