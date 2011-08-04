<?php
/**
 * @package project
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
 * Project Item Link Event
 * @package project
 */
class ProjectItemLinkEvent extends Event
{    
	private $item_id;
	private $folder_id;
	
	function __construct($item_id, $folder_id)
    {
    	if (is_numeric($folder_id) and is_numeric($item_id))
    	{
    		parent::__construct();
    		$this->item_id = $item_id;
    		$this->folder_id = $folder_id;
    	}
    	else
    	{
    		$this->item_id = null;
    		$this->folder_id = null;
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
    
    public function get_folder_id()
    {
    	if ($this->folder_id)
    	{
    		return $this->folder_id;
    	}
    	else
    	{
    		return null;
    	}
    }
}

?>