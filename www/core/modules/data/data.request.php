<?php
/**
 * @package data
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
 * Data Request Class
 * @package data
 */
class DataRequest
{
	public static function ajax_handler()
	{
		switch($_GET[run]):
			
			case "list_data_browser":
				require_once("ajax/data.ajax.php");
				echo DataAjax::list_data_browser($_POST[column_array], $_POST[argument_array], $_POST[get_array], $_POST[css_page_id],  $_POST[css_row_sort_id], $_POST[entries_per_page], $_GET[page], $_GET[sortvalue], $_GET[sortmethod]);
			break;
			
			case "count_data_browser":
				require_once("ajax/data.ajax.php");
				echo DataAjax::count_data_browser($_POST[argument_array]);
			break;
			
		endswitch;
	}
	
	public static function io_handler()
	{
		switch($_GET[action]):
			case("permission"):
				require_once("io/data.io.php");
				DataIO::permission();
			break;
			
			case("chown"):
				require_once("io/data.io.php");
				DataIO::change_owner();
			break;
			
			case("chgroup"):
				require_once("io/data.io.php");
				DataIO::change_group();
			break;

			case("image_browser_detail"):
				require_once("io/data.io.php");
				DataIO::image_browser_detail();
			break;
			
			case("image_browser_multi"):
				require_once("io/data.io.php");
				DataIO::image_browser_multi();
			break;

			
			case("value_detail"):
				require_once("io/value.io.php");
				ValueIO::detail();
			break;
			
			case("value_history"):
				require_once("io/value.io.php");
				ValueIO::history();
			break;
				
			case("value_delete_version"):
				require_once("io/value.io.php");
				ValueIO::delete_version();
			break;

			
			case("file_add"):
				require_once("io/file.io.php");
				FileIO::upload();
			break;
			
			case("file_update"):
			case("file_update_minor"):
				require_once("io/file.io.php");
				FileIO::update();
			break;

			case("file_detail"):
				require_once("io/file.io.php");
				FileIO::detail();
			break;
			
			case("file_history"):
				require_once("io/file.io.php");
				FileIO::history();
			break;
			
			case("file_delete"):
				require_once("io/file.io.php");
				FileIO::delete();
			break;
			
			case("file_delete_version"):
				require_once("io/file.io.php");
				FileIO::delete_version();
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
			
			default:
				require_once("io/data.io.php");
				DataIO::browser();
			break;
			
		endswitch;	
	}
}
?>