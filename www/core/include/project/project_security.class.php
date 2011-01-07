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
require_once("interfaces/project_security.interface.php");

/**
 * Project Security Class
 * @package project
 */
class ProjectSecurity implements ProjectSecurityInterface
{
    private $project_id;
    private $user_id;
       
    /**
     * @param integer $project_id
     */ 
    function __construct($project_id)
    {
    	global $user;
    	
    	if (is_numeric($project_id))
    	{
    		$project = new Project($project_id);
			if ($_GET[project_id] != ($master_project_id = $project->get_master_project_id())) {
				$project = new Project($master_project_id);
				$current_project_id = $master_project_id;
			}else{
				$current_project_id = $project_id;
			}
    		$this->project_id = $current_project_id;
    	}
    	else
    	{
    		$this->project_id = null;
    	}
    	$this->user_id = $user->get_user_id();
    }
    
    function __destruct()
    {
    	unset($this->project_id);
    }
    
    /**
     * Checks the permissions of the current user
     * @param integer $itention Intention of the User (Read, Write, etc.)
     * @param integer $ignore_admin_status If it's true, an admin-status of an user will be ignored
     * @return bool
     */
    public function is_access($intention, $ignore_admin_status)
    {
    	global $user;
    	
    	$access_read			= false;
		$access_set_readable	= false;
		$access_write			= false;
		$access_set_writeable	= false;
		$access_reactivate		= false;
		$access_delete			= false;
		$access_set_permissions	= false;
    	
    	if ($user->is_admin() and $ignore_admin_status == false)
    	{
    		return true;
    	}
    	else
    	{
	    	if (is_numeric($intention) and $this->project_id)
	    	{
	    		$project_permission_array = ProjectPermission::list_entries_by_project_id($this->project_id);
	    		
	    		if (is_array($project_permission_array) and count($project_permission_array) >= 1)
	    		{
		    		foreach($project_permission_array as $key => $value)
		    		{
		    			$project_permission = new ProjectPermission($value);
	
		    			if (($user_id = $project_permission->get_user_id()) != null)
		    			{
		    				if ($this->user_id == $user_id)
		    				{
		    					$permission_array = $project_permission->get_permission_array();
		    					if ($permission_array[read] == true)
		    					{
		    						$access_read			= true;
		    					}
		    					if ($permission_array[set_readable] == true)
		    					{
		    						$access_set_readable	= true;
		    					}
		    					if ($permission_array[write] == true)
		    					{
		    						$access_write			= true;
		    					}
		    					if ($permission_array[set_writable] == true)
		    					{
		    						$access_set_writeable	= true;
		    					}
		    					if ($permission_array[reactivate] == true)
		    					{
		    						$access_reactivate		= true;
		    					}
		    					if ($permission_array[delete] == true)
		    					{
		    						$access_delete			= true;
		    					}
		    					if ($permission_array[set_permissions] == true)
		    					{
		    						$access_set_permissions	= true;
		    					}
		    				}
		    			}
		    			elseif(($organisation_unit_id = $project_permission->get_organisation_unit_id()) != null)
		    			{
		    				$organisation_unit = new OrganisationUnit($organisation_unit_id);
		    				if ($organisation_unit->is_permission($this->user_id))
		    				{
		    					$permission_array = $project_permission->get_permission_array();
		    					if ($permission_array[read] == true)
		    					{
		    						$access_read			= true;
		    					}
		    					if ($permission_array[set_readable] == true)
		    					{
		    						$access_set_readable	= true;
		    					}
		    					if ($permission_array[write] == true)
		    					{
		    						$access_write			= true;
		    					}
		    					if ($permission_array[set_writable] == true)
		    					{
		    						$access_set_writeable	= true;
		    					}
		    					if ($permission_array[reactivate] == true)
		    					{
		    						$access_reactivate		= true;
		    					}
		    					if ($permission_array[delete] == true)
		    					{
		    						$access_delete			= true;
		    					}
		    					if ($permission_array[set_permissions] == true)
		    					{
		    						$access_set_permissions	= true;
		    					}
		    				}
		    			}
		    			elseif(($group_id = $project_permission->get_group_id()) != null)
		    			{
		    				$group = new Group($group_id);
		    				if ($group->is_user_in_group($this->user_id))
		    				{
		    					$permission_array = $project_permission->get_permission_array();
		    					if ($permission_array[read] == true)
		    					{
		    						$access_read			= true;
		    					}
		    					if ($permission_array[set_readable] == true)
		    					{
		    						$access_set_readable	= true;
		    					}
		    					if ($permission_array[write] == true)
		    					{
		    						$access_write			= true;
		    					}
		    					if ($permission_array[set_writable] == true)
		    					{
		    						$access_set_writeable	= true;
		    					}
		    					if ($permission_array[reactivate] == true)
		    					{
		    						$access_reactivate		= true;
		    					}
		    					if ($permission_array[delete] == true)
		    					{
		    						$access_delete			= true;
		    					}
		    					if ($permission_array[set_permissions] == true)
		    					{
		    						$access_set_permissions	= true;
		    					}
		    				}
		    			}
		    			else
		    			{
		    				return false;
		    			}
		    		}	
	    		}
	    		else
	    		{
	    			return false;
	    		}
	    		
	    		switch ($intention):
						
					case (1):
						if ($access_read == true)
						{
							return true;
						}
						else
						{
							return false;
						}
					break;
					
					case(2):
						if ($access_set_readable == true)
						{
							return true;
						}
						else
						{
							return false;
						}
					break;
					
					case(3):
						if ($access_write == true)
						{
							return true;
						}
						else
						{
							return false;
						}
					break;
					
					case(4):
						if ($access_set_writeable == true)
						{
							return true;
						}
						else
						{
							return false;
						}
					break;
					
					case(5):
						if ($access_reactivate== true)
						{
							return true;
						}
						else
						{
							return false;
						}
					break;
					
					case(6):
						if ($access_delete == true)
						{
							return true;
						}
						else
						{
							return false;
						}
					break;
					
					case(7):
						if ($access_set_permissions == true)
						{
							return true;
						}
						else
						{
							return false;
						}
					break;
				
					default:
						return false;
					break;
				
				endswitch;
	    		
	    	}
	    	else
	    	{
	    		return false;
	    	}
    	}
    }
    
    /**
     * Lists involved users of a project
     * @return array
     */
    public function list_involved_users()
    {
    	if ($this->project_id)
    	{
    		$return_array = array();
    		
	    	$project_permission_array = ProjectPermission::list_entries_by_project_id($this->project_id);
	    	
	    	if (is_array($project_permission_array) and count($project_permission_array) >= 1)
	    	{
	    		foreach($project_permission_array as $key => $value)
	    		{
    				$project_permission = new ProjectPermission($value);
	
	    			if (($user_id = $project_permission->get_user_id()) != null)
	    			{
    					$permission_array = $project_permission->get_permission_array();
    					if ($permission_array[write] == true)
    					{
    						array_push($return_array, $user_id);
    					}
	    			}
    			}
    			
    			if (is_array($return_array) and count($return_array) >= 1)
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
    	else
    	{
    		return null;
    	}
    }
    
    /**
     * Changes the owner of a project
     * @param integer $owner_id Project Owner
     * @return bool
     */
    public function change_owner_permission($owner_id)
    {
    	global $transaction;
    	
    	if (is_numeric($owner_id))
    	{
    		$transaction_id = $transaction->begin();
    	
    		$project_permission_array = ProjectPermission::list_entries_by_project_id_and_intention($this->project_id, 1);
    		
    		if (is_numeric($project_permission_array[0]))
    		{
	    		$project_permission = new ProjectPermission($project_permission_array[0]);
	    		if ($project_permission->set_user_id($owner_id) == true)
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
    			$project_permission = new ProjectPermission(null);
				if ($project_permission->create($owner_id, null, null, $this->project_id, $GLOBALS[std_perm_user], null, 1) != null) {
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
    	else
    	{
    		return false;
    	}
    } 
    
    /**
     * Changes the leader of a project
     * @param integer $leader_id
     * @return bool
     */
    public function change_leader_permission($leader_id)
    {
    	global $transaction;
    	
    	if (is_numeric($leader_id))
    	{
    		$transaction_id = $transaction->begin();
    	
    		$project_permission_array = ProjectPermission::list_entries_by_project_id_and_intention($this->project_id, 2);
    	
    		if (is_numeric($project_permission_array[0]))
    		{
	    		$project_permission = new ProjectPermission($project_permission_array[0]);
	    		if ($project_permission->set_user_id($leader_id) == true)
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
    			$project_permission = new ProjectPermission(null);
				if ($project_permission->create($leader_id, null, null, $this->project_id, $GLOBALS[std_perm_organ_leader], null, 2) != null)
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
    	else
    	{
    		return false;
    	}
    }
    
    /**
     * Changes the organisation_unit of a project
     * @param integer $organisation_unit_id
     * @return bool
     */
    public function change_organisation_unit_permission($organisation_unit_id)
    {
		global $transaction;
    
   		if (is_numeric($organisation_unit_id))
   		{
    		$transaction_id = $transaction->begin();
    	
    		$organisation_unit = new OrganisationUnit($organisation_unit_id);
    		
    		$project_permission_ou_group_array = ProjectPermission::list_entries_by_project_id_and_intention($this->project_id, 4);
    	
    		if (is_array($project_permission_ou_group_array) and count($project_permission_ou_group_array) >= 1)
    		{
    			foreach($project_permission_ou_group_array as $key => $value)
    			{
    				$project_permission = new ProjectPermission($value);
    				if ($project_permission->delete() == false)
    				{
    					if ($transaction_id != null)
    					{
							$transaction->rollback($transaction_id);
						}
						return false;
    				}
    			}
    		}
    		
    		$project_permission_array = ProjectPermission::list_entries_by_project_id_and_intention($this->project_id, 3);
    	
    		if (is_numeric($project_permission_array[0]))
    		{
    			$project_permission = new ProjectPermission($project_permission_array[0]);
				if (($return_value = $project_permission->set_organisation_unit_id($organisation_unit_id)) == false)
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
    			$project_permission = new ProjectPermission(null);
				if (($return_value = $project_permission->create(null, $organisation_unit_id, null, $this->project_id, $GLOBALS[std_perm_organ_unit], null, 3)) == null)
				{
					if ($transaction_id != null)
					{
						$transaction->rollback($transaction_id);
					}
					return false;
				}
    		}
    	
    		$group_array = $organisation_unit->list_groups();
					
			if(is_array($group_array) and count($group_array) >= 1)
			{
				foreach($group_array as $key => $value)
				{
					$project_permission = new ProjectPermission(null);
					if ($project_permission->create(null, null, $value, $this->project_id, $GLOBALS[std_perm_organ_group], null, 4) == null)
					{
						if ($transaction_id != null)
						{
							$transaction->rollback($transaction_id);
						}
						return false;
					}
				}
			}
    	
    		if ($transaction_id != null)
    		{
				$transaction->commit($transaction_id);
			}
    		return $return_value;
    	}
    	else
    	{
    		return false;
    	}
    }
    
    /**
     * Sets another user-id (if the current user id is not required)
     * @param integer $user_id
     * @return bool
     */
    public function set_user_id($user_id)
    {
    	if (is_numeric($user_id))
    	{
    		$this->user_id = $user_id;
    		return true;
    	}else{
    		return false;
    	}
    }
    
}
?>