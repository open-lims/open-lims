<?php
/**
 * @package base
 * @version 0.4.0.0
 * @author Roman Konertz <konertz@open-lims.org>
 * @copyright (c) 2008-2016 by Roman Konertz
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
$template = new HTMLTemplate("base/home.html");

$template->set_var("USERNAME",$user->get_full_name(false));
$datetime_handler = new DatetimeHandler();
$template->set_var("DATE",$datetime_handler->get_formatted_string("l, jS F Y"));

$home_summery_left_array = ModuleDialog::list_dialogs_by_type("home_summary_left");
	
if (is_array($home_summery_left_array) and count($home_summery_left_array) >= 1)
{
	$content_array = array();
	$counter = 0;
	
	foreach ($home_summery_left_array as $key => $value)
	{
		require_once($value['class_path']);
		$content_array[$counter]['content'] = $value['class']::$value['method']();
		$counter++;
	}
	
	$template->set_var("HOME_SUMMARY_LEFT_ARRAY" ,$content_array);
}

$home_summery_right_array = ModuleDialog::list_dialogs_by_type("home_summary_right");
	
if (is_array($home_summery_right_array) and count($home_summery_right_array) >= 1)
{
	$content_array = array();
	$counter = 0;
	
	foreach ($home_summery_right_array as $key => $value)
	{
		require_once($value['class_path']);
		$content_array[$counter]['content'] = $value['class']::$value['method']();
		$counter++;
	}
	
	$template->set_var("HOME_SUMMARY_RIGHT_ARRAY" ,$content_array);
}


// Menu

$module_link_array = ModuleLink::list_links_by_type("home_button");
		
if (is_array($module_link_array) and count($module_link_array) >= 1)
{
	$content_array = array();
	$counter = 0;
	
	foreach ($module_link_array as $key => $value)
	{
		$button_template = new HTMLTemplate($value['file']);
	
		$button_paramquery = array();
		$button_paramquery['username'] = $_GET['username'];
		$button_paramquery['session_id'] = $_GET['session_id'];
		
		if (is_array($value['array']) and count($value['array']) >= 1)
		{
			foreach ($value['array'] as $array_key => $array_value)
			{
				$button_paramquery[$array_key] = $array_value;
			}
		}
		
		$button_params = http_build_query($button_paramquery,'','&#38;');
		$button_template->set_var("params", $button_params);
		
		$content_array[$counter]['content'] = $button_template->get_string();
		$counter++;
	}
	
	$template->set_var("I_WANT_TO_ARRAY" ,$content_array);
}
	

	
$paramquery = $_GET;
$paramquery['nav'] = "help";
unset($paramquery['nextpage']);
unset($paramquery['sure']);
unset($paramquery['id']);
unset($paramquery['aspect']);
unset($paramquery['sortvalue']);
unset($paramquery['sortby']);
unset($paramquery['page']);
unset($paramquery['pageref']);
unset($paramquery['action']);
unset($paramquery['run']);
$params = http_build_query($paramquery,'','&#38;');
	
$template->set_var("GET_HELP","index.php?".$params);

// Today Screen

$module_dialog_array = ModuleDialog::list_dialogs_by_type("home_today_box");
		
if (is_array($module_dialog_array) and count($module_dialog_array) >= 1)
{
	$content = "";
	
	foreach ($module_dialog_array as $key => $value)
	{
		require_once($value['class_path']);
		$content .= $value['class']::$value['method']();
	}
	
	$template->set_var("content", $content);
}
else
{
	$template->set_var("content", "");
}

$template->output();

?>
