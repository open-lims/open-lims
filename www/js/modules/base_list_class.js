List = function(ajax_handler, ajax_run, argument_array, css_main_id, number_of_pages, row_array)
{
	var sort_array = new Array();
	
	var sort_value = "";
	var sort_method = "";
	var page = 1;
	
	var get_array = getQueryParams(document.location.search);
	
	var parsed_row_array = $.parseJSON(row_array);
	var colspan = parsed_row_array.length;
	
	function load_content(sort_value, sort_method, page)
	{
		$("#"+css_main_id).contents().detach();
		$("#"+css_main_id).append("<tr><td colspan='"+colspan+"'><div id='AssistantLoading'><img src='images/animations/loading_circle_small.gif' alt='Loading...' /></div></td></tr>");
		
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
			type: "POST",
			url: ajax_handler+"?username="+get_array['username']+"&session_id="+get_array['session_id']+"&run="+ajax_run+"&sortvalue="+sort_value+"&sortmethod="+sort_method+"&page="+page,
			data: "row_array="+row_array+"&argument_array="+argument_array,
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