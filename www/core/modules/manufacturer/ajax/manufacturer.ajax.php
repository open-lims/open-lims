<?php
/**
 * @package manufacturer
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
 * Manufacturer AJAX IO Class
 * @package manufacturer
 */
class ManufacturerAjax
{
	/**
	 * @param string $name
	 */
	public static function exist_name($name)
	{
		if (Manufacturer::exist_name($name))
		{
			echo 1;
		}
		else
		{
			echo 0;
		}
	}
	
	/**
	 * @param string $name
	 */
	public static function add_entry($name)
	{
		if ($name)
		{
			$manufacturer = new Manufacturer(null);
			if ($manufacturer->create($name) == true)
			{
				echo 1;
			}
			else
			{
				echo 0;
			}
		}
		else
		{
			echo 0;
		}
	}
	
	/**
	 * @param string $string
	 */
	public static function get_number_of_entries($string)
	{
		echo Manufacturer::count_entries($string);
	}
	
	/**
	 * @param integer $id
	 * @throws ManufacturerIDMissingException
	 */
	public static function get_name($id)
	{
		if (is_numeric($id))
		{
			$manufacturer = new Manufacturer($id);
			echo $manufacturer->get_name();
		}
		else
		{
			throw new ManufacturerIDMissingException();
		}
	}
	
	/**
	 * @param integer $number_of_entries
	 * @param integer $start_entry
	 * @param string $start_string
	 */
	public static function get_next_entries($number_of_entries, $start_entry, $start_string)
	{
		$manufacturer_array = Manufacturer::list_manufacturers($number_of_entries, $start_entry, $start_string);
				
		if (is_array($manufacturer_array) and count($manufacturer_array) >= 1)
		{
			$content_array = array();
			$counter = 0;
			
			$template = new HTMLTemplate("manufacturer/ajax/dialog_list.html");
		
			foreach($manufacturer_array as $key => $value)
			{
				if ($counter%2)
				{
					$content_array[$counter][style] = "background-color: white;";
				}
				else
				{
					$content_array[$counter][style] = "background-color: #D0D0D0;";	
				}
				$content_array[$counter][id] = $value[id];
				$content_array[$counter][name] = $value[name];
				$counter++;
			}

			$template->set_var("manufacturer", $content_array);
			$template->output();
		}
	}
	
	/**
	 * @param integer $page
	 * @param string $sortvalue
	 * @param string $sortmethod
	 */
	public static function list_manufacturers($json_column_array, $argument_array, $css_page_id, $css_row_sort_id, $entries_per_page, $page, $sortvalue, $sortmethod)
	{
		global $user;
		
		$list_request = new ListRequest_IO();
		$list_request->set_column_array($json_column_array);
		
		if (!is_numeric($entries_per_page) or $entries_per_page < 1)
		{
			$entries_per_page = 20;
		}
		
		$list_array = Manufacturer_Wrapper::list_manufacturers($sortvalue, $sortmethod, ($page*$entries_per_page)-$entries_per_page, ($page*$entries_per_page));
				
		if (is_array($list_array) and count($list_array) >= 1)
		{
			if ($user->is_admin() == true)
			{
				$is_admin = true;
			}
			else
			{
				$is_admin = false;
			}
			
			foreach($list_array as $key => $value)
			{
				$list_array[$key][symbol] = "<img src='images/icons/manufacturer.png' alt='' />";
				
				$user = new User($list_array[$key][user_id]);
				$list_array[$key][user] = $user->get_full_name(false);
				
				if ($is_admin == true)
				{
					$list_array[$key][delete] = "<a href='#' class='ManufacturerListDelete' id='ManufacturerListDelete".$list_array[$key][id]."'><img src='images/icons/delete.png' alt='' style='border: 0;' /></a>";
				}
			}
		}
		else
		{
			$list_request->empty_message("<span class='italic'>No results found!</span>");
		}
		
		$list_request->set_array($list_array);
		
		return $list_request->get_page($page);
	}
	
	/**
	 * @return integer
	 */
	public static function count_manufacturers()
	{
		return Manufacturer_Wrapper::count_manufacturers();
	}
	
	/**
	 * @param integer $id
	 * @return string
	 * @throws ManufacturerIDMissingException
	 */
	public static function delete($id)
	{
		global $user;
		
		if (is_numeric($id) and $user->is_admin() == true)
		{
			$manufacturer = new Manufacturer($id);
			if ($manufacturer->delete() == true)
			{
				echo 1;
			}
			else
			{
				echo 0;
			}
		}
		else
		{
			throw new ManufacturerIDMissingException();
		}
	}
}
?>