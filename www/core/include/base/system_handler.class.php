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

	require_once("events/include_delete_event.class.php");
	
	define("BASE_EVENT_LISTENER_TABLE"	, "core_base_event_listeners");
	define("BASE_INCLUDE_FILE_TABLE"	, "core_base_include_files");
	define("BASE_INCLUDE_TABLE"			, "core_base_includes");
	define("BASE_MODULE_FILE_TABLE"		, "core_base_module_files");
	define("BASE_MODULE_TABLE"			, "core_base_modules");
	
	require_once("access/base_event_listener.access.php");
	require_once("access/base_include_file.access.php");
	require_once("access/base_include.access.php");
	require_once("access/base_module_file.access.php");
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
		
		// Open Table Handlers
		
		// Scan Modules
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
											$base_inclide_file = new BaseIncludeFile_Access($class_event_listener_id);
											$base_inclide_file->set_checksum(md5_file($class_event_listener));
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
	 * @todo implementation
	 * @todo use falg for navigation
	 */
	private function scan_modules()
	{

	}

	public static function get_include_folders()
	{
		return BaseInclude_Access::list_folder_entries();
	}
}
?>