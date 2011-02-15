<?php
/**
 * @package sample
 * @version 0.4.0.0
 * @author Roman Konertz
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
  	
  	/**
  	 * @param integer $folder_id
  	 */
	function __construct($folder_id)
	{
		if (is_numeric($folder_id))
  		{
  			parent::__construct($folder_id);
  			$this->sample_folder = new SampleHasFolder_Access($folder_id);
  			$this->sample_id = $this->sample_folder->get_sample_id();
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
	public function is_read_access()
	{
		return parent::is_read_access();
	}
	
	/**
	 * @return bool
	 */
	public function is_write_access()
	{
		return parent::is_write_access();
	}
	
	/**
	 * @return bool
	 */
	public function is_delete_access()
	{
		return parent::is_delete_access();
	}
	
	/**
	 * @return bool
	 */
	public function is_control_access()
	{
		return parent::is_control_access();
	}
	
	/**
	 * @return bool
	 */
	public function is_flag_change_permission()
	{
		return parent::is_flag_change_permission();
	}
	
	/**
	 * @return bool
	 */
	public function is_flag_add_folder()
	{
		return parent::is_flag_add_folder();
	}
	
	/**
	 * @return bool
	 */
	public function is_flag_cmd_folder()
	{
		return parent::is_flag_cmd_folder();
	}
	
	/**
	 * @return bool
	 */
	public function is_flag_rename_folder()
	{
		return parent::is_flag_rename_folder();
	}
	
	/**
	 * Creates a new Sample Folder including Folder
	 * @param integer $sample_id
	 * @return integer
	 * @todo: remove v-folder
	 */
	public function create($sample_id)
	{
		if (is_numeric($sample_id))
		{
			$sample = new Sample($sample_id);
			
			// Folder
			$sample_folder_id = $GLOBALS[sample_folder_id];
			$folder = new Folder($sample_folder_id);

			$path = new Path($folder->get_path());
			$path->add_element($sample_id);
			
			if (($folder_id = parent::create($sample->get_name(), $sample_folder_id, $path->get_path_string(), $sample->get_owner_id(), null)) != null)
			{
				$sample_has_folder_access = new SampleHasFolder_Access(null);
				if ($sample_has_folder_access->create($sample_id, $folder_id) == null)
				{
					return null;
				}
				if ($this->set_flag(32) == false)
				{
					$this->delete(true, true);
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
	
	// Wird über konkretisierung automatisch über Folder ausgeführt,
	// kann aber auch direkt ausgeführt werden (wenn Klasse bekannt)
	/**
	 * @param bool $recursive
	 * @param bool $content
	 * @return bool
	 */
	public function delete($recursive, $content)
	{
		global $transaction;
		
		if ($this->sample_id)
		{
			$transaction_id = $transaction->begin();
			
			if ($this->sample_folder->delete() == true)
			{
				if (parent::delete($recursive, $content) == true)
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
	 * Checks if $folder_id is a case of Sample Folder
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
	
	public static function get_folder_by_sample_id($sample_id)
	{
		return SampleHasFolder_Access::get_entry_by_sample_id($sample_id);
	}
}
?>