/*
 * version: 0.4.0.0
 * author: Roman Konertz <konertz@open-lims.org>
 * copyright: (c) 2008-2011 by Roman Konertz
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

List = function(ajax_handler, ajax_run, ajax_count_run, argument_array, css_main_id, entries_per_page, row_array)
{
	var sort_array = new Array();
	
	var sort_value = "";
	var sort_method = "";
	var page = 1;
	
	var get_array = getQueryParams(document.location.search);
	
	var parsed_row_array = $.parseJSON(row_array);
	var colspan = parsed_row_array.length;
	
	var number_of_entries = 0;
	var number_of_pages = 0;
	
	this.reload = function()
	{
		load_content(sort_value, sort_method, page);
	}
	
	function count_entries()
	{
		if (ajax_handler.indexOf("?") == -1) 
		{
			var post_ajax_handler = ajax_handler + "?session_id="+ get_array['session_id'] + "&run="+ajax_count_run;
		} 
		else 
		{
			var post_ajax_handler = ajax_handler + "&session_id="+ get_array['session_id'] + "&run="+ajax_count_run;
		}
		
		$.ajax(
		{
			type: "POST",
			url: post_ajax_handler,
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
					number_of_pages = Math.ceil(number_of_entries/entries_per_page);
				}
			}
		});
	}
	
	function load_content(sort_value, sort_method, local_page)
	{
		var local_height = $("#"+css_main_id).height();
		
		page = local_page;
		
		$("#"+css_main_id).contents().detach();
		
		var margin = parseInt(local_height / 2);
		margin = Math.floor(margin);
		margin -= 8;
		
		$("#"+css_main_id).append("<tr><td colspan='"+colspan+"'><div style='text-align:center; margin-top:"+margin+"px;'><img src='images/animations/loading_circle_small.gif' alt='Loading...' /></div></td></tr>");
		
		$("#"+css_main_id).height(local_height);
		
		count_entries();
		
		$.ajax(
		{
			type: "GET",
			url: "core/modules/base/list.ajax.php",
			data: "username="+get_array['username']+"&session_id="+get_array['session_id']+"&run=get_page_bar&page="+page+"&number_of_pages="+number_of_pages+"&css_page_id="+css_main_id+"Page",
			async: false,
			success: function(data)
			{
				$("#"+css_main_id+"PageBar").html(data);
				
				$("."+css_main_id+"Page").each(function()
				{
					$(this).click(function()
					{
						var id = $(this).attr("id");
						page = id.replace(css_main_id+"Page","");
						load_content(sort_value, sort_method, page);
					});
				}); 
			}
		});
		
		
		$.ajax(
		{
			type: "GET",
			url: "core/modules/base/list.ajax.php",
			data: "username="+get_array['username']+"&session_id="+get_array['session_id']+"&run=get_page_information&number_of_entries="+number_of_entries+"&number_of_pages="+number_of_pages+"",
			async: false,
			success: function(data)
			{
				$("#"+css_main_id).parent().parent().children(".ListPageInformation").html(data);
			}
		});
		
		if (ajax_handler.indexOf("?") == -1) 
		{
			var post_ajax_handler = ajax_handler + "?username="+get_array['username']+"&session_id="+get_array['session_id']+"&run="+ajax_run+"&sortvalue="+sort_value+"&sortmethod="+sort_method+"&page="+page;
		} 
		else 
		{
			var post_ajax_handler = ajax_handler + "&username="+get_array['username']+"&session_id="+get_array['session_id']+"&run="+ajax_run+"&sortvalue="+sort_value+"&sortmethod="+sort_method+"&page="+page;
		}
		
		$.ajax(
		{
			type: "POST",
			url: post_ajax_handler,
			data: "row_array="+row_array+"&argument_array="+argument_array+"&entries_per_page="+entries_per_page+"",
			success: function(data)
			{	
				$("#"+css_main_id).contents().detach();
				$("#"+css_main_id).append(data);
				$("#"+css_main_id).height("auto");
				
//				var new_height = $("#"+css_main_id).height();
//				
//				
//				$("#"+css_main_id).height(local_height);
//				
//				if(new_height >= local_height)
//				{
//					$("#"+css_main_id).fadeIn().animate({height:new_height},5000);
//				}
//				else
//				{
//					$("#"+css_main_id).slideUp("slow");
//				}
//				
//				
//				console.log(local_height+" "+new_height);
				
			}
		});
	}
	
	load_content(sort_value, sort_method, page);
	
	function check_array(sort_value)
	{
		if (sort_array != undefined)
		{
			var sort_array_length = sort_array.length;
			
			if (sort_array_length >= 1)
			{
				for (var i=0; i<=sort_array_length-1; i++)
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
		$("."+css_main_id+"Row").each(function()
		{
			var local_id = $(this).attr("id");
			
			if (local_id == id)
			{
				if (symbol == "upside")
				{
					$("#"+local_id+" > a > img").attr("src","images/upside.png");
				}
				else
				{
					$("#"+local_id+" > a > img").attr("src","images/downside.png");
				}
			}
			else
			{
				$("#"+local_id+" > a > img").attr("src","images/nosort.png");
			}
		});
	}
	
	$("."+css_main_id+"Row").each(function()
	{
		$(this).click(function()
		{			
			var id = $(this).attr("id");
			sort_value = id.replace(css_main_id+"Row","");
			
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