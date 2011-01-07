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
 * User Join Access Class
 * @package user
 */
class UserJoin_Access
{
	
	const USER_TABLE = 'core_users';
	const USER_PROFILE_TABLE = 'core_user_profiles';

	const GROUP_HAS_USER_TABLE = 'core_group_has_users';

	/**
	 * @param string $username
	 * @return array
	 */
	public static function search_users($username)
	{
   		global $db;
   		
   		/**
   		 * @param string $username
   		 * @return array
   		 */
   		if ($username)
   		{
   			$username = str_replace("*","%",$username);
   			
   			$return_array = array();
   				
   			$sql = "SELECT ".self::USER_TABLE.".id FROM ".self::USER_TABLE." " .
   					"JOIN ".self::USER_PROFILE_TABLE." ON ".self::USER_TABLE.".id = ".self::USER_PROFILE_TABLE.".id " .
   					"WHERE LOWER(username) LIKE '".$username."' OR " .
   							"LOWER(forename) LIKE '".$username."' OR " .
   							"LOWER(surname) LIKE '".$username."'";   			
   			$res = $db->db_query($sql);
			while ($data = $db->db_fetch_assoc($res))
			{
				array_push($return_array, $data[id]);
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
   		else
   		{
   			return null;
   		}
   	}
	
	/**
	 * @return integer
	 */
	public static function count_administrators()
	{
		global $db;
											
		$sql = "SELECT COUNT(".self::USER_TABLE.".id) AS result FROM ".self::USER_TABLE." " .
				"LEFT JOIN ".self::GROUP_HAS_USER_TABLE." ON ".self::USER_TABLE.".id = ".self::GROUP_HAS_USER_TABLE.".user_id " .
				"WHERE ".self::GROUP_HAS_USER_TABLE.".group_id = 1";
				
		$res = $db->db_query($sql);
		$data = $db->db_fetch_assoc($res);
		
		if ($data[result])
		{
			return $data[result];
		}
		else
		{
			return null;
		}
	}
	
}

?>