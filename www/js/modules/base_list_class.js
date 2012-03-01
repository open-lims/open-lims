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
		var table_width = $(".ListTable > thead").width();
		
		var all_widths = new Array();
		
		var fixed_widths = new Array();
		
		if($(".resizable").size() === 0)
		{
			init();
			init_column_options();
		}
		

//		$(".ListTableOptionsEntryCheckbox").not(":checked").each(function(){
//			var index = $(this).attr("name");
//			
//			var header_col = $(".ListTable > thead > tr > th").get(index);
//			$(header_col).hide();
//			$(".ListTable > tbody > tr").each(function(){
//				var body_col = $(this).children("td").get(index);
//				$(body_col).hide();
//			});
//			$("#dragHandle"+index).hide();
//		});

		
		function init()
		{			
			var num_cols = $(".ListTable > thead > tr > th").size();

			var free_width = table_width;
			
			for (var int = 0; int < num_cols; int++) 
			{
				var column = $(".ListTable > thead > tr > th").get(int);

				if($(column).attr("width") === "")
				{
					$(column).addClass("resizable");
				}
				else if($(column).attr("width").indexOf("%") != -1)
				{
					$(column).addClass("presetWidth");
					$(column).addClass("resizable");
				}
				else
				{
					free_width -= $(column).outerWidth();
					
					fixed_widths.push(int);
				}
			}
			var fixed_width = 0;
			
			$(".presetWidth").each(function(){
				var percentage = $(this).attr("width").replace("%","");
				var absolute_width = Math.floor(free_width * percentage / 100);
				$(column).attr("width", absolute_width);
				
				fixed_width += absolute_width;
			});
			free_width -= fixed_width;
			
			free_width -= $(".resizable").size() * 8;
			
			var num_cols_to_resize = $(".resizable").size() - $(".presetWidth").size();

			var free_column_width = Math.floor(free_width / num_cols_to_resize) + 5;

			$(".resizable").each(function(){
				if(!$(this).hasClass("presetWidth"))
				{
					$(this).width(free_column_width);
				}
				
				var col_index = $(this).parent().children().index($(this));
				
				var head_col_text = $(this).html();			
				var head_col_text_calc = "<span>" + head_col_text + "</span>";
				
				$(this).html(head_col_text_calc);
				var head_col_min_width = $(this).find("span").width() + 1;
				$(this).html(head_col_text);

		
				if($(this).width() < head_col_min_width)
				{
					$(this).addClass("tooSmall");
				}
				
				if(col_index !== num_cols - 1)
				{
					init_slider(col_index, head_col_min_width);
				}
				else
				{
					var helper_div = $("<div class='ResizableColumnHelper'>"+head_col_text+"</div>")
						.data("minWidth", head_col_min_width);
					$(this).html(helper_div);
				}
				
			});
			
			$(".tooSmall").each(function(){
				var min_width = $(this).children().data("minWidth");
				var col_index = $(this).children().data("draggedColIndex");
			
				var diff = min_width - $(this).width();
		
				var next_col = $(".ListTable > thead > tr > th").get(col_index + 1);
				$(next_col).width($(next_col).width() - diff);

				$(this)
					.removeClass("tooSmall")
					.width(min_width);
			});
			
			$(".ListTable > tbody > tr > td").each(function(){
				
				var index = $(this).parent().children().index($(this));
								
				if(fixed_widths.indexOf(index) !== -1)
				{
					return true;
				}
				
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

				var body_col_text = $(containing_element).html();
				var body_col_text_calc = "<span id='textMeasure'>" + body_col_text + "</span>";
				
				$(containing_element).html(body_col_text_calc);
				var body_col_text_width = $(containing_element).find("span").width();
				
				$(containing_element).html(body_col_text);
				
				$(this)
					.data("textWidth", body_col_text_width)
					.data("originalText", body_col_text);
				
				resize_text(this, $(this).width());
			});
		}
		
		function init_slider(int, min_width)
		{
			var head_col = $(".ListTable > thead > tr > th").get(int);
			
			var handle = $("<span></span>")
				.css({
					"background-color": "#669acc",
					"width": "1px",
					"height": "100%",
					"right": "0px",
					"position": "absolute"
				});
			
			var helper_div = $("<div class='ResizableColumnHelper'>"+$(head_col).html()+"</div>")
				.append(handle)
				.data("draggedColIndex", int)
				.data("minWidth", min_width)
				.resizable({
					handles: "e",
					minWidth: min_width,
					start:  function(event, ui)
					{
						$(this)
							.css("outline","dotted #669acc 1px")
							.data("originalWidth", $(head_col).width());
						
//						for(var int = 0; int < all_widths.length; int++)
//						{
//							var col = $(".ListTable > thead > tr > th").get(int);
//							$(col).width(all_widths[int]);
//							$(col).children().width("100%");
//						}				
					},
					resize: function(event, ui)
					{						
						var dragged_col_index = $(this).data("draggedColIndex");
						
						var next_col = $(".ListTable > thead > tr > th").get(dragged_col_index + 1);
						
						var old_width = $(this).data("originalWidth");
						var new_width = $(this).width();
						
						var diff = old_width - new_width;
												
						var next_col_old_width = $(next_col).children().data("originalWidth");					
						var next_col_new_width = next_col_old_width + diff;
						
						$(head_col).width(new_width);
						$(next_col).width(next_col_new_width);

						var next_col_width = $(next_col).width();
						var next_col_min_width = $(next_col).children().data("minWidth");
						
						if(next_col_width < next_col_min_width)
						{
							$(this).trigger("mouseup"); 	
							var diff2 = next_col_min_width - next_col_width;
							$(head_col).width($(head_col).width() - diff2);
							$(next_col).width(next_col_min_width);
						}
						
						//if the mouse is moved very fast, the code above will not trigger
						if(new_width > old_width + next_col_width)
						{
							$(this).trigger("mouseup"); 
							$(head_col).width(old_width);
							$(next_col).width(next_col_old_width);
						}
						
						$(".ResizableColumnHelper").each(function(){
							$(this).width($(this).parent().width());
						});
						
						$(this).data("originalWidth", $(head_col).width());
						$(next_col).children().data("originalWidth", $(next_col).width());
						
						$(".ListTable > tbody > tr").each(function(){
							
							var td_in_this_col = $(this).children().get(dragged_col_index);
							var td_in_next_col = $(this).children().get(dragged_col_index + 1);
							resize_text(td_in_this_col, new_width)
							resize_text(td_in_next_col, next_col_width)
						});						
					},
					stop:  function(event, ui)
					{
						$(this).css("outline","none");

						all_widths = new Array();
						$(".ListTable > thead > tr > th").each(function(){
							all_widths.push($(this).width());
						});
					}
				});
			$(head_col).html(helper_div);
			
			$(helper_div).children(".ui-resizable-handle").css("right","0");
		}
		
		function resize_text(td, new_width)
		{
			var containing_element;
			if($(td).find("a").length > 0)
			{
				containing_element = $(td).find("a");
			}
			else if($(td).find("div").length > 0)
			{
				containing_element = $(td).find("div");
			}
			else
			{
				containing_element = $(td);
			}
			
			var text = $(td).data("originalText");
			var text_width = $(td).data("textWidth");
			
			if(text_width > new_width)
			{
				var text_calc = "<span id='textMeasure'>" + text + "</span>";
				
				$(containing_element).html(text_calc);
				
				while(text.length > 0 && $("#textMeasure").width() >= new_width)
				{
					text = text.substr(0, text.length - 1);
					$("#textMeasure").html(text+"...");
				}
				$(containing_element).html(text+"...");
				
			}
			else if(text_width < new_width)
			{
				$(containing_element).html(text);
			}
		}
	 		
		function init_column_options()
		{
			var options_container = null;
			
			if(window.location.href.indexOf("nav=data") === -1)
			{
				options_container = $(".ListTable > thead > tr > th:first");
			}
			else
			{
				options_container = $(".ListTable > thead > tr > th:nth-child(2)");
			}
			
			$(options_container)
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
			
			var dialog_pos_x = $("#ListTableOptionsTrigger").position().left;
			var dialog_pos_y = $("#ListTableOptionsTrigger").position().top + $("#ListTableOptionsTrigger").height();
			
			var options = $("<div id='ListTableOptions'></div>")
				.css({
					"left": dialog_pos_x,
					"top": dialog_pos_y + 3
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
							if($(".ListTableOptionsEntryCheckbox:checked").size() >= 0)
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
			
			var corner_pos_x = $("#ListTableOptionsTrigger").position().left + $("#ListTableOptionsTrigger").outerWidth() - 3;
			var corner_pos_y = $("#ListTableOptionsTrigger").position().top + 9;
			
			var corner_container = $("<div id='ListTableOptionsCornerContainer'></div>")
				.css(
				{
					"position": "absolute",
					"top": corner_pos_y,
					"left": corner_pos_x,
					"z-index": "96",
					"width": "13px",
					"height": "13px", 
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
			
			var col_to_hide = $(".ListTable > thead > tr > th").get(i);
			var col_to_add_width_to = $(".ListTable > thead > tr > th").get(i - 1);
			
			var width = $(col_to_hide).width();
			
			$(col_to_hide)
				.animate({
					"width": 0
				}, 500, function(){		
					
			
					
					$(this)
//						.css("padding", 0);
						.hide();
					
					$(".ListTable > tbody > tr").each(function(){
						var body_col = $(this).children("td").get(i);
						$(body_col).hide();
					});
					
	
				});
			
			$(col_to_hide).children()
//				.data("hidden")
				.data("originalWidth", width)
				.data("widthAddedTo", i - 1);
			
			
			$(col_to_add_width_to).animate({
				"width": $(col_to_add_width_to).width() + width,
			}, 500, function(){

				var remaining_width = $(col_to_hide).outerWidth();
				var col_to_add_width_to_final_width = $(this).width() + remaining_width;
				$(this).width(col_to_add_width_to_final_width);
				
//				$(".ListTable > tbody > tr").each(function(){
//					var body_col = $(this).children("td").get(i - 1);
//					resize_text(body_col, new_width)
//				});
				
			});
			
			
			
			
//			var header_col = $(".ListTable > thead > tr > th").get(i);
//			 
//			$(header_col).fadeOut("fast");
//			$(".ListTable > tbody > tr").each(function(){
//				var body_col = $(this).children("td").get(i);
//				$(body_col).fadeOut("fast");
//			});
//			$("#dragHandle"+i).fadeOut("fast");
//
////			for(var int = 0; int < fixed_widths.length; int++)
////			{
////				var index = fixed_widths[int][0];
////				var width = fixed_widths[int][1];
////				
////				var col = $(".ListTable > thead > tr > th").get(index);
////				$(col).width(width);
////			}
		}
		
		function show_column(i)
		{
			var col_to_show = $(".ListTable > thead > tr > th").get(i);
			var col_to_remove_width_from_index = $(col_to_show).children().data("widthAddedTo");
			var col_to_remove_width_from = $(".ListTable > thead > tr > th").get(col_to_remove_width_from_index);
						
			var original_width = $(col_to_show).children().data("originalWidth");
				
			$(col_to_show)
				.show()
				.animate({
					"width": original_width
				}, 500, function(){		
					
				});
			
			$(".ListTable > tbody > tr").each(function(){
				var body_col = $(this).children("td").get(i);
				$(body_col).show();
			});
			
			$(col_to_remove_width_from)
				.width($(col_to_remove_width_from).width() - 2)
				.animate({
					"width": $(col_to_remove_width_from).width() - original_width
				}, 500, function(){		
					var new_width = $(this).width();
					$(this).width(new_width);
				});
			
//			var width = $(col_to_show).data("originalWidth");
//			$(col_to_show)
////			.show();
////			.css("padding","2px")
//			.animate({
//				"width": width,
//			}, 500);
////			
//			$(col_to_remove_width_from).animate({
//				"width": $(this).width() - width
//			}, 500);
			
			
			
//			 var header_col = $(".ListTable > thead > tr > th").get(i);
//			 $(header_col).fadeIn("slow");
//			 $(".ListTable > tbody > tr").each(function(){
//				 var body_col = $(this).children("td").get(i);
//				 $(body_col).fadeIn("slow");
//				 	
//			 });
//			 $("#dragHandle"+i).fadeIn("slow");
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