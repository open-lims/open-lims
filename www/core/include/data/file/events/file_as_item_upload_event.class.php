<?php
/**
 * @package data
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
 * File As Item Upload Event
 * @package data
 */
class FileAsItemUploadEvent extends Event
{    
	private $item_id;
	private $get_array;
	
	function __construct($item_id, $get_array, $post_array)
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
    	}
    	else
    	{
    		$this->item_id = null;
    		$this->get_array = null;
    		$this->post_array = null;
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
}

?>