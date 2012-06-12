

function is_image(filename) 
{
	if(typeof(allowed_image_types) === "undefined")
	{
		$.ajax({
			async: false,
			type : "POST",
			url : "ajax.php?session_id="+get_array['session_id']+"&nav=data&run=get_allowed_image_types",
			data : "",
			success : function(data) 
			{
				allowed_image_types = data;
			}
		});
	}

	var split = filename.split(".");
	if(split.length > 1) 
	{
		var filetype = split[split.length-1];
		if(allowed_image_types.indexOf(filetype) !== -1)
		{
			return true;
		}
	}
	return false;
}

function image_browser()
{
	init();
	
	function init()
	{
		var browser_container = $("<div></div>")
			.dialog({
				"title" : "Image Browser" ,
				"height" : 600,
				"width" : 400
			});
		
		var large_image = $("<div id='ImageBrowserFullImage'>large image</div>")
			.css({
				"outline": "solid green 1px",
				"width": "100%",
				"height": "40%"
			})
			.appendTo(browser_container);
		
		for (var int = 1; int < 10; int++) 
		{
			var small_image = $("<div class='ImageBrowserThumbnail'>"+int+"</div>")
				.css({
					"outline": "solid red 1px",
					"width": "30%",
					"height": "15%",
					"float": "left",
					"margin-top": "5%"
				})
				.click(function(){
					$("#ImageBrowserFullImage").html($(this).html());
				})
				.appendTo(browser_container);
			
			if(int % 3 !== 0)
			{
				$(small_image).css("margin-right", "5%");
			}
		}
	}
}