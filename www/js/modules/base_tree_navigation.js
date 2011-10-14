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

function base_tree_nav(id, name, ajax_handler)
{
	var tree_id = id;
	var tree_name = name;
	var ajax_handler = ajax_handler;
	 
	var loading_animation = true;
	
	var scrollbar = true;
	var scroll_api;
	  
	var get_array = getQueryParams(document.location.search);
	var array;
	var write_back_array = true;
	
	var follow_link = true;
	var follow_link_now = true;
	
	var element_to_insert = new Array();
	var inserted_element_event_handler = function(){};
	
	var max_menu_height;
  
    // initialize the member function references for the class prototype
    if (typeof(_file_tree_nav_prototype_called) == "undefined")
    {
	    _base_tree_nav_prototype_called = true;
	    base_tree_nav.prototype.init = init;
	    base_tree_nav.prototype.set_writeback = set_writeback;
	    base_tree_nav.prototype.set_scrollbar = set_scrollbar;
	    base_tree_nav.prototype.add_element_to_insert = add_element_to_insert;
	    base_tree_nav.prototype.set_loading_animation = set_loading_animation;
	    base_tree_nav.prototype.set_follow_link = set_follow_link;
    }

    function set_loading_animation(bool)
    {
    	loading_animation = bool;
    }
  
    function set_scrollbar(bool){
	    scrollbar = bool;
    }
    
    function set_follow_link(bool){
    	follow_link = bool;
    }
    
    function set_writeback(bool){
	    write_back_array = bool;
    }
    
    function add_element_to_insert($element, event_handler)
    {
    	element_to_insert.push($element);
    	inserted_element_event_handler = event_handler;
    }

    function get_list_element(layer,current_id,name,open,symbol,link,clickable,permission,is_folder) 
    {
		var li = $("<li></li>");
		if (!clickable) 
		{
			$(li).addClass("NotClickable");
		}
		if (!permission) 
		{
			$(li).addClass("NotPermitted");
		}
		
		var div = $("<div id='" + tree_name + "ElementID" + current_id + "'>" 
			+ "<table class='BaseTreeEntry'><tr><td><a href='#'><img src='images/plus.png' alt=''/></a></td>" 
			+ "<td><a href='index.php?"
			+ link
			+ "' onclick='return false'><img src='images/icons/"
			+ symbol
			+ "'/></a></td>" 
			+ "<td style='width:1000px;'><a href='index.php?" //extreme with to always align the optional elements on the right
			+ link
			+ "' onclick='return false'>"
			+ name
			+ "</a></td>"
			+ "</tr></table></div>");
		if(open)
		{
			$(div).addClass(tree_name+"Open")
		}
		else
		{
			$(div).addClass(tree_name+"Closed");
		}
		if(is_folder)
		{
			$(div).addClass(tree_name+"IsEmptyFolder");
		}
		if(element_to_insert.length > 0 && clickable && permission)
		{
			for ( var int = 0; int < element_to_insert.length; int++) {
				var td = $("<td></td>").appendTo($(div).children("table").children("tbody").children("tr"));
				var element = element_to_insert[int].clone()
					.attr("id",current_id)
					.addClass("optionalTreeElement")
					.appendTo(td);
			}
		}
		$(li).append(div);
		return li;
    }
    
	function update_icons() 
	{
		$("."+tree_name+"Open > table > tbody > tr > td:nth-child(1) > a:nth-child(1) > img").attr("src","images/minus.png");
		$("."+tree_name+"Closed > table > tbody > tr > td:nth-child(1) > a:nth-child(1) > img").attr("src", "images/plus.png");
		$("."+tree_name+"IsEmptyFolder > table > tbody > tr > td:nth-child(1) > a:nth-child(1) > img").attr("src", "images/icons/dot.png");
		
		$(".NotPermitted").each(function(){
			var src = $(this).children("div").children("table").children("tbody").children("tr").children("td:nth-child(2)").children("a").children("img").attr("src");
			if(src.indexOf("core/images/denied_overlay.php?image=")==-1)
			{
				var new_src = "core/images/denied_overlay.php?image="+src;
				$(this).children().children("table").children("tbody").children("tr").children("td:nth-child(2)").children("a").children("img").attr("src",new_src);
			}
		});
	}
  
	var update_scrollbar = function() 
	{
		if(!scrollbar)
		{
			return false;
		}
		
		var content_div_height = $("#content").css("height").replace("px", "");
		max_menu_height = content_div_height;
		var offset_bottom = 8; 
		
		if (max_menu_height < 500) 
		{
			max_menu_height = 500;
		}
		
		var list_height = parseInt($("#"+tree_id).children("ul").css("height").replace("px",""));
		var scroll_height = list_height + offset_bottom;

		if (scroll_height >= max_menu_height) 
		{
			scroll_height = max_menu_height - offset_bottom;
		} 
		else 
		{
			scroll_api.scrollToY(0);
		}
		$(".jspContainer").css("height", scroll_height);
		scroll_api.reinitialise();
	}
	
	function parse_array() 
	{
		if(!write_back_array)
		{
			return false;
		}
		
		var previous_element_index;

		var entry_index = -1;
		var new_array = new Array();

		// check for deleted elements
		for (var int = 0; int < array.length; int++) 
		{
			var array_id = array[int][1];
			if ($("#"+tree_name+"ElementID" + array_id).length == 0) 
			{
				array.splice(int, 1);
				int--;
			}
		}

		// check for new elements
		$("#" + tree_id)
			.find("li")
			.each(
				function() 
				{
					var entry_id = $(this).children("div").attr("id").replace(tree_name+"ElementID", "");
	
					var found = false;
	
					for ( var int = 0; int < array.length; int++) 
					{
						var array_id = array[int][1];
	
						if (array_id == entry_id) 
						{
							found = true;
							previous_element_index = parseInt(int);
							if ($("#"+tree_name+"ElementID" + entry_id).hasClass(tree_name+"Closed")) 
							{
								array[int][7] = false;
							} 
							else if ($("#"+tree_name+"ElementID"+ entry_id).hasClass(tree_name+"Open")) 
							{
								array[int][7] = true;
							}
							break;
						}
					}
	
					if (!found) 
					{
						if (entry_index == -1) 
						{
							entry_index = previous_element_index + 1;
						}
	
						var entry_layer = $(this).parent().attr("class").replace(tree_name+"Layer", "").replace(" BaseTreeList","");
						var entry_name = $(this).children("div").children("table").children("tbody").children("tr").children("td:nth-child(3)").text();
						
						var entry_symbol = $(this).children("div").children("table").children("tbody").children("tr").children("td:nth-child(2)").children("a").children("img").attr("src");//.replace("images/icons/", "")
						if(entry_symbol.indexOf("core/images/denied_overlay.php?image=") != -1)
						{
							entry_symbol = entry_symbol.replace("core/images/denied_overlay.php?image=","");
						}
						var chars_to_delete = entry_symbol.indexOf("images/icons/") + 13; //ie inserts the full url as src so we need to
						entry_symbol = entry_symbol.slice(chars_to_delete); 			  //delete everything before the original filename

						var entry_link = $(this).children("div").children("table").children("tbody").children("tr").children("td:nth-child(3)").children("a").attr("href").replace("index.php?", "");
						
						var entry_open = true;
						if ($(this).children("div").hasClass(tree_name+"Closed")) 
						{
							entry_open = false;
						}
						
						var is_empty_folder = false;
						if ($(this).children("div").hasClass(tree_name+"IsEmptyFolder")) 
						{
							is_empty_folder = true;
						}
						
						var entry_clickable = true;
						var entry_permission = true;
						if ($(this).hasClass("NotClickable")) 
						{
							entry_clickable = false;
						}
						if ($(this).hasClass("NotPermitted")) 
						{
							entry_permission = false;
						}
	
						var new_array_element = new Array(8);
						new_array_element[0] = entry_layer;
						new_array_element[1] = entry_id;
						new_array_element[2] = entry_name;
						new_array_element[3] = entry_symbol;
						new_array_element[4] = entry_permission;
						new_array_element[5] = entry_clickable;
						new_array_element[6] = entry_link;
						new_array_element[7] = entry_open;
						new_array_element[8] = is_empty_folder;
	
						new_array.push(new_array_element);
					}
				});

		for (var g = 0; g < new_array.length; g++) 
		{
			array.splice(entry_index + g, 0, new_array[g]);
		}

		if (ajax_handler.indexOf("?") == -1) 
		{
			var post_ajax_handler = ajax_handler + "?session_id="+ get_array['session_id'] + "&run=set_array";
		} 
		else 
		{
			var post_ajax_handler = ajax_handler + "&session_id="+ get_array['session_id'] + "&run=set_array";
		}

		var json_array = encodeURIComponent(JSON.stringify(array));
		$.ajax({
			async: false,
			type : "POST",
			url : post_ajax_handler,
			data : "array=" + json_array,
			success : function(data){}
		});
	}
	
    function init()
    {   
    	$("#"+tree_id).unbind("click"); //unbind all existing handlers
	    if(loading_animation)
	    {
	    	$("<div id='loadingAnimation'><img src='images/animations/loading_circle_small.gif'/></div>")
		  		.css({"text-align":"center","margin-top":"10px"}).appendTo("#"+tree_id);
	    }
	  
	    if(scrollbar)
	    {
	    	if(!$("#"+tree_id).parent().hasClass("jspPane"))
	    	{
	    		$("#"+tree_id).parent().jScrollPane();
	    	}
    		scroll_api = $("#"+tree_id).parent().parent().parent().data("jsp");
	    }
	    
		var return_html = $("<ul class='" + tree_name + "Layer0 BaseTreeList' style='margin-left:2px;'></ul>");
		
		var previous_open_element = undefined;
		var previous_open_element_layer = undefined;
		
		var open_elements_list = new Array();
		
		$.ajax(
	    {
	    	type : "GET",
	    	url : ajax_handler,
	    	data : "run=get_array&session_id=" + get_array['session_id'] + "&username=" + get_array['username'],
	    	success : function(data) 
	    	{
	    		array = $.parseJSON(data);
				$(array).each
				(
					function(i)
					{ 
						var layer = $(this)[0];
						var current_id = $(this)[1];
						var name = $(this)[2];
						var open = $(this)[7];
						var symbol = $(this)[3];
						var link = $(this)[6];
						var clickable = $(this)[5]; 
						var permission = $(this)[4];
						var is_empty_folder = !$(this)[8];
						
//						console.log(name+" is empty: "+is_empty_folder);
						
						var li = get_list_element(layer,current_id,name,open,symbol,link,clickable,permission,is_empty_folder);
						function recursively_check_where_to_insert() 
						{
							if(open_elements_list.length > 0)
							{
								var last_element = open_elements_list[open_elements_list.length-1];

								if(last_element[1] == layer - 1)
								{
									$(last_element[0]).append(li);
								}
								else
								{
									open_elements_list.pop();
									recursively_check_where_to_insert();
								}
							}
							else
							{
								$(return_html).append(li);
							}
						}
						recursively_check_where_to_insert();
										
						if(open)
						{
							var next_layer = parseInt(layer) + 1;
							var ul = $("<ul class='" + tree_name + "Layer" + next_layer + " BaseTreeList'></ul>");
							$(li).append(ul);
							var new_open_element = new Array(2);
							new_open_element[0] = ul;
							new_open_element[1] = layer;
							open_elements_list.push(new_open_element);
						}
					}
				);
				$("#" + tree_id).html(return_html) //overwrites loading animation
					.bind("click", handler);
				update_icons();
				update_scrollbar();
	    	}
	    });
    }
    
    var handler = function(evt)
	{
		evt.preventDefault();
		$("#" + tree_id).unbind("click");
		var target = evt.target;
		var target_div = $(target).parents("div")[0];
		if($(target_div).attr("id") == tree_id || $(target_div).hasClass("jspPane"))
		{
			$("#" + tree_id).bind("click",handler);
			return false;
		}
		else if($(target).hasClass("optionalTreeElement"))
		{
			inserted_element_event_handler(target);
			$("#" + tree_id).bind("click",handler);
			return false;
		}
		
		var href = $(target_div).children("table").children("tbody").children("tr").children("td:nth-child(2)").children("a").attr("href");
		follow_link_now = true;
		close_open_entry = false;
		if(follow_link)
		{
			if($(target_div).parent().hasClass("NotClickable") || $(target_div).parent().hasClass("NotPermitted"))
			{
				follow_link_now = false;
			}
			if ($(target).attr("src") == "images/minus.png" || $(target).attr("src") == "images/plus.png") 
			{
				follow_link_now = false;
				close_open_entry = true;
			}
			else 
			{
				if (href.substr(-11) === "index.php?") 
				{
					follow_link_now = false;
				}
			}
		}
		else
		{
			follow_link_now = false;
		}
	
		if ($(target_div).hasClass(tree_name+"Open")) 
		{
			if(close_open_entry)
			{
				$(target_div).attr("class", tree_name+"Closed");
				var num_children = $(target_div).parent().children("ul").size();
				var ul_to_slide = $(target_div).parent().children("ul");
				if(num_children > 0)
				{
					$(ul_to_slide).slideUp("fast",
						function() {
							$(this).remove();
							update_icons();
							update_scrollbar();
							parse_array();
							$("#" + tree_id).bind("click",handler);
						}
					);
				}
				else
				{
					update_icons();
				}
			}
			else
			{
				update_icons();
				update_scrollbar();
				parse_array();
				$("#" + tree_id).bind("click",handler);
				if (follow_link_now) 
				{
					window.location.href = href;
				}
			}
		} 
		else if ($(target_div).hasClass(tree_name+"Closed")) 
		{
			$(target_div).attr("class", tree_name+"Open");
			var clicked_id = $(target_div).attr("id").replace(tree_name+"ElementID","");
			
			var parent_layer = parseInt($(target_div).parent().parent().attr("class").replace(tree_name+"Layer",""));
			var layer = parent_layer + 1;
			var parent_li = $(target_div).parent();
			
			$.ajax({
				type : "GET",
				url : ajax_handler,
				data : "run=get_children&id=" + clicked_id + "&session_id=" + get_array['session_id'] + "&username=" + get_array['username'],
				success : function(data) {
					var child_array = $.parseJSON(data);
	
					if (child_array != null && child_array.length != 0) 
					{
						var ul = $("<ul class='" + tree_name + "Layer" + layer + " BaseTreeList'></ul>");				
						
						$(child_array).each(
							function() 
							{
								var child_id = $(this)[1];
								var child_name = $(this)[2];
								var child_symbol = $(this)[3];
								var child_link = $(this)[6];
								var child_clickable = $(this)[5];
								var child_permission = $(this)[4];
								var li = get_list_element(layer, child_id, child_name, false, child_symbol, child_link, child_clickable, child_permission);
								$(ul).append(li);
							}
						);
						$(".jspContainer").css("height",max_menu_height-5); //temporäre höhe für flüssige animation
						$(ul).hide().appendTo(parent_li).slideDown("normal", function()
						{
							update_icons();
							update_scrollbar();
							parse_array();
							$("#" + tree_id).bind("click",handler);
							if (follow_link_now) 
							{
								window.location.href = href;
							}
						});	
					}
					else
					{
						update_icons();
						parse_array();
						$("#" + tree_id).bind("click",handler);
						if (follow_link_now) 
						{
							window.location.href = href;
						}
					}
				}
			});
			$(parent_li).children().children().children().children().children("td:nth-child(1)").children().children().attr("src", "images/animations/loading_circle_small.gif");
		}
	}
}