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
require_once("interfaces/virtual_folder.interface.php");

if (constant("UNIT_TEST") == false or !defined("UNIT_TEST"))
{
	require_once("access/virtual_folder.access.php");
	require_once("access/virtual_folder_has_folder.access.php");
	require_once("access/virtual_folder_is_project.access.php");
	require_once("access/virtual_folder_is_sample.access.php");
}

/**
 * Virtual Folder Management Class
 * @package data
 */
class VirtualFolder implements VirtualFolderInterface
{
	private $virtual_folder_id;
	private $virtual_folder;
	
	/**
	 * @param integer $virtual_folder_id
	 */
	function __construct($virtual_folder_id)
	{
		if ($virtual_folder_id == null)
		{
			$this->virtual_folder_id 	= null;
			$this->virtual_folder 		= new VirtualFolder_Access(null);
		}
		else
		{				
			$this->virtual_folder_id 	= $virtual_folder_id;
			$this->virtual_folder		= new VirtualFolder_Access($virtual_folder_id);
		}
	}
	
	function __destruct()
	{
		if ($this->virtual_folder_id)
		{
			unset($this->virtual_folder_id);
			unset($this->virtual_folder);
		}
	}
	
	/**
	 * Creates a new Virtual-Folder in a given Folder
	 * @param integer $folder_id
	 * @param string $name
	 * @return integer
	 */
	public function create($folder_id, $name)
	{
		global $transaction;
		
		if (is_numeric($folder_id) and $name)
		{
			$transaction_id = $transaction->begin();
			
			$folder = Folder::get_instance($folder_id);
			
			if ($folder->exist_folder() == true)
			{
				if (($vfolder_id = $this->virtual_folder->create($folder_id, $name)) != null)
				{
					$this->__construct($vfolder_id);
					if ($transaction_id != null)
					{
						$transaction->commit($transaction_id);
					}
					return $vfolder_id;	
				}
				else
				{
					if ($transaction_id != null)
					{
						$transaction->rollback($transaction_id);
					}
					return null;
				}
			}
			else
			{
				if ($transaction_id != null)
				{
					$transaction->rollback($transaction_id);
				}
				return null;
			}
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * Deletes a Virtual-Folder
	 * @return bool
	 */
	public function delete()
	{
		global $transaction;
		
		if ($this->virtual_folder_id and $this->virtual_folder)
		{
			$transaction_id = $transaction->begin();
			
			$linked_folder_array = VirtualFolderHasFolder_Access::list_entries_by_virtual_folder_id($this->virtual_folder_id);
			
			if (is_array($linked_folder_array) and count($linked_folder_array) >= 1)
			{
				foreach($linked_folder_array as $key => $value)
				{
					$virtual_folder_has_folder = new VirtualFolderHasFolder_Access($value);
					if ($virtual_folder_has_folder->delete() == false)
					{
						if ($transaction_id != null)
						{
							$transaction->rollback($transaction_id);
						}
						return false;
					}
				}
			}
			
			if ($this->is_project_vfolder() == true)
			{
				$virtual_folder_is_project = new VirtualFolderIsProject_Access($this->virtual_folder_id);
				if ($virtual_folder_is_project->delete() == false)
				{
					if ($transaction_id != null)
					{
						$transaction->rollback($transaction_id);
					}
					return false;
				}
			}
			
			if ($this->is_sample_vfolder() == true)
			{
				$virtual_folder_is_sample = new VirtualFolderIsSample_Access($this->virtual_folder_id);
				if ($virtual_folder_is_sample->delete() == false)
				{
					if ($transaction_id != null)
					{
						$transaction->rollback($transaction_id);
					}
					return false;
				}
			}
			
			if ($this->virtual_folder->delete() == true)
			{
				if ($transaction_id != null)
				{
					$transaction->commit($transaction_id);
				}
				return true;
			}
			else
			{
				if ($transaction_id != null)
				{
					$transaction->rollback($transaction_id);
				}
				return false;
			}
		}
		else
		{
			return false;
		}	
	}
	
	/**
	 * Adds a given Folder to the current Virtual-Folder
	 * @param integer $folder_id
	 * @return bool
	 */
	public function link_folder($folder_id)
	{
		global $transaction;
		
		if (is_numeric($folder_id))
		{
			$transaction_id = $transaction->begin();
			
			$entry_exists = false;
			
			$folder_array = VirtualFolderHasFolder_Access::list_entries_by_virtual_folder_id($this->virtual_folder_id);
			
			if (is_array($folder_array))
			{
				foreach ($folder_array as $key => $value)
				{
					$virtual_folder_has_folder = new VirtualFolderHasFolder_Access($value);
					if ($virtual_folder_has_folder->get_folder_id() == $folder_id)
					{
						$entry_exists = true;
					}
				}
			}
			
			if ($entry_exists == false)
			{			
				$virtual_folder_has_folder = new VirtualFolderHasFolder_Access(null);
				$return_value = $virtual_folder_has_folder->create($this->virtual_folder_id, $folder_id);
				
				if ($return_value != null)
				{
					if ($transaction_id != null)
					{
						$transaction->commit($transaction_id);
					}
					return true;
				}
				else
				{
					if ($transaction_id != null)
					{
						$transaction->rollback($transaction_id);
					}
					return false;
				}
			}
			else
			{
				return true;
			}
		}
	}
	
	/**
	 * Removes a given Folder from the current Virtual-Folder
	 * @param integer $folder_id
	 * @return bool
	 */
	public function unlink_folder($folder_id)
	{
		global $transaction;
		
		if (is_numeric($folder_id))
		{
			$transaction_id = $transaction->begin();
		
			$entry_exists = false;
			
			$folder_array = VirtualFolderHasFolder_Access::list_entries_by_virtual_folder_id($this->virtual_folder_id);
			
			if (is_array($folder_array))
			{
				foreach ($folder_array as $key => $value)
				{
					$virtual_folder_has_folder = new VirtualFolderHasFolder_Access($value);
					if ($virtual_folder_has_folder->get_folder_id() == $folder_id)
					{
						if ($virtual_folder_has_folder->delete())
						{
							if ($transaction_id != null)
							{
								$transaction->commit($transaction_id);
							}
							return true;
						}
						else
						{
							if ($transaction_id != null)
							{
								$transaction->rollback($transaction_id);
							}
							return false;
						}
					}
				}
				return true;
			}
			else
			{
				if ($transaction_id != null)
				{
					$transaction->commit($transaction_id);
				}
				return true;
			}
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * @todo extrat method from class due to loose dependency
	 * @return bool
	 */
	public function is_project_vfolder()
	{
		if ($this->virtual_folder_id and $this->virtual_folder)
		{
			return VirtualFolderIsProject_Access::is_entry($this->virtual_folder_id);
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * @todo extrat method from class due to loose dependency
	 * @return bool
	 */
	public function is_sample_vfolder()
	{
		if ($this->virtual_folder_id and $this->virtual_folder)
		{
			return VirtualFolderIsSample_Access::is_entry($this->virtual_folder_id);
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * Returns an array of all folders in the current Virtual-Folder
	 * @return array
	 */
	public function get_subfolder_array()
	{
		if ($this->virtual_folder_id and $this->virtual_folder)
		{
			$return_array = array();
			
			$folder_array = VirtualFolderHasFolder_Access::list_entries_by_virtual_folder_id($this->virtual_folder_id);
			
			if (is_array($folder_array) and count($folder_array) > 0)
			{
				$counter = 0;
				foreach($folder_array as $key => $value)
				{
					$virtual_folder_has_folder = new VirtualFolderHasFolder_Access($value);
					
					$return_array[$counter][id]		= $virtual_folder_has_folder->get_folder_id();
					$return_array[$counter][type]	= 0;
					
					$counter++;
				}
			}
											
			if (is_array($return_array) and count($return_array) > 0)
			{
				return $return_array;
			}
			else
			{
				return null;
			}
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return string
	 */
	public function get_name()
	{
		if ($this->virtual_folder_id and $this->virtual_folder)
		{
			return $this->virtual_folder->get_name();
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return string
	 */
	public function get_datetime()
	{
		if ($this->virtual_folder_id and $this->virtual_folder)
		{
			return $this->virtual_folder->get_datetime();
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @todo extrat method from class due to loose dependency
	 * @return bool
	 */
	public function set_project_vfolder()
	{
		if ($this->virtual_folder_id and $this->virtual_folder)
		{
			$virtual_folder_is_project = new VirtualFolderIsProject_Access(null);
			return $virtual_folder_is_project->create($this->virtual_folder_id);	
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * @todo extrat method from class due to loose dependency
	 * @return bool
	 */
	public function set_sample_vfolder()
	{
		if ($this->virtual_folder_id and $this->virtual_folder)
		{
			$virtual_folder_is_sample = new VirtualFolderIsSample_Access(null);
			return $virtual_folder_is_sample->create($this->virtual_folder_id);	
		}
		else
		{
			return false;
		}
	}
	
	
	/**
	 * @param integer $virtual_folder_id
	 * @return bool
	 */
	public static function exist_vfolder($virtual_folder_id)
	{
		if (is_numeric($virtual_folder_id))
		{
   			return VirtualFolder_Access::exist_virtual_folder_by_virtual_folder_id($virtual_folder_id);
   		}
   		else
   		{
   			return false;
   		}
	}
	
	/**
	 * @param integer $folder_id
	 * @return array
	 */
	public static function list_entries_by_folder_id($folder_id)
	{
		return VirtualFolder_Access::list_entries_by_folder_id($folder_id);
	}
	
}

?>