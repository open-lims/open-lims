	
(function($)
{

	var table_width;
	var columns = new Array();
	 
	var methods = {
		init : function() 
		{
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
				columns.push(column);
			}
			
			for(var int = 0; int < num_cols; int++) 
			{
				init_slider(int);
			}

	    },
	    hide : function(num) 
	    {
	    	num -= 1;
	    	var column_to_hide = columns[num];

	    	var column_to_add_width_to = get_visible_neighbour_column(num);
	    	
//	    	if(num === get_last_visible_column_index())
//	    	{
//	    		num -= 1;
//	    		while(num >= 0)
//	    		{
//	    			var column_to_add_width_to = columns[num];
//	    			if(column_to_add_width_to.visible === true)
//	    			{
//	    				break;
//	    			}
//		    		num--;
//	    		}
//	    	}
//	    	else
//	    	{
//	    		while(num < columns.length)
//	    		{
//	    			var column_to_add_width_to = columns[num + 1];
//	    			if(column_to_add_width_to.visible === true)
//	    			{
//	    				break;
//	    			}
//		    		num++;
//	    		}
//	    	}
	    	
	    	$(column_to_hide.th).animate(
	    		{
	    			"width": 0 //[0, "linear"]
	    		}, 
	    		{
	    			duration: 500, 
	    			queue: false,
	    			step: function(now, fx) 
	    			{
	    				var width_dif = Math.floor(column_to_hide.width - now);
	    				$(column_to_add_width_to.th).width(column_to_add_width_to.width + width_dif);
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
	    	num -= 1;
	    	var column_to_show = columns[num];
	    	var showing_last_column = false;
	    	
	    	if(num > get_last_visible_column_index())
	    	{
	    		showing_last_column = true;
//	    		num -= 1;
//	    		while(num >= 0)
//	    		{
//	    			var column_to_remove_width_from = columns[num];
//	    			if(column_to_remove_width_from.visible === true)
//	    			{
//	    				break;
//	    			}
//		    		num--;
//	    		}
	    	}
//	    	else
//	    	{
//	    		while(num < columns.length)
//	    		{
//	    			var column_to_remove_width_from = columns[num + 1];
//	    			if(column_to_remove_width_from.visible === true)
//	    			{
//	    				break;
//	    			}
//		    		num++;
//	    		}
//	    	}
	    	
	    	var column_to_remove_width_from = get_visible_neighbour_column(num);
	    	
	    	if(column_to_remove_width_from.width - column_to_show.width < column_to_remove_width_from.initialWidth)
	    	{ //column_to_remove_width_from is at minumum width - search for the next column that is not TODO return multiple cols
	    		while(column_to_remove_width_from.width === column_to_remove_width_from.initialWidth)
	    		{
	    			if(showing_last_column)
	    			{
		    			num--;
	    			}
	    			else
	    			{
		    			num++;
	    			}
		    		column_to_remove_width_from = get_visible_neighbour_column(num);//columns[num];
	    		}
	    	}
	    	
	    	$(column_to_show.th).animate(
		    		{
		    			"width": column_to_show.width //[column_to_show.width, "linear"]
		    		}, 
		    		{
		    			duration: 500, 
		    			queue: false,
		    			step: function(now, fx) 
		    			{
		    				$(column_to_remove_width_from.th).width(column_to_remove_width_from.width - now);
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
    	var neighbour_column = get_visible_neighbour_column(column_index);
    	
    	var resize_helper = $("<div class='ResizableColumnHelper'>"+$(column.th).html()+"</div>");
    	$(column.th).html(resize_helper);
    	
    	$(resize_helper).resizable(
    	{
			handles: "e",
			minWidth: column.minWidth,
			start: function(event, ui)
			{
				var offset = $(this).offset();
				
				$("<div id='VerticalRuler'></div>")
					.css({
						"position": "absolute",
						"background-color": "#669acc",
						"width": 1,
						"height": $(".ListTable").height() - 1,
						"left": offset.left + $(this).width() - 1,
						"top": offset.top
					})
					.appendTo("body");
			},
			resize: function(event, ui)
			{
				var offset = $(this).offset();
				
				var width_diff = column.width - $(this).width();
				var neighbour_col_new_width = neighbour_column.width + width_diff;
				
				column.width = $(this).width();
				neighbour_column.width = neighbour_col_new_width;
				$(column.th).width($(this).width())
				$(neighbour_column.th).width(neighbour_col_new_width);
				
				$("#VerticalRuler").css("left", offset.left + $(this).width() - 1);
			},
			stop: function(event, ui)
			{
				$("#VerticalRuler").remove();
			}
    	});
    }
    

	$.fn.dynamicTable = function(method) 
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