<?php
/**
 * @package base
 * @version 0.4.0.0
 * @author Roman Konertz <konertz@open-lims.org>
 * @copyright (c) 2008-2014 by Roman Konertz
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
require_once("interfaces/system_message.interface.php");

if (constant("UNIT_TEST") == false or !defined("UNIT_TEST"))
{
	require_once("access/system_message.access.php");
	require_once("access/system_log_type.access.php");
}

/**
 * System Message Management Class
 * @package base
 */
class SystemMessage implements SystemMessageInterface, EventListenerInterface
{
	public $id;
	public $system_message;

	/**
	 * @see SystemMessageInterface::__construct()
	 * @param integer $id
	 * @throws SystemMessageNotFoundException
	 */
	function __construct($id)
	{
		if (is_numeric($id))
		{
			if (SystemMessage_Access::exist_id($id) == true)
			{
				$this->id = $id;
				$this->system_message = new SystemMessage_Access($id);
			}
			else
			{
				throw new SystemMessagNotFoundException();
			}
		}
		else
		{
			$this->id = null;
			$this->system_message = new SystemMessage_Access(null);
		}
	}
	
	function __destruct()
	{
		unset($this->id);
		unset($this->system_message);
	}
	
	/**
	 * @see SystemMessageInterface::create()
	 * @param integer $user_id
	 * @param string $content
	 * @return integer
	 */
	public function create($user_id, $content)
	{
		if ($this->system_message)
		{
			$id = $this->system_message->create($user_id, $content);
			if ($id)
			{
				self::__construct($id);
				return $id;
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
	 * @see SystemMessageInterface::delete()
	 * @return bool
	 */
	public function delete()
	{
		if ($this->system_message)
		{
			return $this->system_message->delete();
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * @see SystemMessageInterface::get_user_id()
	 * @return integer
	 */
	public function get_user_id()
	{
		if ($this->id and $this->system_message) {
			return $this->system_message->get_user_id();
		}else{
			return null;
		}
	}
	
	/**
	 * @see SystemMessageInterface::get_datetime()
	 * @return string
	 */
	public function get_datetime()
	{
		if ($this->id and $this->system_message) {
			return $this->system_message->get_datetime();
		}else{
			return null;
		}
	}
	
	/**
	 * @see SystemMessageInterface::get_content()
	 * @return string
	 */
	public function get_content()
	{
		if ($this->id and $this->system_message) {
			return $this->system_message->get_content();
		}else{
			return null;
		}
	}
	
	/**
	 * @see SystemMessageInterface::set_content()
	 * @param string $content
	 * @return bool
	 */
	public function set_content($content)
	{
		if ($this->id and $this->system_message) {
			return $this->system_message->set_content($content);
		}else{
			return false;
		}
	}
	
	
	/**
	 * @see SystemMessageInterface::exist_entry()
	 * @param integer $id
	 * @return bool
	 */
	public static function exist_entry($id)
	{
		return SystemMessage_Access::exist_entry($id);
	}
	
	/**
	 * @see SystemMessageInterface::list_entries()
	 * @return array
	 */
	public static function list_entries()
	{
		return SystemMessage_Access::list_entries();
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
    		if (SystemMessage_Access::delete_by_user_id($event_object->get_user_id()) == false)
    		{
    			return false;
    		}
    	}
    	
    	return true;
    }
}
?>