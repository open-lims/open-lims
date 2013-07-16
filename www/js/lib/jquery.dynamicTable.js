/**
 * dynamicTable jQuery plugin.
 * Allows dynamic resizing and display of table columns.
 *
 * version: 0.3.2
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
	"use strict";
	$.fn.dynamicTable = function(method)
	{
		var table = this,
			props = $(this).data("properties"),	//columns, animating, border_spacing, resize_neighbour_column
			settings = $(this).data("settings"),//extended default settings
		
		default_settings = 
		{
			sticky: [],
			notResizable: [],
			minColumnWidth: 20,
			showHandle: true,
			handleCSS: {
				"width": 1,
				"min-width": 1,
				"background-color": "#000000",
				"z-index": 100
			},
			showRuler: true,
			rulerCSS: {
				"width": 1,
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
				settings = $.extend(true, default_settings, options);
				updateSettings();
				
				this.css({
					"table-layout": "fixed",
					"box-sizing": "border-box",
					"-moz-box-sizing": "border-box", 
					"-webkit-box-sizing": "border-box"
				});
				this.find("th, td").css({
					"white-space": "nowrap",
					"overflow": "hidden",
					"box-sizing": "border-box",
					"-moz-box-sizing": "border-box", 
					"-webkit-box-sizing": "border-box"
				});				
				
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
						border_spacing = parseInt(border_spacing.substr(0, border_spacing.indexOf("px")), 10);
					}
					if(!border_spacing)
					{
						border_spacing = 0;
					}
				}
				
				var columns = [],
					num_cols = this.find("th").size();
				for(var int = 0; int < num_cols; int++) 
				{
					var column = {
						visible: true,
						sticky: $.inArray(int, settings.sticky) !== -1,
						resizable: $.inArray(int, settings.notResizable) === -1
					};
					
					initColumnHTMLProperties(column, int);
					
					column.width = $(column.th).width();
					column.initialWidth = column.width;
					
					$(column.th).width(column.width + column.paddingCalc);

					if(int < num_cols - 1 && column.resizable && !column.sticky)
					{
						initSlider(column, int);				
					}
					
					columns[int] = column;
				}
				
				props = {
					columns: columns,
					border_spacing: border_spacing,
					animating: false
				};
				updateProperties();

				return this;
			},
			
			/**
			 * Reinitialize.
			 * This method reinitialises all DOM objects related to the columns and should be called if the table content changes, e.g. after sorting.
			 * If passed a columnProperties-array (@see getColumnProperties()), it sets the column properties (e.g. width, visibility) to those specified in the array.
			 * @param options (optional) settings-object to overwrite settings.
			 * @param columnProperties (optional) columnProperties-array to set column properties.
			 */
			reinit: function(options, columnProperties) 
			{
				var int,
					column,
					columns,
					numColumns;
					
				if(!props)
				{
					methods.init.apply(this, arguments);
					columns = props.columns;
					numColumns = props.columns.length;
				}
				else
				{
					settings = $.extend(true, settings, options);
					updateSettings();
					
					$(table).find("th, td").css({
						"white-space": "nowrap",
						"overflow": "hidden",
						"box-sizing": "border-box",
						"-moz-box-sizing": "border-box", 
						"-webkit-box-sizing": "border-box"
					});
				
					columns = props.columns;
					numColumns = props.columns.length;
					for(int = 0; int < numColumns; int++)
					{
						column = columns[int];
						initColumnHTMLProperties(column, int);
						
						if($(column.th).find("div.ResizableColumnHandle").size() === 0 && int < numColumns - 1 && column.resizable && !column.sticky)
						{
							initSlider(column, int);
						}
					}
				}
				
				for(int = 0; int < numColumns; int++)
				{
					column = columns[int];
					
					if(columnProperties)
					{
						column = $.extend(column, columnProperties[int]);
					}
					
					if(!column.visible)
					{
						hideColumn(column);
						$(column.th).width(1 + column.padding);
						
						var last_visible_index = getLastVisibleColumnIndex();
						if(int > last_visible_index)
						{
							var last_visible_th = $(table).find("th").get(last_visible_index);
							$(last_visible_th).find("div.ResizableColumnHandle, div.ui-resizable-handle").hide();
						}
					}
					else
					{
						$(column.th).width(column.width + column.paddingCalc);
					}
				}
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
				var columns = props.columns;
				for(var int = 0; int < columns.length; int++)
				{
					var column = columns[int];
					
					column.width = column.initialWidth;
					$(column.th).width(column.initialWidth + column.paddingCalc);
					showColumn(column);
				}
			},
			
			/**
			 * Returns an array that contains the column properties.
			 * To be used upon reinit.
			 */
			getColumnProperties: function()
			{
				var allColumnProperties = [],
					columns = props.columns;
				
				for(var int = 0; int < columns.length; int++)
				{
					var column = columns[int];
					
					allColumnProperties[int] = {
						width: column.width,
						initialWidth: column.initialWidth,
						resizable: column.resizable,
						sticky: column.sticky,
						visible: column.visible
					};
				}
				
				return allColumnProperties;
			},
			
			/**
			 * Hides a specific column.
			 * @param index the index of the column to hide.
			 */
			hide: function(index) 
			{
				var more_than_1_column_left = false,
					columns = props.columns;
				
				for(var int = 0; int < columns.length; int++)
				{
					var column = columns[int];
					if(int !== index && column.visible && !column.sticky)
					{
						more_than_1_column_left = true;
						break;
					}				
				}
				
				var column_to_hide = columns[index];

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
				
				$(column_to_hide.th).find("div.ResizableColumnHelper").animate(
				{
					"width": [1, settings.resizeAnimationStyle]
				},
				{
					duration: settings.resizeAnimationTime,
					queue: false,
					step: function(now)
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
				
				return true;
				
				function hideColumnAndUpdate()
				{				
					hideColumn(column_to_hide);
					$(column_to_hide.th).width(1 + column_to_hide.padding); 
					
					var column_to_add_width_to_width = column_to_add_width_to.width + column_to_hide.width + column_to_hide.padding + props.border_spacing;
					
					column_to_add_width_to.width = column_to_add_width_to_width;
					$(column_to_add_width_to.th).width(column_to_add_width_to_width + column_to_add_width_to.paddingCalc);

					var last_visible_index = getLastVisibleColumnIndex();
					if($(table).find("th").get(last_visible_index) === $(column_to_add_width_to.th)[0])
					{
						$(column_to_add_width_to.th).find("div.ResizableColumnHandle, div.ui-resizable-handle").hide();
					}
				
					$("div.ResizableColumnHelper").width("100%");
					
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
				
				var column_to_remove_width_from_props = getColumnToRemoveWidthFrom(column_to_show, index),
					space_to_take = column_to_remove_width_from_props[1],
					column_to_remove_width_from = column_to_remove_width_from_props[0];
				
				$(column_to_show.th).find("div.ResizableColumnHandle, div.ui-resizable-handle").show();
				
				var last_visible_index = getLastVisibleColumnIndex();
				if(index > last_visible_index)
				{
					$(props.columns[last_visible_index].th).find("div.ResizableColumnHandle, div.ui-resizable-handle").show();
					$(column_to_show.th).find("div.ResizableColumnHandle, div.ui-resizable-handle").hide();
				}
				
				if(!settings.resizeAnimation)
				{
					showColumn(column_to_show);
					
					$(column_to_show.th).width(space_to_take + column_to_show.paddingCalc);
					column_to_show.width = space_to_take;
					
					var column_to_remove_width_from_width = column_to_remove_width_from.width - space_to_take - props.border_spacing;
					
					column_to_remove_width_from.width = column_to_remove_width_from_width - column_to_show.padding;
					
					if($.browser.msie && $.browser.version == 7.0)
					{
						$(column_to_remove_width_from.th).width(column_to_remove_width_from_width - column_to_show.padding);
					}
					else
					{
						$(column_to_remove_width_from.th).width(column_to_remove_width_from_width); 
					}

					update();
					
					return true;
				}
				
				showColumn(column_to_show);
				
				var column_to_remove_width_from_new_width = column_to_remove_width_from.width - column_to_show.padding - props.border_spacing;
				
				$(column_to_remove_width_from.th).width(column_to_remove_width_from_new_width + column_to_remove_width_from.paddingCalc - column_to_show.border);
				column_to_remove_width_from.width = column_to_remove_width_from_new_width;
		
				$(column_to_show.th).find("div.ResizableColumnHelper").animate(
				{
					"width": [space_to_take, settings.resizeAnimationStyle]
				},
				{
					duration: settings.resizeAnimationTime,
					queue: false,
					step: function(now)
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
				
				return true;
				
				function update()
				{
					$("div.ResizableColumnHelper").width("100%");
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
			 * Returns whether there is currently an animation going on or not.
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
				var indices = [],
					columns = props.columns;
					
				for (var int = 0; int < columns.length; int++) 
				{
					if(!columns[int].visible)
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
			
			var tds = column.tds;
			for(var int = 0; int < tds.length; int++)
			{
				$(tds[int]).hide();
			}
			column.visible = false;
		}
		
		/**
		 * Shows all DOM objects related to a column and updates its visibility.
		 */		
		function showColumn(column)
		{
			$(column.th).show();
			
			var tds = column.tds;
			for(var int = 0; int < tds.length; int++)
			{
				$(tds[int]).show();
			}
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
			$(table).children("tbody").find("tr").each(function(i) 
			{
				column.tds[i] = $(this).children("td").get(index);
			});		
			
			var resize_helper = $(column.th).children("div.ResizableColumnHelper");
			if(resize_helper.size() === 0)
			{
				resize_helper = $("<div class='ResizableColumnHelper'>"+$(column.th).html()+"</div>");
				$(resize_helper).css("white-space", "nowrap");
				$(column.th).html(resize_helper);
			}
			$(resize_helper).css("width", "100%");

			if(!column.visible) 
			{
				showColumn(column);
				column.visible = false;
			}
			
			column.padding_right = parseInt($(column.th).css("padding-right"), 10);
			column.border = $(resize_helper).width() - $(column.th).width();
			column.padding = $(column.th).outerWidth() - $(column.th).width();
			
			if(!column.visible)
			{
				hideColumn(column);
			}
			
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
		
			var resize_helper = $(column.th).children("div.ResizableColumnHelper");
			
			$(resize_helper).resizable(
			{
				handles: "e",
				minWidth: settings.minColumnWidth,
				start: function()
				{
					props.resize_neighbour_column = getVisibleNeighbourColumn(index);

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
				
				resize: function()
				{
					var column_new_width = $(this).width() - column.border,
						width_dif = column.width - column_new_width,
						neighbour_column = props.resize_neighbour_column,
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
					
					$(table).find("div.ResizableColumnHelper").css("width", "100%");
					
					if(settings.showRuler)
					{
						$("#VerticalRuler").css("left", $(this).offset().left + $(this).width() - settings.rulerCSS.width + column.padding_right);
					}
				},
				stop: function()
				{
					props.resize_neighbour_column = null;
					$("#VerticalRuler").remove();
				}
			});
			
			if(settings.showHandle && $(resize_helper).children("div.ResizableColumnHandle").size() === 0)
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
			
			$(column.th).find("div.ui-resizable-handle").css({
				"right": -column.padding_right,
				
				//ie transparency hack
				"background-color": "white",
				"opacity": 0.0001
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
			var columns = props.columns,
				neighbour_column;
			
			function searchToLeft(index)
			{
				var neighbour_column;
				while(index > 0)
				{
					neighbour_column = columns[index - 1];
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
				var neighbour_column,
					last_index = columns.length - 1;
				while(index < last_index)
				{
					neighbour_column = columns[index + 1];
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
				next_column_left = getVisibleNeighbourColumn(index, "left"),
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
			var columns = props.columns,
				widest = null,
				widest_available_width = -1;
			
			for(var int = 0; int < columns.length; int++) 
			{
				var column = columns[int];
				if(column.visible && !column.sticky)
				{
					if(column.width > widest_available_width)
					{
						widest_available_width = column.width;
						widest = column;
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
			var columns = props.columns;
			for(var int = columns.length - 1; int >= 0; int--)
			{
				var column = columns[int];
				if(column.visible && !column.sticky)
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
		else if(typeof method === "object" || !method)
		{
			return methods.init.apply(this, arguments);
		}
		else
		{
			$.error("Method " +  method + " does not exist on jQuery.dynamicTable");
		}
	};
})(jQuery);