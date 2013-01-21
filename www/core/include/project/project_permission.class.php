<?php
/**
 * @package project
 * @version 0.4.0.0
 * @author Roman Konertz <konertz@open-lims.org>
 * @copyright (c) 2008-2013 by Roman Konertz
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
	require_once("access/project.wrapper.access.php");
}

/**
 * Project Permission Management Class
 * @package project
 */
class ProjectPermission implements ProjectPermissionInterface, EventListenerInterface
{
    protected $permission_id;
    protected $project_permission;
    
    /**
     * @see ProjectPermissionInterface::__construct()
     * @param integer $permission_id
     */
    protected function __construct($permission_id)
    {
    	if (is_numeric($permission_id))
		{
			if (ProjectPermission_Access::exist_id($permission_id) == true)
			{
				$this->permission_id = $permission_id;
   	   			$this->project_permission = new ProjectPermission_Access($permission_id);
			}
			else
			{
				throw new ProjectPermissionNotFoundException();
			}
    	}
    	else
    	{
    		$this->permission_id = null;
   	   		$this->project_permission = new ProjectPermission_Access(null);
    	}
    }
    
    /**
     * @param integer $user_id
     * @param integer $organisation_unit_id
     * @param integer $group_id
     * @param integer $project_id
     * @param integer $permission
     * @param integer $owner_id
     * @param integer $intention
     * @return integer
     */
    protected function create($user_id, $organisation_unit_id, $group_id, $project_id, $permission, $owner_id, $intention)
    {
    	return $this->project_permission->create($user_id, $organisation_unit_id, $group_id, $project_id, $permission, $owner_id, $intention);
    } 
    
    /**
     * @return bool
     */
    protected function delete()
    {
    	return $this->project_permission->delete();
    }
    
    /**
     * @see ProjectPermissionInterface::get_permission_array()
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
							$permission_array['read'] = true;
						}
					break;
					
					case(1):
						if ($permission_string{$address})
						{
							$permission_array['set_readable'] = true;
						}
					break;
					
					case(2):
						if ($permission_string{$address})
						{
							$permission_array['write'] = true;
						}
					break;
					
					case(3):
						if ($permission_string{$address})
						{
							$permission_array['set_writeable'] = true;
						}
					break;
					
					case(4):
						if ($permission_string{$address})
						{
							$permission_array['reactivate'] = true;
						}
					break;
					
					case(5):
						if ($permission_string{$address})
						{
							$permission_array['delete'] = true;
						}
					break;
					
					case(6):
						if ($permission_string{$address})
						{
							$permission_array['set_permissions'] = true;
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
     * @see ProjectPermissionInterface::get_user_id()
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
     * @see ProjectPermissionInterface::get_organisation_unit_id()
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
     * @see ProjectPermissionInterface::get_group_id()
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
     * @see ProjectPermissionInterface::get_owner_id()
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
     * @see ProjectPermissionInterface::get_intention()
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
     * @see ProjectPermissionInterface::set_user_id()
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
				$virtual_folder = new ProjectVirtualFolder($value);
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
				$virtual_folder = new ProjectVirtualFolder($value);
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
     * @see ProjectPermissionInterface::set_organisation_unit_id()
     * @param integer $organisation_unit_id
     * @return bool
     */
    public function set_organisation_unit_id($organisation_unit_id)
    {
    	global $transaction;
    	
    	if (is_numeric($organisation_unit_id) and $this->project_permission and $this->permission_id)
    	{
    		$transaction_id = $transaction->begin();
    		
    		$project_id = $this->project_permission->get_project_id();

    		$project_folder_id 		= ProjectFolder::get_folder_by_project_id($project_id);
			$current_ou_folder_id 	= OrganisationUnitFolder::get_folder_by_organisation_unit_id($this->project_permission->get_organisation_unit_id());
			$new_ou_folder_id 		= OrganisationUnitFolder::get_folder_by_organisation_unit_id($organisation_unit_id);
						
			$current_virtual_folder_array = VirtualFolder::list_entries_by_folder_id($current_ou_folder_id);
			
			foreach($current_virtual_folder_array as $key => $value)
			{
				$virtual_folder = new ProjectVirtualFolder($value);
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
				$virtual_folder = new ProjectVirtualFolder($value);
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
     * @see ProjectPermissionInterface::set_group_id()
     * @param integer $group_id
     * @return bool
     */
    public function set_group_id($group_id)
    {
    	global $transaction;
    	
    	if (is_numeric($group_id) and $this->project_permission and $this->permission_id)
    	{
    		$transaction_id = $transaction->begin();
    		
    		$project_id = $this->project_permission->get_project_id();

    		$project_folder_id 			= ProjectFolder::get_folder_by_project_id($project_id);
			$current_group_folder_id	= GroupFolder::get_folder_by_group_id($this->project_permission->get_group_id());
			$new_group_folder_id 		= GroupFolder::get_folder_by_group_id($group_id);
						
			$current_virtual_folder_array = VirtualFolder::list_entries_by_folder_id($current_group_folder_id);
			
			foreach($current_virtual_folder_array as $key => $value)
			{
				$virtual_folder = new ProjectVirtualFolder($value);
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
				$virtual_folder = new ProjectVirtualFolder($value);
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
     * @see ProjectPermissionInterface::set_permission()
     * @param integer $permission
     * @return bool
     */
    public function set_permission($permission)
    {
    	if (is_numeric($permission) and $this->project_permission and $this->permission_id)
    	{
    		if (($user_id = $this->project_permission->get_user_id()) != null)
    		{
	    		$current_permission_string = strrev(decbin($this->project_permission->get_permission()));
	    		$new_permission_string = strrev(decbin($permission));

	    		$project_folder_id = ProjectFolder::get_folder_by_project_id($this->project_permission->get_project_id());
	    		
	    		if ($current_permission_string{2} == 0 and $current_permission_string{3} == 0  and $current_permission_string{7} == 0)
	    		{
	    			$folder_id = UserFolder::get_folder_by_user_id($user_id);
		    			
	    			$virtual_folder = new VirtualFolder(null);
	    			$virtual_folder_array = $virtual_folder->list_entries_by_folder_id($folder_id);
	    			
	    			foreach($virtual_folder_array as $key => $value)
	    			{
	    				$virtual_folder = new ProjectVirtualFolder($value);
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
    		}
    		
    		return $this->project_permission->set_permission($permission);
    	}
    	else
    	{
    		return false;
    	}
    }
    
    
    /**
     * @see ProjectPermissionInterface::list_entries_by_project_id()
     * @param integer $project_id
     * @return array
     */
    public static function list_entries_by_project_id($project_id)
    {
    	return ProjectPermission_Access::list_entries_by_project_id($project_id);
    }

	/**
	 * @see ProjectPermissionInterface::list_entries_by_project_id_and_intention()
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
	private static function list_entries_by_project_id_and_intention_and_group_id($project_id, $intention, $group_id)
	{
		return ProjectPermission_Access::list_entries_by_project_id_and_intention_and_group_id($project_id, $intention, $group_id);
	}
	
	/**
	 * @param integer $project_id
	 * @param integer $intention
	 * @param integer $user_id
	 * @return array
	 */
	private static function list_entries_by_project_id_and_intention_and_user_id($project_id, $intention, $user_id)
	{
		return ProjectPermission_Access::list_entries_by_project_id_and_intention_and_user_id($project_id, $intention, $user_id);
	}
	
	/**
	 * @param integer $organisation_unit_id
	 * @return array
	 */
	private static function list_system_setted_projects_by_organisation_id($organisation_unit_id)
	{
		return ProjectPermission_Access::list_projects_by_organisation_id_and_intention($organisation_unit_id, 3);
	}
	
	/**
	 * @see ProjectPermissionInterface::delete_entries_by_project_id_and_intention()
	 * @param integer $project_id
	 * @param integer $intention
	 * @return array
	 */
	public static function delete_entries_by_project_id_and_intention($project_id, $intention)
	{
		return ProjectPermission_Access::delete_entries_by_project_id_and_intention($project_id, $intention);
	}
	
	/**
	 * @see ProjectPermissionInterface::delete_by_group_id()
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
	 * @see ProjectPermissionInterface::reset_owner_id()
	 * @param integer $old_owner_id
	 * @param integer $new_owner_id
	 * @return bool
	 */
	public static function reset_owner_id($old_owner_id, $new_owner_id)
	{
		return ProjectPermission_Access::reset_owner_id($old_owner_id, $new_owner_id);
	}

    /**
     * @see EventListenerInterface::listen_events()
     * @param object $event_object
     * @return bool
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
    	
    	/**
    	 * @todo slow
    	 */
   		if ($event_object instanceof OrganisationUnitLeaderCreateEvent)
    	{
    		$project_array = Project::list_organisation_unit_related_projects($event_object->get_organisation_unit_id(), true);
    		if (is_array($project_array) and count($project_array) >= 1)
    		{
    			$project_permission = new ProjectPermissionUser(null);
    			foreach($project_array as $key => $value)
    			{
    				if ($project_permission->create($event_object->get_leader_id(), $value, (int)Registry::get_value("project_leader_default_permission"), null, 2) == null)
					{
						return false;
					}
    			}
    		}
    	}
    	
    	/**
    	 * @todo slow
    	 */
    	if ($event_object instanceof OrganisationUnitLeaderDeleteEvent)
    	{
    		$project_array = Project::list_organisation_unit_related_projects($event_object->get_organisation_unit_id(), true);
    		if (is_array($project_array) and count($project_array) >= 1)
    		{
    			foreach($project_array as $key => $value)
    			{
    				$permission_array = self::list_entries_by_project_id_and_intention_and_user_id($value, 2, $event_object->get_leader_id());
    				if (is_array($permission_array) and count($permission_array) >= 1)
    				{
    					foreach($permission_array as $permission_key => $permission_value)
    					{
    						$project_permission = self::get_instance($permission_value);
    						if ($project_permission->delete() == false)
    						{
    							return false;
    						}
    					}
    				}
    			}
    		}
    	}
    	
    	/**
    	 * @todo slow
    	 */
    	if ($event_object instanceof OrganisationUnitQualityManagerCreateEvent)
    	{
    		$project_array = Project::list_organisation_unit_related_projects($event_object->get_organisation_unit_id(), true);
    		if (is_array($project_array) and count($project_array) >= 1)
    		{
    			$project_permission = new ProjectPermissionUser(null);
    			foreach($project_array as $key => $value)
    			{
    				if ($project_permission->create($event_object->get_quality_manager_id(), $value, (int)Registry::get_value("project_quality_manager_default_permission"), null, 5) == null)
					{
						return false;
					}
    			}
    		}
    	}
    	
    	/**
    	 * @todo slow
    	 */
    	if ($event_object instanceof OrganisationUnitQualityManagerDeleteEvent)
    	{
    		$project_array = Project::list_organisation_unit_related_projects($event_object->get_organisation_unit_id(), true);
    		if (is_array($project_array) and count($project_array) >= 1)
    		{
    			foreach($project_array as $key => $value)
    			{
    				$permission_array = self::list_entries_by_project_id_and_intention_and_user_id($value, 5, $event_object->get_quality_manager_id());
    				if (is_array($permission_array) and count($permission_array) >= 1)
    				{
    					foreach($permission_array as $permission_key => $permission_value)
    					{
    						$project_permission = self::get_instance($permission_value);
    						if ($project_permission->delete() == false)
    						{
    							return false;
    						}
    					}
    				}
    			}
    		}
    	}
    	
    	/**
    	 * @todo slow
    	 */
    	if ($event_object instanceof OrganisationUnitGroupCreateEvent)
    	{
    		$project_array = self::list_system_setted_projects_by_organisation_id($event_object->get_organisation_unit_id());
					
			if (is_array($project_array) and count($project_array) >= 1)
			{
				foreach($project_array as $key => $value)
				{
					$project_permission = new ProjectPermissionGroup(null);
					if ($project_permission->create($event_object->get_group_id(), $value, (int)Registry::get_value("project_group_default_permission"), null, 4) == null)
					{
						return false;
					}							
				}
			}
    	}
    	
    	/**
    	 * @todo slow
    	 */
    	if ($event_object instanceof OrganisationUnitGroupDeleteEvent)
    	{
    		$project_array = self::list_system_setted_projects_by_organisation_id($event_object->get_organisation_unit_id());
					
			if (is_array($project_array) and count($project_array) >= 1)
			{
				foreach($project_array as $key => $value)
				{
					$project_permission_array = ProjectPermission::list_entries_by_project_id_and_intention_and_group_id($value, 4, $event_object->get_group_id());
					if (is_array($project_permission_array) and count($project_permission_array) >= 1)
					{
						foreach($project_permission_array as $sub_key => $sub_value)
						{
							$project_permission = self::get_instance($sub_value);
							if ($project_permission->delete() == false)
							{
								return false;
							}
						}
					}							
				}
			}
    	}
    	
    	return true;
    }

    /**
     * @return object
     */
	public static function get_instance($permission_id)
	{
		if (ProjectPermissionUser::is_case($permission_id) == true)
		{
			return new ProjectPermissionUser($permission_id);
		}
		elseif (ProjectPermissionGroup::is_case($permission_id) == true)
		{
			return new ProjectPermissionGroup($permission_id);
		}
		elseif (ProjectPermissionOrganisationUnit::is_case($permission_id) == true)
		{
			return new ProjectPermissionOrganisationUnit($permission_id);
		}
	}
}

?>