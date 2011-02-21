<?php
/**
 * @package data
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
 * 
 */
require_once("interfaces/data_browser.interface.php");

if (constant("UNIT_TEST") == false or !defined("UNIT_TEST"))
{
	require_once("access/data_join.access.php");
}

/**
 * Data Browser Class
 * @package data
 */
class DataBrowser implements DataBrowserInterface
{
	private static $folder_id;
    
    /**
     * Returns an array of folder or virtual-folder content
     * @param integer $folder_id
     * @param integer $virtual_folder_id
     * @return array
     */
    public static function get_data_browser_array($folder_id, $virtual_folder_id, $order_by, $order_method, $start, $end)
    {
    	global $user;
    	
    	if (($folder_id and !$virtual_folder_id) or (!$folder_id and !$virtual_folder_id))
    	{	    	
	    	if ($folder_id == null)
	    	{
	    		$new_folder_id = UserFolder::get_folder_by_user_id($user->get_user_id());
	    		if ($new_folder_id != null)
	    		{
	    			self::$folder_id = $new_folder_id;
	    		}
	    		else
	    		{
	    			// Exception
	    		}
	    	}
	    	else
	    	{
	    		self::$folder_id = $folder_id;
	    	}
	    	
	    	$folder = Folder::get_instance(self::$folder_id);
	    	return DataJoin_Access::list_data_entity_childs($folder->get_data_entity_id(), $order_by, $order_method, $start, $end);
    	}
    	elseif(!$folder_id and $virtual_folder_id)
    	{
    		$virtual_folder = new VirtualFolder($virtual_folder_id);
    		return DataJoin_Access::list_data_entity_childs($virtual_folder->get_data_entity_id(), $order_by, $order_method, $start, $end);
    	}
    	else
    	{
    		// Exception
    	}	
    }
    
    public static function count_data_browser_array($folder_id, $virtual_folder_id)
    {
    	global $user;
    	
    	if (($folder_id and !$virtual_folder_id) or (!$folder_id and !$virtual_folder_id))
    	{	    	
	    	if ($folder_id == null)
	    	{
	    		$new_folder_id = UserFolder::get_folder_by_user_id($user->get_user_id());
	    		if ($new_folder_id != null)
	    		{
	    			self::$folder_id = $new_folder_id;
	    		}
	    		else
	    		{
	    			// Exception
	    		}
	    	}
	    	else
	    	{
	    		self::$folder_id = $folder_id;
	    	}
	    	
	    	$folder = Folder::get_instance(self::$folder_id);
	    	return DataJoin_Access::count_list_data_entity_childs($folder->get_data_entity_id(), $order_by, $order_method, $start, $end);
    	}
    	elseif(!$folder_id and $virtual_folder_id)
    	{
    		$virtual_folder = new VirtualFolder($virtual_folder_id);
    		return DataJoin_Access::count_list_data_entity_childs($virtual_folder->get_data_entity_id(), $order_by, $order_method, $start, $end);
    	}
    	else
    	{
    		// Exception
    	}
    }

    /**
     * @return integer
     */
    public function get_folder_id()
       {
    	if ($this->folder_id != null)
    	{
    		return $this->folder_id;
    	}
    	else
    	{
    		return null;
    	}
    }
    
    /**
     * @param integer $folder_id
     * @return array
     */
    public static function get_image_browser_array($folder_id)
    {
    	if (is_numeric($folder_id))
    	{
	    	return DataJoin_Access::get_images_in_folder($folder_id);
    	}
    	else
    	{
    		return null;
    	}
    }
 
}
?>