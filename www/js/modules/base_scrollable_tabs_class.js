/**
 * version: 0.4.0.0
 * author: Roman Quiring <quiring@open-lims.org>
 * copyright: (c) 2008-2011 by Roman Quiring
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

function scrollable_tabs(tab_list,max_tabs,hide_arrows_if_deactivated,center_tabbar,arrow_left_id,arrow_right_id,camera_id,classname_active)
{
	var tabs = tab_list;
	var max_tabs = max_tabs;
	var num_tabs = $(tabs).children("li").length;
	var tabs_to_center;
	
	var tab_width;
	var max_tab_width;
	
	var arrow_left = $("#"+arrow_left_id);
	var arrow_right = $("#"+arrow_right_id);
	var camera = $("#"+camera_id);
	
	if (typeof(scrollable_tabs_prototype_called) == "undefined")
	{
		init();
	}
	
	/**
	 * Initialise.
	 */
	function init()
	{
		tabs_to_center = max_tabs / 2;
		if(tabs_to_center % 2 != 0)
		{
			tabs_to_center = Math.floor(tabs_to_center);
		}
		
		tab_width = $(tab_list).children().width();
		var tab_height = $(tab_list).children().height();
		max_tabs_width = tab_width * num_tabs;
		
		var camera_height = $(tabs).children().height();
		var camera_width;
		if(num_tabs < max_tabs)
		{
			camera_width = tab_width * num_tabs;
		}
		else
		{
			camera_width = tab_width * max_tabs;
		}
		$(camera).css(
		{
			"float":"left",
			"width":camera_width,
			"height":camera_height,
			"overflow":"hidden"
		});
		
		if(center_tabbar == true)
		{
			var container_width = camera_width + 32;
			var container_margin = ($(camera).parent().width() - container_width) / 2;
			$(arrow_left).css("margin-left",container_margin+"px");
		}
		
		if(hide_arrows_if_deactivated == true)
		{
			if(num_tabs <= max_tabs)
			{
				$(arrow_left).children("img").hide();
				$(arrow_right).children("img").hide();
				return;
			}
		}
		
		$(tabs).children().click(function()
		{
			if($(arrow_left).hasClass("buttonInactive") && $(arrow_right).hasClass("buttonInactive"))
			{
				//num_tabs < max_tabs, no scrolling
			}
			else
			{
				focus_tab($(this).children().text(),true);
			}
		});
		
		var arrow_vertical_offset = (tab_height-16) / 2;
		
		$(arrow_left)
			.addClass("buttonInactive")
			.css(
			{
				"float":"left",
				"padding-top":arrow_vertical_offset
			})
			.click(function()
			{
				if(!$(this).hasClass("buttonInactive"))
				{
					if(!$(this).hasClass("showHiddenTabs")) 
					{
						$(this).addClass("showHiddenTabs")
						$(arrow_left).children("img").rotate({animateTo:-90,duration:300});
						show_hidden_tabs("left");
					}
					else
					{
						$(this).attr("class","");
						$(arrow_left).children("img").rotate({animateTo:0,duration:300});
						$("#hiddenTabs"+$(tabs).attr("class")).remove();
					}
				}
			});
		
		$(arrow_right)
			.addClass("buttonInactive")
			.css(
			{
				"float":"left",
				"padding-top":arrow_vertical_offset
			})
			.click(function()
			{
				if(!$(this).hasClass("buttonInactive"))
				{
					if(!$(this).hasClass("showHiddenTabs")) 
					{
						$(this).addClass("showHiddenTabs")
						$(arrow_right).children("img").rotate({animateTo:90,duration:300});
						show_hidden_tabs("right");
					}
					else
					{
						$(this).attr("class","");
						$(arrow_right).children("img").rotate({animateTo:0,duration:300});
						$("#hiddenTabs"+$(tabs).attr("class")).remove();
					}
				}
			});
		
		if(num_tabs > max_tabs)
		{
			$(tabs).css("width",max_tabs_width);
			$(arrow_right).removeClass("buttonInactive");
			focus_tab($("."+classname_active).text(),false);
		}
		
		if($(arrow_left).hasClass("buttonInactive"))
		{
			$(arrow_left).children("img").attr("src","images/1leftarrow_inactive.png");
		}
		else
		{
			$(arrow_left).children("img").attr("src","images/1leftarrow.png");
		}
		
		if($(arrow_right).hasClass("buttonInactive"))
		{
			$(arrow_right).children("img").attr("src","images/1rightarrow_inactive.png");
		}
		else
		{
			$(arrow_right).children("img").attr("src","images/1rightarrow.png");
		}
	}

	/**
	 * Opens the dropdown menu that lists the hidden tabs.
	 * @param side the side on which to append the menu, either left or right
	 */
	function show_hidden_tabs(side) 
	{
		var current_offset = parseInt($(tabs).css("margin-left").replace("px",""));
				
		var hidden_tabs_div = $("<div></div>")
			.attr("id","hiddenTabs"+$(tabs).attr("class"))
			.css(
			{
				"position":"absolute",
				"width":tab_width,
				"background-color":"white",
				"border":"solid black 1px",
				"padding":"2px",
				"font-family":"arial",
				"font-size":"12px",
				"z-index":"200"
			})
			.hide();
		
		var position;
		var num_hidden_tabs;
		var hidden_tabs;
		
		if(side == "left")
		{
			position = $(arrow_left).position();
			num_hidden_tabs = -(current_offset / tab_width);
			hidden_tabs = $(tabs+" li:lt("+num_hidden_tabs+")");
		}
		else
		{
			position = $(arrow_right).position();
			last_visible_tab = -(current_offset - (max_tabs * tab_width)) / tab_width -1;
			hidden_tabs = $(tabs+" li:gt("+last_visible_tab+")");
		}
		
		$(hidden_tabs).each(function()
		{
			var html = $("<div><a href=''>"+$(this).children().text()+"</a></div>")
				.hover(function()
				{
					$(this).css("background-color","#cccccc");
				}
				,function()
				{
					$(this).css("background-color","white");
				})
				.click(function()
				{
					focus_tab($(this).text(),true);
					if(side == "left")
					{
						$(arrow_left).children("img").rotate({animateTo:0,duration:300});
					}
					else
					{
						$(arrow_right).children("img").rotate({animateTo:0,duration:300});
					}
				})
				.css("padding","2px 4px");
			hidden_tabs_div.append(html);
		});
		
		if(side == "left")
		{
			var margin_left = parseInt($(arrow_left).css("margin-left").replace("px",""));
			$(hidden_tabs_div).css(
			{
				"left":position.left + margin_left,
				"top":position.top + 17
			});
		}
		else
		{
			$(hidden_tabs_div).css(
			{
				"text-align":"right",
				"left": position.left + 10 - tab_width,
				"top":position.top + 17
			});
		}
		
		$(hidden_tabs_div).children().children().css(
		{
			"text-decoration":"none",
			"color":"black"
		});
		
		$(hidden_tabs_div).appendTo($(tabs).parent().parent()).fadeIn(300);
	}
	
	/**
	 * Selects a tab and loads its content.
	 * @param capture the capture of the tab to select
	 * @param slide indicates whether to animate the tab bar or not
	 */
	function focus_tab(capture,slide)
	{
		capture = $.trim(capture);
		$(arrow_right).attr("class","");
		$(arrow_left).attr("class","");

		var selected;
		$(tabs+" > li > a").filter(function() 
		{
		    if($(this).text() === capture)
		    {
		    	selected = this;
		    	return true;
		    }
		});	
		var number_of_previous_tabs = $(selected).parent().prevAll().size();

		var offset = -(number_of_previous_tabs * tab_width - (tabs_to_center * tab_width));
		var max_offset = -(max_tabs_width - (max_tabs * tab_width));
		
		if(offset <= max_offset)
		{
			offset = max_offset;
			$(arrow_right).addClass("buttonInactive");
		}
		else if(offset >= 0)
		{
			offset = 0;
			$(arrow_left).addClass("buttonInactive");
		}
		
		var url = $(selected).attr("href");
		
		if(slide==true)
		{
			$(tabs).animate({"margin-left":offset+"px"},100,function()
			{
				$(location).attr('href',url);
			});
		}
		else
		{
			$(tabs).css("margin-left",offset+"px");
		}
	}
}
