
var tree_menu = new Array();
var	menu = new Array();

function treeFolderInit(id) {

	tree_menu[id] = new TreeMenu(id, "/core/modules/data/folder.ajax.php?run=list_folder_childs&folder_id", "#", "treeFolderSub", false, "in_box_list");
	
	tree_menu[id].treeFormInit(id, 1);
	tree_menu[id].addHtmlElement("<input type='radio' name='folder_id' value='{#}' />");
		
}

function treeFolderSub(id, sub_id) {

	tree_menu[id].treeFormSub(id, sub_id);
		
}

function selectDate(id, event) {
	
	var mini_cal = new MiniCalendar(id);
	mini_cal.toggleFrame(event);
	
}


function menuFolderInit(id) {

	var session_id = getGetParam("session_id");
	var username = getGetParam("username");
	
	menu[id] = new TreeMenu(id, "/core/modules/data/folder.ajax.php?run=list_menu_folder_childs&session_id=" + session_id + "&folder_id", "index.php?username=" + username + "&session_id=" + session_id + "&run=delete_stack&change_tab=true&nav=data&folder_id", "menuFolderSub", false, "left_navigation");
	
	menu[id].setRewriteAjaxSource("/core/modules/data/folder.ajax.php?run=rewrite_menu_childs_array&session_id=" + session_id + "");
	menu[id].treeLinkSavedInit(id, 1);
		
}

function menuFolderSub(id, sub_id) {

	menu[id].treeLinkSavedSub(id, sub_id);
		
}

function menuOrganisationUnitInit(id) {

	var session_id = getGetParam("session_id");
	var username = getGetParam("username");
	
	var nav = getGetParam("nav");
	if (nav == "sample") {
		nav = "sample";
	}else{
		nav = "project";
	}
	
	menu[id] = new TreeMenu(id, "/core/modules/organisation_unit/organisation_unit.ajax.php?run=list_menu_ou_childs&session_id=" + session_id + "&ou_id", "index.php?username=" + username + "&session_id=" + session_id + "&nav=" + nav +"&run=organ_unit&ou_id", "menuFolderSub", false, "left_navigation");
	
	menu[id].setRewriteAjaxSource("/core/modules/organisation_unit/organisation_unit.ajax.php?run=rewrite_menu_childs_array&session_id=" + session_id + "");
	menu[id].treeLinkSavedInit(id, 0);
		
}

function menuOrganisationUnitSub(id, sub_id) {

	menu[id].treeLinkSavedSub(id, sub_id);
		
}

function menuProjectInit(id) {

	var session_id = getGetParam("session_id");
	var username = getGetParam("username");
	var project_id = getGetParam("project_id");
	
	menu[id] = new TreeMenu(id, "/core/modules/project/project.ajax.php?run=list_menu_project_childs&session_id=" + session_id + "&project_id", "index.php?username=" + username + "&session_id=" + session_id + "&nav=projects&run=detail&project_id", "menuFolderSub", false, "left_navigation");
	
	menu[id].setRewriteAjaxSource("/core/modules/project/project.ajax.php?run=rewrite_menu_childs_array&session_id=" + session_id + "");
	menu[id].treeLinkSavedInit(id, project_id);
		
}

function menuProjectSub(id, sub_id) {
	menu[id].treeLinkSavedSub(id, sub_id);
		
}

function toogleDataSearchProject() {
	
	var sample_div = document.getElementById("SearchDataAjaxSample");
		sample_div = sample_div.style.display = "none";
		
	var project_div = document.getElementById("SearchDataAjaxProject");
		project_div = project_div.style.display = "block";
	
	var sample_select = document.getElementById("SearchDataAjaxSelectSample");
		sample_select = sample_select.name = "";	
		
	var project_select = document.getElementById("SearchDataAjaxSelectProject");
		project_select = project_select.name = "project_id";	
		
}

function toogleDataSearchSample() {
	
	var project_div = document.getElementById("SearchDataAjaxProject");
		project_div = project_div.style.display = "none";
	
	var sample_div = document.getElementById("SearchDataAjaxSample");
		sample_div = sample_div.style.display = "block";

	var sample_select = document.getElementById("SearchDataAjaxSelectSample");
		sample_select = sample_select.name = "sample_id";	
		
	var project_select = document.getElementById("SearchDataAjaxSelectProject");
		project_select = project_select.name = "";	
		
}
