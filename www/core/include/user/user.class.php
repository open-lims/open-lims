<?php
/**
 * @package user
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
require_once("interfaces/user.interface.php");

if (constant("UNIT_TEST") == false or !defined("UNIT_TEST"))
{
	require_once("exceptions/user_already_exist_exception.class.php");
	require_once("exceptions/user_creation_failed_exception.class.php");
	require_once("exceptions/user_not_found_exception.class.php");
	
	require_once("events/user_create_event.class.php");
	require_once("events/user_delete_event.class.php");
	require_once("events/user_delete_precheck_event.class.php");
	require_once("events/user_post_delete_event.class.php");
	require_once("events/user_rename_event.class.php");
	
	require_once("access/user.access.php");
	require_once("access/user_admin_setting.access.php");
	require_once("access/user_profile_setting.access.php");
	require_once("access/user_profile.access.php");
	
	require_once("access/user.wrapper.access.php");
}

/**
 * User Management Class
 * @package user
 */
class User implements UserInterface {
	
	private $user_id;
	
	private $user;
	private $user_admin_setting;
	private $user_profile_setting;
	private $user_profile;
	
	/**
	 * @param interger $user_id User-ID
	 */
	function __construct($user_id)
	{
		if ($user_id == null)
		{
			$this->user_id = null;
			$this->user = new User_Access(null);
			$this->user_admin_setting = null;
			$this->user_profile_setting = null;
			$this->user_profile = new UserProfile_Access(null);
		}
		else
		{
			$this->user_id = $user_id;
			$this->user = new User_Access($user_id);
			$this->user_admin_setting = new UserAdminSetting_Access($user_id);
			$this->user_profile_setting = new UserProfileSetting_Access($user_id);
			$this->user_profile = new UserProfile_Access($user_id);
		}
	}
	
	function __destruct()
	{
		unset($this->user_id);
		unset($this->user);
		unset($this->user_profile);
	}	
	
	/**
	 * Creates a new user including all dependencies
	 * @param string $username
	 * @param string $gener
	 * @param string $title
	 * @param string $forename
	 * @param string $surname
	 * @param string $mail
	 * @param bool $can_change_password
	 * @param bool $must_change_password
	 * @param bool $disabled
	 * @return string Generated User Password
	 * @throws UserCreationFailedException
	 * @throws UserAlreadyExistException
	 */
	public function create($username, $gender, $title, $forename, $surname, $mail, $can_change_password, $must_change_password, $disabled)
	{
		global $transaction;
							
		if ($this->user)
		{
			if ($username and $gender and $forename and $surname and $mail)
			{
				$transaction_id = $transaction->begin();

				if (self::exist_username($username) == true)
				{
					if ($transaction_id != null)
					{
						$transaction->rollback($transaction_id);
					}
					throw new UserAlreadyExistException("",2);
				}
						
				$password = self::generate_password();
				if (($user_id = $this->user->create($username, md5(sha1($password)))) != null)
				{
					$user_admin_setting = new UserAdminSetting_Access(null);
					if ($user_admin_setting->create($user_id) == false)
					{
						if ($transaction_id != null)
						{
							$transaction->rollback($transaction_id);
						}
						throw new UserCreationFailedException("",1);
					}
					
					$user_profile_setting = new UserProfileSetting_Access(null);
					if ($user_profile_setting->create($user_id) == false)
					{
						if ($transaction_id != null)
						{
							$transaction->rollback($transaction_id);
						}
						throw new UserCreationFailedException("",1);
					}
					
					if ($can_change_password == true)
					{
						$user_admin_setting->set_can_change_password(true);
					}
					else
					{
						$user_admin_setting->set_can_change_password(false);
					}
					
					if ($must_change_password == true)
					{
						$user_admin_setting->set_must_change_password(true);
					}
					else
					{
						$user_admin_setting->set_must_change_password(false);
					}
					
					if ($disabled == true)
					{
						$user_admin_setting->set_user_locked(true);
					}
					else
					{
						$user_admin_setting->set_user_locked(false);
					}
										
					$user_profile_setting->set_timezone_id(constant("TIMEZONE_ID"));
					
					if ($this->user_profile->create($user_id, $gender, $title, $forename, $surname, $mail) == null)
					{
						if ($transaction_id != null)
						{
							$transaction->rollback($transaction_id);
						}
						throw new UserCreationFailedException("",1);
					}
					
					$this->__construct($user_id);
					
					$group = new Group(10);
					if ($group->create_user_in_group($user_id) == false)
					{
						if ($transaction_id != null)
						{
							$transaction->rollback($transaction_id);
						}
						throw new UserCreationFailedException("",1);
					}
					
					$user_create_event = new UserCreateEvent($user_id);
					$event_handler = new EventHandler($user_create_event);
					
					if ($event_handler->get_success() == false)
					{
						if ($transaction_id != null)
						{
							$transaction->rollback($transaction_id);
						}
						throw new UserCreationFailedException("",1);
					}
					else
					{
						$transaction->commit($transaction_id);
					}
					return $password;
						
				}
				else
				{
					if ($transaction_id != null)
					{
						$transaction->rollback($transaction_id);
					}
					throw new UserCreationFailedException("",1);
				}
			
			}
			else
			{
				throw new UserCreationFailedException("",1);
			}
				
		}
		else
		{
			throw new UserCreationFailedException("",1);
		}
	
	}
	
	/**
	 * Deletes an user
	 * @todo Delete System-Messages
	 * @todo Set File-Version creator_id on null
	 * @todo Set Value-Version creator_id on null
	 * @return bool
	 */
	public function delete()
	{
		global $transaction;
		
		if ($this->user)
		{
			if ($this->check_delete_dependencies() == true)
			{
				$transaction_id = $transaction->begin();
				
				// Sessions
				if (Session::delete_user_sessions($this->user_id) == false)
				{
					if ($transaction_id != null)
					{
						$transaction->rollback($transaction_id);
					}
					return false;
				}
				
				// Profile and Settings
				$user_profile = new UserProfile_Access($this->user_id);
				if ($user_profile->delete() == false)
				{
					if ($transaction_id != null)
					{
						$transaction->rollback($transaction_id);
					}
					return false;
				}
				
				$user_profile_setting = new UserProfileSetting_Access($this->user_id);
				if ($user_profile_setting->delete() == false)
				{
					if ($transaction_id != null)
					{
						$transaction->rollback($transaction_id);
					}
					return false;
				}
				
				$user_admin_setting = new UserAdminSetting_Access($this->user_id);
				if ($user_admin_setting->delete() == false)
				{
					if ($transaction_id != null)
					{
						$transaction->rollback($transaction_id);
					}
					return false;
				}
				
				// Groups
				if (GroupHasUser_Access::delete_by_user_id($this->user_id) == false)
				{
					if ($transaction_id != null)
					{
						$transaction->rollback($transaction_id);
					}
					return false;
				}

				// System-Logs
				if (SystemLog::set_user_id_on_null($this->user_id) == false)
				{
					if ($transaction_id != null)
					{
						$transaction->rollback($transaction_id);
					}
					return false;
				}
				
				$user_delete_event = new UserDeleteEvent($this->user_id);
				$event_handler = new EventHandler($user_delete_event);
				
				if ($event_handler->get_success() == false)
				{
					if ($transaction_id != null)
					{
						$transaction->rollback($transaction_id);
					}
					return false;
				}

				if ($this->user->delete() == false)
				{
					if ($transaction_id != null)
					{
						$transaction->rollback($transaction_id);
					}
					return false;
				}

				$user_post_delete_event = new UserPostDeleteEvent($this->user_id);
				$event_handler = new EventHandler($user_post_delete_event);
				
				if ($event_handler->get_success() == false)
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
		else
		{
			return false;
		}
		
	}
	
	/**
	 * Checks dependencies before user deletion.
	 * @return bool
	 */
	public function check_delete_dependencies()
	{
		if ($this->user_id)
		{
			if ($this->user_id == 1)
			{
				return false;
			}
			
			$user_delete_precheck_event = new UserDeletePrecheckEvent($this->user_id);
			$event_handler = new EventHandler($user_delete_precheck_event);
			
			if ($event_handler->get_success() == false)
			{
				return false;
			}
			else
			{
				return true;
			}
		}
		else
		{
			return false;
		}
		
	}
	
	/**
	 * Matches a given password with the database-saved password
	 * @param string $password
	 * @return bool
	 */
	public function check_password($password)
	{
		$user_password = $this->user->get_password();
    	$current_password = md5(sha1($password));
		
		if ($user_password == $current_password)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * Matches a gove mail-address with the database-saved password
	 * @param string $mail
	 * @return bool
	 */
	public function check_mail($mail)
	{
		$user_mail = $this->get_profile("mail");
	
		if (trim($user_mail) == trim($mail))
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * Checks the administration-permission of an user
	 * @return bool
	 */
	public function is_admin()
	{
		if ($this->user_id)
		{
			$group = new Group(1);
			
			if ($group->is_user_in_group($this->user_id) == true)
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
	 * @return interger User-ID
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
	 * @return string Username
	 */
	public function get_username()
	{
		if ($this->user)
		{
			return $this->user->get_username();
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @param bool $short_version Function will return "J. Smith" instead of "Joe Smith", if it is true
	 * @return string Full User-Name (Title, Forname and Surname)
	 */
	public function get_full_name($short_version)
	{	
		if ($this->user_id and $this->user_id != 1 and $this->user_profile)
		{
			$title = $this->user_profile->get_title();
			$forename = $this->user_profile->get_forename();
			$surname = $this->user_profile->get_surname();
						
			if (!$title)
			{
				if ($short_version == true)
				{
					return substr($forename,0,1).".&nbsp;".$surname;
				}
				else
				{
					return $forename."&nbsp;".$surname;
				}	
			}
			else
			{
				if ($short_version == true)
				{
					return $title."&nbsp;".substr($forename,0,1).".&nbsp;".$surname;
				}
				else
				{
					return $title."&nbsp;".$forename." ".$surname;
				}	
			}	
		}
		elseif($this->user_id == 1)
		{
			return "Administrator";
		}
		else
		{
			return "System";
		}
		
	}
	
	/**
	 * @return string Hashed Password String
	 */
	public function get_password()
	{
		if ($this->user)
		{
			return $this->user->get_password();
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return string Date of last password change
	 */
	public function get_last_password_change()
	{
		if ($this->user_admin_setting)
		{
			return $this->user_admin_setting->get_last_password_change();
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @param string $entry Name of required entry
	 * @return bool
	 */
	public function get_boolean_user_entry($entry)
	{
		if ($this->user_admin_setting)
		{
			switch($entry):
			
				case("can_change_password"):
					return $this->user_admin_setting->get_can_change_password();
				break;
				
				case("must_change_password"):
					return $this->user_admin_setting->get_must_change_password();
				break;
				
				case("user_locked"):
					return $this->user_admin_setting->get_user_locked();
				break;
				
				case("user_inactive"):
					return $this->user_admin_setting->get_user_inactive();
				break;
				
				case("secure_password"):
					return $this->user_admin_setting->get_secure_password();
				break;
				
				case("block_write"):
					return $this->user_admin_setting->get_block_write();
				break;
				
				case("create_folder"):
					return $this->user_admin_setting->get_create_folder();
				break;
				
				default:
					return null;
				break;
			
			endswitch;
		}
		else
		{
			return null;
		}
		
	}
	
	/**
	 * @param string $entry Name of required entry
	 * @return mixed
	 */
	public function get_profile($entry)
	{
		if ($this->user_profile)
		{
			switch($entry):
			
			case("gender"):
				return $this->user_profile->get_gender();
			break;
			
			case("title"):
				return $this->user_profile->get_title();
			break;
			
			case("forename"):
				return $this->user_profile->get_forename();
			break;
			
			case("surname"):
				return $this->user_profile->get_surname();
			break;
			
			case("mail"):
				return $this->user_profile->get_mail();
			break;
					
			case("institution"):
				return $this->user_profile->get_institution();
			break;		
			
			case("department"):
				return $this->user_profile->get_department();
			break;
			
			case("street"):
				return $this->user_profile->get_street();
			break;
			
			case("zip"):
				return $this->user_profile->get_zip();
			break;
			
			case("city"):
				return $this->user_profile->get_city();
			break;
			
			case("country"):
				return $this->user_profile->get_country();
			break;
			
			case("phone"):
				return $this->user_profile->get_phone();
			break;
			
			case("icq"):
				return $this->user_profile->get_icq();
			break;
			
			case("msn"):
				return $this->user_profile->get_msn();
			break;
			
			case("yahoo"):
				return $this->user_profile->get_yahoo();
			break;
			
			case("aim"):
				return $this->user_profile->get_aim();
			break;
			
			case("skype"):
				return $this->user_profile->get_skype();
			break;
					
			default:
				return null;
			break;
			
			endswitch;	
		}
		else
		{
			return null;
		}
		
	}
	
	/**
	 * @return integer Language-ID
	 */
	public function get_language_id()
	{
		if ($this->user_profile_setting)
		{
			return $this->user_profile_setting->get_language_id();
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return integer Timezone-ID
	 */
	public function get_timezone_id()
	{
		if ($this->user_profile_setting)
		{
			return $this->user_profile_setting->get_timezone_id();
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @param string $username New User-Name
	 * @return bool
	 * @todo remove dependency with folder
	 */
	public function set_username($username)
	{
		global $transaction;
		
		if ($this->user and $this->user_id and $username)
		{
			$transaction_id = $transaction->begin();
			
			if ($this->user->set_username($username) == true)
			{
				$user_rename_event = new UserRenameEvent($this->user_id);
				$event_handler = new EventHandler($user_rename_event);
				
				if ($event_handler->get_success() == true)
				{
					if ($transaction_id != null)
					{
						$transaction->commit($transaction_id);
					}
					return true;
				}
				else
				{
					if ($transaction_id != null)
					{
						$transaction->rollback($transaction_id);
					}
					return false;
				}
			}
			else
			{
				if ($transaction_id != null)
				{
				$transaction->rollback($transaction_id);
				}
				return false;
			}
		}
		else
		{
			return false;
		}
		
	}
	
	/**
	 * @param string $password New Password
	 * @return bool
	 */
	public function set_password($password)
	{
		if ($this->user and $password)
		{
			return $this->user->set_password(md5(sha1($password)));
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * @param string $password New Password
	 * @return bool
	 */
	public function set_password_on_login($password)
	{
		global $session, $transaction;
		
		if ($this->user and $password)
		{
			$transaction_id = $transaction->begin();
			
			if ($this->user->set_password(md5(sha1($password))) == true) 
			{
				if ($this->user_admin_setting->set_must_change_password(false) == true)
				{
					if ($session->write_value("must_change_password",false, true) == false)
					{
						if ($transaction_id != null)
						{
							$transaction->rollback($transaction_id);
						}
						return false;
					}
					
					
					if ($transaction_id != null)
					{
						$transaction->commit($transaction_id);
					}
					return true;
				}
				else
				{
					if ($transaction_id != null)
					{
						$transaction->rollback($transaction_id);
					}
					return false;
				}
			}
			else
			{
				if ($transaction_id != null)
				{
					$transaction->rollback($transaction_id);
				}
				return false;
			}
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * @param string $last_password_change New date of lase password change
	 * @return bool
	 */
	public function set_last_password_change($last_password_change)
	{
		if ($this->user_admin_setting and $last_password_change)
		{
			return $this->user_admin_setting->set_last_password_change($last_password_change);
		}
		else
		{
			return false;
		}
	}
	
	
	/**
	 * @param string $entry Name of the entry
	 * @param bool $value
	 * @return bool
	 */
	public function set_boolean_user_entry($entry, $value)
	{
		if ($this->user_admin_setting)
		{
			switch($entry):
			
				case("can_change_password"):
					return $this->user_admin_setting->set_can_change_password($value);
				break;
				
				case("must_change_password"):
					return $this->user_admin_setting->set_must_change_password($value);
				break;
				
				case("user_locked"):
					return $this->user_admin_setting->set_user_locked($value);
				break;
				
				case("user_inactive"):
					return $this->user_admin_setting->set_user_inactive($value);
				break;
				
				case("secure_password"):
					return $this->user_admin_setting->set_secure_password($value);
				break;
				
				case("block_write"):
					return $this->user_admin_setting->set_block_write($value);
				break;
				
				case("create_folder"):
					return $this->user_admin_setting->set_create_folder($value);
				break;
				
				default:
					return null;
				break;
			
			endswitch;
		}
		else
		{
			return null;
		}
		
	}
	
	/**
	 * @param string $entry Name of the entry
	 * @param string $value
	 * @return bool
	 */
	public function set_profile($entry, $value)
	{
		if ($this->user_profile and isset($value))
		{
			switch($entry):
			
			case("gender"):
				return $this->user_profile->set_gender($value);
			break;
			
			case("title"):
				return $this->user_profile->set_title($value);
			break;
			
			case("forename"):
				return $this->user_profile->set_forename($value);
			break;
			
			case("surname"):
				return $this->user_profile->set_surname($value);
			break;
			
			case("mail"):
				return $this->user_profile->set_mail($value);
			break;
					
			case("institution"):
				return $this->user_profile->set_institution($value);
			break;		
			
			case("department"):
				return $this->user_profile->set_department($value);
			break;
			
			case("street"):
				return $this->user_profile->set_street($value);
			break;
			
			case("zip"):
				return $this->user_profile->set_zip($value);
			break;
			
			case("city"):
				return $this->user_profile->set_city($value);
			break;
			
			case("country"):
				return $this->user_profile->set_country($value);
			break;
			
			case("phone"):
				return $this->user_profile->set_phone($value);
			break;
			
			case("icq"):
				return $this->user_profile->set_icq($value);
			break;
			
			case("msn"):
				return $this->user_profile->set_msn($value);
			break;
			
			case("yahoo"):
				return $this->user_profile->set_yahoo($value);
			break;
			
			case("aim"):
				return $this->user_profile->set_aim($value);
			break;
			
			case("skype"):
				return $this->user_profile->set_skype($value);
			break;
					
			default:
				return null;
			break;
			
			endswitch;
		}
		else
		{
			return null;
		}
		
	}

	/**
	 * @param integer $language_id Language-ID
	 * @return bool
	 */
	public function set_language_id($language_id)
	{
		if ($this->user_profile_setting and $language_id)
		{
			return $this->user_profile_setting->set_language_id($language_id);
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * @param integer $timezone_id Timezone-ID
	 * @return bool
	 */
	public function set_timezone_id($timezone_id)
	{
		if ($this->user_profile_setting and $timezone_id)
		{
			return $this->user_profile_setting->set_timezone_id($timezone_id);
		}
		else
		{
			return false;
		}
	}


	/**
	 * Generates a random password
	 * @return string new password
	 */
	public static function generate_password()
	{
		$new_password = "";

		srand(mktime());
		for ($i=0;$i<=7;$i++)
		{
			$lu = rand(1,2);
			
			if ($lu % 2)
			{
				$id = rand();
				$id = ($id % 26)+97;
			}
			else
			{
				$id = rand();
				$id = ($id % 26)+65;
			}

			$new_password = $new_password."".chr($id);
		}
		return $new_password;
	}

	/**
	 * @param string $username
	 * @return integer User-ID
	 */
	public static function get_user_id_by_username($username)
	{
		$username = trim(strtolower($username));
		return User_Access::get_user_id_by_username($username);
	}
	
	/**
	 * @return integer Number of inactive Users
	 */
	public static function get_number_of_inactive_users()
	{
		return UserAdminSetting_Access::get_number_of_inactive_users();
	}
	
	/**
	 * @return integer Number of locked Users
	 */
	public static function get_number_of_locked_users()
	{
		return UserAdminSetting_Access::get_number_of_locked_users();
	}

	/**
	 * Checks if an user exists
	 * @param string $username
	 * @return bool
	 */
	public static function exist_username($username)
	{
		$username = trim(strtolower($username));
		if (User_Access::get_user_id_by_username($username) != null)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * Checks if an user exists
	 * @param integer $user_id
	 * @return bool
	 */
	public static function exist_user($user_id)
	{
		return User_Access::exist_user($user_id);
	}

	/**
	 * @return array Array of all User-IDs
	 */
	public static function list_entries()
	{
		return User_Access::list_entries();
	}
   	
   	/**
   	 * @return integer
   	 */
   	public static function count_users()
   	{
   		return User_Access::count_users();
   	}
   	
   	/**
   	 * @return integer
   	 */
   	public static function count_administrators()
   	{
   		return User_Wrapper_Access::count_administrators();
   	}

}

?>
