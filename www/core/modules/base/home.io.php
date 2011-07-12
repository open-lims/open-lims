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

 $user_data = new DataUserData($user->get_user_id());
 $template = new Template("template/base/home.html");

 $homeDate = date("l, jS F Y");

 $template->set_var("USERNAME",$user->get_full_name(false));
 $template->set_var("DATE",$homeDate);

 $project = new Project(null);
  
 $user_filesize = $user_data->get_filesize();
 $user_quota = $user_data->get_quota();
 
 if ($user_quota == 0) {
 	$quota = "unlimited";
 } else{
 	$quota = Misc::calc_size($user_quota); 	
 }
 
 if ($user_quota != 0) {
	 $diskspace_per = $user_filesize / $user_quota*100;
	 
	 if ($diskspace_per == 0) {
	 	$diskspace_per_display = "(0%)";
	 }else{
	 	
	 	
	 	$diskspace_per = floor($diskspace_per);
	 	
	 	if ($diskspace_per == 0) {
	 		$diskspace_per_display = "(> 1%)";	
	 	}else{
	 		$diskspace_per_display = "(".$diskspace_per."%)";
	 	}
	 	
	 }
	 
	if (round($user_filesize/$user_quota*100,0) >= constant("QUOTA_WARNING")) {
		$quotaWarn = " <img src='images/icons/notice.png' alt='W' />";
	}
 }else{
 	$quotaWarn = "";
 	$diskspace_per_display = "";
 }
 
 $act_filesize = Misc::calc_size($user_filesize);
 

 $sum_running_projects = Project_Wrapper::count_user_running_projects($user->get_user_id());
 $sum_finished_projects = Project_Wrapper::count_user_finished_projects($user->get_user_id());
 $sum_projects = Project_Wrapper::count_user_projects($user->get_user_id());
 $sum_samples = Sample_Wrapper::count_user_samples($user->get_user_id());
 
 $template->set_var("RUNNING_PROJECTS",$sum_running_projects."/".$sum_projects);
 $template->set_var("FINISHED_PROJECTS",$sum_finished_projects."/".$sum_projects);
 $template->set_var("SAMPLES",$sum_samples);
 $template->set_var("USED_DISKSPACE",$act_filesize." ".$diskspace_per_display."".$quotaWarn);
 $template->set_var("QUOTA",$quota);
	 
	
// Menu

$module_link_array = ModuleLink::list_links_by_type("home_button");
		
if (is_array($module_link_array) and count($module_link_array) >= 1)
{
	$content_array = array();
	$counter = 0;
	
	foreach ($module_link_array as $key => $value)
	{
		$button_template = new Template("template/".$value[file]);
	
		$button_paramquery = array();
		$button_paramquery[username] = $_GET[username];
		$button_paramquery[session_id] = $_GET[session_id];
		
		if (is_array($value['array']) and count($value['array']) >= 1)
		{
			foreach ($value['array'] as $array_key => $array_value)
			{
				$button_paramquery[$array_key] = $array_value;
			}
		}
		
		$button_params = http_build_query($button_paramquery,'','&#38;');
		$button_template->set_var("params", $button_params);
		
		$content_array[$counter][content] = $button_template->get_string();
		$counter++;
	}
	
	$template->set_var("I_WANT_TO_ARRAY" ,$content_array);
}
	

	
$paramquery = $_GET;
$paramquery[nav] = "help";
unset($paramquery[runit]);
unset($paramquery[nextpage]);
unset($paramquery[sure]);
unset($paramquery[id]);
unset($paramquery[aspect]);
unset($paramquery[sortvalue]);
unset($paramquery[sortby]);
unset($paramquery[page]);
unset($paramquery[pageref]);
unset($paramquery[folderid]);
unset($paramquery[objectid]);
unset($paramquery[action]);
unset($paramquery[projectid]);
unset($paramquery[run]);
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
