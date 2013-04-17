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
	
	protected function create($folder_id, $owner_id = null)
	{
		global $user, $transaction;
		
		if (is_numeric($folder_id))
		{
			$transaction_id = $transaction->begin();
			
			if ($owner_id == null)
			{
				$owner_id = $user->get_user_id();
			}
			
			$checksum = md5(serialize($value));
			
			$folder = Folder::get_instance($folder_id);
					
			if (($data_entity_id = parent::create($owner_id, null)) != null)
			{
				if (parent::set_as_child_of($folder->get_data_entity_id()) == false)
				{
					if ($transaction_id != null)
					{
						$transaction->rollback($transaction_id);
					}
					return null;
				}
				
				$parameter_access = new Parameter_Access(null);
				if (($parameter_id = $parameter_access->create($data_entity_id)) == null)
				{
					if ($transaction_id != null)
					{
						$transaction->rollback($transaction_id);
					}
					return null;
				}
				
				$parameter_version_access = new ParameterVersion_Access(null);
				if (($parameter_version_id = $parameter_version_access->create($parameter_id, 1, 1, null, true, $owner_id, null)) == null)
				{
					if ($transaction_id != null)
					{
						$transaction->rollback($transaction_id);
					}
					return null;
				}
				else
				{
					if ($transaction_id != null)
					{
						$transaction->commit($transaction_id);
					}
					$this->__construct($parameter_id);
					return $parameter_id;
				}
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
	
	protected function update()
	{
		
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

}
?>