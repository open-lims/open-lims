<?php
/**
 * @package base
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
 * Base Include Admin IO Class
 * @package base
 */
class AdminBaseNavigationIO
{
	public static function home()
	{
		$list = new ListStat_IO((Base_Wrapper::count_base_module_navigation()+1), 20);
		
		$list->add_row("Displayed-Name (en-GB)", "name", false, null);
		$list->add_row("Module", "module", false, null);
		$list->add_row("Colour", "colour", false, null);
		$list->add_row("UW", "uw", false, null);
		$list->add_row("DW", "dw", false, null);
		$list->add_row("Show/Hide", "hide", false, "80px");

		$home_array = array();
		$home_array[name] = "Home";
		$home_array[module] = "base";
		$home_array[colour] = "blue";
		$home_array[hidden] = "f";

		if ($_GET[page])
		{
			$result_array = Base_Wrapper::list_base_module_navigation(($_GET[page]*20)-20, ($_GET[page]*20));			
		}
		else
		{
			$result_array = Base_Wrapper::list_base_module_navigation(0, 20);		
		}
		
		array_unshift($result_array, $home_array);
		
		if (is_array($result_array) and count($result_array) >= 1)
		{		
			foreach($result_array as $key => $value)
			{	
				if ($result_array[$key][module] != "base")
				{
					$paramquery = $_GET;
					$paramquery[id] = $result_array[$key][id];
					$paramquery[action] = "hide";
					unset($paramquery[sortvalue]);
					unset($paramquery[sortmethod]);
					unset($paramquery[nextpage]);
					$params = http_build_query($paramquery, '', '&#38;');
	
					$result_array[$key][hide][link] = $params;
					
					if ($result_array[$key][hidden] == 't')
					{
						$result_array[$key][hide][content] = "<img src='images/icons/grey_point.png' alt='hide' style='border: 0;' />";
					}
					else
					{
						$result_array[$key][hide][content] = "<img src='images/icons/green_point.png' alt='hide' style='border: 0;' />";
					}
					
					if ($result_array[$key][position] != 1)
					{
						$paramquery = $_GET;
						$paramquery[id] = $result_array[$key][id];
						$paramquery[action] = "upwards";
						unset($paramquery[sortvalue]);
						unset($paramquery[sortmethod]);
						unset($paramquery[nextpage]);
						$params = http_build_query($paramquery, '', '&#38;');
		
						$result_array[$key][uw][link] = $params;
						$result_array[$key][uw][content] = "<img src='images/icons/upward.png' alt='uw' style='border: 0;' />";
					}
					else
					{
						$result_array[$key][uw] = "<img src='images/icons/upward_na.png' alt='uw' style='border: 0;' />";
					}
					
					if ($result_array[$key][position] != ModuleNavigation::get_highest_position())
					{
						$paramquery = $_GET;
						$paramquery[id] = $result_array[$key][id];
						$paramquery[action] = "downwards";
						unset($paramquery[sortvalue]);
						unset($paramquery[sortmethod]);
						unset($paramquery[nextpage]);
						$params = http_build_query($paramquery, '', '&#38;');
		
						$result_array[$key][dw][link] = $params;
						$result_array[$key][dw][content] = "<img src='images/icons/downward.png' alt='dw' style='border: 0;' />";
					}
					else
					{
						$result_array[$key][dw] = "<img src='images/icons/downward_na.png' alt='dw' style='border: 0;' />";
					}
				}
				else
				{
					$result_array[$key][hide] = "<img src='images/icons/green_point.png' alt='hide' style='border: 0;' />";
					$result_array[$key][uw] = "<img src='images/icons/upward_na.png' alt='uw' style='border: 0;' />";
					$result_array[$key][dw] = "<img src='images/icons/downward_na.png' alt='dw' style='border: 0;' />";
				}
			}
		}
		else
		{
			$list->override_last_line("<span class='italic'>No results found!</span>");
		}
		
		$template = new Template("template/base/admin/base_navigation/list.html");

		$template->set_var("table", $list->get_list($result_array, $_GET[page]));
		
		$template->output();
	}
	
	/**
	 * @todo Error
	 */
	public static function hide()
	{
		if ($_GET[id])
		{
			$module_navigation = new ModuleNavigation($_GET[id]);
				
			$paramquery = $_GET;
			unset($paramquery[action]);
			unset($paramquery[id]);
			$params = http_build_query($paramquery,'','&#38;');
			
			if ($module_navigation->hide())
			{
				Common_IO::step_proceed($params, "Hide/Show", "Operation Successful", null);
			}
			else
			{
				Common_IO::step_proceed($params, "Hide/Show", "Operation Failed" ,null);	
			}
		}
		else
		{
			
		}
	}
	
	/**
	 * @todo Error
	 */
	public static function upwards()
	{
		if ($_GET[id])
		{
			$module_navigation = new ModuleNavigation($_GET[id]);
				
			$paramquery = $_GET;
			unset($paramquery[action]);
			unset($paramquery[id]);
			$params = http_build_query($paramquery,'','&#38;');
			
			if ($module_navigation->upwards())
			{
				Common_IO::step_proceed($params, "Upwards", "Operation Successful", null);
			}
			else
			{
				Common_IO::step_proceed($params, "Upwards", "Operation Failed" ,null);	
			}
		}
		else
		{
			
		}
	}
	
	/**
	 * @todo Error
	 */
	public static function downwards()
	{
		if ($_GET[id])
		{
			$module_navigation = new ModuleNavigation($_GET[id]);
				
			$paramquery = $_GET;
			unset($paramquery[action]);
			unset($paramquery[id]);
			$params = http_build_query($paramquery,'','&#38;');
			
			if ($module_navigation->downwards())
			{
				Common_IO::step_proceed($params, "Downwards", "Operation Successful", null);
			}
			else
			{
				Common_IO::step_proceed($params, "Downwards", "Operation Failed" ,null);	
			}
		}
		else
		{
			
		}
	}
	
	public static function handler()
	{
		switch($_GET[action]):
			case "upwards":
				self::upwards();
			break;	
		
			case "downwards":
				self::downwards();
			break;
			
			case "hide":
				self::hide();
			break;
		
			default:
				self::home();
			break;
		endswitch;
	}
}