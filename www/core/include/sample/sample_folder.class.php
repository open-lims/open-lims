<?php
/**
 * @package sample
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
 * 
 */
if (constant("UNIT_TEST") == false or !defined("UNIT_TEST"))
{
	require_once("events/sample_folder_create_event.class.php");
	
	require_once("access/sample_has_folder.access.php");
}

/**
 * Sample Folder Class
 * @package sample
 */
class SampleFolder extends Folder implements ConcreteFolderCaseInterface
{
  	private $sample_folder;
	private $sample_id;
	
	private $ci_sample_id;
  	
  	/**
  	 * @param integer $folder_id
  	 */
	function __construct($folder_id)
	{
		global $user;
		
		if (is_numeric($folder_id))
  		{  			
  			parent::__construct($folder_id);
  			$this->sample_folder = new SampleHasFolder_Access($folder_id);
  			$this->sample_id = $this->sample_folder->get_sample_id();
  			
  			if ($this->sample_id)
  			{
  				$sample_security = new SampleSecurity($this->sample_id);
  				
  				if ($this->get_automatic == false)
  				{
  					$permission_bin = decbin($this->get_permission());
					$permission_bin = str_pad($permission_bin, 16, "0", STR_PAD_LEFT);
					$permission_bin = strrev($permission_bin);		
  				}
  				
				
  				// Read-Access
  				if ($this->get_automatic() == true)
  				{
  					if ($sample_security->is_access(1, false))
					{
						$this->read_access = true;
					}
					else
					{
						$this->read_access = false;
					}
  				}
  				else
  				{
	  				if ($permission_bin{8} == "1" and $sample_security->is_access(1, false))
					{
						$this->read_access = true;
					}
					else
					{
						$this->read_access = false;
					}
  				}

  				
				// Write-Access
  				if ($this->get_automatic() == true)
  				{
  					if ($sample_security->is_access(2, false))
					{
						$this->write_access = true;
					}
					else
					{
						$this->write_access = false;
					}
  				}
  				else
  				{
	  				if ($permission_bin{9} == "1" and $sample_security->is_access(2, false))
					{
						$this->write_access = true;
					}
					else
					{
						$this->write_access = false;
					}
  				}
  				
  				
  				// Delete-Access
  				if ($user->is_admin() == true)
  				{
  					if ($sample_security->is_access(5, false))
					{
						$this->delete_access = true;
					}
					else
					{
						$this->delete_access = false;
					}
  				}
  				else
  				{
	  				if ($permission_bin{10} == "1" and $user->is_admin() == true)
					{
						$this->delete_access = true;
					}
					else
					{
						$this->delete_access = false;
					}
  				}

  				
  				// Control-Access
  				if ($user->is_admin() == true)
  				{
  					if ($sample_security->is_access(7, false))
					{
						$this->control_access = true;
					}
					else
					{
						$this->control_access = false;
					}
  				}
  				else
  				{
	  				if ($permission_bin{11} == "1" and $user->is_admin() == true)
					{
						$this->control_access = true;
					}
					else
					{
						$this->control_access = false;
					}
  				}
  			}
  		}
  		else
  		{
  			parent::__construct(null);
  			$this->sample_folder = null;
  			$this->sample_id = null;
  		}
  	}
  	
	function __destruct()
	{
		unset($this->sample_folder);
		unset($this->sample_id);
		parent::__destruct();
	}
	
	/**
	 * @return bool
	 */
	protected function get_inherit_permission()
	{
		return true;
	}
		
	/**
	 * @see DataEntityInterface::can_set_automatic()
	 * @return bool
	 */
	public function can_set_automatic()
	{
		return false;
	}
	
	/**
	 * @see DataEntityInterface::can_set_data_entity()
	 * @return bool
	 */
	public function can_set_data_entity()
	{
		return false;
	}
	
	/**
	 * @see DataEntityInterface::can_set_control()
	 * @return bool
	 */
	public function can_set_control()
	{
		return false;
	}
	
	/**
	 * @see DataEntityInterface::can_set_remain()
	 * @return bool
	 */
	public function can_set_remain()
	{
		return false;
	}

	/**
	 * @see FolderInterface::can_add_folder()
	 * @param bool $inherit
	 * @return bool
	 */
	public function can_change_permission($inherit = false)
	{
		return true;
	}
	
	/**
	 * @see FolderInterface::can_add_folder()
	 * @param bool $inherit
	 * @return bool
	 */
	public function can_add_folder($inherit = false)
	{
		return false;
	}
	
	/**
	 * @see FolderInterface::can_command_folder()
	 * @param bool $inherit
	 * @return bool
	 */
	public function can_command_folder($inherit = false)
	{
		if ($inherit == true)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * @see FolderInterface::can_rename_folder()
	 * @param bool $inherit
	 * @return bool
	 */
	public function can_rename_folder($inherit = false)
	{
		return false;
	}
	
	/**
	 * Creates a new Sample Folder including Folder
	 * @return integer
	 */
	public function create()
	{
		if (is_numeric($this->ci_sample_id))
		{
			$sample = new Sample($this->ci_sample_id);
			
			// Folder
			$sample_folder_id = constant("SAMPLE_FOLDER_ID");
			$folder = new Folder($sample_folder_id);

			$path = new Path($folder->get_path());
			$path->add_element($this->ci_sample_id);

			$name = $sample->get_name()." (".$sample->get_formatted_id().")";
			
			parent::ci_set_name($name);
			parent::ci_set_toid($sample_folder_id);
			parent::ci_set_path($path->get_path_string());
			parent::ci_set_owner_id($sample->get_owner_id());
			if (($folder_id = parent::create()) != null)
			{
				$sample_has_folder_access = new SampleHasFolder_Access(null);
				if ($sample_has_folder_access->create($this->ci_sample_id, $folder_id) == null)
				{
					return null;
				}
				
				// Virtual Folders (Event)
				$sample_folder_create_event = new SampleFolderCreateEvent($folder_id);
				$event_handler = new EventHandler($sample_folder_create_event);
				
				if ($event_handler->get_success() == false)
				{
					$this->delete();
					return false;
				}
				else
				{
					return $folder_id;
				}
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
	 * Injects $sample_id into create()
	 * @param integer $sample_id
	 */
	public function ci_set_sample_id($sample_id)
	{
		$this->ci_sample_id = $sample_id;
	}
	
	/**
	 * @see ConcreteFolderCaseInterface::delete()
	 * @return bool
	 */
	public function delete()
	{
		global $transaction;
		
		if ($this->sample_id)
		{
			$transaction_id = $transaction->begin();
			
			if ($this->sample_folder->delete() == true)
			{
				if (parent::delete() == true)
				{
					$transaction->commit($transaction_id);
					return true;
				}
				else
				{
					$transaction->rollback($transaction_id);
					return false;
				}
			}
			else
			{
				$transaction->rollback($transaction_id);
				return false;
			}
		}
		else
		{
			return false;
		}
	}
	
	
	/**
	 * @see ConcreteFolderCaseInterface::is_case()
	 * @param integer $folder_id
	 * @return bool
	 */
	public static function is_case($folder_id)
	{
		if (is_numeric($folder_id))
		{
			$sample_has_folder_access = new SampleHasFolder_Access($folder_id);
			if ($sample_has_folder_access->get_sample_id())
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * @param integer $sample_id
	 * @return integer
	 */
	public static function get_folder_by_sample_id($sample_id)
	{
		return SampleHasFolder_Access::get_entry_by_sample_id($sample_id);
	}

	/**
	 * @param integer $folder_id
	 * @return integer
	 */
	public static function get_sample_id_by_folder_id($folder_id)
	{
		return SampleHasFolder_Access::get_sample_id_by_folder_id($folder_id);
	}
}
?>