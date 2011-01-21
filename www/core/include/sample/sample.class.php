<?php
/**
 * @package sample
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
require_once("interfaces/sample.interface.php");

if (constant("UNIT_TEST") == false or !defined("UNIT_TEST"))
{
	require_once("exceptions/sample_not_found_exception.class.php");
	require_once("exceptions/sample_creation_failed_exception.class.php");
	
	require_once("access/sample.access.php");
	
	require_once("access/sample_has_sample_depository.access.php");
	require_once("access/sample_has_sample.access.php");
	require_once("access/sample_has_organisation_unit.access.php");
	require_once("access/sample_has_user.access.php");
	
	require_once("access/sample_join.access.php");
}

/**
 * Sample Management Class
 * @package sample
 */
class Sample implements SampleInterface, EventListenerInterface
{
	private $sample;
	
	private $sample_id;
	private $item_id;
	
	private $template_data_type;
	private $template_data_type_id;
	private $template_data_array;

	/**
	 * @param integer $sample_id Sample-ID
	 */
    function __construct($sample_id)
    {
    	if ($sample_id == null)
    	{
    		$this->sample_id = null;
    		$this->sample = new Sample_Access(null);
    	}
    	else
    	{
    		$this->sample_id = $sample_id;
    		$this->sample = new Sample_Access($sample_id);
    		
			$this->item_id = Item::get_id_by_sample_id($sample_id);
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
     * Sets tempalte-date before sample creation
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
     * Creates a new sample
     * @param integer $organisation_unit_id
     * @param integer $template_id
     * @param string $name
     * @param string $supplier
     * @param integer $depository_id
     * @param string $desc
     * @return integer Sample-ID
     * @throws SampleCreationFailedException
     */
    public function create($organisation_unit_id, $template_id, $name, $supplier, $depository_id, $desc)
    {
    	global $user, $transaction;
    	
    	if ($this->sample)
    	{
	    	if (is_numeric($template_id) and $name and $supplier and is_numeric($depository_id))
	    	{
	    		$transaction_id = $transaction->begin();
	    		
	    		if (($sample_id = $this->sample->create($name, $user->get_user_id(), $template_id, $supplier, $desc)) != null)
	    		{
					if ($desc)
					{
						$this->sample->set_comment_text_search_vector($desc, "english");
					}

					// Create Sample Folder
					$sample_folder_id = $GLOBALS[sample_folder_id];
					$folder = new Folder($sample_folder_id);

					$path = new Path($folder->get_path());
					$path->add_element($sample_id);
					
					$folder = new Folder(null);
					if (($folder_id = $folder->create($name, $sample_folder_id, false, $path->get_path_string(), $user->get_user_id(), null)) != null)
					{ 
						if ($folder->create_sample_folder($sample_id) == false)
						{
							$folder->delete(true, true);
							if ($transaction_id != null)
							{
								$transaction->rollback($transaction_id);
							}
							throw new SampleCreationFailedException("",1);
						}
						
		    			if ($folder->set_flag(32) == false)
		    			{
		    				$folder->delete(true, true);
							if ($transaction_id != null)
							{
								$transaction->rollback($transaction_id);
							}
							throw new SampleCreationFailedException("",1);
		    			}
		    			
		    			// Create Permissions and V-Folders
		    			$sample_security = new SampleSecurity($sample_id);
		    		
		    			if ($sample_security->create_user($user->get_user_id(), true, true) == null)
		    			{
		    				$folder->delete(true, true);
							if ($transaction_id != null)
							{
								$transaction->rollback($transaction_id);
							}
							throw new SampleCreationFailedException("",1);
		    			}
		    			
		    			if (is_numeric($organisation_unit_id)) {
		    				if ($sample_security->create_organisation_unit($organisation_unit_id) == null)
		    				{
		    					$folder->delete(true, true);
								if ($transaction_id != null)
								{
									$transaction->rollback($transaction_id);
								}
								throw new SampleCreationFailedException("",1);
		    				}
		    			}
		    			
		    			// Create Subfolders
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
									
									$sub_folder = new Folder(null);
									if ($sub_folder->create($value, $folder_id, false, $folder_path->get_path_string(), $user->get_user_id(), null) == null)
									{
										$folder->delete(true, true);
										if ($transaction_id != null)
										{
											$transaction->rollback($transaction_id);
										}
										throw new SampleCreationFailedException("",1);
									}
									
									if ($sub_folder->set_flag(1024) == false)
									{
										$folder->delete(true, true);
										if ($transaction_id != null)
										{
											$transaction->rollback($transaction_id);
										}
										throw new SampleCreationFailedException("",1);
									}
		    					}
		    				}
		    			}
		    			
		    			// Create First Depository
		    			$sample_has_sample_depository_access = new SampleHasSampleDepository_Access(null);
		    			if ($sample_has_sample_depository_access->create($sample_id, $depository_id, $user->get_user_id()) == null)
		    			{
		    				$folder->delete(true, true);
							if ($transaction_id != null)
							{
								$transaction->rollback($transaction_id);
							}
							throw new SampleCreationFailedException("",1);
		    			}
		    			
		    			// Create Item
		    			$item = new Item(null);
						if (($this->item_id = $item->create()) == null)
						{
							$folder->delete(true, true);
							if ($transaction_id != null)
							{
								$transaction->rollback($transaction_id);
							}
							throw new SampleCreationFailedException("",1);
						}
						
						if ($item->link_sample($sample_id) == false)
						{
							$folder->delete(true, true);
							if ($transaction_id != null)
							{
								$transaction->rollback($transaction_id);
							}
							throw new SampleCreationFailedException("",1);
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
										$sample_has_sample = new SampleHasSample_Access(null);
										if ($sample_has_sample->create($value, $sample_id) == null)
										{
											$folder->delete(true, true);
											if ($transaction_id != null)
											{
												$transaction->rollback($transaction_id);
											}
											throw new SampleCreationFailedException("",1);
										}
									}
								}
							}
							
							if ($this->template_data_type == "value")
							{
								$value = new Value(null);				
								if ($value->create($folder_id, $user->get_user_id(), $this->template_data_type_id, $this->template_data_array, false) == null)
								{
									$folder->delete(true, true);
									if ($transaction_id != null)
									{
										$transaction->rollback($transaction_id);
									}
									throw new SampleCreationFailedException("",1);
								}
								
								$sample_item = new SampleItem($sample_id);
								
								if ($sample_item->set_gid(1) == false)
								{
									$folder->delete(true, true);
									if ($transaction_id != null)
									{
										$transaction->rollback($transaction_id);
									}
									throw new SampleCreationFailedException("",1);
								}
								
								$sample_item->set_item_id($value->get_item_id());
								
								if ($sample_item->link_item() == false)
								{
									$folder->delete(true, true);
									if ($transaction_id != null)
									{
										$transaction->rollback($transaction_id);
									}
									throw new SampleCreationFailedException("",1);
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
						throw new SampleCreationFailedException("",1);
					}
	    		}
	    		else
	    		{
	    			if ($transaction_id != null)
	    			{
						$transaction->rollback($transaction_id);
					}
	    			throw new SampleCreationFailedException("",1);
	    		}
	    	}
	    	else
	    	{
	    		throw new SampleCreationFailedException("",1);
	    	}
    	}
    	else
    	{
    		throw new SampleCreationFailedException("",1);
    	}
    }

	/**
	 * Deletes a sample
	 * @return bool
	 */
	public function delete()
	{
		global $transaction;
		
		if ($this->sample_id and $this->sample)
		{
			$transaction_id = $transaction->begin();
			
			$tmp_sample_id = $this->sample_id;
			
			// Delete Item
			if ($this->item_id) {
				$item = new Item($this->item_id);
				if ($item->delete() == false)
				{
					if ($transaction_id != null)
					{
						$transaction->rollback($transaction_id);
					}
					return false;
				}
			}
		
			// Parent Relations (on Null)
			$sample_has_sample_pid_array = SampleHasSample_Access::list_entries_by_sample_pid($tmp_sample_id);
			if (is_array($sample_has_sample_pid_array) and count($sample_has_sample_pid_array) >= 1)
			{
				foreach($sample_has_sample_pid_array as $key => $value)
				{
					$sample_has_sample = new SampleHasSample_Access($value);
					if ($sample_has_sample->set_sample_pid_on_null() == false)
					{
						if ($transaction_id != null)
						{
							$transaction->rollback($transaction_id);
						}
						return false;
					}
				}
			}
			
			// Child Relations (on Null)
			$sample_has_sample_cid_array = SampleHasSample_Access::list_entries_by_sample_cid($tmp_sample_id);
			if (is_array($sample_has_sample_cid_array) and count($sample_has_sample_cid_array) >= 1)
			{
				foreach($sample_has_sample_cid_array as $key => $value)
				{
					$sample_has_sample = new SampleHasSample_Access($value);
					if ($sample_has_sample->set_sample_cid_on_null() == false)
					{
						if ($transaction_id != null)
						{
							$transaction->rollback($transaction_id);
						}
						return false;
					}
				}
			}
			
			// Depository Relations
			$sample_has_sample_depository_array = SampleHasSampleDepository_Access::list_entries_by_sample_id($tmp_sample_id);
			if (is_array($sample_has_sample_depository_array) and count($sample_has_sample_depository_array) >= 1)
			{
				foreach($sample_has_sample_depository_array as $key => $value)
				{
					$sample_has_sample_depository = new SampleHasSampleDepository_Access($value);
					if ($sample_has_sample_depository->delete() == false)
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
				foreach($item_array as $key => $value)
				{
					$item = new Item($value);
					if ($item->delete() == false)
					{
						if ($transaction_id != null)
						{
							$transaction->rollback($transaction_id);
						}
						return false;
					}
				}
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
	    		$folder_id = Folder::get_sample_folder_by_sample_id($tmp_sample_id);
	    		$folder = new Folder($folder_id);
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
	 * Returns all requirements
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
			return $return_array;
    	}
    	else
    	{
    		return null;
    	}
    }
    
    /**
     * Returns fulfilled requirements
     * @return array
     */
    public function get_fulfilled_requirements()
    {
    	if ($this->sample_id and $this->sample)
    	{
	    	$requirements_array = $this->get_requirements();			
			$fulfilled_array = array();
			
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
					
					switch($value[type]):
					
						case("file"):
							$sample_item = new SampleItem($this->sample_id);
							$item_array = $sample_item->get_sample_items();
							if (is_array($item_array) and count($item_array) >= 1)
							{
								foreach($item_array as $item_key => $item_value)
								{
									$item = new Item($item_value);
									$item_has_sample_gid = new ItemHasSampleGid($item_value);

									if (($object_id = $item->get_object_id()) != null and $item_has_sample_gid->get_gid() == $gid)
									{
										$object = new Object($object_id);
										if ($object->get_file_id() != null)
										{
											$fulfilled_array[$key] = true;
										}
									}
								}
							}
						break;
							
						case("value"):
							$sample_item = new SampleItem($this->sample_id);
							$item_array = $sample_item->get_sample_items();
							if (is_array($item_array) and count($item_array) >= 1)
							{	
								foreach($item_array as $item_key => $item_value)
								{
									$item = new Item($item_value);
									$item_has_sample_gid = new ItemHasSampleGid($item_value);

									if (($object_id = $item->get_object_id()) != null and $item_has_sample_gid->get_gid() == $gid)
									{
										$object = new Object($object_id);
										
										if(($value_id = $object->get_value_id()) != null)
										{
											$value_obj = new Value($value_id);
											if (is_array($value[type_id]))
											{
												if (in_array($value_obj->get_type_id(),$value[type_id]))
												{
													$fulfilled_array[$key] = true;	
												}
											}
											else
											{
												$fulfilled_array[$key] = true;	
											}		
										}
									}
								}
							}
						break;
						
						case("method"):
							$sample_item = new SampleItem($this->sample_id);
							$item_array = $sample_item->get_sample_items();
							if (is_array($item_array) and count($item_array) >= 1)
							{
								foreach($item_array as $item_key => $item_value)
								{
									$item = new Item($item_value);
									$item_has_sample_gid = new ItemHasSampleGid($item_value);
									if ($item->get_method_id() != null and $item_has_sample_gid->get_gid() == $gid)
									{
										$fulfilled_array[$key] = true;	
									}
								}
							}
						break;
						
						case("sample"):
						case("parentsample"):
							$sample_item = new SampleItem($this->sample_id);
							$item_array = $sample_item->get_sample_items();
							if (is_array($item_array) and count($item_array) >= 1)
							{
								foreach($item_array as $item_key => $item_value)
								{
									$item = new Item($item_value);
									$item_has_sample_gid = new ItemHasSampleGid($item_value);
									if ($item->get_sample_id() != null and $item_has_sample_gid->get_gid() == $gid)
									{
										$fulfilled_array[$key] = true;	
									}
								}
							}
						break;
					
					endswitch;
					
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
     * Returns subfolder of a given gid
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
				$sample_folder_id = Folder::get_sample_folder_by_sample_id($this->sample_id);
	    		
	    		if ($folder_id == $sample_folder_id)
	    		{
	    			$folder = new Folder($folder_id);
	    		
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
     * Links another sample with the current sample as child
     * @param integer $sample_id Sample-ID
     * @return bool
     */
    public function link_sample($sample_id)
    {
    	if ($this->sample_id and $this->sample and is_numeric($sample_id))
    	{
    		$sample_has_sample = new SampleHasSample_Access(null);
    		
    		if ($sample_has_sample->create($this->sample_id, $sample_id) != null)
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
     * Unlinks another sample with the current sample as child
     * @param integer $sample_id Sample-ID
     * @return bool
     */
    public function unlink_sample($sample_id)
    {
    	if ($this->sample_id and $this->sample and is_numeric($sample_id))
    	{
	    	$pk = SampleHasSample_Access::get_entry_by_sample_pid_and_sample_cid($this->sample_id, $sample_id);
	    	if ($pk)
	    	{
	    		$sample_has_sample = new SampleHasSample_Access($pk);
	    		return $sample_has_sample->delete();
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
     * Links another sample with the current sample as parent
     * @param integer $sample_id Sample-ID
     * @return bool
     */
    public function link_parent_sample($sample_id)
    {
    	if ($this->sample_id and $this->sample and is_numeric($sample_id))
    	{
    		$sample_has_sample = new SampleHasSample_Access(null);
    		if ($sample_has_sample->create($sample_id, $this->sample_id) != null)
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
     * Unlinks another sample with the current sample as parent
     * @param integer $sample_id Sample-ID
     * @return bool
     */
    public function unlink_parent_sample($sample_id)
    {
    	if ($this->sample_id and $this->sample and is_numeric($sample_id))
    	{
	    	$sample_has_sample = new SampleHasSample_Access(null);
	    	$pk = $sample_has_sample->get_entry_by_sample_cid_and_sample_pid($this->sample_id, $sample_id);
	    	if ($pk)
	    	{
	    		$sample_has_sample = new SampleHasSample_Access($pk);
	    		return $sample_has_sample->delete();
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
     * Returns all related childs
     * @return array
     */
    public function list_related_samples()
    {
    	if ($this->sample_id and $this->sample)
    	{
    		$pk_array = SampleHasSample_Access::list_entries_by_sample_pid($this->sample_id);
    		if (is_array($pk_array) and count($pk_array) >= 1)
    		{
    			$return_array = array();
    			foreach ($pk_array as $key => $value)
    			{
    				$sample_has_sample = new SampleHasSample_Access($value);
    				array_push($return_array, $sample_has_sample->get_sample_cid());
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
     * Returns all related parents
     * @return array
     */
    public function list_parent_samples()
    {
    	if ($this->sample_id and $this->sample)
    	{
    		$pk_array = SampleHasSample_Access::list_entries_by_sample_cid($this->sample_id);
    		if (is_array($pk_array) and count($pk_array) >= 1)
    		{
    			$return_array = array();
    			foreach ($pk_array as $key => $value)
    			{
    				$sample_has_sample = new SampleHasSample_Access($value);
    				array_push($return_array, $sample_has_sample->get_sample_pid());
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
     * Adds a new depository to the current sample
     * @param integer $depository_id
     * @return bool
     */
    public function add_depository($depository_id)
    {
    	global $user;
    	
    	if ($this->sample_id and $this->sample and is_numeric($depository_id))
    	{
    		$sample_has_sample_depository = new SampleHasSampleDepository_Access(null);
    		if ($depository_id != $this->get_current_depository())
    		{
	    		if ($sample_has_sample_depository->create($this->sample_id, $depository_id, $user->get_user_id()) != null)
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
     * Returns all depositories
     * @return array
     */
    public function get_all_depositories()
    {
    	if ($this->sample_id and $this->sample)
    	{
    		$pk_array = SampleHasSampleDepository_Access::list_entries_by_sample_id($this->sample_id);
    		if (is_array($pk_array) and count($pk_array) >= 1)
    		{
    			$return_array = array();
    			foreach ($pk_array as $key => $value)
    			{
    				$sample_has_sample_depository = new SampleHasSampleDepository_Access($value);
    				array_push($return_array, $sample_has_sample_depository->get_sample_depository_id());
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
     * Returns all depositories with special information
     * @return array
     */
    public function get_all_depository_information()
    {
    	if ($this->sample_id and $this->sample)
    	{
    		$pk_array = SampleHasSampleDepository_Access::list_entries_by_sample_id($this->sample_id);
    		if (is_array($pk_array) and count($pk_array) >= 1)
    		{
    			$return_array = array();
    			$counter = 0;
    			
    			foreach ($pk_array as $key => $value)
    			{
    				$sample_has_sample_depository = new SampleHasSampleDepository_Access($value);
    				
    				$return_array[$counter][id] = $sample_has_sample_depository->get_sample_depository_id();
    				$return_array[$counter][datetime] = $sample_has_sample_depository->get_datetime();
    				$return_array[$counter][user_id] = $sample_has_sample_depository->get_user_id();
    				
    				$counter++;
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
     * Returns current depository
     * @return integer
     */
    public function get_current_depository()
    {
    	if ($this->sample_id and $this->sample)
    	{
    		$depository_array = $this->get_all_depositories();
    		return $depository_array[count($depository_array)-1];
    	}
    	else
    	{
    		return null;
    	}	
    }    

	/**
	 * @return integer
	 */
    public function get_item_id()
    {
    	if ($this->item_id)
    	{
    		return $this->item_id;
    	}
    	else
    	{
    		return null;
    	}
    }
    
    /**
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
    
    /**
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
     * @return string
     */
    public function get_supplier()
    {
    	if ($this->sample_id and $this->sample)
    	{
    		return $this->sample->get_supplier();
    	}
    	else
    	{
    		return null;
    	}
    }
    
    /**
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
	 * Returns the name of the current depository
	 * @return string
	 */
	public function get_current_depository_name()
	{
		if ($this->sample_id and $this->sample)
		{
			$sample_depository_id = $this->get_current_depository();
			$sample_depository = new SampleDepository($sample_depository_id);
			if ($sample_depository->get_name()) 
			{
				return $sample_depository->get_name();
			}
			else
			{
				return "unknow";
			}
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * Returns the name of template
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
	 * Returns the ID of the current sample as S000000X
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
	 * @param string $name
	 * @return bool
	 */
	public function set_name($name)
	{
		global $transaction;

		if ($this->sample_id and $this->sample and $name)
		{
    		$transaction_id = $transaction->begin();

	    	$folder_id = Folder::get_sample_folder_by_sample_id($this->sample_id);
	    	$folder = new Folder($folder_id);

			if ($folder->set_name($name) == false)
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
	 * @param string $supplier
	 * @return bool
	 */
	public function set_supplier($supplier)
	{
		if ($this->sample_id and $this->sample and $supplier)
		{
    		return $this->sample->set_supplier($supplier);
    	}
    	else
    	{
    		return false;
    	}
	}
	
	/**
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
	 * Returns true if a sample exists
	 * @param integer $sample_id
	 * @return bool
	 */
   	public static function exist_sample($sample_id)
   	{
		return Sample_Access::exist_sample_by_sample_id($sample_id);
   	}
   	  
   	/**
   	 * Lists all user-related samples
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
     * Lists all OU related samples
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
   	 * @param integer $template_id
   	 * @return array
   	 */
    public static function list_entries_by_template_id($template_id)
    {
    	return Sample_Access::list_entries_by_template_id($template_id);;
    }
    
    /**
     * Returns the number of samples of an user
     * @param integer $user_id
     * @return integer
     * @todo implementation
     */
    public static function count_samples($user_id)
    {
    	
    }
	
	/**
	 * Searchs a sample
	 * @param string $string Search-String
	 * @param array $organisation_unit_array Array of Organisation Units
	 * @param array $template_array Array of Sample-Templates
	 * @param bool $in_id Searchs in ID
	 * @param bool $in_name Searchs in Name
	 * @return array
	 * @todo Replace with Wrapper-Method
	 */
	public static function search_samples($string, $organisation_unit_array, $template_array, $in_id, $in_name)
	{
    	if (($string or ((is_array($template_array) and count($template_array) >= 1))) and 
    		is_array($organisation_unit_array) and 
    		count($organisation_unit_array) >= 1)
    	{
			if ($string)
			{
				$string = strtolower($string);
			}
			
			if ($in_id)
			{
				$id = $string;
			}
			else
			{
				$id = null;
			}
			
			if ($in_name)
			{
				$name = $string;
			}
			else
			{
				$name = null;
			}

    		return SampleJoin_Access::search_samples($id, $name, $template_array, $organisation_unit_array);
    	}
    	else
    	{
    		return null;
    	}
    }
    
    /**
     * @todo implementation
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
    	
    	return true;
    }
    
}
?>