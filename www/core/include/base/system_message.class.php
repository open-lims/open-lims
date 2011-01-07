<?php
/**
 * @package base
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
class SystemMessage implements SystemMessageInterface
{
	public $id;
	public $system_message;

	/**
	 * @param integer $id
	 */
	function __construct($id)
	{
		if ($id == null)
		{
			$this->id = null;
			$this->system_message = new SystemMessage_Access(null);
		}
		else
		{
			$this->id = $id;
			$this->system_message = new SystemMessage_Access($id);
		}
	}
	
	function __destruct()
	{
		unset($this->id);
		unset($this->system_message);
	}
	
	/**
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
				$this->__construct($id);
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
	 * @param integer $id
	 * @return bool
	 */
	public static function exist_entry($id)
	{
		return SystemMessage_Access::exist_entry($id);
	}
	
	/**
	 * @return array
	 */
	public static function list_entries()
	{
		return SystemMessage_Access::list_entries();
	}

}
?>