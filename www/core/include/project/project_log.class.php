<?php
/**
 * @package project
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
require_once("interfaces/project_log.interface.php");

if (constant("UNIT_TEST") == false or !defined("UNIT_TEST"))
{
	require_once("access/project_log.access.php");
	require_once("access/project_log_has_project_status.access.php");
}

/**
 * Project Item Management Class
 * @package project
 */
class ProjectLog implements ProjectLogInterface, EventListenerInterface
{
	private $log_id;
	private $log;

	/**
	 * @param integer $log_id
	 */
	function __construct($log_id)
	{
		if ($log_id)
		{
			$this->log_id = $log_id;
			$this->log = new ProjectLog_Access($log_id);
		}
		else
		{
			$this->log_id = null;
			$this->log = new ProjectLog_Access(null);
		}		
	}   
	 
	function __destruct()
	{
		unset($this->log_id);
		unset($this->log);
	} 
	
	/**
	 * Creates a new log-entry
	 * @param integer $project_id
	 * @param string $content
	 * @param bool $cancel
	 * @param bool $important
	 * @param string $action_checksum
	 * @return integer
	 */
	public function create($project_id, $content, $cancel, $important, $action_checksum)
	{
		global $user;
		
		if (is_numeric($project_id))
		{
			$log_id = $this->log->create($project_id, $content, $cancel, $important, $user->get_user_id(), $action_checksum);
			$this->__construct($log_id);
			return $log_id;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * Deletes a log-entry
	 * @return bool
	 */
	public function delete()
	{
		global $transaction;
		
		if ($this->log_id and $this->log)
		{
			$transaction_id = $transaction->begin();
			
			$project_log_has_status_pk = ProjectLogHasProjectStatus_Access::get_entry_by_log_id($this->log_id);
			
			if ($project_log_has_status_pk)
			{
				$project_log_has_status = new ProjectLogHasProjectStatus_Access($project_log_has_status_pk);
				if ($project_log_has_status->delete() == false)
				{
					if ($transaction_id != null)
					{
						$transaction->rollback($transaction_id);
					}
					return false;
				}
			}

			if (ProjectLogHasItem::delete_by_log_id($this->log_id) == false)
			{
				if ($transaction_id != null)
				{
					$transaction->rollback($transaction_id);
				}
				return false;
			}
			
			if ($this->log->delete() == false)
			{
				if ($transaction_id != null)
				{
					$transaction->rollback($transaction_id);
				}
				return false;
			}
			else
			{
				if ($transaction_id != null)
				{
					$transaction->commit($transaction_id);
				}
				$this->__destruct();
				return true;
			}
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * Links a project-status to the current log-entry
	 * @param integer $status_id
	 * @return bool
	 */
	public function link_status($status_id)
	{
		if ($this->log_id and is_numeric($status_id) and ($this->get_status_id() == null)) 
		{
			$project_log_has_status = new ProjectLogHasProjectStatus_Access(null);
			$project_log_has_status_pk = $project_log_has_status->create($this->log_id, $status_id);
			
			if ($project_log_has_status_pk)
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
	 * Returns a given project-status linked to the current log-entry
	 * @return integer
	 */
	public function get_status_id()
	{
		if ($this->log_id)
		{
			$project_log_has_status_pk = ProjectLogHasProjectStatus_Access::get_entry_by_log_id($this->log_id);
			
			if ($project_log_has_status_pk)
			{
				$project_log_has_status = new ProjectLogHasProjectStatus_Access($project_log_has_status_pk);
				return $project_log_has_status->get_status_id();
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
	public function get_datetime()
	{
		if ($this->log_id and $this->log)
		{
			return $this->log->get_datetime();
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return string
	 */
	public function get_content()
	{
		if ($this->log_id and $this->log)
		{
			return $this->log->get_content();
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return bool
	 */
	public function get_cancel()
	{
		if ($this->log_id and $this->log)
		{
			return $this->log->get_cancel();
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return bool
	 */
	public function get_important()
	{
		if ($this->log_id and $this->log)
		{
			return $this->log->get_important();
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return integer
	 */
	public function get_owner_id()
	{
		if ($this->log_id and $this->log)
		{
			return $this->log->get_owner_id();
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @param string $content
	 * @return bool
	 */
	public function set_content($content)
	{
		if ($this->log_id and $this->log and $content)
		{
			return $this->log->set_content($content);
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * @param bool $important
	 * @return bool
	 */
	public function set_important($important)
	{
		if ($this->log_id and $this->log and $important)
		{
			return $this->log->set_important($important);
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * @return array
	 */
	public function list_items()
	{
		if ($this->log_id and $this->log)
		{
			return ProjectLogHasItem::get_items_by_log_id($this->log_id);
		}
		else
		{
			return null;
		}
	}


	/**
	 * @param integer $project_id
	 * @return array
	 */
	public static function list_entries_by_project_id($project_id)
	{
		return ProjectLog_Access::list_entries_by_project_id($project_id);
	}
	
    /**
     * @param object $event_object
     * @return bool
     */
    public static function listen_events($event_object)
    {
    	if ($event_object instanceof UserDeletePrecheckEvent)
    	{
    		$project_log_array = ProjectLog_Access::list_entries_by_owner_id($event_object->get_user_id());
			
			if (is_array($project_log_array))
			{
				if (count($project_log_array) >= 1)
				{
					return false;
				}
			}
    	}
    	
    	return true;
    }
}
?>