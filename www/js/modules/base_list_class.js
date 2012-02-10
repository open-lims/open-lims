/*
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
			success : function(data) {
				$("#" + css_main_id + "PageBar").html(data);

				$("." + css_main_id + "Page").each(function() {
					$(this).bind("click",function() {
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
			success : function(data) {
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
							return true;
						}
					}
					$("#" + css_main_id).height(last_height);
					$("#" + css_main_id).animate({
						"height" : new_height
					}, "fast", function() {
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
		var table_width = 744; //$(".ListTable > thead").width() returns an incorrect width if columns contain long text
		
		if($(".resizable").size() === 0)
		{
			init();
		}
		
		$(".ListTableOptionsEntryCheckbox").not(":checked").each(function(){
			var index = $(this).attr("name");
			
			var header_col = $(".ListTable > thead > tr > th").get(index);
			$(header_col).hide();
			$(".ListTable > tbody > tr").each(function(){
				var body_col = $(this).children("td").get(index);
				$(body_col).hide();
			});
			$("#dragHandle"+index).hide();
		});
		
		$(".resizable").each(function(){
			var width = $(this).data("initialWidth");
			var index = $(this).data("colIndex");
			
			$(".ListTable > tbody > tr").each(function(){
				var col = $(this).children("td").get(index);
				
				if($(col).width() >= width)
				{
					shorten_text($(col), width, index);
				}
			});
		});	
		
		function init()
		{
			var num_cols = $(".ListTable > thead > tr > th").size();

			var free_width = table_width;
			var columns_to_resize = 0;
			var preset_width_columns = 0;
			
			for (var int = 0; int < num_cols; int++) 
			{
				var column = $(".ListTable > thead > tr > th").get(int);

				if($(column).attr("width") === "")
				{
					columns_to_resize++;
					init_slider(int);
					$(column).addClass("resizable");
				}
				else if($(column).attr("width").indexOf("%") != -1)
				{
					preset_width_columns++;
					columns_to_resize++;
					var percentage = $(column).attr("width").replace("%","");
					var absolute_width = Math.floor(table_width * percentage / 100);				
//					$(column).width(absolute_width);
//					$(column).removeAttr("width");
					$(column).attr("width", absolute_width);
					free_width -= absolute_width;
					$(column).addClass("presetWidth");
					$(column).addClass("resizable");
					init_slider(int);
				}
				else
				{
					free_width -= $(column).outerWidth();
					$(column).width("1%"); //inhibits resize of outer columns
				}
			}
			
			var num_cols_to_resize = $(".resizable").size() - $(".presetWidth").size();
			
			var free_column_width = Math.floor(free_width / num_cols_to_resize);
			
			$(".ListTable > tbody > tr > td").hide();

			$(".resizable").each(function(){
				if(!$(this).hasClass("presetWidth"))
				{
					$(this).width(free_column_width);
				}
				
				var width = $(this).width();
				var col_index = $(this).parent().children().index($(this));
				
				$(this).data("initialWidth", width);
				$(this).data("colIndex", col_index);
			});
			
			$(".ListTable > tbody > tr > td").show();

			init_column_options();
		}
		
		function init_slider(int)
		{
			var head_col = $(".ListTable > thead > tr > th").get(int);
			
			var head_col_text = $(head_col).html();			
			var head_col_text_calc = "<span>" + head_col_text + "</span>";
			
			$(head_col).html(head_col_text_calc);
			var head_col_text_width = $(head_col).find("span").width();
			
			var handle = $("<span></span>")
				.css({
					"background-color": "#669acc",
					"width": "2px",
					"height": "100%",
					"right": "0",
					"position": "absolute"
				});
			
			var html_div = $("<div>"+head_col_text+"</div>")
				.append(handle)
				.data("originalWidth", $(head_col).width())
				.data("draggedColIndex", int)
				.resizable({
					handles: "e",
					minWidth: head_col_text_width,
					containment: $(".ListTable > thead > tr:first"),
					start:  function(event, ui)
					{
						$(html_div).css("outline","dotted #669acc 2px");
					},
					resize: function(event, ui)
					{
						var dragged_col_index = $(this).data("draggedColIndex");
						
						var old_width = $(this).data("originalWidth");
						var new_width = $(this).width();
							
						
						$(head_col).width(new_width); //this is where the magic happens
						
						if($(".ListTable").width() > table_width)
						{
							//have to invoke mouseup as resizable currently has no valid cancel event
							new_width = old_width;
							$(head_col).width(new_width);
							$(html_div).trigger("mouseup"); 
						}
						
						$(html_div).width("100%");
						
						lengthen_text(new_width, dragged_col_index);
											
						if($(head_col).width() >= new_width)
						{
							$(".ListTable > tbody > tr").each(function(){
								var body_col = $(this).children("td").get(dragged_col_index);
								shorten_text(body_col, new_width, dragged_col_index);
							});
						}
						
						$(this).data("originalWidth", new_width);
					},
					stop:  function(event, ui)
					{
						$(html_div).css("outline","none");
					}
				});
			$(head_col).html(html_div);
			
			$(html_div).children(".ui-resizable-handle").css("right","0");
		}
		
		function shorten_text(body_col, new_width, index)
		{
			var containing_element;
			if($(body_col).find("a").length > 0)
			{
				containing_element = $(body_col).find("a");
			}
			else if($(body_col).find("div").length > 0)
			{
				containing_element = $(body_col).find("div");
			}
			else
			{
				containing_element = $(body_col);
			}

			var body_col_text = $(containing_element).html();
			var body_col_text_calc = "<span id='textMeasure'>" + body_col_text + "</span>";
			
			$(containing_element).html(body_col_text_calc);
			var body_col_text_width = $(containing_element).find("span").width();
		
			if(body_col_text_width >= new_width)
			{
				var text = body_col_text;
				
				if($(body_col).data("originalText") === undefined)
				{
					$(body_col).data("originalText", body_col_text);
					$(body_col).data("originalTextWidth", body_col_text_width);
				}
				
				if(text.substr(text.length - 3, text.length - 1) === "...")
				{
					text = text.substr(0, text.length - 3);
				}
				
				while(text.length > 0 && $("#textMeasure").width() >= new_width)
				{
					text = text.substr(0, text.length - 1);
					$("#textMeasure").html(text+"...");
				}
				$(containing_element).html(text+"...");
				
				$(body_col).addClass("truncated"+index);
			}
			else
			{
				$(containing_element).html(body_col_text);
				$(body_col).removeClass("truncated"+index);
			}
		}
		
		function lengthen_text(new_width, index)
		{
			$(".truncated"+index).each(function(){
				
				var original_text_width = $(this).data("originalTextWidth");
				var original_text = $(this).data("originalText");
				
				var containing_element;
				if($(this).find("a").length > 0)
				{
					containing_element = $(this).find("a");
				}
				else if($(this).find("div").length > 0)
				{
					containing_element = $(this).find("div");
				}
				else
				{
					containing_element = $(this);
				}
				
				var current_text = $(containing_element).html();
				
				var current_text_calc = "<span id='textMeasure'>" + original_text + "</span>";
				$(containing_element).html(current_text_calc);

				var truncated_width = $("#textMeasure").width();
				
				var new_text = original_text;

				if(new_width >= $("#textMeasure").width())
				{
					$(containing_element).html(original_text);
					$(this).removeClass("truncated");
				}
				else
				{
					while($("#textMeasure").width() > original_text_width) //< new_width)
					{
						new_text = new_text.substr(0, new_text.length - 1);
						$("#textMeasure").html(new_text+"...");
					}
					$(containing_element).html(new_text+"...");
				}
			});
		}
		
		function init_column_options()
		{
			
			var dialog_pos_x = $(".ListTable").position().left;
			var dialog_pos_y = $(".ListTable").position().top + $(".ListTable > thead > tr").height();
			
			var options = $("<div id='ListTableOptions'></div>")
				.css({
					"left": dialog_pos_x,
					"top": dialog_pos_y
				})
				.appendTo("#main")
				.hide();
			
			$(".ListTable > thead > tr > th").each(function(i){
				
				$(this).children().css("z-index", "98");
				
				var text = $(this).children().first().text();
				if(text !== "")
				{
					var checkbox = $("<input type='checkbox' class='ListTableOptionsEntryCheckbox' name='"+i+"' value='' checked=''></input>")
						.css({
							"float": "right",
							"cursor": "default",
							"margin-top": "2px"
						})
						.click(function(){
							if($(".ListTableOptionsEntryCheckbox:checked").size() >= 2)
							{
								if($(this).attr("checked"))
								{
									show_column($(this).attr("name"));
								}
								else
								{
									hide_column($(this).attr("name"));
								}
							}
							else
							{
								$(this).attr("checked","checked");
							}						
						});
					$("<div class='ListTableOptionsEntry'>"+text+"</div>")
						.css("height", "16px")
						.css("padding-top", "2px")
						.append(checkbox)
						.appendTo(options);
				}
			});
			
			$(".ListTable > thead > tr > th:first")
				.html("<img src='images/icons/visible.png' alt=''/>")
				.attr("id","ListTableOptionsTrigger")
				.click(function(){
					if($(this).hasClass("active"))
					{
						close_display_options_dialog();
					}
					else
					{
						open_display_options_dialog();
					}
				});
			
			tooltip("ListTableOptionsTrigger", "Show Display Options");
			
			var corner_pos_x = $("#ListTableOptionsTrigger").position().left + $("#ListTableOptionsTrigger").outerWidth() - 3;
			var corner_pos_y = $("#ListTableOptionsTrigger").position().top + 9;
			
			var corner_container = $("<div id='ListTableOptionsCornerContainer'></div>")
				.css(
				{
					"position": "absolute",
					"top": corner_pos_y,
					"left": corner_pos_x,
					"z-index": "96",
					"width": "12px",
					"height": "12px", 
					"background-color": "white",
					"margin": "0",
					"padding": "0"
				})
				.hide()
				.appendTo("#main");
			
			var corner = $("<div id='ListTableOptionsCorner'></div>")
				.css(
				{
					"position": "absolute",
					"top": corner_pos_y,
					"left": corner_pos_x + 1,
					"z-index": "97",
					"width": "10px",
					"height": "10px"
				})
				.hide()
				.appendTo("#main");			
		}
		
		function open_display_options_dialog()
		{
			$("#ListTableOptionsTrigger")
				.css({
					"border":"solid #669acc 2px",
					"border-bottom":"solid white 1px"
				})
				.addClass("active");
		
			$("#ListTableOptionsCornerContainer").show();
			$("#ListTableOptionsCorner").show();
			
			$("#ListTableOptions").slideDown(200, function(){			
				
				$("body").bind("click",function(evt){
					if(!$(evt.target).hasClass("ListTableOptionsEntry") && !$(evt.target).hasClass("ListTableOptionsEntryCheckbox"))
					{
						close_display_options_dialog();
						$("body").unbind("click");
					}
				});
			
			});
			
			tooltip("ListTableOptionsTrigger", "Hide Display Options");
		}
		
		function close_display_options_dialog()
		{
			$("#ListTableOptionsTrigger")
			.css({
				"border":"solid white 2px",
				"border-bottom": "1px #C0C0C0 dotted"
			})
			.removeClass("active");
		
			$("#ListTableOptionsCornerContainer").hide();
			$("#ListTableOptionsCorner").hide();
			
			$("#ListTableOptions").hide();
	
			tooltip("ListTableOptionsTrigger", "Show Display Options");
		}
	
		function hide_column(i)
		{
			 var header_col = $(".ListTable > thead > tr > th").get(i);
			 $(header_col).fadeOut("fast");
			 $(".ListTable > tbody > tr").each(function(){
				 var body_col = $(this).children("td").get(i);
				 $(body_col).fadeOut("fast");
			 });
			 $("#dragHandle"+i).fadeOut("fast");
		}
		
		function show_column(i)
		{
			 var header_col = $(".ListTable > thead > tr > th").get(i);
			 $(header_col).fadeIn("slow");
			 $(".ListTable > tbody > tr").each(function(){
				 var body_col = $(this).children("td").get(i);
				 $(body_col).fadeIn("slow");
				 	
			 });
			 $("#dragHandle"+i).fadeIn("slow");
		}
	}

	function check_array(sort_value) {
		if (sort_array != undefined) {
			var sort_array_length = sort_array.length;

			if (sort_array_length >= 1) {
				for ( var i = 0; i <= sort_array_length - 1; i++) {
					if (sort_array[i][0] == sort_value) {
						return i;
					}
				}
				return -1;
			} else {
				return -1;
			}
		} else {
			return -1;
		}
	}

	function change_symbol(id, symbol) 
	{
		$("." + css_main_id + "Column").each(
				function() {
					var local_id = $(this).attr("id");

					if (local_id == id) {
						if (symbol == "upside") {
							$("#" + local_id + " > a > img").attr("src",
									"images/upside.png");
						} else {
							$("#" + local_id + " > a > img").attr("src",
									"images/downside.png");
						}
					} else {
						$("#" + local_id + " > a > img").attr("src",
								"images/nosort.png");
					}
				});
	}


	this.reinit_sort_handler();
}