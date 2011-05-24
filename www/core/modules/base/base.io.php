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
 * Base IO Class
 * @package base
 */
class BaseIO
{	
	public static function list_system_messages()
	{
		define(SYSTEM_MESSAGE_ENTRIES_PER_PAGE, 6);
		
		$system_message_array = SystemMessage::list_entries();
	
		if (!$_GET[page])
    	{
			$page = 1;
		}
		else
		{
			$page = $_GET[page];	
		}
	
		$entry_count = count($system_message_array);
		$number_of_pages = ceil($entry_count/SYSTEM_MESSAGE_ENTRIES_PER_PAGE);
		
		$template = new Template("languages/en-gb/template/base/list_system_messages.html");	
		
		if (is_array($system_message_array) and count($system_message_array) >= 1)
		{
			$template->set_var("no_entry",false);
			
			$result = array();
			$counter = 0;
			
			if (count($system_message_array) < ($page*SYSTEM_MESSAGE_ENTRIES_PER_PAGE))
			{
				$max_for = (count($system_message_array) % SYSTEM_MESSAGE_ENTRIES_PER_PAGE) - 1;
			}
			else
			{
				$max_for = SYSTEM_MESSAGE_ENTRIES_PER_PAGE-1;
			}
						
			for ($i=0;$i<=$max_for;$i++)
			{
				$entry = ($page*SYSTEM_MESSAGE_ENTRIES_PER_PAGE)+$i-SYSTEM_MESSAGE_ENTRIES_PER_PAGE; // Erzeugt Entry-ID
				$value = $system_message_array[$entry];
				
				$system_message 	= new SystemMessage($value);
				$user 				= new User($system_message->get_user_id());
				$datetime_handler 	= new DatetimeHandler($system_message->get_datetime());
				
				$content = str_replace("\n", "<br />", $system_message->get_content());
				$content = str_replace("\\", "", $content);
				
				$result[$counter][user] = $user->get_full_name(false);
				$result[$counter][datetime] = $datetime_handler->get_formatted_string("dS M Y \\a\\t H:i");
				$result[$counter][content] = $content;
				
				$counter++;
			}
			$template->set_var("message_array", $result);
		}
		else
		{
			$template->set_var("no_entry",true);
		}
		
		if ($number_of_pages > 1)
		{
			$template->set_var("page_bar",Common_IO::page_bar($page, $number_of_pages, $_GET));
		}
		else
		{
			$template->set_var("page_bar","");
		}
		
		$template->output();
		
	}
	
	public static function system_info()
	{
		$template = new Template("languages/en-gb/template/base/system_info.html");
		
		$template->set_var("product", constant("PRODUCT"));
		$template->set_var("product_version", constant("PRODUCT_VERSION"));
		$template->set_var("product_user", constant("PRODUCT_USER"));
		$template->set_var("product_function", constant("PRODUCT_FUNCTION"));

		$paramquery = $_GET;
		$paramquery[run] = "software_info";
		$params = http_build_query($paramquery, '', '&#38;');
		
		$template->set_var("sw_info_params", $params);
		
		$paramquery = $_GET;
		$paramquery[run] = "license";
		$params = http_build_query($paramquery, '', '&#38;');
		
		$template->set_var("license_params", $params);
		
		$include_array = SystemHandler::list_includes();
		
		if (is_array($include_array) and count($include_array) >= 1)
		{
			$include_string = null;
			
			foreach($include_array as $key => $value)
			{
				if (!$include_string)
				{
					$include_string = $value[name];
				}
				else
				{
					$include_string = $include_string.", ".$value[name];
				}
			}
			
			$template->set_var("includes", $include_string);
		}
		else
		{
			$template->set_var("includes", "<span class='italic'>none</span>");
		}
		
		$module_array = SystemHandler::list_modules();
		
		if (is_array($module_array) and count($module_array) >= 1)
		{
			$module_string = null;
			
			foreach($module_array as $key => $value)
			{
				if (!$module_string)
				{
					$module_string = $value[name];
				}
				else
				{
					$module_string = $module_string.", ".$value[name];
				}
			}
			
			$template->set_var("modules", $module_string);
		}
		else
		{
			$template->set_var("modules", "<span class='italic'>none</span>");
		}
		
		$template->output();
	}
	
	public static function software_info()
	{
		$template = new Template("languages/en-gb/template/base/software_info.html");
		
		$template->set_var("product", constant("PRODUCT"));
		$template->set_var("product_version", constant("PRODUCT_VERSION"));
		
		$paramquery = $_GET;
		$paramquery[run] = "license";
		$params = http_build_query($paramquery, '', '&#38;');
		
		$template->set_var("license_params", $params);
		
		$template->output();
	}
	
	public static function license()
	{
		$template = new Template("languages/en-gb/template/base/license.html");
		$template->output();
	}
	
	public static function method_handler()
	{
		switch ($_GET[run]):
		
			case "myorgan":
				require_once("core/modules/organisation_unit/organisation_unit.io.php");
				$organisation_unit_io = new OrganisationUnitIO();
				$organisation_unit_io->list_user_related_organisation_units();
			break;
			
			case "sysmsg":
				self::list_system_messages();
			break;
			
			case "system_info":
				self::system_info();
			break;
			
			case "software_info":
				self::software_info();
			break;
			
			case "license":
				self::license();
			break;
			
		endswitch;
	}
	
}

?>
