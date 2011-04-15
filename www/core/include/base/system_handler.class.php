<?php
/**
 * @package base
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
require_once("interfaces/system_handler.interface.php");

if (constant("UNIT_TEST") == false or !defined("UNIT_TEST"))
{
	require_once("exceptions/event_handler_creation_failed_exception.class.php");
	require_once("exceptions/include_folder_empty_exception.class.php");
	require_once("exceptions/include_data_corrupt_exception.class.php");
	require_once("exceptions/include_requirement_failed_exception.class.php");
	require_once("exceptions/include_process_failed_exception.class.php");
	require_once("exceptions/module_data_corrupt_exception.class.php");
	require_once("exceptions/module_dialog_corrupt_exception.class.php");
	require_once("exceptions/module_dialog_creation_failed_exception.class.php");
	require_once("exceptions/module_dialog_missing_exception.class.php");
	require_once("exceptions/module_dialog_not_found_exception.class.php");
	require_once("exceptions/module_folder_empty_exception.class.php");
	require_once("exceptions/module_process_failed_exception.class.php");
	
	require_once("events/include_delete_event.class.php");
	
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
 * @todo Modules
 */
class SystemHandler implements SystemHandlerInterface
{
	function __construct()
	{
		$this->scan_include();
		$this->scan_modules();
	}
		
	/**
	 * Scans include folder and registers include-modules
	 * @return bool
	 * @throws IncludeDataCorruptException
	 * @throws IncludeProcessFailedException
	 * @throws IncludeRequirementFailedException
	 * @throws IncludeFolderEmptyException
	 * @throws EventHandlerCreationFailedException
	 * @todo create warning on empty folders
	 * @todo refresh md5 after rewriting changes of data
	 */
	private function scan_include()
	{
		global $transaction;
		
		$registered_include_array = BaseInclude_Access::list_folder_entries();
		$found_include_array = array();
		$include_requirements_array = array();
		
		$include_folder_array = scandir($GLOBALS[include_dir]);
		
		if (is_array($include_folder_array) and count($include_folder_array) >= 1)
		{
			$transaction_id = $transaction->begin();
			
			foreach($include_folder_array as $key => $value)
			{
				$sub_folder = $GLOBALS[include_dir]."/".$value;
				if (is_dir($sub_folder) and $key > 1)
				{
					$config_folder = $sub_folder."/config";
					if (is_dir($config_folder))
					{
						$config_file = $config_folder."/include_info.php";
						if (is_file($config_file))
						{
							include($config_file);
							
							if ($no_db_table_name != true)
							{
								$db_table_name_file = $config_folder."/db_table_name.php";
								if (!is_file($db_table_name_file))
								{
									if ($transaction_id != null)
									{
										$transaction->rollback($transaction_id);
									}
									throw new IncludeDataCorruptException(null, null);
								}
								else
								{
									include($db_table_name_file);
								}
							}
							
							if ($no_class_event_listener != true)
							{
								$class_event_listener = $config_folder."/class_event_listener.php";
								if (!is_file($class_event_listener))
								{
									if ($transaction_id != null)
									{
										$transaction->rollback($transaction_id);
									}
									throw new IncludeDataCorruptException(null, null);
								}
							}
							
							if ($no_class_path != true)
							{
								$class_path_file = $config_folder."/class_path.php";
								if (!is_file($class_path_file))
								{
									if ($transaction_id != null)
									{
										$transaction->rollback($transaction_id);
									}
									throw new IncludeDataCorruptException(null, null);
								}
							}
							
							// Is include registered ?
							if (($register_key = array_search($value, $registered_include_array)) !== false)
							{
								$found_include_array[$register_key] = $value;
								
								// Check Files
								if ($no_class_event_listener != true)
								{
									$class_event_listener_checksum = BaseIncludeFile_Access::get_checksum_by_include_id_and_name($register_key, "class_event_listener.php");
									if ($class_event_listener_checksum != md5_file($class_event_listener))
									{										
										include($class_event_listener);
										
										if (BaseEventListener_Access::delete_by_include_id($register_key) == false)
										{
											if ($transaction_id != null)
											{
												$transaction->rollback($transaction_id);
											}
											throw new IncludeProcessFailedException(null, null);
										}
										
										// Register Event Handler
										if (is_array($event_listener) and count($event_listener) >= 1)
										{
											foreach($event_listener as $event_listener_key => $event_listener_value)
											{
												$base_event_listener = new BaseEventListener_Access(null);
												if ($base_event_listener->create($register_key, $event_listener_value) == null)
												{
													if ($transaction_id != null)
													{
														$transaction->rollback($transaction_id);
													}
													throw new EventHandlerCreationFailedException(null, null);
												}
											}
										}
										
										$class_event_listener_id = BaseIncludeFile_Access::get_id_by_include_id_and_name($register_key, "class_event_listener.php");
										if ($class_event_listener_id != null)
										{
											$base_include_file = new BaseIncludeFile_Access($class_event_listener_id);
											$base_include_file->set_checksum(md5_file($class_event_listener));
										}
										else
										{
											$base_include_file = new BaseIncludeFile_Access(null);
											if ($base_include_file->create($register_key, "class_event_listener.php", md5_file($class_event_listener)) == null)
											{
												if ($transaction_id != null)
												{
													$transaction->rollback($transaction_id);
												}
												throw new IncludeProcessFailedException(null, null);
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
									$include_requirements_array[$value] = $requires;
								}
		
								// Register includes
								$base_include = new BaseInclude_Access(null);
								if (($base_include_id = $base_include->create($name, $value)) == null)
								{
									if ($transaction_id != null)
									{
										$transaction->rollback($transaction_id);
									}
									throw new IncludeProcessFailedException(null, null);
								}
								else
								{
									$base_include_file = new BaseIncludeFile_Access(null);
									if ($base_include_file->create($base_include_id, "include_info.php", md5_file($config_file)) == null)
									{
										if ($transaction_id != null)
										{
											$transaction->rollback($transaction_id);
										}
										throw new IncludeProcessFailedException(null, null);
									}
								}
		
								if ($no_db_table_name != true)
								{
									$base_include_file = new BaseIncludeFile_Access(null);
									if ($base_include_file->create($base_include_id, "db_table_name.php", md5_file($db_table_name_file)) == null)
									{
										if ($transaction_id != null)
										{
											$transaction->rollback($transaction_id);
										}
										throw new IncludeProcessFailedException(null, null);
									}
								}
								
								if ($no_class_event_listener != true)
								{
									include($class_event_listener);
									
									$base_include_file = new BaseIncludeFile_Access(null);
									if ($base_include_file->create($base_include_id, "class_event_listener.php", md5_file($class_event_listener)) == null)
									{
										if ($transaction_id != null)
										{
											$transaction->rollback($transaction_id);
										}
										throw new IncludeProcessFailedException(null, null);
									}
									
									// Register Event Handler
									if (is_array($event_listener) and count($event_listener) >= 1)
									{
										foreach($event_listener as $event_listener_key => $event_listener_value)
										{
											$base_event_listener = new BaseEventListener_Access(null);
											if ($base_event_listener->create($base_include_id, $event_listener_value) == null)
											{
												if ($transaction_id != null)
												{
													$transaction->rollback($transaction_id);
												}
												throw new EventHandlerCreationFailedException(null, null);
											}
										}
									}
								}
		
								if ($no_class_path != true)
								{
									$base_include_file = new BaseIncludeFile_Access(null);
									if ($base_include_file->create($base_include_id, "class_path.php", md5_file($class_path_file)) == null)
									{
										if ($transaction_id != null)
										{
											$transaction->rollback($transaction_id);
										}
										throw new IncludeProcessFailedException(null, null);
									}
								}
								
								$found_include_array[$register_key] = $value;
							}
						}
						else
						{
							// Warning
						}
					}
					else
					{
						// Warning
					}
				}
				unset($name);
				unset($requires);
				unset($event_listener);
				unset($no_db_table_name);
				unset($no_class_event_listener);
				unset($no_class_path);
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
								if ($transaction_id != null)
								{
									$transaction->rollback($transaction_id);
								}
								throw new IncludeRequirementFailedException(null, null);
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
						if ($transaction_id != null)
						{
							$transaction->rollback($transaction_id);
						}
						throw new IncludeProcessFailedException(null, null);
					}
					if (BaseEventListener_Access::delete_by_include_id($legacy_key) == false)
					{
						if ($transaction_id != null)
						{
							$transaction->rollback($transaction_id);
						}
						throw new IncludeProcessFailedException(null, null);
					}
					$include_delete_event = new IncludeDeleteEvent($legacy_key);
					$event_handler = new EventHandler($include_delete_event);
					
					if ($event_handler->get_success() == false)
					{
						if ($transaction_id != null)
						{
							$transaction->rollback($transaction_id);
						}
						return false;
					}	
					
					$base_include = new BaseInclude_Access($legacy_key);
					if ($base_include->delete() == false)
					{
						if ($transaction_id != null)
						{
							$transaction->rollback($transaction_id);
						}
						throw new IncludeProcessFailedException(null, null);
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
							include($register_execute);

							if ($result == true)
							{
								BaseIncludeFile_Access::delete_by_include_id_and_name($key, "register_execute.php");
								
								$base_include_file = new BaseIncludeFile_Access(null);
								if ($base_include_file->create($key, "register_execute.php", md5_file($register_execute)) == null)
								{
									if ($transaction_id != null)
									{
										$transaction->rollback($transaction_id);
									}
									throw new IncludeProcessFailedException(null, null);
								}
							}
							else
							{
								if ($transaction_id != null)
								{
									$transaction->rollback($transaction_id);
								}
								throw new IncludeProcessFailedException(null, null);
							}
							
							unset($result);
						}
					}
				}
			}
			
			if ($transaction_id != null)
			{
				$transaction->commit($transaction_id);
			}
			
			return true;
		}
		else
		{
			throw new IncludeFolderEmptyException(null, null);
		}
	}
	
	/**
	 * @todo use flag for navigation
	 * @todo add register dialog (like EventHander in Include)
	 * @todo reregister module after md5-checksum file change (hold position)
	 * @todo lösche module_links und module_dialogs (evtl.)
	 */
	private function scan_modules()
	{
		global $transaction;
		
		$registered_module_array = BaseModule_Access::list_folder_entries();
		$found_module_array = array();
		$module_requirements_array = array();
		
		$module_folder_array = scandir($GLOBALS[modules_dir]);
		
		if (is_array($module_folder_array) and count($module_folder_array) >= 1)
		{
			$transaction_id = $transaction->begin();
			
			foreach($module_folder_array as $key => $value)
			{
				$sub_folder = $GLOBALS[modules_dir]."/".$value;
				if (is_dir($sub_folder) and $key > 1)
				{
					$config_folder = $sub_folder."/config";
					if (is_dir($config_folder))
					{
						$config_file = $config_folder."/module_info.php";
						if (is_file($config_file))
						{
							include($config_file);
						
							if ($no_dialog != true)
							{
								$module_dialog = $config_folder."/module_dialog.php";
								if (!is_file($module_dialog))
								{
									if ($transaction_id != null)
									{
										$transaction->rollback($transaction_id);
									}
									throw new ModuleDataCorruptException(null, null);
								}
							}
							
							if ($no_link != true)
							{
								$module_link = $config_folder."/module_link.php";
								if (!is_file($module_link))
								{
									if ($transaction_id != null)
									{
										$transaction->rollback($transaction_id);
									}
									throw new ModuleDataCorruptException(null, null);
								}
							}
							
							// Is include registered ?
							if (($register_key = array_search($value, $registered_module_array)) !== false)
							{
								$found_module_array[$register_key] = $value;
								
								// Check Files
								if ($no_dialog != true)
								{
									$module_dialog_checksum = BaseModuleFile_Access::get_checksum_by_module_id_and_name($register_key, "module_dialog.php");
									if ($module_dialog_checksum != md5_file($module_dialog))
									{										
										include($module_dialog);
										
										if (BaseModuleDialog_Access::delete_by_module_id($register_key) == false)
										{
											if ($transaction_id != null)
											{
												$transaction->rollback($transaction_id);
											}
											throw new ModuleProcessFailedException(null, null);
										}
										
										// Register Dialog
										if (is_array($dialog) and count($dialog) >= 1)
										{
											foreach($dialog as $dialog_key => $dialog_value)
											{
												$base_module_dialog = new BaseModuleDialog_Access(null);
												if ($base_module_dialog->create($register_key, $dialog_value[type], $dialog_value[class_path], $dialog_value['class'], $dialog_value[method], $dialog_value[internal_name], $dialog_value[display_name], $dialog_value[weight]) == null)
												{
													if ($transaction_id != null)
													{
														$transaction->rollback($transaction_id);
													}
													throw new ModuleDialogCreationFailedException(null, null);
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
											if ($base_module_file->create($register_key, "module_dialog.php", md5_file($module_dialog)) == null)
											{
												if ($transaction_id != null)
												{
													$transaction->rollback($transaction_id);
												}
												throw new IncludeProcessFailedException(null, null);
											}
										}
									}
								}
								
								if ($no_link != true)
								{

									$module_link_checksum = BaseModuleFile_Access::get_checksum_by_module_id_and_name($register_key, "module_link.php");
									if ($module_link_checksum != md5_file($module_link))
									{										
										include($module_link);

										if (BaseModuleDialog_Access::delete_by_module_id($register_key) == false)
										{
											if ($transaction_id != null)
											{
												$transaction->rollback($transaction_id);
											}
											throw new ModuleProcessFailedException(null, null);
										}
										
										// Register Dialog
										if (is_array($link) and count($link) >= 1)
										{
											foreach($link as $link_key => $link_value)
											{
												$base_module_link = new BaseModuleLink_Access(null);
												if ($base_module_link->create($register_key, $link_value[type], serialize($link_value['array']), $link_value[file], $link_value[weight]) == null)
												{
													if ($transaction_id != null)
													{
														$transaction->rollback($transaction_id);
													}
													throw new ModuleLinkCreationFailedException(null, null);
												}
											}
										}
										
										$module_link_id = BaseModuleFile_Access::get_id_by_module_id_and_name($register_key, "module_link.php");
										if ($module_dialog_id != null)
										{
											$base_module_file = new BaseModuleFile_Access($module_link_id);
											$base_module_file->set_checksum(md5_file($module_link));
										}
										else
										{
											$base_module_file = new BaseModuleFile_Access(null);
											if ($base_module_file->create($register_key, "module_link.php", md5_file($module_link)) == null)
											{
												if ($transaction_id != null)
												{
													$transaction->rollback($transaction_id);
												}
												throw new IncludeProcessFailedException(null, null);
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
									$module_requirements_array[$value] = $required_include;
								}
		
								// Register includes
								$base_module = new BaseModule_Access(null);
								if (($base_module_id = $base_module->create($name, $value, $main_class)) == null)
								{
									if ($transaction_id != null)
									{
										$transaction->rollback($transaction_id);
									}
									throw new ModuleProcessFailedException(null, null);
								}
								else
								{
									$base_module_file = new BaseModuleFile_Access(null);
									if ($base_module_file->create($base_module_id, "module_info.php", md5_file($config_file)) == null)
									{
										if ($transaction_id != null)
										{
											$transaction->rollback($transaction_id);
										}
										throw new ModuleProcessFailedException(null, null);
									}
								}
								
								if ($no_tab != true)
								{
									$position = BaseModuleNavigation_Access::get_highest_position();
									$base_module_navigation = new BaseModuleNavigation_Access(null);
									if ($base_module_navigation->create($tab_name, $tab_colour, $position, $base_module_id) == null)
									{
										if ($transaction_id != null)
										{
											$transaction->rollback($transaction_id);
										}
										throw new ModuleProcessFailedException(null, null);
									}
								}
								
								if ($no_dialog != true)
								{
									include($module_dialog);
									
									$base_module_file = new BaseModuleFile_Access(null);
									if ($base_module_file->create($base_module_id, "module_dialogs.php", md5_file($module_dialog)) == null)
									{
										if ($transaction_id != null)
										{
											$transaction->rollback($transaction_id);
										}
										throw new ModuleProcessFailedException(null, null);
									}
									
									// Register Dialog
									if (is_array($dialog) and count($dialog) >= 1)
									{
										foreach($dialog as $dialog_key => $dialog_value)
										{
											$base_module_dialog = new BaseModuleDialog_Access(null);
											if ($base_module_dialog->create($base_module_id, $dialog_value[type], $dialog_value[class_path], $dialog_value['class'], $dialog_value[method], $dialog_value[internal_name], $dialog_value[display_name], $dialog_value[weight]) == null)
											{
												if ($transaction_id != null)
												{
													$transaction->rollback($transaction_id);
												}
												throw new ModuleDialogCreationFailedException(null, null);
											}
										}
									}
									
									unset($dialog);
								}
								
								if ($no_link != true)
								{
									include($module_link);
									
									$base_module_file = new BaseModuleFile_Access(null);
									if ($base_module_file->create($base_module_id, "module_link.php", md5_file($module_link)) == null)
									{
										if ($transaction_id != null)
										{
											$transaction->rollback($transaction_id);
										}
										throw new ModuleProcessFailedException(null, null);
									}
									
									// Register Dialog
									if (is_array($link) and count($link) >= 1)
									{
										foreach($link as $link_key => $link_value)
										{
											$base_module_link= new BaseModuleLink_Access(null);
											if ($base_module_dialog->create($base_module_id, $link_value[type], serialize($link_value['array']), $link_value[file], $link_value[weight]) == null)
											{
												if ($transaction_id != null)
												{
													$transaction->rollback($transaction_id);
												}
												throw new ModuleLinkCreationFailedException(null, null);
											}
										}
									}
									
									unset($link);
								}
								
								$found_module_array[$register_key] = $value;
							}
						}
					}
				}
				unset($name);
				unset($class);
				unset($no_tab);
				unset($no_dialog);
				unset($no_link);
				unset($tab_name);
				unset($tab_colour);
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
								if ($transaction_id != null)
								{
									$transaction->rollback($transaction_id);
								}
								throw new ModuleRequirementFailedException(null, null);
							}
						}
					}
				}
			}

			// Delete legacy includes
			$legacy_module_array = array_diff($registered_module_array, $found_module_array);
			if (is_array($legacy_module_array) and count($legacy_module_array) >= 1)
			{
				foreach($legacy_module_array as $legacy_key => $legacy_value)
				{
					if (BaseModuleFile_Access::delete_by_module_id($legacy_key) == false)
					{
						if ($transaction_id != null)
						{
							$transaction->rollback($transaction_id);
						}
						throw new ModuleProcessFailedException(null, null);
					}
					
					$base_module_navigation_id = BaseModuleNavigation_Access::get_id_by_module_id($legacy_key);
					$base_module_navigation = new BaseModuleNavigation_Access($base_module_navigation_id);
					
					// Position
					if ($base_module_navigation->get_next_position() != $base_module_navigation_id)
					{
						$tmp_base_module_navigation = $base_module_navigation;
						$tmp_base_module_navigation_id = $base_module_navigation_id;
						$tmp_position = $base_module_navigation->get_position();
						
						while(($next_base_module_navigation_id = $tmp_base_module_navigation->get_next_position()) != $tmp_base_module_navigation_id)
						{
							$next_base_module_navigation = new BaseModuleNavigation_Access($next_base_module_navigation_id);
							$next_position = $next_base_module_navigation->get_position();
							if ($next_base_module_navigation->set_position($tmp_position) == false)
							{
								if ($transaction_id != null)
								{
									$transaction->rollback($transaction_id);
								}
								throw new ModuleProcessFailedException(null, null);
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
						if ($transaction_id != null)
						{
							$transaction->rollback($transaction_id);
						}
						throw new ModuleProcessFailedException(null, null);
					}
					$base_module = new BaseModule_Access($legacy_key);
					if ($base_module->delete() == false)
					{
						if ($transaction_id != null)
						{
							$transaction->rollback($transaction_id);
						}
						throw new IncludeProcessFailedException(null, null);
					}
				}
			}
			
			if ($transaction_id != null)
			{
				$transaction->commit($transaction_id);
			}
		}
		else
		{
			throw new ModuleFolderEmptyException(null, null);
		}
	}

	
	/**
	 * For AJAX Handler only
	 */
	public static function init_db_constants()
	{		
		$registered_include_array = BaseInclude_Access::list_folder_entries();		
		$include_folder_array = scandir($GLOBALS[include_dir]);
		
		if (is_array($include_folder_array) and count($include_folder_array) >= 1)
		{
			foreach($include_folder_array as $key => $value)
			{
				$sub_folder = $GLOBALS[include_dir]."/".$value;
				if (is_dir($sub_folder) and $key > 1)
				{
					$config_folder = $sub_folder."/config";
					if (is_dir($config_folder))
					{
						$config_file = $config_folder."/include_info.php";
						if (is_file($config_file))
						{
							include($config_file);
							
							if ($no_db_table_name != true)
							{
								$db_table_name_file = $config_folder."/db_table_name.php";
								if (is_file($db_table_name_file))
								{
									include($db_table_name_file);
								}
							}
							unset($no_db_table_name);
						}
					}
				}
			}
		}
	}

	public static function get_include_folders()
	{
		return BaseInclude_Access::list_folder_entries();
	}
	
	public static function get_module_folders()
	{
		return BaseModule_Access::list_folder_entries();
	}
	
	public static function get_module_name_by_module_id($module_id)
	{
		return BaseModule_Access::get_module_name_by_module_id($module_id);
	}
	
	public static function get_module_folder_by_module_name($module_name)
	{
		return BaseModule_Access::get_module_folder_by_module_name($module_name);
	}
	
	public static function list_module_navigations_entries()
	{
		return BaseModuleNavigation_Access::list_entries();
	}
	
	public static function list_modules()
	{
		return BaseModule_Access::list_entries();
	}
	
	public static function list_includes()
	{
		return BaseInclude_Access::list_entries();
	}
}
?>