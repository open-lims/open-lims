<?php
/**
 * @package sample
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
require_once("interfaces/sample.interface.php");

if (constant("UNIT_TEST") == false or !defined("UNIT_TEST"))
{
	require_once("access/sample.access.php");
	require_once("access/sample_is_item.access.php");
	require_once("access/sample_has_location.access.php");
	require_once("access/sample_has_organisation_unit.access.php");
	require_once("access/sample_has_user.access.php");
}

/**
 * Sample Management Class
 * @package sample
 */
class Sample extends Item implements SampleInterface, EventListenerInterface, ItemListenerInterface, ItemHolderInterface
{
	private $sample;
	
	private $sample_id;
	
	private $template_data_type;
	private $template_data_type_id;
	private $template_data_array;

	private $sample_folder_id;
	private $sample_folder_object;
	
	private static $sample_delete_array = array();
	
	/**
	 * @see SampleInterface::__construct()
	 * @param integer $sample_id Sample-ID
	 * @throws SampleNotFoundException
	 */
    function __construct($sample_id)
    {
    	if (is_numeric($sample_id))
		{
			if (Sample_Access::exist_sample_by_sample_id($sample_id) == true)
			{
				$this->sample_id = $sample_id;
	    		$this->sample = new Sample_Access($sample_id);
	    		
	    		$sample_is_item = new SampleIsItem_Access($sample_id);
	    		$this->item_id = $sample_is_item->get_item_id();
	    		parent::__construct($this->item_id);
			}
			else
			{
				if (in_array($sample_id, self::$sample_delete_array) == false)
				{
					throw new SampleNotFoundException();
				}
			}
		}
		else
		{
			$this->sample_id = null;
    		$this->sample = new Sample_Access(null);
    		parent::__construct(null);
		}
    }
    
    function __destruct()
    {
    	if ($this->sample_id)
    	{
    		unset($this->sample_id);
    		unset($this->sample);
    	}
    	else
    	{
    		unset($this->sample);
    	}
    }
    
    /**
     * @see SampleInterface::set_template_data()
     * @param string $type
     * @param integer $type_id
     * @param array $array
     * @return bool
     */
    public function set_template_data($type, $type_id, $array)
    {
    	if (($type == "sample" or $type == "value") and is_array($array))
    	{
    		$this->template_data_type = $type;
    		$this->template_data_type_id = $type_id;
    		$this->template_data_array = $array;
    		return true;
    	}
    	else
    	{
    		return false;
    	} 	
    }
    
    /**
     * @param string $transaction_id
     * @param integer $sample_id
     * @param integer $template_id
     * @return mixed
     * @throws SampleCreateFolderException
     * @throws SampleCreateSubFolderException
     */
    private function create_sample_folder($transaction_id, $sample_id, $template_id)
    {
    	global $user, $transaction;
    	
    	if ($transaction_id and is_numeric($sample_id) and is_numeric($template_id))
    	{
	    	// Create Sample Folder
	    	$base_folder_id = constant("SAMPLE_FOLDER_ID");
			$base_folder = Folder::get_instance($base_folder_id);
	
			$path = new Path($base_folder->get_path());
			$path->add_element($sample_id);
					
			$sample_folder = new SampleFolder(null);
			if (($folder_id = $sample_folder->create($sample_id)) == null)
			{
				$sample_folder->delete(true, true);
				if ($transaction_id != null)
				{
					$transaction->rollback($transaction_id);
				}
				throw new SampleCreateFolderException("Could not create main folder");
			}
			$folder = Folder::get_instance($folder_id);

			// Create Subfolders
			$sub_folder_name_array = array();
    		$sample_template = new SampleTemplate($template_id);
	    			
    		$folder_array = array();
    		$requirement_array = $sample_template->get_requirements();
    			
    		if (is_array($requirement_array) and count($requirement_array) >= 1)
    		{
    			foreach($requirement_array as $key => $value)
    			{
    				if (($value[type] == "file" or $value[type] == "value") and $value[folder])
    				{
						if (!in_array($value[folder], $folder_array))
						{
							array_push($folder_array, $value[folder]);
						}
					}	
    			}	
    				
    			if (is_array($folder_array) and count($folder_array) >= 1)
    			{
    				foreach($folder_array as $key => $value)
    				{
    					$folder_name = strtolower(trim($value));
    					$folder_name = str_replace(" ","-",$folder_name);
		
						$folder_path = new Path($folder->get_path());
						$folder_path->add_element($folder_name);

						$sub_folder = Folder::get_instance(null);
						if (($sub_folder_id = $sub_folder->create($value, $folder_id, $folder_path->get_path_string(), $user->get_user_id(), null)) == null)
						{
							$sample_folder->delete(true, true);
							if ($transaction_id != null)
							{
								$transaction->rollback($transaction_id);
							}
							throw new SampleCreateSubFolderException("Could not create sub folder");
						}
						
						$sub_folder_name_array[$sub_folder_id] = strtolower(trim($value));
    				}
    			}
    		}
    		
    		$this->sample_folder_id = $folder_id;
    		$this->sample_folder_object = $sample_folder;
    		
    		return $sub_folder_name_array;
    	}
    	else
    	{
    		throw new SampleCreateFolderException("Could not create main folder");
    	}
    }
    
    /**
     * @param string $transaction_id
     * @param integer $sample_id
     * @return bool
     * @throws SampleCreateAsItemException
     */
    private function create_sample_item($transaction_id, $sample_id)
    {
    	global $transction;
    	
    	if ($transaction_id and is_numeric($sample_id))
    	{
    		// Create Item
			if (($this->item_id = parent::create()) == null)
			{
				$this->sample_folder_object->delete(true, true);
				if ($transaction_id != null)
				{
					$transaction->rollback($transaction_id);
				}
				throw new SampleCreateAsItemException("Could not create sample-item");
			}
				
    		$sample_is_item = new SampleIsItem_Access(null);
			if ($sample_is_item->create($sample_id, $this->item_id) == false)
			{
				$this->sample_folder_object->delete(true, true);
				if ($transaction_id != null)
				{
					$transaction->rollback($transaction_id);
				}
				throw new SampleCreateAsItemException("Could not create sample-item");
			}
			
			return true;
    	}
    	else
    	{
    		throw new SampleCreateAsItemException("Could not create sample-item");
    	}
    }
    
    /**
     * @see SampleInterface::create()
     * @todo sample adding via template required field via item_id
     * @param integer $organisation_unit_id
     * @param integer $template_id
     * @param string $name
     * @param string $supplier
     * @param integer $location_id
     * @param string $desc
     * @return integer Sample-ID
     * @throws SampleCreateException
     * @throws SampleCreateFolderException
     * @throws SampleCreateSubFolderException
     * @throws SampleCreateAsItemException
     * @throws SampleCreateUserException
     * @throws SampleCreateOrganisationUnitException
     * @throws SampleCreateLocationException
     * @throws SampleCreateItemSampleException
     * @throws SampleCreateItemValueException
     */
    public function create($organisation_unit_id, $template_id, $name, $manufacturer_id, $location_id, $desc, $language_id, $date_of_expiry, $expiry_warning)
    {    	
    	global $user, $transaction;
    	
    	if ($this->sample)
    	{
	    	if (is_numeric($template_id) and $name)
	    	{
	    		$transaction_id = $transaction->begin();
	    		
	    		if (($sample_id = $this->sample->create($name, $user->get_user_id(), $template_id, $manufacturer_id, $desc, $language_id, $date_of_expiry, $expiry_warning)) != null)
	    		{
					if ($desc)
					{
						$this->sample->set_comment_text_search_vector($desc, "english");
					}

					// Create Sample Folder
					try
					{
						$this->create_sample_folder($transaction_id, $sample_id, $template_id);
					}
					catch(SampleCreateFolderException $e)
					{
						if ($transaction_id != null)
						{
							$transaction->rollback($transaction_id);
						}
						throw $e;
					}
	    			catch(SampleCreateSubFolderException $e)
					{
						if ($transaction_id != null)
						{
							$transaction->rollback($transaction_id);
						}
						throw $e;
					}
	    			
    			
					try
					{
						$this->create_sample_item($transaction_id, $sample_id);
					}
					catch(SampleCreateAsItemException $e)
	    			{
						if ($transaction_id != null)
						{
							$transaction->rollback($transaction_id);
						}
						throw $e;
	    			}
	    			
	    			// Create Permissions and V-Folders
	    			$sample_security = new SampleSecurity($sample_id);
	    		
	    			if ($sample_security->create_user($user->get_user_id(), true, true) == null)
	    			{
	    				$this->sample_folder_object->delete(true, true);
						if ($transaction_id != null)
						{
							$transaction->rollback($transaction_id);
						}
						throw new SampleCreateUserException("Could not create user permission");
	    			}
	    			
	    			if (is_numeric($organisation_unit_id))
	    			{
	    				if ($sample_security->create_organisation_unit($organisation_unit_id) == null)
	    				{
	    					$this->sample_folder_object->delete(true, true);
							if ($transaction_id != null)
							{
								$transaction->rollback($transaction_id);
							}
							throw new SampleCreateOrganisationUnitException("Could not create organisation-unit permission");
	    				}
	    			}    			
	    			
	    			if (is_numeric($location_id))
	    			{
		    			// Create First Location
		    			$sample_has_locaiton_access = new SampleHasLocation_Access(null);
		    			if ($sample_has_locaiton_access->create($sample_id, $location_id, $user->get_user_id()) == null)
		    			{
		    				$this->sample_folder_object->delete(true, true);
							if ($transaction_id != null)
							{
								$transaction->rollback($transaction_id);
							}
							throw new SampleCreateLocationException("Could not create first location");
		    			}
	    			}
		
					// Create Required Value or Sample
					if (is_array($this->template_data_array) and count($this->template_data_array) >= 1)
					{
						if ($this->template_data_type == "sample")
						{
							foreach($this->template_data_array as $key => $value)
							{
								if ($value > 0)
								{
									if (SampleItemFactory::create($value, $this->item_id, null, null, null) == false)
									{
										$sample_folder->delete(true, true);
										if ($transaction_id != null)
										{
											$transaction->rollback($transaction_id);
										}
										throw new SampleCreateItemSampleException("Could not create required sample relation");
									}
								}
							}
						}
						
						if ($this->template_data_type == "value")
						{
							$value = Value::get_instance(null);				
							if ($value->create($this->sample_folder_id, $user->get_user_id(), $this->template_data_type_id, $this->template_data_array) == null)
							{
								$this->sample_folder_object->delete(true, true);
								if ($transaction_id != null)
								{
									$transaction->rollback($transaction_id);
								}
								throw new SampleCreateItemValueException("Could not create required value");
							}
							
							$sample_item = new SampleItem($sample_id);
							
							if ($sample_item->set_gid(1) == false)
							{
								$this->sample_folder_object->delete(true, true);
								if ($transaction_id != null)
								{
									$transaction->rollback($transaction_id);
								}
								throw new SampleCreateItemValueException("Could not create required value");
							}
							
							$sample_item->set_item_id($value->get_item_id());
							
							if ($sample_item->link_item() == false)
							{
								$this->sample_folder_object->delete(true, true);
								if ($transaction_id != null)
								{
									$transaction->rollback($transaction_id);
								}
								throw new SampleCreateItemValueException("Could not create required value");
							}
						}
					}
		
					if ($transaction_id != null)
					{
						$transaction->commit($transaction_id);
					}
		
					$this->__construct($sample_id);
	    			return $sample_id;	
	    		}
	    		else
	    		{
	    			if ($transaction_id != null)
	    			{
						$transaction->rollback($transaction_id);
					}
	    			throw new SampleCreateException("Could not create Sample");
	    		}
	    	}
	    	else
	    	{
	    		throw new SampleCreateException("Missing information!");
	    	}
    	}
    	else
    	{
    		throw new SampleCreateException("No instance!");
    	}
    }
    
    /**
     * @see SampleInterface::clone_sample()
     * @param integer $source_sample_id
     * @param string $name
     * @param integer $manufacturer_id
     * @param integer $location_id
     * @param string $desc
     * @param integer $language_id
     * @param string $date_of_expiry
     * @param integer $expiry_warning
     * @param array $value_array
     * @param array $item_array
     * @return integer
     * @throws SampleCloneException
     * @throws SampleCloneCreateException
     * @throws SampleCloneCreateFolderException
     * @throws SampleCloneCreateSubFolderException
     * @throws SampleCloneCreateAsItemException
     * @throws SampleCloneCreateLocationException
     * @throws SampleCloneUserException
     * @throws SampleCloneOrganisationUnitException
     * @throws SampleCloneLocationException
     * @throws SampleCloneValueException
     * @throws SampleCloneFileException
     * @throws SampleCloneParentException
     * @throws SampleCloneItemException
     */
    public function clone_sample($source_sample_id, $name, $manufacturer_id, $location_id, $desc, $language_id, $date_of_expiry, $expiry_warning, $value_array, $item_array)
    {
    	global $user, $transaction;
    	
    	if (is_numeric($source_sample_id) and $name)
    	{
    		$source_sample = new Sample($source_sample_id);
    		$source_sample_security = new SampleSecurity($source_sample_id);
    		$source_sample_folder_id = SampleFolder::get_folder_by_sample_id($source_sample_id);
    		
    		$transaction_id = $transaction->begin();
	    		
    		if (($sample_id = $this->sample->create($name, $user->get_user_id(), $source_sample->get_template_id(), $manufacturer_id, $desc, $language_id, $date_of_expiry, $expiry_warning)) != null)
    		{
    			if ($desc)
				{
					$this->sample->set_comment_text_search_vector($desc, "english");
				}

    			// Create Sample Folder
    			try
    			{
	    			$sub_folder_name_array = $this->create_sample_folder($transaction_id, $sample_id, $source_sample->get_template_id());
    			}
	    		catch (SampleCreateFolderException $e)
	    		{
					if ($transaction_id != null)
					{
						$transaction->rollback($transaction_id);
					}
					throw new SampleCloneCreateFolderException("Could not create main folder");
	    		}
	    		catch (SampleCreateSubFolderException $e)
	    		{
	    			if ($transaction_id != null)
					{
						$transaction->rollback($transaction_id);
					}
					throw new SampleCloneCreateSubFolderException("Could not create sub folder");
	    		}
    			
	    		
	    		try
	    		{
	    			$this->create_sample_item($transaction_id, $sample_id);
	    		}
	    		catch (SampleCreateAsItemException $e)
	    		{
	    			$this->sample_folder_object->delete(true, true);
					if ($transaction_id != null)
					{
						$transaction->rollback($transaction_id);
					}
					throw new SampleCloneCreateAsItemException("Could not create sample-item");
	    		}
    			
    			
	    		$sample_security = new SampleSecurity($sample_id);
				
				$source_sample_user_list = $source_sample_security->list_users();
				
				if (is_array($source_sample_user_list) and count($source_sample_user_list) >= 1)
				{
					foreach($source_sample_user_list as $key => $value)
					{
						if ($sample_security->create_user($value, true, true) == null)
		    			{
		    				$this->sample_folder_object->delete(true, true);
							if ($transaction_id != null)
							{
								$transaction->rollback($transaction_id);
							}
							throw new SampleCloneUserException("Could not clone user permission");
		    			}
					}
				}
				
				$source_sample_organisation_list = $source_sample_security->list_organisation_units();
				
				if (is_array($source_sample_organisation_list) and count($source_sample_organisation_list) >= 1)
				{
					foreach($source_sample_organisation_list as $key => $value)
					{
						if ($sample_security->create_organisation_unit($value) == null)
	    				{
	    					$this->sample_folder_object->delete(true, true);
							if ($transaction_id != null)
							{
								$transaction->rollback($transaction_id);
							}
							throw new SampleCloneOrganisationUnitException("Could not clone organisation unit permission");
	    				}
					}
				}
				
				
				// Locations
				$source_sample_location_array = SampleHasLocation_Access::list_entries_by_sample_id($source_sample_id);
				$end_sample_has_location_access = new SampleHasLocation_Access(end($source_sample_location_array));
				
				if (is_array($source_sample_location_array) and count($source_sample_location_array) >= 1)
				{
					if ($location_id != $end_sample_has_location_access->get_location_id())
					{
						$add_new_location = true;
					}
					else
					{
						$add_new_location = false;
					}
					
					foreach ($source_sample_location_array as $key => $value)
					{
						$current_sample_has_location_access = new SampleHasLocation_Access($value);
						$sample_has_location_access = new SampleHasLocation_Access(null);
		    			if ($sample_has_location_access->create($sample_id, $current_sample_has_location_access->get_location_id(), $user->get_user_id()) == null)
		    			{
		    				$this->sample_folder_object->delete(true, true);
							if ($transaction_id != null)
							{
								$transaction->rollback($transaction_id);
							}
							throw new SampleCloneLocationException("Could not clone location");
		    			}
					}
				}
				else
				{
					$add_new_location = true;
				}
				
    			if (is_numeric($location_id) and $add_new_location == true)
    			{
	    			// Create First Location
	    			$sample_has_location_access = new SampleHasLocation_Access(null);
	    			if ($sample_has_location_access->create($sample_id, $location_id, $user->get_user_id()) == null)
	    			{
	    				$this->sample_folder_object->delete(true, true);
						if ($transaction_id != null)
						{
							$transaction->rollback($transaction_id);
						}
						throw new SampleCloneCreateLocationException("Could not create location");
	    			}
    			}
    			
    			if (is_array($value_array) and count($item_array) >= 1)
    			{
    				$value_item_array = array();
    				$value_data_array = array();
    				
    				foreach($value_array as $key => $value)
    				{
    					$key = str_replace("value-","",$key);
    					$key_array = explode("-", $key, 2);
    					
    					if ($key_array[0] == "item")
    					{
    						$value_item_array[$key_array[1]] = $value;
    					}
    					elseif(is_numeric($key_array[0]))
    					{
    						$value_data_array[$key_array[0]][$key_array[1]] = $value;
    					}
    				}
    				
    				if (is_array($value_item_array) and count($value_item_array) >= 1)
    				{
    					foreach ($value_item_array as $key => $value)
    					{
    						$gid = SampleItem::get_gid_by_item_id_and_sample_id($value, $source_sample_id);
    						$data_entity_id = DataEntity::get_entry_by_item_id($value);
    						$value_id = Value::get_value_id_by_data_entity_id($data_entity_id);
    						if (is_numeric($value_id))
    						{
    							$value_obj = Value::get_instance($value_id);
    							$parent_folder_id = $value_obj->get_parent_folder_id();
    							$value_type_id = $value_obj->get_type_id();
    							
    							if ($parent_folder_id == $source_sample_folder_id)
    							{
    								$new_folder_id = $this->sample_folder_id;
    							}
    							else
    							{
    								$folder_name = Folder::get_name_by_id($parent_folder_id);
    								$new_folder_id = array_search(trim(strtolower($folder_name)),$sub_folder_name_array);
    							}
    							
    							if (is_numeric($new_folder_id) and is_numeric($value_type_id))
    							{
    								$new_value_obj = Value::get_instance(null);
    								
    								if ($new_value_obj->create($new_folder_id, $user->get_user_id(), $value_type_id, $value_data_array[$key]) == null)
    								{
    									$this->sample_folder_object->delete(true, true);
										if ($transaction_id != null)
										{
											$transaction->rollback($transaction_id);
										}
										throw new SampleCloneValueException("Could not clone value");
    								}
    								
    								$new_value_item_id = $new_value_obj->get_item_id();
    								
    								$sample_item = new SampleItem($sample_id);
    								$sample_item->set_gid($gid);
    								
    								if ($sample_item->set_item_id($new_value_item_id) == false)
    								{
    									$this->sample_folder_object->delete(true, true);
										if ($transaction_id != null)
										{
											$transaction->rollback($transaction_id);
										}
										throw new SampleCloneValueException("Could not clone value");
    								}
    								
    								if ($sample_item->link_item() == false)
    								{
    									$this->sample_folder_object->delete(true, true);
										if ($transaction_id != null)
										{
											$transaction->rollback($transaction_id);
										}
										throw new SampleCloneValueException("Could not clone value");
    								}
    							}
    						}
    					}
    				}
    			}
    			

    			if (is_array($item_array) and count($item_array) >= 1)
    			{
    				$item_type_array = array();
    				$item_data_array = array();
    				
    				foreach($item_array as $key => $value)
    				{
    					if ($value[1] == "1")
    					{
    						$item_explode_array = explode("-", $value[0], 2);
    						
    						if (!in_array($item_explode_array[0], $item_type_array))
    						{
    							array_push($item_type_array, $item_explode_array[0]);
    						}
    						if (!is_array($item_data_array[$item_explode_array[0]]))
    						{
    							$item_data_array[$item_explode_array[0]] = array();
    						}
    						array_push($item_data_array[$item_explode_array[0]], $item_explode_array[1]);
    					}
    				}
    				
    				if (is_array($item_type_array) and count($item_type_array) >= 1)
    				{
    					foreach($item_type_array as $key => $value)
    					{
    						if ($value == "parent")
    						{
    							foreach($item_data_array[$value] as $data_key => $data_value)
	    						{
	    							$parent_item_explode_array = explode("-", $data_value, 2);
	    							
	    							if ($parent_item_explode_array[0] and $parent_item_explode_array[1])
	    							{
							  			$item_add_holder_event = new ItemAddHolderEvent($parent_item_explode_array[1], $parent_item_explode_array[0], $this->item_id);
										$event_handler = new EventHandler($item_add_holder_event);
											
										if ($event_handler->get_success() == false)
										{
											$this->sample_folder_object->delete(true, true);
											if ($transaction_id != null)
											{
												$transaction->rollback($transaction_id);
											}
											throw new SampleCloneParentException("Could not clone parent");
										}
	    							}
	    						}
    						}
    						elseif ($value == "file")
    						{
    							if (is_array($item_data_array[$value]) and count($item_data_array[$value]) >= 1)
    							{
    								foreach($item_data_array[$value] as $data_key => $data_value)
	    							{
			    						$gid = SampleItem::get_gid_by_item_id_and_sample_id($data_value, $source_sample_id);
	    								
	    								$data_entity_id = DataEntity::get_entry_by_item_id($data_value);
		    							$file_id = File::get_file_id_by_data_entity_id($data_entity_id);
		    							
		    							if ($file_id)
		    							{
			    							$file_obj = File::get_instance($file_id);
			    							$parent_folder_id = $file_obj->get_parent_folder_id();
			    							    							
			    							if ($parent_folder_id == $source_sample_folder_id)
			    							{
			    								$new_folder_id = $this->sample_folder_id;
			    							}
			    							else
			    							{
			    								$folder_name = Folder::get_name_by_id($parent_folder_id);
			    								$new_folder_id = array_search(trim(strtolower($folder_name)),$sub_folder_name_array);
			    							}
			    							
			    							if (is_numeric($new_folder_id))
			    							{
			    								if ($file_obj->copy($new_folder_id) == false)
			    								{
			    									$this->sample_folder_object->delete(true, true);
													if ($transaction_id != null)
													{
														$transaction->rollback($transaction_id);
													}
													throw new SampleCloneFileException("Could not clone files");
			    								}
			    								
			    								$new_file_item_id = $file_obj->get_item_id();
			    								
			    								$sample_item = new SampleItem($sample_id);
			    								$sample_item->set_gid($gid);
			    								
			    								if ($sample_item->set_item_id($new_file_item_id) == false)
			    								{
			    									$this->sample_folder_object->delete(true, true);
													if ($transaction_id != null)
													{
														$transaction->rollback($transaction_id);
													}
													throw new SampleCloneFileException("Could not clone files");
			    								}
			    								
			    								if ($sample_item->link_item() == false)
			    								{
			    									$this->sample_folder_object->delete(true, true);
													if ($transaction_id != null)
													{
														$transaction->rollback($transaction_id);
													}
													throw new SampleCloneFileException("Could not clone files");
			    								}
			    							}
		    							}
	    							}
    							}
    						}
    						else
    						{
    							if (is_array($item_data_array[$value]) and count($item_data_array[$value]) >= 1)
    							{
    								$handling_class = Item::get_handling_class_by_type($value);
    								
    								if ($handling_class)
    								{
	    								foreach($item_data_array[$value] as $data_key => $data_value)
	    								{
	    									$gid = SampleItem::get_gid_by_item_id_and_sample_id($data_value, $source_sample_id);
	    									
	    									$new_item_id = $handling_class::clone_item($data_value);
	    									
	    									if ($new_item_id)
	    									{
	    										$sample_item = new SampleItem($sample_id);
	    										$sample_item->set_gid($gid);
	    										
			    								if ($sample_item->set_item_id($new_item_id) == false)
			    								{
			    									$this->sample_folder_object->delete(true, true);
													if ($transaction_id != null)
													{
														$transaction->rollback($transaction_id);
													}
													throw new SampleCloneItemException("Could not clone item");
			    								}
			    								
			    								if ($sample_item->link_item() == false)
			    								{
			    									$this->sample_folder_object->delete(true, true);
													if ($transaction_id != null)
													{
														$transaction->rollback($transaction_id);
													}
													throw new SampleCloneItemException("Could not clone item");
			    								}
	    									}
	    								}
    								}
    							}
    						}
    					}
    				}
    			}
    			
    			if ($transaction_id != null)
				{
					$transaction->commit($transaction_id);
				}
	
				$this->__construct($sample_id);
    			return $sample_id;	
    		}
    		else
    		{
    			if ($transaction_id != null)
				{
					$transaction->rollback($transaction_id);
				}
    			throw new SampleCloneException("Could not clone Sample!");
    		}
    	}
    	else
    	{
    		throw new SampleCloneException("Could not clone Sample!");
    	}
    }
    
	/**
	 * @see SampleInterface::delete()
	 * @return bool
	 * @throws SampleDeleteException
	 * @throws SampleDeleteLocationException
	 * @throws SampleDeleteUserException
	 * @throws SampleDeleteOrganisationUnitException
	 * @throws SampleDeleteItemException
	 * @throws SampleDeleteAsItemException
	 * @throws SampleDeleteFolderException
	 */
	public function delete()
	{
		global $transaction;
		
		if ($this->sample_id and $this->sample)
		{
			$transaction_id = $transaction->begin();
			
			array_push(self::$sample_delete_array, $this->sample_id);
			
			$tmp_sample_id = $this->sample_id;
		
			// Location Relations
			$sample_has_location_array = SampleHasLocation_Access::list_entries_by_sample_id($tmp_sample_id);
			if (is_array($sample_has_location_array) and count($sample_has_location_array) >= 1)
			{
				foreach($sample_has_location_array as $key => $value)
				{
					$sample_has_location = new SampleHasLocation_Access($value);
					if ($sample_has_location->delete() == false)
					{
						if ($transaction_id != null)
						{
							$transaction->rollback($transaction_id);
						}
						return false;
					}
				}
			}
			
			// Organisation Unit and User Relations
			$sample_security = new SampleSecurity($tmp_sample_id);
			$organisation_unit_array = $sample_security->list_organisation_unit_entries();
			if (is_array($organisation_unit_array) and count($organisation_unit_array) >= 1)
			{
				foreach($organisation_unit_array as $key => $value)
				{
					if ($sample_security->delete_organisation_unit($value) == false)
					{
						if ($transaction_id != null)
						{
							$transaction->rollback($transaction_id);
						}
						return false;
					}
				}
			}
						
			$user_array = $sample_security->list_user_entries();
			if (is_array($user_array) and count($user_array) >= 1)
			{
				foreach($user_array as $key => $value)
				{
					if ($sample_security->delete_user($value) == false)
					{
						if ($transaction_id != null)
						{
							$transaction->rollback($transaction_id);
						}
						return false;
					}
				}
			}
			
			// Other Items
			$sample_item = new SampleItem($tmp_sample_id);
			$item_array = $sample_item->get_sample_items();
			if (is_array($item_array) and count($item_array) >= 1)
			{
				foreach($item_array as $item_key => $item_value)
				{
					$sample_item = new SampleItem($tmp_sample_id);
					$sample_item->set_item_id($item_value);
					if ($sample_item->unlink_item() == false)
					{
						if ($transaction_id != null)
						{
							$transaction->rollback($transaction_id);
						}
						return false;
					}
				}
			}	
			
			// Delete Item
			if ($this->item_id) {
				if (parent::delete() == false)
				{
					if ($transaction_id != null)
					{
						$transaction->rollback($transaction_id);
					}
					return false;
				}
			}
    		
			$sample_is_item = new SampleIsItem_Access($tmp_sample_id);
			if ($sample_is_item->delete() == false)
			{
				if ($transaction_id != null)
				{
					$transaction->rollback($transaction_id);
				}
				return false;
			}
			
			if ($this->sample->delete() == false)
			{
				if ($transaction_id != null)
				{
					$transaction->rollback($transaction_id);
				}
				return false;
			}
			else
			{
				$this->__destruct();
	    		$folder_id = SampleFolder::get_folder_by_sample_id($tmp_sample_id);
	    		$folder = Folder::get_instance($folder_id);
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
		}
		else
		{
			return false;
		}	
	}

	
	/**
	 * @see SampleInterface::get_requirements()
	 * @return array
	 */
    public function get_requirements()
    {
    	if ($this->sample_id and $this->sample)
    	{
	    	$sample_template 		= new SampleTemplate($this->sample->get_template_id());
	    	
	    	$requirements_array 	= $sample_template->get_requirements();
			
			$return_array = array();
			$counter = 0;
			$type_counter = 0;
			$category_counter = 0;

			if (is_array($requirements_array) and count($requirements_array) >= 1)
			{
				foreach($requirements_array as $key => $value)
				{
					if ($value[xml_element] == "item" and !$value[close])
					{
						$in_item = true;
						$return_array[$counter][type] = $value[type];
						$return_array[$counter][name] = $value[name];

						$return_array[$counter][requirement] = $value[requirement];

						if ($value[occurrence])
						{
							$return_array[$counter][occurrence] = $value[occurrence];
						}
						else
						{
							$return_array[$counter][occurrence] = "once";
						}
					}
					
					if ($value[xml_element] == "item" and $value[close] == "1")
					{
						$counter++;
						$type_counter = 0;
						$category_counter = 0;
						$in_item = false;
					}
					
					if ($value[xml_element] == "type" and !$value[close] and $in_item == true and is_numeric($value[id]))
					{
						$return_array[$counter][type_id][$type_counter] = $value[id];
						$type_counter++;
					}	
					
					if ($value[xml_element] == "category" and !$value[close] and $in_item == true and is_numeric($value[id]))
					{
						$return_array[$counter][category_id][$category_counter] = $value[id];
						$category_counter++;
					}				
				}
			}
			
			if (is_array($return_array) and count($return_array) >= 1)
			{
				foreach($return_array as $key => $value)
				{
					if (!$value[name] and $value[type])
					{
						$item_handling_class = Item::get_handling_class_by_type($value[type]);
						if ($item_handling_class)
						{
							$return_array[$key][name] = "Add ".$item_handling_class::get_generic_name($value[type], $value[type_id]);
						}
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
     * @see SampleInterface::get_fulfilled_requirements()
     * @return array
     */
    public function get_fulfilled_requirements()
    {
    	if ($this->sample_id and $this->sample)
    	{
	    	$requirements_array = $this->get_requirements();			
			$fulfilled_array = array();
			$item_type_array = Item::list_types();
			
			$sample_item = new SampleItem($this->sample_id);
			$item_array = $sample_item->get_sample_items();
			
			if (is_array($requirements_array) and count($requirements_array) >= 1)
			{
				foreach($requirements_array as $key => $value)
				{
					if ($value[gid])
					{
						$gid = $value[gid];
					}
					else
					{
						$gid = $key;
					}
					
					if ($value[type] != "parentsample")
					{
						if (is_array($item_array) and count($item_array) >= 1)
						{
							foreach($item_array as $item_key => $item_value)
							{
								$item_gid = SampleItem::get_gid_by_item_id_and_sample_id($item_value, $this->sample_id);
								
								if (is_array($item_type_array) and count($item_type_array) >= 1)
								{
									foreach ($item_type_array as $item_type => $item_handling_class)
									{
										if (class_exists($item_handling_class))
										{
											if ($item_handling_class::is_kind_of($item_type, $item_value) == true  and $item_gid == $gid)
											{
												$fulfilled_array[$key] = true;
											}
										}
									}
								}
							}
						}
					}
					else
					{
						$parent_sample_array = SampleItem::list_sample_id_by_item_id_and_gid($this->item_id, ($gid*-1));
						if (is_array($parent_sample_array) and count($parent_sample_array) >= 1)
						{
							$fulfilled_array[$key] = true;
						}
					}
				}
				return $fulfilled_array;
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
     * @see SampleInterface::get_sub_folder()
     * @param integer $folder_id Folder-ID
     * @param integer $gid 
     * @return string Sub-Folder-Path
     */
    public function get_sub_folder($folder_id, $gid)
    {
    	if ($this->sample_id and $this->sample)
    	{
	    	if (is_numeric($folder_id) and is_numeric($gid))
	    	{
				$sample_folder_id = SampleFolder::get_folder_by_sample_id($this->sample_id);
	    		
	    		if ($folder_id == $sample_folder_id)
	    		{
	    			$folder = Folder::get_instance($folder_id);
	    		
	    			$sample_template = new SampleTemplate($this->sample->get_template_id());
	    			$attribute_array = $sample_template->get_gid_attributes($gid);
	    			
	    			if ($attribute_array[folder])
	    			{
	    				$folder_name = strtolower(trim($attribute_array[folder]));
	    				$folder_name = str_replace(" ","-",$folder_name);
	    				
	    				$folder_path = new Path($folder->get_path());
						$folder_path->add_element($folder_name);

	    				return Folder::get_folder_by_path($folder_path->get_path_string());	    				
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
    	else
    	{
    		return null;
    	}
    }
    
    /**
     * @see SampleInterface::add_location()
     * @param integer location_id
     * @return bool
     */
    public function add_location($location_id)
    {
    	global $user;
    	
    	if ($this->sample_id and $this->sample and is_numeric($location_id))
    	{
    		$sample_has_location = new SampleHasLocation_Access(null);
    		if ($location_id != $this->get_current_location())
    		{
	    		if ($sample_has_location->create($this->sample_id, $location_id, $user->get_user_id()) != null)
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
     * @see SampleInterface::get_current_location()
     * @return integer
     */
    public function get_current_location()
    {
    	if ($this->sample_id and $this->sample)
    	{
	    	if ($this->sample_id and $this->sample)
	    	{
	    		$pk_array = SampleHasLocation_Access::list_entries_by_sample_id($this->sample_id);
	    		if (is_array($pk_array) and count($pk_array) >= 1)
	    		{
	    			foreach ($pk_array as $key => $value)
	    			{
	    				$sample_has_location = new SampleHasLocation_Access($value);
	    				$last_element = $sample_has_location->get_location_id();
	    			}
	    			return $last_element;
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
     * @see SampleInterface::get_name()
     * @return string
     */
    public function get_name()
    {
    	if ($this->sample_id and $this->sample)
    	{
    		return $this->sample->get_name();
    	}
    	else
    	{
    		return null;
    	}
    }
    
    public function get_description()
    {
    	if ($this->sample_id and $this->sample)
    	{
    		return $this->sample->get_comment();
    	}
    	else
    	{
    		return null;
    	}
    }
    
    /**
     * @see SampleInterface::get_datetime()
     * @return string
     */
    public function get_datetime()
    {
    	if ($this->sample_id and $this->sample)
    	{
    		return $this->sample->get_datetime();
    	}
    	else
    	{
    		return null;
    	}
    }
    
    /**
     * @see SampleInterface::get_owner_id()
     * @return integer
     */
    public function get_owner_id()
    {
    	if ($this->sample_id and $this->sample)
    	{
    		return $this->sample->get_owner_id();
    	}
    	else
    	{
    		return null;
    	}
    }
    
    /**
     * @see SampleInterface::get_manufacturer_id()
     * @return string
     */
    public function get_manufacturer_id()
    {
    	if ($this->sample_id and $this->sample)
    	{
    		return $this->sample->get_manufacturer_id();
    	}
    	else
    	{
    		return null;
    	}
    }
    
    /**
     * @see SampleInterface::get_template_id()
     * @return integer
     */
    public function get_template_id()
    {
    	if ($this->sample_id and $this->sample)
    	{
    		return $this->sample->get_template_id();
    	}
    	else
    	{
    		return null;
    	}
    }
	
	/**
	 * @see SampleInterface::get_availability()
	 * @return bool
	 */
	public function get_availability()
	{
		if ($this->sample_id and $this->sample)
		{
			return $this->sample->get_available();
		}
		else
		{
			return false;
		}		
	}
	
	/**
	 * @see SampleInterface::get_date_of_expiry()
	 * @return bool
	 */
	public function get_date_of_expiry()
	{
		if ($this->sample_id and $this->sample)
		{
			return $this->sample->get_date_of_expiry();
		}
		else
		{
			return null;
		}		
	}
		
	/**
	 * @see SampleInterface::get_current_location_name()
	 * @return string
	 */
	public function get_current_location_name()
	{
		if ($this->sample_id and $this->sample)
		{
			$location_id = $this->get_current_location();
			$location = new Location($location_id);
			if ($location_name = $location->get_name(false)) 
			{
				return $location_name;
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
	 * @see SampleInterface::get_template_name()
	 * @return string
	 */
	public function get_template_name()
	{
		if ($this->sample_id and $this->sample)
		{
			$sample_template_id = $this->get_template_id();
			$sample_template = new SampleTemplate($sample_template_id);
			return $sample_template->get_name();
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @see SampleInterface::get_formatted_id()
	 * @return string
	 */
	public function get_formatted_id()
	{
		if ($this->sample_id)
		{
    		return "S".str_pad($this->sample_id, 8 ,'0', STR_PAD_LEFT);
    	}
    	else
    	{
    		return null;
    	}
	}
	
	/**
	 * @see SampleInterface::get_organisation_unit_id()
	 * @return integer
	 */
	public function get_organisation_unit_id()
	{
    	if ($this->sample_id)
    	{
    		$sample_security = new SampleSecurity($this->sample_id);
    		$sample_security_array = $sample_security->list_organisation_units();
    		if (is_array($sample_security_array) and count($sample_security_array) >= 1)
    		{
    			return $sample_security_array[0];
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
	 * @see SampleInterface::set_name()
	 * @param string $name
	 * @return bool
	 */
	public function set_name($name)
	{
		global $transaction;

		if ($this->sample_id and $this->sample and $name)
		{
    		$transaction_id = $transaction->begin();

	    	$folder_id = SampleFolder::get_folder_by_sample_id($this->sample_id);
	    	$folder = Folder::get_instance($folder_id);

	    	$folder_name = $name." (".$this->get_formatted_id().")";
	    	
			if ($folder->set_name($folder_name) == false)
			{
				if ($transaction_id != null)
				{
					$transaction->rollback($transaction_id);
				}
				return false;
			}
    		
    		if ($this->sample->set_name($name) == false)
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
	 * @see SampleInterface::set_owner_id()
	 * @param integer $owner_id
	 * @return bool
	 */
	public function set_owner_id($owner_id)
	{
		if ($this->sample_id and $this->sample and is_numeric($owner_id))
		{
    		return $this->sample->set_owner_id($owner_id);
    	}
    	else
    	{
    		return false;
    	}
	}
	
	/**
	 * @see SampleInterface::set_manufacturer_id()
	 * @param string $supplier
	 * @return bool
	 */
	public function set_manufacturer_id($manufacturer_id)
	{
		if ($this->sample_id and $this->sample)
		{
    		return $this->sample->set_manufacturer_id($manufacturer_id);
    	}
    	else
    	{
    		return false;
    	}
	}
	
	/**
	 * @see SampleInterface::set_availability()
	 * @param bool $availability
	 * @return bool
	 */
	public function set_availability($availability)
	{
		if ($this->sample_id and $this->sample and isset($availability))
		{
    		return $this->sample->set_available($availability);
    	}
    	else
    	{
    		return false;
    	}
	}
	

	/**
	 * @see SampleInterface::exist_sample()
	 * @param integer $sample_id
	 * @return bool
	 */
   	public static function exist_sample($sample_id)
   	{
		return Sample_Access::exist_sample_by_sample_id($sample_id);
   	}

   	/**
   	 * @todo overhaul
   	 * @see SampleInterface::list_user_related_samples()
   	 * @param integer $user_id
   	 * @return array
   	 */
    public static function list_user_related_samples($user_id)
    {
    	if (is_numeric($user_id))
    	{
    		$pk_array = SampleHasUser_Access::list_entries_by_user_id($user_id);

    		if (is_array($pk_array) and count($pk_array) >= 1)
    		{
    			$return_array = array();
    			
    			foreach ($pk_array as $key => $value)
    			{
    				$sample_has_user_access = new SampleHasUser_Access($value);
    				array_push($return_array, $sample_has_user_access->get_sample_id());
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
     * @see SampleInterface::list_organisation_unit_related_samples()
     * @param integer $organisation_unit_id
     * @return array
     */
    public static function list_organisation_unit_related_samples($organisation_unit_id)
    {
    	if (is_numeric($organisation_unit_id))
    	{
    		$pk_array = SampleHasOrganisationUnit_Access::list_entries_by_organisation_unit_id($organisation_unit_id);
    		
    		if (is_array($pk_array) and count($pk_array) >= 1)
    		{
    			$return_array = array();
    			
    			foreach ($pk_array as $key => $value)
    			{
    				$sample_has_organisation_unit_access = new SampleHasOrganisationUnit_Access($value);
    				array_push($return_array, $sample_has_organisation_unit_access->get_sample_id());
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
     * @see SampleInterface::list_entries_by_template_id()
   	 * @param integer $template_id
   	 * @return array
   	 */
    public static function list_entries_by_template_id($template_id)
    {
    	return Sample_Access::list_entries_by_template_id($template_id);;
    }

    /**
     * @see ItemListenerInterface::get_item_name()
	 * @return string
	 */
	public final function get_item_name()
	{
		if ($this->sample_id and $this->sample)
    	{
    		return $this->sample->get_name();
    	}
    	else
    	{
    		return null;
    	}
	}
    
    /**
     * @see ItemListenerInterface::get_item_parents()
	 * @return string
	 */
	public final function get_item_parents()
	{
		if ($this->item_id)
		{
			$parent_sample_array = SampleItem::list_entries_by_item_id($this->item_id);
			
			if (is_array($parent_sample_array) and count($parent_sample_array) >= 1)
			{
				$return_array = array();
				
				foreach($parent_sample_array as $key => $value)
				{
					$sample_is_item = new SampleIsItem_Access($value);
    				array_push($return_array, $sample_is_item->get_item_id());
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
	 * @see ItemListenerInterface::clone_item()
	 * @param integer $item_id
	 * @return integer
	 */
	public static function clone_item($item_id)
	{
		return $item_id;
	}
	
	/**
	 * @see ItemListenerInterface::get_entry_by_item_id()
	 * @param integer $item_id
	 * @return integer
	 */
	public static function get_entry_by_item_id($item_id)
	{
		return SampleIsItem_Access::get_entry_by_item_id($item_id);
	}
    
 	/**
 	 * @see ItemListenerInterface::is_kind_of()
	 * @param string $type
	 * @param integer $item_id
	 * @return bool
	 */
    public static function is_kind_of($type, $item_id)
    {
    	if (is_numeric($item_id))
    	{
    		if (($sample_id = SampleIsItem_Access::get_entry_by_item_id($item_id)) != null)
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
     * @see ItemListenerInterface::is_type_or_category()
	 * @param $category_id
	 * @param integer $type_id
	 * @param integer $item_id
	 * @return bool
	 */
    public static function is_type_or_category($category_id, $type_id, $item_id)
    {
    	if (is_numeric($type_id))
    	{
    		$sample_id = SampleIsItem_Access::get_entry_by_item_id($item_id);
    		$sample = new Sample($sample_id);
    		
    		if ($sample->get_template_id() == $type_id)
    		{
    			return true;
    		}
    		else
    		{
    			return false;
    		}
    	}
    	elseif (is_numeric($category_id))
    	{
    		$sample_id = SampleIsItem_Access::get_entry_by_item_id($item_id);
    		$sample = new Sample($sample_id);
    		$sample_template = new SampleTemplate($sample->get_template_id());
    		
    		if ($sample_template->get_cat_id() == $category_id)
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
     * @see ItemListenerInterface::get_instance_by_item_id()
	 * @param integer $item_id
	 * @return object
	 */
	public static function get_instance_by_item_id($item_id)
    {
    	if (is_numeric($item_id))
    	{
    		$sample_id = SampleIsItem_Access::get_entry_by_item_id($item_id);
    		return new Sample($sample_id);
    	}
    	else
    	{
    		return null;
    	}
    }
    
    /**
     * @see ItemListenerInterface::get_generic_name()
	 * @param string $type
	 * @param array $type_array
	 * @return string
	 */
    public static function get_generic_name($type, $type_array)
    {
    	if (is_array($type_array) and count($type_array) == 1)
    	{
			$sample_template = new SampleTemplate($type_array[0]);

			if ($sample_template->get_name() != null)
			{
				return $sample_template->get_name();
			}
			else
			{
				if ($type == "parentsample")
	    		{
	    			return "Parent Sample";
	    		}
	    		else
	    		{
	    			return "Sample";
	    		}
			}
    	}
    	else
    	{
    		if ($type == "parentsample")
    		{
    			return "Parent Sample";
    		}
    		else
    		{
    			return "Sample";
    		}
    	}
    }

    /**
     * @see ItemListenerInterface::get_generic_symbol()
	 * @param string $type
	 * @param integer $id
	 * @return string
	 */
    public static function get_generic_symbol($type, $id)
    {
   		if ($type == "sample")
    	{
    		return "<img src='images/icons/sample.png' alt='' style='border: 0;' />";
    	}
    }
    
    /**
     * @see ItemListenerInterface::get_generic_link()
	 * @param string $type
	 * @param integer $id
	 * @return string
	 */
	public static function get_generic_link($type, $id)
	{
		if ($type == "sample")
		{
			$paramquery[username] = $_GET[username];
			$paramquery[session_id] = $_GET[session_id];
			$paramquery[nav] = "sample";
			$paramquery[run] = "detail";
			$paramquery[sample_id] = $id;
			return http_build_query($paramquery, '', '&#38;');
		}
	}
    
	/**
	 * @see ItemListenerInterface::get_sql_select_array()
	 * @param string $type
	 * @return array
	 */
	public static function get_sql_select_array($type)
    {
   		if ($type == "sample")
		{
			$select_array[name] = "".constant("SAMPLE_TABLE").".name";
			$select_array[type_id] = "".constant("SAMPLE_TABLE").".id AS sample_id";
			$select_array[datetime] = "".constant("SAMPLE_TABLE").".datetime";
			return $select_array;
		}
		else
		{
			return null;
		}
    }
    
    /**
     * @see ItemListenerInterface::get_sql_join()
	 * @param string $type
	 * @return string
	 */
	public static function get_sql_join($type)
	{
		if ($type == "sample")
		{
			return 	"LEFT JOIN ".constant("SAMPLE_IS_ITEM_TABLE")." 	ON ".constant("ITEM_TABLE").".id 					= ".constant("SAMPLE_IS_ITEM_TABLE").".item_id " .
					"LEFT JOIN ".constant("SAMPLE_TABLE")." 			ON ".constant("SAMPLE_IS_ITEM_TABLE").".sample_id 	= ".constant("SAMPLE_TABLE").".id ";
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @see ItemListenerInterface::get_sql_where()
	 * @param string $type
	 * @return string
	 */
	public static function get_sql_where($type)
	{
		if ($type == "sample")
		{
			return "(LOWER(TRIM(".constant("SAMPLE_TABLE").".name)) LIKE '{STRING}')";
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @see ItemListenerInterface::get_sql_fulltext_select_array()
	 * @param string $type
	 * @return array
	 */
	public static function get_sql_fulltext_select_array($type)
	{
		if ($type == "sample")
		{
			$select_array[name] = "".constant("SAMPLE_TABLE").".name";
			$select_array[type_id] = "".constant("SAMPLE_TABLE").".id AS sample_id";
			$select_array[datetime] = "".constant("SAMPLE_TABLE").".datetime";
			$select_array[rank] = "ts_rank_cd(".constant("SAMPLE_TABLE").".comment_text_search_vector, to_tsquery('{LANGUAGE}', '{STRING}'), 32 /* rank/(rank+1) */)";
			return $select_array;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @see ItemListenerInterface::get_sql_fulltext_join()
	 * @param string $type
	 * @return string
	 */
	public static function get_sql_fulltext_join($type)
	{
		if ($type == "sample")
		{
			return 	"LEFT JOIN ".constant("SAMPLE_IS_ITEM_TABLE")." 	ON ".constant("ITEM_TABLE").".id 					= ".constant("SAMPLE_IS_ITEM_TABLE").".item_id " .
					"LEFT JOIN ".constant("SAMPLE_TABLE")." 			ON ".constant("SAMPLE_IS_ITEM_TABLE").".sample_id 	= ".constant("SAMPLE_TABLE").".id ";
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @see ItemListenerInterface::get_sql_fulltext_where()
	 * @param string $type
	 * @return string
	 */
	public static function get_sql_fulltext_where($type)
	{
		if ($type == "sample")
		{
			return "".constant("SAMPLE_TABLE").".comment_text_search_vector @@ to_tsquery('{LANGUAGE}', '{STRING}')";
		}
		else
		{
			return null;
		}
	}
	
    /**
     * @see EventListenerInterface::listen_events()
     * @param object $event_object
     * @return bool
     */
    public static function listen_events($event_object)
    {
    	if ($event_object instanceof UserDeletePrecheckEvent)
    	{
    		$sample_array = self::list_user_related_samples($event_object->get_user_id());
			
			if (is_array($sample_array))
			{
				if (count($sample_array) >= 1)
				{
					return false;
				}
			}
    	}
    	
    	if ($event_object instanceof OrganisationUnitDeletePrecheckEvent)
    	{
    		$sample_array = self::list_organisation_unit_related_samples($event_object->get_organisation_unit_id());
			
			if (is_array($sample_array))
			{
				if (count($sample_array) >= 1)
				{
					return false;
				}
			}
    	}
    	
   		if ($event_object instanceof LocationDeleteEvent)
    	{
    		if (SampleHasLocation_Access::delete_by_location_id($event_object->get_location_id()) == false)
			{
				return false;
			}
    	}
    	
   		if ($event_object instanceof ItemUnlinkEvent)
    	{
    		// Do Nothing
    	}
    	
    	return true;
    }
    
	/**
     * @see ItemHolderInterface::get_item_list_sql()
	 * @param integer $holder_id
	 * @return string
	 */
	public static function get_item_list_sql($holder_id)
	{
		return " SELECT item_id FROM ".constant("SAMPLE_HAS_ITEM_TABLE")." WHERE sample_id = ".$holder_id."";
	}
	
}
?>