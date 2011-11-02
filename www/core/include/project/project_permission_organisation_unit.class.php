<?php
/**
 * @package project
 * @version 0.4.0.0
 * @author Roman Konertz <konertz@open-lims.org>
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
require_once("interfaces/project_permission_organisation_unit.interface.php");
require_once("interfaces/project_permission_case.interface.php");

if (constant("UNIT_TEST") == false or !defined("UNIT_TEST"))
{
	require_once("access/project_permission.access.php");
}

/**
 * Project Organisation Unit Permission Management Class
 * @package project
 */
class ProjectPermissionOrganisationUnit extends ProjectPermission implements ProjectPermissionOrganisationUnitInterface, ProjectPermissionCaseInterface
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
	 * @see ProjectPermissionOrganisationUnitInterface::create()
	 * @param integer $organisaton_unit_id
	 * @param integer $project_id
	 * @param integer $permission
	 * @param integer $owner_id
	 * @param integer $intention
	 * @return integer
	 * @throws ProjectPermissionOrganisationUnitCreateException
	 * @throws ProjectPermissionOrganisationUnitCreateVirtualFolderException
	 */
	public function create($organisation_unit_id, $project_id, $permission, $owner_id, $intention)
    {
    	global $transaction;

    	if (is_numeric($organisation_unit_id) and is_numeric($project_id))
    	{
    		$transaction_id = $transaction->begin();
    		
    		if (($permission_id = parent::create(null, $organisation_unit_id, null, $project_id, $permission, $owner_id, $intention)) != null)
    		{
    			$this->__construct($permission_id);
    			$project_folder_id = ProjectFolder::get_folder_by_project_id($project_id);
    			
    			$permission_string = strrev(decbin($permission));
    			
    			$folder_id = OrganisationUnitFolder::get_folder_by_organisation_unit_id($organisation_unit_id);
    			
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
			    		throw new ProjectPermissionOrganisationUnitCreateVirtualFolderException();
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
    			throw new ProjectPermissionOrganisationUnitCreateException();
    		}
    	}
    	else
    	{
    		throw new ProjectPermissionOrganisationUnitCreateException();
    	}
    }
    
    /**
     * @see ProjectPermissionOrganisationUnitInterface::delete()
     * @return bool
     * @throws ProjectPermissionOrganisationUnitDeleteException
     * @throws ProjectPermisisonOrganisationUnitDeleteVirtualFolderException
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
    			
    		if (ProjectPermission_Access::count_entries_with_project_id_and_organisation_unit_id($project_id, $this->project_permission->get_organisation_unit_id()) <= 1)
    		{
	    		$folder_id = OrganisationUnitFolder::get_folder_by_organisation_unit_id($this->project_permission->get_organisation_unit_id());
	    			
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
	    				if ($transaction_id != null)
    					{
							$transaction->rollback($transaction_id);
						}
			    		throw new ProjectPermissionOrganisationUnitDeleteVirtualFolderException();
	    			}
	    		}
    		}
    		
    		if (parent::delete() == true)
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
			    throw new ProjectPermissionOrganisationUnitDeleteException();
    		}
    	}
    	else
    	{
    		throw new ProjectPermissionOrganisationUnitDeleteException();
    	}
    }
    
    
    /**
     * @see ProjectPermissionOrganisationUnitInterface::is_case()
     * @param integer $permission_id
     * @return bool
     */
    public static function is_case($permission_id)
    {
    	return ProjectPermission_Access::is_organisation_unit_entry($permission_id);
    }
}
?>