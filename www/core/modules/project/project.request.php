<?php
/**
 * @package project
 * @version 0.4.0.0
 * @author Roman Konertz <konertz@open-lims.org>
 * @copyright (c) 2008-2016 by Roman Konertz
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
	/**
	 * @param string $alias
	 */
	public static function ajax_handler($alias)
	{
		global $project_security;
	
		if (System::get_post("get_array"))
		{
			$get_array = unserialize(System::get_post("get_array"));	
					
			if (isset($get_array['project_id']) and is_numeric($get_array['project_id']))
			{
				$project_security = new ProjectSecurity($get_array['project_id']);
			}
			else
			{
				$project_security = new ProjectSecurity(null);
			}
		}
		else
		{
			$project_security = new ProjectSecurity(null);
		}
		
		switch(System::get_get("run")):
			
			// Project
			
			case "list_user_related_projects":
				require_once("ajax/project.ajax.php");
				echo ProjectAjax::list_user_related_projects(
						System::get_post("column_array"), 
						System::get_post("argument_array"), 
						System::get_post("css_page_id"),  
						System::get_post("css_row_sort_id"), 
						System::get_post("entries_per_page"), 
						System::get_get("page"), 
						System::get_get("sortvalue"), 
						System::get_get("sortmethod")
						);
			break;
			
			case "count_user_related_projects":
				require_once("ajax/project.ajax.php");
				echo ProjectAjax::count_user_related_projects(System::get_post("argument_array"));
			break;
		
			case "list_organisation_unit_related_projects":
				require_once("ajax/project.ajax.php");
				echo ProjectAjax::list_organisation_unit_related_projects(
						System::get_post("column_array"), 
						System::get_post("argument_array"), 
						System::get_post("css_page_id"), 
						System::get_post("css_row_sort_id"), 
						System::get_post("entries_per_page"), 
						System::get_get("page"), 
						System::get_get("sortvalue"), 
						System::get_get("sortmethod")
						);
			break;
			
			case "count_organisation_unit_related_projects":
				require_once("ajax/project.ajax.php");
				echo ProjectAjax::count_organisation_unit_related_projects(System::get_post("argument_array"));
			break;
			
			case "list_projects_by_item_id":
				require_once("ajax/project.ajax.php");
				echo ProjectAjax::list_projects_by_item_id(
						System::get_post("column_array"), 
						System::get_post("argument_array"), 
						System::get_post("css_page_id"),  
						System::get_post("css_row_sort_id"), 
						System::get_post("entries_per_page"), 
						System::get_get("page"), 
						System::get_get("sortvalue"), 
						System::get_get("sortmethod")
						);
			break;
			
			case "count_projects_by_item_id":
				require_once("ajax/project.ajax.php");
				echo ProjectAjax::count_projects_by_item_id(System::get_post("argument_array"));
			break;

			case "get_project_status_bar":
				require_once("ajax/project.ajax.php");
				echo ProjectAjax::get_project_status_bar(System::get_post("get_array"));
			break;
			
			case "get_project_menu":
				require_once("ajax/project.ajax.php");
				echo ProjectAjax::get_project_menu(System::get_post("get_array"));
			break;
			
			case "get_project_proceed":
				require_once("ajax/project.ajax.php");
				echo ProjectAjax::get_project_proceed(System::get_post("get_array"));
			break;
			
			case "proceed_project":
				require_once("ajax/project.ajax.php");
				echo ProjectAjax::proceed_project(System::get_post("get_array"), System::get_post("comment"));
			break;
			
			
			// Log
			
			case "log_get_list":
				require_once("ajax/project_log.ajax.php");
				echo ProjectLogAjax::get_list(System::get_post("get_array"), System::get_post("page"));
			break;
			
			case "log_create":
				require_once("ajax/project_log.ajax.php");
				echo ProjectLogAjax::create(System::get_post("get_array"));
			break;
			
			case "log_create_handler":
				require_once("ajax/project_log.ajax.php");
				echo ProjectLogAjax::create_handler(System::get_post("get_array"), 
													System::get_post("comment"), 
													System::get_post("important"));
			break;
			
			case "log_get_more":
				require_once("ajax/project_log.ajax.php");
				echo ProjectLogAjax::get_more(System::get_post("id"));
			break;
			
			case "log_get_less":
				require_once("ajax/project_log.ajax.php");
				echo ProjectLogAjax::get_less(System::get_post("id"));
			break;
			
			case "log_delete":
				require_once("ajax/project_log.ajax.php");
				echo ProjectLogAjax::delete(System::get_post("id"));
			break;
			
			
			// Int Admin
			
			case "get_project_admin_menu":
				require_once("ajax/project_admin.ajax.php");
				echo ProjectAdminAjax::get_project_admin_menu(System::get_post("get_array"));
			break;
			
			case "delete":
				require_once("ajax/project_admin.ajax.php");
				echo ProjectAdminAjax::delete(System::get_post("get_array"));
			break;
			
			case "delete_handler":
				require_once("ajax/project_admin.ajax.php");
				echo ProjectAdminAjax::delete_handler(System::get_post("get_array"));
			break;
			
			case "restore":
				require_once("ajax/project_admin.ajax.php");
				echo ProjectAdminAjax::restore(System::get_post("get_array"));
			break;
			
			case "restore_handler":
				require_once("ajax/project_admin.ajax.php");
				echo ProjectAdminAjax::restore_handler(System::get_post("get_array"));
			break;
			
			case "cancel":
				require_once("ajax/project_admin.ajax.php");
				echo ProjectAdminAjax::cancel(System::get_post("get_array"));
			break;
			
			case "cancel_handler":
				require_once("ajax/project_admin.ajax.php");
				echo ProjectAdminAjax::cancel_handler(System::get_post("get_array'"), System::get_post("comment"));
			break;
			
			
			// Ext. Admin
			
			case "admin_project_status_list_status":
				require_once("ajax/admin/admin_project_status.ajax.php");
				echo AdminProjectStatusAjax::list_status(
						System::get_post("column_array"), 
						System::get_post("argument_array"), 
						System::get_post("get_array"), 
						System::get_post("css_page_id"), 
						System::get_post("css_row_sort_id"), 
						System::get_post("entries_per_page"), 
						System::get_get("page"), 
						System::get_get("sortvalue"), 
						System::get_get("sortmethod")
						);
			break;
			
			case "admin_project_status_count_status":
				require_once("ajax/admin/admin_project_status.ajax.php");
				echo AdminProjectStatusAjax::count_status(System::get_post("argument_array"));
			break;
			
			case "admin_project_template_cat_list_categories":
				require_once("ajax/admin/admin_project_template_cat.ajax.php");
				echo AdminProjectTemplateCatAjax::list_categories(
						System::get_post("column_array"), 
						System::get_post("argument_array"), 
						System::get_post("get_array"), 
						System::get_post("css_page_id"), 
						System::get_post("css_row_sort_id"), 
						System::get_post("entries_per_page"), 
						System::get_get("page"), 
						System::get_get("sortvalue"), 
						System::get_get("sortmethod")
						);
			break;
			
			case "admin_project_template_cat_count_categories":
				require_once("ajax/admin/admin_project_template_cat.ajax.php");
				echo AdminProjectTemplateCatAjax::count_categories(System::get_post("argument_array"));
			break;
			
			case "admin_project_template_list_templates":
				require_once("ajax/admin/admin_project_template.ajax.php");
				echo AdminProjectTemplateAjax::list_templates(
						System::get_post("column_array"), 
						System::get_post("argument_array"), 
						System::get_post("get_array"), 
						System::get_post("css_page_id"), 
						System::get_post("css_row_sort_id"), 
						System::get_post("entries_per_page"), 
						System::get_get("page"), 
						System::get_get("sortvalue"), 
						System::get_get("sortmethod")
						);
			break;
			
			case "admin_project_template_count_templates":
				require_once("ajax/admin/admin_project_template.ajax.php");
				echo AdminProjectTemplateAjax::count_templates(System::get_post("argument_array"));
			break;
			
			
			// Tasks
			
			case "list_project_tasks":
				require_once("ajax/project_task.ajax.php");
				echo ProjectTaskAjax::list_project_tasks(
						System::get_post("column_array"), 
						System::get_post("argument_array"), 
						System::get_post("css_page_id"), 
						System::get_post("css_row_sort_id"), 
						System::get_post("entries_per_page"), 
						System::get_get("page"), 
						System::get_get("sortvalue"), 
						System::get_get("sortmethod")
						);
			break;
			
			case "count_project_tasks":
				require_once("ajax/project_task.ajax.php");
				echo ProjectTaskAjax::count_project_tasks(System::get_post("argument_array"));
			break;
			
			case "list_project_permissions":
				require_once("ajax/project_admin.ajax.php");
				echo ProjectAdminAjax::list_project_permissions(
						System::get_post("column_array"), 
						System::get_post("argument_array"), 
						System::get_post("css_page_id"), 
						System::get_post("css_row_sort_id"), 
						System::get_post("entries_per_page"), 
						System::get_get("page"), 
						System::get_get("sortvalue"), 
						System::get_get("sortmethod")
						);
			break;
			
			case "count_project_permissions":
				require_once("ajax/project_admin.ajax.php");
				echo ProjectAdminAjax::count_project_permissions(System::get_post("argument_array"));
			break;
			
			
			// Search
			
			case "search_project_list_projects":
				require_once("ajax/project_search.ajax.php");
				echo ProjectSearchAjax::list_projects(
						System::get_post("column_array"), 
						System::get_post("argument_array"), 
						System::get_post("css_page_id"), 
						System::get_post("css_row_sort_id"), 
						System::get_post("entries_per_page"), 
						System::get_get("page"), 
						System::get_get("sortvalue"), 
						System::get_get("sortmethod")
						);
			break;
			
			case "search_project_count_projects":
				require_once("ajax/project_search.ajax.php");
				echo ProjectSearchAjax::count_projects(System::get_post("argument_array"));
			break;
			
			case "search_project_data_list_projects":
				require_once("ajax/project_data_search.ajax.php");
				echo ProjectDataSearchAjax::list_projects(
						System::get_post("column_array"), 
						System::get_post("argument_array"), 
						System::get_post("css_page_id"), 
						System::get_post("css_row_sort_id"), 
						System::get_post("entries_per_page"), 
						System::get_get("page"), 
						System::get_get("sortvalue"), 
						System::get_get("sortmethod")
						);
			break;
			
			case "search_project_data_count_projects":
				require_once("ajax/project_data_search.ajax.php");
				echo ProjectDataSearchAjax::count_projects(System::get_post("argument_array"));
			break;
			
			
			// Navigation
			
			case "navigation":
				require_once 'ajax/navigation/project_navigation.ajax.php';
					
				switch(System::get_get("action")):
	
					case "get_name":
						echo ProjectNavigationAjax::get_name();
					break;
					
					case "get_html":
						echo ProjectNavigationAjax::get_html();
					break;
					
					case "get_array":
						echo ProjectNavigationAjax::get_array();
					break;
					
					case "set_array":
						echo ProjectNavigationAjax::set_array(System::get_post("array"));
					break;
				
					case "get_children":
						echo ProjectNavigationAjax::get_children(System::get_post("id"));
					break;	
				
				endswitch;
			break;
			
			
			// Create Project
			
			case "create_project":
				require_once 'ajax/project_create.ajax.php';
					
				switch(System::get_get("action")):
	
					case "get_content":
						echo ProjectCreateAjax::get_content(System::get_post("page"));
					break;
					
					case "get_next_page":
						echo ProjectCreateAjax::get_next_page(System::get_post("page"));
					break;
					
					case "get_previous_page":
						echo ProjectCreateAjax::get_previous_page(System::get_post("page"));
					break;
					
					case "set_data":
						echo ProjectCreateAjax::set_data(System::get_post("page"), System::get_post("data"));
					break;
					
					case "check_data":
						echo ProjectCreateAjax::check_data(System::get_post("name"));
					break;
					
					case "run":
						echo ProjectCreateAjax::run(System::get_get("username"), System::get_get("session_id"));
					break;
				
				endswitch;
			break;
			
		endswitch;
	}
	
	/**
	 * @param string $alias
	 * @throws BaseModuleDialogMethodNotFoundException
	 * @throws BaseModuleDialogClassNotFoundException
	 * @throws BaseModuleDialogFileNotFoundException
	 * @throws BaseModuleDialogMissingException
	 * @throws ProjectSecurityAccessDeniedException
	 * @throws BaseModuleDialogNotFoundException
	 * @throws ItemAddIOClassNotFoundException
	 * @throws ItemAddIOFileNotFoundException
	 * @throws ItemHandlerClassNotFoundException
	 * @throws ItemPositionIDMissingException
	 * @throws ItemParentIDMissingException
	 * @throws ItemParentTypeMissingException
	 * @throws ProjectStatusWithoutExtensionException
	 * @throws BaseExtensionClassNotFoundException
	 * @throws BaseExtensionFileNotFoundException
	 * @throws BaseExtensionNotFoundException
	 * @throws BaseExtensionMissingException
	 */
	public static function io_handler($alias)
	{
		global $project_security, $session, $transaction;

		if (System::get_get("project_id") and is_numeric(System::get_get("project_id")))
		{
			$project_security = new ProjectSecurity(System::get_get("project_id"));
					
			if (System::get_get("run") != "new_subproject")
			{
 				require_once("io/project_common.io.php");
 				ProjectCommon_IO::tab_header();
			}
		}
		else
		{
			$project_security = new ProjectSecurity(null);
		}

		switch(System::get_get("run")):
		
			case ("new"):
			case ("new_subproject"):
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
				ProjectIO::list_user_related_projects(System::get_get("id"));
			break;		

			case("organ_unit"):
				require_once("io/project.io.php");
				ProjectIO::list_organisation_unit_related_projects();
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
			case("item_list"):
				if ($project_security->is_access(1, false) == true)
				{
					if (System::get_get("dialog"))
					{
						if (System::get_get("dialog") == "data")
						{
							$path_stack_array = array();
							
							$folder_id = ProjectFolder::get_folder_by_project_id(System::get_get("project_id"));
					    	$folder = Folder::get_instance($folder_id);
					    	$init_array = $folder->get_object_id_path();
					    	
					    	foreach($init_array as $key => $value)
					    	{
					    		$temp_array = array();
					    		$temp_array['virtual'] = false;
					    		$temp_array['id'] = $value;
					    		array_unshift($path_stack_array, $temp_array);
					    	}
							
					    	if (!System::get_get("folder_id"))
					    	{
								$session->write_value("stack_array", $path_stack_array, true);
					    	}
						}
						
						$module_dialog = ModuleDialog::get_by_type_and_internal_name("item_list", System::get_get("dialog"));
						
						if (file_exists($module_dialog['class_path']))
						{
							require_once($module_dialog['class_path']);
							
							if (class_exists($module_dialog['class']))
							{
								if (method_exists($module_dialog['class'], $module_dialog['method']))
								{
									$method = $module_dialog['method'];
									$module_dialog['class']::$method("project", System::get_get("project_id"), true);
								}
								else
								{
									throw new BaseModuleDialogMethodNotFoundException();
								}
							}
							else
							{
								throw new BaseModuleDialogClassNotFoundException();
							}
						}
						else
						{
							throw new BaseModuleDialogFileNotFoundException();
						}
					}
					else
					{
						throw new BaseModuleDialogMissingException();
					}
				}
				else
				{
					throw new ProjectSecurityAccessDeniedException();
				}
			break;
			
			// Item Add
			case("item_add"):
			case("item_edit"):
				if ($project_security->is_access(3, false) == true)
				{
					if (System::get_get("dialog"))
					{
						if (System::get_get("run") == "item_add")
						{
							$module_dialog = ModuleDialog::get_by_type_and_internal_name("item_add", System::get_get("dialog"));
						}
						elseif (System::get_get("run") == "item_edit")
						{
							$module_dialog = ModuleDialog::get_by_type_and_internal_name("item_edit", System::get_get("dialog"));
						}

						if (is_array($module_dialog) and $module_dialog['class_path'])
						{
							if (file_exists($module_dialog['class_path']))
							{
								require_once($module_dialog['class_path']);
								
								if (class_exists($module_dialog['class']))
								{
									if (method_exists($module_dialog['class'], $module_dialog['method']))
									{
										$project = new Project(System::get_get("project_id"));
										$project_item = new ProjectItem(System::get_get("project_id"));
										$project_item->set_status_id($project->get_current_status_id());
										$project_item->set_gid(System::get_get("key"));
										
										$description_required = $project_item->is_description_required();
										$keywords_required = $project_item->is_keywords_required();
										
										if (($description_required and !System::get_post("description") and !System::get_get("idk_unique_id")) or ($keywords_required and !System::get_post("keywords") and !System::get_get("idk_unique_id")))
										{
											require_once("core/modules/item/io/item.io.php");
											ItemIO::information(http_build_query($_GET), $description_required, $keywords_required);
										}
										else
										{
											$current_status_requirements = $project->get_current_status_requirements($project->get_current_status_id());
											
											if (System::get_get("run") == "item_add")
											{
												$method = $module_dialog['method'];
												$module_dialog['class']::$method($current_status_requirements[System::get_get("key")]['type_id'], $current_status_requirements[System::get_get("key")]['category_id'], "Project", System::get_get("project_id"), System::get_get("key"));
											}
											elseif (System::get_get("run") == "item_edit")
											{
												$method = $module_dialog['method'];
												$module_dialog['class']::$method($current_status_requirements[System::get_get("key")]['fulfilled'][0]['item_id']);
											}
										}
									}
									else
									{
										throw new BaseModuleDialogMethodNotFoundException();
									}
								}
								else
								{
									throw new BaseModuleDialogClassNotFoundException();
								}
							}
							else
							{
								throw new BaseModuleDialogFileNotFoundException();
							}
						}
						else
						{
							throw new BaseModuleDialogNotFoundException();
						}
					}
					else
					{
						throw new BaseModuleDialogMissingException();
					}
				}
				else
				{
					throw new ProjectSecurityAccessDeniedException();
				}
			break;
			
			// Sub Item Add and Edit
			case("sub_item_add"):
			case("sub_item_edit"):
				if ($project_security->is_access(3, false) == true)
				{
					if (System::get_get("parent"))
					{
						if (is_numeric(System::get_get("parent_id")))
						{
							if (is_numeric(System::get_get("key")))
							{
								$item_handling_class = Item::get_handling_class_by_type(System::get_get("parent"));
														
								if (class_exists($item_handling_class))
								{
									$item_io_handling_class = $item_handling_class::get_item_add_io_handling_class();
									
									if (file_exists("core/modules/".$item_io_handling_class[0]))
									{
										require_once("core/modules/".$item_io_handling_class[0]);
										if (class_exists($item_io_handling_class[1]))
										{
											if (System::get_get("run") == "sub_item_add")
											{
												$item_io_handling_class[1]::item_add_edit_handler("add");
											}
											elseif(System::get_get("run") == "sub_item_edit")
											{
												$item_io_handling_class[1]::item_add_edit_handler("edit");
											}
										}
										else
										{
											throw new ItemAddIOClassNotFoundException();
										}
									}
									else
									{
										throw new ItemAddIOFileNotFoundException();
									}
								}
								else
								{
									throw new ItemHandlerClassNotFoundException();
								}
							}
							else
							{
								throw new ItemPositionIDMissingException();
							}
						}
						else
						{
							throw new ItemParentIDMissingException();
						}
					}
					else
					{
						throw new ItemParentTypeMissingException();
					}
				}
				else
				{
					throw new ProjectSecurityAccessDeniedException();
				}
			break;
						
			// Extension
			/**
			 * @todo type filter
			 */
			case("extension"):
				if (System::get_get("extension"))
				{
					$extension_id = Extension::get_id_by_identifier(System::get_get("extension"));
					
					if ($extension_id)
					{
						$extension = new Extension($extension_id);
						
						
						$main_file = constant("EXTENSION_DIR")."/".$extension->get_folder()."/".$extension->get_main_file();
						$main_class = $extension->get_class();
						
						if (file_exists($main_file))
						{
							require_once($main_file);
							
							if (class_exists($main_class))
							{
								$project = new Project(System::get_get("project_id"));
								$project_item = new ProjectItem(System::get_get("project_id"));
								$project_status_requirements = $project->get_current_status_requirements();
								
								if (is_array($project_status_requirements) and count($project_status_requirements) >= 1)
								{
									foreach($project_status_requirements as $key => $value)
									{
										if($value['element_type'] == "extension" and $value['extension'] == System::get_get("extension"))
										{
											if (is_array($value['filter']) and count($value['filter']) >= 1)
											{
												$filter_array = $value['filter'];
											}
											else
											{
												$filter_array = null;
											}
											break;
										}
									}
								}
								else
								{
									throw new ProjectStatusWithoutExtensionException();
								}
							
								
								if ($filter_array)
								{
									$item_array = array();
									
									foreach($filter_array as $key => $value)
									{
										if (is_numeric($value['status']))
										{
											$item_array = array_merge($item_array, $project_item->get_project_status_items($value['status'], true));
										}
									}
								}
								else
								{
									$item_array = $project_item->get_project_items(true);
								}					
								
								$event_identifier = uniqid("", true);
								
								if ($session->is_value("PROJECT_EXTENSION_EVENT_IDENTIFIER_ARRAY"))
								{
									$project_extension_event_identifier_array = $session->read_value("PROJECT_EXTENSION_EVENT_IDENTIFIER_ARRAY");
									$project_extension_event_identifier_array[$event_identifier] = System::get_get("project_id");
								}
								else
								{
									$project_extension_event_identifier_array = array();
									$project_extension_event_identifier_array[$event_identifier] = System::get_get("project_id");
								}
								
								$session->write_value("PROJECT_EXTENSION_EVENT_IDENTIFIER_ARRAY", $project_extension_event_identifier_array);
								$main_class::set_event_identifier($event_identifier);
								
								$main_class::set_target_folder_id(ProjectStatusFolder::get_folder_by_project_id_and_project_status_id(System::get_get("project_id"), $project->get_current_status_id()));
								$main_class::push_data($item_array);
							}
							else
							{
								throw new BaseExtensionClassNotFoundException();
							}
						}
						else
						{
							throw new BaseExtensionFileNotFoundException();
						}
					}
					else
					{
						throw new BaseExtensionNotFoundException();
					}
				}
				else
				{
					throw new BaseExtensionMissingException();
				}
			break;
			
			// Common Dialogs
			case("common_dialog"):
				require_once("core/modules/base/common.request.php");
				CommonRequest::common_dialog();
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