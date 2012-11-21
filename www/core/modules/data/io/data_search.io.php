<?php
/**
 * @package data
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
 * Data Search IO Class
 * @package data
 */
class DataSearchIO
{
	/**
	 * @param integer $language_id
	 * @return string
	 */
	public static function get_description($language_id)
	{
		return "Finds Files, Values or Folders in Folders via Name or Extension.";
	}
	
	/**
	 * @return string
	 */
	public static function get_icon()
	{
		return "images/icons_large/ffv_search_50.png";
	}
	
	public static function search()
	{
		global $user, $session;
		
		if ($_GET['nextpage'])
		{
			if ($_GET['sortvalue'] and $_GET['sortmethod'])
			{
				if ($_GET['nextpage'] == "2" and $_POST['name'])
				{
					$name = $_POST['name'];
					$folder_id = $session->read_value("SEARCH_FFV_FOLDER_ID");
				}
				else
				{
					$name = $session->read_value("SEARCH_FFV_NAME");
					$folder_id = $session->read_value("SEARCH_FFV_FOLDER_ID");
				}
			}
			else
			{
				if ($_GET['page'])
				{
					$name = $session->read_value("SEARCH_FFV_NAME");
					$folder_id = $session->read_value("SEARCH_FFV_FOLDER_ID");
				}
				else
				{
					if ($_GET['nextpage'] == "1")
					{
						$name = $_POST['name'];
						if ($_POST['folder_id'])
						{
							$folder_id = $_POST['folder_id'];
						}
						else
						{
							$folder_id = UserFolder::get_folder_by_user_id($user->get_user_id());
						}
						$session->delete_value("SEARCH_FFV_NAME");
						$session->delete_value("SEARCH_FFV_FOLDER_ID");
					}
					else
					{
						$name = $_POST['name'];
						$folder_id = $session->read_value("SEARCH_FFV_FOLDER_ID");
					}
				}
			}
			$no_error = true;
		}
		else
		{
			$no_error = false;
		}
		
		if ($no_error == false)
		{
			$template = new HTMLTemplate("data/search/ffv_search.html");
			
			$paramquery = $_GET;
			unset($paramquery['page']);
			$paramquery['nextpage'] = "1";
			$params = http_build_query($paramquery,'','&#38;');
					
			$template->set_var("params",$params);
			
			$template->set_var("error", "");
			
			$template->output();
		}
		else
		{
			if (!$folder_id)
			{
				$folder_id = $_POST['folder_id'];
			}

			$session->write_value("SEARCH_FFV_NAME", $name, true);
			$session->write_value("SEARCH_FFV_FOLDER_ID", $folder_id, true);

			$argument_array = array();
			$argument_array[0][0] = "folder_id";
			$argument_array[0][1] = $folder_id;
			$argument_array[1][0] = "name";
			$argument_array[1][1] = $name;
					
			$list = new List_IO("DataSearch", "ajax.php?nav=data", "search_data_list_data", "search_data_count_data", $argument_array, "DataSearch");
		
			$list->add_column("", "symbol", false, "16px");
			$list->add_column("Name", "name", true, null);
			$list->add_column("Type", "type", false, null);
			$list->add_column("Version", "version", false, null);
			$list->add_column("Datetime", "datetime", true, null);
			$list->add_column("Size", "size", true, null);
			$list->add_column("Owner", "owner", true, null);
			$list->add_column("Permission", "permission", false, null);
			
			$folder = Folder::get_instance($folder_id);
			
			$template = new HTMLTemplate("data/search/ffv_search_result.html");
		
			$paramquery = $_GET;
			$paramquery['nextpage'] = "2";
			$params = http_build_query($paramquery,'','&#38;');
			
			$template->set_var("params", $params);
			
			$template->set_var("name", $name);
			$template->set_var("folder", $folder->get_name());
				
			$template->set_var("list", $list->get_list());	
	
			$template->output();
		}
	}
}
?>