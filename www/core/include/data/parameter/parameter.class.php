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
	protected $parameter_id;
	
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
				
				$parameter_version_id = ParameterVersion_Access::get_current_entry_by_toid($parameter_id);
				$this->parameter_version = new ParameterVersion_Access($parameter_version_id);
	
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
	
	protected function create()
	{
		
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