function create_menu_tree(id, ajax_handler) {
	var array;
	var div_id = id;
	var global_ajax_handler = ajax_handler;
	var get_array = getQueryParams(document.location.search);

	$("<div id='loadingAnimation'><img src='images/animations/loading_circle_small.gif'/></div>")
		.css("margin", "10px 0 0 90px").appendTo("#LeftNavigationTree");

	$('#LeftNavigationTreeContainer').jScrollPane();
	var scroll_api = $('#LeftNavigationTreeContainer').data('jsp');

	var return_html = "";
	$.ajax(
	{
		type : "GET",
		url : ajax_handler,
		data : "run=get_array&session_id=" + get_array['session_id'] + "&username=" + get_array['username'],
		success : function(data) 
		{
			array = $.parseJSON(data);

			var current_layer = 0;

			$(array).each
			(
				function(i) { // alle arrays
					var layer = $(this)[0]; // level
					var current_id = $(this)[1]; // id
					var name = $(this)[2]; // name
					var open = $(this)[7]; // open
					var symbol = $(this)[3]; // symbol
					var link = $(this)[6]; // link
					var clickable = $(this)[5]; // clickable
					var permission = $(this)[4]; // permission

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
							return_html += "<ul class='LeftNavigationLayer" + layer + "'><li";
							if (!clickable || !permission) 
							{
								return_html += " class='";
								if (!clickable) 
								{
									return_html += " NotClickable";
								}
								if (!permission) 
								{
									return_html += " NotPermitted";
								}
								return_html += "'";
							}
							return_html += "><div id='LeftNavigationElementID"
									+ current_id
									+ "' class='LeftNavigationFirstAnchorOpen'><a href='#'><img/></a> <a href='index.php?"
									+ link
									+ "' style='height: 1%;'><img src='images/icons/"
									+ symbol
									+ "'/ style='border: 0;'></a> <a href='index.php?"
									+ link
									+ "'>"
									+ name
									+ "</a></div>";
						} 
						else 
						{
							return_html += "<ul class='LeftNavigationLayer" + layer + "'><li";
							if (!clickable || !permission) 
							{
								return_html += " class='";
								if (!clickable) 
								{
									return_html += " NotClickable";
								}
								if (!permission) 
								{
									return_html += " NotPermitted";
								}
								return_html += "'";
							}
							return_html += "><div id='LeftNavigationElementID"
								+ current_id
								+ "' class='LeftNavigationFirstAnchorClosed'><a href='#'><img/></a> <a href='index.php?"
								+ link
								+ "' style='height: 1%;'><img src='images/icons/"
								+ symbol
								+ "' style='border: 0;'/></a> <a href='index.php?"
								+ link
								+ "'>"
								+ name
								+ "</a></div>";
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
							return_html += "<li";

							if (!clickable || !permission) 
							{
								return_html += " class='";
								if (!clickable) 
								{
									return_html += " NotClickable";
								}
								if (!permission) 
								{
									return_html += " NotPermitted";
								}
								return_html += "'";
							}

							return_html += "><div id='LeftNavigationElementID"
								+ current_id
								+ "' class='LeftNavigationFirstAnchorOpen'><a href='#'><img/></a> <a href='index.php?"
								+ link
								+ "'><img src='images/icons/"
								+ symbol
								+ "' style='border: 0;'/></a> <a href='index.php?"
								+ link
								+ "'>"
								+ name
								+ "</a></div>";
						} else 
						{
							return_html += "<li";

							if (!clickable || !permission) 
							{
								return_html += " class='";
								if (!clickable) 
								{
									return_html += " NotClickable";
								}
								if (!permission) 
								{
									return_html += " NotPermitted";
								}
								return_html += "'";
							}

							return_html += "><div id='LeftNavigationElementID"
								+ current_id
								+ "' class='LeftNavigationFirstAnchorClosed'><a href='#'><img/></a> <a href='index.php?"
								+ link
								+ "'><img src='images/icons/"
								+ symbol
								+ "' style='border: 0;'/></a> <a href='index.php?"
								+ link
								+ "'>"
								+ name
								+ "</a></div>";
						}

						if (layer >= next_layer) 
						{
							return_html += "</li>";
						}
					}
				});
			$("#loadingAnimation").remove();
			$("#" + id).append(return_html).bind("click", handler);
			update_icons();
			update_scrollbar();
		}
	});
	
	function update_icons() 
	{
		$(".LeftNavigationFirstAnchorOpen > a:nth-child(1) > img").attr("src","images/minus.png").css("border", "0");
		$(".LeftNavigationFirstAnchorClosed > a:nth-child(1) > img").attr("src", "images/plus.png").css("border", "0");
		$(".NotPermitted").each(function(){
			var src = $(this).children().children("a:nth-child(2)").children().attr("src");
			if(src.indexOf("core/images/denied_overlay.php?image=")==-1)
			{
				var new_src = "core/images/denied_overlay.php?image="+src;
				$(this).children().children("a:nth-child(2)").children().attr("src",new_src);
			}
		});
		$("#LeftNavigationTree").find("a").css({"text-decoration" : "none","color" : "black"});
	}

	function update_scrollbar() 
	{
		var content_div_height = $("#content").css("height").replace("px", "");
		var max_height
		var offset_bottom = 5;

		if (content_div_height < 500) 
		{
			max_height = 500;
		} 
		else 
		{
			max_height = content_div_height;
		}

		var list_height = parseInt(array.length * 19) + offset_bottom;
		var scroll_height = list_height + 8;

		if (scroll_height >= max_height) 
		{
			scroll_height = max_height - 8;
		} 
		else 
		{
			scroll_api.scrollToY(0);
		}

		$("#LeftNavigationTree").css("height", list_height);
		$(".jspContainer").css("height", scroll_height);

		scroll_api.reinitialise();
	}

	function parse_array() 
	{
		var previous_element_index;

		var entry_index = -1;
		var new_array = new Array();

		// check for deleted elements
		for ( var int = 0; int < array.length; int++) 
		{
			var array_id = array[int][1];
			if ($("#LeftNavigationElementID" + array_id).length == 0) 
			{
				array.splice(int, 1);
				int--;
			}
		}

		// check for new elements
		$("#" + div_id)
			.find("li")
			.each(
				function() 
				{
					var entry_id = $(this).children("div").attr("id").replace("LeftNavigationElementID", "");
	
					var found = false;
	
					for ( var int = 0; int < array.length; int++) 
					{
						var array_id = array[int][1];
	
						if (array_id == entry_id) 
						{
							found = true;
							previous_element_index = parseInt(int);
							if ($("#LeftNavigationElementID" + entry_id).hasClass("LeftNavigationFirstAnchorClosed")) 
							{
								array[int][7] = false;
							} 
							else if ($("#LeftNavigationElementID"+ entry_id).hasClass("LeftNavigationFirstAnchorOpen")) 
							{
								array[int][7] = true;
							}
							break;
						}
					}
	
					if (!found) 
					{
						if (entry_index == -1) 
						{
							entry_index = previous_element_index + 1;
						}
	
						var entry_layer = $(this).parent().attr("class").replace("LeftNavigationLayer", "");
						var entry_name = $(this).children("div").children("a:nth-child(3)").text();
						var entry_symbol = $(this).children("div").children("a:nth-child(2)").children("img").attr("src").replace("images/icons/", "");
						var entry_link = $(this).children("div").children("a:nth-child(3)").attr("href").replace("index.php?", "");
						var entry_open = true;
						if ($(this).children("div").hasClass("LeftNavigationFirstAnchorClosed")) 
						{
							entry_open = false;
						}
						var entry_clickable = true;
						var entry_permission = true;
						if ($(this).hasClass("NotClickable")) 
						{
							entry_clickable = false;
						}
						if ($(this).hasClass("NotPermitted")) 
						{
							entry_permission = false;
						}
	
						var new_array_element = new Array(8);
						new_array_element[0] = entry_layer;
						new_array_element[1] = entry_id;
						new_array_element[2] = entry_name;
						new_array_element[3] = entry_symbol;
						new_array_element[4] = entry_permission;
						new_array_element[5] = entry_clickable;
						new_array_element[6] = entry_link;
						new_array_element[7] = entry_open;
	
						new_array.push(new_array_element);
					}
				});

		for ( var g = 0; g < new_array.length; g++) 
		{
			array.splice(entry_index + g, 0, new_array[g]);
		}

		if (global_ajax_handler.indexOf("?") == -1) 
		{
			var post_global_ajax_handler = global_ajax_handler + "?session_id="+ get_array['session_id'] + "&run=set_array";
		} else 
		{
			var post_global_ajax_handler = global_ajax_handler + "&session_id="+ get_array['session_id'] + "&run=set_array";
		}

		var json_array = encodeURIComponent(JSON.stringify(array));
		$.ajax({
			type : "POST",
			url : post_global_ajax_handler,
			data : "array=" + json_array,
			success : function(data) 
			{}
		});
	}

	
	var handler = function(evt) {

		evt.preventDefault();
		$("#" + id).unbind("click");

		var target = evt.target;
		var target_div = $(target).parents("div")[0];

		var follow_link = true;
		var href = $("#" + $(target_div).attr("id") + " a:nth-child(2)").attr("href");

		if ($(target).attr("src") == "images/minus.png" || $(target).attr("src") == "images/plus.png") 
		{
			follow_link = false;
		}

		if ($(target_div).hasClass("LeftNavigationFirstAnchorOpen")) 
		{
			$(target_div).attr("class", "LeftNavigationFirstAnchorClosed");
			if ($(target_div).parent().children("ul").length > 0) 
			{
				var ul_to_slide;
				if($(target_div).parent().children("ul").length == 2)
				{
					ul_to_slide = $(target_div).parent().children("ul").first();
				}
				else
				{
					ul_to_slide = $(target_div).parent().children("ul");
				}
				
//				console.log(ul_to_slide);
				$(ul_to_slide).slideUp("fast",
					function() {
						$(this).remove();
						parse_array();
						update_icons();
						update_scrollbar();
						$("#" + id).bind("click", handler);
					});
			} 
			else 
			{
				parse_array();
				update_icons();
				update_scrollbar();
				$("#" + id).bind("click", handler);
			}
		} 
		else if ($(target_div).hasClass("LeftNavigationFirstAnchorClosed")) 
		{
			$(target_div).attr("class", "LeftNavigationFirstAnchorOpen");

			var clicked_id = $(target_div).attr("id").replace("LeftNavigationElementID", "");

			var parent_layer = parseInt($(target_div).parent().parent().attr("class").replace("LeftNavigationLayer", ""));
			var layer = parent_layer + 1;

			var parent_li = $(target_div).parent();

			$.ajax({
				type : "GET",
				url : ajax_handler,
				data : "run=get_children&id=" + clicked_id + "&session_id=" + get_array['session_id'],
				success : function(data) {
					var child_array = $.parseJSON(data);

					if (child_array.length != 0) 
					{
						var child_html = "<ul class='LeftNavigationLayer"+ layer + "'>";

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
								child_html += "><div id='LeftNavigationElementID"
									+ child_id
									+ "' class='LeftNavigationFirstAnchorClosed'><a href=''><img/></a> <a href='index.php?"
									+ child_link
									+ "'><img src='images/icons/"
									+ child_symbol
									+ "' style='border: 0;'/></a> <a href='index.php?"
									+ child_link
									+ "'>"
									+ child_name
									+ "</a></div></li>";
							});
						child_html += "</ul>";

						$(parent_li)
							.append(child_html)
							.children()
							.hide()
							.slideDown("normal",function() 
								{
									if (!$(parent_li).hasClass(" NotClickable")) 
									{
										if (href.substr(-11) === "index.php?") 
										{
											console.log("link nicht güig");
										}
										else
										{
											if (follow_link) 
											{
//												parse_array();
												update_icons();
												update_scrollbar();
												window.location.href = href;
											}
										}
									}
								});
						parse_array();
					} 
					else // keine children
					{
						if (href.substr(-11) === "index.php?") 
						{
							console.log("link nicht güig");
						}
						else
						{
							if (follow_link) 
							{
								update_icons();
								update_scrollbar();
//								parse_array();
								$("#" + id).bind("click", handler);
								window.location.href = href;
							}
						}
					}
					update_icons();
					update_scrollbar();
//					parse_array();
					$("#" + id).bind("click", handler);
				}
			});
		}
		$(parent_li).children().children("a:nth-child(1)").children().attr("src", "images/animations/loading_circle_small.gif");
	}
}