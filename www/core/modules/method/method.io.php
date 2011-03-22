<?php
/**
 * @package equipment
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
 * Equipment IO Class
 * @package equipment
 */
class MethodIO
{
	/**
	 * @todo types of status or type-id-array
	 */
	public static function add_method_item()
	{
		global $user, $project_security;
		
		if ($_GET[nextpage] == "2")
		{
			if (!is_numeric($_POST[type_id]))
			{
				$page_2_passed = false;
			}
			else
			{
				$page_2_passed = true;
			}
		}
		else
		{
			$page_2_passed = false;
		}
		
		if ($page_2_passed == false)
		{
			$method_array = MethodType::list_entries();
		
			$template = new Template("languages/en-gb/template/equipment/add.html");
			
			$paramquery = $_GET;
			$paramquery[nextpage] = 2;
			$params = http_build_query($paramquery,'','&#38;');
			
			$template->set_var("params",$params);
			
			$result = array();
			$counter = 0;
			
			if (is_array($method_array) and count($method_array) >= 1)
			{
				foreach($method_array as $key => $value)
				{
					$method_type = new MethodType($value);
					
					$result[$counter][value] = $value;
					$result[$counter][content] = $method_type->get_name()." (".$method_type->get_cat_name().")";
					
					$counter++;
				}
			}
			else
			{
				$result[0][value] = "0";
				$result[0][content] = "NO METHOD FOUND!";	
			}
			
			$template->set_var("select",$result);
			
			$template->set_var("keywords", $_POST[keywords]);
			$template->set_var("description", $_POST[description]);
			
			$template->output();
		}
		else
		{
			$method = new Method($_POST[method_id]);

			$method_add_successful = $method->create($_POST[type_id], $user->get_user_id());

			if ($method_add_successful)
			{
				return $method->get_item_id();
			}
			else
			{
				return false;
			}
		}
		return null;
	}
	
	/**
	 * @todo error on missing $sql
	 * @param string $sql
	 */
	public static function list_method_items($sql)
	{
		if ($sql)
		{
			$list = new List_IO(Equipment_Wrapper::count_item_equipments($sql), 20);

			$list->add_row("","symbol",false,16);
			$list->add_row("Equipment Name","name",true,null);
			$list->add_row("Category","category",true,null);
			$list->add_row("Date/Time","datetime",true,null);
			
			if ($_GET[page])
			{
				if ($_GET[sortvalue] and $_GET[sortmethod])
				{
					$result_array = Equipment_Wrapper::list_item_equipments($sql, $_GET[sortvalue], $_GET[sortmethod], ($_GET[page]*20)-20, ($_GET[page]*20));
				}
				else
				{
					$result_array = Equipment_Wrapper::list_item_equipments($sql, null, null, ($_GET[page]*20)-20, ($_GET[page]*20));
				}				
			}
			else
			{
				if ($_GET[sortvalue] and $_GET[sortmethod])
				{
					$result_array = Equipment_Wrapper::list_item_equipments($sql, $_GET[sortvalue], $_GET[sortmethod], 0, 20);
				}
				else
				{
					$result_array = Equipment_Wrapper::list_item_equipments($sql, null, null, 0, 20);
				}	
			}
			
			if (is_array($result_array) and count($result_array) >= 1)
			{
				foreach($result_array as $key => $value)
				{
					$datetime_handler = new DatetimeHandler($result_array[$key][datetime]);
					$result_array[$key][datetime] = $datetime_handler->get_formatted_string("dS M Y H:i");

					$result_array[$key][symbol]	= "<img src='images/icons/method.png' alt='N' border='0' />";
				}
			}
			else
			{
				$list->override_last_line("<span class='italic'>No results found!</span>");
			}
			
			$template = new Template("languages/en-gb/template/equipment/list.html");

			$template->set_var("table", $list->get_list($result_array, $_GET[page]));
			
			$template->output();
		}
		else
		{
			// Error
		}
	}	
}
?>

