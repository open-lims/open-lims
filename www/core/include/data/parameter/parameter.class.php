<?php
/**
 * @package data
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
require_once("interfaces/parameter.interface.php");

if (constant("UNIT_TEST") == false or !defined("UNIT_TEST"))
{
	require_once("access/parameter.access.php");
	require_once("access/parameter_version.access.php");
	require_once("access/parameter_field_value.access.php");
}

/**
 * Parameter Management Class
 * @package data
 */
class Parameter extends DataEntity implements ParameterInterface, EventListenerInterface
{
	protected static $parameter_object_array;
	
	protected $parameter_id;
	protected $parameter_version_id;
	
	protected $parameter;
	protected $parameter_version;
	
	function __construct($parameter_id)
	{
		if (is_numeric($parameter_id))
    	{
    		if (Parameter_Access::exist_parameter_by_parameter_id($parameter_id) == true)
    		{
    			$this->parameter_id = $parameter_id;
				$this->parameter = new Parameter_Access($parameter_id);
				
				$this->parameter_version_id = ParameterVersion_Access::get_current_entry_by_parameter_id($parameter_id);
				$this->parameter_version = new ParameterVersion_Access($this->parameter_version_id);
				
				parent::__construct($this->parameter->get_data_entity_id());
    		}
    		else
    		{
    			throw new ParameterNotFoundException();
    		}
    	}
    	else
    	{
    		parent::__construct(null);
			$this->parameter_id = null;
			$this->parameter = new Parameter_Access(null);
			$this->parameter_version = new ParameterVersion_Access(null);
    	}
	}
	
	function __destruct()
	{
		unset($this->parameter_id);
    	unset($this->parameter);
    	unset($this->parameter_version);
	}
	
	protected function create($folder_id, $parameter_array, $owner_id = null)
	{
		global $user, $transaction;
		
		if (is_numeric($folder_id) and is_array($parameter_array))
		{
			$transaction_id = $transaction->begin();
			
			try
			{
				if ($owner_id == null)
				{
					$owner_id = $user->get_user_id();
				}
				
				$folder = Folder::get_instance($folder_id);
						
				$data_entity_id = parent::create($owner_id, null);
				parent::set_as_child_of($folder->get_data_entity_id());
				
				$parameter_access = new Parameter_Access(null);
				if (($parameter_id = $parameter_access->create($data_entity_id)) == null)
				{
					throw new ParameterCreateFailedException();
				}
				
				$parameter_version_access = new ParameterVersion_Access(null);
				if (($parameter_version_id = $parameter_version_access->create($parameter_id, 1, 1, null, true, $owner_id, null)) == null)
				{
					throw new ParameterCreateVersionCreateFailedException();
				}
				
				foreach($parameter_array as $key => $value)
				{
					$parameter_field_value = new ParameterFieldValue_Access(null);
					if ($parameter_field_value->create($parameter_version_id, $key, $value['method'], $value['value']) == null)
					{
						throw new ParameterCreateValueCreateFailedException();
					}
				}
			}
			catch(BaseException $e)
			{
				if ($transaction_id != null)
				{
					$transaction->rollback($transaction_id);
				}
				throw $e;
			}
			
			if ($transaction_id != null)
			{
				$transaction->commit($transaction_id);
			}
			
			self::__construct($parameter_id);
			
			return $parameter_id;
		}
		else
		{
			throw new ParameterCreateIDMissingException();
		}
	}
	
	public function delete()
	{
		
	}
	
	public function delete_version()
	{
		
	}
	
	public function exist_parameter_version($internal_revision)
	{
		
	}
	
	public function get_version()
	{
		if ($this->parameter_version)
		{
			if ($this->parameter_version->get_id() == $this->parameter_version->get_previous_version_id())
			{
				return $this->parameter_version->get_version();
			}
			else
			{
				$tmp_parameter_version_id = $this->parameter_version->get_id();
				$tmp_internal_revision = $this->parameter_version->get_internal_revision();
				$tmp_previous_version_id = $this->parameter_version->get_previous_version_id();
				
				$return_string = $this->parameter_version->get_version();
				
				do
				{
					$tmp_parameter_version = new ParameterVersion_Access($tmp_previous_version_id);
					$return_string = $tmp_parameter_version->get_version().".".$return_string;
					$tmp_previous_version_id = $tmp_parameter_version->get_previous_version_id();
				}while($tmp_parameter_version->get_id() != $tmp_parameter_version->get_previous_version_id());
			
				return $return_string;
			}
		}
		else
		{
			return null;
		}
	}
	
	public function get_values()
	{
		if ($this->parameter_version_id)
		{
			return ParameterFieldValue_Access::list_values($this->parameter_version_id);
		}
		else
		{
			return null;
		}
	}
	
	public function get_methods()
	{
		if ($this->parameter_version_id)
		{
			return ParameterFieldValue_Access::list_methods($this->parameter_version_id);
		}
		else
		{
			return null;
		}
	}
	
	public function get_name()
	{
		
	}
	
	/**
	 * @param array $parameter_array
	 * @param integer $previous_version_id
	 * @param bool $major
	 * @param bool $current
	 * @return bool
	 * @throws BaseUserAccessDeniedException
	 * @throws ParameterUpdateVersionCreateFailedException
	 * @throws ParameterUpdateValueCreateFailedException
	 * @throws ParameterUpdateNoValuesException
	 * @throws ParameterNoInstanceException
	 */
	public function update($parameter_array, $previous_version_id = null, $major = true, $current = true)
	{
		global $transaction, $user;
		
		if ($this->parameter_id and $this->parameter_version_id)
		{
			if ($this->is_write_access())
			{
				if (is_array($parameter_array) and count($parameter_array) >= 1)
				{
					$changed = false;
					
					foreach($parameter_array as $key => $value)
					{
						$parameter_field_value_id = ParameterFieldValue_Access::get_id_by_version_id_and_field_id($this->parameter_version_id, $key);
						$parameter_field_value = new ParameterFieldValue_Access($parameter_field_value_id);
												
						if ($parameter_field_value->get_value() != $value['value'])
						{
							$changed = true;
						}
						
						if ($parameter_field_value->get_parameter_method_id() != $value['method'])
						{
							$changed = true;
						}
					}
					
					if ($changed == true)
					{						
						$transaction_id = $transaction->begin();
						
						try
						{
							$current_parameter_version_id = ParameterVersion_Access::get_current_entry_by_parameter_id($this->parameter_id);
							$current_parameter_version = new ParameterVersion_Access($current_parameter_version_id);
					
							$new_internal_revision = $current_parameter_version->get_internal_revision()+1;
							
							if ($major == true)
							{									
								if ($previous_version_id == null)
								{
									$new_version = $current_parameter_version->get_version()+1;
									$previous_version_pk_id = null;
								}
								else
								{
									$major_parameter_version_id = ParameterVersion_Access::get_entry_by_parameter_id_and_internal_revision($this->parameter_id, $previous_version_id);
									$major_parameter_version = new ParameterVersion_Access($major_parameter_version_id);
									
									if ($major_parameter_version->get_previous_version_id() == $major_parameter_version->get_id())
									{
										$previous_version_pk_id = null;
									}
									else
									{
										$previous_version_pk_id = $major_parameter_version->get_previous_version_id();
									}
									
									$major_parameter_version_id = ParameterVersion_Access::get_highest_major_version_entry_by_parameter_id_and_previous_version_id($major_parameter_version->get_toid(), $previous_version_pk_id);
									$major_parameter_version = new ParameterVersion_Access($major_parameter_version_id);
									
									$new_version = $major_parameter_version->get_version()+1;
								}
							}
							else
							{				
								$major_parameter_version_id = ParameterVersion_Access::get_entry_by_parameter_id_and_internal_revision($this->parameter_id, $previous_version_id);
								
								$current_minor_version_id = ParameterVersion_Access::get_highest_minor_version_entry_by_id($major_parameter_version_id);
								
								if ($current_minor_version_id)
								{
									$current_minor_version = new ParameterVersion_Access($current_minor_version_id);
									$new_version = $current_minor_version->get_version() + 1;
								}
								else
								{
									$new_version = 1;
								}								
								
								$previous_version_pk_id = $major_value_version_id;
							}
			
							if ($current == true)
							{
								if (($parameter_version_id = $this->parameter_version->create($this->parameter_id, $new_version, $new_internal_revision, $previous_version_pk_id, true, $user->get_user_id(), null)) == null)
								{
									throw new ParameterUpdateVersionCreateFailedException();
								}
	
								if ($current_parameter_version->set_current(false) == false)
								{
									throw new ParameterUpdateVersionCreateFailedException();
								}
							}
							else
							{
								if (($parameter_version_id = $this->parameter_version->create($this->parameter_id, $new_version, $new_internal_revision, $previous_version_pk_id, false, $user->get_user_id(), null)) == null)
								{
									throw new ParameterUpdateVersionCreateFailedException();
								}
							}
		
		
							foreach($parameter_array as $key => $value)
							{
								$parameter_field_value = new ParameterFieldValue_Access(null);
								if ($parameter_field_value->create($parameter_version_id, $key, $value['method'], $value['value']) == null)
								{
									throw new ParameterUpdateValueCreateFailedException();
								}
							}
						}
						catch(BaseException $e)
						{
							if ($transaction_id != null)
							{
								$transaction->rollback($transaction_id);
							}
							throw $e;
						}
						
						if ($transaction_id != null)
						{
							$transaction->commit($transaction_id);
						}
						
						$this->parameter_version_id = $parameter_version_id;
					
						return true;
					}
					else
					{
						return true;
					}
				}
				else
				{
					throw new ParameterUpdateNoValuesException();
				}
			}
			else
			{
				throw new BaseUserAccessDeniedException();
			}
		}
		else
		{
			throw new ParameterNoInstanceException();
		}
	}
	
	
	public static function get_parameter_id_by_data_entity_id($date_entity_id)
	{
		return Parameter_Access::get_entry_by_data_entity_id($data_entity_id);
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
    		/*if (ValueVersion_Access::set_owner_id_on_null($event_object->get_user_id()) == false)
    		{
    			return false;
    		}*/
    	}
    	
    	return true;
    }

    public static function get_instance($parameter_id, $force_new_instance = false)
    { 
    	
    }
    
}
?>