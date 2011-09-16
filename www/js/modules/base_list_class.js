List = function(ajax_handler, ajax_run, argument_array, css_main_id, css_page_id, css_row_sort_id, row_array)
{
	var sort_array = new Array();
	
	var sort_value = "";
	var sort_method = "";
	var page = 1;
	
	var get_array = getQueryParams(document.location.search);
	
	function load_content(sort_value, sort_method, page)
	{
		$("#"+css_main_id).contents().detach();
		$("#"+css_main_id).append("<div id='AssistantLoading'><img src='images/animations/loading_circle_small.gif' alt='Loading...' /></div>");
		
		$.ajax(
		{
			type: "POST",
			url: ajax_handler+"?session_id="+get_array['session_id']+"&run="+ajax_run+"&sortvalue="+sort_value+"&sortmethod="+sort_method+"&page="+page,
			data: "row_array="+row_array+"&argument_array="+argument_array+"&css_page_id="+css_page_id+"&css_row_sort_id="+css_row_sort_id,
			success: function(data)
			{
				$("#"+css_main_id).contents().detach();
				$("#"+css_main_id).append(data);
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
	
	$("."+css_row_sort_id).each().live('click', function()
	{
		var id = $(this).attr("id");
		sort_value = id.replace(css_row_sort_id,"");
		
		var sort_method_key = check_array(sort_value);
		if (sort_method_key != -1)
		{
			if (sort_array[sort_method_key][1] == "asc")
			{	
				sort_array[sort_method_key][1] = "desc";
				sort_method = "desc";
			}
			else
			{
				sort_array[sort_method_key][1] = "asc";
				sort_method = "asc";
			}
		}
		else
		{
			sort_array_length = sort_array.length;
			
			sort_array[sort_array_length] = new Array();
			sort_array[sort_array_length][0] = sort_value;
			sort_array[sort_array_length][1] = "asc";
			sort_method = "asc";
		}
		
		load_content(sort_value, sort_method, page);
	});
	
	$("."+css_page_id).each().live('click', function()
	{
		var id = $(this).attr("id");
		page = id.replace(css_page_id,"");
		load_content(sort_value, sort_method, page);
	});
}