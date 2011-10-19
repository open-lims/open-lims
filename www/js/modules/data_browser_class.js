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
	
	if (typeof(_data_browser_prototype_called) == "undefined")
	{
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
			show_current_dir_path();
			init_base_tree_nav_link_handler();
		}
	}
	
	function list_click_handler() 
	{	
		$(".DataBrowserAjaxPage").click(function() { 
			init(); //re-init on page change
		});
		
		$(".DataBrowserAdd").click(function(){
			console.log("add");
		});
		$(data_browser_table).children("tr").each(function() {
			var link = $(this).children("td:nth-child(2)").children();
			if ($(link).is("div")) 
			{
				link = $(link).children("a");
			}
			var href = $(link).attr("href");
			var linked_folder_id = href.split("&folder_id=")[1];
			if(linked_folder_id != undefined)
			{
				linked_folder_id = linked_folder_id.split("&")[0];
			}
			var linked_virtual_folder_id = href.split("&vfolder_id=")[1];
			
			$(this)
				.hover(function(){
					$(this).addClass("DataBrowserFileHover");
					},function(){
						$(this).removeClass("DataBrowserFileHover");
					})
				.click(function(){
					open_data_browser_file_dialog(this);
				});
			
			
			$(link).click(function(evt) {
				evt.preventDefault();
				load_different_folder(linked_folder_id,linked_virtual_folder_id);
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
	
	function load_different_folder(folder_id,virtual_folder_id)
	{
		if(folder_id == undefined && virtual_folder_id == undefined)
		{ //clicked on file
			$(location).attr("href",href);
		}
		else
		{ //clicked on folder or virtual folder
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
	}
	
	function init_base_tree_nav_link_handler(){
		var handler = function(href){
			if($(location).attr("href").indexOf("&nav=data") != -1)
			{ //data browser is selected, push the contents directly
				var folder_id = href.split("&folder_id=")[1];
				load_different_folder(folder_id);
				return true;
			}
		}
		if(tree_nav == undefined)
		{ //navigation might not be loaded yet
			setTimeout(init_base_tree_nav_link_handler,100);
		}
		else
		{
			tree_nav.set_follow_link_handler(handler);
		}
	}
	
	function open_data_browser_file_dialog(element)
	{
		$("#DataBrowserFileDialog").remove();
		$(".DataBrowserFileSelected").removeClass("DataBrowserFileSelected");
		$(element).addClass("DataBrowserFileSelected");
		var dialog_width = 150;
		var pos = $(element).position();
		var width = $(element).width();
		var height = $(element).height();
		var offset_x = pos.left + width - dialog_width -2;
		var offset_y = pos.top + height - 1;
		$("<div id='DataBrowserFileDialog'>some dialog!<br/>do something<br/>do something else</div>")
			.css({"width":dialog_width,"position":"absolute","top":offset_y,"left":offset_x})
			.hide()
			.appendTo("#main")
			.slideDown(200);
		
	}
}