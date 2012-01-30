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
					});
				} 
				else 
				{
					$("#" + css_main_id).height(last_height)
					$("#" + css_main_id).html(data);
				}
			}
		});
	}
	load_content(sort_value, sort_method, page);

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