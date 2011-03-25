<?php
/**
 * @package sample
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
 * Sample Common IO Class
 * @package sample
 */
class SampleCommon_IO
{
	public static function tab_header()
	{			
		$template = new Template("languages/en-gb/template/samples/tabs/small_tab_header.html");
		$template->output();
		
		if ($_GET[nav] == "sample")
		{
			switch ($_GET[run]):				
				case "parents":
					$current_tab = 2;
				break;
								
				default:
					$current_tab = 1;
				break;
			endswitch;
		}
		elseif ($_GET[nav] == "data" or
				 $_GET[nav] == "value" or
				 $_GET[nav] == "file" or
				 $_GET[nav] == "folder")
		{
			$current_tab = 5;	
		}
		elseif ($_GET[nav] == "method")
		{
			$current_tab = 4;	
		}
		else
		{
			$current_tab = 0;
		}

		// Main Page
		$paramquery[username] 	= $_GET[username];
		$paramquery[session_id] = $_GET[session_id];
		$paramquery[nav]		= "sample";
		$paramquery[run]		= "detail";
		$paramquery[sample_id]	= $_GET[sample_id];
		$params 				= http_build_query($paramquery,'','&#38;');
		unset($paramquery);
		
		if ($current_tab == 1)
		{ 
			$template = new Template("languages/en-gb/template/samples/tabs/generic_active.html");
			$template->set_var("title", "Main Page");
			$template->set_var("params", $params);
			$template->output();
		}
		else
		{
			$template = new Template("languages/en-gb/template/samples/tabs/generic.html");
			$template->set_var("title", "Main Page");
			$template->set_var("params", $params);
			$template->output();
		}
				
		// Parent Samples
		
		$paramquery[username] 	= $_GET[username];
		$paramquery[session_id] = $_GET[session_id];
		$paramquery[nav]		= "sample";
		$paramquery[run]		= "parents";
		$paramquery[sample_id]	= $_GET[sample_id];
		$params 				= http_build_query($paramquery,'','&#38;');
		unset($paramquery);
		
		if ($current_tab == 2)
		{ 
			$template = new Template("languages/en-gb/template/samples/tabs/generic_active.html");
			$template->set_var("title", "Par. Samples");
			$template->set_var("params", $params);
			$template->output();
		}
		else
		{
			$template = new Template("languages/en-gb/template/samples/tabs/generic.html");
			$template->set_var("title", "Par. Samples");
			$template->set_var("params", $params);
			$template->output();
		}
		
		// Item Lister Dialogs
		$module_dialog_array = ModuleDialog::list_dialogs_by_type("item_list");
		
		if (is_array($module_dialog_array) and count($module_dialog_array) >= 1)
		{
			foreach ($module_dialog_array as $key => $value)
			{
				$paramquery[username] 	= $_GET[username];
				$paramquery[session_id] = $_GET[session_id];
				$paramquery[nav]		= "sample";
				$paramquery[run]		= "item_list";
				$paramquery[sample_id]	= $_GET[sample_id];
				$paramquery[dialog]		= $value[internal_name];
				$params 				= http_build_query($paramquery,'','&#38;');
				
				if ($_GET[run] == "item_list" and $_GET[dialog] == $value[internal_name])
				{ 
					$template = new Template("languages/en-gb/template/samples/tabs/generic_active.html");
					$template->set_var("title", $value[display_name]);
					$template->set_var("params", $params);
					$template->output();
				}
				else
				{
					$template = new Template("languages/en-gb/template/samples/tabs/generic.html");
					$template->set_var("title", $value[display_name]);
					$template->set_var("params", $params);
					$template->output();
				}
			}
		}
		
		$template = new Template("languages/en-gb/template/samples/tabs/small_tab_footer.html");
		$template->output();
	}

}

?>
