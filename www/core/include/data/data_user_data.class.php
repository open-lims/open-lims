<?php
/**
 * @package data
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
require_once("interfaces/data_user_data.interface.php");

if (constant("UNIT_TEST") == false or !defined("UNIT_TEST"))
{
	require_once("access/data_user_data.access.php");
}

/**
 * Data User Data Class
 * @package data
 */
class DataUserData implements DataUserDataInterface, EventListenerInterface
{
	private $user_id;
	private $data_user_data;
	
	/**
	 * @see DataUserDataInterface::__construct()
	 * @param integer $user_id
	 */
	function __construct($user_id)
	{
		if (is_numeric($user_id))
		{
			$this->user_id = $user_id;
			$this->data_user_data = new DataUserData_Access($user_id);	
		}
		else
		{
			$this->user_id = null;
			$this->data_user_data = null;
		}
	}
	
	function __destruct()
	{
		unset($this->user_id);
		unset($this->data_user_data);
	}
	
	/**
	 * @see DataUserDataInterface::get_quota()
	 * @return integer
	 */
	public function get_quota()
	{
		if ($this->user_id)
		{
			return $this->data_user_data->get_quota();
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @see DataUserDataInterface::get_filesize()
	 * @return integer
	 */
	public function get_filesize()
	{
		if ($this->user_id)
		{
			return $this->data_user_data->get_filesize();
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @see DataUserDataInterface::set_quota()
	 * @param integer $quota
	 * @return bool
	 */
	public function set_quota($quota)
	{
		if ($this->user_id)
		{
			return $this->data_user_data->set_quota($quota);
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @see DataUserDataInterface::set_filesize()
	 * @param integer $filesize
	 * @return bool
	 */
	public function set_filesize($filesize)
	{
		if ($this->user_id)
		{
			return $this->data_user_data->set_filesize($filesize);
		}
		else
		{
			return null;
		}
	}
	
	
	/**
	 * @see DataUserDataInterface::get_used_space()
	 * @return integer
	 */
	public static function get_used_space()
	{
		return DataUserData_Access::get_used_space();
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
    		$data_user_data_access = new DataUserData_Access(null);
    		if ($data_user_data_access->create($event_object->get_user_id(),constant("USER_STD_QUOTA")) == false)
    		{
    			return false;
    		}
    	}
    	
    	if ($event_object instanceof UserDeleteEvent)
    	{
    		$data_user_data_access = new DataUserData_Access($event_object->get_user_id());
    		if ($data_user_data_access->delete() == false)
    		{
    			return false;
    		}
    	}
    	return true;
    }
}

?>