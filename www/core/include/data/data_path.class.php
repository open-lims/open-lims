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
require_once("interfaces/data_path.interface.php");

/**
 * Data Path Class
 * @package data
 */
class DataPath implements DataPathInterface
{
	private $path;
	private $path_stack_array = array();
	
	private $folder_id;
	private $virtual_folder_id;

    function __construct($folder_id, $virtual_folder_id)
    {
    	global $session, $user;
    	
    	if ($folder_id xor $virtual_folder_id)
    	{
    		if ($folder_id)
    		{			
    			$folder = Folder::get_instance($folder_id);
    			$this->path = $folder->get_object_path();
    			
    			$this->folder_id = $folder_id;
    			$this->virtual_folder_id = null;
    			
    			if ($session->is_value("stack_array"))
    			{
	    			$this->path_stack_array = $session->read_value("stack_array");
	    			$this->push_folder_id($folder_id);
	    			$this->clear_stack();
	    		}
	    		else
	    		{
	    			$this->init_stack($folder_id);
	    		}
    		}
    		else
    		{
    			$this->folder_id = null;
    			$this->virtual_folder_id = $virtual_folder_id;
    			
    			if ($session->is_value("stack_array"))
    			{
	    			$this->path_stack_array = $session->read_value("stack_array");
	    			$this->push_virtual_folder_id($virtual_folder_id);
	    			$this->clear_stack();
	    		}
	    		else
	    		{
	    			// Exception
	    		}
    		}
    	}
    	elseif(!$folder_id and !$virtual_folder_id)
    	{
    		if ($session->is_value("stack_array"))
    		{
    			$this->path_stack_array = $session->read_value("stack_array");
    			$this->clear_stack();
    		}
    		else
    		{
		    	$folder_id = UserFolder::get_folder_by_user_id($user->get_user_id());
		    	$folder = Folder::get_instance($folder_id);
		    	$this->init_stack($folder_id);
	    		$this->path = $folder->get_object_path();
	    		
	    		$this->folder_id = $folder_id;
	    		$this->virtual_folder_id = null;
    		}
    	}
    	else
    	{
    		// Exception
    	}
    }
    
    function __destruct()
    {
    	unset($this->path);
    	unset($this->path_stack_array);
    	unset($this->folder_id);
    	unset($this->virtual_folder_id);
    }
    
    /**
     * Removes self-references of folder-stack
     */
    private function clear_stack()
    {
		global $session;

		$current_path_stack_array = $this->path_stack_array;
		$cleared_path_stack_array = array();
		$break = false;
		
		foreach($current_path_stack_array as $key => $value)
		{
			if ($break == false)
			{
				$temp_array = array();
				$temp_array[id] = $value[id];
				$temp_array[virtual] = $value[virtual];
				array_push($cleared_path_stack_array, $temp_array);
			}
			
			if ($value[virtual] == true and $value[id] == $this->virtual_folder_id) 
			{
				$break = true;
			}
			
			if ($value[virtual] == false and $value[id] == $this->folder_id)
			{
				$break = true;
			}
		}		
    	
    	$this->path_stack_array = $cleared_path_stack_array;
    	$session->write_value("stack_array", $this->path_stack_array, true);
    }
    
    /**
     * Checks, of the stack contains a virtual-folder. If not, the stack will be ne initialised
     */
    private function check_stack()
    {
    	$virtual_folder_found = false;
    	$last_folder_id = null;
    	
    	foreach($this->path_stack_array as $key => $value)
    	{
    		if ($value[virtual] == true)
    		{
    			$virtual_folder_found = true;
    		}
    		$last_folder_id = $value[id];
    	}
    	
    	if ($virtual_folder_found == true)
    	{
    		return true;
    	}
    	else
    	{
    		$this->init_stack($last_folder_id);
    		return false;
    	}
    }
    
    /**
     * Initialises the stack with given folder_id
     * @param integer $folder_id
     */
   	private function init_stack($folder_id)
   	{ 	
    	global $session;
    	
    	$this->path_stack_array = array();
    	
    	$folder = Folder::get_instance($folder_id);
    	$init_array = $folder->get_object_id_path();
    	
    	if (is_array($init_array) and count($init_array) >= 1)
    	{
	    	foreach($init_array as $key => $value)
	    	{
	    		$temp_array = array();
	    		$temp_array[virtual] = false;
	    		$temp_array[id] = $value;
	    		array_unshift($this->path_stack_array, $temp_array);
	    	}
    	}
		
		$session->write_value("stack_array", $this->path_stack_array, true);
   	}
   	
   	/**
   	 * Puts a new folder on the stack
   	 * @param $folder_id
   	 */
   	private function push_folder_id($folder_id)
   	{
    	global $session;
    	
    	$cut = false;
    	
    	foreach($this->path_stack_array as $key => $value)
    	{
    		if ($value[id] == $folder_id and $value[virtual] == false)
    		{
    			$cut = true;
    		}
    	}
    	
    	if ($cut == false)
    	{
			$temp_array = array();
	    	$temp_array[virtual] = false;
	    	$temp_array[id] = $folder_id;
	    	array_push($this->path_stack_array, $temp_array);
    	}
    	
    	$session->write_value("stack_array", $this->path_stack_array, true);
   	}
   	
    /**
   	 * Puts a new virtual-folder on the stack
   	 * @param $virtual_folder_id
   	 */
   	private function push_virtual_folder_id($virtual_folder_id)
   	{
   		global $session;
   		
   		$cut = false;
   		
   		foreach($this->path_stack_array as $key => $value)
   		{
    		if ($value[id] == $virtual_folder_id and $value[virtual] == true)
    		{
    			$cut = true;
    		}
    	}
    	
    	if ($cut == false)
    	{
	   		$temp_array = array();
	    	$temp_array[virtual] = true;
	    	$temp_array[id] = $virtual_folder_id;
	    	array_push($this->path_stack_array, $temp_array);
    	}
   		
   		$session->write_value("stack_array", $this->path_stack_array, true);
   	}
   	
   	/**
   	 * Initialises the stack with given project_id
   	 * @todo extrat method from class due to loose dependency
   	 * @param $project_id
   	 */
   	public function init_project_folder($project_id)
   	{
    	global $session;
    	
    	$this->path_stack_array = array();
    	
    	$folder_id = ProjectFolder::get_folder_by_project_id($project_id);
    	$folder = Folder::get_instance($folder_id);
    	$init_array = $folder->get_object_id_path();
    	
    	foreach($init_array as $key => $value)
    	{
    		$temp_array = array();
    		$temp_array[virtual] = false;
    		$temp_array[id] = $value;
    		array_unshift($this->path_stack_array, $temp_array);
    	}
		
		$session->write_value("stack_array", $this->path_stack_array, true);
		
		$this->init_stack($folder_id);
		$this->path = $folder->get_object_path();
		
		$this->folder_id = $folder_id;
		$this->virtual_folder_id = null;
   	}
   	
    /**
   	 * Initialises the stack with given sample_id
   	 * @todo extrat method from class due to loose dependency
   	 * @param $sample_id
   	 */
   	public function init_sample_folder($sample_id)
   	{
    	global $session;
    	
    	$this->path_stack_array = array();
    	
    	$folder_id = SampleFolder::get_folder_by_sample_id($sample_id);
    	$folder = Folder::get_instance($folder_id);
    	$init_array = $folder->get_object_id_path();
    	
    	foreach($init_array as $key => $value)
    	{
    		$temp_array = array();
    		$temp_array[virtual] = false;
    		$temp_array[id] = $value;
    		array_unshift($this->path_stack_array, $temp_array);
    	}
		
		$session->write_value("stack_array", $this->path_stack_array, true);
		
		$this->init_stack($folder_id);
		$this->path = $folder->get_object_path();
		
		$this->folder_id = $folder_id;
		$this->virtual_folder_id = null;	
   	}
   	
   	/**
   	 * @return string
   	 */
   	public function get_path()
   	{
   		if ($this->path != null)
   		{
    		return $this->path;
    	}
    	else
    	{
    		return null;
    	}
   	}
   	
   	/**
   	 * Returns the path
   	 * @return string
   	 */
   	public function get_stack_path() {
		if ($this->check_stack() == true or $this->path == null)
		{
			$return_string = "";
	    	foreach ($this->path_stack_array as $key => $value)
	    	{
	    		if ($value[virtual] == false)
	    		{
	    			$folder = Folder::get_instance($value[id]);
	    			$return_string = $return_string."/".$folder->get_name();
	    		}
	    		else
	    		{
	    			$virtual_folder = new VirtualFolder($value[id]);
	    			$return_string = $return_string."/<span class='underline'>".$virtual_folder->get_name()."</span>";
	    		}
	    	}
    		return $return_string;
		}
		else
		{
			return $this->get_path();
		}
   	}
   	
   	/**
   	 * Removes last two entries of the stack an returns the virtual-flag from the first of them
   	 * @return bool
   	 */
   	public function get_previous_entry_virtual()
   	{
   		$current_path_stack_array = $this->path_stack_array;
   		array_pop($current_path_stack_array);
   		$previous_entry = array_pop($current_path_stack_array);
   		
   		if ($previous_entry != null and is_array($previous_entry))
   		{
   			return $previous_entry[virtual];
   		}
   		else
   		{
   			return false;
   		}
   	}
   	
    /**
   	 * Removes last two entries of the stack an returns the first of them
   	 * @return integer
   	 */
   	public function get_previous_entry_id()
   	{
   		$current_path_stack_array = $this->path_stack_array;
   		array_pop($current_path_stack_array);
   		$previous_entry = array_pop($current_path_stack_array);
   		
   		if ($previous_entry != null and is_array($previous_entry))
   		{
   			return $previous_entry[id];
   		}
   		else
   		{
   			return 1;
   		}
   	}
	
	/**
	 * Returns the type of the last entry.
	 * @return bool
	 */
	public function get_last_entry_type()
	{	
		$current_path_stack_array = $this->path_stack_array;
   		$previous_entry = array_pop($current_path_stack_array);
   		array_push($current_path_stack_array, $previous_entry);
   		
   		if ($previous_entry != null and is_array($previous_entry))
   		{
   			return $previous_entry[virtual];
   		}
   		else
   		{
   			return 1;
   		}
	}
	
	/**
	 * Returns the id of the last entry.
	 * @return bool
	 */
	public function get_last_entry_id()
	{
		$current_path_stack_array = $this->path_stack_array;
   		$previous_entry = array_pop($current_path_stack_array);
   		array_push($current_path_stack_array, $previous_entry);
   		
   		if ($previous_entry != null and is_array($previous_entry))
   		{
   			return $previous_entry[id];
   		}
   		else
   		{
   			return 1;
   		}
	}	
	
    /**
	 * Deletes the stack
	 */
    public function delete_stack()
    {
    	global $session;
    	$session->delete_value("stack_array");
    }
    
}
?>