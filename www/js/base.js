var array;
var div_id;
var global_ajax_handler;
var get_array;
var scroll_api;
	
function create_menu_tree(id, ajax_handler) 
{
	div_id = id;
	global_ajax_handler = ajax_handler;
	get_array = getQueryParams(document.location.search);
	var return_html = "";	

	$('#LeftNavigationTreeContainer').jScrollPane();
	scroll_api = $('#LeftNavigationTreeContainer').data('jsp');

	$.ajax(
	{
		type: "GET",
		url: ajax_handler,
		data: "run=get_array&session_id="+get_array['session_id'],
		success: function(data)
		{
			array = $.parseJSON(data);
			
			var current_layer = 0;
			
			$(array).each(function(i){ //alle arrays
				var layer = $(this)[0]; //level	
				var current_id = $(this)[1]; //id
				var name = $(this)[2]; //name
				var open = $(this)[7]; //open
				var symbol = $(this)[3]; //symbol
				var link = $(this)[6]; //link
				var clickable = $(this)[5]; //clickable
				var permission = $(this)[4]; //permission

				var next_layer;
				
				if(array[i+1] != undefined)
				{
					next_layer = array[i+1][0];	
				}
				else 
				{
					next_layer = 0;
				}
				
				if(current_layer < layer) 
				{
					if(open) 
					{
						return_html += "<ul class='LeftNavigationLayer"+layer+"'><li";
						if(!clickable || !permission)
						{
							return_html += " class='";
							if(!clickable)
							{
								return_html += " NotClickable";
							}
							if(!permission)
							{
								return_html += " NotPermitted";
							}
							return_html += "'";
						}
						return_html += "><div id='LeftNavigationElementID"+current_id+"' class='LeftNavigationFirstAnchorOpen'><a href='"+link+"'><img/></a> <a><img src='images/icons/"+symbol+"'/></a> <a>"+name+"</a></div>";
					}
					else 
					{
						return_html += "<ul class='LeftNavigationLayer"+layer+"'><li";
						if(!clickable || !permission)
						{
							return_html += " class='";
							if(!clickable)
							{
								return_html += " NotClickable";
							}
							if(!permission)
							{
								return_html += " NotPermitted";
							}			
							return_html += "'";
						}
						return_html += "><div id='LeftNavigationElementID"+current_id+"' class='LeftNavigationFirstAnchorClosed'><a href='"+link+"'><img/></a> <a><img src='images/icons/"+symbol+"'/></a> <a>"+name+"</a></div>";
					}
					
					if(layer >= next_layer)
					{
						return_html += "</li>";
					}
					
					current_layer = layer;
				}
				else
				{
					if(current_layer > layer) 
					{
						var layer_difference = current_layer - layer;
						
						for(var j = 1; j <= layer_difference; j++) 
						{
							return_html += "</ul>";
						}					
						current_layer =layer;
					}
					if(open) 
					{
						return_html += "<li";
						
						if(!clickable || !permission)
						{
							return_html += " class='";
							if(!clickable)
							{
								return_html += " NotClickable";
							}
							if(!permission)
							{
								return_html += " NotPermitted";
							}
							return_html += "'";
						}
						
						return_html += "><div id='LeftNavigationElementID"+current_id+"' class='LeftNavigationFirstAnchorOpen'><a href='"+link+"'><img/></a> <a><img src='images/icons/"+symbol+"'/></a> <a>"+name+"</a></div>";
					}
					else 
					{
						return_html += "<li";
						
						if(!clickable || !permission)
						{
							return_html += " class='";
							if(!clickable)
							{
								return_html += " NotClickable";
							}
							if(!permission)
							{
								return_html += " NotPermitted";
							}
							return_html += "'";
						} 
						
						return_html += "><div id='LeftNavigationElementID"+current_id+"' class='LeftNavigationFirstAnchorClosed'><a href='"+link+"'><img/></a> <a><img src='images/icons/"+symbol+"'/></a> <a>"+name+"</a></div>";
					}
					
					if(layer >= next_layer)
					{
						return_html += "</li>";
					}
				}
			});
			
			$("#"+id).append(return_html)
			.click(
				function(event) {					
					event.preventDefault();
					var target = event.target;
					var target_div = $(target).parents("div")[0];
			
					if($(target_div).hasClass("LeftNavigationFirstAnchorOpen"))
					{
						$(target_div).attr("class","LeftNavigationFirstAnchorClosed")	
							.parent().children("ul").slideUp("fast", function() {
								$(this).remove();
								parse_array();
								update_icons();
								update_scrollbar();
						});
					}
					
					else if($(target_div).hasClass("LeftNavigationFirstAnchorClosed"))
					{
						var parent_layer = parseInt($(target_div).parent().parent().attr("class").replace("LeftNavigationLayer",""));
						var layer = parent_layer + 1;

						$(target_div).attr("class","LeftNavigationFirstAnchorOpen");
						
						$.ajax(
						{
							type: "GET",
							url: ajax_handler,
							data: "run=get_childs&id="+id+"&session_id="+get_array['session_id'],
							success: function(data)
							{
								var child_html = "<ul class='LeftNavigationLayer"+layer+"'>";
								var child_array = $.parseJSON(data);
								$(child_array).each(function(){ //alle arrays
									var child_id = $(this)[1]; //id
									var child_name = $(this)[2]; //name
									var child_symbol = $(this)[3];
									var child_link = $(this)[6];
									var child_clickable = $(this)[5]; //clickable
									var child_permission = $(this)[4]; //permission
									
									child_html += "<li";
									
									if(!child_clickable || !child_permission)
									{
										child_html += " class='";
										if(!child_clickable)
										{
											child_html += " NotClickable";
										}
										if(!child_permission)
										{
											child_html += " NotPermitted";
										}
										child_html += "'";
									} 
									child_html += "><div id='LeftNavigationElementID"+child_id+"' class='LeftNavigationFirstAnchorClosed'><a href='"+child_link+"'><img/></a> <a><img src='images/icons/"+child_symbol+"'/></a> <a>"+child_name+"</a></div></li>";
								});
								child_html += "</ul>";
								
								var parent_li = $(target_div).parent();
								$(parent_li).append(child_html)
									.children("ul").hide().slideDown("normal");
								parse_array();
								update_icons();
								update_scrollbar();
							}
						});
					}
				}
			);	
			update_icons();
			update_scrollbar();
		}
	});
};


function update_icons() {
	$(".LeftNavigationFirstAnchorOpen > a:nth-child(1) > img").attr("src","images/minus.png");
	$(".LeftNavigationFirstAnchorClosed > a:nth-child(1) > img").attr("src","images/plus.png");
}


function update_scrollbar() 
{
	var content_div_height = $("#content").css("height").replace("px","");
	var max_height;
	
	if(content_div_height<500)
	{
		max_height = 500;
	}
	else
	{
		max_height = content_div_height;
	}	
	
	var list_height = parseInt(array.length * 19);
	var scroll_height = list_height + 8;
	
	if(scroll_height >= max_height)
	{
		scroll_height = max_height - 8;
	}
	else
	{
		scroll_api.scrollToY(0);
	}
	
	$("#LeftNavigationTree").css("height",list_height);
	$(".jspContainer").css("height",scroll_height);
	
	scroll_api.reinitialise();
}


function parse_array(){
		
	var previous_element_index;
	
	//check for deleted elements
	for ( var int = 0; int < array.length; int++) {
		var array_id = array[int][1];
		if($("#LeftNavigationElementID"+array_id).length == 0)
		{
			array.splice(int,1);	
			int--;
		}
	}
	
	//check for new elements
	$("#"+div_id).find("li").each(function(){
		var entry_id = $(this).children("div").attr("id").replace("LeftNavigationElementID","");

		var found = false;
		
		for ( var int = 0; int < array.length; int++) {
			var array_id = array[int][1];
	
			if(array_id == entry_id)
			{
				found = true;
				previous_element_index = parseInt(int);
				break;
			}
		}
		
		if(!found)
		{
			var entry_index = previous_element_index +1;
			
			var entry_layer = $(this).parent().attr("class").replace("LeftNavigationLayer","");
			var entry_name = $(this).children("div").children("a:nth-child(3)").text();
			var entry_symbol = $(this).children("div").children("a:nth-child(2)").children("img").attr("src").replace("images/icons/","");
			var entry_link = $(this).children("div").children("a:nth-child(1)").attr("href");
			var entry_open = $(this).children("div").hasClass("LeftNavigationFirstAnchorOpen");
			var entry_clickable = true;
			var entry_permission = true;
			if($(this).hasClass("NotClickable"))
			{
				entry_clickable = false;
			};
			if($(this).hasClass("NotPermitted"))
			{
				entry_permission = false;
			};
			
			var new_array_element = new Array(8);
			new_array_element[0] = entry_layer;
			new_array_element[1] = entry_id;
			new_array_element[2] = entry_name;
			new_array_element[3] = entry_symbol;
			new_array_element[4] = entry_permission;
			new_array_element[5] = entry_clickable;
			new_array_element[6] = entry_link;
			new_array_element[7] = entry_open;
			
			array.splice(entry_index,0,new_array_element);
		}
	});
	
	var json_array = JSON.stringify(array);
	
	$.ajax(
	{
		type: "POST",
		url: global_ajax_handler+"?session_id="+get_array['session_id']+"&run=set_array",
		data: "array="+json_array,
		success: function(data)
		{
//			console.log("wrote array: "+data+" entries.");
		}
	});
}
