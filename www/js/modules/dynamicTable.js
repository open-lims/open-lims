	
(function($)
{

	var tbody;
	var table_width;
	var columns = new Array();
	 
	var settings = {
		sticky: [],
		showHandle: true,
		handleColor: "#000000",
		showRuler: true,
		rulerColor: "#000000",
		resizeAnimation: true,
		resizeAnimationStyle: "swing"
	}
	
	var methods = {
			
		init : function(options) 
		{			
			settings = $.extend(settings, options);
			
			tbody = this.children("tbody");
			table_width = this.children("thead").width();
			var num_cols = this.find("th").size();
			
			for(var int = 0; int < num_cols; int++) 
			{
				var column = new Object();
				var th = this.find("th").get(int);
				
				column.th = th;
				column.initialWidth = $(th).width();	
				column.minWidth = 20; //TODO setzen per css?
				column.width = $(th).width();
				column.visible = true;
				column.paddingLeft = parseInt($(th).css("padding-right").replace("px",""));
				column.paddingRight = parseInt($(th).css("padding-left").replace("px","")); 
				column.sticky = ($.inArray(int, settings.sticky) !== -1);
				columns.push(column);
			}
			
			for(var int = 0; int < num_cols - 1; int++) 
			{
				init_slider(int);
			}

	    },
	    
	    hide : function(num) 
	    {
	    	var column_to_hide = columns[num];
	    	
	    	if(column_to_hide.sticky)
	    	{
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
	    			"width": [0, settings.resizeAnimationStyle]
	    		}, 
	    		{
	    			duration: 500, 
	    			queue: false,
	    			step: function(now, fx) 
	    			{
	    				var current_width = Math.floor(now);
	    				
	    				$(column_to_hide.th).width(current_width);
	    				$(column_to_add_width_to.th).width(column_to_add_width_to.width + (column_to_hide.width - current_width));
	    			},
    				complete: function() 
    				{
	    				$(column_to_hide.th).css("padding-left", 0);
	    				$(column_to_hide.th).css("padding-right", 0);
	       				column_to_hide.visible = false;
	       				
	    				var new_width = $(column_to_add_width_to.th).width() + column_to_add_width_to.paddingLeft + column_to_add_width_to.paddingRight;
	    				$(column_to_add_width_to.th).width(new_width);
	    				column_to_add_width_to.width = new_width;
	    			}
	    		}
	    	);
	    },
	    
	    show : function(num) 
	    {
	    	var column_to_show = columns[num];
	    	
	    	if(column_to_show.sticky)
	    	{
	    		return false;
	    	}
	    	
	    	var showing_last_column = false;
	    	
	    	if(num > get_last_visible_column_index())
	    	{
	    		showing_last_column = true;
	    	}
	    	
	    	var column_to_remove_width_from = get_visible_neighbour_column(num);

//	    	if(column_to_remove_width_from.width - column_to_show.width < column_to_remove_width_from.initialWidth)
//	    	{ //column_to_remove_width_from is at minumum width - search for the next column that is not TODO return multiple cols
//	    		while(column_to_remove_width_from.width === column_to_remove_width_from.initialWidth)
//	    		{
//	    			if(showing_last_column)
//	    			{
//		    			num--;
//	    			}
//	    			else
//	    			{
//		    			num++;
//	    			}
//		    		column_to_remove_width_from = get_visible_neighbour_column(num);//columns[num];
//	    		}
//	    	}
	   	   
	    	var other_columns = new Array();
	    	
	    	for ( var int = 0; int < columns.length; int++) {
//				console.log(int+" (start): "+$(columns[int].th).children(".ResizableColumnHelper").width()+" "+$(columns[int].th).width()+" "+columns[int].width);
				if(columns[int] !== column_to_show && columns[int] !== column_to_remove_width_from)
				{
					other_columns.push(columns[int]);
				}
			}
	    	
	    	if(!settings.resizeAnimation)
	    	{
		    	$(column_to_show.th).width(column_to_show.width);
		    	$(column_to_remove_width_from.th).width(column_to_remove_width_from.width - column_to_show.width);
		    	column_to_remove_width_from.width = column_to_remove_width_from.width - column_to_show.width;
	    		return true;
	    	}
	    	
	    	var x = true;
	    	
	    	$(column_to_show.th).animate(
	    		{
	    			"width": [column_to_show.width, settings.resizeAnimationStyle]
	    		}, 
	    		{
	    			duration: 500,
	    			queue: false,
	    			step: function(now, fx) 
	    			{
//	    				if(x)
//	    				{
//	    					for ( var int2 = 0; int2 < other_columns.length; int2++) {
//
//	    						$(other_columns[int2].th).width(other_columns[int2].width);
//	    						$(other_columns[int2].th).children(".ResizableColumnHelper").width(other_columns[int2].width);
//	    						
//	    						$(tbody).find("tr").each(function(){
//	    							var td = $(this).children(":nth-child("+(int2+1)+")");
//	    							$(td).width(other_columns[int2].width);
//	    						});
//							}
//	    					
//	    					x = false;
//	    				}
	    				
	    				var current_width = Math.floor(now);
	    				
	    				$(column_to_show.th).width(current_width);
	    				$(column_to_remove_width_from.th).width(column_to_remove_width_from.width - current_width);
	    			},
    				complete: function() 
    				{
	    				$(column_to_show.th).css("padding-left", column_to_show.paddingLeft);
	    				$(column_to_show.th).css("padding-right", column_to_show.paddingRight);
	    				column_to_show.visible = true;
	    				
	    				var new_width = $(column_to_remove_width_from.th).width() - column_to_show.paddingLeft - column_to_show.paddingRight;
	    				$(column_to_remove_width_from.th).width(new_width);
	    				column_to_remove_width_from.width = new_width;
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
    
    function get_visible_neighbour_column(column_index)
    {
    	if(column_index === get_last_visible_column_index() || column_index === columns.length - 1)
    	{
    		while(column_index > 0)
    		{
    			var neighbour_column = columns[column_index - 1];
    			if(neighbour_column.visible === true)
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
    			if(neighbour_column.visible === true)
    			{
    				break;
    			}
    			column_index++;
    		}
    	}
    	return neighbour_column;
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
			minWidth: column.minWidth,
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
						"height": $(".ListTable").height() - 1,
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
				var neighbour_col_new_width = neighbour_column.width + column.width - $(this).width();
				
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