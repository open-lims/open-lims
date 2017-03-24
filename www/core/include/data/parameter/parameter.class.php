<?php
/**
 * @package data
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
require_once("interfaces/parameter.interface.php");

if (constant("UNIT_TEST") == false or !defined("UNIT_TEST"))
{
	require_once("access/parameter.access.php");
	require_once("access/parameter_version.access.php");
	require_once("access/parameter_field_value.access.php");
	require_once("access/parameter_field_limit.access.php");
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
	
	protected $ci_folder_id;
	protected $ci_limit_id;
	protected $ci_parameter_id;
	
	/**
	 * @param integer $parameter_id
	 */
	function __construct($id)
	{
		if (is_numeric($id))
    	{
    		if (Parameter_Access::exist_parameter_by_parameter_id($id) == true)
    		{
    			$this->parameter_id = $parameter_id;
				$this->parameter = new Parameter_Access($id);
				
				$this->parameter_version_id = ParameterVersion_Access::get_current_entry_by_parameter_id($id);
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
	
	public function open_internal_revision($internal_revision)
	{
		if (is_numeric($internal_revision) and $this->parameter_id)
		{
			if (ParameterVersion_Access::exist_internal_revision($this->parameter_id, $internal_revision) == true)
			{
				$this->parameter_version_id = ParameterVersion_Access::get_entry_by_parameter_id_and_internal_revision($this->parameter_id, $internal_revision);
				$this->parameter_version = new ParameterVersion_Access($this->parameter_version_id);
				return true;
			}
			else
			{
				throw new ParameterVersionNotFoundException();
			}
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * Opens another version of the parameter with parameter version id (primary key)
	 * @param integer $parameter_verion_id
	 * @return bool
	 */
	private function open_parameter_version_id($parameter_verion_id)
	{
		if (is_numeric($parameter_verion_id) and $this->parameter_id)
		{
			$this->parameter_version = new ParameterVersion_Access($parameter_verion_id);
			$this->parameter_version_id = $parameter_verion_id;
			return true;
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * @see ParameterInterface::create()
	 * @return integer
	 * @throws ParameterCreateFailedException
	 * @throws ParameterCreateVersionCreateFailedException
	 * @throws ParameterCreateValueCreateFailedException
	 * @throws ParameterCreateIDMissingException
	 */
	protected function create()
	{
		global $user, $regional, $transaction;
		
		if (is_numeric($this->ci_folder_id) and is_array($this->ci_parameter_array))
		{
			$transaction_id = $transaction->begin();
			
			try
			{
				if ($this->ci_owner_id == null)
				{
					$this->ci_owner_id = $user->get_user_id();
				}
				
				$folder = Folder::get_instance($this->ci_folder_id);
						
				$data_entity_id = parent::create();
				parent::set_as_child_of($folder->get_data_entity_id());
				
				$parameter_access = new Parameter_Access(null);
				if (($parameter_id = $parameter_access->create($data_entity_id)) == null)
				{
					throw new ParameterCreateFailedException();
				}
				
				$parameter_version_access = new ParameterVersion_Access(null);
				if (($parameter_version_id = $parameter_version_access->create($parameter_id, 1, 1, null, true, $this->ci_owner_id, null, $this->ci_limit_id)) == null)
				{
					throw new ParameterCreateVersionCreateFailedException();
				}
				
				foreach($this->ci_parameter_array as $key => $value)
				{
					$value['value'] = str_replace($regional->get_decimal_separator(),".",$value['value']);
					
					if (is_numeric($value['value']))
					{
						$parameter_field_value = new ParameterFieldValue_Access(null);
						if ($parameter_field_value->create($parameter_version_id, $key, $value['method'], $value['value']) == null)
						{
							throw new ParameterCreateValueCreateFailedException();
						}
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
	
	/**
	 * Injects $folder_id into create()
	 * @param integer $folder_id
	 */
	public function ci_set_folder_id($folder_id)
	{
		$this->ci_folder_id = $folder_id;
	}
	
	/**
	 * Injects $folder_id into create()
	 * @param integer $limit_id
	 */
	public function ci_set_limit_id($limit_id)
	{
		$this->ci_limit_id = $limit_id;
	}
	
	/**
	 * Injects $folder_id into create()
	 * @param array $parameter_array
	 */
	public function ci_set_parameter_array($parameter_array)
	{
		$this->ci_parameter_array = $parameter_array;
	}
		
	/**
	 * @return bool
	 * @throws ParameterDeleteVersionValueFailedException
	 * @throws ParameterDeleteVersionFailedException
	 * @throws ParameterDeleteFailedException
	 * @throws ParameterDeleteIDMissingException
	 */
	protected function delete()
	{
		global $transaction;
		
		if ($this->parameter_id and $this->parameter)
		{
			$transaction_id = $transaction->begin();
				
			try
			{
				$parameter_version_array = ParameterVersion_Access::list_entries_by_parameter_id($this->parameter_id);
				
				if (is_array($parameter_version_array) and count($parameter_version_array) >= 1)
				{
					foreach($parameter_version_array as $key => $value)
					{
						if(ParameterFieldValue_Access::delete_by_parameter_version_id($value) == false)
						{
							if ($transaction_id != null)
							{
								$transaction->rollback($transaction_id);
							}
							throw new ParameterDeleteVersionValueFailedException();
						}
						
						$parameter_version = new ParameterVersion_Access($value);
						
						if ($parameter_version->delete() == false)
						{
							if ($transaction_id != null)
							{
								$transaction->rollback($transaction_id);
							}
							throw new ParameterDeleteVersionFailedException();
						}
					}
				}	
				
				if ($this->parameter->delete() == false)
				{
					if ($transaction_id != null)
					{
						$transaction->rollback($transaction_id);
					}
					throw new ParameterDeleteFailedException();
				}
				
				parent::delete();				
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
			return true;
		}
		else
		{
			throw new ParameterDeleteIDMissingException();
		}
	}
	
	/**
	 * @param integer $internal_revision
	 * @return integer
	 * @throws ParameterDeletePreviousVersionValueFailedException
	 * @throws ParameterDeletePreviousVersionFailedException
	 * @throws ParameterDeleteVersionValueFailedException
	 * @throws ParameterDeleteVersionFailedException
	 * @throws ParameterDeleteIDMissingException
	 */
	public function delete_version($internal_revision)
	{
		global $transaction;
		
		if ($this->parameter_id and $this->parameter_version_id and $this->parameter_version and is_numeric($internal_revision))
		{
			$number_of_root_major_versions = ParameterVersion_Access::get_number_of_root_major_versions_by_parameter_id($this->parameter_id);
				
			if ($number_of_root_major_versions > 1)
			{
				$transaction_id = $transaction->begin();
									
				$parameter_version_id = ParameterVersion_Access::get_entry_by_parameter_id_and_internal_revision($this->parameter_id, $internal_revision);			
				
				$minor_parameter_array = ParameterVersion_Access::list_entries_by_previous_version_id($parameter_version_id);
				
				if (is_array($minor_parameter_array) and count($minor_parameter_array) >= 1)
				{
					foreach($minor_parameter_array as $fe_key => $fe_value)
					{
						if(ParameterFieldValue_Access::delete_by_parameter_version_id($fe_value) == false)
						{
							throw new ParameterDeletePreviousVersionValueFailedException();
						}
						
						$parameter = new Parameter($this->parameter_id);
						$parameter->open_parameter_version_id($fe_value);	
						if ($parameter->delete_version($parameter->get_internal_revision()) == false)
						{
							if ($transaction_id != null)
							{
								$transaction->rollback($transaction_id);
							}
							throw new ParameterDeletePreviousVersionFailedException();
						}								
					}	
				}

				if ($this->parameter_version->get_current() == true)
				{
					$next_current_parameter_version_id = ParameterVersion_Access::get_last_uploaded_version_entry_by_parameter_id($this->parameter_id, $internal_revision);
					$parameter_version_access = new ParameterVersion_Access($next_current_parameter_version_id);
					$parameter_version_access->set_current(true);
				}

				if(ParameterFieldValue_Access::delete_by_parameter_version_id($this->parameter_version_id) == false)
				{
					throw new ParameterDeleteVersionValueFailedException();
				}
				
				if ($this->parameter_version->delete() == false)
				{
					if ($transaction_id != null)
					{
						$transaction->rollback($transaction_id);
					}
					throw new ParameterDeleteVersionFailedException();
				}
				else
				{
					if ($transaction_id != null)
					{
						$transaction->commit($transaction_id);
					}
					return 1;
				}
			}
			else
			{
				if($this->delete() == true)
				{
					return 2;
				}
			}			
		}
		else
		{
			throw new ParameterDeleteIDMissingException();
		}
	}
	
	/**
	 * @todo implementation
	 */
	public function exist_parameter_version($internal_revision)
	{
		
	}
	
	/**
	 * @return bool
	 */
	public function is_current()
	{
		if ($this->parameter_id and $this->parameter_version)
		{
			if ($this->parameter_version->get_current() == true)
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
	 * @return integer
	 */
	public function get_id()
	{
		if($this->parameter_id)
		{
			return $this->parameter_id;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @see ValueInterface::get_value_internal_revisions()
	 * @return integer
	 */
	public function get_parameter_internal_revisions()
	{
		if ($this->parameter_id and $this->parameter_version)
		{
			$parameter_version_array = ParameterVersion_Access::list_entries_by_parameter_id($this->parameter_id);
			$return_array = array();
	
			foreach($parameter_version_array as $key => $value)
			{
				$parameter_version = new ParameterVersion_Access($value);
				array_push($return_array, $parameter_version->get_internal_revision());
			}
			
			if (count($return_array) > 0)
			{
				return $return_array;
			}
			else
			{
				return null;
			}			
		}
	}
	
	/**
	 * @return string
	 */
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
	
	/**
	 * @return array
	 */
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
		
	/**
	 * Returns an array with all current selected methods
	 * @return array
	 */
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
	
	/**
	 * @return string
	 */
	public function get_status()
	{
		if ($this->parameter_version_id)
		{
			$limit_id = $this->parameter_version->get_parameter_limit_id();
			$limit_array = ParameterFieldLimit_Access::list_limits_by_limit_id($limit_id);
			$value_array = ParameterFieldValue_Access::list_values($this->parameter_version_id);
						
			if (is_array($value_array) and (count($value_array) >= 1) and is_array($limit_array))
			{
				$return_array = array();
				
				foreach($value_array as $key => $value)
				{		
					if (is_numeric($limit_array[$key]['lsl']) and $value < $limit_array[$key]['lsl'])
					{
						$return_array[$key] = "min";
					}
					elseif (is_numeric($limit_array[$key]['usl']) and $value > $limit_array[$key]['usl'])
					{
						$return_array[$key] = "max";
					}
					else
					{
						$return_array[$key] = "ok";
					}
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
	 * @todo implementation
	 */
	public function get_name()
	{
		return "Parameter";
	}
	
	/**
	 * @return string
	 */
	public function get_version_datetime()
	{
		if ($this->parameter_version)
		{
			return $this->parameter_version->get_datetime();
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return integer
	 */
	public function get_internal_revision()
	{
		if ($this->parameter_version)
		{
			return $this->parameter_version->get_internal_revision();
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return integer
	 */
	public function get_limit_id()
	{
		if ($this->parameter_version_id)
		{
			return $this->parameter_version->get_parameter_limit_id();
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return array
	 */
	public function get_limits()
	{
		if ($this->parameter_version_id)
		{
			$limit_id = $this->parameter_version->get_parameter_limit_id();
			return ParameterFieldLimit_Access::list_limits_by_limit_id($limit_id);
		}
		else
		{
			return null;
		}
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
	public function update($parameter_array, $limit_id, $previous_version_id = null, $major = true, $current = true)
	{
		global $transaction, $regional, $user;
		
		if ($this->parameter_id and $this->parameter_version_id)
		{
			if ($this->is_write_access())
			{
				if (is_array($parameter_array) and is_numeric($limit_id) and count($parameter_array) >= 1)
				{
					$changed = false;
					
					foreach($parameter_array as $key => $value)
					{
						$parameter_field_value_id = ParameterFieldValue_Access::get_id_by_version_id_and_field_id($this->parameter_version_id, $key);
						$parameter_field_value = new ParameterFieldValue_Access($parameter_field_value_id);

						$value['value'] = str_replace($regional->get_decimal_separator(),".",$value['value']);
						
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
								if (($parameter_version_id = $this->parameter_version->create($this->parameter_id, $new_version, $new_internal_revision, $previous_version_pk_id, true, $user->get_user_id(), null, $limit_id)) == null)
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
								if (($parameter_version_id = $this->parameter_version->create($this->parameter_id, $new_version, $new_internal_revision, $previous_version_pk_id, false, $user->get_user_id(), null, $limit_id)) == null)
								{
									throw new ParameterUpdateVersionCreateFailedException();
								}
							}
		
		
							foreach($parameter_array as $key => $value)
							{
								$value['value'] = str_replace($regional->get_decimal_separator(),".",$value['value']);
								
								if (is_numeric($value['value']))
								{
									$parameter_field_value = new ParameterFieldValue_Access(null);
									if ($parameter_field_value->create($parameter_version_id, $key, $value['method'], $value['value']) == null)
									{
										throw new ParameterUpdateValueCreateFailedException();
									}
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
	
	/**
	 * @param integer $data_entity_id
	 * @return integer
	 */
	public static function get_parameter_id_by_data_entity_id($data_entity_id)
	{
		return Parameter_Access::get_entry_by_data_entity_id($data_entity_id);
	}
	
	/**
	 * @param integer $method_id
	 * @return bool
	 */
	public static function is_method_linked($method_id)
	{
		return ParameterFieldValue_Access::is_method_linked($method_id);
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
    		if (ParameterVersion_Access::set_owner_id_on_null($event_object->get_user_id()) == false)
    		{
    			return false;
    		}
    	}
    	
    	return true;
    }

    /**
     * @param integer $parameter_id
     * @param bool $force_new_instance
     * @return object
     */
    public static function get_instance($parameter_id, $force_new_instance = false)
    { 
    	if(is_numeric($parameter_id) and $parameter_id > 0)
    	{
    		if(ParameterTemplateParameter::is_template_parameter($parameter_id) == true)
    		{
	    		if($force_new_instance == true)
	    		{
	    			return new ParameterTemplateParameter($parameter_id);
	    		}
	    		else
	    		{
		    		if (self::$parameter_object_array[$parameter_id])
					{
						return self::$parameter_object_array[$parameter_id];
					}
					else
					{
						$parameter = new ParameterTemplateParameter($parameter_id);
						self::$parameter_object_array[$parameter_id] = $parameter;
						return $parameter;
					}
	    		}
    		}
    		elseif(ParameterNonTemplateParameter::is_non_template_parameter($parameter_id) == true)
    		{
	    		if($force_new_instance == true)
	    		{
	    			return new ParameterNonTemplateParameter($parameter_id);
	    		}
	    		else
	    		{
		    		if (self::$parameter_object_array[$parameter_id])
					{
						return self::$parameter_object_array[$parameter_id];
					}
					else
					{
						$parameter = new ParameterNonTemplateParameter($parameter_id);
						self::$parameter_object_array[$parameter_id] = $parameter;
						return $parameter;
					}
	    		}
    		}
    		else
    		{
    			return null;
    		}
    	}
    	else
    	{
    		return new ParameterTemplateParameter(null);
    	}
    }
}
?>