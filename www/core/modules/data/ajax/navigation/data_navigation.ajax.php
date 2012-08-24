<?php
/**
 * @package data
 * @version 0.4.0.0
 * @author Roman Konertz <konertz@open-lims.org>
 * @author Roman Quiring <quiring@open-lims.org>
 * @copyright (c) 2008-2012 by Roman Konertz, Roman Quiring
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
 * Data AJAX IO Class
 * @package organisation_unit
 */
class DataNavigationAjax 
{

	public static function get_name()
	{
		echo "Data";
	}
	
	public static function get_html()
	{
		$template = new HTMLTemplate("data/navigation/left.html");
		$template->output();
	}
	
	public static function get_array()
	{
		global $session;

		if ($session->is_value("LEFT_NAVIGATION_DATA_ARRAY"))
		{
			echo json_encode($session->read_value("LEFT_NAVIGATION_DATA_ARRAY"));
		}
		else
		{
			$return_array = array();
									
			$folder = Folder::get_instance(1);
		
			$data_array = $folder->get_subfolder_array();
			
			if (is_array($data_array) and count($data_array) >= 1)
			{
				$counter = 0;
				
				foreach($data_array as $key => $value)
				{
					$folder = Folder::get_instance($value);
				
					$return_array[$counter][0] = 0;
					$return_array[$counter][1] = $value;
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
					
					$paramquery['username'] = $_GET['username'];
					$paramquery['session_id'] = $_GET['session_id'];
					$paramquery['nav'] = "data";
					$paramquery['folder_id'] = $value;
					$params = http_build_query($paramquery, '', '&#38;');
					
					$return_array[$counter][6] = $params; //link
					$return_array[$counter][7] = false; //open
					$return_array[$counter][8] = Data_Wrapper::has_folder_children($value); //has children
					
					$counter++;
				}
			}
			
			echo json_encode($return_array);
		}
	}
	
	/**
	 * @param array $array
	 */
	public static function set_array($array)
	{
		global $session;
		
		$var = json_decode($array);
		if (is_array($var))
		{
			$session->write_value("LEFT_NAVIGATION_DATA_ARRAY", $var, true);
		}
	}
	
	/**
	 * @param integer $id
	 */
	public static function get_children($id)
	{
		if (is_numeric($id) and $id != 0)
		{
			$return_array = array();

			$folder = Folder::get_instance($id);
					
			$folder_array = $folder->get_subfolder_array();
			
			if (is_array($folder_array) and count($folder_array) >= 1)
			{
				$counter = 0;
				
				foreach($folder_array as $key => $value)
				{
		
					$folder = Folder::get_instance($value);
					
					$return_array[$counter][0] = -1;
					$return_array[$counter][1] = $value;
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
					
					$paramquery['username'] = $_GET['username'];
					$paramquery['session_id'] = $_GET['session_id'];
					$paramquery['nav'] = "data";
					$paramquery['folder_id'] = $value;
					$params = http_build_query($paramquery, '', '&#38;');
					
					$return_array[$counter][6] = $params; //link
					$return_array[$counter][7] = false; //open
					$return_array[$counter][8] = Data_Wrapper::has_folder_children($value); //has children
					$counter++;
					
				}
			}
			echo json_encode($return_array);
		}
	}
}

?>