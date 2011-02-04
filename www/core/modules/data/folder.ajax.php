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
 */
 
/**
 * 
 */
require_once("../base/ajax_init.php");

/**
 * Folder AJAX Class
 * @package data
 */
class FolderAJAX extends AJAXInit
{
	function __construct()
	{
		parent::__construct();
	}
		
	private function list_childs_by_id()
	{
		if ($_GET[folder_id]) {

			$return_array = array();

			$folder = Folder::get_instance($_GET[folder_id]);
			
			$folder_array = $folder->get_subfolder_array();
			
			if (is_array($folder_array) and count($folder_array) >= 1)
			{
				$counter = 0;
				
				foreach($folder_array as $key => $value)
				{
					if ($value[type] == 0)
					{
						$folder = Folder::get_instance($value[id]);
						
						$return_array[$counter][0] = -1;
						$return_array[$counter][1] = $value[id];
						$return_array[$counter][2] = $folder->get_name();
						$return_array[$counter][3] = "folder.png";
						
						if ($folder->is_read_access() == true)
						{
							$return_array[$counter][4] = true;
						}
						else
						{
							$return_array[$counter][4] = false;	
						}
						
						$return_array[$counter][5] = true; // Clickable
						
						$counter++;
					}	
				}	
			}
			return serialize($return_array);	
		}		
	}

	private function list_menu_childs_by_id()
	{
		if ($_GET[session_id])
		{
			$session = new Session($_GET[session_id]);

			if ($session->is_valid())
			{		
				if ($_GET[folder_id] == 1)
				{
					
					if ($session->is_value("CURRENT_NAVIGATION_FOLDER"))
					{	
						return serialize($session->read_value("CURRENT_NAVIGATION_FOLDER"));			
					}
					else
					{
						$return_array = array();
						
						$folder = Folder::get_instance(1);
					
						$folder_array = $folder->get_subfolder_array();
						
						if (is_array($folder_array) and count($folder_array) >= 1)
						{
							$counter = 0;
							
							foreach($folder_array as $key => $value)
							{
								if ($value[type] == 0)
								{
									$folder = Folder::get_instance($value[id]);
									
									$return_array[$counter][0] = 0;
									$return_array[$counter][1] = $value[id];
									$return_array[$counter][2] = $folder->get_name();
									$return_array[$counter][3] = "folder.png";
									
									if ($folder->is_read_access() == true)
									{
										$return_array[$counter][4] = true;
									}
									else
									{
										$return_array[$counter][4] = false;	
									}
									
									$return_array[$counter][5] = true; // Clickable
									
									$counter++;
								}	
							}
						}
						return serialize($return_array);	
					}
				}
				else
				{
					if ($_GET[folder_id])
					{
						$return_array = array();
						
						$folder = Folder::get_instance($_GET[folder_id]);
						
						$folder_array = $folder->get_subfolder_array();
						
						if (is_array($folder_array) and count($folder_array) >= 1)
						{
							$counter = 0;
							
							foreach($folder_array as $key => $value)
							{
								if ($value[type] == 0)
								{
									$folder = Folder::get_instance($value[id]);
									
									$return_array[$counter][0] = -1;
									$return_array[$counter][1] = $value[id];
									$return_array[$counter][2] = $folder->get_name();
									$return_array[$counter][3] = "folder.png";
									
									if ($folder->is_read_access() == true)
									{
										$return_array[$counter][4] = true;
									}
									else
									{
										$return_array[$counter][4] = false;	
									}
									
									$return_array[$counter][5] = true; // Clickable
									
									$counter++;
								}
							}
						}
						return serialize($return_array);	
					}
				}
			}	
		}
	}
	
	private function rewrite_menu_childs_array()
	{
		if ($_GET[session_id])
		{
			$session = new Session($_GET[session_id]);
		
			if ($_POST[serialized_folder_array])
			{
				$serialized_folder_array = $_POST[serialized_folder_array];
				
				$serialized_folder_array = stripslashes($serialized_folder_array);
				$folder_array = unserialize($serialized_folder_array);

				$session->write_value("CURRENT_NAVIGATION_FOLDER", $folder_array, true);
			}
		}
	}

	public function method_handler()
	{
		switch($_GET[run]):
			
			case "list_folder_childs":
				echo $this->list_childs_by_id();
			break;
			
			case "list_menu_folder_childs":
				echo $this->list_menu_childs_by_id();
			break;
			
			case "rewrite_menu_childs_array":
				echo $this->rewrite_menu_childs_array();
			break;
			
		endswitch;
	}

}

$folder_ajax = new FolderAJAX;
$folder_ajax->method_handler();

?>
