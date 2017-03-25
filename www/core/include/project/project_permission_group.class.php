<?php
/**
 * @package project
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
require_once("interfaces/project_permission_group.interface.php");
require_once("interfaces/project_permission_case.interface.php");

if (constant("UNIT_TEST") == false or !defined("UNIT_TEST"))
{
	require_once("access/project_permission.access.php");
}

/**
 * Project Group Permission Management Class
 * @package project
 */
class ProjectPermissionGroup extends ProjectPermission implements ProjectPermissionGroupInterface, ProjectPermissionCaseInterface
{
	function __construct($permission_id)
	{
		if (is_numeric($permission_id))
		{
			parent::__construct($permission_id);
		}
		else
		{
			parent::__construct(null);
		}
	}
	
	/**
	 * @see ProjectPermissionGroupInterface::create()
	 * @param integer $group_id
	 * @param integer $project_id
	 * @param integer $permission
	 * @param integer $owner_id
	 * @param integer $intention
	 * @return integer
	 * @throws ProjectPermissionGroupCreateException
	 * @throws ProjectPermissionGroupCreateVirtualFolderException
	 */
	public function create($group_id, $project_id, $permission, $owner_id, $intention)
    {
    	global $transaction;

    	if (is_numeric($group_id) and is_numeric($project_id))
    	{
    		$transaction_id = $transaction->begin();
    		
    		if (($permission_id = parent::create(null, null, $group_id, $project_id, $permission, $owner_id, $intention)) != null)
    		{
    			self::__construct($permission_id);
    			$project_folder_id = ProjectFolder::get_folder_by_project_id($project_id);
    			
    			$permission_string = strrev(decbin($permission));
    			
    			$folder_id = GroupFolder::get_folder_by_group_id($group_id);
    			
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
						$transaction->rollback($transaction_id);
			    		throw new ProjectPermissionGroupCreateVirtualFolderException();
    				}
    			}
    			
				$transaction->commit($transaction_id);
	    		return $permission_id;
    		}
    		else
    		{
				$transaction->rollback($transaction_id);
    			throw new ProjectPermissionGroupCreateException();
    		}
    	}
    	else
    	{
    		throw new ProjectPermissionGroupCreateException();
    	}
    }
    
    /**
     * @see ProjectPermissionGroupInterface::delete()
     * @return bool
     * @throws ProjectPermissionGroupDeleteException
     * @throws ProjectPermisisonGroupDeleteVirtualFolderException
     */
    public function delete()
    {
    	global $transaction;
    	
    	if ($this->permission_id and $this->project_permission)
    	{
			$transaction_id = $transaction->begin();
    	
			$project_id = $this->project_permission->get_project_id();

    		$project_folder_id = ProjectFolder::get_folder_by_project_id($project_id);
			
   			$permission_string = strrev(decbin($this->project_permission->get_permission()));
    			
    		if (ProjectPermission_Access::count_entries_with_project_id_and_group_id($project_id, $this->project_permission->get_group_id()) <= 1)
    		{
	    		$folder_id = GroupFolder::get_folder_by_group_id($this->project_permission->get_group_id());
	    			
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
	    			
	    		if ($virtual_folder_id)
	    		{
	    			$virtual_folder = new VirtualFolder($virtual_folder_id);
	    			if ($virtual_folder->unlink_folder($project_folder_id) == false)
		    		{
						$transaction->rollback($transaction_id);
				    	throw new ProjectPermissionGroupDeleteVirtualFolderException();
		    		}
	    		}
    		}
    		
    		if (parent::delete() == true)
    		{
				$transaction->commit($transaction_id);
				return true;
    		}
    		else
    		{
				$transaction->rollback($transaction_id);
			    throw new ProjectPermissionGroupDeleteException();
    		}
    	}
    	else
    	{
    		throw new ProjectPermissionGroupDeleteException();
    	}
    }
    
    
    /**
     * @see ProjectPermissionGroupInterface::is_case()
     * @param integer $permission_id
     * @return bool
     */
    public static function is_case($permission_id)
    {
    	return ProjectPermission_Access::is_group_entry($permission_id);
    }
}
?>