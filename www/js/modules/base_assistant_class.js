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

Assistant = function(ajax_handler, init_page, end_page, form_field_name)
{	
	var page = init_page;
	var max_page = init_page;
	var clickable_array;

	var original_error_message;
	
	if (init_page == 1)
	{
		set_active(1);
		set_clickable(1);
	}
	
	function set_visited(key)
	{
		$(".AssistantElement"+key+" > a > span.AssistantElementImage > img").attr("src", "images/numbers/"+key+"_dgrey.png");
	}
	
	function set_active(key)
	{
		$(".AssistantElement"+key+" > a > span.AssistantElementImage > img").attr("src", "images/numbers/"+key+"_blue.png");
		$(".AssistantElementImageActive").removeClass("AssistantElementImageActive");
		$(".AssistantElement"+key+" > a > span.AssistantElementImage > img").addClass("AssistantElementImageActive");
	}
	
	function set_clickable(key)
	{				
		$(".AssistantElement"+key+" > a").attr("href", "#");
		
		$(".AssistantElement"+key+"").click(function()
		{
			var data = read_data();
			
			if (data)
			{
				set_data(page, data);
			}
			
			var new_key = $(this).attr("class").replace(/AssistantElement/g, "");
				new_key = $.trim(new_key);
			var current_active_image = $(".AssistantElementImageActive").attr("src").replace("blue", "dgrey");
				
			$(".AssistantElementImageActive").attr("src", current_active_image);
			$(".AssistantElementImageActive").removeClass("AssistantElementImageActive");
			
			$(".AssistantElement"+new_key+" > a > span.AssistantElementImage > img").attr("src", "images/numbers/"+new_key+"_blue.png");
			$(".AssistantElement"+new_key+" > a > span.AssistantElementImage > img").addClass("AssistantElementImageActive");
			
			$("#AssistantContent").empty();
			$("#AssistantContent").html("<div id='AssistantLoading'><img src='images/animations/loading_circle_small.gif' alt='Loading...' /></div>");
			
			load_page(new_key);
		});
	}
	
	function load_page(new_page)
	{
		page = parseInt(new_page);
		
		$.ajax(
		{
			type: "GET",
			url: ajax_handler,
			data: "session_id="+get_array['session_id']+"&run=get_content&form_field_name="+form_field_name+"&page="+page,
			success: function(data)
			{
				if (data)
				{
					$("#AssistantContent").empty().append(data).slideDown("slow");
				}
			}
		});
	}
	
	function read_data()
	{
		var return_array = new Array();
		var array_counter = 0;
		
		$("."+form_field_name+":radio:checked").each(function()
		{
			return_array[array_counter] = new Array();
			return_array[array_counter][0] = $(this).attr("name");
			return_array[array_counter][1] = $(this).val();
			array_counter++;
		});
		
		$("."+form_field_name+":checkbox").each(function()
		{
			if ($(this).is(":checkbox:checked"))
			{
				return_array[array_counter] = new Array();
				return_array[array_counter][0] = $(this).attr("name");
				return_array[array_counter][1] = $(this).val();
				array_counter++;
			}
			else
			{
				return_array[array_counter] = new Array();
				return_array[array_counter][0] = $(this).attr("name");
				return_array[array_counter][1] = 0;
				array_counter++;
			}
		});
		
		$("."+form_field_name+"").each(function()
		{	
			if (($(this).is(":input") == true) && ($(this).is(":radio") == false) && ($(this).is(":checkbox") == false))
			{
				return_array[array_counter] = new Array();
				return_array[array_counter][0] = $(this).attr("name");
				return_array[array_counter][1] = $(this).val();
				array_counter++;
			}
		});
		
		return return_array;
	}
	
	function set_data(page, data)
	{
		var json_array = encodeURIComponent(JSON.stringify(data));
		
		$.ajax(
		{
			type: "POST",
			url: ajax_handler+"?session_id="+get_array['session_id']+"&run=set_data",
			async: false,
			data: "page="+page+"&data="+json_array,
			success: function(data)
			{
				
			}
		});
	}
	
	this.save_data = function()
	{
		var data = read_data();
		
		if (data)
		{
			set_data(page, data);
		}
	}
	
	this.load_next_page = function()
	{
		if (page < end_page)
		{			
			var data = read_data();
			var current_page = page;
			
			if (data)
			{
				set_data(page, data);
			}
			
			$("#AssistantContent").empty();
			$("#AssistantContent").html("<div id='AssistantLoading'><img src='images/animations/loading_circle_small.gif' alt='Loading...' /></div>");

			$.ajax(
			{
				type: "GET",
				url: ajax_handler,
				async: false,
				data: "session_id="+get_array['session_id']+"&run=get_next_page&page="+page,
				success: function(data)
				{
					page = parseInt(data);
				}
			});
						
			set_active(page);
			
			if (page > 1)
			{
				for (var i=current_page; i<=(page-1); i++)
				{
					set_visited(i);
				}
			}
			
			if (max_page < page)
			{
				set_clickable(page);
				max_page = page;
			}
			
			load_page(page);
		}
		else
		{
			$.ajax(
			{
				type: "GET",
				url: ajax_handler,
				data: "username="+get_array['username']+"&session_id="+get_array['session_id']+"&run=run",
				beforeSend: function()
				{
					$.blockUI({ message: $('#AssistantFinish'), css: { width: '275px' } }); 
					$('.blockUI.blockMsg').center();
				},
				success: function(data)
				{
					if ((data != '0') && ((data + '').indexOf("index.php?",0) == 0))
					{
						window.setTimeout('window.location = "'+data+'"',500);
					}
					else
					{
						if (!original_error_message)
						{
							original_error_message = $("#AssistantError").html();
						}
						
						if ((data + '').indexOf("EXCEPTION",0) == 0)
						{
							var exception_message = data.replace("EXCEPTION: ","");
							$("#AssistantError").html(exception_message);
							$.unblockUI();
							$("#AssistantError" ).dialog( "open" );
							return false;
						}
						else
						{
							$("#AssistantError").html(original_error_message);
							$.unblockUI();
							$("#AssistantError" ).dialog( "open" );
							return false;
						}
					}
				}
			});
		}
	}
	
	this.load_previous_page = function()
	{
		if (page > 1)
		{
			var data = read_data();
			var current_page = page;
			
			if (data)
			{
				set_data(page, data);
			}
			
			$("#AssistantContent").empty();
			$("#AssistantContent").html("<div id='AssistantLoading'><img src='images/animations/loading_circle_small.gif' alt='Loading...' /></div>");
			
			$.ajax(
			{
				type: "GET",
				url: ajax_handler,
				async: false,
				data: "session_id="+get_array['session_id']+"&run=get_previous_page&page="+page,
				success: function(data)
				{
					page = parseInt(data);
				}
			});
			
			set_active(page);
			
			if (page < end_page)
			{
				set_visited(current_page);
			}
			
			load_page(page);
		}
	}

}