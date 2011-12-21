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
 * Base Module Admin IO Class
 * @package base
 */
class AdminBaseModuleIO
{
	public static function home()
	{
		$list = new ListStat_IO(System_Wrapper::count_base_module(), 20);
		
		$list->add_column("Name", "name", true, null);
		$list->add_column("Dialogs", "dialogs", true, null);
		$list->add_column("Links", "links", true, null);
		$list->add_column("Disable", "disable", false, "80px");
		
		if ($_GET[page])
		{
			if ($_GET[sortvalue] and $_GET[sortmethod])
			{
				$result_array = System_Wrapper::list_base_module($_GET[sortvalue], $_GET[sortmethod], ($_GET[page]*20)-20, ($_GET[page]*20));
			}
			else
			{
				$result_array = System_Wrapper::list_base_module(null, null, ($_GET[page]*20)-20, ($_GET[page]*20));
			}				
		}
		else
		{
			if ($_GET[sortvalue] and $_GET[sortmethod])
			{
				$result_array = System_Wrapper::list_base_module($_GET[sortvalue], $_GET[sortmethod], 0, 20);
			}
			else
			{
				$result_array = System_Wrapper::list_base_module(null, null, 0, 20);
			}	
		}
		
		if (is_array($result_array) and count($result_array) >= 1)
		{		
			foreach($result_array as $key => $value)
			{	
				if ($result_array[$key][name] != "base")
				{
					$paramquery = $_GET;
					$paramquery[id] = $result_array[$key][id];
					$paramquery[action] = "disable";
					unset($paramquery[sortvalue]);
					unset($paramquery[sortmethod]);
					unset($paramquery[nextpage]);
					$params = http_build_query($paramquery, '', '&#38;');
	
					$result_array[$key][disable][link] = $params;
					
					if ($result_array[$key][disabled] == 't')
					{
						$result_array[$key][disable][content] = "<img src='images/icons/grey_point.png' alt='hide' style='border: 0;' />";
					}
					else
					{
						$result_array[$key][disable][content] = "<img src='images/icons/green_point.png' alt='hide' style='border: 0;' />";
					}					
				}
				else
				{
					$result_array[$key][disable] = "<img src='images/icons/green_point.png' alt='hide' style='border: 0;' />";
				}
			}
		}
		else
		{
			$list->override_last_line("<span class='italic'>No results found!</span>");
		}
		
		$template = new HTMLTemplate("base/admin/base_module/list.html");

		$template->set_var("table", $list->get_list($result_array, $_GET[page]));
		
		$template->output();
	}
	
	public static function disable()
	{
		if ($_GET[id])
		{				
			$paramquery = $_GET;
			unset($paramquery[action]);
			unset($paramquery[id]);
			$params = http_build_query($paramquery,'','&#38;');
			
			if (SystemHandler::disable_module($_GET[id]))
			{
				Common_IO::step_proceed($params, "Disable", "Operation Successful", null);
			}
			else
			{
				Common_IO::step_proceed($params, "Disable", "Operation Failed" ,null);	
			}
		}
		else
		{
			
		}
	}
	
	public static function handler()
	{
		switch($_GET[action]):
			case "disable":
				self::disable();
			break;	
		
			default:
				self::home();
			break;
		endswitch;
	}
}