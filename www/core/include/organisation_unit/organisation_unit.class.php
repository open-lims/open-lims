<?php
/**
 * @package organisation_unit
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
require_once("interfaces/organisation_unit.interface.php");

if (constant("UNIT_TEST") == false or !defined("UNIT_TEST"))
{
	require_once("exceptions/organisation_unit_already_exist_exception.class.php");
	require_once("exceptions/organisation_unit_creation_failed_exception.class.php");
	require_once("exceptions/organisation_unit_not_found_exception.class.php");
	
	require_once("access/organisation_unit.access.php");
	
	require_once("access/organisation_unit_has_member.access.php");
	require_once("access/organisation_unit_has_group.access.php");
	require_once("access/organisation_unit_type.access.php");
}

/**
 * Organisation Unit Class
 * @package organisation_unit
 */
class OrganisationUnit implements OrganisationUnitInterface, EventListenerInterface
{
	private $organisation_unit_id;
	private $organisation_unit;

	/**
	 * @param integer $organisation_unit_id Organisation-Unit-ID
	 */
	function __construct($organisation_unit_id)
	{
		if ($organisation_unit_id == null)
		{
    		$this->organisation_unit_id = null;
			$this->organisation_unit	= new OrganisationUnit_Access(null);
		}
		else
		{		
			$this->organisation_unit_id = $organisation_unit_id;
			$this->organisation_unit	= new OrganisationUnit_Access($organisation_unit_id);
    	}
	}
	
	function __destruct()
	{
		unset($this->organisation_unit_id);
		unset($this->organisation_unit);
	}
	
	/**
	 * Creates a new Organisation-Unit including all dependencies
	 * @param integer $toid Parent-ID
	 * @param string $name
	 * @param integer $type_id
	 * @param bool $contains_projects
	 * @return integer
	 * @throws OrganisationUnitAlreadyExistException
	 * @throws OrganisationUnitCreationFailedException
	 */
	public function create($toid, $name, $type_id, $contains_projects) 
	{
		global $transaction;
		
		if ($this->organisation_unit)
		{
			if ($name and is_numeric($type_id) and is_bool($contains_projects))
			{
				$transaction_id = $transaction->begin();

				if (self::exist_name($name) == true)
				{
					if ($transaction_id != null)
					{
						$transaction->rollback($transaction_id);
					}
					throw new OrganisationUnitAlreadyExistException("",2);
				}
				
				$current_highest_position_ou_id = OrganisationUnit_Access::get_highest_position_by_toid($toid);
				$current_highest_position_ou = new OrganisationUnit_Access($current_highest_position_ou_id);
				
				if ($current_highest_position_ou->get_position())
				{
					$new_highest_position = $current_highest_position_ou->get_position()+1;
				}
				else
				{
					$new_highest_position = 1;
				}
				
				if (($organisation_unit_id = $this->organisation_unit->create($toid, $name, $type_id, $contains_projects, $new_highest_position)) != null)
				{
					$this->__construct($organisation_unit_id);
					
					if ($contains_projects == true)
					{
						// Folder
						$organisation_unit_folder_id = $GLOBALS[organisation_unit_folder_id];
						$folder = new Folder($organisation_unit_folder_id);
		
						$path = new Path($folder->get_path());
						$path->add_element($organisation_unit_id);
						
						$folder = new Folder(null);
						if (($folder_id = $folder->create($name, $organisation_unit_folder_id, false, $path->get_path_string(), $this->get_owner_id(), null)) != null)
						{
							if ($folder->create_organisation_unit_folder($organisation_unit_id) == false)
							{
								$folder->delete(true, true);
								if ($transaction_id != null)
								{
									$transaction->rollback($transaction_id);
								}
								throw new OrganisationUnitCreationFailedException("",1);
							}
							if ($folder->set_flag(8) == false)
							{
								$folder->delete(true, true);
								if ($transaction_id != null)
								{
									$transaction->rollback($transaction_id);
								}
								throw new OrganisationUnitCreationFailedException("",1);
							}
							
													
							// Sample - Virtual Folder
							
							$virtual_folder = new VirtualFolder(null);
							if ($virtual_folder->create($folder_id, "samples") == null)
							{
								$folder->delete(true, true);
								if ($transaction_id != null)
								{
									$transaction->rollback($transaction_id);
								}
								throw new OrganisationUnitCreationFailedException("",1);
							}
							if ($virtual_folder->set_sample_vfolder() == false)
							{
								$folder->delete(true, true);
								if ($transaction_id != null)
								{
									$transaction->rollback($transaction_id);
								}
								throw new OrganisationUnitCreationFailedException("",1);
							}
							
							
							// Project - Virtual Folder
							
							$virtual_folder = new VirtualFolder(null);
							if ($virtual_folder->create($folder_id, "projects") == null)
							{
								$folder->delete(true, true);
								if ($transaction_id != null)
								{
									$transaction->rollback($transaction_id);
								}
								throw new OrganisationUnitCreationFailedException("",1);
							}
							if ($virtual_folder->set_project_vfolder() == false)
							{
								$folder->delete(true, true);
								if ($transaction_id != null)
								{
									$transaction->rollback($transaction_id);
								}
								throw new OrganisationUnitCreationFailedException("",1);
							}
						}
						else
						{
							if ($transaction_id != null)
							{
								$transaction->rollback($transaction_id);
							}
							throw new OrganisationUnitCreationFailedException("",1);
						}
					}
						
					if ($transaction_id != null)
					{
						$transaction->commit($transaction_id);
					}
					
					return $organisation_unit_id;
				}
				else
				{
					if ($transaction_id != null)
					{
						$transaction->rollback($transaction_id);
					}
					throw new OrganisationUnitCreationFailedException("",1);
				}
			}
			else
			{
				throw new OrganisationUnitCreationFailedException("",1);
			}
		}
		else
		{
			throw new OrganisationUnitCreationFailedException("",1);
		}
		
	}
	
	/**
	 * Deletes an Organisation-Unit
	 * @return bool
	 */
	public function delete()
	{
		global $transaction;
		
		if ($this->organisation_unit)
		{
			if ($this->check_delete_dependencies() == true)
			{
				$transaction_id = $transaction->begin();
			
				// User-Connection
				if ($this->delete_members() == false)
				{
					if ($transaction_id != null)
					{
						$transaction->rollback($transaction_id);
					}
					return false;
				}
				
				
				// Group-Connection
				if ($this->delete_groups() == false)
				{
					if ($transaction_id != null)
					{
						$transaction->rollback($transaction_id);
					}
					return false;
				}
				
					
				// Permissions
				if (ProjectPermission::delete_by_organisation_unit_id($this->organisation_unit_id) == false)
				{
					if ($transaction_id != null)
					{
						$transaction->rollback($transaction_id);
					}
					return false;
				}
			
								
				// Folders
				if ($this->organisation_unit->get_contains_projects() == true)
				{
					$contains_projects = true;
					$folder_id = Folder::get_organisation_unit_folder_by_organisation_unit_id($this->organisation_unit_id);
					$folder = new Folder($folder_id);
					
					if ($folder->unset_organisation_unit_folder() == false)
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
					$contains_projects = false;
				}
					
				// Organisation Unit
				if ($this->organisation_unit->get_next_position() != $this->organisation_unit_id)
				{
					
					$tmp_organisation_unit = $this->organisation_unit;
					$tmp_organisation_unit_id = $this->organisation_unit_id;
					$tmp_position = $this->organisation_unit->get_position();
					
					while(($next_organisation_unit_id = $tmp_organisation_unit->get_next_position()) != $tmp_organisation_unit_id)
					{
						$next_organisation_unit = new OrganisationUnit_Access($next_organisation_unit_id);
						$next_position = $next_organisation_unit->get_position();
						if ($next_organisation_unit->set_position($tmp_position) == false)
						{
							if ($transaction_id != null)
							{
								$transaction->rollback($transaction_id);
							}
							return false;
						}
						else
						{
							$tmp_organisation_unit = $next_organisation_unit;
							$tmp_organisation_unit_id = $next_organisation_unit_id;
							$tmp_position = $next_position;
						}
					}
					
				}
				
				if ($this->organisation_unit->delete() == false)
				{
					if ($transaction_id != null)
					{
						$transaction->rollback($transaction_id);
					}
					return false;
				}
				
				if ($contains_projects == true)
				{	
					// Final Folder
					if ($folder->delete(true, true) == false)
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
		else
		{
			return false;	
		}		
		
	}
	
	/**
	 * Checks dependencies before Organisation-Unit deletion.
	 * @return bool
	 */
	public function check_delete_dependencies()
	{
		if ($this->organisation_unit_id)
		{
			// Projecs
			$project_array = Project::list_organisation_unit_related_projects($this->organisation_unit_id, true);
			if (is_array($project_array))
			{
				if (count($project_array) >= 1)
				{
					return false;
				}
			}
		
			// Samples
			$sample_array = Sample::list_organisation_unit_related_samples($this->organisation_unit_id);
			
			if (is_array($sample_array))
			{
				if (count($sample_array) >= 1)
				{
					return false;
				}
			}
			
			// Organisation-Unit Childs	
			$organisation_unit_array = OrganisationUnit_Access::list_entries_by_toid($this->organisation_unit_id);
			if (is_array($organisation_unit_array))
			{
				if (count($organisation_unit_array) >= 1)
				{
					return false;
				}
			}
			
			return true;
			
		}
		else
		{
			return false;
		}
	}

	/**
	 * Checks permissions on Organisation-Unit
	 * @param integer $user_id
	 * @return bool
	 */
	public function is_permission($user_id)
	{
		global $user;
		
		if ($this->organisation_unit)
		{
			if ($user->is_admin())
			{
				return true;
			}
			else
			{
				if ($this->organisation_unit->get_leader_id() == $user_id)
				{
					return true;
				}
				else
				{
					$organisation_unit_has_member_array = OrganisationUnitHasMember_Access::list_users_by_organisation_unit_id($this->organisation_unit_id);
					foreach($organisation_unit_has_member_array as $key => $value)
					{
						if ($value == $user_id)
						{
							return true;
						}
					}				
				}
			}
		}
		return false;
	}
	
	/**
	 * Checks if the OU is on the upper position
	 * @return bool
	 */
	public function is_upper_position()
	{
		if ($this->organisation_unit and $this->organisation_unit_id)
		{
			if ($this->organisation_unit->get_previous_position() == $this->organisation_unit_id)
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
	 * Checks if the OU is on the lower position
	 * @return bool
	 */
	public function is_lower_position()
	{
		if ($this->organisation_unit)
		{
			if ($this->organisation_unit->get_next_position() == $this->organisation_unit_id)
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
	 * Moves the OU one position upwards
	 * @return bool
	 */
	public function position_upwards()
	{
		global $transaction;
		
		if ($this->organisation_unit and $this->organisation_unit_id)
		{
			if ($this->is_upper_position() == false)
			{
				$upper_position = $this->organisation_unit->get_position()-1;
				
				if ($this->organisation_unit->get_is_root() == true)
				{
					$id = OrganisationUnit_Access::get_id_by_position_and_toid($upper_position, null);
				}
				else
				{
					$id = OrganisationUnit_Access::get_id_by_position_and_toid($upper_position, $this->organisation_unit->get_toid());
				}
				
				if (is_numeric($id))
				{
					$transaction_id = $transaction->begin();
					
					$change_organisation_unit = new OrganisationUnit_Access($id);
					if ($change_organisation_unit->set_position($this->organisation_unit->get_position()) == false)
					{
						if ($transaction_id != null)
						{
							$transaction->rollback($transaction_id);
						}
						return false;
					}
					
					if ($this->organisation_unit->set_position($upper_position) == false)
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
	 * Moves the OU one position downwards
	 * @return bool
	 */
	public function position_downwards()
	{
		global $transaction;
		
		if ($this->organisation_unit and $this->organisation_unit_id)
		{
			if ($this->is_lower_position() == false)
			{
				$lower_position = $this->organisation_unit->get_position()+1;
				
				if ($this->organisation_unit->get_is_root() == true)
				{
					$id = OrganisationUnit_Access::get_id_by_position_and_toid($lower_position, null);
				}
				else
				{
					$id = OrganisationUnit_Access::get_id_by_position_and_toid($lower_position, $this->organisation_unit->get_toid());
				}
				
				if (is_numeric($id))
				{
					$transaction_id = $transaction->begin();
					
					$change_organisation_unit = new OrganisationUnit_Access($id);
					if ($change_organisation_unit->set_position($this->organisation_unit->get_position()) == false)
					{
						if ($transaction_id != null)
						{
							$transaction->rollback($transaction_id);
						}
						return false;
					}
					
					if ($this->organisation_unit->set_position($lower_position) == false)
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
	 * Checks if an User is in the OU
	 * @param integer $user_id User-ID
	 * @return bool
	 */
	public function is_user_in_organisation_unit($user_id)
	{
		if (is_numeric($user_id))
		{
			$user_array = OrganisationUnitHasMember_Access::list_users_by_organisation_unit_id($this->organisation_unit_id);
			if (in_array($user_id, $user_array))
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
	 * Creates an User in the OU
	 * @param integer $user_id User-ID
	 * @return bool
	 */
	public function create_user_in_organisation_unit($user_id)
	{
		if ($this->organisation_unit_id)
		{
			if ($this->is_user_in_organisation_unit($user_id) == false)
			{
				$organisation_unit_has_user = new OrganisationUnitHasMember_Access(null);
				if ($organisation_unit_has_user->create($this->organisation_unit_id, $user_id) != null)
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
		else
		{
			return false;
		}
	}
	
	/**
	 * Deletes an User from the OU
	 * @param integer $user_id User-ID
	 * @return bool
	 */
	public function delete_user_from_organisation_unit($user_id)
	{
		if ($this->organisation_unit_id)
		{
			if ($this->is_user_in_organisation_unit($user_id) == true)
			{
				$primary_key = OrganisationUnitHasMember_Access::get_pk_by_user_id_and_organisation_unit_id($user_id, $this->organisation_unit_id);
				$organisation_unit_has_user = new OrganisationUnitHasMember_Access($primary_key);
				if ($organisation_unit_has_user->delete() == true)
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
		else
		{
			return false;
		}
	}

	/**
	 * Checks if a Group is in the OU
	 * @param integer $group_id Group-ID
	 * @return bool
	 */
	public function is_group_in_organisation_unit($group_id)
	{
		if (is_numeric($group_id))
		{
			$group_array = OrganisationUnitHasGroup_Access::list_groups_by_organisation_unit_id($this->organisation_unit_id);
			if (in_array($group_id, $group_array))
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
	 * Creates a Group in the OU
	 * @param integer $group_id Group-ID
	 * @return bool
	 */
	public function create_group_in_organisation_unit($group_id)
	{
		global $transaction;
		
		if ($this->organisation_unit_id)
		{
			$transaction_id = $transaction->begin();
			
			if ($this->is_group_in_organisation_unit($group_id) == false)
			{
				$organisation_unit_has_group = new OrganisationUnitHasGroup_Access(null);
				if ($organisation_unit_has_group->create($this->organisation_unit_id, $group_id) != null)
				{
					$project_array = ProjectPermission::list_system_setted_projects_by_organisation_id($this->organisation_unit_id);
					
					if (is_array($project_array) and count($project_array) >= 1)
					{
						foreach($project_array as $key => $value)
						{
							$project_permission = new ProjectPermission(null);
							if ($project_permission->create(null, null, $group_id, $value, $GLOBALS[std_perm_organ_group], null, 4) == null)
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
	 * Deletes a Group from the OU
	 * @param integer $group_id Group-ID
	 * @return bool
	 */
	public function delete_group_from_organisation_unit($group_id)
	{
		global $transaction;
		
		if ($this->organisation_unit_id)
		{
			$transaction_id = $transaction->begin();
			
			if ($this->is_group_in_organisation_unit($group_id) == true)
			{
				$primary_key = OrganisationUnitHasGroup_Access::get_pk_by_group_id_and_organisation_unit_id($group_id, $this->organisation_unit_id);
				$organisation_unit_has_group = new OrganisationUnitHasGroup_Access($primary_key);
				
				if ($organisation_unit_has_group->delete() == true)
				{
					$project_array = ProjectPermission::list_system_setted_projects_by_organisation_id($this->organisation_unit_id);
					
					if (is_array($project_array) and count($project_array) >= 1)
					{
						foreach($project_array as $key => $value)
						{
							$project_permission_array = ProjectPermission::list_entries_by_project_id_and_intention_and_group_id($value, 4, $group_id);
							if (is_array($project_permission_array) and count($project_permission_array) >= 1)
							{
								foreach($project_permission_array as $sub_key => $sub_value)
								{
									$project_permission = new ProjectPermission($sub_value);
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
						}
					}
					
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
	 * @return string Type-Name
	 */
	public function get_type_name()
	{
		if ($this->organisation_unit)
		{
			$organisation_unit_type = new OrganisationUnitType_Access($this->organisation_unit->get_type_id());
			return $organisation_unit_type->get_name();
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return string OU-Name
	 */
	public function get_name()
	{
		if ($this->organisation_unit)
		{
			return $this->organisation_unit->get_name();
		}
		else
		{
			return null;
		}
	}

	/**
	 * @return string Icon
	 */
	public function get_icon()
	{
		if ($this->organisation_unit)
		{
			$organisation_unit_type = new OrganisationUnitType_Access($this->organisation_unit->get_type_id());
			return $organisation_unit_type->get_icon();
		}
		else
		{
			return null;
		}
	}

	/**
	 * @return integer Owner-ID
	 */
	public function get_owner_id()
	{
		if ($this->organisation_unit)
		{
			return $this->organisation_unit->get_owner_id();
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return integer Leader-ID
	 */
	public function get_leader_id()
	{
		if ($this->organisation_unit)
		{
			return $this->organisation_unit->get_leader_id();
		}
		else
		{
			return null;
		}
	}

	/**
	 * @return bool Contains-Projects
	 */
	public function get_contains_projects()
	{
		if ($this->organisation_unit)
		{
			return $this->organisation_unit->get_contains_projects();
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return bool Hidden
	 */
	public function get_hidden()
	{
		if ($this->organisation_unit)
		{
			return $this->organisation_unit->get_hidden();
		}
		else
		{
			return null;
		}
	}

	/**
	 * Return the status of an user
	 * @param integer $user_id
	 * @return string status
	 */
	public function get_user_status($user_id)
	{
		if ($this->organisation_unit->get_leader_id() == $user_id)
		{			
			return "Leader";
		}
		else
		{
			return "Member";
		}		
	}

	/**
	 * @return array Array of childs
	 */
	public function get_organisation_unit_childs()
	{
		if ($this->organisation_unit_id)
		{
			return OrganisationUnit_Access::list_entries_by_toid($this->organisation_unit_id);
		}
		else
		{
			return null;	
		}
	}
	
	/**
	 * @return integer Number of members of the OU
	 */
	public function get_number_of_members()
	{
		if ($this->organisation_unit_id)
		{
			$number_of_users = OrganisationUnitHasMember_Access::count_users_by_organisation_unit_id($this->organisation_unit_id);
			if (is_numeric($number_of_users))
			{
				return $number_of_users;
			}
			else
			{
				return 0;
			}
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return integer Number of groups of the OU
	 */
	public function get_number_of_groups()
	{
		if ($this->organisation_unit_id)
		{
			$number_of_groups = OrganisationUnitHasGroup_Access::count_groups_by_organisation_unit_id($this->organisation_unit_id);
			if (is_numeric($number_of_groups))
			{
				return $number_of_groups;
			}
			else
			{
				return 0;
			}
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return array Array of all members
	 */
	public function list_members()
	{
		if ($this->organisation_unit_id)
		{
			return OrganisationUnitHasMember_Access::list_users_by_organisation_unit_id($this->organisation_unit_id);	
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return array Array of all groups
	 */
	public function list_groups()
	{
		if ($this->organisation_unit_id)
		{
			return OrganisationUnitHasGroup_Access::list_groups_by_organisation_unit_id($this->organisation_unit_id);
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @param string $name New OU-Name
	 * @return bool
	 */
	public function set_name($name)
	{
		global $transaction;
		
		if ($this->organisation_unit and $this->organisation_unit_id and $name)
		{
			$transaction_id = $transaction->begin();
			
			$folder_id = Folder::get_organisation_unit_folder_by_organisation_unit_id($this->organisation_unit_id);
			$folder = new Folder($folder_id);
			if ($folder->set_name($name) == true)
			{
				if ($this->organisation_unit->set_name($name) == true)
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
	 * @param integer $owner_id New Owner-ID
	 * @return bool
	 */
	public function set_owner_id($owner_id)
	{
		global $transaction;
		
		if ($this->organisation_unit)
		{
			$transaction_id = $transaction->begin();
			
			$folder_id = Folder::get_organisation_unit_folder_by_organisation_unit_id($this->organisation_unit_id);
			$folder = new Folder($folder_id);
			
			if ($folder->set_owner_id($owner_id) == true)
			{
				if ($this->organisation_unit->set_owner_id($owner_id) == true)
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
	 * @param interger $leader_id New Leader-ID
	 * @return bool
	 */
	public function set_leader_id($leader_id)
	{
		global $transaction;
		
		if ($this->organisation_unit)
		{
			if (is_numeric($leader_id))
			{
				$transaction_id = $transaction->begin();
				
				$current_leader_id = $this->organisation_unit->get_leader_id();
				
				// Alle Projektrechte ändern
				$project_permission_array = ProjectPermission::list_system_setted_entries_by_leader_id($current_leader_id);
				
				if (is_array($project_permission_array) and count($project_permission_array) >= 1)
				{
					foreach($project_permission_array as $key => $value)
					{
						$project_permission = new ProjectPermission($value);
						
						if ($project_permission->set_user_id($leader_id) == false)
						{
							if ($transaction_id != null)
							{
								$transaction->rollback($transaction_id);
							}
							return false;
						}							
					}
				}
			
				// Leader Ändern
				if ($this->organisation_unit->set_leader_id($leader_id) == false)
				{
					if ($transaction_id != null)
					{
						$transaction->rollback($transaction_id);
					}
					return false;
				}
			
				// Alten User Prüfen
				
				$group = new Group(constant("GROUP_LEADER_GROUP"));
				
				if (self::is_leader($current_leader_id) == false)
				{
					if ($group->is_user_in_group($current_leader_id) == true)
					{
						if($group->delete_user_from_group($current_leader_id) == false)
						{
							if ($transaction_id != null)
							{
								$transaction->rollback($transaction_id);
							}
							return false;
						}
					}
				}
				
				// Neuer User in Group
				if (defined("GROUP_LEADER_GROUP"))
				{
					if ($group->is_user_in_group($leader_id) == false)
					{
						if($group->create_user_in_group($leader_id) == false)
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
					if ($transaction_id != null)
					{
						$transaction->rollback($transaction_id);
					}
					return false;
				}
				
				if ($transaction_id != null)
				{
					$transaction->commit($transaction_id);
				}
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
	 * @param bool $hidden
	 * @return bool
	 */
	public function set_hidden($hidden)
	{
		if ($this->organisation_unit and $this->organisation_unit_id and is_bool($hidden))
		{
			return $this->organisation_unit->set_hidden($hidden);
		}
		else
		{
			return false;
		}
	}
	

	/**
	 * Deletes all members of an OU
	 * @return bool
	 */
	private function delete_members()
	{
		if ($this->organisation_unit_id)
		{
			return OrganisationUnitHasMember_Access::delete_by_organisation_unit_id($this->organisation_unit_id);
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * Deletes all groups of an OU
	 * @return bool
	 */
	private function delete_groups()
	{
		if ($this->organisation_unit_id)
		{
			return OrganisationUnitHasGroup_Access::delete_by_organisation_unit_id($this->organisation_unit_id);
		}
		else
		{
			return false;
		}
	}
	
	
	/**
	 * @param integer $user_id User-ID
	 * @return integer
	 */
	public static function get_number_of_organisation_units_by_user_id($user_id)
	{
		$number_of_organisation_units = OrganisationUnitHasMember_Access::count_organisation_units_by_user_id($user_id);
		if (is_numeric($number_of_organisation_units))
		{
			return $number_of_organisation_units;
		}
		else
		{
			return 0;
		}
	}	
	
	/**
	 * @param integer $group_id Group-ID
	 * @return integer
	 */
	public static function get_number_of_organisation_units_by_group_id($group_id)
	{
		$number_of_organisation_units = OrganisationUnitHasGroup_Access::count_organisation_units_by_group_id($group_id);
		if (is_numeric($number_of_organisation_units))
		{
			return $number_of_organisation_units;
		}
		else
		{
			return 0;
		}
	}
	
	/**
	 * @param interger $type_id
	 * @return string Name of the Type
	 */
	public static function get_name_by_type_id($type_id)
	{
		if (is_numeric($type_id))
		{
			$organisation_unit_type = new OrganisationUnitType_Access($type_id);
			return $organisation_unit_type->get_name();
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * Checks if an OU exists
	 * @param string $name
	 * @return bool
	 */
	public static function exist_name($name)
	{
		$name = trim(strtolower($name));
		if (OrganisationUnit_Access::get_organisation_unit_id_by_name($name) != null)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * Checks if an OU exists
	 * @param integer $id Organisation-Unit-ID
	 * @return bool
	 */
	public static function exist_organisation_unit($id)
	{
		return OrganisationUnit_Access::exist_organisation_unit($id);
	}
	
	/**
	 * @return array List of root OUs
	 */		
	public static function list_organisation_unit_roots()
	{
		return OrganisationUnit_Access::list_root_entries();
	}
	
	/**
	 * @return array List of OUs
	 */
	public static function list_entries()
	{
		return OrganisationUnit_Access::list_entries();
	}
	
	/**
	 * @param integer $user_id User-ID
	 * @return array List of user OUs
	 */
	public static function list_entries_by_user_id($user_id)
	{
		return OrganisationUnitHasMember_Access::list_organisation_units_by_user_id($user_id);
	}
	
	/***
	 * @param integer $group_id Group-ID
	 * @return array List of group OUs
	 */
	public static function list_entries_by_group_id($group_id)
	{
		return OrganisationUnitHasGroup_Access::list_organisation_units_by_group_id($group_id);
	}
	
	/**
	 * @param integer $owner_id User-ID
	 * @return array List of owner OUs
	 */
	public static function list_entries_by_owner_id($owner_id)
	{
		return OrganisationUnit_Access::list_entries_by_owner_id($owner_id);
	}
	
	/**
	 * @param integer $leader_id User-ID
	 * @return array List of leader OUs
	 */
	public static function list_entries_by_leader_id($leader_id)
	{
		return OrganisationUnit_Access::list_entries_by_leader_id($leader_id);
	}
	
	/**
	 * @return array Array of OU-Types
	 */
	public static function list_types()
	{
		return OrganisationUnitType_Access::list_entries();
	}
	
	/**
	 * Deletes an user from all OUs
	 * @param integer $user_id User-ID
	 * @return bool
	 */
	public static function delete_members_by_user_id($user_id)
	{
		return OrganisationUnitHasMember_Access::delete_by_user_id($user_id);
	}
	
	/**
	 * Deletes a group from all OUs
	 * @param integer $group_id Group-ID
	 * @return bool
	 */
	public static function delete_groups_by_group_id($group_id)
	{
		return OrganisationUnitHasGroup_Access::delete_by_group_id($group_id);
	}
	
	/**
	 * Checks if an User is a leader of any OU
	 * @param integer $user_id User-ID
	 * @return bool
	 */	
	private static function is_leader($user_id)
	{
		return OrganisationUnit_Access::is_leader($user_id);
	}
	
	/**
   	 * @return integer
   	 */
   	public static function count_organisation_units()
   	{
   		return OrganisationUnit_Access::count_organisation_units();
   	}
	
	// --- TREE REC BLOCK BEGIN ---

	/**
	 * Creates an Tree of OUs recursively
	 * @param integer $id Organisation-Unit-ID
	 * @return array Array of Childs
	 */
	private static function make_organisation_unit_tree_path_rec($id)
	{
		if ($id)
		{
			$return_array = array();
			array_unshift($return_array,$id);
			
			$organisation_unit_access = new OrganisationUnit_Access($id);
			
			if ($organisation_unit_access->get_is_root() == false)
			{
				$return_array = array_merge(self::make_organisation_unit_tree_path_rec($organisation_unit_access->get_toid()),$return_array);
			}
						
			return $return_array;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * Create an Full Tree of OUs recursively
	 * @param interger $layer Layer of current run
	 * @param array $array Creates OU tree
	 * @return array
	 */
	private static function make_full_organisation_unit_tree_rec($layer, $array)
	{
		if (is_array($array))
		{
			$return_array = array();
			
			$organisation_unit_access = new OrganisationUnit_Access(null);
			$organisation_unit_access_array = $organisation_unit_access->list_entries_by_toid($array[$layer]);
			
			foreach($organisation_unit_access_array as $key => $value)
			{			
				if ($value != $array[$layer])
				{	
					$data_array = array();
					$data_array[id] = $value;
					$data_array[layer] = $layer+1;	
						
					array_push($return_array,$data_array);
					
					unset($data_array);
					
					if ($value == $array[$layer+1])
					{
						$return_array = array_merge($return_array,self::make_full_organisation_unit_tree_rec($layer+1, $array));
					}
				}
			}
			return $return_array;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * Return the recursively created tree
	 * @return array
	 */
	public function get_organisation_unit_tree()
	{
		$tree_array = $this->make_organisation_unit_tree_path_rec($this->organisation_unit_id);

		$return_array = array();
	
		$organisation_unit_access_array = OrganisationUnit_Access::list_root_entries();
			
		foreach($organisation_unit_access_array as $key => $value)
		{
			$data_array = array();
			$data_array[id] = $value;
			$data_array[layer] = 0;
			
			array_push($return_array, $data_array);
			
			unset($data_array);
			if (is_array($tree_array))
			{
				if (in_array($value,$tree_array))
				{
					$return_array = array_merge($return_array,self::make_full_organisation_unit_tree_rec(0, $tree_array));
				}
			}
		}
		return $return_array;
	}
	
	// --- TREE REC BLOCK END ---
	   
    /**
     * @todo implementation
     */
    public static function listen_events($event_object)
    {
    	
    }
    
}
?>