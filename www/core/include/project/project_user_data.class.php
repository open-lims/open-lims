<?php
/**
 * @package project
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
require_once("interfaces/project_user_data.interface.php");

if (constant("UNIT_TEST") == false or !defined("UNIT_TEST"))
{
	require_once("access/project_user_data.access.php");
}

/**
 * Project User Data Class
 * @package project
 */
class ProjectUserData implements ProjectUserDataInterface, EventListenerInterface
{
	private $user_id;
	private $project_user_data;
	
	/**
	 * @see ProjectUserDataInterface::__construct()
	 * @param integer $user_id
	 */
	function __construct($user_id)
	{
		if (is_numeric($user_id))
		{
			$this->user_id = $user_id;
			$this->project_user_data = new ProjectUserData_Access($user_id);	
		}
		else
		{
			$this->user_id = null;
			$this->project_user_data = null;
		}
	}
	
	function __destruct()
	{
		unset($this->user_id);
		unset($this->project_user_data);
	}
	
	/**
	 * @see ProjectUserDataInterface::get_quota()
	 * @return integer
	 */
	public function get_quota()
	{
		if ($this->user_id)
		{
			return $this->project_user_data->get_quota();
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @see ProjectUserDataInterface::set_quota()
	 * @param integer $quota
	 * @return bool
	 * @throws ProjectUserSetQuotaException
	 */
	public function set_quota($quota)
	{
		if ($this->user_id)
		{
			if ($this->project_user_data->set_quota($quota) == true)
			{
				return true;
			}
			else
			{
				throw new ProjectUserSetQuotaException();
			}
		}
		else
		{
			throw new ProjectUserSetQuotaException();
		}
	}	
	
	/**
	 * @see EventListenerInterface::listen_events()
     * @param object $event_object
     * @return bool
     */
    public static function listen_events($event_object)
    {
    	if ($event_object instanceof UserCreateEvent)
    	{
    		$project_user_data_access = new ProjectUserData_Access(null);
    		if ($project_user_data_access->create($event_object->get_user_id(),(int)Registry::get_value("project_user_default_quota")) == false)
    		{
    			return false;
    		}
    	}
    	
    	if ($event_object instanceof UserDeleteEvent)
    	{
    		$project_user_data_access = new ProjectUserData_Access($event_object->get_user_id());
    		if ($project_user_data_access->delete() == false)
    		{
    			return false;
    		}
    	}
    	return true;
    }
}

?>