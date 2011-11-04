/*
 * version: 0.4.0.0
 * author: Roman Quiring <quiring@open-lims.org>
 * copyright: (c) 2008-2011 by Roman Quiring
 * license: GPLv3
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

function data_browser() 
{
	var data_browser_table;
	var current_folder_id;
	var current_virtual_folder_id;
	var click_from_left_nav_menu = false;
	
	if (typeof(_data_browser_prototype_called) == "undefined")
	{  
		data_browser.prototype.open_link_in_ui = open_link_in_ui;
		init();
	}

	function init()
	{
		data_browser_table = $(".OverviewTable").children("tbody");
		var argument_array = list.get_argument_array();
		var argument_parts = argument_array.split("],[");
		current_folder_id = argument_parts[0].replace("[[\"folder_id\",","").replace(/"/g,"");
		current_virtual_folder_id = argument_parts[1].replace("\"virtual_folder_id\",","").replace(/]/g,"").replace(/"/g,"");
		
		var children = $(data_browser_table).children();
		var src =  $(children).children("td").children("div").children("img").attr("src");
		if(children.length == 1 && $(children).hasClass("ListLoadingContents"))
		{ //not loaded contents yet. children can be an empty div due to list animate function -> base_list_class.js#126
			setTimeout(init,200);
		}
		else
		{
			list_click_handler();
			if(click_from_left_nav_menu)
			{
				show_current_dir_path_and_clear_stack();
				click_from_left_nav_menu = false;
			}
			else
			{
				show_current_dir_path();	
			}
			init_base_tree_nav_link_handler();
		}
	}
	
	function list_click_handler() 
	{	
		$(".DataBrowserAjaxPage").click(function() { 
			init(); //re-init on page change
		});
		$(".DataBrowserAdd").click(function(){
			toggle_add_file_dialog();
		});
		$(data_browser_table).children("tr").each(function() {
			var linked_folder_id;
			var linked_virtual_folder_id;
			if($(this).children("td").length < 2) {
				return true; //no clickable link ("no results found!"), continue
			}
			var link = $(this).children("td:nth-child(2)").children();
			if ($(link).is("div")) 
			{
				link = $(link).children("a");
			}
			var href = $(link).attr("href");
			linked_folder_id = href.split("&folder_id=")[1];
			if(linked_folder_id != undefined)
			{
				linked_folder_id = linked_folder_id.split("&")[0];
			}
			linked_virtual_folder_id = href.split("&vfolder_id=")[1];
			$(this)
				.hover(function(){
					$(this).addClass("DataBrowserFileHover");
					},function(){
						$(this).removeClass("DataBrowserFileHover");
					})
				.click(function(evt){
					evt.preventDefault();
					if(($(evt.target)[0] == $(link)[0]))
					{ //clicked on a link
						if(linked_folder_id == undefined && linked_virtual_folder_id == undefined)
						{ //clicked on file
							close_data_browser_file_dialog();
							href = href.replace("&action=file_detail","").replace("index.php?","download.php?");
							$(location).attr("href",href);
						}
						else
						{ //clicked on folder
							close_data_browser_file_dialog();
							load_different_folder(linked_folder_id,linked_virtual_folder_id);		
						}
					}
					else
					 { //clicked somewhere inside row
						if($(this).hasClass("DataBrowserFileSelected"))
						{
							close_data_browser_file_dialog()
						}
						else 
						{
							open_data_browser_file_dialog(this);	
						} 
					 }
				});
		});
	}
	
	function show_current_dir_path()
	{
		$.ajax({
			type : "GET",
			url : "core/modules/data/data_browser.ajax.php",
			data : "username="+get_array['username']+"&session_id="+ get_array['session_id']+"&run=get_data_browser_path&folder_id="+current_folder_id+"&virtual_folder_id="+current_virtual_folder_id,
			success : function(data) {
				$(data_browser_table).parent().parent().children(".OverviewTableRight").html(data);
			}
		});
	}
	
	function show_current_dir_path_and_clear_stack()
	{
		$.ajax({
			type : "GET",
			url : "core/modules/data/data_browser.ajax.php",
			data : "username="+get_array['username']+"&session_id="+ get_array['session_id']+"&run=get_data_browser_path_cleared&folder_id="+current_folder_id+"&virtual_folder_id="+current_virtual_folder_id,
			success : function(data) {
				$(data_browser_table).parent().parent().children(".OverviewTableRight").html(data);
			}
		});
	}
	
	function load_different_folder(folder_id,virtual_folder_id)
	{
		if(folder_id == undefined)
		{
			folder_id = null;
		}
		if(folder_id != null)
		{
			folder_id = "\""+folder_id+"\"";
		}
		if(virtual_folder_id == undefined)
		{
			virtual_folder_id = null;
		}
		if(virtual_folder_id != null)
		{
			virtual_folder_id = "\""+virtual_folder_id+"\"";
		}
		var argument_array = "[[\"folder_id\","+folder_id+"],[\"virtual_folder_id\","+virtual_folder_id+"]]";
		list.set_argument_array(argument_array);
		list.reload();
		init();
	}
	
	function init_base_tree_nav_link_handler()
	{
		if(typeof(tree_nav) == "undefined")
		{ //navigation might not be loaded yet
			setTimeout(init_base_tree_nav_link_handler,100);
		}
		else
		{
			var handler = function(href)
			{
				// if list is instanciated: push the contents directly
				click_from_left_nav_menu = true;
				var folder_id = href.split("&folder_id=")[1];
				load_different_folder(folder_id);
				return true;
			}
			tree_nav.set_follow_link_handler(handler);
		}
	}
	
	function open_data_browser_file_dialog(element)
	{
		close_data_browser_file_dialog();
		var dialog_width = 150;
		var pos = $(element).position();
		var width = $(element).width();
		var height = $(element).height();
		var offset_x = pos.left + width - dialog_width - 5;
		var offset_y = pos.top + height;
		var dialog = $("<div id='DataBrowserFileDialog'>Permission denied.</div>");
		if($(element).children("td:nth-child(2)").children().hasClass("DataBrowserIsFolder"))
		{
			var link = $(element).children("td:nth-child(2)").children().children().attr("href");
			var folder_id = link.split("&nav=data&folder_id=")[1];
			load_context_sensitive_dialog(folder_id,"folder");
		}
		else
		{
			var link = $(element).children("td:nth-child(2)").children().attr("href");
			if($(element).children("td:nth-child(3)").text() == "File")
			{
	//			link = link.replace("&run=get_data_browser_link_html_and_button_handler","");
				var split = link.split("&nav=data&file_id=");
				var file_id = split[1].replace("&action=file_detail","");
				load_context_sensitive_dialog(file_id,"file");
			}
			else if($(element).children("td:nth-child(3)").text() == "Value")
			{
				var split = link.split("&nav=data&value_id=");
				var value_id = split[1].replace("&action=value_detail","");
				load_context_sensitive_dialog(value_id,"value");
			}
			else
			{
				return false;
			}
		}
		$(element).addClass("DataBrowserFileSelected");
		$(dialog)
			.css({"width":dialog_width,"position":"absolute","top":offset_y,"left":offset_x})
			.hide()
			.appendTo("#main");
	}
	
	function close_data_browser_file_dialog() 
	{
		$("#DataBrowserFileDialog").remove();
		$(".DataBrowserFileSelected").removeClass("DataBrowserFileSelected");
	}
	
	function load_context_sensitive_dialog(file_id, type)
	{
		var data;
		switch(type)
		{
			case "folder":
				data = "username="+get_array['username']+"&session_id="+ get_array['session_id']+"&run=get_context_sensitive_folder_menu&file_id="+file_id;
				break;
			case "file":
				data = "username="+get_array['username']+"&session_id="+ get_array['session_id']+"&run=get_context_sensitive_file_menu&file_id="+file_id;
				break;
			case "value":
				data = "username="+get_array['username']+"&session_id="+ get_array['session_id']+"&run=get_context_sensitive_value_menu&file_id="+file_id;
				break;
			default: 
				break;
		}
		$.ajax({
			type : "GET",
			url : "core/modules/data/data_browser.ajax.php",
			data : data,
			success : function(data) {
				if(data != "")
				{
					$("#DataBrowserFileDialog")
						.click(function(evt){
							evt.preventDefault();
							var target = evt.target;
							var href = $(target).attr("href");
							if($(target).hasClass("DataBrowserDialogLinkFollowDirectly"))
							{
								$(location).attr("href",href);
							}
							else
							{
								open_link_in_ui(type, href);
							}
						})
						.html(data);
				}
				$("#DataBrowserFileDialog").slideDown(200);
			}
		});
	}
	
	function open_link_in_ui(type, link)
	{
		close_data_browser_file_dialog();
		$("#DataBrowserAddFileDialog").remove(); //close add dialog if open
		var data_params = link.replace("index.php?","");
		if(data_params.indexOf("run=add_file") == -1 && data_params.indexOf("run=add_folder") == -1)
		{
			data_params += "&run=get_data_browser_link_html_and_button_handler";		
		}
		var url;
		switch(type)
		{
			case "folder":
				url = "core/modules/data/folder.ajax.php";
				break;
			case "file":
				url = "core/modules/data/file.ajax.php";
				break;
			case "value":
				url = "core/modules/data/value.ajax.php";
				break;
			default: 
				break;
		}
		$.ajax({
			type : "GET",
			url : url,
			data : data_params,
			success : function(data) {
				var json = $.parseJSON(data);
				var click_handler = json["handler"];
				var click_handler_caption = json["handler_caption"];
				var html_template = json["content"];
				var html_template_caption = json["content_caption"];
				var container = $("<div id='DataBrowserLoadedAjaxContent'></div>").html(html_template);
				$(container).dialog({
					"title" : html_template_caption ,  
					"minHeight" : "100" , 
					"width" : "400", 
					"buttons" : [
					     {text : click_handler_caption , click : function(){
					    	eval(click_handler);	
					     }} , 
			             {text : "Cancel", click : function(){$(container).dialog("close");}}]
				});
			}
		});
	}
	
	function close_ui_window_and_reload()
	{
		$("#DataBrowserLoadedAjaxContent")
			.dialog("close")
			.remove();
		list.reload();
		init();
	}
	
	function toggle_add_file_dialog()
	{
		if($("#DataBrowserAddFileDialog").length == 1)
		{
			$("#DataBrowserAddFileDialog").remove();
			return false;
		}
		var pos = $(".DataBrowserAdd").position();
		var height = $(".DataBrowserAdd").height();
		var offset_x = pos.left;
		var offset_y = pos.top + height;
		var dialog = $("<div id='DataBrowserAddFileDialog'>Permission denied.</div>")
			.css({"position":"absolute","top":offset_y,"left":offset_x});
		var folder_path = $(".OverviewTableRight").text();
		$.ajax({
			type : "GET",
			url : "core/modules/data/data_browser.ajax.php",
			data : "username="+get_array['username']+"&session_id="+ get_array['session_id']+"&run=get_file_add_menu&folder_path="+folder_path,
			success : function(data) {
				if(data != "")
				{
					$(dialog)
						.html(data)
						.hide()
						.appendTo("#main");
				}
				$("#DataBrowserAddFileDialog").slideDown(200);
			}
		});
	}
	
}