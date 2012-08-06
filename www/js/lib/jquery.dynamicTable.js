/**
 * dynamicTable jQuery plugin.
 * Allows dynamic resizing and displaying of table columns.
 *
 * version: 0.3
 * author: Roman Quiring <rquiring@gmx.de>
 * copyright: (c) 2012 by Roman Quiring
 * license: MIT and GPLv3
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
 * 
 * This plugin was originally developed for Open-LIMS.
 * http://www.open-lims.org
 */

(function($)
{
	$.fn.dynamicTable = function(method, options) 
	{
		var table = this,
			props = $(this).data("properties"),	//columns, animating, border_spacing
			settings = $(this).data("settings"),//extended default settings
		
		default_settings = 
		{
			sticky: [],
			notResizable: [],
			minColumnWidth: 20,
			showHandle: true,
			handleCSS: {
				width: 1,
				"min-width": 1,
				"background-color": "#000000",
				"z-index": 100
			},
			showRuler: true,
			rulerCSS: {
				width: 1,
				"background-color": "#000000",
				"z-index": 100
			},
			resizeAnimation: true,
			resizeAnimationStyle: "linear",
			resizeAnimationTime: 500
		},
		
		/**
		 * Public Methods.
		 */
		methods = 
		{
			/**
			 * Initialise.
			 * @param options (optional) settings-object to overwrite default settings.
			 */
			init: function(options) 
			{
				//save settings to data
				settings = $.extend(true, default_settings, options);
				updateSettings();
				
				//set table layout and box model
				this.css({
					"table-layout": "fixed",
					"box-sizing": "border-box",
					"-moz-box-sizing": "border-box", 
					"-webkit-box-sizing": "border-box",
					"box-sizing": "border-box"
				});
				this.find("th, td").css({
					"white-space": "nowrap",
					"overflow": "hidden",
					"box-sizing": "border-box",
					"-moz-box-sizing": "border-box", 
					"-webkit-box-sizing": "border-box",
					"box-sizing": "border-box"
				});				
				
				//get border spacing
				var border_spacing = 0;
				if($(table).css("border-collapse") !== "collapse")
				{
					border_spacing = $(table).css("border-spacing");
					if(border_spacing === "auto")
					{
						border_spacing = 2;
					}
					else
					{
						border_spacing = parseInt(border_spacing.substr(0, border_spacing.indexOf("px")));
					}
					if(!border_spacing)
					{
						border_spacing = 0;
					}
				}
				
				//init columns array
				var columns = [];
				var num_cols = this.find("th").size();
				for(var int = 0; int < num_cols; int++) 
				{
					var column = {};
					
					initColumnHTMLProperties(column, int);
					
					column.width = $(column.th).width();
					column.initialWidth = column.width;
					column.visible = true;
					column.sticky = ($.inArray(int, settings.sticky) !== -1);
					column.resizable = ($.inArray(int, settings.notResizable) === -1);

					//update width
					$(column.th).width(column.width + column.paddingCalc);

					//init manual slider
					if(int < num_cols - 1 && column.resizable && !column.sticky)
					{
						initSlider(column, int);				
					}
					
					columns.push(column);
				}
				
				//save properties to data
				props = {};
				props.columns = columns;
				props.border_spacing = border_spacing;
				props.animating = false;
				updateProperties();

				return this;
			},
			
			/**
			 * Reinitialize.
			 * This method reinitialises all DOM objects related to the columns and should be called if the table content changes, e.g. after sorting.
			 * If passed an columnProperties-array (@see getColumnProperties()), it sets the column properties (e.g. width, visibility) to those specified in the array.
			 * @param options (optional) settings-object to overwrite settings.
			 * @param columnProperties (optional) columnProperties-array to set column properties.
			 */
			reinit: function(options, columnProperties) 
			{
				if(!props)
				{
					methods.init.apply(this, arguments);
				}
				else
				{
					//save new settings
					settings = $.extend(true, settings, options);
					updateSettings();
					
					//reinit each columns th, tds, padding, border, resize helper
					$(props.columns).each(function(i)
					{
						initColumnHTMLProperties(this, i);

						//if table head was changed: set box model and init manual slider
						if($(this.th).find(".ResizableColumnHandle").size() === 0)
						{
							$(this.th).css({
								"white-space": "nowrap",
								"overflow": "hidden",
								"box-sizing": "border-box",
								"-moz-box-sizing": "border-box", 
								"-webkit-box-sizing": "border-box",
								"box-sizing": "border-box"
							});
						
							
							if(i < $(table).children("thead").find("th").size() - 1 && this.resizable && !this.sticky)
							{
								initSlider(this, i);
							} 
						}
					});
				}
				
				$(props.columns).each(function(i)
				{
					var column = this;
					
					//set given column properties
					if(columnProperties)
					{
						column = $.extend(column, columnProperties[i]);
					}

					//update visibility
					if(!column.visible)
					{
						hideColumn(column);
					}
					else
					{
						//update width
						$(column.th).width(column.width + column.paddingCalc);
					}
				});
			},
			
			/**
			 * Sets an option to a specified value.
			 * @param option the option to select.
			 * @param value the value to set.
			 */
			setOption: function(option, value)
			{
				settings[option] = value;
				updateSettings();
			},
			
			/**
			 * Resets all columns to their initial width and visibility.
			 */
			reset: function() 
			{
				$(props.columns).each(function()
				{
					this.width = this.initialWidth;
					$(this.th).width(this.initialWidth + this.paddingCalc);

					showColumn(this);
				});
			},
			
			/**
			 * Returns an array which contains the column properties.
			 * To be used upon reinit.
			 */
			getColumnProperties: function()
			{
				var allColumnProperties = [];
				
				$(props.columns).each(function(i)
				{
					var column = this,
						columnProperties = {};
						
					columnProperties.width = column.width;
					columnProperties.initialWidth = column.initialWidth;
					columnProperties.resizable = column.resizable;
					columnProperties.sticky = column.sticky;
					columnProperties.visible = column.visible;
					
					allColumnProperties.push(columnProperties);
				});
				
				return allColumnProperties;
			},
			
			/**
			 * Hides a specific column.
			 * @param index the index of the column to hide.
			 */
			hide: function(index) 
			{
				var more_than_1_column_left = false;
				for(var int = 0; int < props.columns.length; int++)
				{
					if(int !== index && props.columns[int].visible)
					{
						more_than_1_column_left = true;
						break;
					}				
				}
				
				var column_to_hide = props.columns[index];
				
				if(!more_than_1_column_left || props.animating || column_to_hide.sticky)
				{
					return false;
				}
				
				props.animating = true;
				updateProperties();
				
				var column_to_add_width_to = getVisibleNeighbourColumn(index, "left");
				
				if(!settings.resizeAnimation)
				{
					hideColumnAndUpdate();
					return true;
				}
				
				$(column_to_hide.th).find(".ResizableColumnHelper").animate(
	    		{
	    			"width": [1, settings.resizeAnimationStyle]
	    		}, 
	    		{
	    			duration: settings.resizeAnimationTime,
	    			queue: false,
	    			step: function(now, fx) 
	    			{
						now = Math.floor(now);

						$(column_to_hide.th).width(now + column_to_hide.paddingCalc);
						$(column_to_add_width_to.th).width(column_to_add_width_to.width + (column_to_hide.width - now) + column_to_add_width_to.paddingCalc);

					},
    				complete: function() 
    				{
						hideColumnAndUpdate();
	    			}
	    		});
				
				function hideColumnAndUpdate()
				{				
					hideColumn(column_to_hide);
					
					//set width to 1 in case show event will be animated
					$(column_to_hide.th).width(1 + column_to_hide.padding); 
					
					$(column_to_add_width_to.th).width(column_to_add_width_to.width + column_to_add_width_to.paddingCalc + column_to_hide.width + column_to_hide.padding + props.border_spacing);
					column_to_add_width_to.width = column_to_add_width_to.width + column_to_hide.width + column_to_hide.padding + props.border_spacing;
					
					var last_visible_index = getLastVisibleColumnIndex();
					if($(table).find("th").get(last_visible_index) === $(column_to_add_width_to.th)[0])
					{
						$(column_to_add_width_to.th).find(".ResizableColumnHandle").hide();
						$(column_to_add_width_to.th).find(".ui-resizable-handle").hide()
					}
				
					$(".ResizableColumnHelper").width("100%");
					props.animating = false;
					updateProperties();
				}
			},
			
			/**
			 * Shows a specific column.
			 * @param index the index of the column to show.
			 */
			show : function(index) 
			{
				var column_to_show = props.columns[index];

				if(column_to_show.sticky || props.animating)
				{
					return false;
				}

				props.animating = true;
				updateProperties();
				
				var column_to_remove_width_from = getColumnToRemoveWidthFrom(column_to_show, index),
					space_to_take = column_to_remove_width_from[1],
					column_to_remove_width_from = column_to_remove_width_from[0];
				
				$(column_to_show.th).find(".ResizableColumnHandle").show();
				$(column_to_show.th).find(".ui-resizable-handle").show();
				
				var last_visible_index = getLastVisibleColumnIndex();
				if(index > last_visible_index)
				{
					$(props.columns[last_visible_index].th).find(".ResizableColumnHandle").show();
					$(props.columns[last_visible_index].th).find(".ui-resizable-handle").show()
					
					$(column_to_show.th).find(".ResizableColumnHandle").hide();
					$(column_to_show.th).find(".ui-resizable-handle").hide()
				}
				
				if(!settings.resizeAnimation)
				{
					showColumn(column_to_show);
					
					$(column_to_show.th).width(space_to_take + column_to_show.paddingCalc);
					column_to_show.width = space_to_take;
					
					if($.browser.msie && $.browser.version == 7.0)
					{
						$(column_to_remove_width_from.th).width(column_to_remove_width_from.width - space_to_take - props.border_spacing - column_to_show.padding);
					}
					else
					{
						$(column_to_remove_width_from.th).width(column_to_remove_width_from.width - space_to_take - props.border_spacing); 
					}
					column_to_remove_width_from.width = column_to_remove_width_from.width - space_to_take - column_to_show.padding - props.border_spacing;

					update();
					
					return true;
				}
				
				showColumn(column_to_show);
				
				$(column_to_remove_width_from.th).width(column_to_remove_width_from.width + column_to_remove_width_from.paddingCalc - column_to_show.padding - column_to_show.border - props.border_spacing);
				column_to_remove_width_from.width = column_to_remove_width_from.width - column_to_show.padding - props.border_spacing;
		
				$(column_to_show.th).find(".ResizableColumnHelper").animate(
	    		{
	    			"width": [space_to_take, settings.resizeAnimationStyle]
	    		}, 
	    		{
	    			duration: settings.resizeAnimationTime,
	    			queue: false,
	    			step: function(now, fx) 
	    			{
						now = Math.floor(now);
						
						$(column_to_show.th).width(now + column_to_show.paddingCalc);
						$(column_to_remove_width_from.th).width(column_to_remove_width_from.width + column_to_remove_width_from.paddingCalc - now);
					},
    				complete: function() 
    				{
						column_to_show.width = space_to_take;
						column_to_remove_width_from.width = column_to_remove_width_from.width - space_to_take;
						
						update();
	    			}
	    		});
				
				function update()
				{
					$(".ResizableColumnHelper").width("100%");
					props.animating = false;
					updateProperties();
				}
			},

			/**
			 * Toggles the visibility of a specific column.
			 * @param index the index of the column to toggle.
			 */
			toggle: function(index) 
			{
				if(props.columns[index].visible)
				{
					methods.hide(index);
				}
				else
				{
					methods.show(index);
				}
			},
			
			/**
			 * Returns whether there is currently an animation going on.
			 */
			isAnimating: function()
			{
				return props.animating;
			},
			
			/**
			 * Returns an array containing the indices of all hidden columns.
			 */
			getHiddenColumnIndices: function()
			{
				var indices = [];
				for (var int = 0; int < props.columns.length; int++) 
				{
					if(!props.columns[int].visible)
					{
						indices.push(int);
					}
				}
				return indices;
			}
		};
		
		/**
		 * Private Methods.
		 */
		
		/**
		 * Saves the properties to table data.
		 */
		function updateProperties()
		{
			$(table).data("properties", props);
		}
		
		/**
		 * Saves the settings to table data.
		 */
		function updateSettings()
		{
			$(table).data("settings", settings);
		}
		
		/**
		 * Hides all DOM objects related to a column and updates its visibility.
		 */		
		function hideColumn(column)
		{
			$(column.th).hide();
			$(column.tds).each(function()
			{
				$(this).hide();
			});
			column.visible = false;
		}
		
		/**
		 * Shows all DOM objects related to a column and updates its visibility.
		 */		
		function showColumn(column)
		{
			$(column.th).show();
			$(column.tds).each(function()
			{
				$(this).show();
			});
			column.visible = true;
		}
		
		/**
		 * Attaches all DOM objects related to a column to a column object.
		 * @param column the column object to attach the properties to.
		 * @param index the index of the column.
		 */
		function initColumnHTMLProperties(column, index)
		{
			column.th = $(table).find("th").get(index);		
			column.tds = [];
			$(table).children("tbody").find("tr").each(function() 
			{
				column.tds.push($(this).children("td").get(index));
			});		
			
			var resize_helper = $(column.th).children(".ResizableColumnHelper");
			if(resize_helper.size() === 0)
			{
				resize_helper = $("<div class='ResizableColumnHelper'>"+$(column.th).html()+"</div>");
				$(resize_helper).css("white-space", "nowrap");
				$(column.th).html(resize_helper);
			}
			$(resize_helper).css("width", "100%");

			column.padding_right = parseInt($(column.th).css("padding-right"), 10);
			column.border = $(resize_helper).width() - $(column.th).width();
			column.padding = $(column.th).outerWidth() - $(column.th).width();
			
			if($.browser.msie && $.browser.version == 7.0)
			{	//ie box model fix
				column.paddingCalc = 0;
				column.padding -= column.border;
			}
			else
			{
				column.paddingCalc = column.padding;
			}
		}
		
		/**
		 * Attaches a slider to a column to manually resize it.
		 * @param column the column object to attach the slider to.
		 * @param index the index of the column.
		 */
		function initSlider(column, index) {
		
			var resize_helper = $(column.th).children(".ResizableColumnHelper");
			
			$(resize_helper).resizable(
			{
				handles: "e",
				minWidth: settings.minColumnWidth,
				start: function(event, ui)
				{
					neighbour_column = getVisibleNeighbourColumn(index);

					if(settings.showRuler)
					{
						$("<div id='VerticalRuler'></div>")
							.css({
								"position": "absolute",
								"height": $(table).outerHeight(),
								"left": $(column.th).offset().left + column.width + column.padding - settings.rulerCSS.width,
								"top": $(table).offset().top
							})
							.css(settings.rulerCSS)
							.appendTo("body");
					}
				},
				
				resize: function(event, ui)
				{
					var column_new_width = $(this).width() - column.border,
						width_dif = column.width - column_new_width,
						neighbour_column_new_width = neighbour_column.width + width_dif;						
					
					if(neighbour_column_new_width < settings.minColumnWidth)
					{
						var dif = settings.minColumnWidth - neighbour_column_new_width;

						column_new_width -= Math.floor(dif);
						neighbour_column_new_width = settings.minColumnWidth;

						$(this).trigger("mouseup"); 
					}
					
					column.width = column_new_width;
					neighbour_column.width = neighbour_column_new_width;

					$(column.th).width(column_new_width + column.paddingCalc);
					$(neighbour_column.th).width(neighbour_column_new_width + neighbour_column.paddingCalc);					
					
					$(".ResizableColumnHelper").css("width", "100%");
					
					if(settings.showRuler)
					{
						$("#VerticalRuler").css("left", $(this).offset().left + $(this).width() - settings.rulerCSS.width + column.padding_right);
					}
				},
				stop: function(event, ui)
				{
					$("#VerticalRuler").remove();
				}
			});
			
			if(settings.showHandle && $(resize_helper).children(".ResizableColumnHandle").size() === 0)
			{
				$("<div class='ResizableColumnHandle'></div>")
					.css({
						"position" : "absolute",
						"right" : -column.padding_right,
						"top" : 0,
						"height": "100%"
					})
					.css(settings.handleCSS)
					.appendTo(resize_helper);
			}
			
			$(column.th).find(".ui-resizable-handle").css({
				"background-color": "white",
				"opacity": 0.0001, //ie transparency hack
				right: -column.padding_right
			});
		}
		
		/**
		 * Returns the column closest to a given column index that is visible and not sticky.
		 * If not specified otherwise, the first column on the right will be selected if possible.
		 * @param index the index of the column.
		 * @param indicates whether to prefer the left neighbours.
		 */
		function getVisibleNeighbourColumn(index, left)
		{
			var index = index,
				neighbour_column;
			
			function searchToLeft(index)
			{
				var neighbour_column;
				while(index > 0)
				{
					neighbour_column = props.columns[index - 1];
					if(neighbour_column.visible && !neighbour_column.sticky)
					{
						return neighbour_column;
					}
					index--;
				}
				return null;
			}
			function searchToRight(index)
			{
				var neighbour_column;
				while(index < props.columns.length - 1)
				{
					neighbour_column = props.columns[index + 1];
					if(neighbour_column.visible && !neighbour_column.sticky)
					{
						return neighbour_column;
					}
					index++;
				}
				return null;
			}
			
			if(left)
			{
				neighbour_column = searchToLeft(index);
				if(!neighbour_column)
				{
					neighbour_column = searchToRight(index);
				}
			}
			else
			{
				neighbour_column = searchToRight(index);
				if(!neighbour_column)
				{
					neighbour_column = searchToLeft(index);
				}
			}
			return neighbour_column;
		}

		/**
		 * Returns an array that contains a column to take space from and a space parameter which indicates how much space to take from that column during animation.
		 * @param column the column to insert into the table.
		 * @param index the index of the column to insert into the table.
		 */
		function getColumnToRemoveWidthFrom(column, index)
		{
			var next_column_right = getVisibleNeighbourColumn(index),
				next_column_left = getVisibleNeighbourColumn(index, "left")
				widest_column = getWidestVisibleColumn(),
				toReturn = []; // 0 = column, 1 = width
			
			toReturn[0] = getColumnAndAvailableSpace(next_column_right, next_column_left, widest_column, column.initialWidth);
			toReturn[1] = column.initialWidth;
			
			if(!toReturn[0])
			{
			
				toReturn[0] = getColumnAndAvailableSpace(next_column_right, next_column_left, widest_column, settings.minColumnWidth);
				toReturn[1] = settings.minColumnWidth;
			}
			
			return toReturn;
			
			function getColumnAndAvailableSpace(right, left, widest, needed_space)
			{
				if(right.width - settings.minColumnWidth >= needed_space)
				{
					return right;
				}
				else if(left.width - settings.minColumnWidth >= needed_space)
				{
					return left;
				}
				else if(widest.width - settings.minColumnWidth >= needed_space)
				{
					return widest;
				}
			}
		}
		
		/**
		 * Returns the widest column that is visible and not sticky.
		 */
		function getWidestVisibleColumn()
		{
			var widest = null;
			var widest_available_width = -1;
			for(var int = 0; int < props.columns.length; int++) 
			{
				var column_to_check = props.columns[int];
				if(column_to_check.visible && !column_to_check.sticky)
				{
					if(column_to_check.width > widest_available_width)
					{
						widest_available_width = column_to_check.width;
						widest = column_to_check;
					}
				}
			}
			return widest;
		}
		
		/**
		 * Returns the index of the last column that is visible and not sticky.
		 */
		function getLastVisibleColumnIndex()
		{
			for(var int = props.columns.length - 1; int >= 0; int--)
			{
				if(props.columns[int].visible && !props.columns[int].sticky)
				{
					return int;
				}
			}
		}
		
		/**
		 * Method calling logic.
		 */
		if(methods[method]) 
	    {
	    	return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
	    } 
	    else if(typeof method === "object" || ! method) 
	    {
	    	return methods.init.apply(this, arguments);
	    } 
	    else 
		{
	    	$.error("Method " +  method + " does not exist on jQuery.dynamicTable");
	    }
	
	};
})(jQuery);