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

/**
 * Base main tab navigation class.
 */
function base_navigation()
{
	var animate_downwards_pixels = 5;
	
	init();
	
	/**
	 * Initialise.
	 */
	function init()
	{
		$("#NavigationMenu").children("li").each(function(){
			button_handler(this);
		});
		
		base_scrollable_navigation_tabs();
	}
	
	/**
	 * Binds event handlers to a given tab (of whatever kind).
	 * @param tab
	 */
	function button_handler(tab)
	{
		var last_color;
		$(tab)
			.bind("mouseover", function(){
				if(!$(tab).hasClass("GreyedOut"))
				{
					grey_out_active_tab_if_necessary(tab);
				}
				else
				{
					remove_grey_out_active_tab();
				}
				if($("#NavigationButtonMenu").size() !== 0)
				{
					if($(".SubMenuOpened")[0] !== $(tab)[0])
					{
						var open_tab = close_menu();
						animate_right_tab_side_up(open_tab);

						$(open_tab).removeClass("SubMenuOpened");
						
						if($(".GreyedOut").size() > 0)
						{
							$(open_tab)
								.removeClass("ActiveOrange")
								.removeClass("ActiveGreen")
								.removeClass("ActiveBlue")
								.removeClass("ActiveGrey");
						}
						else
						{
							grey_out_active_tab_if_necessary(tab);
						}
					}
				}

				var color = get_tab_color(tab);
				$("#NavigationBackground").css("border-bottom", "solid 1px "+color);
				
			})
			.bind("mouseout", function(){
				if($("#NavigationButtonMenu").size() === 0)
				{
					remove_grey_out_active_tab();
					
					var active_tab = get_active_tab();
					var color = get_tab_color(active_tab);

					$("#NavigationBackground").css("border-bottom", "solid 1px "+color);
				}
			});
		
		var button_down = $(tab).find(".NavigationButtonDown");

		if(button_down.length > 0)
		{
			$(button_down)
				.bind("mouseover", function(){
					$(this).children("img").attr("src","images/down_active.png");
				})
				.bind("mouseout", function(){
					$(this).children("img").attr("src","images/down.png");
				})
				.bind("click", function(evt){
					evt.preventDefault();
					
					//close open tab
					var currently_selected_tab = close_menu();
								
					if(currently_selected_tab !== undefined)
					{
						animate_right_tab_side_up(currently_selected_tab);
						
						//if the open tab was this one, we're done
						if(tab === currently_selected_tab)
						{					
							var color = get_tab_color(tab);
							$("#NavigationBackground").css("border-bottom", "solid 1px "+color);
							return true;
						}
					}

					//reset style of currently active tab
					remove_grey_out_active_tab();

					//change style of currently active tab (if the menu does not come from this tab)
					grey_out_active_tab_if_necessary(tab);
					
					//open new tab
					animate_right_tab_side_down(tab);

					open_menu(tab);
				});
		}
	}
	
	/**
	 * Greys out the active tab if it is not the given tab.
	 * @param menu_tab the tab to be checked against.
	 */
	function grey_out_active_tab_if_necessary(menu_tab)
	{
		var active_tab = get_active_tab();			
		
		if($(menu_tab)[0] !== $(active_tab)[0]) 
		{
			$(active_tab).data("originalClass",$(active_tab).attr("class"));
			$(active_tab).attr("class", "GreyedOut");
		}
	}
	
	/**
	 * Removes the greyed out tab.
	 */
	function remove_grey_out_active_tab() 
	{	
		var original_class = $(".GreyedOut").data("originalClass");
		$(".GreyedOut")
			.removeClass("GreyedOut")
			.attr("class", original_class);
	}	
	
	/**
	 * Returns the currently active tab.
	 */
	function get_active_tab()
	{
		var active_tab;
		$("#NavigationMenu").children().each(function(){
			if($(this).attr("class").indexOf("Active") !== -1)
			{
				active_tab = this;
				return false;
			}
		});
		return active_tab;
	}
	
	
	/**
	 * Returns the color of a given tab as hex value.
	 * @param tab the tab
	 * @returns hex color string
	 */
	function get_tab_color(tab)
	{
		var color;
		if($(tab).hasClass("Blue"))
		{
			color = "#336699";
		}
		else if($(tab).hasClass("Green"))
		{
			color = "#99cd32";
		}
		else if($(tab).hasClass("Orange"))
		{
			color = "#dcaf36";
		}
		else if($(tab).hasClass("Grey"))
		{
			color = "#535353";
		}
		return color;
	}
	
	/**
	 * Opens the menu of a given tab.
	 * @param tab
	 */
	function open_menu(tab)
	{
		var height = $(tab).find(".NavigationButtonLeft").height();
		var top = $(tab).offset().top + height + 1;
		var left = $(tab).offset().left;
		var color = get_tab_color(tab);
		
		var menu_html = get_menu_html(tab);
				
		$(menu_html).find(".NavigationButtonMenuCategory").children().children().children().each(function(){
			$(this).hover(function(){
				$(this).css("background-color",color);
			}, function(){
				$(this).css("background-color","");
			})
			.click(function(event){
				event.stopPropagation();
				
				if($(".GreyedOut").size() > 0)
				{
					var original_class = $(".GreyedOut").data("originalClass");
					$(".GreyedOut")
						.removeClass("GreyedOut")
						.attr("class",original_class)
						.removeClass("ActiveOrange")
						.removeClass("ActiveGreen")
						.removeClass("ActiveBlue")
						.removeClass("ActiveGrey");
				}
				
				var tab = $("#NavigationButtonMenu").data("refersToTab");
				
				var color = get_tab_color(tab);
				$("#NavigationBackground").css("border-bottom", "solid 1px "+color);
				
//				animate_right_tab_side_up(tab);
//				close_menu();
				
			});
		});
		
		var menu = $("<div id='NavigationButtonMenu'></div>")
			.css({
				"top": top,
				"left": left,
				"border-left": "solid 1px "+color,
				"border-right": "solid 1px "+color,
				"border-bottom": "solid 1px "+color
			})
			.html(menu_html)
			.data("refersToTab", tab)
			.hide()
			.appendTo("body")
			.fadeIn(200);
		
		var menu_columns = $(menu_html).find(".NavigationButtonMenuColumn");
		if(menu_columns.length >= 2)
		{
			var menu_height = $("#NavigationButtonMenu").height();
			$(menu_columns).each(function(i){
				if(i < menu_columns.length - 1)
				{
					var separator = $("<div class='NavigationButtonMenuColumnSeparator'></div>");
					$(separator).css({
							"height": menu_height,
							"border-left": "dotted "+color+" 1px"
						});
					
					$(this).after(separator);
				}
			});
		}
		
		//mark tab as active
		var tab_class = $(tab).attr("class");
		$(tab)
			.data("originalClass", tab_class)
			.addClass("SubMenuOpened")
			.addClass("Active"+tab_class);
		
		$("#NavigationBackground").css("border-bottom", "solid 1px "+color);
		
		var bind_body_click_handler = function() 
		{
			$("body")
				.unbind("click", body_click_handler)
				.bind("click", body_click_handler);
		};
		setTimeout(bind_body_click_handler, 100);
	}
	
	var body_click_handler = function(){
		var tab_to_close = close_menu();
		animate_right_tab_side_up(tab_to_close);

		var original_class = $(".GreyedOut").data("originalClass");
		$(".GreyedOut")
			.removeClass("GreyedOut")
			.attr("class",original_class);
		
		var active_tab = get_active_tab();
		
		var tab_color = get_tab_color(active_tab);
		$("#NavigationBackground").css("border-bottom", "solid 1px "+tab_color);
		
		$("body").unbind("click", body_click_handler);
	};
	
	/**
	 * Closes the menu. Returns the tab corresponding to the closed menu.
	 * @returns the tab whose menu was closed.
	 */
	function close_menu()
	{
		var tab_to_close = $("#NavigationButtonMenu").data("refersToTab");
		
		//fadeout and destroy menu
		$("#NavigationButtonMenu").fadeOut(200, function(){
			$(this).remove();
		});
					
		//mark tab as inactive
		var original_class = $(tab_to_close).data("originalClass");
		$(tab_to_close).attr("class", original_class);
		
		$("#NavigationBackground").css("border-bottom", "solid 1px #336699");
				
		$("body").unbind("click", body_click_handler);

		return tab_to_close;
	}
	
	/**
	 * Animates the right arrow side of a tab down.
	 * @param tab
	 */
	function animate_right_tab_side_down(tab)
	{
		var right_tab_side_arrow = $(tab).find(".NavigationButtonDown");

		if($.browser.msie && ($.browser.version == 7.0 || $.browser.version == 8.0))
		{
			$(right_tab_side_arrow)
				.css("background-position-y", 0)
				.animate({"background-position-y": animate_downwards_pixels+"px"}, 200);
			return false;
		}
		
		//animate arrow part
		$(right_tab_side_arrow)
			.stop()
			.animate({backgroundPosition: "0 "+animate_downwards_pixels+"px"}, 200);
		
		//animate arrow
		$(right_tab_side_arrow)
			.children()
			.stop()
			.rotate({animateTo:-180, duration:400});
	}
	
	/**
	 * Animates the right arrow side of a tab up.
	 * @param tab
	 */
	function animate_right_tab_side_up(tab)
	{
		var right_tab_side_arrow = $(tab).find(".NavigationButtonDown");
		
		if($.browser.msie && ($.browser.version == 7.0 || $.browser.version == 8.0))
		{
			$(right_tab_side_arrow).animate({"background-position-y": "0px"}, 200);
			return false;
		}
		
		//animate arrow part
		$(right_tab_side_arrow)
			.stop()
			.animate({backgroundPosition: "0 0"}, 200);
		
		//animate arrow
		$(right_tab_side_arrow)
			.children()
			.stop()
			.rotate({animateTo:0, duration:400});
	}
	
	/**
	 * Returns the html to be placed inside the menu of a given tab.
	 * @param tab
	 * @returns jQuery-Object.
	 */
	function get_menu_html(tab)
	{
		var lists = $(tab).find(".NavigationButtonSubMenu").html();
		
		var html = $("<div></div>");
		var append_to = html;
		$(lists).each(function(){
			if($(this).hasClass("NavigationButtonSubMenucolumnCaption"))
			{
				append_to = $("<div class='NavigationButtonMenuColumn'></div>").appendTo(html);
				$("<div class='NavigationButtonMenuCategoryCaption'></div>")
					.append($(this))
					.appendTo(append_to);
			}
			else
			{
				$("<div class='NavigationButtonMenuCategory'></div>")
					.append($(this))
					.appendTo(append_to);
			}
		});
		
		return html;
	}

}

/**
 * Base scrollable tabs class.
 */
function base_scrollable_navigation_tabs()
{
	var tab_container = $("#NavigationMenu");
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
				"margin-left": 0, //reset margin, was 25px TODO css change
				"width": total_tab_width //set width to max to prevent line break -> position() would not be correct
			});
	
		var camera = $("<div id='NavigationMenuCamera'></div>")
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
	}
	
	/**
	 * Appends the left and right arrow to the menu.
	 */
	function append_arrows()
	{
		var camera_position = $("#NavigationMenuCamera").position();
		var camera_width = $("#NavigationMenuCamera").width();
		
		var left_arrow_x = camera_position.left + 5;
		var left_arrow_y = camera_position.top + 10;
		
		var right_arrow_x = camera_position.left + camera_width + 30;
		var right_arrow_y = left_arrow_y;
		
		var left_arrow = $("<img src='images/1leftarrow.png' id='NavigationMenuArrowLeft'/>")
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
					show_hidden_tabs_menu(left_arrow_x, left_arrow_y + 20, left_arrow);
					$(this).data("hiddenTabsActive", true);
				}
				else
				{
					$(this).rotate({animateTo:0,duration:300})
					hide_hidden_tabs_menu();
					$(this).data("hiddenTabsActive", false);
				}
			})
			.appendTo("#NavigationMenuCamera");
		
		var right_arrow = $("<img src='images/1rightarrow.png' id='NavigationMenuArrowRight'/>")
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
					show_hidden_tabs_menu(right_arrow_x - 95, right_arrow_y + 20, right_arrow);
					$(this).data("hiddenTabsActive", true);
				}
				else
				{
					$(this).rotate({animateTo:0,duration:300})
					hide_hidden_tabs_menu();
					$(this).data("hiddenTabsActive", false);
				}
			})
			.appendTo("#NavigationMenuCamera");
	}
	
	/**
	 * Disables the arrows if there is enough space.
	 */
	function disable_arrows_if_needed()
	{
		if(!$("#NavigationMenu").children(":first").hasClass("ToBeHidden"))
		{
			$("#NavigationMenuArrowLeft")
				.attr("src","images/1leftarrow_inactive.png")
				.addClass("Disabled");
		}
		else
		{
			$("#NavigationMenuArrowLeft")
				.attr("src","images/1leftarrow.png")
				.removeClass("Disabled");
		}
		
		if(!$("#NavigationMenu").children(":last").hasClass("ToBeHidden"))
		{
			$("#NavigationMenuArrowRight")
				.attr("src","images/1rightarrow_inactive.png")
				.addClass("Disabled");
		}
		else
		{
			$("#NavigationMenuArrowRight")
				.attr("src","images/1rightarrow.png")
				.removeClass("Disabled");
		}
	}
	
	/**
	 * Displays the arrow menu that shows hidden tabs.
	 * @param x the x-coordinate.
	 * @param y the y-coordinate.
	 * @param arrow the arrow.
	 */
	function show_hidden_tabs_menu(x, y, arrow)
	{
		var hidden_tabs_menu = $("<div id='NavigationMenuHiddenTabsMenu'></div>")
			.css(
			{
				"top": y,
				"left": x
			})
			.appendTo("#NavigationMenuCamera")
			.fadeIn(200)
			.click(function(evt){
				if($(evt.target).hasClass("NavigationMenuHiddenTabsMenuEntry"))
				{
					var tab_to_be_focused = $(evt.target).data("RelatesToTab");
					focus_tab(tab_to_be_focused);
					$(arrow).trigger("click");
				}
				
			});
		
		if($(arrow).attr("id") === "NavigationMenuArrowLeft")
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

		function get_menu_entry_for_tab(tab)
		{
			var entry = $("<div class='NavigationMenuHiddenTabsMenuEntry'>"+$(tab).find(".NavigationButtonContent").text()+"</div>")
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
		$("#NavigationMenuHiddenTabsMenu").fadeOut(200, function(){
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
			$("#NavigationMenu").animate({
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
			$("#NavigationMenu").css("margin-left", -offset);
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
			$("#NavigationMenu").css("margin-left", -offset - margin);
		}
		
		disable_arrows_if_needed();
	}
}