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
	private $folder_id;
    
    /**
     * Returns an array of folder or virtual-folder content
     * @param integer $folder_id
     * @param integer $virtual_folder_id
     * @return array
     */
    public function get_data_browser_array($folder_id, $virtual_folder_id)
    {
    	global $user;
    	
    	if (($folder_id and !$virtual_folder_id) or (!$folder_id and !$virtual_folder_id))
    	{	    	
	    	if ($folder_id == null)
	    	{
	    		$new_folder_id = Folder::get_home_folder_by_user_id($user->get_user_id());
	    		if ($new_folder_id != null)
	    		{
	    			$this->folder_id = $new_folder_id;
	    		}
	    		else
	    		{
	    			// Exception
	    		}
	    	}
	    	else
	    	{
	    		$this->folder_id = $folder_id;
	    	}
	    	
	    	$return_array = array();
	    	
	    	// Folder
	    	$folder = new Folder($this->folder_id);
	    	$folder_array = $folder->get_subfolder_array();
	    	
	    	if (is_array($folder_array))
	    	{
	    		$return_array[1] = $folder_array;
	    	}
	    	
	    	// Files
	    	$file_array = Object::get_file_array($this->folder_id);
	    	if (is_array($file_array))
	    	{
	    		$return_array[2] = $file_array;
	    	}
	    	
	    	// Value
	    	$value_array = Object::get_value_array($this->folder_id);
	    	if (is_array($value_array))
	    	{
	    		$return_array[3] = $value_array;
	    	}
	    	
	    	return $return_array;
    	}
    	elseif(!$folder_id and $virtual_folder_id)
    	{
    		$virtual_folder = new VirtualFolder($virtual_folder_id);
    		
    		$virtual_folder_array = $virtual_folder->get_subfolder_array();
    		
    		if (is_array($virtual_folder_array))
    		{
    			$return_array[1] = $virtual_folder_array;
    		}

    		return $return_array;	
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