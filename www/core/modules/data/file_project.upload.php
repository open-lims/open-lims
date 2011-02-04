<?php
/**
 * @package data
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
 * 
 */
 
 /**
  * This file uploads project data
  */

	// Disable PHP Timeout
	set_time_limit(0);
	/**
	 * @ignore
	 */
	define("UNIT_TEST", false);

	require_once("../../../config/main.php");
	require_once("../../db/db.php");
	
	require_once("../../include/base/transaction.class.php");
	
	require_once("../../include/base/events/event.class.php");
	require_once("../../include/base/system_handler.class.php");

	require_once("../../include/base/session.class.php");

	$GLOBALS[autoload_prefix] = "../../../";

	require_once("../../include/base/autoload.function.php");

	if ($_GET[username] and $_GET[session_id] and $_FILES)
	{
		global $db, $user, $session, $transaction;
	
		$db = new Database("postgresql");
		$db->db_connect($GLOBALS[server],$GLOBALS[port],$GLOBALS[dbuser],$GLOBALS[password],$GLOBALS[database]);
		
		$session = new Session($_GET[session_id]);
		$user = new User($session->get_user_id());
		$transaction = new Transaction();
		
		if ($session->is_valid() == true)
		{ 
			$project_id = $_GET[project_id];
			$project = new Project($project_id);
			
			$project_item = new ProjectItem($project_id);
			$project_item->set_gid($_GET[key]);
			$project_item->set_status_id($project->get_current_status_id());
			
			$description_required = $project_item->is_description();
			$keywords_required = $project_item->is_keywords();
			
			if ($_GET[run] == "add_project_supplementary_file")
			{
				$folder_id = ProjectFolder::get_supplementary_folder($project_id);
				$folder = Folder::get_instance($folder_id);	
			}
			else
			{
				$folder_id = ProjectStatusFolder::get_folder_by_project_id_and_project_status_id($project_id,$project->get_current_status_id());
				
				$sub_folder_id = $project->get_sub_folder($_GET[key], $project->get_current_status_id());
				
				if (is_numeric($sub_folder_id))
				{
					$folder_id = $sub_folder_id;
				}
				
				$folder = Folder::get_instance($folder_id);	
			}
			
			if ($_POST[file_amount] > 25 or $_POST[file_amount] < 1 or !$_POST[file_amount])
			{				
				$file_amount = 1;		
			}
			else
			{	
				$file_amount = $_POST[file_amount];		
			}	
	
			$file = new File(null);
			$file_upload_successful = $file->upload_file_stack($file_amount, $folder_id, $_FILES, $_GET[unique_id]);
	
			if ($file_upload_successful == true)
			{
				$item_id_array = $file->get_item_id_array();
				
				if(is_array($item_id_array) and count($item_id_array) >= 1)
				{
					foreach($item_id_array as $key => $value)
					{
						$item_id = $value;
						
						$project_item->set_item_id($item_id);
						$project_item->link_item();
						$project_item->set_item_status();
					
						if (($class_name = $project_item->is_classified()) == true)
						{
							$project_item->set_class($class_name);
						}
						
						if ($description_required == true xor $keywords_required == true)
						{
							if ($description_required == false and $keywords_required == true)
							{
								$project_item->set_information(null,$_POST[keywords]);
							}
							else
							{
								$project_item->set_information($_POST[description],null);
							}
						}
						else
						{
							if ($description_required == true and $keywords_required == true)
							{
								$project_item->set_information($_POST[description],$_POST[keywords]);
							}
						}
						$project_item->create_log_entry();	
					}
				}
				ProjectTask::check_over_time_tasks($project_id);
			}
		}
	}

?>

