<?php
/**
 * @package data
 * @version 0.4.0.0
 * @author Roman Konertz <konertz@open-lims.org>
<<<<<<< HEAD
 * @copyright (c) 2008-2011 by Roman Konertz
=======
 * @author Roman Quiring <quiring@open-lims.org>
 * @copyright (c) 2008-2011 by Roman Konertz, Roman Quiring
>>>>>>> uploader
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
require_once("../base/ajax.php");

/**
 * File AJAX IO Class
 * @package data
 */
class FileAjax extends Ajax
{
	function __construct()
	{
		parent::__construct();
	}
	

	public static function list_file_items($json_row_array, $json_argument_array, $css_page_id, $css_row_sort_id, $page, $sortvalue, $sortmethod)
	{
		$argument_array = json_decode($json_argument_array);
		
		$handling_class = Item::get_holder_handling_class_by_name($argument_array[0][1]);
		if ($handling_class)
		{
			$sql = $handling_class::get_item_list_sql($argument_array[1][1]);
		}
		
		if ($sql)
		{
			if ($argument_array[2][1] == true)
			{
				$list_request = new ListRequest_IO(Data_Wrapper::count_item_files($sql), $css_page_id, $css_row_sort_id);
				$list_array = Data_Wrapper::list_item_files($sql, $sortvalue, $sortmethod, ($page*20)-20, ($page*20));
			}
			else
			{
				$number_of_entries = Data_Wrapper::count_item_files($sql);
				$list_request = new ListRequest_IO($number_of_entries, $css_page_id, $css_row_sort_id, $number_of_entries, null, false, false);	
				$list_array = Data_Wrapper::list_item_files($sql, $sortvalue, $sortmethod, 0, null);
			}
			$list_request->set_row_array($json_row_array);
					
			if (is_array($list_array) and count($list_array) >= 1)
			{
				foreach($list_array as $key => $value)
				{
					if ($argument_array[3][1] == true)
					{
						$row_array = json_decode($json_row_array);
						if (is_array($row_array) and count($row_array) >= 1)
						{
							foreach ($row_array as $row_key => $row_value)
							{
								if ($row_value[1] == "checkbox")
								{
									if ($row_value[4])
									{
										$checkbox_class = $row_value[4];
										break;
									}
								}
							}
						}
						
						if ($checkbox_class)
						{
							$list_array[$key][checkbox] = "<input type='checkbox' name='file-".$list_array[$key][item_id]."' value='1' class='".$checkbox_class."' />";
						}
						else
						{
							$list_array[$key][checkbox] = "<input type='checkbox' name='file-".$list_array[$key][item_id]."' value='1' />";
						}
					} 
					
					$file = File::get_instance($list_array[$key][id]);
					$list_array[$key][symbol] = "<img src='".$file->get_icon()."' alt='' style='border:0;' />";
					
					$list_array[$key][size] = Misc::calc_size($list_array[$key][size]);
					
					$datetime_handler = new DatetimeHandler($list_array[$key][datetime]);
					$list_array[$key][datetime] = $datetime_handler->get_formatted_string("dS M Y H:i");
				}
			}
			else
			{
				
			}
			
			$list_request->set_array($list_array);
			
			return $list_request->get_page($page);
			
		}
		
	}
	
	
	public function method_handler()
	{
		global $session;
		
		if ($session->is_valid())
		{
			switch($_GET[run]):
	
				case "list_file_items":
					echo $this->list_file_items($_POST[row_array], $_POST[argument_array], $_POST[css_page_id],  $_POST[css_row_sort_id], $_GET[page], $_GET[sortvalue], $_GET[sortmethod]);
				break;
				
				default:
				break;

			endswitch;
		}
	}
}

$file_ajax = new FileAjax;
$file_ajax->method_handler();

?>