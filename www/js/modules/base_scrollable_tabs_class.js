/**
 * version: 0.4.0.0
 * author: Roman Quiring <quiring@open-lims.org>
 * author: Roman Konertz <konertz@open-lims.org>
 * copyright: (c) 2008-2013 by Roman Quiring, Roman Konertz
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

// function scrollable_tabs(tab_list,max_tabs,hide_arrows_if_deactivated,center_tabbar,arrow_left_id,arrow_right_id,camera_id,classname_active)
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
				"width": total_tab_width+40 //set width to max to prevent line break -> position() would not be correct
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
		
		for (var int = 1; int <= num_tabs; int++) 
		{
			var tab_to_check = $(tab_container).children(":nth-child("+int+")");
			var tab_to_check_min_x = $(tab_to_check).position().left;
			var tab_to_check_max_x = tab_to_check_min_x + $(tab_to_check).width();
			
			if ($(tab_to_check).children("a").hasClass("SmallTabActive"))
			{
				focus_tab(tab_to_check, false);
			}
		}
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
		
		var right_arrow_x = camera_position.left + camera_width + 25;
		var right_arrow_y = left_arrow_y;
		
		var left_arrow = $("<div id='SmallTabArrowLeft'><img src='images/tabs/arrow_left_active.png' />")
			.click(function()
			{
				if($(this).hasClass("Disabled"))
				{
					return false;
				}
				
				if($(this).data("hiddenTabsActive") !== true)
				{
					$(this).children("img").rotate({animateTo:-90,duration:300});
					show_hidden_tabs_menu(left_arrow_x, left_arrow_y + 25, left_arrow, "left");
					$(this).data("hiddenTabsActive", true);
				}
				else
				{
					$(this).children("img").rotate({animateTo:0,duration:300})
					hide_hidden_tabs_menu();
					$(this).data("hiddenTabsActive", false);
				}
			})
			.insertBefore("#SmallTabCamera")
			.css({"left":left_arrow_x,"top":left_arrow_y});
		
		var right_arrow = $("<div id='SmallTabArrowRight'><img src='images/tabs/arrow_right_active.png' />")
			.click(function()
			{
				if($(this).hasClass("Disabled"))
				{
					return false;
				}
				
				if($(this).data("hiddenTabsActive") !== true)
				{
					$(this).children("img").rotate({animateTo:90,duration:300})
					show_hidden_tabs_menu(right_arrow_x, right_arrow_y + 25, right_arrow, "right");
					$(this).data("hiddenTabsActive", true);
				}
				else
				{
					$(this).children("img").rotate({animateTo:0,duration:300})
					hide_hidden_tabs_menu();
					$(this).data("hiddenTabsActive", false);
				}
			})
			.insertAfter("#SmallTabCamera")
			.css({"left":right_arrow_x,"top":right_arrow_y});
	}
	
	/**
	 * Disables the arrows if there is enough space.
	 */
	function disable_arrows_if_needed()
	{		
		if(!$(".SmallTabList").children(":first").hasClass("ToBeHidden"))
		{
			$("#SmallTabArrowLeft")
				.children("img")
				.attr("src","images/tabs/arrow_left_inactive.png");
			
			$("#SmallTabArrowLeft")
				.addClass("Disabled");
		}
		else
		{
			$("#SmallTabArrowLeft")
				.children("img")
				.attr("src","images/tabs/arrow_left_active.png");
				
			$("#SmallTabArrowLeft")
				.removeClass("Disabled");
		}
		
		if(!$(".SmallTabList").children(":last").hasClass("ToBeHidden"))
		{
			$("#SmallTabArrowRight")
				.children("img")
				.attr("src","images/tabs/arrow_right_inactive.png");
			
			$("#SmallTabArrowRight")
				.addClass("Disabled");
		}
		else
		{
			$("#SmallTabArrowRight")
				.children("img")
				.attr("src","images/tabs/arrow_right_active.png");
				
			$("#SmallTabArrowRight")
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
					focus_tab(tab_to_be_focused, true);
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
	function focus_tab(tab, click)
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
		
		for (var int = 1; int <= num_tabs; int++) 
		{
			var tab_to_check = $(tab_container).children(":nth-child("+int+")");
			$(tab_to_check).children("a").removeClass("SmallTabActive")
		}
		
		$(tab).children("a").addClass("SmallTabActive")
		
		scroll_camera_to_offset(true);
		
		if (click === true)
		{
			var url = $(tab).children("a").attr("href");
			$(location).attr('href',url);
		}
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
				
			}, 200, function(){
				
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

		for (var int = 1; int <= num_tabs; int++) 
		{
			var tab_to_check = $(tab_container).children(":nth-child("+int+")");
			var tab_to_check_min_x = $(tab_to_check).position().left;
			var tab_to_check_max_x = tab_to_check_min_x + $(tab_to_check).width();

			if(tab_to_check_max_x > current_camera_max_x)
			{
				$(tab_to_check).addClass("ToBeHidden")
			}
			else if(tab_to_check_min_x < current_camera_min_x)
			{
				$(tab_to_check).addClass("ToBeHidden");
			}
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