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
 * 
 */
require_once("../base/ajax_init.php");
require_once("../../include/base/session.class.php");
require_once("../../include/project/project.class.php");

/**
 * Project AJAX Class
 * @package project
 */
class ProjectAJAX extends AJAXInit
{
	function __construct()
	{
		parent::__construct();
	}
		
	private static function list_menu_childs_by_id()
	{
		if ($_GET[session_id])
		{
			$session = new Session($_GET[session_id]);

			if ($session->is_valid())
			{
				if ($_GET[mode] == "init")
				{
					if ($session->is_value("CURRENT_NAVIGATION_PROJECT"))
					{
						$project = new Project($_GET[project_id]);
						if ($_GET[project_id] != ($master_project_id = $project->get_master_project_id()))
						{
							$project = new Project($master_project_id);
							$project_id = $master_project_id;
						}
						else
						{
							$project_id = $_GET[project_id];
						}
						
						$session_array = $session->read_value("CURRENT_NAVIGATION_PROJECT");
						
						if (is_array($session_array) and count($session_array) >= 1)
						{
							if ($session_array[0][1] == $project_id)
							{
								return serialize($session_array);			
							}
							else
							{
								$return_array = array();
	
								// Mit Main-Project
								$project = new Project($_GET[project_id]);
								if ($_GET[project_id] != ($master_project_id = $project->get_master_project_id()))
								{
									$project = new Project($master_project_id);
									$project_id = $master_project_id;
								}
								else
								{
									$project_id = $_GET[project_id];
								}
										
								$return_array[0][0] = 0;
								$return_array[0][1] = $project_id;
								$return_array[0][2] = $project->get_name();
								$return_array[0][3] = "project.png";
								$return_array[0][4] = true; // Permission
								$return_array[0][5] = true;
								
								return serialize($return_array);
							}	
						}
						else
						{
							$return_array = array();
	
							// Mit Main-Project
							$project = new Project($_GET[project_id]);
							if ($_GET[project_id] != ($master_project_id = $project->get_master_project_id()))
							{
								$project = new Project($master_project_id);
								$project_id = $master_project_id;
							}
							else
							{
								$project_id = $_GET[project_id];
							}
									
							$return_array[0][0] = 0;
							$return_array[0][1] = $project_id;
							$return_array[0][2] = $project->get_name();
							$return_array[0][3] = "project.png";
							$return_array[0][4] = true; // Permission
							$return_array[0][5] = true;
							
							return serialize($return_array);	
						}
					}
					else
					{
						$return_array = array();
	
						// Mit Main-Project
						$project = new Project($_GET[project_id]);
						if ($_GET[project_id] != ($master_project_id = $project->get_master_project_id()))
						{
							$project = new Project($master_project_id);
							$project_id = $master_project_id;
						}
						else
						{
							$project_id = $_GET[project_id];
						}
								
						$return_array[0][0] = 0;
						$return_array[0][1] = $project_id;
						$return_array[0][2] = $project->get_name();
						$return_array[0][3] = "project.png";
						$return_array[0][4] = true; // Permission
						$return_array[0][5] = true;
						
						return serialize($return_array);
					}
				}
				else
				{
					if ($_GET[project_id])
					{
						$return_array = array();
		
						$project = new Project($_GET[project_id]);
						
						$project_array = $project->list_project_related_projects();
	
						if (is_array($project_array) and count($project_array ) >= 1)
						{
							$counter = 0;
							
							foreach($project_array as $key => $value)
							{
								$project = new Project($value);
									
								$return_array[$counter][0] = -1;
								$return_array[$counter][1] = $value;
								$return_array[$counter][2] = $project->get_name();
								$return_array[$counter][3] = "project.png";
								$return_array[$counter][4] = true; // Permission
								$return_array[$counter][5] = true;
								
								$counter++;
							}
						}
						return serialize($return_array);	
					}
				}
			}	
		}
	}
	
	private static function rewrite_menu_childs_array()
	{
		if ($_GET[session_id])
		{
			$session = new Session($_GET[session_id]);
		
			if ($_POST[serialized_folder_array])
			{
				$serialized_folder_array = $_POST[serialized_folder_array];
				
				$serialized_folder_array = stripslashes($serialized_folder_array);
				$folder_array = unserialize($serialized_folder_array);

				$session->write_value("CURRENT_NAVIGATION_PROJECT", $folder_array, true);
			}
		}
	}

	public function method_handler()
	{
		switch($_GET[run]):
			case "list_menu_project_childs":
				echo self::list_menu_childs_by_id();
			break;
			
			case "rewrite_menu_childs_array":
				echo self::rewrite_menu_childs_array();
			break;
		endswitch;
	}

}

$project_ajax = new ProjectAJAX;
$project_ajax->method_handler();

?>
