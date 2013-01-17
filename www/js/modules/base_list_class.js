/**
 * version: 0.4.0.0
 * author: Roman Konertz <konertz@open-lims.org>
 * author: Roman Quiring <quiring@open-lims.org>
 * copyright: (c) 2008-2011 by Roman Konertz, Roman Quiring
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

List = function(ajax_handler, ajax_run, ajax_count_run, argument_array, json_get_array, css_main_id, entries_per_page, column_array)
{	
	if (ajax_handler.indexOf("?") == -1) 
	{
		ajax_handler = ajax_handler+"?";
	} 
	else 
	{
		ajax_handler = ajax_handler+"&";
	}

	var sort_array = new Array();
	var sort_value = "";
	var sort_method = "";
	var page = 1;

	var get_array = getQueryParams(document.location.search);

	var parsed_column_array = $.parseJSON(column_array);
	var colspan = parsed_column_array.length;

	var number_of_entries = 0;
	var number_of_pages = 0;
	

	reload = function() 
	{
		count_entries();
		if(number_of_pages < page)
		{
			page = 1;
		}
		load_content(sort_value, sort_method, page);
	}
	
	autoreload = function(time)
	{
		setTimeout("reload()",time);
		setTimeout("autoreload("+time+")",time);
	}
	
	reinit_sort_handler = function()
	{
		$("." + css_main_id + "Column").each(function() 	
		{
			$(this).bind("click", function() 
			{
				var id = $(this).attr("id");
				sort_value = id.replace(css_main_id + "Column", "");

				var sort_method_key = check_array(sort_value);
				if (sort_method_key != -1) 
				{
					if (sort_array[sort_method_key][1] == "asc") 
					{
						sort_array[sort_method_key][1] = "desc";
						sort_method = "desc";

						change_symbol(id, "downside");
					} 
					else 
					{
						sort_array[sort_method_key][1] = "asc";
						sort_method = "asc";

						change_symbol(id, "upside");
					}
				}
				else
				{
					sort_array_length = sort_array.length;

					sort_array[sort_array_length] = new Array();
					sort_array[sort_array_length][0] = sort_value;
					sort_array[sort_array_length][1] = "asc";
					sort_method = "asc";

					change_symbol(id, "upside");
				}

				load_content(sort_value, sort_method, page);
			});
		});
	}

	reinit_page_handler = function(local_page) 
	{
		if(local_page != undefined)
		{
			page = local_page;
		}
		
		$.ajax({
			type : "POST",
			url : "ajax.php?username="+get_array['username']+"&session_id="+get_array['session_id']+"&nav=base&run=list_get_page_bar",
			data : "page="+page+"&number_of_pages="+number_of_pages+"&css_page_id="+css_main_id+"Page",
			async : false,
			success : function(data)
			{
				$("#" + css_main_id + "PageBar").html(data);

				$("." + css_main_id + "Page").each(function()
				{
					$(this).bind("click",function()
					{
						var id = $(this).attr("id");
						page = id.replace(css_main_id + "Page", "");
						load_content(sort_value, sort_method, page);
					});
				});
			}
		});
	}

	get_argument_array = function()
	{
		return argument_array;
	}
		
	set_argument_array = function(array)
	{
		argument_array = array;
	}

	this.reload = reload;
	this.autoreload = autoreload;
	this.reinit_sort_handler = reinit_sort_handler;
	this.reinit_page_handler = reinit_page_handler;
	this.get_argument_array = get_argument_array;
	this.set_argument_array = set_argument_array;
	
	function count_entries()
	{		
		$.ajax(
		{
			type: "POST",
			url: ajax_handler+"session_id="+get_array['session_id']+"&run="+ajax_count_run,
			data: "argument_array="+argument_array,
			async: false,
			success: function(data)
			{		
				number_of_entries = parseInt(data);
				if (number_of_entries == 0) 
				{
					number_of_pages = 1;
				} 
				else 
				{
					number_of_pages = Math.ceil(number_of_entries / entries_per_page);
				}
			}
		});
	}

	function load_content(sort_value, sort_method, local_page) 
	{
		var local_height = $("#" + css_main_id).height();

		page = local_page;

		$("#" + css_main_id).contents().detach();

		var margin = parseInt(local_height / 2);
		margin = Math.floor(margin);
		margin -= 8;
		if (margin < 0) // init
			margin = 10;

		$("#" + css_main_id).append("<tr class='ListLoadingContents'><td colspan='"+colspan+"'><div style='text-align:center; margin-top:"+margin+"px;'><img src='images/animations/loading_circle_small.gif' alt='Loading...' /></div></td></tr>");

		count_entries();

		reinit_page_handler(page);

		
		$.ajax({
			type : "POST",
			url : "ajax.php?username="+get_array['username']+"&session_id="+get_array['session_id']+"&nav=base&run=list_get_page_information",
			data : "number_of_entries="+number_of_entries+"&number_of_pages="+number_of_pages+"",
			async : false,
			success : function(data) {
				$("#" + css_main_id).parent().parent().children(".ListPageInformation").html(data);
			}
		});

		$.ajax({
			type : "POST",
			url : ajax_handler + "username=" + get_array['username']+"&session_id="+get_array['session_id']+"&run="+ajax_run + "&sortvalue="+sort_value+"&sortmethod="+sort_method+"&page="+page,
			data : "column_array="+column_array+"&argument_array="+argument_array+"&entries_per_page="+entries_per_page+"&get_array="+json_get_array,
			success : function(data)
			{
				var last_height = $("#" + css_main_id).height();
				$("#" + css_main_id).height("auto");
				$("#" + css_main_id).html(data);
				var new_height = $("#" + css_main_id).height();
				$("#" + css_main_id).children().remove();
				$("#"+css_main_id).css({"display":"block","display":"table-row-group"});
				$("#" + css_main_id).append("<div class='ListLoadingContents'></div>"); // element must not be empty to animate height
				
				if (new_height != last_height) 
				{
					if($.browser.msie)
					{
						if($.browser.version == 7.0 || $.browser.version == 9.0)
						{ //we got an ie version that does not support tbody animation							
							$("#" + css_main_id).html(data);
							
							make_resizable();
							return true;
						}
					}
					$("#" + css_main_id).height(last_height);
					$("#" + css_main_id).animate(
					{
						"height" : new_height
					}
					, "fast", function()
					{
						$("#" + css_main_id).html(data);
						make_resizable();
					});
				} 
				else 
				{
					$("#" + css_main_id).height(last_height)					
					$("#" + css_main_id).html(data);
					make_resizable();
				}
			}
		});
	}
	load_content(sort_value, sort_method, page);

		
	function make_resizable()
	{			
		
		if($(".ListTable").find(".ui-resizable-e").length > 0)
		{
			$(".ListTable").dynamicTable("reinit");
			return false;
		}
		
		/*
		$(document).keyup(function(evt)
		{
			if(evt.which === 49)
			{
				open_column_menu();
			}
			
		}); */
		
		var num_cols = $(".ListTable > thead > tr > th").size();
		
		var sticky = [0]; //first column is always sticky
		var notResizable = [num_cols - 1]; //last column is never resizable
		
		for (var int = 1; int < num_cols; int++) 
		{
			var column = $(".ListTable > thead > tr > th").get(int);		
			
			if($.browser.msie)
			{ //attr("width") causes IE to return numeric values, so we cannot distinguish px from em
				var width = $(column)[0].currentStyle["width"];
			}
			else
			{
				var width = $(column).attr("width");
			}		
			
			if(width !== undefined && width !== "")
			{
				if(width.indexOf("px") !== -1)
				{
					sticky.push(int);
					
					//if the last column is sticky, the column before that is not resizable
					if(int === num_cols - 1)
					{
						notResizable.push(int - 1);
					}
				}
			}
		}

		for (var int2 = 0; int2 < sticky.length; int2++)
		{
			var sticky_col = sticky[int2];
			
			var th = $(".ListTable").find("th:nth-child("+(sticky_col + 1)+")"), 
			width = $(th).width()
	
			//width_dif is padding + border in ie7, just padding in all other browsers
			var width_dif = $(th).outerWidth() - $(th).width(); 
	
			if($.browser.msie && $.browser.version == 7.0)
			{//ie box model fix
				var border = parseInt($(th).css("border-left-width"), 10);
				if(border)
				{
					var padding = parseInt($(th).css("padding-left"), 10) + parseInt($(th).css("padding-right"), 10)
					width_dif = width_dif - border - padding;
				}
				else
				{
					width_dif = 0;
				}
			}
			
			$(th).width(width + width_dif);
			var real_width = $(th).width() - width_dif;
			var border = width - real_width;
			$(th).width(width + width_dif - border); 
		}
		
		$(".ListTable").dynamicTable({
			"sticky": sticky,
			"notResizable": notResizable,
			"settings" : {
				rulerCSS: {
					"background-color": "#669acc"
				},
				handleCSS: {
					"background-color": "#669acc"
				}
			}
		});
		
		var column_menu_trigger = $("<div id='ColumnMenuTrigger'><img src='images/icons/visible.png' alt=''/></div>")
			.css("float", "left")
			.unbind("click")
			.click(function()
			{
				if($(this).hasClass("columnMenuOpen"))
				{
					close_column_menu();
					$(this).removeClass("columnMenuOpen");
				}
				else
				{
					open_column_menu();
					$(this).addClass("columnMenuOpen");
				}
			})
			.appendTo(".ContentBoxBeginTitle");
		
		tooltip("ColumnMenuTrigger", "Toggle Display Options");

		
		function open_column_menu()
		{
			var position = $("#ColumnMenuTrigger").position();
			
			var column_menu = $("<div id='ColumnMenu'></div>");
	
			for (var int = 1; int < num_cols; int++) 
			{
				if($.inArray(int, sticky) !== -1)
				{
					continue;
				}
	
				var column = $(".ListTable > thead > tr > th").get(int);
				
				if($.inArray(int, notResizable) === -1)
				{
					if($(column).children(".ResizableColumnHelper").length > 0)
					{
						var div = $(column).children(".ResizableColumnHelper");
						if($(div).children("a:first").length > 0)
						{
							var column_text = $(column).children(".ResizableColumnHelper").children("a:first").text();
						}
						else
						{
							var column_text = $(column).children(".ResizableColumnHelper").text();
						}
					}
				}
				else
				{
					var div = $(column).children("div:first");
					if($(div).children("a:first").length > 0)
					{
						var column_text = $(column).children("div:first").children("a:first").text();
					}
					else
					{
						var column_text = $(column).children("div:first").text();
					}
				}
				
				var checked = "checked='checked'";
				if(!$(column).is(":visible") || $(column).width() === 0)
				{
					checked = "";
				}
				
				var label = $("<div class='ColumnMenuEntryLabel'>"+column_text+"</div>");
				
				var checkbox = $("<input type='checkbox' class='ColumnMenuEntryCheckbox' name='' value='' "+checked+"></input>")
					.click(function(event)
					{
						if($(".ListTable").dynamicTable("isAnimating"))
						{
							event.preventDefault();
						}
						
						var num_checked = 0;
						$(".ColumnMenuEntryCheckbox").each(function()
						{
							if($(this).is(":checked"))
							{
								num_checked++;
							}
						});
						
						if(num_checked > 0)
						{
							$(".ListTable").dynamicTable("toggle", $(this).parent().data("columnIndex"));
						}
						else
						{
							event.preventDefault();
						}
					});
				
				$("<div class='ColumnMenuEntry'></div>")
					.data("columnIndex", int)
					.append(label)
					.append(checkbox)
					.appendTo(column_menu);
				
				$(column_menu).dialog(
				{
					"title": "Change column visibility",
					"close": function()
					{
						$("#ColumnMenuTrigger").removeClass("columnMenuOpen");
					},
					"buttons": [
					{
						text: "OK",
				        click: function()
				        { 
				        	close_column_menu() 
				        }
					}]
				});
			}
		}
		
		function close_column_menu()
		{
			$("#ColumnMenu").dialog("close");
			$("#ColumnMenu").remove();
		}

	} 

	function check_array(sort_value)
	{
		if (sort_array != undefined)
		{
			var sort_array_length = sort_array.length;

			if (sort_array_length >= 1)
			{
				for (var i = 0; i <= sort_array_length - 1; i++)
				{
					if (sort_array[i][0] == sort_value)
					{
						return i;
					}
				}
				return -1;
			}
			else
			{
				return -1;
			}
		}
		else
		{
			return -1;
		}
	}

	function change_symbol(id, symbol) 
	{
		$("." + css_main_id + "Column").each(function() 
		{
			var local_id = $(this).attr("id");
			var img = $("#" + local_id).find("img");
			
			if (local_id == id) 
			{
				if (symbol == "upside") {
					$(img).attr("src", "images/upside.png");
				} else {
					$(img).attr("src", "images/downside.png");
				}
			} 
			else 
			{
				$(img).attr("src", "images/nosort.png");
			}
		});
	}

	this.reinit_sort_handler();
}