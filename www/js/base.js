var array;
var div_id;
var global_ajax_handler;
var get_array;
var scroll_api;
	
function create_menu_tree(id, ajax_handler) 
{
	div_id = id;
	global_ajax_handler = ajax_handler;


	$('#LeftNavigationTreeContainer').jScrollPane();
	scroll_api = $('#LeftNavigationTreeContainer').data('jsp');
	
	get_array = getQueryParams(document.location.search);
	var return_html = "";	
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


			$("#loadingAnimation").remove();
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
						var clicked_id = $(target_div).attr("id").replace("LeftNavigationElementID","");
						
						var parent_layer = parseInt($(target_div).parent().parent().attr("class").replace("LeftNavigationLayer",""));
						var layer = parent_layer + 1;

						$(target_div).attr("class","LeftNavigationFirstAnchorOpen");
						var parent_li = $(target_div).parent();
						
						$.ajax(
						{
							type: "GET",
							url: ajax_handler,
							data: "run=get_childs&id="+clicked_id+"&session_id="+get_array['session_id'],
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
								
								
								$(parent_li).append(child_html)
									.children("ul").hide().slideDown("normal");
								parse_array();
								update_icons();
								update_scrollbar();
							}
						});		
					}
					$(parent_li).children().children("a:nth-child(1)").children().attr("src","images/animations/loading_circle_small.gif");
				}
			);	
			update_icons();
			update_scrollbar();
		}
	});
	
	$("<div id='loadingAnimation'><img src='images/animations/loading_circle_small.gif'/></div>")
		.css("margin","10px 0 0 90px")
		.appendTo("#LeftNavigationTree");
};


function update_icons() {
	$(".LeftNavigationFirstAnchorOpen > a:nth-child(1) > img").attr("src","images/minus.png").css("border","0");
	$(".LeftNavigationFirstAnchorClosed > a:nth-child(1) > img").attr("src","images/plus.png").css("border","0");
}


function update_scrollbar() 
{
	var content_div_height = $("#content").css("height").replace("px","");
	var max_height
	var offset_bottom = 5;
	
	if(content_div_height<500)
	{
		max_height = 500;
	}
	else
	{
		max_height = content_div_height;
	}	
	
	var list_height = parseInt(array.length * 19) + offset_bottom;
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
				if($("#LeftNavigationElementID"+entry_id).hasClass("LeftNavigationFirstAnchorClosed"))
				{
					array[int][7] = false;
				}
				else if($("#LeftNavigationElementID"+entry_id).hasClass("LeftNavigationFirstAnchorOpen"))
				{
					array[int][7] = true;
				}
				
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
			var entry_open = true;
			if($(this).children("div").hasClass("LeftNavigationFirstAnchorClosed"))
			{
				entry_open = false;
			}
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

function tooltip(element_id, message)
{
	var offsetX = 20;
	var offsetY = 10;
	
	$("#"+element_id).hover(function(e){
		$("<div id='tooltip'>"+message+"</div>")
			.css("position","absolute")
			.css("background-color","white")
			.css("border","solid black 1px")
			.css("padding","2px 4px 2px 4px")
			.css({"font-family":"arial","font-size":"12px"})
			.css("top", e.pageY + offsetY)
			.css("left", e.pageX + offsetX)
			.hide()
			.appendTo('body')
			.fadeIn(300);
	},function(){
		$('#tooltip').remove();
	});
	
	$("#"+element_id).mousemove(function(e) {
		$("#tooltip").css("top", e.pageY + offsetY).css("left", e.pageX + offsetX);
	});
}



function create_scrollable_tabs()
{	
	var tabs = $(".SmallTabList");
	var max_tabs = 7;
	var num_tabs = $(tabs).children("li").length;
	
	$(tabs).children().click(function(e){
		e.stopPropagation();
		var loading_animation = $("<div id='loadingAnimation'><img src='images/animations/loading_circle_small.gif'/></div>")
			.css("margin","10px 0 0 367px");
		$(".boxRoundMain").html(loading_animation);
		if($("#arrowLeft").hasClass("buttonInactive") && $("#arrowRight").hasClass("buttonInactive"))
		{
			//num_tabs < max_tabs, no scrolling
		}
		else
		{
			focus_tab($(this).children().text(),true);
		}
	});

	var tab_width = parseInt($(tabs).children().css("width").replace("px",""));
	var max_tabs_width = tab_width * num_tabs;
	tabs.css("width",max_tabs_width);
	

	$("#arrowLeft")
		.addClass("buttonInactive")
		.css("float","left")
		.click(function(){
			if(!$(this).hasClass("buttonInactive"))
			{
				if(!$(this).hasClass("showHiddenTabs")) 
				{
					$(this).addClass("showHiddenTabs")
					$("#arrowLeft img").rotate({animateTo:-90,duration:300});
					show_hidden_tabs("left");
				}
				else
				{
					$(this).attr("class","");
					$("#arrowLeft img").rotate({animateTo:0,duration:300});
					$("#hiddenTabs").remove();
				}
			}
		});
	$("#arrowRight")
		.addClass("buttonInactive")
		.css("float","left")
		.click(function(){
			if(!$(this).hasClass("buttonInactive"))
			{
				if(!$(this).hasClass("showHiddenTabs")) 
				{
					$(this).addClass("showHiddenTabs")
					$("#arrowRight img").rotate({animateTo:90,duration:300});
					show_hidden_tabs("right");
				}
				else
				{
					$(this).attr("class","");
					$("#arrowRight img").rotate({animateTo:0,duration:300});
					$("#hiddenTabs").remove();
				}
			}
		});

	
	var camera_width;
	var camera_height = 17;
	
	if(num_tabs > max_tabs)
	{	
		camera_width = tab_width * max_tabs +1;    //width - (2 * arrow_width);
		focus_tab($(".SmallTabActive").text(),false);
	}
	else
	{
		camera_width = tab_width * num_tabs +1;
	}

	$("#cameraDiv")
	.css("float","left")
	.css("width",camera_width+"px")
	.css("height",camera_height+"px")
	.css("background-color","red")
	.css("overflow","hidden");
	
	$("#cameraDiv").append(tabs);
	var container_width = camera_width + 32;
	var container_margin = ($("#SmallTabContainer").css("width").replace("px","") - container_width) / 2;
	$("#SmallTabContainer").css("margin-left",container_margin+"px");
	
	if($("#arrowLeft").hasClass("buttonInactive"))
	{
		$("#arrowLeft img").attr("src","images/1leftarrow_inactive.png");
	}
	else
	{
		$("#arrowLeft img").attr("src","images/1leftarrow.png");
	}
	if($("#arrowRight").hasClass("buttonInactive"))
	{
		$("#arrowRight img").attr("src","images/1rightarrow_inactive.png");
	}
	else
	{
		$("#arrowRight img").attr("src","images/1rightarrow.png");
	}	
}

function show_hidden_tabs(side) {
	
	var max_tabs = 7;
	
	var tab_width = parseInt($(".SmallTabList").children().css("width").replace("px",""));
	var current_offset = parseInt($(".SmallTabList").css("margin-left").replace("px",""));
	var num_hidden_tabs;
	
	
	var hidden_tabs_div = $("<div></div>")
		.attr("id","hiddenTabs")
		.css("position","absolute")
		.css("width",tab_width+"px")
		.css("background-color","white")
		.css("border","solid black 1px")
		.css("padding","2px 2px 2px 2px")
		.css({"font-family":"arial","font-size":"12px"})
		.hide();
	
	switch(side)
	{
	case "left":
		var position = $("#arrowLeft").position();
		num_hidden_tabs = -(current_offset / tab_width);
		var hidden_tabs = $(".SmallTabList li:lt("+num_hidden_tabs+")");
		
		$(hidden_tabs).each(function(){
			console.log(this);
			var html = $("<div>"+$(this).children().text()+"</div>")
				.hover(function()
					{
						$(this).css("background-color","#cccccc");
					},function()
					{
						$(this).css("background-color","white");
					})
				.click(function(){
					
					var loading_animation = $("<div id='loadingAnimation'><img src='images/animations/loading_circle_small.gif'/></div>")
						.css("margin","10px 0 0 367px");
					$(".boxRoundMain").html(loading_animation);
					
					focus_tab($(this).text(),true);
					$("#arrowLeft img").rotate({animateTo:0,duration:300});
				})
				.css("padding","2px 4px 2px 4px");
			hidden_tabs_div.append(html);
		});
		
		$(hidden_tabs_div)
			.css({"left": position.left,"top":position.top+17});
		break;

	case "right":
		var position = $("#arrowRight").position();
		last_visible_tab = -(current_offset - (max_tabs * tab_width)) / tab_width -1;
		var hidden_tabs = $(".SmallTabList li:gt("+last_visible_tab+")");
		
		$(hidden_tabs).each(function(){
			console.log(this);
			var html = $("<div>"+$(this).children().text()+"</div>")
				.hover(function()
					{
						$(this).css("background-color","#cccccc");
					},function()
					{
						$(this).css("background-color","white");
					})
				.click(function(){
					
					var loading_animation = $("<div id='loadingAnimation'><img src='images/animations/loading_circle_small.gif'/></div>")
						.css("margin","10px 0 0 367px");
					$(".boxRoundMain").html(loading_animation);
					
					focus_tab($(this).text(),true);
					$("#arrowRight img").rotate({animateTo:0,duration:300});
					
				});
			hidden_tabs_div.append(html);
		});
		
		$(hidden_tabs_div)
			.css({"text-align":"right","left": position.left+10-tab_width,"top":position.top+17});
		break;
	}
	$(hidden_tabs_div).appendTo("#SmallTabContainer").fadeIn(300);
}


function focus_tab(capture,slide)
{
	capture = $.trim(capture);
	$("#arrowRight").attr("class","");
	$("#arrowLeft").attr("class","");
	
	var selected;
	$(".SmallTabList > li > a").filter(function() {
	    if($(this).text() === capture)
	    {
	    	selected = this;
	    	return true;
	    }
	});
	
	var number_of_previous_tabs = $(selected).parent().prevAll().size();
	var num_tabs = $(".SmallTabList").children("li").length;

	var tab_width = parseInt($(".SmallTabList").children().css("width").replace("px",""));
	var max_tabs_width = tab_width * num_tabs;

	var offset = -(number_of_previous_tabs * tab_width - (3 * tab_width));
	var max_offset = -(max_tabs_width-(7*tab_width));
	
	if(offset <= max_offset)
	{
		offset = max_offset;
		$("#arrowRight").addClass("buttonInactive");
	}
	else if(offset >= 0)
	{
		offset = 0;
		$("#arrowLeft").addClass("buttonInactive");
	}
	
	var url = $(selected).attr("href");
	
	if(slide==true)
	{
		$(".SmallTabList").animate({"margin-left":offset+"px"},100,function(){
			$(location).attr('href',url);
		});
	}
	else
	{
		$(".SmallTabList").css("margin-left",offset+"px");
	}
}


