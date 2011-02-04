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
require_once("interfaces/project_permission.interface.php");

if (constant("UNIT_TEST") == false or !defined("UNIT_TEST"))
{
	require_once("access/project_permission.access.php");
}

/**
 * Project Permission Management Class
 * @package project
 */
class ProjectPermission implements ProjectPermissionInterface, EventListenerInterface
{
    private $permission_id;
    private $project_permission;
    
    /**
     * @param integer $permission_id
     */
    function __construct($permission_id)
    {
    	if ($permission_id == null)
    	{
   	   		$this->permission_id = null;
   	   		$this->project_permission = new ProjectPermission_Access(null);
   	   	}
   	   	else
   	   	{
   	   		$this->permission_id = $permission_id;
   	   		$this->project_permission = new ProjectPermission_Access($permission_id);
   	   	}
    }
    
    function __destruct()
    {
    	unset($this->permission_id);
    	unset($this->project_permission);
    }
    
    /**
     * Creates a project-permission
     * @param integer $user_id
     * @param integer $organisation_unit_id
     * @param integer $group_id
     * @param integer $project_id
     * @param integer $permission
     * @param integer $owner_id
     * @param integer $intention
     * @return integer
     */
    public function create($user_id, $organisation_unit_id, $group_id, $project_id, $permission, $owner_id, $intention)
    {
    	global $transaction;
    	
    	$transaction_id = $transaction->begin();
    	
    	$permission_id = $this->project_permission->create($user_id, $organisation_unit_id, $group_id, $project_id, $permission, $owner_id, $intention);
    	
    	if ($permission_id != null)
    	{
    		$this->__construct($permission_id);
    		
    		$project_folder_id = ProjectFolder::get_folder_by_project_id($project_id);
    		
    		if($user_id)
    		{
    			$folder_id = UserFolder::get_folder_by_user_id($user_id);
    			
    			$virtual_folder = new VirtualFolder(null);
    			$virtual_folder_array = $virtual_folder->list_entries_by_folder_id($folder_id);
    			
    			foreach($virtual_folder_array as $key => $value)
    			{
    				$virtual_folder = new VirtualFolder($value);
    				if ($virtual_folder->is_project_vfolder() == true)
    				{
    					$virtual_folder_id = $value;
    				}
    			}

    			if (is_numeric($virtual_folder_id))
    			{
    				$virtual_folder = new VirtualFolder($virtual_folder_id);
    				if ($virtual_folder->link_folder($project_folder_id) == false)
    				{
    					if ($transaction_id != null)
    					{
							$transaction->rollback($transaction_id);
						}
			    		return null;
    				}
    			}
    		}
    		elseif($organisation_unit_id)
    		{
    			$folder_id = OrganisationUnitFolder::get_folder_by_organisation_unit_id($organisation_unit_id);
    			
    			$virtual_folder = new VirtualFolder(null);
    			$virtual_folder_array = $virtual_folder->list_entries_by_folder_id($folder_id);
    			
    			foreach($virtual_folder_array as $key => $value)
    			{
    				$virtual_folder = new VirtualFolder($value);
    				if ($virtual_folder->is_project_vfolder() == true)
    				{
    					$virtual_folder_id = $value;
    				}
    			}
    			   			
    			if (is_numeric($virtual_folder_id))
    			{
    				$virtual_folder = new VirtualFolder($virtual_folder_id);
    				if ($virtual_folder->link_folder($project_folder_id) == false)
    				{
    					if ($transaction_id != null)
    					{
							$transaction->rollback($transaction_id);
						}
			    		return null;
    				}
    			}
    		}
    		elseif($group_id)
    		{
    			$folder_id = GroupFolder::get_folder_by_group_id($group_id);
    			
    			$virtual_folder = new VirtualFolder(null);
    			$virtual_folder_array = $virtual_folder->list_entries_by_folder_id($folder_id);
    			
    			foreach($virtual_folder_array as $key => $value)
    			{
    				$virtual_folder = new VirtualFolder($value);
    				if ($virtual_folder->is_project_vfolder() == true)
    				{
    					$virtual_folder_id = $value;
    				}
    			}
    			
    			if (is_numeric($virtual_folder_id))
    			{
    				$virtual_folder = new VirtualFolder($virtual_folder_id);
    				if ($virtual_folder->link_folder($project_folder_id) == false)
    				{
    					if ($transaction_id != null)
    					{
							$transaction->rollback($transaction_id);
						}
			    		return null;
    				}
    			}
    		}
    		
    		if ($transaction_id != null)
    		{
				$transaction->commit($transaction_id);
			}
    		return $permission_id;	
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
    
    /**
     * Deletes a project-permission
     * @return bool
     */
    public function delete()
    {
    	global $transaction;
    	
    	if ($this->permission_id and $this->project_permission)
    	{
			$transaction_id = $transaction->begin();

			$project_id = $this->project_permission->get_project_id();

    		$project_folder_id = ProjectFolder::get_folder_by_project_id($project_id);
    		
    		if ($this->project_permission->get_user_id())
    		{
				if (ProjectPermission_Access::count_entries_with_project_id_and_user_id($project_id, $this->project_permission->get_user_id()) <= 1)
				{
	    			$folder_id = UserFolder::get_folder_by_user_id($this->project_permission->get_user_id());
	    			
	    			$virtual_folder = new VirtualFolder(null);
	    			$virtual_folder_array = $virtual_folder->list_entries_by_folder_id($folder_id);
	    			
	    			foreach($virtual_folder_array as $key => $value)
	    			{
	    				$virtual_folder = new VirtualFolder($value);
	    				if ($virtual_folder->is_project_vfolder() == true)
	    				{
	    					$virtual_folder_id = $value;
	    				}
	    			}
	    			
	    			if ($virtual_folder_id)
	    			{
	    				$virtual_folder = new VirtualFolder($virtual_folder_id);
	    				$vfolder_return = $virtual_folder->unlink_folder($project_folder_id);
	    			}
	    			else
	    			{
	    				$vfolder_return = true;
	    			}
				}
				else
				{
					$vfolder_return = true;
				}
    		}
    		elseif($this->project_permission->get_organisation_unit_id())
    		{
    			if (ProjectPermission_Access::count_entries_with_project_id_and_organisation_unit_id($project_id, $this->project_permission->get_organisation_unit_id()) <= 1)
    			{
	    			$folder_id = OrganisationUnitFolder::get_folder_by_organisation_unit_id($this->project_permission->get_organisation_unit_id());
	    			
	    			$virtual_folder = new VirtualFolder(null);
	    			$virtual_folder_array = $virtual_folder->list_entries_by_folder_id($folder_id);
	    			
	    			foreach($virtual_folder_array as $key => $value)
	    			{
	    				$virtual_folder = new VirtualFolder($value);
	    				if ($virtual_folder->is_project_vfolder() == true)
	    				{
	    					$virtual_folder_id = $value;
	    				}
	    			}
	    			
	    			if ($virtual_folder_id)
	    			{
	    				$virtual_folder = new VirtualFolder($virtual_folder_id);
	    				$vfolder_return = $virtual_folder->unlink_folder($project_folder_id);
	    			}
	    			else
	    			{
	    				$vfolder_return = true;
	    			}
    			}
    			else
    			{
					$vfolder_return = true;
				}
    		}
    		elseif($this->project_permission->get_group_id())
    		{
    			if (ProjectPermission_Access::count_entries_with_project_id_and_group_id($project_id, $this->project_permission->get_group_id()) <= 1)
    			{
	    			$folder_id = GroupFolder::get_folder_by_group_id($this->project_permission->get_group_id());
	    			
	    			$virtual_folder = new VirtualFolder(null);
	    			$virtual_folder_array = $virtual_folder->list_entries_by_folder_id($folder_id);
	    			
	    			foreach($virtual_folder_array as $key => $value)
	    			{
	    				$virtual_folder = new VirtualFolder($value);
	    				if ($virtual_folder->is_project_vfolder() == true)
	    				{
	    					$virtual_folder_id = $value;
	    				}
	    			}
	    			
	    			if ($virtual_folder_id)
	    			{
	    				$virtual_folder = new VirtualFolder($virtual_folder_id);
	    				$vfolder_return = $virtual_folder->unlink_folder($project_folder_id);
	    			}
	    			else
	    			{
	    				$vfolder_return = true;
	    			}
    			}
    			else
    			{
					$vfolder_return = true;
				}
    		}

    		if ($vfolder_return == true)
    		{
    			if ($this->project_permission->delete() == true)
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
     * Returns the permission array of a permission
     * @return array
     */
    public function get_permission_array()
    {
    	if ($this->permission_id and $this->project_permission)
    	{
    		$permission_array = array();
    	
    		$permission_string = strrev(decbin($this->project_permission->get_permission()));
	
			$count = strlen($permission_string)-1;
			$address = 0;
			
			while ($count+1)
			{	
				switch ($address):
				
					case (0):
						if ($permission_string{$address})
						{
							$permission_array[read] = true;
						}
					break;
					
					case(1):
						if ($permission_string{$address})
						{
							$permission_array[set_readable] = true;
						}
					break;
					
					case(2):
						if ($permission_string{$address})
						{
							$permission_array[write] = true;
						}
					break;
					
					case(3):
						if ($permission_string{$address})
						{
							$permission_array[set_writeable] = true;
						}
					break;
					
					case(4):
						if ($permission_string{$address})
						{
							$permission_array[reactivate] = true;
						}
					break;
					
					case(5):
						if ($permission_string{$address})
						{
							$permission_array[delete] = true;
						}
					break;
					
					case(6):
						if ($permission_string{$address})
						{
							$permission_array[set_permissions] = true;
						}
					break;
					
					default:
					break;
				
				endswitch;					
				
				$count--;
				$address++;
				
			}
			
			unset($count);
			unset($address);
			
			return $permission_array;
    	}
    }
    
    /**
     * @return integer
     */
    public function get_user_id()
    {
    	if ($this->permission_id and $this->project_permission)
    	{
    		return $this->project_permission->get_user_id();
    	}
    	else
    	{
    		return null;
    	}
    }
    
    /**
     * @return integer
     */
    public function get_organisation_unit_id()
    {
    	if ($this->permission_id and $this->project_permission)
    	{
    		return $this->project_permission->get_organisation_unit_id();
    	}
    	else
    	{
    		return null;
    	}
    }
    
    /**
     * @return integer
     */
    public function get_group_id()
    {
    	if ($this->permission_id and $this->project_permission)
    	{
    		return $this->project_permission->get_group_id();	
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
    	if ($this->permission_id and $this->project_permission)
    	{
    		return $this->project_permission->get_owner_id();	
    	}
    	else
    	{
    		return null;
    	}
    }
    
    /**
     * @return integer
     */
    public function get_intention()
    {
    	if ($this->permission_id and $this->project_permission)
    	{
    		return $this->project_permission->get_intention();	
    	}
    	else
    	{
    		return null;
    	}
    }
    
    /**
     * @param integer $user_id
     * @return bool
     */
    public function set_user_id($user_id)
    {
    	global $transaction;

    	if (is_numeric($user_id) and $this->project_permission and $this->permission_id)
    	{
    		$transaction_id = $transaction->begin();
    		
    		$project_id = $this->project_permission->get_project_id();

    		$project_folder_id		= ProjectFolder::get_folder_by_project_id($project_id);
			$current_user_folder_id = UserFolder::get_folder_by_user_id($this->project_permission->get_user_id());
			$new_user_folder_id 	= UserFolder::get_folder_by_user_id($user_id);
						
			$current_virtual_folder_array = VirtualFolder::list_entries_by_folder_id($current_user_folder_id);
			
			foreach($current_virtual_folder_array as $key => $value)
			{
				$virtual_folder = new VirtualFolder($value);
				if ($virtual_folder->is_project_vfolder() == true)
				{
					$virtual_folder_id = $value;
				}
			}
						
			if ($virtual_folder_id)
			{
				$virtual_folder = new VirtualFolder($virtual_folder_id);
				if ($virtual_folder->unlink_folder($project_folder_id) == false)
				{
					if ($transaction_id != null)
					{
						$transaction->rollback($transaction_id);
					}
					return false;
				}
			}
    		
			$new_virtual_folder_array = $virtual_folder->list_entries_by_folder_id($new_user_folder_id);
			
			foreach($new_virtual_folder_array as $key => $value)
			{
				$virtual_folder = new VirtualFolder($value);
				if ($virtual_folder->is_project_vfolder() == true)
				{
					$virtual_folder_id = $value;
				}
			}
    		
    		if ($virtual_folder_id)
    		{
				$virtual_folder = new VirtualFolder($virtual_folder_id);
				if ($virtual_folder->link_folder($project_folder_id) == false)
				{
					if ($transaction_id != null)
					{
						$transaction->rollback($transaction_id);
					}
					return false;
				}
			}
    		
    		if ($this->project_permission->set_user_id($user_id) == false)
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
				return true;
    		}
    	}
    	else
    	{
    		return false;
    	}
    }
    
    /**
     * @param integer $organisation_unit_id
     * @return bool
     */
    public function set_organisation_unit_id($organisation_unit_id)
    {
    	if (is_numeric($organisation_unit_id) and $this->project_permission and $this->permission_id)
    	{
    		$transaction_id = $transaction->begin();
    		
    		$project_id = $this->project_permission->get_project_id();

    		$project_folder_id 		= ProjectFolder::get_folder_by_project_id($project_id);
			$current_ou_folder_id 	= Folder::get_organisation_unit_folder_by_organisaiton_unit_id($this->project_permission->get_organisation_unit_id());
			$new_ou_folder_id 		= Folder::get_organisation_unit_folder_by_organisaiton_unit_id($organisation_unit_id);
						
			$current_virtual_folder_array = VirtualFolder::list_entries_by_folder_id($current_ou_folder_id);
			
			foreach($current_virtual_folder_array as $key => $value)
			{
				$virtual_folder = new VirtualFolder($value);
				if ($virtual_folder->is_project_vfolder() == true)
				{
					$virtual_folder_id = $value;
				}
			}
						
			if ($virtual_folder_id)
			{
				$virtual_folder = new VirtualFolder($virtual_folder_id);
				if ($virtual_folder->unlink_folder($project_folder_id) == false)
				{
					if ($transaction_id != null)
					{
						$transaction->rollback($transaction_id);
					}
					return false;
				}
			}
    		
			$new_virtual_folder_array = $virtual_folder->list_entries_by_folder_id($new_ou_folder_id);
			
			foreach($new_virtual_folder_array as $key => $value)
			{
				$virtual_folder = new VirtualFolder($value);
				if ($virtual_folder->is_project_vfolder() == true)
				{
					$virtual_folder_id = $value;
				}
			}
    		
    		if ($virtual_folder_id)
    		{
				$virtual_folder = new VirtualFolder($virtual_folder_id);
				if ($virtual_folder->link_folder($project_folder_id) == false)
				{
					if ($transaction_id != null)
					{
						$transaction->rollback($transaction_id);
					}
					return false;
				}
			}
    		
    		if ($this->project_permission->set_organisation_unit_id($organisation_unit_id) == false)
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
				return true;
    		}
    	}
    	else
    	{
    		return false;
    	}
    }
    
    /**
     * @param integer $group_id
     * @return bool
     */
    public function set_group_id($group_id)
    {
    	if (is_numeric($group_id) and $this->project_permission and $this->permission_id)
    	{
    		$transaction_id = $transaction->begin();
    		
    		$project_id = $this->project_permission->get_project_id();

    		$project_folder_id 			= ProjectFolder::get_folder_by_project_id($project_id);
			$current_group_folder_id	= Folder::get_group_folder_by_organisaiton_group_id($this->project_permission->get_group_id());
			$new_group_folder_id 		= Folder::get_group_folder_by_organisaiton_group_id($group_id);
						
			$current_virtual_folder_array = VirtualFolder::list_entries_by_folder_id($current_group_folder_id);
			
			foreach($current_virtual_folder_array as $key => $value)
			{
				$virtual_folder = new VirtualFolder($value);
				if ($virtual_folder->is_project_vfolder() == true)
				{
					$virtual_folder_id = $value;
				}
			}
						
			if ($virtual_folder_id)
			{
				$virtual_folder = new VirtualFolder($virtual_folder_id);
				if ($virtual_folder->unlink_folder($project_folder_id) == false)
				{
					if ($transaction_id != null)
					{
						$transaction->rollback($transaction_id);
					}
					return false;
				}
			}
    		
			$new_virtual_folder_array = $virtual_folder->list_entries_by_folder_id($new_group_folder_id);
			
			foreach($new_virtual_folder_array as $key => $value)
			{
				$virtual_folder = new VirtualFolder($value);
				if ($virtual_folder->is_project_vfolder() == true)
				{
					$virtual_folder_id = $value;
				}
			}
    		
    		if ($virtual_folder_id)
    		{
				$virtual_folder = new VirtualFolder($virtual_folder_id);
				if ($virtual_folder->link_folder($project_folder_id) == false)
				{
					if ($transaction_id != null)
					{
						$transaction->rollback($transaction_id);
					}
					return false;
				}
			}
    		
    		if ($this->project_permission->set_group_id($group_id) == false)
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
				return true;
    		}
    	}
    	else
    	{
    		return false;
    	}
    }
    
    /**
     * @param integer $permission
     * @return bool
     */
    public function set_permission($permission)
    {
    	if (is_numeric($permission) and $this->project_permission and $this->permission_id)
    	{
    		return $this->project_permission->set_permission($permission);
    	}
    	else
    	{
    		return false;
    	}
    }
    
    
    /**
     * @param integer $project_id
     * @return array
     */
    public static function list_entries_by_project_id($project_id)
    {
    	return ProjectPermission_Access::list_entries_by_project_id($project_id);
    }

	/**
	 * @param integer $project_id
	 * @param integer $intention
	 * @return array
	 */
	public static function list_entries_by_project_id_and_intention($project_id, $intention)
	{
		return ProjectPermission_Access::list_entries_by_project_id_and_intention($project_id, $intention);
	}
	
	/**
	 * @param integer $project_id
	 * @param integer $intention
	 * @param integer $group_id
	 * @return array
	 */
	public static function list_entries_by_project_id_and_intention_and_group_id($project_id, $intention, $group_id)
	{
		return ProjectPermission_Access::list_entries_by_project_id_and_intention_and_group_id($project_id, $intention, $group_id);
	}
	
	/**
	 * @param integer $organisation_unit_id
	 * @return array
	 */
	public static function list_system_setted_projects_by_organisation_id($organisation_unit_id)
	{
		return ProjectPermission_Access::list_projects_by_organisation_id_and_intention($organisation_unit_id, 3);
	}
	
	/**
	 * @param integer $leader_id
	 * @return array
	 */
	public static function list_system_setted_entries_by_leader_id($leader_id)
	{
		return ProjectPermission_Access::list_entries_by_user_id_and_intention($leader_id, 2);
	}
	
	/**
     * Deletes a group from permission table completly.
     * Warning: This method is for group-deletion only!
     * 			Outside group-deletion is causes logical inconsistency!
     * @param integer $group_id
     * @return bool
     */
	public static function delete_by_group_id($group_id)
	{
		return ProjectPermission_Access::delete_by_group_id($group_id);
	}
	
	/**
	 * Changes all owner_ids of another giver owner_id
	 * @param integer $old_owner_id
	 * @param integer $new_owner_id
	 * @return bool
	 */
	public static function reset_owner_id($old_owner_id, $new_owner_id)
	{
		return ProjectPermission_Access::reset_owner_id($old_owner_id, $new_owner_id);
	}

    /**
     * @todo implementation
     */
    public static function listen_events($event_object)
    {
    	if ($event_object instanceof UserDeleteEvent)
    	{
    		if (ProjectPermission_Access::delete_by_user_id($event_object->get_user_id()) == false)
			{
				return false;
			}
    	}
    	
    	if ($event_object instanceof GroupDeleteEvent)
    	{
    		if (ProjectPermission_Access::delete_by_group_id($event_object->get_group_id()) == false)
			{
				return false;
			}
    	}
    	
    	if ($event_object instanceof OrganisationUnitDeleteEvent)
    	{
    		if (ProjectPermission_Access::delete_by_organisation_unit_id($event_object->get_organisation_unit_id()) == false)
			{
				return false;
			}
    	}
    	
    	return true;
    }
}

?>