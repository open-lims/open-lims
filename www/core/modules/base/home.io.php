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
 	
 $template = new Template("languages/en-gb/template/home.html");

 $homeDate = date("l, jS F Y");

 $template->set_var("USERNAME",$user->get_full_name(false));
 $template->set_var("DATE",$homeDate);
 
 $project = new Project(null);
 
 $user_filesize = $user->get_user_filesize();
 $user_quota = $user->get_user_quota();
 
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
	 
	if (round($user_filesize/$user_quota*100,0) >= $GLOBALS[quota_warning]) {
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

$paramquery = $_GET;
$paramquery[nav] = "projects";
$paramquery[run] = "new";
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
$params = http_build_query($paramquery,'','&#38;');

$template->set_var("NEW_PROJECT","index.php?".$params);

	
$paramquery = $_GET;
$paramquery[nav] = "projects";
$paramquery[run] = "workon";
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
$params = http_build_query($paramquery,'','&#38;');
	
$template->set_var("WORK_PROJECT","index.php?".$params);

	
$paramquery = $_GET;
$paramquery[nav] = "projects";
$paramquery[run] = "accessdata";
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
$params = http_build_query($paramquery,'','&#38;');
	
$template->set_var("ACCESS_PROJECT","index.php?".$params);

	
$paramquery = $_GET;
$paramquery[nav] = "samples";
$paramquery[run] = "new";
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
$params = http_build_query($paramquery,'','&#38;');
	
$template->set_var("CREATE_SAMPLE","index.php?".$params);

	
$paramquery = $_GET;
$paramquery[nav] = "samples";
$paramquery[run] = "mysamples";
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
$params = http_build_query($paramquery,'','&#38;');

$template->set_var("VIEW_SAMPLES","index.php?".$params);

	
$paramquery = $_GET;
$paramquery[nav] = "objects";
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
	
$template->set_var("VIEW_FILES","index.php?".$params);

	
$paramquery = $_GET;
$paramquery[nav] = "static";
$paramquery[run] = "tomylab";
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
$params = http_build_query($paramquery,'','&#38;');
	
$template->set_var("GET_TO_GROUP","index.php?".$params);
	

	
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

$project_task = new ProjectTask(null);
$project_task_array = $project_task->list_upcoming_tasks();

if (is_array($project_task_array) and count($project_task_array) >= 1) {
	
	$template->set_var("exist_project_task", true);
	
	$content_array = array();
	$counter = 0;
	
	foreach ($project_task_array as $key => $value) {
		
		$paramquery = $_GET;
		$paramquery[nav] = "projects";
		$paramquery[run] = "detail";
		$paramquery[project_id] = $value[project_id];
		$params = http_build_query($paramquery, '', '&#38;');
		
		if ($value[status] == 1) {
			$content_array[$counter][name] = "<span class='HomeTodayOverdueEntry'><a href='index.php?".$params."'>".$value[project_name]."</a> - ".$value[task_name]." - ".$value[end_date]."</span>";
		}else{
			$content_array[$counter][name] = "<a href='index.php?".$params."'>".$value[project_name]."</a> - ".$value[task_name]." - ".$value[end_date];
		}
		
		
		$counter++;
		
	}
	
	$template->set_var("project_task_array", $content_array);
	
}else{
	$template->set_var("exist_project_task", false);
}

$template->set_var("exist_appointment", false);
$template->set_var("exist_todo_task", false);

$template->output();

?>
