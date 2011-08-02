function file_search()
{
	var get_array = getQueryParams(document.location.search);
	var ajax_handler = "core/modules/data/folder.ajax.php";
	var id = "fileSearchList";
	var array;
	$.ajax(
		{
			type: "GET",
			url: ajax_handler,
			data: "run=get_array&session_id="+get_array['session_id']+"&username="+get_array['username'],
			success: function(data)
			{
				array = $.parseJSON(data);

				var current_layer = -2;
				var return_html = "";
				
				$(array).each(
					
					function(i) // alle arrays
					{
						var layer = $(this)[0]; // level
						var current_id = $(this)[1]; // id
						var name = $(this)[2]; // name
						var open = $(this)[7]; // open
						var symbol = $(this)[3]; // symbol
						var link = $(this)[6]; // link

						var next_layer;

						if (array[i + 1] != undefined) 
						{
							next_layer = array[i + 1][0];
						} 
						else 
						{
							next_layer = 0;
						}

						if (current_layer < layer) {
							if (open) 
							{
								return_html += "<ul class='FileSearchLayer" + layer + "'>" 
										+ "<li><div id='FileSearchElementID"
										+ current_id
										+ "' class='FileSearchFirstAnchorOpen'><a href='#'><img/></a> <a href='index.php?"
										+ link
										+ " onclick='return false'><img src='images/icons/"
										+ symbol
										+ "'/ style='border: 0; overflow:auto;'></a> <a href='index.php?"
										+ link
										+ "' onclick='return false'>"
										+ name
										+ "</a><input type='radio' class='searchRadioButton' id='"+current_id+"'></div>";
							} 
							else 
							{
								return_html += "<ul class='FileSearchLayer" + layer + "'>" 
									+ "<li><div id='FileSearchElementID"
									+ current_id
									+ "' class='FileSearchFirstAnchorClosed'><a href='#'><img/></a> <a href='index.php?"
									+ link
									+ "' onclick='return false'><img src='images/icons/"
									+ symbol
									+ "' style='border: 0; overflow:auto;'/></a> <a href='index.php?"
									+ link
									+ "' onclick='return false'>"
									+ name
									+ "</a><input type='radio' class='searchRadioButton' id='"+current_id+"'></div>";
							}

							if (layer >= next_layer) 
							{
								return_html += "</li>";
							}
							current_layer = layer;
						} 
						else 
						{
							if (current_layer > layer) 
							{
								var layer_difference = current_layer - layer;

								for ( var j = 1; j <= layer_difference; j++) 
								{
									return_html += "</ul>";
								}
								current_layer = layer;
							}
							if (open) 
							{
								return_html += "<li><div id='FileSearchElementID"
									+ current_id
									+ "' class='FileSearchFirstAnchorOpen'><a href='#'><img/></a> <a href='index.php?"
									+ link
									+ "' onclick='return false'><img src='images/icons/"
									+ symbol
									+ "' style='border: 0;overflow:auto;'/></a> <a href='index.php?"
									+ link
									+ "' onclick='return false'>"
									+ name
									+ "</a><input type='radio' class='searchRadioButton' id='"+current_id+"'></div>";
							} 
							else 
							{
								return_html += "<li><div id='FileSearchElementID"
									+ current_id
									+ "' class='FileSearchFirstAnchorClosed'><a href='#'><img/></a> <a href='index.php?"
									+ link
									+ "' onclick='return false'><img src='images/icons/"
									+ symbol
									+ "' style='border: 0;overflow:auto;'/></a> <a href='index.php?"
									+ link
									+ "' onclick='return false'>"
									+ name
									+ "</a><input type='radio' class='searchRadioButton' id='"+current_id+"'></div>";
							}

							if (layer >= next_layer) 
							{
								return_html += "</li>";
							}
						}
					}
				);
				return_html += "</li></ul>";
				$("#"+id)
					.append(return_html)
					.bind("click", handler);
				update_icons();

			}
		});

	function update_icons() 
	{
		$(".FileSearchFirstAnchorOpen > a:nth-child(1) > img").attr("src","images/minus.png").css("border", "0");
		$(".FileSearchFirstAnchorClosed > a:nth-child(1) > img").attr("src", "images/plus.png").css("border", "0");
		$(".NotPermitted").each(function(){
			var src = $(this).children().children("a:nth-child(2)").children().attr("src");
			if(src.indexOf("core/images/denied_overlay.php?image=")==-1)
			{
				var new_src = "core/images/denied_overlay.php?image="+src;
				$(this).children().children("a:nth-child(2)").children().attr("src",new_src);
			}
		});
		$("#fileSearchList").find("a").css({"text-decoration" : "none","color" : "black"});
	}
	
	var handler = function(evt)
	{
		evt.preventDefault();
		var target = evt.target;
		var target_div = $(target).parents("div")[0];
		
		if($(target).hasClass("searchRadioButton"))
		{
			$(".searchRadioButton").attr("checked",false);
			$(target).attr("checked",true);
			return false;
		}
		
		if ($(target_div).hasClass("FileSearchFirstAnchorOpen")) 
		{
			$("#"+id).unbind("click");
			$(target_div).attr("class", "FileSearchFirstAnchorClosed");
			var ul_to_slide = $(target_div).parent().children("ul");
			if($(ul_to_slide).length > 0) 
			{
				$(ul_to_slide).slideUp("fast",
					function() {
						$(this).remove();
						update_icons();
						$("#" + id).bind("click", handler);
					});
			}
			else
			{
				update_icons();
				$("#" + id).bind("click", handler);
			}
		} 
		else if ($(target_div).hasClass("FileSearchFirstAnchorClosed")) 
		{
			$("#"+id).unbind("click");
			$(target_div).attr("class", "FileSearchFirstAnchorOpen");
	
			var clicked_id = $(target_div).attr("id").replace("FileSearchElementID", "");
	
			var parent_layer = parseInt($(target_div).parent().parent().attr("class").replace("FileSearchLayer", ""));
			var layer = parent_layer + 1;
	
			var parent_li = $(target_div).parent();
	
			$.ajax({
				type : "GET",
				url : ajax_handler,
				data : "run=get_children&id=" + clicked_id + "&session_id=" + get_array['session_id'],
				success : function(data) {
					var child_array = $.parseJSON(data);
	
					if (child_array != null && child_array.length != 0) 
					{
						var child_html = "<ul class='FileSearchLayer"+ layer + "'>";
	
						$(child_array).each(
							function() 
							{ // alle arrays
								var child_id = $(this)[1]; // id
								var child_name = $(this)[2]; // name
								var child_symbol = $(this)[3];
								var child_link = $(this)[6];
								var child_clickable = $(this)[5]; // clickable
								var child_permission = $(this)[4]; // permission
	
								child_html += "<li";
	
								if (!child_clickable || !child_permission) 
								{
									child_html += " class='";
									if (!child_clickable) 
									{
										child_html += " NotClickable";
									}
									if (!child_permission) 
									{
										child_html += " NotPermitted";
									}
									child_html += "'";
								}
								child_html += "><div id='FileSearchElementID"
									+ child_id
									+ "' class='FileSearchFirstAnchorClosed'><a href=''><img style='overflow:auto;'/></a> <a href='index.php?"
									+ child_link
									+ "' onclick='return false'><img src='images/icons/"
									+ child_symbol
									+ "' style='border: 0; overflow:auto;'/></a> <a href='index.php?"
									+ child_link
									+ "' onclick='return false'>"
									+ child_name
									+ "</a><input type='radio' class='searchRadioButton' id='"+child_id+"'></div></li>";
							});
						child_html += "</ul>";
						
						$(parent_li)
							.append(child_html)
							.first()
							.hide()
							.slideDown("normal",function() 
							{
								$("#" + id).bind("click", handler);
							});
						update_icons();
					} 
					else // keine children
					{
						update_icons();
						$("#" + id).bind("click", handler);
					}
					
				}
			});
			$(parent_li).children().children("a:nth-child(1)").children().attr("src", "images/animations/loading_circle_small.gif");
		}	
	}

	
}