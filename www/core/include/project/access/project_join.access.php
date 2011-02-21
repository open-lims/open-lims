<?php
/**
 * @package project
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
 * Project Join Access Class
 * @package project
 */
class ProjectJoin_Access
{
	/**
	 * @param integer $user_id
	 * @param string $today
	 * @return array
	 */
	public static function list_not_finished_over_time_project_tasks_by_user_id($user_id, $today)
	{
		global $db;
		
		if (is_numeric($user_id) and $today)
		{	
			$sql = "SELECT ".constant("PROJECT_TASK_TABLE").".id FROM " .
					"".constant("PROJECT_TASK_TABLE")." " .
						"JOIN ".constant("PROJECT_TABLE")." 					ON ".constant("PROJECT_TABLE").".id 					= ".constant("PROJECT_TASK_TABLE").".project_id " .
						"LEFT JOIN ".constant("PROJECT_PERMISSION_TABLE")." 	ON ".constant("PROJECT_PERMISSION_TABLE").".project_id	= ".constant("PROJECT_TASK_TABLE").".project_id " .
							"WHERE ".constant("PROJECT_PERMISSION_TABLE").".user_id IS NOT NULL " .
								"AND ".constant("PROJECT_PERMISSION_TABLE").".user_id = ".$user_id." " .
								"AND ".constant("PROJECT_PERMISSION_TABLE").".permission > 1 " .
								"AND ".constant("PROJECT_TASK_TABLE").".end_date < '".$today."'";
			
			$return_array = array();
				
			$res = $db->db_query($sql);

			while ($data = $db->db_fetch_assoc($res))
			{
				array_push($return_array, $data[id]);
			}
			return $return_array;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @todo implementation
	 * @param integer $user_id
	 * @param string $today
	 * @return array
	 */
	public static function list_not_finished_today_project_tasks_by_user_id($user_id, $today)
	{
		
	}
	
	/**
	 * @todo implementation
	 * @param integer $user_id
	 * @param string $today
	 * @return array
	 */
	public static function list_not_finished_next_7_days_project_tasks_by_user_id($user_id, $today)
	{
		
	}
	
	/**
	 * @param integer $leader_id
	 * @param integer $organisation_unit_id
	 * @return bool
	 */
	public static function change_leader_permission_by_organisation_unit_id($leader_id, $organisation_unit_id)
	{
		global $db;
		
		if (is_numeric($leader_id) and is_numeric($organisation_unit_id))
		{	
			$sql = "UPDATE ".constant("PROJECT_PERMISSION_TABLE")." " .
					"SET user_id = ".$leader_id." " .
					"WHERE ".constant("PROJECT_PERMISSION_TABLE").".intention = 2 " .
						"AND ".constant("PROJECT_PERMISSION_TABLE").".project_id = " .
							"(SELECT ".constant("PROJECT_TABLE").".id FROM ".constant("PROJECT_TABLE")." WHERE toid_organ_unit = ".$organisation_unit_id.")";

			$res = $db->db_query($sql);
			
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
