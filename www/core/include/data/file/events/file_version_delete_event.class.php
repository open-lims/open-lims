<?php
/**
 * @package data
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
 * File Upload Event
 * @package data
 */
class FileDeleteEvent extends DeleteEvent
{
	private $file_version_id;    
	private $folder_id;
	private $filesize;
	
	function __construct($file_version_id, $folder_id, $filesize)
    {
    	if (is_numeric($file_version_id) and is_numeric($folder_id) and is_numeric($filesize))
    	{
    		parent::__construct();
    		$this->file_version_id = $file_version_id;
    		$this->folder_id = $folder_id;
    		$this->filesize = $filesize;
    	}
    	else
    	{
    		$this->file_version_id = null;
    		$this->folder_id = null;
    		$this->filesize = null;
    	}
    }
    
 	public function get_file_version_id()
    {
    	if ($this->file_version_id)
    	{
    		return $this->file_version_id;
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
    
	public function get_filesize()
    {
    	if ($this->filesize)
    	{
    		return $this->filesize;
    	}
    	else
    	{
    		return null;
    	}
    }
}

?>