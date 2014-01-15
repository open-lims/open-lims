<?php
/**
 * @package sample
 * @version 0.4.0.0
 * @author Roman Konertz <konertz@open-lims.org>
 * @copyright (c) 2008-2014 by Roman Konertz
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
		$tab_io = new Tab_IO();

		// Main Page
		$paramquery['username'] 	= $_GET['username'];
		$paramquery['session_id'] 	= $_GET['session_id'];
		$paramquery['nav']			= "sample";
		$paramquery['run']			= "detail";
		$paramquery['sample_id']	= $_GET['sample_id'];
		$params 					= http_build_query($paramquery,'','&#38;');
		unset($paramquery);
		
		$tab_io->add("main", Language::get_message("SampleGeneralDetailTabMainPage", "general"), $params, false);
		
					
		// Parent Item Dialogs
		$module_dialog_array = ModuleDialog::list_dialogs_by_type("parent_item_list");
		
		if (is_array($module_dialog_array) and count($module_dialog_array) >= 1)
		{
			foreach ($module_dialog_array as $key => $value)
			{
				$paramquery['username'] 	= $_GET['username'];
				$paramquery['session_id'] 	= $_GET['session_id'];
				$paramquery['nav']			= "sample";
				$paramquery['run']			= "parent_item_list";
				$paramquery['sample_id']	= $_GET['sample_id'];
				$paramquery['dialog']		= $value['internal_name'];
				$params 				= http_build_query($paramquery,'','&#38;');
				
				$tab_io->add("pil_".$value['internal_name'], Language::get_message($value['language_address'], "dialog"), $params, false);
			}
		}
		
		// Item Lister Dialogs
		$module_dialog_array = ModuleDialog::list_dialogs_by_type("item_list");
		
		if (is_array($module_dialog_array) and count($module_dialog_array) >= 1)
		{
			foreach ($module_dialog_array as $key => $value)
			{
				$paramquery['username'] 	= $_GET['username'];
				$paramquery['session_id'] 	= $_GET['session_id'];
				$paramquery['nav']			= "sample";
				$paramquery['run']			= "item_list";
				$paramquery['sample_id']	= $_GET['sample_id'];
				$paramquery['dialog']		= $value['internal_name'];
				$params 				= http_build_query($paramquery,'','&#38;');
				
				$tab_io->add("il_".$value['internal_name'], Language::get_message($value['language_address'], "dialog"), $params, false);
			}
		}
		
		
		if ($_GET['run'] != "parent_item_list" and $_GET['run'] != "item_list")
		{ 
			$tab_io->activate("main");
		}
		else
		{
			if ($_GET['run'] == "item_list" and $_GET['dialog'])
			{
				$tab_io->activate("il_".$_GET['dialog']);
			}
			elseif ($_GET['run'] == "parent_item_list" and $_GET['dialog'])
			{
				$tab_io->activate("pil_".$_GET['dialog']);
			}
			else
			{
				$tab_io->activate("main");
			}
		}

		$tab_io->output();
	}
}

?>
