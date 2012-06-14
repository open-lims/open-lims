/**
 * version: 0.4.0.0
 * author: Roman Quiring <quiring@open-lims.org>
 * copyright: (c) 2008-2011 by Roman Konertz, Roman Quiring
 * license: GPLv3
 * 
 * This file is part of Open-LIMS
 * Available at http://www.open-lims.org
 * 
 * This program is free software;
 * you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation;
 * version 3 of the License.
 * 
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
 * See the GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, see <http://www.gnu.org/licenses/>.
 */

/**
 * DynamicTable jQuery plugin.
 * Allows dynamic resizing and displaying of table columns.
 */
(function($)
{
	var table;
	var tbody;
	var table_width;
	var table_height;
	var columns = [];
	
	var animating = false;
	
	/**
	 * Settings. These may be overwritten.
	 */
	var settings = 
	{
		sticky: [],
		notResizable: [],
		showHandle: true,
		handleColor: "#000000",
		showRuler: true,
		rulerColor: "#000000",
		resizeAnimation: true,
		resizeAnimationStyle: "swing",
		minWidth: 20
	}
	
	/**
	 * Public Methods.
	 */
	var methods = 
	{
		/**
		 * Initialise.
		 */
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
				$(th).html(div);
				
				column.th = th;
				column.initialWidth = $(th).width();
				column.width = $(th).width();
				column.visible = true;
				column.sticky = ($.inArray(int, settings.sticky) !== -1);
				column.resizable = ($.inArray(int, settings.notResizable) === -1);
				column.tds = [];
				$(tbody).find("tr").each(function() {
					var td = $(this).children("td:nth-child("+(int + 1)+")");
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
					if($(th).html().replace(/&nbsp;/g, "") !== "")
					{
						var measure = $("<div>"+$(th).children().html()+"</div>");
						$(measure)
							.css("position", "absolute")
							.appendTo("body");
						header_width = $(measure).width();
						$(measure).remove();
					}
				}
				column.headerWidth = header_width;
			
				columns.push(column);
			}
			
			for(var int = 0; int < num_cols - 1; int++) 
			{
				init_slider(int);
			}
	    },
	    
	    /**
	     * Method for hiding a specific column.
	     * @param num the index of the column to hide.
	     */
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
		    	column_to_hide.visible = false;
		    	$(column_to_add_width_to.th).width(column_to_add_width_to.width + column_to_hide.width);
		    	column_to_add_width_to.width = column_to_add_width_to.width + column_to_hide.width;
		    	animating = false;
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
	       				
	    				var new_width = $(column_to_add_width_to.th).width();
	    				$(column_to_add_width_to.th).width(new_width);
	    				column_to_add_width_to.width = new_width;
	    				
	    				animating = false;
	    				
	    				var last_visible_index = get_last_visible_column_index();
	    				if($(".ListTable > thead > tr > th").get(last_visible_index) === $(column_to_add_width_to.th)[0])
	    				{
	    					$(column_to_add_width_to.th).find(".ResizableColumnHandle").hide();
	    					$(column_to_add_width_to.th).find(".ui-resizable-handle").hide()
	    				}
	    			}
	    		}
	    	);
	    },
	    
	    /**
	     * Method for showing a specific column.
	     * @param num the index of the column to show.
	     */
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
	    	
	    	$(column_to_show.th).find(".ResizableColumnHandle").show();
			$(column_to_show.th).find(".ui-resizable-handle").show();
	    	
	    	if(num > get_last_visible_column_index())
	    	{
	    		var last_visible_column_index = get_last_visible_column_index();
	    		
				$(columns[last_visible_column_index].th).find(".ResizableColumnHandle").show();
				$(columns[last_visible_column_index].th).find(".ui-resizable-handle").show()
				
		    	$(column_to_show.th).find(".ResizableColumnHandle").hide();
				$(column_to_show.th).find(".ui-resizable-handle").hide()
	    	}	    	
	    	
	    	var column_to_remove_width_from = get_column_to_remove_width_from(num);
	    	
	    	if(!settings.resizeAnimation)
	    	{
	    		column_to_show.visible = true;
	    		$(column_to_show.th).show();
   				$(column_to_show.tds).each(function()
   				{
   					$(this).show();
   				});
	    		column_to_show.width = column_to_remove_width_from.space;
		    	$(column_to_show.th).width(column_to_remove_width_from.space);
		    	$(column_to_remove_width_from.th).width(column_to_remove_width_from.width - column_to_remove_width_from.space);
		    	column_to_remove_width_from.width = column_to_remove_width_from.width - column_to_remove_width_from.space;
		    	animating = false;
	    		return true;
	    	}

	    	$(column_to_show.th).show();
			$(column_to_show.tds).each(function(){
				$(this).show();
			});
			
	    	$(column_to_show.th).animate(
	    		{
	    			"width": [column_to_remove_width_from.space, settings.resizeAnimationStyle]
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
	    				$(column_to_show.th).width(current_width);

	    				var column_to_remove_width_from_new_width = column_to_remove_width_from.width - this_step_width;
	    				$(column_to_remove_width_from.th).width(column_to_remove_width_from_new_width);
	    				column_to_remove_width_from.width = column_to_remove_width_from_new_width;	    				
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
	    
	    /**
	     * Method for toggling visibility of a specific column.
	     * @param num the index of the column to toggle.
	     */
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
	    },
	    
	    /**
	     * Returns whether there is currently an animation going on.
	     */
	    isAnimating: function()
	    {
	    	return animating;
	    },
	    
	    reinit: function()
	    {
//	    	var widths = [];
//	    	var visible = [];
//	    	
//	    	for (var int = 0; int < columns.length; int++) {
//				var column = columns[int];
//				widths.push(column.width);
//				visible.push(column.visible);
//			}
//	    	
//	    	columns = [];
//	    	
//	    	methods.init.apply(this, arguments);
//	    	
//	    	for (var int = 0; int < columns.length; int++) {
//				var column = columns[int];
//				
//				column.width = widths[int];
//				column.visible = visible[int];
//				
//				$(column.th).width(column.width);
//				
//				if(!column.visible)
//				{
//					$(column.th).hide();
//	   				$(column.tds).each(function()
//	   				{
//	   					$(this).hide();
//	   				});
//				}
//			}
	    	
	    	
//			var num_cols = $(table).find("th").size();
//			
//			for(var int = 0; int < num_cols; int++) 
//			{
//				var column = {};
//				var th = $(table).find("th").get(int);
//				
//				var div = $("<div>"+$(th).html()+"</div>");
//				$(th).html(div);
//				
//				column.th = th;
//				column.initialWidth = $(th).width();
//				column.width = widths[int];
//				column.visible = visible[int];
//				column.sticky = ($.inArray(int, settings.sticky) !== -1);
//				column.resizable = ($.inArray(int, settings.notResizable) === -1);
//				column.tds = [];
//				$(tbody).find("tr").each(function() {
//					var td = $(this).children("td:nth-child("+(int + 1)+")");
//					column.tds.push(td);
//				});
//				
//				var header_width = 0;
//				if($(th).children().children().size() > 0)
//				{
//					$(th).children().children().each(function(){
//						header_width += $(this).outerWidth(true);
//					});
//				}
//				else
//				{
//					if($(th).html().replace(/&nbsp;/g, "") !== "")
//					{
//						var measure = $("<div>"+$(th).children().html()+"</div>");
//						$(measure)
//							.css("position", "absolute")
//							.appendTo("body");
//						header_width = $(measure).width();
//						$(measure).remove();
//					}
//				}
//				column.headerWidth = header_width;
//			
//				columns.push(column);
//				
//				
//				$(column.th).width(column.width);
//				
//				if(!column.visible)
//				{
//					$(column.th).hide();
//	   				$(column.tds).each(function()
//	   				{
//	   					$(this).hide();
//	   				});
//				}
//			}
	    }
	    
	};
	
	
	/**
	 * Private Method.
	 * Returns the index of the last column that is visible and not sticky.
	 */
    function get_last_visible_column_index()
    {
    	for(var int = columns.length - 1; int >= 0; int--)
    	{
    		if(columns[int].visible && !columns[int].sticky)
    		{
    			return int;
    		}
    	}
    }
    
	/**
	 * Private Method.
	 * Returns the index of the first column that is visible and not sticky.
	 */
    function get_first_visible_column_index()
    {
    	for(var int = 0; int < columns.length; int++)
    	{
    		if(columns[int].visible && !columns[int].sticky)
    		{
    			return int;
    		}
    	}
    }
    
    /**
     * Private Method.
     * Returns the column closest to a given column index that is visible and not sticky.
     * @param column_index the index of the column.
     */
    function get_visible_neighbour_column(column_index)
    {
    	if(column_index === get_last_visible_column_index() || column_index === columns.length - 1)
    	{
    		while(column_index > 0)
    		{
    			var neighbour_column = columns[column_index - 1];
    			if(neighbour_column.visible && !neighbour_column.sticky)
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
    			if(neighbour_column.visible && !neighbour_column.sticky)
    			{
    				break;
    			}
    			column_index++;
    		}
    	}
    	return neighbour_column;
    }
    
    /**
     * Private Method.
     * Returns an object that contains a column to take space from and a space parameter, which
     * indicates how much space to take from that column during animation.
     * @param column_index the index of the column to insert into the table.
     */
    function get_column_to_remove_width_from(column_index)
    {
    	var column = columns[column_index]
    	
    	var needed_space = column.initialWidth;
    	var minimal_space = settings.minWidth;
    	
    	var right_neighbour_col = undefined;
    	var left_neighbour_col = undefined;
    	
		for(var int = column_index + 1; int < columns.length; int++) 
		{
			var neighbour_column = columns[int];
			if(neighbour_column.visible && !neighbour_column.sticky)
			{
				right_neighbour_col = neighbour_column;
				break;
			}
		}

		for(var int = column_index - 1; int > 0; int--) 
		{
			var neighbour_column = columns[int];
			if(neighbour_column.visible && !neighbour_column.sticky)
			{
				left_neighbour_col = neighbour_column;
				break;
			}
		}
		
    	//check right neighbour (needed space)
		var available_space_right;
		if(right_neighbour_col !== undefined)
		{
			available_space_right = right_neighbour_col.width - right_neighbour_col.headerWidth;
			
			if(available_space_right >= needed_space)
			{
				right_neighbour_col.space = needed_space;
				return right_neighbour_col;
			}
		}
		
		//check left neighbour (needed space)
		var available_space_left;
		if(left_neighbour_col !== undefined)
		{
			available_space_left = left_neighbour_col.width - left_neighbour_col.headerWidth;
			
			if(available_space_left >= needed_space)
			{
				left_neighbour_col.space = needed_space;
				return left_neighbour_col;
			}
		}
		
		//check widest column (needed space)
		var widest = -1;
		var widest_available_width = -1;
		for(var int = 0; int < columns.length; int++) 
		{
			var column_to_check = columns[int];
			if(column_to_check.visible && !column_to_check.sticky)
			{
				if(column_to_check.width > widest_available_width)
				{
					widest_available_width = column_to_check.width - column_to_check.headerWidth;
					widest = column_to_check;
				}
			}
		}
		if(widest_available_width >= needed_space)
		{
			widest.space = needed_space;
			return widest;
		}
		
		//divide widest column by 2
		var widest_column_half_width = widest.width / 2;
		if(widest_column_half_width > minimal_space)
		{
			widest.space = widest_column_half_width;
			return widest;
		}
		
		//check right neighbour (minimal space)
		if(available_space_right >= minimal_space)
		{
			right_neighbour_col.space = minimal_space;
			return right_neighbour_col;
		}
		
		//check left neighbour (minimal space)
		if(available_space_left >= minimal_space)
		{
			left_neighbour_col.space = minimal_space;
			return left_neighbour_col;
		}
		
		//check widest column (minimal space)
		if(widest_available_width >= minimal_space)
		{
			widest.space = minimal_space;
			return widest;
		}
		
		alert("Unable to insert column! Resize the other columns to gain enough free space.");
    }
    
    /**
     * Private Method.
     * Makes all columns resizable that are not sticky and defined to be resizable.
     * @param column_index the index of the column to make resizable.
     */
    function init_slider(column_index) 
    {
    	var column = columns[column_index];
    	
    	if(column.sticky || !column.resizable)
    	{
    		return false;
    	}
    	
    	var resize_helper = $("<div class='ResizableColumnHelper'>"+$(column.th).html()+"</div>");
    	$(column.th).html(resize_helper);
    	
    	$(resize_helper).css("background-color","white");

    	if(settings.showHandle)
    	{
    		$("<div class='ResizableColumnHandle'></div>")
    			.css({
    				"position" : "absolute",
    				"right" : 0,
    				"top" : 0,
    				"min-width": 1,
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
				
				var neighbour_column = get_visible_neighbour_column(column_index);
	
				var column_new_width = $(this).width();
				var neighbour_col_new_width = neighbour_column.width + column.width - column_new_width;
				
				if(neighbour_col_new_width < neighbour_column.headerWidth)
				{
					$(neighbour_column.th).width(neighbour_col_new_width);
					$(neighbour_column.th).find(".ResizableColumnHelper").css("width", "100%");
				}
				
				if(neighbour_col_new_width < settings.minWidth)
				{
					var dif = settings.minWidth - neighbour_col_new_width;

					column_new_width -= Math.floor(dif);
					neighbour_col_new_width = settings.minWidth;

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