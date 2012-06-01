	
(function($)
{

	var table;
	var tbody;
	var table_width;
	var table_height;
	var columns = [];
	
	var animating = false;
	 
	var settings = 
	{
		sticky: [],
		showHandle: true,
		handleColor: "#000000",
		showRuler: true,
		rulerColor: "#000000",
		resizeAnimation: true,
		resizeAnimationStyle: "swing",
		minWidth: 20,
		padding: 2
	}
	
	var methods = 
	{
		init : function(options) 
		{			
			settings = $.extend(settings, options);
			
			table = this;
			tbody = this.children("tbody");
			table_width = this.children("thead").width();
			table_height = this.height();
			var num_cols = this.find("th").size();
						
			for(var int = 0; int < num_cols; int++) 
			{
				var column = {};
				var th = this.find("th").get(int);
				
				var div = $("<div>"+$(th).html()+"</div>");
				$(div).css({
					"margin-left": settings.padding, 
					"margin-right": settings.padding
				});
				$(th).html(div);
				
				column.th = th;
				column.initialWidth = $(th).width();
				column.width = $(th).width();
				column.visible = true;
				column.sticky = ($.inArray(int, settings.sticky) !== -1);
				column.tds = [];
				$(tbody).find("tr").each(function() {
					var td = $(this).children("td:nth-child("+(int + 1)+")");
					var div = $("<div>"+$(td).html()+"</div>");
					$(div).css({
						"margin-left": settings.padding, 
						"margin-right": settings.padding
					});
					$(td).html(div);
					column.tds.push(td);
				});
				
				var header_width = 0;
				if($(th).children().children().size() > 0)
				{
					$(th).children().children().each(function(){
						header_width += $(this).outerWidth(true);
					});
				}
				else
				{
					if($(th).children().html().replace(/&nbsp;/g, "") !== "")
					{
						var measure = $("<div>"+$(th).children().html()+"</div>");
						$(measure)
							.css("position", "absolute")
							.appendTo("body");
						header_width = $(measure).width();
						$(measure).remove();
					}
				}
				column.headerWidth = header_width;// + 10; //TODO
			
				columns.push(column);
			}
			
			for(var int = 0; int < num_cols - 1; int++) 
			{
				init_slider(int);
			}
	    },
	    
	    hide : function(num) 
	    {
	    	if(animating)
	    	{
	    		return false;
	    	}
	    	animating = true;
	    	
	    	var column_to_hide = columns[num];
	    	
	    	if(column_to_hide.sticky)
	    	{
	    		animating = false;
	    		return false;
	    	}

	    	var column_to_add_width_to = get_visible_neighbour_column(num);
	    	
	    	if(!settings.resizeAnimation)
	    	{
		    	$(column_to_hide.th).width(0);
		    	$(column_to_add_width_to.th).width(column_to_add_width_to.width + column_to_hide.width);
		    	column_to_add_width_to.width = column_to_add_width_to.width + column_to_hide.width;
	    		return true;
	    	}
	    		    	
	    	$(column_to_hide.th).animate(
	    		{
	    			"width": [1, settings.resizeAnimationStyle]
	    		}, 
	    		{
	    			duration: 500, 
	    			queue: false,
	    			step: function(now, fx) 
	    			{
	    				if($.browser.mozilla)
	    				{ //Firefox uses its own rounding algorithm
	    					var current_width = now;
	    				}
	    				else
	    				{
	    					var current_width = Math.floor(now);
	    				}
	    				
	    				$(column_to_hide.th).width(current_width);
	    				
	    				column_to_add_width_to_current_width = column_to_add_width_to.width + (column_to_hide.width - current_width)
	    				
	    				$(column_to_add_width_to.th).width(column_to_add_width_to_current_width);
	    			},
    				complete: function() 
    				{
	       				column_to_hide.visible = false;

	       				if($.browser.msie && $.browser.version == 8.0)
       					{ //IE8 does not like hidden columns
	       					$(column_to_hide.th).width(0);
       					}
	       				else
	       				{
		       				$(column_to_hide.th).hide();
		       				$(column_to_hide.tds).each(function()
		       				{
		       					$(this).hide();
		       				});
	       				}
	       				
	    				var new_width = $(column_to_add_width_to.th).width() + 1;// + 3;// + column_to_add_width_to.paddingLeft + column_to_add_width_to.paddingRight + 1;
	    				$(column_to_add_width_to.th).width(new_width);
	    				column_to_add_width_to.width = new_width;
	    				
	    				animating = false;
	    			}
	    		}
	    	);
	    },
	    
	    show : function(num) 
	    {
	    	if(animating)
	    	{
	    		return false;
	    	}
	    	animating = true;
	    	
	    	var column_to_show = columns[num];
	    	
	    	if(column_to_show.sticky)
	    	{
		    	animating = false;
	    		return false;
	    	}
	    	
	    	var showing_last_column = false;
	    	
	    	if(num > get_last_visible_column_index())
	    	{
	    		showing_last_column = true;
	    	}
	    	
//	    	var column_to_remove_width_from = get_visible_neighbour_column(num);
	    	
//	    	var columns_to_remove_width_from = get_columns_to_remove_width_from(num);
	    	var columns_to_remove_width_from = get_columns_to_remove_width_from3(num);
	    	
	    	var current_column_to_remove_width_from_array_index = 0;
	    	
	    	if(!settings.resizeAnimation) //TODO
	    	{
		    	$(column_to_show.th).width(column_to_show.width);
		    	$(column_to_remove_width_from.th).width(column_to_remove_width_from.width - column_to_show.width);
		    	column_to_remove_width_from.width = column_to_remove_width_from.width - column_to_show.width;
	    		return true;
	    	}
	    	
	    	//all except ie8
	    	if(!($.browser.msie && $.browser.version == 8.0))
	    	{
		    	$(column_to_show.th).show();
				$(column_to_show.tds).each(function(){
					$(this).show();
				});
	    	}

			
			var first_column_to_remove_width_from = columns_to_remove_width_from[current_column_to_remove_width_from_array_index];
			first_column_to_remove_width_from.column.width = first_column_to_remove_width_from.column.width - 1;
			$(first_column_to_remove_width_from.column.th).width(first_column_to_remove_width_from.column.width);
			
			

	    	$(column_to_show.th).animate(
	    		{
	    			"width": [column_to_show.initialWidth, settings.resizeAnimationStyle]
	    		}, 
	    		{
	    			duration: 500,
	    			queue: false,
	    			step: function(now, fx) 
	    			{
	    				if($.browser.mozilla)
	    				{ //Firefox uses its own rounding algorithm
	    					var current_width = now;
	    				}
	    				else
	    				{
	    					var current_width = Math.floor(now);
	    				}
	    					    				
	    	
	    				
	    				var this_step_width = current_width - $(column_to_show.th).width();
//	    				$(this).width(current_width);
	    				$(column_to_show.th).width(current_width);
//	    				$(column_to_remove_width_from.th).width(column_to_remove_width_from.width - current_width);
	    				
	    				var column_to_remove_width_from = columns_to_remove_width_from[current_column_to_remove_width_from_array_index];
	    				
//	    				console.log(column_to_remove_width_from);
	    				
	    				if(column_to_remove_width_from.space < this_step_width)
	    				{
	    					var needed_space = this_step_width;
	    					
	    					while(true)
	    					{
	    						if(column_to_remove_width_from.space < needed_space)
	    						{
	    							var column_to_remove_width_from_new_width = Math.round(column_to_remove_width_from.column.width - column_to_remove_width_from.space);
	    							column_to_remove_width_from.column.width = column_to_remove_width_from_new_width;
	    							$(column_to_remove_width_from.column.th).width(column_to_remove_width_from_new_width);
	    							
	    							needed_space -= column_to_remove_width_from.space;
	    							console.log("still needed: "+needed_space);

	    							if(needed_space < 1)
	    							{
	    								break;
	    							}
	    							current_column_to_remove_width_from_array_index++;
	    							column_to_remove_width_from = columns_to_remove_width_from[current_column_to_remove_width_from_array_index];
	    						}
	    						else
	    						{
	    							var column_to_remove_width_from_new_width = Math.round(column_to_remove_width_from.column.width - needed_space);
	    							column_to_remove_width_from.column.width = column_to_remove_width_from_new_width;
	    							$(column_to_remove_width_from.column.th).width(column_to_remove_width_from_new_width);
	    							column_to_remove_width_from.space = column_to_remove_width_from.space - needed_space;
	    							break;
	    						}
	    					}
	    				}
	    				else
	    				{
	    					var column_to_remove_width_from_new_width = column_to_remove_width_from.column.width - this_step_width;
							column_to_remove_width_from.column.width = column_to_remove_width_from_new_width;
							$(column_to_remove_width_from.column.th).width(column_to_remove_width_from_new_width);
	    					column_to_remove_width_from.space = column_to_remove_width_from.space - this_step_width;
	    				}
	    				
	    			},
    				complete: function(now, fx) 
    				{
	    				column_to_show.visible = true;
	    				column_to_show.width = $(column_to_show.th).width();
	    				
	    				animating = false;    				
	    			}
	    		}
	    	);
	    },
	    
	    toggle: function(num) 
	    {
	    	var column_to_toggle = columns[num];
	    	if(column_to_toggle.visible)
	    	{
	    		methods.hide(num);
	    	}
	    	else
	    	{
	    		methods.show(num);
	    	}
	    }
	};
	
    function get_last_visible_column_index()
    {
    	for(var int = columns.length - 1; int >= 0; int--)
    	{
    		if(columns[int].visible)
    		{
    			return int;
    		}
    	}
    }
    
    function get_first_visible_column_index()
    {
    	for(var int = 0; int < columns.length; int--)
    	{
    		if(columns[int].visible)
    		{
    			return int;
    		}
    	}
    }
    
    function get_visible_neighbour_column(column_index)
    {
    	if(column_index === get_last_visible_column_index() || column_index === columns.length - 1)
    	{
    		while(column_index > 0)
    		{
    			var neighbour_column = columns[column_index - 1];
    			if(neighbour_column.visible)
    			{
    				break;
    			}
    			column_index--;
    		}
    	}
    	else
    	{
    		while(column_index < columns.length)
    		{
    			var neighbour_column = columns[column_index + 1];
    			if(neighbour_column.visible)
    			{
    				break;
    			}
    			column_index++;
    		}
    	}
    	return neighbour_column;
    }
    
    function get_columns_to_remove_width_from3(column_index)
    {
    	var column = columns[column_index]
    	var columns_to_remove_width_from = [];
    	
    	var needed_space = column.initialWidth;
    	var acumulated_space = 0;
    	
    	var step = 1;
    	var left = false;
    	
//    	console.log("need "+needed_space);
    	
    	while(acumulated_space < needed_space)
    	{
    		if(left)
    		{
        		var neighbour_column = columns[column_index - step];

        		if(neighbour_column === undefined)
        		{
            		left = false;
        			continue;
        		}
    			step++;
    		}
    		else
    		{
    			var neighbour_column = columns[column_index + step];

        		if(neighbour_column === undefined)
        		{
        			left = true;
        			continue;
        		}
        		step++;
    		}
    		
			if(neighbour_column.visible && !neighbour_column.sticky)
			{
				var available_space = Math.floor(neighbour_column.width - neighbour_column.headerWidth);
				
				var column_to_remove_width_from = {};
        		column_to_remove_width_from.column = neighbour_column;
				        		
				if(acumulated_space + available_space > needed_space)
				{
					var rest_space = needed_space - acumulated_space;
					column_to_remove_width_from.space = rest_space;
					acumulated_space += rest_space;
				}
				else
				{
					column_to_remove_width_from.space = available_space;
					acumulated_space += available_space;
				}
				
				columns_to_remove_width_from.push(column_to_remove_width_from);
			}
    	}

    	return columns_to_remove_width_from;
    }
    
    
    
    function get_columns_to_remove_width_from2(column_index)
    {
    	var column = columns[column_index]
    	var needed_space = column.width;
    	var columns_to_remove_width_from = [];
    	
//    	//check left neighbour columns
//    	for (var int = column_index + 1; int < columns.length; int++) 
//    	{
//			var neighbour_column = columns[int];
//			if(neighbour_column.visible && !neighbour_column.sticky)
//			{
//				var available_space = neighbour_column.width - neighbour_column.headerWidth;
//				if(available_space >= needed_space)
//				{
//					var column_to_remove_width_from = {};
//	        		column_to_remove_width_from.column = neighbour_column;
//	        		column_to_remove_width_from.space = needed_space;
//					columns_to_remove_width_from.push(column_to_remove_width_from);
//					break;
//				}
//			}
//		}
//    	
//    	if(columns_to_remove_width_from.length === 1)
//    	{
//    		return columns_to_remove_width_from;
//    	}
//    	
//    	//check right neighbour columns
//    	for (var int = column_index - 1; int >= 0; int--) 
//    	{
//			var neighbour_column = columns[int];
//			if(neighbour_column.visible && !neighbour_column.sticky)
//			{
//				var available_space = neighbour_column.width - neighbour_column.headerWidth;
//				if(available_space >= needed_space)
//				{
//					var column_to_remove_width_from = {};
//	        		column_to_remove_width_from.column = neighbour_column;
//	        		column_to_remove_width_from.space = needed_space;
//					columns_to_remove_width_from.push(column_to_remove_width_from);
//					break;
//				}
//			}
//		}
//    	
//    	if(columns_to_remove_width_from.length === 1)
//    	{
//    		return columns_to_remove_width_from;
//    	}
    	
    	//there is no single column to take space from
    	
    	var acumulated_space = 0;
    	
    	//check left neighbour columns
    	for (var int = column_index + 1; int < columns.length; int++) 
    	{
			var neighbour_column = columns[int];
			if(neighbour_column.visible && !neighbour_column.sticky)
			{
				var available_space = neighbour_column.width - neighbour_column.headerWidth;
				
//				if(available_space >= 10)
//				{					
//					console.log("col "+int+" has space "+available_space+" acu "+acumulated_space);
					var column_to_remove_width_from = {};
	        		column_to_remove_width_from.column = neighbour_column;
	        		
					if(acumulated_space + available_space > needed_space)
					{
//						console.log("asd space left "+(needed_space - acumulated_space));
						var rest_space = needed_space - acumulated_space;
						
						column_to_remove_width_from.space = rest_space;
						columns_to_remove_width_from.push(column_to_remove_width_from);
						acumulated_space += rest_space;
						break;
					}
					else
					{
						column_to_remove_width_from.space = available_space;
					}
					columns_to_remove_width_from.push(column_to_remove_width_from);
					
					acumulated_space += available_space;
//				}
			}
		}
    	
//   le.log(acumulated_space+" "+needed_space);
    	
    	if(acumulated_space < needed_space)
    	{
        	//check right neighbour columns
        	for (var int = column_index - 1; int >= 0; int--) 
        	{
    			var neighbour_column = columns[int];
    			if(neighbour_column.visible && !neighbour_column.sticky)
    			{
    				var available_space = neighbour_column.width - neighbour_column.headerWidth;

//    				if(available_space >= 10)
//    				{	
    					var column_to_remove_width_from = {};
    	        		column_to_remove_width_from.column = neighbour_column;
    	        		
    					if(acumulated_space + available_space > needed_space)
    					{
    						var rest_space = needed_space - acumulated_space;
    						
    						column_to_remove_width_from.space = rest_space;
    						columns_to_remove_width_from.push(column_to_remove_width_from);
    						acumulated_space += rest_space;
    						break;
    					}
    					else
    					{
    						column_to_remove_width_from.space = available_space;
    					}
    					columns_to_remove_width_from.push(column_to_remove_width_from);
    					
    					acumulated_space += available_space;
//    				}
    			}
    		}
    	}
    	
//    	console.log(columns_to_remove_width_from);
    	
    	return columns_to_remove_width_from;
    	
    }
    
    
    function get_columns_to_remove_width_from(column_index)
    {
    	var column = columns[column_index]
    	
    	var needed_space = column.width;
    	var acumulated_space = 0;

    	var columns_to_remove_width_from = [];
    	
    	var left = false;
    	if(column_index === columns.length - 1 || column_index === get_last_visible_column_index())
    	{
    		left = true;
    	}
    	
    	var num_left_columns_to_take_space_from = 0;
    	var num_right_columns_to_take_space_from = 0;
    	
    	var recursively_add_columns_to_remove_width_from = function(column_index) 
    	{
    		console.log("checking col neighbour of "+column_index+" left: "+left);
    		
        	if(left)
        	{
            	if(column_index === 0 || column_index === get_first_visible_column_index())
            	{
            		console.log("left does not work because "+column_index+" is the first or first visible col or sticky");
            		left = false;
            	}
            	else
            	{
            		var current_column_index = column_index;
            		
            		while(current_column_index > 0)
            		{
            			var neighbour_column = columns[current_column_index - 1];
            			num_left_columns_to_take_space_from++;
            			if(neighbour_column.visible && !neighbour_column.sticky)
            			{
            				break;
            			}
            			current_column_index--;
            		}
            		
             		if(!neighbour_column.visible || neighbour_column.sticky)
             		{
             			neighbour_column = undefined;
             		}
             		
            		left = false;
            	}
        	}
        	else
        	{
        		if(column_index === columns.length - 1 || column_index === get_last_visible_column_index())
        		{
            		console.log("right does not work because "+column_index+" is the last or last visible col");
            		left = true;
        		}
        		else
        		{
        			var current_column_index = column_index;
        			
             		while(current_column_index < columns.length)
            		{
            			var neighbour_column = columns[current_column_index + 1];
                 		num_right_columns_to_take_space_from++;
            			if(neighbour_column.visible && !neighbour_column.sticky)
            			{
            				break;
            			}
            			current_column_index++;
            		}
             		
             		if(!neighbour_column.visible || neighbour_column.sticky)
             		{
             			neighbour_column = undefined;
             		}
             		
             		left = true;
        		}
        	}
        	
        	console.log("neighbour col: ");
        	console.log(neighbour_column);
        	
        	if(neighbour_column !== undefined)
        	{
//        		recursively_add_columns_to_remove_width_from(column_index);
//        	}
//        	else
//        	{
        		var available_space = neighbour_column.width - neighbour_column.headerWidth;
        		
        		var space_still_needed = needed_space - acumulated_space;
        		
        		
        		var column_to_remove_width_from = {};
        		column_to_remove_width_from.column = neighbour_column;
        		
        		if(space_still_needed <= available_space)
        		{
        			column_to_remove_width_from.space = space_still_needed;
        			acumulated_space += space_still_needed;
        			console.log("this col has the needed space of "+space_still_needed);
        		}
        		else
        		{
        			column_to_remove_width_from.space = available_space;
            		acumulated_space += available_space;
            		console.log("this col has not enough space available: need "+space_still_needed+" has "+available_space);
        		}
        		
        		columns_to_remove_width_from.push(column_to_remove_width_from);
        	}
        		if(acumulated_space < needed_space)
        		{
        			if(left)
        			{
        				console.log("go left. checked "+num_left_columns_to_take_space_from+" on the left side already.");
        				
        				var current_index_left = column_index - num_left_columns_to_take_space_from - 1;
        				if(current_index_left > 0)
        				{
        					recursively_add_columns_to_remove_width_from(current_index_left);
        				}
        				else
        				{
        					left = false;
        					recursively_add_columns_to_remove_width_from(current_index_right);
        				}
        			}
        			if(!left)
        			{
        				console.log("go right. checked "+num_right_columns_to_take_space_from+" on the right side already.");
        				
        				var current_index_right = column_index + num_right_columns_to_take_space_from + 1;
        				
        				if(current_index_right < columns.length)
        				{
        					recursively_add_columns_to_remove_width_from(current_index_right);
        				}
        				else
        				{
        					left = true;
        					recursively_add_columns_to_remove_width_from(current_index_left);
        				}
        			}
        		}
        		else
        		{
        			console.log("done!");
        		}
//        	}
    	}
    	
    	recursively_add_columns_to_remove_width_from(column_index);
    	
    	console.log("will remove space from "+columns_to_remove_width_from.length+" columns: ");
    	for ( var int = 0; int < columns_to_remove_width_from.length; int++) {
			console.log(columns_to_remove_width_from[int]);
		}
    	
    	return columns_to_remove_width_from;
    }
    
    
    function init_slider(column_index) 
    {
    	var column = columns[column_index];
    	
    	if(column.sticky)
    	{
    		return false;
    	}
    	
    	var neighbour_column = get_visible_neighbour_column(column_index);
    	
    	var resize_helper = $("<div class='ResizableColumnHelper'>"+$(column.th).html()+"</div>");
    	$(column.th).html(resize_helper);
    	
    	$(resize_helper).children().css("margin",0);
    	$(resize_helper).css("box-sizing", "padding-box")
    	$(resize_helper).css("-moz-box-sizing", "padding-box")
    	
    	if(settings.showHandle)
    	{
    		$("<div class='ResizableColumnHandle'></div>")
    			.css({
    				"position" : "absolute",
    				"right" : 0,
    				"top" : 0,
    				"width": 1,
    				"height": "100%",
    				"background-color": settings.handleColor
    			})
    			.appendTo(resize_helper);
    	}
    	
    	$(resize_helper).resizable(
    	{
			handles: "e",
			minWidth: settings.minWidth,
			start: function(event, ui)
			{
				var offset = $(this).offset();
				
				if(settings.showRuler)
				{
					$("<div id='VerticalRuler'></div>")
					.css({
						"position": "absolute",
						"background-color": settings.rulerColor,
						"width": 1,
						"height": table_height - 1,
						"left": offset.left + $(this).width() - 1,
						"top": offset.top
					})
					.appendTo("body");
				}
			},
			resize: function(event, ui)
			{
				var offset = $(this).offset();
				
				var column_new_width = $(this).width();
				
				var neighbour_col_new_width = neighbour_column.width + column.width - column_new_width;
				
				if(neighbour_col_new_width < neighbour_column.headerWidth)
				{
					var dif = neighbour_column.headerWidth - neighbour_col_new_width;

					column_new_width -= Math.floor(dif);
					neighbour_col_new_width = neighbour_column.headerWidth;

					$(this).trigger("mouseup"); 
				}
				
				column.width = column_new_width;
				neighbour_column.width = neighbour_col_new_width;
				$(column.th).width(column_new_width)
				$(neighbour_column.th).width(neighbour_col_new_width);
		
				$(resize_helper).css("width", "100%");
				$("#VerticalRuler").css("left", offset.left + $(this).width() - 1);
			},
			stop: function(event, ui)
			{
				$("#VerticalRuler").remove();
			}
    	});
    }
    

	$.fn.dynamicTable = function(method, options) 
	{
	    // Method calling logic
	    if(methods[method]) 
	    {
	    	return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
	    } 
	    else if(typeof method === "object" || ! method) 
	    {
	    	return methods.init.apply(this, arguments);
	    } 
	    else {
	    	$.error("Method " +  method + " does not exist on jQuery.dynamicTable");
	    } 
	    
	    init();  
	};

})(jQuery);