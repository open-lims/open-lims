<?php
/**
 * @package sample
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
 * Sample Request Class
 * @package sample
 */
class SampleRequest
{
	/**
	 * @param string $alias
	 */
	public static function ajax_handler($alias)
	{
		global $sample_security;
		
		if (System::get_post("get_array"))
		{
			$get_array = unserialize(System::get_post("get_array"));	
					
			if (isset($get_array['sample_id']))
			{
				$sample_security = new SampleSecurity($get_array['sample_id']);
			}
			else
			{
				$sample_security = new SampleSecurity(null);
			}
		}
		else
		{
			$sample_security = new SampleSecurity(null);
		}

		switch(System::get_get("run")):
	
			case "list_user_related_samples":
				require_once("ajax/sample.ajax.php");
				echo SampleAjax::list_user_related_samples(
						System::get_post("column_array"), 
						System::get_post("css_page_id"),  
						System::get_post("css_row_sort_id"), 
						System::get_post("entries_per_page"), 
						System::get_get("page"), 
						System::get_get("sortvalue"), 
						System::get_get("sortmethod")
						);
			break;
			
			case "count_user_related_samples":
				require_once("ajax/sample.ajax.php");
				echo SampleAjax::count_user_related_samples();
			break;
			
			case "list_organisation_unit_related_samples":
				require_once("ajax/sample.ajax.php");
				echo SampleAjax::list_organisation_unit_related_samples(
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
			
			case "count_organisation_unit_related_samples":
				require_once("ajax/sample.ajax.php");
				echo SampleAjax::count_organisation_unit_related_samples(System::get_post("argument_array"));
			break;
			
			case "list_sample_items":
				require_once("ajax/sample.ajax.php");
				echo SampleAjax::list_sample_items(
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
			
			case "count_sample_items":
				require_once("ajax/sample.ajax.php");
				echo SampleAjax::count_sample_items(System::get_post("argument_array"));
			break;
			
			case "list_samples_by_item_id":
				require_once("ajax/sample.ajax.php");
				echo SampleAjax::list_samples_by_item_id(
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
			
			case "count_samples_by_item_id":
				require_once("ajax/sample.ajax.php");
				echo SampleAjax::count_samples_by_item_id(System::get_post("argument_array"));
			break;
			
			case "list_location_history":
				require_once("ajax/sample.ajax.php");
				echo SampleAjax::list_location_history(
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
			
			case "count_location_history":
				require_once("ajax/sample.ajax.php");
				echo SampleAjax::count_location_history(System::get_post("argument_array"));
			break;
			
			case "associate":
				require_once("ajax/sample.ajax.php");
				echo SampleAjax::associate(System::get_post("get_array"), System::get_post("sample_id"));
			break;
			
			case "get_sample_menu":
				require_once("ajax/sample.ajax.php");
				echo SampleAjax::get_sample_menu(System::get_post("get_array"));
			break;
			
			case "get_sample_information":
				require_once("ajax/sample.ajax.php");
				echo SampleAjax::get_sample_information(System::get_post("get_array"));
			break;
			
			case "delete":
				require_once("ajax/sample.ajax.php");
				echo SampleAjax::delete(System::get_post("get_array"));
			break;
			
			case "delete_handler":
				require_once("ajax/sample.ajax.php");
				echo SampleAjax::delete_handler(System::get_post("get_array"));
			break;
			
			
			// Search
			
			case "search_sample_list_samples":
				require_once("ajax/sample_search.ajax.php");
				echo SampleSearchAjax::list_samples(
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
			
			case "search_sample_count_samples":
				require_once("ajax/sample_search.ajax.php");
				echo SampleSearchAjax::count_samples(System::get_post("argument_array"));
			break;
			
			case "search_sample_data_list_samples":
				require_once("ajax/sample_data_search.ajax.php");
				echo SampleDataSearchAjax::list_samples(
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
			
			case "search_sample_data_count_samples":
				require_once("ajax/sample_data_search.ajax.php");
				echo SampleDataSearchAjax::count_samples(System::get_post("argument_array"));
			break;
			
			
			// Int. Admin
			
			case "admin_list_user_permissions":
				require_once("ajax/sample_admin.ajax.php");
				echo SampleAdminAjax::list_user_permissions(
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
			
			case "admin_count_user_permissions":
				require_once("ajax/sample_admin.ajax.php");
				echo SampleAdminAjax::count_user_permissions(System::get_post("argument_array"));
			break;
			
			case "admin_list_organisation_unit_permissions":
				require_once("ajax/sample_admin.ajax.php");
				echo SampleAdminAjax::list_organisation_unit_permissions(
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
			
			case "admin_count_organisation_unit_permissions":
				require_once("ajax/sample_admin.ajax.php");
				echo SampleAdminAjax::count_organisation_unit_permissions(System::get_post("argument_array"));
			break;
			
			
			// Ext. Admin
			
			case "admin_sample_template_categorie_list_categories":
				require_once("ajax/admin/admin_sample_template_cat.ajax.php");
				echo AdminSampleTemplateCatAjax::list_categories(
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
			
			case "admin_sample_template_categorie_count_categories":
				require_once("ajax/admin/admin_sample_template_cat.ajax.php");
				echo AdminSampleTemplateCatAjax::count_categories(System::get_post("argument_array"));
			break;
			
			case "admin_sample_template_list_templates":
				require_once("ajax/admin/admin_sample_template.ajax.php");
				echo AdminSampleTemplateAjax::list_templates(
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
			
			case "admin_sample_template_count_templates":
				require_once("ajax/admin/admin_sample_template.ajax.php");
				echo AdminSampleTemplateAjax::count_templates(System::get_post("argument_array"));
			break;
		
			// Create Sample
			
			case "create_sample":
				require_once 'ajax/sample_create.ajax.php';
					
					switch(System::get_get("action")):
	
					case "get_content":
						echo SampleCreateAjax::get_content(System::get_post("page"));
					break;
					
					case "get_next_page":
						echo SampleCreateAjax::get_next_page(System::get_post("page"));
					break;
					
					case "get_previous_page":
						echo SampleCreateAjax::get_previous_page(System::get_post("page"));
					break;
					
					case "set_data":
						echo SampleCreateAjax::set_data(System::get_post("page"), System::get_post("data"));
					break;
					
					case "run":
						echo SampleCreateAjax::run(System::get_get("username"), System::get_get("session_id"));
					break;
				
				endswitch;
			break;
			
			
			// Clone Sample
			
			case "clone_sample":
				require_once 'ajax/sample_clone.ajax.php';
					
					switch(System::get_get("action")):
	
					case "get_content":
						echo SampleCloneAjax::get_content(System::get_post("page"), System::get_post("form_field_name"));
					break;
					
					case "get_next_page":
						echo SampleCloneAjax::get_next_page(System::get_post("page"));
					break;
					
					case "get_previous_page":
						echo SampleCloneAjax::get_previous_page(System::get_post("page"));
					break;
					
					case "set_data":
						echo SampleCloneAjax::set_data(System::get_post("page"), System::get_post("data"));
					break;
					
					case "check_data":
						echo SampleCloneAjax::check_data(System::get_post("name"));
					break;
					
					case "run":
						echo SampleCloneAjax::run(System::get_get("username"), System::get_get("session_id"));
					break;
				
				endswitch;
			break;
			
		endswitch;
	}
	
	/**
	 * @param string $alias
	 * @throws SampleSecurityAccessDeniedException
	 * @throws BaseModuleDialogMethodNotFoundException
	 * @throws BaseModuleDialogClassNotFoundException
	 * @throws BaseModuleDialogFileNotFoundException
	 * @throws BaseModuleDialogMissingException
	 * @throws BaseModuleDialogNotFoundException
	 * @throws ItemAddIOClassNotFoundException
	 * @throws ItemAddIOFileNotFoundException
	 * @throws ItemHandlerClassNotFoundException
	 * @throws ItemPositionIDMissingException
	 * @throws ItemParentIDMissingException
	 * @throws ItemParentTypeMissingException
	 */
	public static function io_handler($alias)
	{
		global $sample_security, $session, $transaction;
		
		if (System::get_get("sample_id"))
		{
			$sample_security = new SampleSecurity(System::get_get("sample_id"));
					
			require_once("io/sample_common.io.php");
 			SampleCommon_IO::tab_header();
		}
		else
		{
			$sample_security = new SampleSecurity(null);
		}

		switch(System::get_get("run")):
		
			case ("new"):
			case ("new_subsample"):
				require_once("io/sample.io.php");
				SampleIO::create();
			break;
			
			case ("clone"):
				require_once("io/sample.io.php");
				SampleIO::clone_sample();
			break;
			
			case ("organ_unit"):
				require_once("io/sample.io.php");
				SampleIO::list_organisation_unit_related_samples();
			break;
			
			case("detail"):
				require_once("io/sample.io.php");
				SampleIO::detail();
			break;
			
			case("move"):
				require_once("io/sample.io.php");
				SampleIO::move();
			break;
			
			case("set_availability"):
				require_once("io/sample.io.php");
				SampleIO::set_availability();
			break;
			
			case("location_history"):
				require_once("io/sample.io.php");
				SampleIO::location_history();
			break;

			// Administration
			
			case ("delete"):
				require_once("io/sample_admin.io.php");
				SampleAdminIO::delete();
			break;
							
			case ("rename"):
				require_once("io/sample_admin.io.php");
				SampleAdminIO::rename();
			break;
			
			case("admin_permission_user"):
				require_once("io/sample_admin.io.php");
				SampleAdminIO::user_permission();
			break;
			
			case("admin_permission_user_add"):
				require_once("io/sample_admin.io.php");
				SampleAdminIO::user_permission_add();
			break;
			
			case("admin_permission_user_delete"):
				require_once("io/sample_admin.io.php");
				SampleAdminIO::user_permission_delete();
			break;
			
			case("admin_permission_ou"):
				require_once("io/sample_admin.io.php");
				SampleAdminIO::ou_permission();
			break;
			
			case("admin_permission_ou_add"):
				require_once("io/sample_admin.io.php");
				SampleAdminIO::ou_permission_add();
			break;
			
			case("admin_permission_ou_delete"):
				require_once("io/sample_admin.io.php");
				SampleAdminIO::ou_permission_delete();
			break;
				
			
			// Item Lister
			case("item_list"):
				if ($sample_security->is_access(1, false) == true)
				{
					if (System::get_get("dialog"))
					{
						if (System::get_get("dialog") == "data")
						{
							$path_stack_array = array();
							
					    	$folder_id = SampleFolder::get_folder_by_sample_id(System::get_get("sample_id"));
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
									$module_dialog['class']::$method("sample", System::get_get("sample_id"), true, false);
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
					throw new SampleSecurityAccessDeniedException();
				}
			break;
			
			case("item_add"):
			case("item_edit"):
				if ($sample_security->is_access(2, false) == true)
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
										$sample_item = new SampleItem(System::get_get("sample_id"));
										$sample_item->set_gid(System::get_get("key"));
										
										$description_required = $sample_item->is_description_required();
										$keywords_required = $sample_item->is_keywords_required();
										
										if (($description_required and !System::get_post("description") and !System::get_get("idk_unique_id")) or ($keywords_required and !System::get_post("keywords") and !System::get_get("idk_unique_id")))
										{
											require_once("core/modules/item/io/item.io.php");
											ItemIO::information(http_build_query($_GET), $description_required, $keywords_required);
										}
										else
										{										
											$sample = new Sample(System::get_get("sample_id"));
											$current_requirements = $sample->get_requirements();
											
											if (System::get_get("run") == "item_add")
											{
												$method = $module_dialog['method'];
												$module_dialog['class']::$method($current_requirements[System::get_get("key")]['type_id'], $current_requirements[System::get_get("key")]['category_id'], "Sample", System::get_get("sample_id"), System::get_get("key"));
											}
											elseif (System::get_get("run") == "item_edit")
											{
												$method = $module_dialog['method'];
												$module_dialog['class']::$method($current_requirements[System::get_get("key")]['fulfilled'][0]['item_id']);
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
					throw new SampleSecurityAccessDeniedException();
				}
			break;

			// Sub Item Add
			case("sub_item_add"):
			case("sub_item_edit"):
				if ($sample_security->is_access(2, false) == true)
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
											else
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
					throw new SampleSecurityAccessDeniedException();
				}
			break;
			
			// Parent Item Lister
			case("parent_item_list"):
				if ($sample_security->is_access(1, false) == true)
				{
					if (System::get_get("dialog"))
					{
						$sample = new Sample(System::get_get("sample_id"));
						$item_id = $sample->get_item_id();
						$module_dialog = ModuleDialog::get_by_type_and_internal_name("parent_item_list", System::get_get("dialog"));
						
						if (file_exists($module_dialog['class_path']))
						{
							require_once($module_dialog['class_path']);
							
							if (class_exists($module_dialog['class']))
							{
								if (method_exists($module_dialog['class'], $module_dialog['method']))
								{
									$method = $module_dialog['method'];
									$module_dialog['class']::$method($item_id);
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
							throw new BaseModuleDialogFileNotFoundException;
						}
					}
					else
					{
						throw new BaseModuleDialogMissingException();
					}
				}
				else
				{
					throw new SampleSecurityAccessDeniedException();
				}
			break;
			
			// Common Dialogs
			case("common_dialog"):
				require_once("core/modules/base/common.request.php");
				CommonRequest::common_dialog();
			break;
							
			default:
				require_once("io/sample.io.php");
				SampleIO::list_user_related_samples();
			break;
		
		endswitch;
	}

	/**
	 * @param string $role
	 * @throws ItemParentIDMissingException
	 * @throws ItemPositionIDMissingException
	 * @throws BaseModuleDialogMethodNotFoundException
	 * @throws BaseModuleDialogClassNotFoundException
	 * @throws BaseModuleDialogFileNotFoundException
	 * @throws BaseModuleDialogNotFoundException
	 * @throws SampleSecurityAccessDeniedException
	 * @throws BaseModuleDialogMissingException
	 */
	public static function item_add_edit_handler($role = "add")
	{		
		if (System::get_get("dialog"))
		{
			if (!is_numeric(System::get_get("parent_id")))
			{
				throw new ItemParentIDMissingException();
			}
			
			if (!is_numeric(System::get_get("key")))
			{
				throw new ItemPositionIDMissingException();
			}
			
			$sample = new Sample(System::get_get("parent_id"));
			$sample_security = new SampleSecurity(System::get_get("parent_id"));
			
			if ($sample_security->is_access(2, false) == true)
			{

				if ($role == "add")
				{
					$module_dialog = ModuleDialog::get_by_type_and_internal_name("item_add", System::get_get("dialog"));
				}
				elseif ($role == "edit")
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
								$sample_item = new SampleItem(System::get_get("parent_id"));
								$sample_item->set_gid(System::get_get("key"));
																
								$current_requirements = $sample->get_requirements();
								
								if ($role == "add")
								{
									$method = $module_dialog['method'];
									$module_dialog['class']::$method($current_requirements[System::get_get("key")]['type_id'], $current_requirements[System::get_get("key")]['category_id'], "Sample", System::get_get("parent_id"), System::get_get("key"));
								}
								elseif ($role == "edit")
								{
									$method = $module_dialog['method'];
									$module_dialog['class']::$method($current_requirements[System::get_get("key")]['fulfilled'][0]['item_id']);
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
				throw new SampleSecurityAccessDeniedException();
			}
		}
		else
		{
			throw new BaseModuleDialogMissingException();
		}
	}
}
?>