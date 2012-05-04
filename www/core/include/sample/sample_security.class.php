<?php
/**
 * @package sample
 * @version 0.4.0.0
 * @author Roman Konertz <konertz@open-lims.org>
 * @copyright (c) 2008-2012 by Roman Konertz
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
require_once("interfaces/sample_security.interface.php");

if (constant("UNIT_TEST") == false or !defined("UNIT_TEST"))
{
	require_once("access/sample_has_organisation_unit.access.php");
	require_once("access/sample_has_user.access.php");
}

/**
 * Sample Security Management Class
 * @package sample
 */
class SampleSecurity implements SampleSecurityInterface, EventListenerInterface
{
	private $sample_id;

	/**
	 * @see SampleSecurityInterface::__construct()
	 * @param integer $sample_id
	 */
    function __construct($sample_id)
    {
    	if ($sample_id == null)
    	{
    		$this->sample_id = null;
    	}
    	else
    	{
    		$this->sample_id = $sample_id;
    	}
    }
    
    function __destruct()
    {
    	unset($this->sample_id);
    }
    
   	/**
   	 * @see SampleSecurityInterface::is_access()
   	 * @param integer $intention 1 = Read, 2 = Write
   	 * @param bool $ignore_admin_status
   	 * @return bool
   	 */
    public function is_access($intention, $ignore_admin_status)
    {
    	global $user;
    	
    	if ($this->sample_id)
    	{
	    	if ($user->is_admin() and $ignore_admin_status == false)
	    	{
	    		return true;
	    	}
	    	else
	    	{
	    		$sample = new Sample($this->sample_id);
	    		$sample_folder_id = SampleFolder::get_folder_by_sample_id($this->sample_id);
	    		$sample_folder_data_entity = new DataEntity(Folder::get_data_entity_id_by_folder_id($sample_folder_id));
	    	
	    		if ($sample->get_owner_id() == $user->get_user_id())
	    		{
	    			return true;
	    		}
	    		else
	    		{
	    			
		    		$pk = SampleHasUser_Access::get_entry_by_sample_id_and_user_id($this->sample_id, $user->get_user_id());
		    		$sample_has_user = new SampleHasUser_Access($pk);
	    			
	    			if ($intention == 1)
	    			{
	    				if ($sample_has_user->get_read() == true)
	    				{
	    					return true;
	    				}
	    				else
	    				{
	    					
	    					$organisation_unit_array = OrganisationUnit::list_entries_by_user_id($user->get_user_id());
	    					
	    					if (is_array($organisation_unit_array) and count($organisation_unit_array) >= 1)
	    					{
	    						foreach($organisation_unit_array as $key => $value)
	    						{
	    							$pk = SampleHasOrganisationUnit_Access::get_entry_by_sample_id_and_organisation_unit_id($this->sample_id, $value);
		    						if (is_numeric($pk))
		    						{
		    							return true;
		    						}
	    						}
	    					}
/*
	    					$parent_virtual_folder_array = $sample_folder_data_entity->get_parent_virtual_folder_ids();
	    					
	    					if (is_array($parent_virtual_folder_array) and count($parent_virtual_folder_array) >= 1)
	    					{
	    						foreach($parent_virtual_folder_array as $key => $value)
	    						{
	    							$virtual_folder = new VirtualFolder($value);
	    							$parent_folder_id = $virtual_folder->get_parent_folder_id();
	    							$folder = Folder::get_instance($parent_folder_id);
	    							if ($folder->is_read_access() == true)
	    							{
	    								return true;
	    							}
	    						}
	    					} */
	    					
	    					return false;
	    				}
	    			
	    			}
	    			else
	    			{
	    				if ($sample_has_user->get_write() == true)
	    				{
	    					return true;
	    				}
	    				else
	    				{
	    					return false;
	    				}
	    			}
	    		}
	    	}
    	}
    	else
    	{
    		return null;
    	}
    }
    
    /**
     * @see SampleSecurityInterface::get_access_string()
     * @return string
     */
    public function get_access_string()
    {
    	global $user;
    	
    	if ($this->sample_id)
    	{
	    		
	    	if ($this->is_access(1, false))
	    	{
	    		$string = "Read";
	    		
	    		if ($this->is_access(2, false))
	    		{
	    			return $string.", Write";
	    		}
	    		else
	    		{
	    			return $string;
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
 	 * @see SampleSecurityInterface::get_access_by_user_id()
 	 * @param integer $user_id
 	 * @return array
 	 */
    public function get_access_by_user_id($user_id)
    {
    	if ($this->sample_id)
    	{
    		$sample_has_user = new SampleHasUser_Access(null);
    		$pk = SampleHasUser_Access::get_entry_by_sample_id_and_user_id($this->sample_id, $user_id);
    		$sample_has_user = new SampleHasUser_Access($pk);
    	
    		$return_array = array();
    		
    		if ($sample_has_user->get_read() == true)
    		{
    			$return_array[read] = true;
    		}
    		else
    		{
    			$return_array[read] = false;
    		}
    		
    		if ($sample_has_user->get_write() == true)
    		{
    			$return_array[write] = true;
    		}
    		else
    		{
    			$return_array[write] = false;
    		}
    		return $return_array;
    	}
    	else
    	{
    		return null;
    	}
    }
    
    /**
     * @see SampleSecurityInterface::get_entry_by_user_id()
     * @param integer $user_id
     * @return integer
     */
    public function get_entry_by_user_id($user_id)
    {
    	if (is_numeric($user_id))
    	{
    		return SampleHasUser_Access::get_entry_by_sample_id_and_user_id($this->sample_id, $user_id);
    	}
    	else
    	{
    		return null;
    	}
    }
    
    /**
     * @see SampleSecurityInterface::get_entry_by_organisation_unit_id()
     * @param integer $organisation_unit_id
     * @return integer
     */
    public function get_entry_by_organisation_unit_id($organisation_unit_id)
    {
    	if (is_numeric($organisation_unit_id))
    	{
    		return SampleHasOrganisationUnit_Access::get_entry_by_sample_id_and_organisation_unit_id($this->sample_id ,$organisation_unit_id);
    	}
    	else
    	{
    		return null;
    	}
    }
    
    /**
     * @see SampleSecurityInterface::create_user()
     * @param integer $user_id
     * @param bool $read
     * @param bool $write
     * @return integer
     */
    public function create_user($user_id, $read, $write)
    {
    	global $transaction;
    	
    	if ($this->sample_id)
    	{
    		if (is_numeric($user_id) and isset($read) and isset($write))
    		{
    			$transaction_id = $transaction->begin();
    		
	    		$sample_has_user = new SampleHasUser_Access(null);
	    		if (($sample_has_user_pk = $sample_has_user->create($this->sample_id, $user_id, $read, $write)) != null)
	    		{
	    			if ($write == true)
	    			{
		    			$sample_folder_id = SampleFolder::get_folder_by_sample_id($this->sample_id);
		    			
		    			$folder_id = UserFolder::get_folder_by_user_id($user_id);
		    			
		    			$virtual_folder = new VirtualFolder(null);
		    			$virtual_folder_array = $virtual_folder->list_entries_by_folder_id($folder_id);
		    			
		    			foreach($virtual_folder_array as $key => $value)
		    			{
		    				$virtual_folder = new SampleVirtualFolder($value);
		    				if ($virtual_folder->is_sample_vfolder() == true)
		    				{
		    					$virtual_folder_id = $value;
		    				}
		    			}
		    			
		    			if ($virtual_folder_id)
		    			{
		    				$virtual_folder = new VirtualFolder($virtual_folder_id);
		    				if ($virtual_folder->link_folder($sample_folder_id) == false)
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
	    			return $sample_has_user_pk;	
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
    	else
    	{
    		return null;
    	}
    }
    
    /**
     * @see SampleSecurityInterface::create_organisation_unit()
     * @param integer $organisation_unit_id
     * @return integer
     */
    public function create_organisation_unit($organisation_unit_id)
    {
    	global $transaction;
    	
    	if ($this->sample_id)
    	{
    		$transaction_id = $transaction->begin();
    		
    		$sample_has_organisation_unit = new SampleHasOrganisationUnit_Access(null);
    		if (($sample_has_organisation_unit_pk = $sample_has_organisation_unit->create($this->sample_id, $organisation_unit_id)) != null)
    		{
    			$sample_folder_id = SampleFolder::get_folder_by_sample_id($this->sample_id);
    			
    			$folder_id = OrganisationUnitFolder::get_folder_by_organisation_unit_id($organisation_unit_id);
    			
    			$virtual_folder = new VirtualFolder(null);
    			$virtual_folder_array = $virtual_folder->list_entries_by_folder_id($folder_id);
    			
    			if (is_array($virtual_folder_array))
    			{
	    			foreach($virtual_folder_array as $key => $value)
	    			{
	    				$virtual_folder = new SampleVirtualFolder($value);
	    				if ($virtual_folder->is_sample_vfolder() == true)
	    				{
	    					$virtual_folder_id = $value;
	    				}
	    			}
    			}
    			   			
    			if ($virtual_folder_id)
    			{
    				$virtual_folder = new VirtualFolder($virtual_folder_id);
    				if ($virtual_folder->link_folder($sample_folder_id) == false)
    				{
    					if ($transaction_id != null)
    					{
							$transaction->rollback($transaction_id);
						}
						return null;
    				}
    			}
    			
    			$organisation_unit = new OrganisationUnit($organisation_unit_id);
    			$group_array = $organisation_unit->list_groups();

				if(is_array($group_array) and count($group_array) >= 1)
				{
					foreach($group_array as $key => $value)
					{
						$folder_id = GroupFolder::get_folder_by_group_id($value);
		    			
		    			$virtual_folder = new VirtualFolder(null);
		    			$virtual_folder_array = $virtual_folder->list_entries_by_folder_id($folder_id);
		    			
		    			if (is_array($virtual_folder_array))
		    			{
			    			foreach($virtual_folder_array as $key => $value)
			    			{
			    				$virtual_folder = new SampleVirtualFolder($value);
			    				if ($virtual_folder->is_sample_vfolder() == true)
			    				{
			    					$virtual_folder_id = $value;
			    				}
			    			}
		    			}
		    			
		    			if (is_numeric($virtual_folder_id))
		    			{
		    				$virtual_folder = new VirtualFolder($virtual_folder_id);
		    				if ($virtual_folder->link_folder($sample_folder_id) == false)
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
    			
    			if ($transaction_id != null)
    			{
					$transaction->commit($transaction_id);
				}
    			return $sample_has_organisation_unit_pk;
    			
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
     * @see SampleSecurityInterface::delete_user()
     * @param integer $entry_id
     * @return bool
     */
    public function delete_user($entry_id)
    {
    	global $transaction;
    	
    	if ($this->sample_id and is_numeric($entry_id))
    	{
    		$transaction_id = $transaction->begin();
    		
    		$sample_has_user = new SampleHasUser_Access($entry_id);
    		$write = $sample_has_user->get_write();
    		$user_id = $sample_has_user->get_user_id();
    		$success = $sample_has_user->delete();
    		
    		if ($success == true)
    		{
    			if ($write == true)
    			{
	    			$sample_folder_id = SampleFolder::get_folder_by_sample_id($this->sample_id);
	    			
	    			$folder_id = UserFolder::get_folder_by_user_id($user_id);
	    			
	    			$virtual_folder = new VirtualFolder(null);
	    			$virtual_folder_array = $virtual_folder->list_entries_by_folder_id($folder_id);
	    			
	    			foreach($virtual_folder_array as $key => $value)
	    			{
	    				$virtual_folder = new SampleVirtualFolder($value);
	    				if ($virtual_folder->is_sample_vfolder() == true)
	    				{
	    					$virtual_folder_id = $value;
	    				}
	    			}
	    			   			
	    			if ($virtual_folder_id)
	    			{
	    				$virtual_folder = new VirtualFolder($virtual_folder_id);
	    				if ($virtual_folder->unlink_folder($sample_folder_id) == false)
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
    		return false;
    	}
    }
    
    /**
     * @see SampleSecurityInterface::delete_organisation_unit()
     * @param integer $entry_id
     * @return bool
     */
    public function delete_organisation_unit($entry_id)
    {
    	global $transaction;
    	
    	if ($this->sample_id and is_numeric($entry_id))
    	{
    		$transaction_id = $transaction->begin();
    		
    		$sample_has_organisation_unit = new SampleHasOrganisationUnit_Access($entry_id);
    		$organisation_unit_id = $sample_has_organisation_unit->get_organisation_unit_id();
    		$success = $sample_has_organisation_unit->delete();
    		
    		if ($success == true)
    		{
    			$sample_folder_id = SampleFolder::get_folder_by_sample_id($this->sample_id);
    			
    			$folder_id = OrganisationUnitFolder::get_folder_by_organisation_unit_id($organisation_unit_id);
    			
    			$virtual_folder = new VirtualFolder(null);
    			$virtual_folder_array = $virtual_folder->list_entries_by_folder_id($folder_id);
    			
    			foreach($virtual_folder_array as $key => $value)
    			{
    				$virtual_folder = new SampleVirtualFolder($value);
    				if ($virtual_folder->is_sample_vfolder() == true)
    				{
    					$virtual_folder_id = $value;
    				}
    			}
    			   			
    			if ($virtual_folder_id)
    			{
    				$virtual_folder = new VirtualFolder($virtual_folder_id);
    				if ($virtual_folder->unlink_folder($sample_folder_id) == false)
    				{
    					if ($transaction_id != null)
    					{
							$transaction->rollback($transaction_id);
						}
    					return false;
    				}
    			}
    			
    			$organisation_unit = new OrganisationUnit($organisation_unit_id);
    			$group_array = $organisation_unit->list_groups();

				if(is_array($group_array) and count($group_array) >= 1)
				{
					foreach($group_array as $key => $value)
					{
						$folder_id = GroupFolder::get_folder_by_group_id($value);
		    			
		    			$virtual_folder = new VirtualFolder(null);
		    			$virtual_folder_array = $virtual_folder->list_entries_by_folder_id($folder_id);
		    			
		    			foreach($virtual_folder_array as $key => $value)
		    			{
		    				$virtual_folder = new SampleVirtualFolder($value);
		    				if ($virtual_folder->is_sample_vfolder() == true)
		    				{
		    					$virtual_folder_id = $value;
		    				}
		    			}
		    			
		    			if (is_numeric($virtual_folder_id))
		    			{
		    				$virtual_folder = new VirtualFolder($virtual_folder_id);
		    				if ($virtual_folder->unlink_folder($sample_folder_id) == false)
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
     * @todo PERFORMANCE: avoid foreach
     * @see SampleSecurityInterface::list_users()
     * @return array
     */
    public function list_users()
    {
    	if ($this->sample_id)
    	{
    		$sample_has_user = new SampleHasUser_Access(null);
    		$sample_has_user_pk_array = SampleHasUser_Access::list_entries_by_sample_id($this->sample_id);
    		
    		if (is_array($sample_has_user_pk_array) and count($sample_has_user_pk_array) >= 1)
    		{
    			$return_array = array();
    			
    			foreach($sample_has_user_pk_array as $key => $value)
    			{
    				$sample_has_user = new SampleHasUser_Access($value);
    				array_push($return_array, $sample_has_user->get_user_id());	
    			}
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
   	 * @todo PERFORMANCE: avoid foreach
   	 * @see SampleSecurityInterface::list_organisation_units()
   	 * @return array
   	 */
    public function list_organisation_units()
    {
    	if ($this->sample_id)
    	{
    		$sample_has_organisation_unit_pk_array = SampleHasOrganisationUnit_Access::list_entries_by_sample_id($this->sample_id);
    		
    		if (is_array($sample_has_organisation_unit_pk_array) and count($sample_has_organisation_unit_pk_array) >= 1)
    		{
    			$return_array = array();
    			
    			foreach($sample_has_organisation_unit_pk_array as $key => $value)
    			{
    				$sample_has_organisation_unit = new SampleHasOrganisationUnit_Access($value);
    				array_push($return_array, $sample_has_organisation_unit->get_organisation_unit_id());	
    			}
    			
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
     * @see SampleSecurityInterface::list_user_entries()
     * @return array
     */
    public function list_user_entries()
    {
    	if ($this->sample_id)
    	{
    		$sample_has_user_pk_array = SampleHasUser_Access::list_entries_by_sample_id($this->sample_id);
    		
    		if (is_array($sample_has_user_pk_array) and count($sample_has_user_pk_array) >= 1)
    		{  			
    			return $sample_has_user_pk_array;
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
     * @see SampleSecurityInterface::list_organisation_unit_entries()
     * @return array
     */
    public function list_organisation_unit_entries()
    {
    	if ($this->sample_id)
    	{
    		$sample_has_organisation_unit_pk_array = SampleHasOrganisationUnit_Access::list_entries_by_sample_id($this->sample_id);

    		if (is_array($sample_has_organisation_unit_pk_array) and count($sample_has_organisation_unit_pk_array) >= 1)
    		{   			
    			return $sample_has_organisation_unit_pk_array;
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
     * @see SampleSecurityInterface::is_user()
     * @param integer $user_id
     * @return bool
     */
    public function is_user($user_id)
    {    	
    	if ($this->sample_id and is_numeric($user_id))
    	{
    		$pk = SampleHasUser_Access::get_entry_by_sample_id_and_user_id($this->sample_id, $user_id);
    		
    		if ($pk)
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
    		return true;
    	}
    }
    
    /**
     * @see SampleSecurityInterface::is_organisation_unit()
     * @param integer $organisation_unit_id
     * @return bool
     */
    public function is_organisation_unit($organisation_unit_id)
    {
    	if ($this->sample_id and is_numeric($organisation_unit_id))
    	{
    		$pk = SampleHasOrganisationUnit_Access::get_entry_by_sample_id_and_organisation_unit_id($this->sample_id, $organisation_unit_id);
    		
    		if ($pk)
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
    		return true;
    	}
    }
    
    /**
     * @see SampleSecurityInterface::delete_organisation_complete()
     * Warning: This method is for organisation-unit-deletion only!
     * 			Outside organisation-unit-deletion is causes logical inconsistency!
     * @param integer $organisation_unit_id
     * @return bool
     */
    public static function delete_organisation_complete($organisation_unit_id)
    {
    	return SampleHasOrganisationUnit_Access::delete_by_organisation_unit_id($organisation_unit_id);
    }
    
    /**
     * @param object $event_object
     * @return bool
     */
    public static function listen_events($event_object)
    {
    	if ($event_object instanceof UserDeleteEvent)
    	{
    		if (SampleHasUser_Access::delete_by_user_id($event_object->get_user_id()) == false)
			{
				return false;
			}
    	}
    	
    	return true;
    }
}
?>