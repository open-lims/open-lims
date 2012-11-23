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

function scrollable_tabs()
{
	var tab_container = $(".SmallTabList");
	var max_total_tab_width = $(tab_container).width() - 50;
	var num_tabs = $(tab_container).children().length;
	var offset = 0;
	var rest_margin = 0;

	init();
	
	/**
	 * Initialise.
	 */
	function init()
	{
		var total_tab_width = 0;
		$(tab_container).children().each(function(){
			total_tab_width += $(this).width();
		});
		
		$(tab_container)
			.css({
				"margin-left": 0,
				"width": total_tab_width //set width to max to prevent line break -> position() would not be correct
			});
	
		var camera = $("<div id='SmallTabCamera'></div>")
			.css({
				"overflow":"hidden",
				"margin-left":25,
				"margin-right":25
			});
		$(tab_container).wrap(camera);
		
		if(total_tab_width > max_total_tab_width)
		{
			append_arrows();		
			hide_invisible_tabs_and_align_left();
			disable_arrows_if_needed();
		}
		
		console.log(total_tab_width+" "+max_total_tab_width);
	}
	
	/**
	 * Appends the left and right arrow to the menu.
	 */
	function append_arrows()
	{
		var camera_position = $("#SmallTabCamera").position();
		// var camera_position = 25;
		var camera_width = $("#SmallTabCamera").width();
		
		var left_arrow_x = camera_position.left + 5;
		var left_arrow_y = camera_position.top + 5;
		
		var right_arrow_x = camera_position.left + camera_width - 25;
		var right_arrow_y = left_arrow_y;
		
		var left_arrow = $("<img src='images/tabs/arrow_left_active.png' id='SmallTabArrowLeft'/>")
			.css({
				"position": "absolute",
				"left": left_arrow_x,
				"top": left_arrow_y,
				"z-index": 1000
			})
			.click(function()
			{
				if($(this).hasClass("Disabled"))
				{
					return false;
				}
				
				if($(this).data("hiddenTabsActive") !== true)
				{
					$(this).rotate({animateTo:-90,duration:300});
					show_hidden_tabs_menu(left_arrow_x, left_arrow_y + 25, left_arrow, "left");
					$(this).data("hiddenTabsActive", true);
				}
				else
				{
					$(this).rotate({animateTo:0,duration:300})
					hide_hidden_tabs_menu();
					$(this).data("hiddenTabsActive", false);
				}
			})
			.appendTo("#SmallTabCamera");
		
		var right_arrow = $("<img src='images/tabs/arrow_right_active.png' id='SmallTabArrowRight'/>")
			.css({
				"position": "absolute",
				"left": right_arrow_x,
				"top": right_arrow_y,
				"z-index": 1000
			})
			.click(function()
			{
				if($(this).hasClass("Disabled"))
				{
					return false;
				}
				
				if($(this).data("hiddenTabsActive") !== true)
				{
					$(this).rotate({animateTo:90,duration:300})
					show_hidden_tabs_menu(right_arrow_x, right_arrow_y + 25, right_arrow, "right");
					$(this).data("hiddenTabsActive", true);
				}
				else
				{
					$(this).rotate({animateTo:0,duration:300})
					hide_hidden_tabs_menu();
					$(this).data("hiddenTabsActive", false);
				}
			})
			.appendTo("#SmallTabCamera");
	}
	
	/**
	 * Disables the arrows if there is enough space.
	 */
	function disable_arrows_if_needed()
	{		
		if(!$(".SmallTabList").children(":first").hasClass("ToBeHidden"))
		{
			$("#SmallTabArrowLeft")
				.attr("src","images/tabs/arrow_left_inactive.png")
				.addClass("Disabled");
		}
		else
		{
			$("#SmallTabArrowLeft")
				.attr("src","images/tabs/arrow_left_active.png")
				.removeClass("Disabled");
		}
		
		if(!$(".SmallTabList").children(":last").hasClass("ToBeHidden"))
		{
			$("#SmallTabArrowRight")
				.attr("src","images/tabs/arrow_right_inactive.png")
				.addClass("Disabled");
		}
		else
		{
			$("#SmallTabArrowRight")
				.attr("src","images/tabs/arrow_right_active.png")
				.removeClass("Disabled");
		}
	}
	
	/**
	 * Displays the arrow menu that shows hidden tabs.
	 * @param x the x-coordinate.
	 * @param y the y-coordinate.
	 * @param arrow the arrow.
	 */
	function show_hidden_tabs_menu(x, y, arrow, anchor)
	{
		var hidden_tabs_menu = $("<div id='SmallTabHiddenTabsMenu'></div>")
			.css(
			{
				"top": y,
				"left": x
			})
			.appendTo("#SmallTabCamera")
			.fadeIn(200)
			.click(function(evt){
				if($(evt.target).hasClass("SmallTabHiddenTabsMenuEntry"))
				{
					var tab_to_be_focused = $(evt.target).data("RelatesToTab");
					focus_tab(tab_to_be_focused);
					$(arrow).trigger("click");
				}
				
			});
		
		
		
		if($(arrow).attr("id") === "SmallTabArrowLeft")
		{
			for ( var int = 1; int <= num_tabs; int++) 
			{
				var tab_to_check = $(tab_container).children(":nth-child("+int+")");
				if(!$(tab_to_check).hasClass("ToBeHidden"))
				{
					break;
				}
				else
				{
					var menu_entry = get_menu_entry_for_tab(tab_to_check)
						.appendTo(hidden_tabs_menu);
				}
			}
		}
		else
		{
			for ( var int = num_tabs; int > 0; int--) 
			{
				var tab_to_check = $(tab_container).children(":nth-child("+int+")");
				if(!$(tab_to_check).hasClass("ToBeHidden"))
				{
					break;
				}
				else
				{
					var menu_entry = get_menu_entry_for_tab(tab_to_check)
						.prependTo(hidden_tabs_menu);
				}
			}
		}

		
		if (anchor === "right")
		{
			var position_left_correction = hidden_tabs_menu.css("left").replace("px", "") - hidden_tabs_menu.width();
			$("#SmallTabHiddenTabsMenu").css("left", position_left_correction);
		}
		
		function get_menu_entry_for_tab(tab)
		{
			var entry = $("<div class='SmallTabHiddenTabsMenuEntry'>"+$(tab).find(".SmallTabContent").text()+"</div>")
				.data("RelatesToTab", tab)
				.css("cursor","pointer")
			return entry;
		}
	}
	
	/**
	 * Hides the arrow menu that shows hidden tabs.
	 */
	function hide_hidden_tabs_menu()
	{
		$("#SmallTabHiddenTabsMenu").fadeOut(200, function(){
			$(this).remove();
		});
	}
	
	/**
	 * Selects and centers a tab.
	 * @param tab
	 */
	function focus_tab(tab)
	{		
		var tab_to_focus_min_x = $(tab).position().left;
		var tab_to_focus_max_x = tab_to_focus_min_x + $(tab).width();
		
		var current_camera_min_x = offset;
		var current_camera_max_x = offset + max_total_tab_width;
				
		if(tab_to_focus_min_x >= current_camera_max_x || tab_to_focus_max_x >= current_camera_max_x)
		{ //tab is on the right side and not visible 	 //tab is on the right side and half visible
			offset += tab_to_focus_max_x - current_camera_max_x;
		}
		else if(tab_to_focus_max_x <= current_camera_min_x || tab_to_focus_min_x <= current_camera_min_x)
		{ //tab is on the left side and not visible			//tab is on the left side and half visible
			offset -= current_camera_min_x - tab_to_focus_min_x;
		}
		
		scroll_camera_to_offset(true);
	}
	
	/**
	 * Scrolls the camera to the current offset position.
	 * @param animate boolean that indicates whether to use an animation as transition.
	 */
	function scroll_camera_to_offset(animate)
	{
		if(animate)
		{
			$(".SmallTabList").animate({
				"margin-left": -offset
				
			}, 100, function(){
				
				$(".ToBeHidden")
				.css("opacity",1)
				.removeClass("ToBeHidden");
				
				hide_invisible_tabs_and_align_left();
			});
		}
		else
		{
			$(".SmallTabList").css("margin-left", -offset);
			hide_invisible_tabs_and_align_left();
		}
	}

	/**
	 * Hides the invisible tabs and aligns the tabs on the left side.
	 */
	function hide_invisible_tabs_and_align_left()
	{
		$(".ToBeHidden")
			.css("opacity",1)
			.removeClass("ToBeHidden");
		
		var current_camera_min_x = offset;
		var current_camera_max_x = current_camera_min_x + max_total_tab_width;	
		var current_tab_left_position = 0;
		
		console.log(current_camera_min_x+"#"+current_camera_max_x);
		
		for (var int = 1; int <= num_tabs; int++) 
		{
			var tab_to_check = $(tab_container).children(":nth-child("+int+")");
			// var tab_to_check_min_x = current_tab_left_position;
			var tab_to_check_min_x = $(tab_to_check).position().left;
			var tab_to_check_max_x = tab_to_check_min_x + $(tab_to_check).width();
			
			console.log(tab_to_check_min_x+"#"+tab_to_check_max_x);
			
			if(tab_to_check_max_x > current_camera_max_x)
			{
				$(tab_to_check).addClass("ToBeHidden")
			}
			else if(tab_to_check_min_x < current_camera_min_x)
			{
				$(tab_to_check).addClass("ToBeHidden");
			}
			
			current_tab_left_position = tab_to_check_max_x;
		}
		
		$(".ToBeHidden").css("opacity",0);
		
		var visible_tabs_width = 0;
		
		var margin_on_left = false;
		
		$(tab_container).children().each(function(){
			if(!$(this).hasClass("ToBeHidden"))
			{	
				if(visible_tabs_width === 0)
				{
					var margin_left = ($(this).position().left - offset);
					if(margin_left > 0)
					{
						margin_on_left = true;
					}
				}
				
				visible_tabs_width += $(this).width();
			}
		});
		
		var margin = Math.floor(max_total_tab_width - visible_tabs_width);
		
		if(margin_on_left)
		{
			$(".SmallTabList").css("margin-left", -offset - margin);
		}
		
		disable_arrows_if_needed();
	}
}

/*
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
	
	
	 * Initialise.
	
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
			$(arrow_left).children("img").attr("src","images/tabs/arrow_left_inactive.png");
		}
		else
		{
			$(arrow_left).children("img").attr("src","images/tabs/arrow_left_active.png");
		}
		
		if($(arrow_right).hasClass("buttonInactive"))
		{
			$(arrow_right).children("img").attr("src","images/tabs/arrow_right_inactive.png");
		}
		else
		{
			$(arrow_right).children("img").attr("src","images/tabs/arrow_right_active.png");
		}
	}
	
	
	 * Opens the dropdown menu that lists the hidden tabs.
	 * @param side the side on which to append the menu, either left or right
	
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
				.click(function(evt)
				{
					evt.preventDefault();
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
	
	
	 * Selects a tab and loads its content.
	 * @param capture the capture of the tab to select
	 * @param slide indicates whether to animate the tab bar or not
	
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
*/