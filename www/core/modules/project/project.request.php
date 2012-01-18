<?php
/**
 * @package project
 * @version 0.4.0.0
 * @author Roman Konertz <konertz@open-lims.org>
 * @copyright (c) 2008-2011 by Roman Konertz
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
 * Project Request Class
 * @package project
 */
class ProjectRequest
{	
	public static function ajax_handler()
	{
		global $project_security;
	
		if ($_GET[project_id])
		{
			$project_security = new ProjectSecurity($_GET[project_id]);
					
			if ($_GET[run] != "new_subproject")
			{
 				require_once("io/project_common.io.php");
 				ProjectCommon_IO::tab_header();
			}
		}
		else
		{
			$project_security = new ProjectSecurity(null);
		}
		
		switch($_GET[run]):
			
			// Project
			
			case "list_user_related_projects":
				require_once("ajax/project.ajax.php");
				echo ProjectAjax::list_user_related_projects($_POST[column_array], $_POST[argument_array], $_POST[css_page_id],  $_POST[css_row_sort_id], $_POST[entries_per_page], $_GET[page], $_GET[sortvalue], $_GET[sortmethod]);
			break;
			
			case "count_user_related_projects":
				require_once("ajax/project.ajax.php");
				echo ProjectAjax::count_user_related_projects($_POST[argument_array]);
			break;
		
			case "list_organisation_unit_related_projects":
				require_once("ajax/project.ajax.php");
				echo ProjectAjax::list_organisation_unit_related_projects($_POST[column_array], $_POST[argument_array], $_POST[css_page_id],  $_POST[css_row_sort_id], $_POST[entries_per_page], $_GET[page], $_GET[sortvalue], $_GET[sortmethod]);
			break;
			
			case "count_organisation_unit_related_projects":
				require_once("ajax/project.ajax.php");
				echo ProjectAjax::count_organisation_unit_related_projects($_POST[argument_array]);
			break;
			
			case "list_projects_by_item_id":
				require_once("ajax/project.ajax.php");
				echo ProjectAjax::list_projects_by_item_id($_POST[column_array], $_POST[argument_array], $_POST[css_page_id],  $_POST[css_row_sort_id], $_POST[entries_per_page], $_GET[page], $_GET[sortvalue], $_GET[sortmethod]);
			break;
			
			case "count_projects_by_item_id":
				require_once("ajax/project.ajax.php");
				echo ProjectAjax::count_projects_by_item_id($_POST[argument_array]);
			break;

			case "get_project_status_bar":
				require_once("ajax/project.ajax.php");
				echo ProjectAjax::get_project_status_bar($_POST[get_array]);
			break;
			
			case "get_project_menu":
				require_once("ajax/project.ajax.php");
				echo ProjectAjax::get_project_menu($_POST[get_array]);
			break;
			
			case "get_project_proceed":
				require_once("ajax/project.ajax.php");
				echo ProjectAjax::get_project_proceed($_POST[get_array]);
			break;
			
			case "proceed_project":
				require_once("ajax/project.ajax.php");
				echo ProjectAjax::proceed_project($_POST[get_array]);
			break;
			
			
			// Log
			
			case "log_get_list":
				require_once("ajax/project_log.ajax.php");
				echo ProjectLogAjax::get_list($_POST[get_array], $_POST[page]);
			break;
			
			case "log_create":
				require_once("ajax/project_log.ajax.php");
				echo ProjectLogAjax::create($_POST[get_array]);
			break;
			
			case "log_create_handler":
				require_once("ajax/project_log.ajax.php");
				echo ProjectLogAjax::create_handler($_POST[get_array], $_POST[comment], $_POST[important]);
			break;
			
			case "log_get_more":
				require_once("ajax/project_log.ajax.php");
				echo ProjectLogAjax::get_more($_POST[id]);
			break;
			
			case "log_get_less":
				require_once("ajax/project_log.ajax.php");
				echo ProjectLogAjax::get_less($_POST[id]);
			break;
			
			
			// Admin
			
			case "get_project_admin_menu":
				require_once("ajax/project_admin.ajax.php");
				echo ProjectAdminAjax::get_project_admin_menu($_POST[get_array]);
			break;
			
			case "delete":
				require_once("ajax/project_admin.ajax.php");
				echo ProjectAdminAjax::delete($_POST[get_array]);
			break;
			
			case "delete_handler":
				require_once("ajax/project_admin.ajax.php");
				echo ProjectAdminAjax::delete_handler($_POST[get_array]);
			break;
			
			case "restore":
				require_once("ajax/project_admin.ajax.php");
				echo ProjectAdminAjax::restore($_POST[get_array]);
			break;
			
			case "restore_handler":
				require_once("ajax/project_admin.ajax.php");
				echo ProjectAdminAjax::restore_handler($_POST[get_array]);
			break;
			
			case "cancel":
				require_once("ajax/project_admin.ajax.php");
				echo ProjectAdminAjax::cancel($_POST[get_array]);
			break;
			
			case "cancel_handler":
				require_once("ajax/project_admin.ajax.php");
				echo ProjectAdminAjax::cancel_handler($_POST[get_array], $_POST[comment]);
			break;
			
			
			// Tasks
			
			case "list_project_tasks":
				require_once("ajax/project_task.ajax.php");
				echo ProjectTaskAjax::list_project_tasks($_POST[column_array], $_POST[argument_array], $_POST[css_page_id],  $_POST[css_row_sort_id], $_POST[entries_per_page], $_GET[page], $_GET[sortvalue], $_GET[sortmethod]);
			break;
			
			case "count_project_tasks":
				require_once("ajax/project_task.ajax.php");
				echo ProjectTaskAjax::count_project_tasks($_POST[argument_array]);
			break;
			
			case "list_project_permissions":
				require_once("ajax/project_admin.ajax.php");
				echo ProjectAdminAjax::list_project_permissions($_POST[column_array], $_POST[argument_array], $_POST[css_page_id],  $_POST[css_row_sort_id], $_POST[entries_per_page], $_GET[page], $_GET[sortvalue], $_GET[sortmethod]);
			break;
			
			case "count_project_permissions":
				require_once("ajax/project_admin.ajax.php");
				echo ProjectAdminAjax::count_project_permissions($_POST[argument_array]);
			break;
			
		endswitch;
	}
	
	/**
	 * @throws ProjectSecurityAccessDeniedException
	 */
	public static function io_handler()
	{
		global $project_security, $session, $transaction;

		if ($_GET[project_id])
		{
			$project_security = new ProjectSecurity($_GET[project_id]);
					
			if ($_GET[run] != "new_subproject")
			{
 				require_once("io/project_common.io.php");
 				ProjectCommon_IO::tab_header();
			}
		}
		else
		{
			$project_security = new ProjectSecurity(null);
		}
			
		switch($_GET[run]):
		
			case ("new"):
				require_once("io/project.io.php");
				ProjectIO::create();
			break;
			
			case ("myprojects"):
			case ("workon"):
			case ("accessdata"):
			case ("analyse"):
				require_once("io/project.io.php");
				ProjectIO::list_user_related_projects(null);
			break;
			
			case("userprojects"):
				require_once("io/project.io.php");
				ProjectIO::list_user_related_projects($_GET[id]);
			break;		

			case("organ_unit"):
				require_once("io/project.io.php");
				ProjectIO::list_organisation_unit_related_projects();
			break;

			case ("new_subproject"):
				require_once("io/project.io.php");
				ProjectIO::create();
			break;
			
			case ("detail"):
				require_once("io/project.io.php");
				ProjectIO::detail();
			break;

			case("structure"):
				require_once("io/project.io.php");
				ProjectIO::structure();
			break;
			
			// Project Log
			
			case("log"):
				require_once("io/project_log.io.php");
				ProjectLogIO::list_project_related_logs();
			break;
						
			// Tasks and Schedule
			
			case ("add_task"):
				require_once("io/project_task.io.php");
				ProjectTaskIO::add();
			break;
			
			case ("schedule"):
			case ("show_tasks"):
				require_once("io/project_task.io.php");
				ProjectTaskIO::show();
			break;
			
			case ("task_detail"):
				require_once("io/project_task.io.php");
				ProjectTaskIO::detail();
			break;
			
			case ("task_delete"):
				require_once("io/project_task.io.php");
				ProjectTaskIO::delete();
			break;
			
			case ("task_edit_start"):
				require_once("io/project_task.io.php");
				ProjectTaskIO::edit_start();
			break;
			
			case ("task_edit_end"):
				require_once("io/project_task.io.php");
				ProjectTaskIO::edit_end();
			break;
			
						
			// Administration
			
			case("admin"):
				require_once("io/project_admin.io.php");
				ProjectAdminIO::menu();
			break;
			
			case("admin_rename"):
				require_once("io/project_admin.io.php");
				ProjectAdminIO::rename();
			break;
			
			case("admin_chown"):
				require_once("io/project_admin.io.php");
				ProjectAdminIO::chown();
			break;
			
			case("admin_move"):
				require_once("io/project_admin.io.php");
				ProjectAdminIO::move();
			break;
			
			case("admin_quota"):
				require_once("io/project_admin.io.php");
				ProjectAdminIO::quota();
			break;
			
			// Administration - Permission
			
			case("admin_permission"):
				require_once("io/project_admin.io.php");
				$project_admin_io = new ProjectAdminIO();
				ProjectAdminIO::permission();
			break;
			
			case("admin_permission_add_user"):
				require_once("io/project_admin.io.php");
				ProjectAdminIO::permission_add_user();
			break;
			
			case("admin_permission_add_group"):
				require_once("io/project_admin.io.php");
				ProjectAdminIO::permission_add_group();
			break;
			
			case("admin_permission_add_ou"):
				require_once("io/project_admin.io.php");
				ProjectAdminIO::permission_add_organisation_unit();
			break;
			
			case("admin_permission_edit"):
				require_once("io/project_admin.io.php");
				ProjectAdminIO::permission_edit();
			break;
			
			case("admin_permission_delete"):
				require_once("io/project_admin.io.php");
				ProjectAdminIO::permission_delete();
			break;
							
			// Item Lister
			/**
			 * @todo errors, exceptions
			 */
			case("item_list"):
				if ($project_security->is_access(1, false) == true)
				{
					if ($_GET[dialog])
					{
						if ($_GET[dialog] == "data")
						{
							$path_stack_array = array();
							
							$folder_id = ProjectFolder::get_folder_by_project_id($_GET[project_id]);
					    	$folder = Folder::get_instance($folder_id);
					    	$init_array = $folder->get_object_id_path();
					    	
					    	foreach($init_array as $key => $value)
					    	{
					    		$temp_array = array();
					    		$temp_array[virtual] = false;
					    		$temp_array[id] = $value;
					    		array_unshift($path_stack_array, $temp_array);
					    	}
							
					    	if (!$_GET[folder_id])
					    	{
								$session->write_value("stack_array", $path_stack_array, true);
					    	}
						}
						
						$module_dialog = ModuleDialog::get_by_type_and_internal_name("item_list", $_GET[dialog]);
						
						if (file_exists($module_dialog[class_path]))
						{
							require_once($module_dialog[class_path]);
							
							if (class_exists($module_dialog['class']) and method_exists($module_dialog['class'], $module_dialog[method]))
							{
								$module_dialog['class']::$module_dialog[method]("project", $_GET[project_id], true);
							}
							else
							{
								// Error
							}
						}
						else
						{
							// Error
						}
					}
					else
					{
						// error
					}
				}
				else
				{
					throw new ProjectSecurityAccessDeniedException();
				}
			break;
			
			// Item Add
			case("item_add"):
				if ($project_security->is_access(3, false) == true)
				{
					if ($_GET[dialog])
					{
						$module_dialog = ModuleDialog::get_by_type_and_internal_name("item_add", $_GET[dialog]);

						if (is_array($module_dialog) and $module_dialog[class_path])
						{
							if (file_exists($module_dialog[class_path]))
							{
								require_once($module_dialog[class_path]);
								
								if (class_exists($module_dialog['class']) and method_exists($module_dialog['class'], $module_dialog[method]))
								{
									$project = new Project($_GET[project_id]);
									$project_item = new ProjectItem($_GET[project_id]);
									$project_item->set_status_id($project->get_current_status_id());
									$project_item->set_gid($_GET[key]);
									
									$description_required = $project_item->is_description_required();
									$keywords_required = $project_item->is_keywords_required();
									
									if (($description_required and !$_POST[description] and !$_GET[idk_unique_id]) or ($keywords_required and !$_POST[keywords] and !$_GET[idk_unique_id]))
									{
										require_once("core/modules/item/io/item.io.php");
										ItemIO::information(http_build_query($_GET), $description_required, $keywords_required);
									}
									else
									{
										$transaction_id = $transaction->begin();
										
										$current_status_requirements = $project->get_current_status_requirements($project->get_current_status_id());
																					
										$folder_id = ProjectStatusFolder::get_folder_by_project_id_and_project_status_id($_GET[project_id],$project->get_current_status_id());
										
										$sub_folder_id = $project->get_sub_folder($_GET[key], $project->get_current_status_id());
										
										if (is_numeric($sub_folder_id))
										{
											$folder_id = $sub_folder_id;
										}
										
										$return_value = $module_dialog['class']::$module_dialog[method]($current_status_requirements[$_GET[key]][type_id], $current_status_requirements[$_GET[key]][category_id], $project->get_organisation_unit_id(), $folder_id);
										
										if (is_numeric($return_value))
										{
											if ($_GET[retrace])
											{
												$params = http_build_query(Retrace::resovle_retrace_string($_GET[retrace]),'','&#38;');
											}
											else
											{
												$paramquery[username] = $_GET[username];
												$paramquery[session_id] = $_GET[session_id];
												$paramquery[nav] = "home";
												$params = http_build_query($paramquery,'','&#38;');
											}
											
											
											if (ProjectItemFactory::create($_GET[project_id], $return_value, $_GET[key], $_POST[keywords], $_POST[description]) == true)
											{
												if ($transaction_id != null)
												{
													$transaction->commit($transaction_id);
												}
												Common_IO::step_proceed($params, "Add Item", "Successful." ,null);
											}
											else
											{
												if ($transaction_id != null)
												{
													$transaction->rollback($transaction_id);
												}
												Common_IO::step_proceed($params, "Add Item", "Failed." ,null);	
											}
										}
										else
										{
											if ($return_value === false)
											{
												if ($transaction_id != null)
												{
													$transaction->rollback($transaction_id);
												}
												throw new ModuleDialogFailedException("",1);
											}
											else
											{
												if ($transaction_id != null)
												{
													$transaction->commit($transaction_id);
												}
											}
										}
									}
								}
								else
								{
									throw new ModuleDialogCorruptException(null, null);
								}
							}
							else
							{
								throw new ModuleDialogCorruptException(null, null);
							}
						}
						else
						{
							throw new ModuleDialogNotFoundException(null, null);
						}
					}
					else
					{
						throw new ModuleDialogMissingException(null, null);
					}
				}
				else
				{
					throw new ProjectSecurityAccessDeniedException();
				}
			break;
			
			// Common Dialogs
			/**
			 * @todo errors, exceptions
			 */
			case("common_dialog"):
				if ($_GET[dialog])
				{
					$module_dialog = ModuleDialog::get_by_type_and_internal_name("common_dialog", $_GET[dialog]);
					
					if (file_exists($module_dialog[class_path]))
					{
						require_once($module_dialog[class_path]);
						
						if (class_exists($module_dialog['class']) and method_exists($module_dialog['class'], $module_dialog[method]))
						{
							$module_dialog['class']::$module_dialog[method]();
						}
						else
						{
							// Error
						}
					}
					else
					{
						// Error
					}
				}
				else
				{
					// error
				}
			break;
			
			// Search
			/**
			 * @todo errors, exceptions
			 */
			case("search"):
				if ($_GET[dialog])
				{
					$module_dialog = ModuleDialog::get_by_type_and_internal_name("search", $_GET[dialog]);
					
					if (file_exists($module_dialog[class_path]))
					{
						require_once($module_dialog[class_path]);
						
						if (class_exists($module_dialog['class']) and method_exists($module_dialog['class'], $module_dialog[method]))
						{
							$module_dialog['class']::$module_dialog[method]();
						}
						else
						{
							// Error
						}
					}
					else
					{
						// Error
					}
				}
				else
				{
					// error
				}
			break;
			
			// Default
			
			default:
				require_once("io/project.io.php");
				ProjectIO::list_user_related_projects(null);
			break;

		endswitch;
	}

}
?>