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
 * System Message Access Class
 * @package base
 */
class SystemMessage_Access
{
	const SYSTEM_MESSAGE_PK_SEQUENCE = 'core_system_messages_id_seq';
	
	private $id;
	
	private $user_id;
	private $datetime;
	private $content;

	/**
	 * @param integer $id
	 */
	function __construct($id)
	{
		global $db;
		
		if ($id == null)
		{
			$this->id = null;
		}
		else
		{
			$sql = "SELECT * FROM ".constant("SYSTEM_MESSAGE_TABLE")." WHERE id = :id";
			$res = $db->prepare($sql);
			$db->bind_value($res, ":id", $id, PDO::PARAM_INT);
			$db->execute($res);
			$data = $db->fetch($res);
			
			if ($data['id'])
			{
				$this->id			= $data['id'];
				
				$this->user_id		= $data['user_id'];
				$this->datetime		= $data['datetime'];
				$this->content		= $data['content'];
			}
			else
			{
				$this->id 			= null;
			}
		}
	}
	
	function __destruct()
	{
		if ($this->id)
		{
			unset($this->id);
					
			unset($this->user_id);
			unset($this->datetime);
			unset($this->content);
		}
	}
	
	/**
	 * @param integer $user_id
	 * @param string $content
	 * @return integer
	 */
	public function create($user_id, $content)
	{
		global $db;
		
		if (is_numeric($user_id) and $content)
		{		
			$sql_write = "INSERT INTO ".constant("SYSTEM_MESSAGE_TABLE")." (id,user_id,datetime,content) " .
							"VALUES (nextval('".self::SYSTEM_MESSAGE_PK_SEQUENCE."'::regclass), :user_id, :datetime, :content)";
			
			$res_write = $db->prepare($sql_write);
			$db->bind_value($res_write, ":user_id", $user_id, PDO::PARAM_INT);
			$db->bind_value($res_write, ":datetime", date("Y-m-d H:i:s"), PDO::PARAM_STR);
			$db->bind_value($res_write, ":content", $content, PDO::PARAM_STR);
			$db->execute($res_write);
			
			if ($db->row_count($res_write) == 1)
			{
				$sql_read = "SELECT id FROM ".constant("SYSTEM_MESSAGE_TABLE")." WHERE id = currval('".self::SYSTEM_MESSAGE_PK_SEQUENCE."'::regclass)";
				$res_read = $db->prepare($sql_read);
				$db->execute($res_read);
				$data_read = $db->fetch($res_read);
				
				self::__construct($data_read['id']);
								
				return $data_read['id'];
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
    	global $db;
    	
    	if ($this->id)
    	{
    		$id_tmp = $this->id;
    		$this->__destruct();

    		$sql = "DELETE FROM ".constant("SYSTEM_MESSAGE_TABLE")." WHERE id = :id";
    		$res = $db->prepare($sql);
			$db->bind_value($res, ":id", $id_tmp, PDO::PARAM_INT);
			$db->execute($res);
    		
    		if ($db->row_count($res) == 1)
    		{
    			return true;
    		}
    		else
    		{
    			return false;
    		}
    	}else{
    		return false;
    	}
    	
    }
	
	/**
	 * @return integer
	 */
	public function get_user_id()
	{
		if ($this->user_id)
		{
			return $this->user_id;
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
		if ($this->datetime)
		{
			return $this->datetime;
		}
		else
		{
			return null;
		}		
	}
	
	/**
	 * @return string
	 */
	public function get_content()
	{
		if ($this->content)
		{
			return $this->content;
		}
		else
		{
			return null;
		}		
	}
	
	/**
	 * @param integer $user_id
	 * @return bool
	 */
	public function set_user_id($user_id)
	{
		global $db;

		if ($this->id and is_numeric($user_id))
		{
			$sql = "UPDATE ".constant("SYSTEM_MESSAGE_TABLE")." SET user_id = :user_id WHERE id = :id";
			$res = $db->prepare($sql);
			$db->bind_value($res, ":id", $this->id, PDO::PARAM_INT);
			$db->bind_value($res, ":user_id", $user_id, PDO::PARAM_INT);
			$db->execute($res);
			
			if ($db->row_count($res))
			{
				$this->user_id = $user_id;
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
	 * @param string $datetime
	 * @return bool
	 */
	public function set_datetime($datetime)
	{
		global $db;

		if ($this->id and $datetime)
		{
			$sql = "UPDATE ".constant("SYSTEM_MESSAGE_TABLE")." SET datetime = ':datetime WHERE id = :id";
			$res = $db->prepare($sql);
			$db->bind_value($res, ":id", $this->id, PDO::PARAM_INT);
			$db->bind_value($res, ":datetime", $datetime, PDO::PARAM_STR);
			$db->execute($res);
			
			if ($db->row_count($res))
			{
				$this->datetime = $datetime;
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
	 * @param string $content
	 * @return bool
	 */
	public function set_content($content)
	{
		global $db;

		if ($this->id and $content)
		{
			$sql = "UPDATE ".constant("SYSTEM_MESSAGE_TABLE")." SET content = :content WHERE id = :id";
			$res = $db->prepare($sql);
			$db->bind_value($res, ":id", $this->id, PDO::PARAM_INT);
			$db->bind_value($res, ":content", $content, PDO::PARAM_STR);
			$db->execute($res);
			
			if ($db->row_count($res))
			{
				$this->content = $content;
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
	 * @param integer $id
	 * @return bool
	 */
	public static function exist_id($id)
	{
		global $db;
		
		if (is_numeric($id))
		{
			$sql = "SELECT id FROM ".constant("SYSTEM_MESSAGE_TABLE")." WHERE id = :id";
			$res = $db->prepare($sql);
			$db->bind_value($res, ":id", $id, PDO::PARAM_INT);
			$db->execute($res);
			$data = $db->fetch($res);
			
			if ($data['id'])
			{
				return true;
			}
			else
			{
				return false;
			}
		}
	}
	
	/**
	 * @return bool
	 */
	public static function exist_entry($id)
	{
		global $db;

		if (is_numeric($id))
		{
			$return_array = array();
			
			$sql = "SELECT id FROM ".constant("SYSTEM_MESSAGE_TABLE")." WHERE id = :id";
			$res = $db->prepare($sql);
			$db->bind_value($res, ":id", $id, PDO::PARAM_INT);
			$db->execute($res);
			$data = $db->fetch($res);

			if ($data['id'])
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
	 * @return array
	 */
	public static function list_entries()
	{
		global $db;

		$return_array = array();
		
		$sql = "SELECT id FROM ".constant("SYSTEM_MESSAGE_TABLE")." ORDER BY datetime DESC";
		$res = $db->prepare($sql);
		$db->execute($res);
		
		while ($data = $db->fetch($res))
		{
			array_push($return_array,$data['id']);
		}
		
		if (is_array($return_array))
		{
			return $return_array;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @param integer $user_id
	 * @return bool
	 */
	public static function delete_by_user_id($user_id)
	{
		global $db;

		if (is_numeric($user_id))
		{
			$sql = "DELETE FROM ".constant("SYSTEM_MESSAGE_TABLE")." WHERE user_id = :user_id";
			$res = $db->prepare($sql);
			$db->bind_value($res, ":user_id", $user_id, PDO::PARAM_INT);
			$db->execute($res);

			if ($res !== false)
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
}

?>
