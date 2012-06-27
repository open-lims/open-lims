<?php
/**
 * @package base
 * @version 0.4.0.0
 * @author Roman Konertz <konertz@open-lims.org>
 * @copyright (c) 2008-2012 by Roman Konertz
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
require_once("interfaces/system_handler.interface.php");

if (constant("UNIT_TEST") == false or !defined("UNIT_TEST"))
{
	require_once("exceptions/base.exception.class.php");
	
	require_once("exceptions/base_event_handler.exception.class.php");
	require_once("exceptions/base_event_handler_creation_failed.exception.class.php");
	
	require_once("exceptions/base_include.exception.class.php");
	require_once("exceptions/base_include_folder_empty.exception.class.php");
	require_once("exceptions/base_include_data_corrupt.exception.class.php");
	require_once("exceptions/base_include_requirement_failed.exception.class.php");
	require_once("exceptions/base_include_process_failed.exception.class.php");
	
	require_once("exceptions/base_module.exception.class.php");
	require_once("exceptions/base_module_data_corrupt.exception.class.php");
	require_once("exceptions/base_module_dialog_corrupt.exception.class.php");
	require_once("exceptions/base_module_dialog_creation_failed.exception.class.php");
	require_once("exceptions/base_module_dialog_missing.exception.class.php");
	require_once("exceptions/base_module_dialog_not_found.exception.class.php");
	require_once("exceptions/base_module_folder_empty.exception.class.php");
	require_once("exceptions/base_module_process_failed.exception.class.php");
	
	require_once("events/include_delete_event.class.php");
	require_once("events/module_disable_event.class.php");
	require_once("events/module_enable_event.class.php");
	
	define("BASE_EVENT_LISTENER_TABLE"		, "core_base_event_listeners");
	define("BASE_INCLUDE_FILE_TABLE"		, "core_base_include_files");
	define("BASE_INCLUDE_TABLE"				, "core_base_includes");
	define("BASE_MODULE_DIALOG_TABLE"		, "core_base_module_dialogs");
	define("BASE_MODULE_LINK_TABLE"			, "core_base_module_links");
	define("BASE_MODULE_FILE_TABLE"			, "core_base_module_files");
	define("BASE_MODULE_NAVIGATION_TABLE"	, "core_base_module_navigation");
	define("BASE_MODULE_TABLE"				, "core_base_modules");
	
	require_once("access/base_event_listener.access.php");
	require_once("access/base_include_file.access.php");
	require_once("access/base_include.access.php");
	require_once("access/base_module_dialog.access.php");
	require_once("access/base_module_link.access.php");
	require_once("access/base_module_file.access.php");
	require_once("access/base_module_navigation.access.php");
	require_once("access/base_module.access.php");
}

/**
 * System Handler Class
 * registers include and module files
 * @package base
 */
class SystemHandler implements SystemHandlerInterface
{
	private $include_folders;
	private $module_folders;
	
	private static $classes = array();
	
	private static $module_get_values = array();
	private static $module_retrace_values = array();
	
	
	/**
	 * @see SystemHandlerInterface::__construct
	 */
	function __construct($recheck = true)
	{
		global $transaction;
		
		if (file_exists(constant("INCLUDE_DIR")) and file_exists(constant("MODULES_DIR")))
		{
			$include_folder_array = scandir(constant("INCLUDE_DIR"));
			
			if (is_array($include_folder_array) and count($include_folder_array) >= 1)
			{				
				foreach($include_folder_array as $key => $value)
				{
					$sub_folder = constant("INCLUDE_DIR")."/".$value;
					if (is_dir($sub_folder) and $key > 1)
					{
						$this->include_folders[] = $sub_folder;
					}
				}
			}
			
			if (file_exists(constant("MODULES_DIR")))
			{
				$module_folder_array = scandir(constant("MODULES_DIR"));
			
				if (is_array($module_folder_array) and count($module_folder_array) >= 1)
				{
					foreach($module_folder_array as $key => $value)
					{
						$sub_folder = constant("MODULES_DIR")."/".$value;
						if (is_dir($sub_folder) and $key > 1)
						{
							$this->module_folders[] = $sub_folder;
						}
					}
				}
			}
			
			$this->init_environment();	
			
			if ($recheck == true)
			{
				$transaction_id = $transaction->begin();
				
				try
				{
					$this->register_includes();
					$this->register_modules();
					
					if ($transaction_id != null)
					{
						$transaction->commit($transaction_id);
					}
				}
				catch(BaseException $e)
				{
					if ($transaction_id != null)
					{
						$transaction->rollback($transaction_id);
					}
					throw $e;
				}
			}
		}
		else
		{
			// Exception
		}
	}
		
	private function init_environment()
	{
		if (is_array($this->include_folders) and 
			count($this->include_folders) >= 1 and 
			is_array($this->module_folders) and 
			count($this->module_folders) >= 1)
		{
			foreach ($this->include_folders as $key => $value)
			{
				$config_folder = $value."/config";
				if (is_dir($config_folder))
				{
					$db_table_name_file = $config_folder."/db_table_name.php";
					if (is_file($db_table_name_file))
					{
						require($db_table_name_file);
					}
					
					$class_path_file = $config_folder."/class_path.php";
					if (is_file($class_path_file))
					{
						require($class_path_file);
						self::$classes = array_merge(self::$classes, $classes);
					}
				}
			}
			
			foreach ($this->module_folders as $key => $value)
			{				
				$config_folder = $value."/config";
				if (is_dir($config_folder))
				{
					$get_file = $config_folder."/module_get.php";
					if (is_file($get_file))
					{
						require($get_file);
						
						if (is_array($get))
						{
							self::$module_get_values = array_merge(self::$module_get_values, $get);
						}
	
						if (is_array($retrace))
						{
							self::$module_retrace_values = array_merge(self::$module_retrace_values, $retrace);
						}
					}
				}
			}
		}
	}
	
	private function register_includes()
	{
		if (is_array($this->include_folders) and count($this->include_folders) >= 1)
		{
			$registered_include_array = BaseInclude_Access::list_folder_entries();
			$found_include_array = array();
			$include_requirements_array = array();
			
			foreach ($this->include_folders as $key => $value)
			{
				$config_folder = $value."/config";
				if (is_dir($config_folder))
				{
					$config_file = $config_folder."/include_info.php";
					if (is_file($config_file))
					{
						require($config_file);
						
						$class_event_listener_file = $config_folder."/class_event_listener.php";
						if (!is_file($class_event_listener_file))
						{
							unset($class_event_listener_file);
						}
						
						$db_table_name_file = $config_folder."/db_table_name.php";
						if (!is_file($db_table_name_file))
						{
							unset($db_table_name_file);
						}
						
						$class_path_file = $config_folder."/class_path.php";
						if (!is_file($class_path_file))
						{
							unset($class_path_file);
						}
												
						if (($register_key = array_search(str_replace(constant("INCLUDE_DIR")."/","",$value), $registered_include_array)) !== false)
						{
							$found_include_array[$register_key] = str_replace(constant("INCLUDE_DIR")."/","",$value);
							
							if ($db_table_name_file)
							{
								$db_table_name_checksum = BaseIncludeFile_Access::get_checksum_by_include_id_and_name($register_key, "db_table_name.php");
								if ($db_table_name_checksum != md5_file($db_table_name_file))
								{
									$db_table_name_id = BaseIncludeFile_Access::get_id_by_include_id_and_name($register_key, "db_table_name.php");
									if ($db_table_name_id != null)
									{
										$base_include_file = new BaseIncludeFile_Access($db_table_name_id);
										$base_include_file->set_checksum(md5_file($db_table_name_file));
									}
									else
									{
										$base_include_file = new BaseIncludeFile_Access(null);
										if ($base_include_file->create($register_key, "db_table_name.php", md5_file($db_table_name_file)) == null)
										{
											throw new IncludeProcessFailedException();
										}
									}
								}
							}
														
							if ($class_event_listener_file)
							{
								$class_event_listener_checksum = BaseIncludeFile_Access::get_checksum_by_include_id_and_name($register_key, "class_event_listener.php");
								if ($class_event_listener_checksum != md5_file($class_event_listener_file))
								{										
									require($class_event_listener_file);
									
									if (BaseEventListener_Access::delete_by_include_id($register_key) == false)
									{
										throw new IncludeProcessFailedException();
									}
									
									// Register Event Handler
									if (is_array($event_listener) and count($event_listener) >= 1)
									{
										foreach($event_listener as $event_listener_key => $event_listener_value)
										{
											$base_event_listener = new BaseEventListener_Access(null);
											if ($base_event_listener->create($register_key, $event_listener_value) == null)
											{
												throw new EventHandlerCreationFailedException();
											}
										}
									}
									
									$class_event_listener_id = BaseIncludeFile_Access::get_id_by_include_id_and_name($register_key, "class_event_listener.php");
									if ($class_event_listener_id != null)
									{
										$base_include_file = new BaseIncludeFile_Access($class_event_listener_id);
										$base_include_file->set_checksum(md5_file($class_event_listener_file));
									}
									else
									{
										$base_include_file = new BaseIncludeFile_Access(null);
										if ($base_include_file->create($register_key, "class_event_listener.php", md5_file($class_event_listener_file)) == null)
										{
											throw new IncludeProcessFailedException();
										}
									}
								}
							}
							
							if ($class_path_file)
							{
								$class_path_checksum = BaseIncludeFile_Access::get_checksum_by_include_id_and_name($register_key, "class_path.php");
								if ($class_path_checksum != md5_file($class_path_file))
								{
									$class_path_id = BaseIncludeFile_Access::get_id_by_include_id_and_name($register_key, "class_path.php");
									if ($class_path_id != null)
									{
										$base_include_file = new BaseIncludeFile_Access($class_path_id);
										$base_include_file->set_checksum(md5_file($class_path_file));
									}
									else
									{
										$base_include_file = new BaseIncludeFile_Access(null);
										if ($base_include_file->create($register_key, "class_path.php", md5_file($class_path_file)) == null)
										{
											throw new IncludeProcessFailedException();
										}
									}
								}
							}
						}
						else
						{
							// Register new includes
							if (is_array($requires) and count($requires) >= 1)
							{
								$include_requirements_array[str_replace(constant("INCLUDE_DIR")."/","",$value)] = $requires;
							}
	
							// Register includes
							$base_include = new BaseInclude_Access(null);
							if (($base_include_id = $base_include->create($name, str_replace(constant("INCLUDE_DIR")."/","",$value))) == null)
							{
								throw new IncludeProcessFailedException();
							}
							else
							{
								$base_include_file = new BaseIncludeFile_Access(null);
								if ($base_include_file->create($base_include_id, "include_info.php", md5_file($config_file)) == null)
								{
									throw new IncludeProcessFailedException();
								}
							}
	
							if ($db_table_name_file)
							{
								$base_include_file = new BaseIncludeFile_Access(null);
								if ($base_include_file->create($base_include_id, "db_table_name.php", md5_file($db_table_name_file)) == null)
								{
									throw new IncludeProcessFailedException();
								}
							}
							
							if ($class_event_listener_file)
							{
								require($class_event_listener_file);
								
								$base_include_file = new BaseIncludeFile_Access(null);
								if ($base_include_file->create($base_include_id, "class_event_listener.php", md5_file($class_event_listener_file)) == null)
								{
									throw new IncludeProcessFailedException();
								}
								
								// Register Event Handler
								if (is_array($event_listener) and count($event_listener) >= 1)
								{
									foreach($event_listener as $event_listener_key => $event_listener_value)
									{
										$base_event_listener = new BaseEventListener_Access(null);
										if ($base_event_listener->create($base_include_id, $event_listener_value) == null)
										{
											throw new EventHandlerCreationFailedException();
										}
									}
								}
							}
	
							if ($class_path_file)
							{
								$base_include_file = new BaseIncludeFile_Access(null);
								if ($base_include_file->create($base_include_id, "class_path.php", md5_file($class_path_file)) == null)
								{
									throw new IncludeProcessFailedException();
								}
							}
							
							$found_include_array[$register_key] = str_replace(constant("INCLUDE_DIR")."/","",$value);
						}
						
						unset($name);
						unset($requires);
						unset($event_listener);
					}
				}
			}
			
			// Past Requirements Check
			$registered_include_array = BaseInclude_Access::list_folder_entries();
			if (is_array($registered_include_array) and count($registered_include_array) >= 1)
			{
				foreach($registered_include_array as $key => $value)
				{
					if (is_array($include_requirements_array[$value]) and count($include_requirements_array[$value]) >= 1)
					{
						foreach ($include_requirements_array[$value] as $sub_key => $sub_value)
						{
							if (!in_array($sub_value, $registered_include_array))
							{
								throw new IncludeRequirementFailedException();
							}
						}
					}
				}
			}
			
			// Delete legacy includes
			$legacy_include_array = array_diff($registered_include_array, $found_include_array);
			if (is_array($legacy_include_array) and count($legacy_include_array) >= 1)
			{
				foreach($legacy_include_array as $legacy_key => $legacy_value)
				{
					if (BaseIncludeFile_Access::delete_by_include_id($legacy_key) == false)
					{
						throw new IncludeProcessFailedException();
					}
					if (BaseEventListener_Access::delete_by_include_id($legacy_key) == false)
					{
						throw new IncludeProcessFailedException();
					}
					
					$include_delete_event = new IncludeDeleteEvent($legacy_key);
					$event_handler = new EventHandler($include_delete_event);
					
					if ($event_handler->get_success() == false)
					{
						throw new IncludeProcessFailedException();
					}	
					
					$base_include = new BaseInclude_Access($legacy_key);
					if ($base_include->delete() == false)
					{
						throw new IncludeProcessFailedException();
					}
				}
			}
			
			// Register Executes
			$registered_include_array = BaseInclude_Access::list_folder_entries();
			if (is_array($registered_include_array) and count($registered_include_array) >= 1)
			{
				foreach($registered_include_array as $key => $value)
				{
					$register_execute = "core/include/".$value."/config/register_execute.php";
					if (is_file($register_execute))
					{
						$register_execute_checksum = BaseIncludeFile_Access::get_checksum_by_include_id_and_name($key, "register_execute.php");
						if ($register_execute_checksum != md5_file($register_execute))
						{										
							require($register_execute);

							if ($result == true)
							{
								BaseIncludeFile_Access::delete_by_include_id_and_name($key, "register_execute.php");
								
								$base_include_file = new BaseIncludeFile_Access(null);
								if ($base_include_file->create($key, "register_execute.php", md5_file($register_execute)) == null)
								{
									throw new IncludeProcessFailedException();
								}
							}
							else
							{
								throw new IncludeProcessFailedException();
							}
							
							unset($result);
						}
					}
				}
			}
		}
	}
	
	private function register_modules()
	{
		if (is_array($this->module_folders) and count($this->module_folders) >= 1)
		{
			$registered_module_array = BaseModule_Access::list_folder_entries();
			$found_module_array = array();
			$module_requirements_array = array();
			
			foreach ($this->module_folders as $key => $value)
			{
				$config_folder = $value."/config";
				if (is_dir($config_folder))
				{
					$config_file = $config_folder."/module_info.php";
					if (is_file($config_file))
					{
						require($config_file);
						
						$module_dialog_file = $config_folder."/module_dialog.php";
						if (!is_file($module_dialog_file))
						{
							unset($module_dialog_file);
						}
						
						$module_link_file = $config_folder."/module_link.php";
						if (!is_file($module_link_file))
						{
							unset($module_link_file);
						}
						
						if (($register_key = array_search(str_replace(constant("MODULES_DIR")."/","",$value), $registered_module_array)) !== false)
						{
							$config_info_changed = false;
							
							$found_module_array[$register_key] = str_replace(constant("MODULES_DIR")."/","",$value);
							
							$config_file_checksum = BaseModuleFile_Access::get_checksum_by_module_id_and_name($register_key, "module_info.php");
							if ($config_file_checksum != md5_file($config_file))
							{
								$config_info_changed = true;
								
								$base_module = new BaseModule_Access($register_key);
								if ($base_module->set_name($name) == false)
								{
									throw new ModuleProcessFailedException();
								}
								if ($base_module->set_class($main_class) == false)
								{
									throw new ModuleProcessFailedException();
								}
								
								$module_info_id = BaseModuleFile_Access::get_id_by_module_id_and_name($register_key, "module_info.php");
								if ($module_info_id != null)
								{
									$base_module_file = new BaseModuleFile_Access($module_info_id);
									$base_module_file->set_checksum(md5_file($config_file));
								}
								else
								{
									$base_module_file = new BaseModuleFile_Access(null);
									if ($base_module_file->create($register_key, "module_info.php", md5_file($config_file)) == null)
									{
										throw new ModuleProcessFailedException();
									}
								}
																
								if (is_array($tab) and count($tab) >= 1)
								{
									$tab_array = array();
									$tab_list = BaseModuleNavigation_Access::list_entries_by_module_id($register_key);
									
									foreach ($tab as $tab_key => $tab_value)
									{				
										array_push($tab_array, $tab_value['language_address']);
										
										if (is_array($tab_list[$tab_value['language_address']]))
										{
											if (trim($tab_list[$tab_value['language_address']]['colour']) != $tab_value['colour'])
											{
												$base_module_navigation = new BaseModuleNavigation_Access($tab_list[$tab_value['language_address']]['id']);
												$base_module_navigation->set_colour($tab_value['colour']);
											}
											
											if (trim($tab_list[$tab_value['language_address']]['controller_class']) != $tab_value['controller_class'])
											{
												$base_module_navigation = new BaseModuleNavigation_Access($tab_list[$tab_value['language_address']]['id']);
												$base_module_navigation->set_controller_class($tab_value['controller_class']);
											}
											
											if (trim($tab_list[$tab_value['language_address']]['controller_file']) != $tab_value['controller_class'])
											{
												$base_module_navigation = new BaseModuleNavigation_Access($tab_list[$tab_value['language_address']]['id']);
												$base_module_navigation->set_controller_file($tab_value['controller_file']);
											}
											
											if (trim($tab_list[$tab_value['language_address']]['alias']) != $tab_value['controller_class'])
											{
												$base_module_navigation = new BaseModuleNavigation_Access($tab_list[$tab_value['language_address']]['id']);
												$base_module_navigation->set_alias($tab_value['alias']);
											}
										}
										else
										{	
											$position = BaseModuleNavigation_Access::get_highest_position();
											$base_module_navigation = new BaseModuleNavigation_Access(null);
											if ($base_module_navigation->create($tab_value['language_address'], $tab_value['colour'], $position+1, $register_key, $tab_value['controller_class'], $tab_value['controller_file'], $tab_value['alias']) == null)
											{
												throw new ModuleProcessFailedException();
											}
										}
									}
									
									if (is_array($tab_list) and count($tab_list) >= 1)
									{
										foreach ($tab_list as $tab_list_key => $tab_list_value)
										{
											if (!in_array($tab_list_key, $tab_array))
											{
												$base_module_navigation = new BaseModuleNavigation_Access($tab_list_value['id']);
												if ($base_module_navigation->delete() == false)
												{
													throw new ModuleProcessFailedException();
												}
											}
										}
									}
								}
							}
							
							if ($module_dialog_file)
							{
								$module_dialog_checksum = BaseModuleFile_Access::get_checksum_by_module_id_and_name($register_key, "module_dialog.php");
								if ($module_dialog_checksum != md5_file($module_dialog_file))
								{									
									require($module_dialog);
									
									if (BaseModuleDialog_Access::delete_by_module_id($register_key) == false)
									{
										throw new ModuleProcessFailedException();
									}
									
									// Register Dialog
									if (is_array($dialog) and count($dialog) >= 1)
									{
										foreach($dialog as $dialog_key => $dialog_value)
										{
											$base_module_dialog = new BaseModuleDialog_Access(null);
											if ($base_module_dialog->create($register_key, $dialog_value[type], $dialog_value[class_path], $dialog_value['class'], $dialog_value[method], $dialog_value[internal_name], $dialog_value[display_name], $dialog_value[weight]) == null)
											{
												throw new ModuleDialogCreationFailedException();
											}
										}
									}
									
									$module_dialog_id = BaseModuleFile_Access::get_id_by_module_id_and_name($register_key, "module_dialog.php");
									if ($module_dialog_id != null)
									{
										$base_module_file = new BaseModuleFile_Access($module_dialog_id);
										$base_module_file->set_checksum(md5_file($module_dialog));
									}
									else
									{
										$base_module_file = new BaseModuleFile_Access(null);
										if ($base_module_file->create($register_key, "module_dialog.php", md5_file($module_dialog_file)) == null)
										{
											throw new ModuleProcessFailedException();
										}
									}
									
									unset($dialog);
								}
							}
							
							if ($module_link_file)
							{									
								$module_link_checksum = BaseModuleFile_Access::get_checksum_by_module_id_and_name($register_key, "module_link.php");							
								if ($module_link_checksum != md5_file($module_link_file))
								{										
									require($module_link);

									if (BaseModuleLink_Access::delete_by_module_id($register_key) == false)
									{
										throw new ModuleProcessFailedException();
									}
									
									// Register Dialog
									if (is_array($link) and count($link) >= 1)
									{
										foreach($link as $link_key => $link_value)
										{
											$base_module_link = new BaseModuleLink_Access(null);
											if ($base_module_link->create($register_key, $link_value[type], serialize($link_value['array']), $link_value[file], $link_value[weight]) == null)
											{
												throw new ModuleLinkCreationFailedException();
											}
										}
									}
									
									$module_link_id = BaseModuleFile_Access::get_id_by_module_id_and_name($register_key, "module_link.php");
									if ($module_link_id != null)
									{
										$base_module_file = new BaseModuleFile_Access($module_link_id);
										$base_module_file->set_checksum(md5_file($module_link));
									}
									else
									{
										$base_module_file = new BaseModuleFile_Access(null);
										if ($base_module_file->create($register_key, "module_link.php", md5_file($module_link_file)) == null)
										{
											throw new ModuleProcessFailedException();
										}
									}
									
									unset($link);
								}
							}
						}
						else
						{
							// Register new includes
							if (is_array($requires) and count($requires) >= 1)
							{
								$module_requirements_array[str_replace(constant("MODULES_DIR")."/","",$value)] = $required_include;
							}
	
							// Register includes
							$base_module = new BaseModule_Access(null);
							if (($base_module_id = $base_module->create($name, str_replace(constant("MODULES_DIR")."/","",$value), $main_class)) == null)
							{
								throw new ModuleProcessFailedException();
							}
							else
							{
								$base_module_file = new BaseModuleFile_Access(null);
								if ($base_module_file->create($base_module_id, "module_info.php", md5_file($config_file)) == null)
								{
									throw new ModuleProcessFailedException();
								}
							}
							
							if (is_array($tab) and count($tab) >= 1)
							{								
								foreach ($tab as $tab_key => $tab_value)
								{
									$position = BaseModuleNavigation_Access::get_highest_position();
									$base_module_navigation = new BaseModuleNavigation_Access(null);
									if ($base_module_navigation->create($tab_value['language_address'], $tab_value['colour'], $position+1, $base_module_id, $tab_value['controller_class'], $tab_value['controller_file'], $tab_value['alias']) == null)
									{
										throw new ModuleProcessFailedException();
									}
								}
							}

							if ($module_dialog_file)
							{
								require($module_dialog_file);
								
								$base_module_file = new BaseModuleFile_Access(null);
								if ($base_module_file->create($base_module_id, "module_dialogs.php", md5_file($module_dialog_file)) == null)
								{
									throw new ModuleProcessFailedException();
								}
								
								// Register Dialog
								if (is_array($dialog) and count($dialog) >= 1)
								{
									foreach($dialog as $dialog_key => $dialog_value)
									{
										$base_module_dialog = new BaseModuleDialog_Access(null);
										if ($base_module_dialog->create($base_module_id, $dialog_value[type], $dialog_value[class_path], $dialog_value['class'], $dialog_value[method], $dialog_value[internal_name], $dialog_value[display_name], $dialog_value[weight]) == null)
										{
											throw new ModuleDialogCreationFailedException();
										}
									}
								}
								
								unset($dialog);
							}
							
							if ($module_link_file)
							{
								require($module_link_file);
								
								$base_module_file = new BaseModuleFile_Access(null);
								if ($base_module_file->create($base_module_id, "module_link.php", md5_file($module_link_file)) == null)
								{
									throw new ModuleProcessFailedException();
								}
								
								// Register Dialog
								if (is_array($link) and count($link) >= 1)
								{
									foreach($link as $link_key => $link_value)
									{
										$base_module_link= new BaseModuleLink_Access(null);
										if ($base_module_link->create($base_module_id, $link_value[type], serialize($link_value['array']), $link_value[file], $link_value[weight]) == null)
										{
											throw new ModuleLinkCreationFailedException();
										}
									}
								}
								unset($link);
							}

							$found_module_array[$base_module_id] = str_replace(constant("MODULES_DIR")."/","",$value);
						}
					}
				}
				
				unset($name);
				unset($class);
				unset($tab);
				unset($required_include);
			}
			
			
			// Past Requirements Check
			$registered_module_array = BaseModule_Access::list_folder_entries();
			if (is_array($registered_module_array) and count($registered_module_array) >= 1)
			{
				foreach($registered_module_array as $key => $value)
				{
					if (is_array($module_requirements_array[$value]) and count($module_requirements_array[$value]) >= 1)
					{
						foreach ($module_requirements_array[$value] as $sub_key => $sub_value)
						{
							if (!in_array($sub_value, $module_requirements_array))
							{
								throw new ModuleRequirementFailedException();
							}
						}
					}
				}
			}

			// Delete legacy modules
			$legacy_module_array = array_diff($registered_module_array, $found_module_array);			
			if (is_array($legacy_module_array) and count($legacy_module_array) >= 1)
			{
				foreach($legacy_module_array as $legacy_key => $legacy_value)
				{
					if (BaseModuleFile_Access::delete_by_module_id($legacy_key) == false)
					{
						throw new ModuleProcessFailedException();
					}
					
					$base_module_navigation_id = BaseModuleNavigation_Access::get_id_by_module_id($legacy_key);
					$base_module_navigation = new BaseModuleNavigation_Access($base_module_navigation_id);
					
					// Position
					if ($base_module_navigation->get_next_position() != $base_module_navigation_id)
					{
						$tmp_base_module_navigation = clone $base_module_navigation;
						$tmp_base_module_navigation_id = $base_module_navigation_id;
						$tmp_position = $base_module_navigation->get_position();
						
						if ($base_module_navigation->set_position(null) === false)
						{
							throw new ModuleProcessFailedException();
						}
						
						while(($next_base_module_navigation_id = $tmp_base_module_navigation->get_next_position()) != $tmp_base_module_navigation_id)
						{							
							$next_base_module_navigation = new BaseModuleNavigation_Access($next_base_module_navigation_id);
							$next_position = $next_base_module_navigation->get_position();
							
							echo $tmp_position." -> ".$next_position."<br />";
							
							if ($next_base_module_navigation->set_position($tmp_position) == false)
							{
								throw new ModuleProcessFailedException();
							}
							else
							{
								$tmp_base_module_navigation = $next_base_module_navigation;
								$tmp_base_module_navigation_id = $next_base_module_navigation_id;
								$tmp_position = $next_position;
							}
						}
					}
					
					if (BaseModuleNavigation_Access::delete_by_module_id($legacy_key) == false)
					{
						throw new ModuleProcessFailedException();
					}
					
					if (BaseModuleDialog_Access::delete_by_module_id($legacy_key) == false)
					{
						throw new ModuleProcessFailedException();
					}
					
					$base_module = new BaseModule_Access($legacy_key);
					if ($base_module->delete() == false)
					{
						throw new ModuleProcessFailedException();
					}
				}
			}
			
		}
	}
		
	
	/**
	 * @see SystemHandlerInterface::get_classes()
	 * @return array
	 */
	public static function get_classes()
	{
		return self::$classes;
	}
	
	/**
	 * @see SystemHandlerInterface::get_module_get_values()
	 * @return array
	 */
	public static function get_module_get_values()
	{
		return self::$module_get_values;
	}
	
	/**
	 * @see SystemHandlerInterface::get_module_retrace_values()
	 * @return array
	 */
	public static function get_module_retrace_values()
	{
		return self::$module_retrace_values;
	}
	
	/**
	 * @see SystemHandlerInterface::get_module_controller()
	 * @param string $nav
	 * @return arráy
	 */
	public static function get_module_controller($nav)
	{
		if ($nav)
		{
			$nav_array = explode(".", $nav);
			
			$module_id = BaseModule_Access::get_module_id_by_module_name($nav_array[0]);
			$base_module = new BaseModule_Access($module_id);
			
			if (is_numeric($module_id))
			{
				if (count($nav_array) == 1)
				{
					$tmp_array = BaseModuleNavigation_Access::get_module_controller($module_id, null);
				}
				elseif (count($nav_array) == 2)
				{
					$tmp_array = BaseModuleNavigation_Access::get_module_controller($module_id, $nav_array[1]);
				}
					
					
				if (is_array($tmp_array) and count($tmp_array) == 2)
				{
					$controller_array['path'] = $base_module->get_folder()."/".$tmp_array['controller_file'];
					$controller_array['class'] = $tmp_array['controller_class'];
					
					if ($nav_array[1])
					{
						$controller_array['alias'] = $nav_array[1];
					}
					else
					{
						$controller_array['alias'] = null;
					}
					
					return $controller_array;
				}
				else
				{
					$controller_array = array();
					$controller_array['path'] = $base_module->get_folder()."/".$nav_array[0].".request.php";
					$controller_array['class'] = $base_module->get_class();
					$controller_array['alias'] = null;
					
					return $controller_array;
				}
			}
		}							
	}
	
	/**
	 * @see SystemHandlerInterface::get_module_name_by_module_id()
	 * @param integer $module_id
	 * @return string
	 */
	public static function get_module_name_by_module_id($module_id)
	{
		return BaseModule_Access::get_module_name_by_module_id($module_id);
	}
	
	/**
	 * @see SystemHandlerInterface::get_module_folder_by_module_name()
	 * @param string $module_name
	 * @return string
	 */
	public static function get_module_folder_by_module_name($module_name)
	{
		return BaseModule_Access::get_module_folder_by_module_name($module_name);
	}
		
	/**
	 * @see SystemHandlerInterface::list_modules()
	 * @return array
	 */
	public static function list_modules()
	{
		return BaseModule_Access::list_entries();
	}
	
	/**
	 * @see SystemHandlerInterface::list_includes()
	 * @return array
	 */
	public static function list_includes()
	{
		return BaseInclude_Access::list_entries();
	}

	/**
	 * @see SystemHandlerInterface::disable_module()
	 * @param integer $module_id
	 * @return bool
	 */
	public static function disable_module($module_id)
	{
		global $transaction;
		
		if (is_numeric($module_id))
		{
			$transaction_id = $transaction->begin();
			
			$module_access = new BaseModule_Access($module_id);
			
			if ($module_access->get_disabled() == true)
			{
				$module_enable_event = new ModuleEnableEvent($module_id);
				$event_handler = new EventHandler($module_enable_event);
					
				if ($event_handler->get_success() == false)
				{
					if ($transaction_id != null)
					{
						$transaction->rollback($transaction_id);
					}
					return false;
				}	
				
				if ($module_access->set_disabled(false) == true)
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
				$module_disable_event = new ModuleDisableEvent($module_id);
				$event_handler = new EventHandler($module_disable_event);
					
				if ($event_handler->get_success() == false)
				{
					if ($transaction_id != null)
					{
						$transaction->rollback($transaction_id);
					}
					return false;
				}	
				
				if ($module_access->set_disabled(true) == true)
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
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * @see SystemHandlerInterface::include_exists()
	 * @param string $include_name
	 * @return bool
	 */
	public static function include_exists($include_name)
	{
		return BaseInclude_Access::exist_entry($include_name);
	}
	
	/**
	 * @see SystemHandlerInterface::module_exists()
	 * @param string $module_name
	 * @return bool
	 */
	public static function module_exists($module_name)
	{
		return BaseModule_Access::exist_entry($module_name);
	}
}
?>