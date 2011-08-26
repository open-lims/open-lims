

Assistant = function(ajax_handler, init_page, end_page)
{	
	var page = init_page;
	var max_page = init_page;
	var clickable_array;

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
			var new_key = $(this).attr("class").replace(/AssistantElement/g, "");
				new_key = $.trim(new_key);
			var current_active_image = $(".AssistantElementImageActive").attr("src").replace("blue", "dgrey");
				
			$(".AssistantElementImageActive").attr("src", current_active_image);
			$(".AssistantElementImageActive").removeClass("AssistantElementImageActive");
			
			$(".AssistantElement"+new_key+" > a > span.AssistantElementImage > img").attr("src", "images/numbers/"+new_key+"_blue.png");
			$(".AssistantElement"+new_key+" > a > span.AssistantElementImage > img").addClass("AssistantElementImageActive");
			
			load_page(new_key);
		});
	}
	
	function set_not_clickable(key)
	{
		
	}
	
	function load_page(new_page)
	{
		page = parseInt(new_page);
		
		$.ajax(
		{
			type: "GET",
			url: ajax_handler,
			data: "session_id="+get_array['session_id']+"&run=get_content&page="+page,
			success: function(data)
			{
				if (data)
				{
					$("#AssistantContent").empty().append(data).slideDown("slow");
				}
			}
		});
	}
	
	function set_data(page, data)
	{
		var json_array = encodeURIComponent(JSON.stringify(data));
		
		$.ajax(
		{
			type: "POST",
			url: ajax_handler+"?session_id="+get_array['session_id']+"&run=set_data",
			data: "page="+page+"&data="+json_array,
			success: function(data)
			{
				
			}
		});
	}
	
	
	this.load_next_page = function(data)
	{
		if (page < end_page)
		{
			$("#AssistantContent").empty();
			$("#AssistantContent").html("<div id='AssistantLoading'><img src='images/animations/loading_circle_small.gif' alt='Loading...' /></div>");
			
			if (data)
			{
				set_data(page, data)
			}
			
			page = page + 1;
			set_active(page);
			
			if (page > 1)
			{
				set_visited(page-1);
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
			
		}
	}
	
	this.load_previous_page = function(data)
	{
		if (page > 1)
		{
			$("#AssistantContent").empty();
			$("#AssistantContent").html("<div id='AssistantLoading'><img src='images/animations/loading_circle_small.gif' alt='Loading...' /></div>");
			
			page = page - 1;
			set_active(page);
			
			if (page < end_page)
			{
				set_visited(page+1);
			}
			
			load_page(page);
		}
	}

}