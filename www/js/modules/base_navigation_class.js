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

function base_navigation()
{
	
	var arrow_margin_top = parseInt($(".NavigationButtonDown").children().css("margin-top").replace("px",""));
	var animate_downwards_pixels = 5;
	
	init();
	
	function init()
	{
		$("#NavigationMenu").find(".NavigationButtonDown").each(function(){
			var button_down = this;
			
			//this should actually fix IE 7 + 8 but somehow it does not?!
			$(button_down)
				.css("background-position","0px 0px")
				.next().css("background-position","0px 0px");
//			alert($(this).css("background-position")); //always returns undefined
			
			button_handler(button_down);
		});
		
		base_scrollable_navigation_tabs();
	}
	
	function button_handler(button_down)
	{
		var tab = $(button_down).parent().parent();

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
						return true;
					}
				}
				
				//open new tab
				animate_right_tab_side_down(tab);
				open_menu(tab);
			});
	}
	
	function open_menu(tab)
	{
		var height = $(tab).find(".NavigationButtonLeft").height();
		var top = $(tab).offset().top + height + 1;
		var left = $(tab).offset().left;
		var color = $(tab).find(".NavigationButtonContent").css("text-shadow");		
		color = color.split(")")[0] + ")";
		
		var menu_html = get_html(tab);
		
		var menu = $("<div class='NavigationButtonMenu'></div>")
			.css({
				"position": "absolute",
				"top": top,
				"left": left,
				"background-color": color,
				"border": "solid 1px "+color,
				"border-top": 0,
			    "-moz-border-bottom-right-radius": 10,
		    	"-webkit-border-bottom-right-radius": 10,
		    	"-khtml-border-bottom-right-radius": 10,
		    	"border-bottom-right-radius": 10,
		    	"-moz-border-bottom-left-radius": 10,
		    	"-webkit-border-bottom-left-radius": 10,
		    	"-khtml-border-bottom-left-radius": 10,
		    	"border-bottom-left-radius": 10,
		    	"padding": "5px 10px 10px 10px"
			})
			.html(menu_html)
			.data("refersToTab", tab)
			.hide()
			.appendTo("body")
			.fadeIn(200);
		
		//mark tab as active
		var tab_class = $(tab).attr("class");
		$(tab)
			.addClass("Active"+tab_class)
			.data("originalClass", tab_class);
		
		//TODO php?
		$("#NavigationBackground").css("border-bottom", "solid 1px "+color);
	}
	
	function close_menu()
	{
		var tab_to_close = $(".NavigationButtonMenu").data("refersToTab");
		
		//fadeout and destroy menu
		$(".NavigationButtonMenu").fadeOut(200, function(){
			$(this).remove();
		});
			
		//mark tab as inactive
		var original_class = $(tab_to_close).data("originalClass");
		$(tab_to_close).attr("class", original_class);
		
		//TODO php?
		$("#NavigationBackground").css("border-bottom", "solid 1px #336699");
		
		return tab_to_close;
	}
	
	function animate_right_tab_side_down(tab)
	{
		var right_tab_side_arrow = $(tab).find(".NavigationButtonDown");
		var right_tab_side_corner = $(tab).find(".NavigationButtonRight");
		
		//animate arrow part
		$(right_tab_side_arrow)
			.stop()
			.animate({backgroundPosition: "0 "+animate_downwards_pixels+"px"}, 200);
	
		//animate arrow
		$(right_tab_side_arrow)
			.children()
			.stop()
			.animate({"margin-top": arrow_margin_top + animate_downwards_pixels}, 200)
			.rotate({animateTo:-180, duration:400});
		
		//animate right part
		$(right_tab_side_corner).stop().animate({backgroundPosition: "0 "+animate_downwards_pixels+"px"}, 200);
	}
	
	function animate_right_tab_side_up(tab)
	{
		var right_tab_side_arrow = $(tab).find(".NavigationButtonDown");
		var right_tab_side_corner = $(tab).find(".NavigationButtonRight");
		
		//animate arrow part
		$(right_tab_side_arrow)
			.stop()
			.animate({backgroundPosition: "0 0"}, 200);
		
		//animate arrow
		$(right_tab_side_arrow)
			.children()
			.stop()
			.animate({"margin-top": arrow_margin_top}, 200)
			.rotate({animateTo:0, duration:400});
		
		//animate right part
		$(right_tab_side_corner).stop().animate({backgroundPosition: "0 0"}, 200);
	}
	
	function get_html(tab)
	{
		var html = $("<ul><li>entry</li><li>long entry</li><li>longer entry</li><li>even longer entry</li><li>very very long entry</li></ul>")
			.css({
				"font-family": "arial",
				"padding": 0,
				"margin": 0,
				"margin-left": 10
			});
		return html;
	}

}

function base_scrollable_navigation_tabs()
{
	var tab_container = $("#NavigationMenu");
	var max_total_tab_width = $(tab_container).width() - 50;
	var num_tabs = $(tab_container).children().length;
	var offset = 0;
	var rest_margin = 0;

	init();
	
	function init()
	{
		var total_tab_width = 0;
		$(tab_container).children().each(function(){
			total_tab_width += $(this).width();
		});
		
		$(tab_container)
			.css({
				"margin-left": 0, //reset margin, was 25px
				"width": total_tab_width //set width to max to prevent line break -> position() not correct
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
			
			//(forward)
	//		var last_visible_tab_num = 0;
	//		var new_total_tab_width = 0;
	//		$(tab_container).children().each(function(i){
	//			new_total_tab_width += $(this).width();
	//			if(new_total_tab_width > max_total_tab_width)
	//			{
	//				last_visible_tab_num = i; //-1
	//			}
	//		});
			
			
			
			hide_invisible_tabs_and_center();
			
			
			//needed? hide_invisible_tabs_and_center()
			
//			var last_visible_tab_num = num_tabs ;
//			var new_total_tab_width = total_tab_width;
//			while(true)
//			{
//				var last_tab_width = $(tab_container).children(":nth-child("+last_visible_tab_num+")").width();
//				new_total_tab_width -= last_tab_width;		
//				if(new_total_tab_width > max_total_tab_width)
//				{
//					last_visible_tab_num--;
//				}
//				else
//				{
//					break;
//				}
//			}
//			
//			hide_tabs_after(last_visible_tab_num);
		}
	}
	
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
				if($(this).data("hiddenTabsActive") !== true)
				{
					$(this).rotate({animateTo:-90,duration:300});
					show_hidden_tabs_menu(left_arrow_x, left_arrow_y + 20, true);
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
				if($(this).data("hiddenTabsActive") !== true)
				{
					$(this).rotate({animateTo:90,duration:300})
					show_hidden_tabs_menu(right_arrow_x, right_arrow_y + 20, false);
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
	
	function show_hidden_tabs_menu(x, y, left)
	{
		var hidden_tabs_menu = $("<div id='NavigationMenuHiddenTabsMenu'></div>")
			.css(
			{
				"position":"absolute",
				"width":100,
				"background-color":"white",
				"border":"solid black 1px",
				"padding":"2px",
				"font-family":"arial",
				"font-size":"12px",
				"z-index":"200",
				"top": y,
				"left": x
			})
			.appendTo("#NavigationMenuCamera")
			.fadeIn(200);
		
		if(left)
		{
			for ( var int = 1; int <= num_tabs; int++) 
			{
				var tab_to_check = $(tab_container).children(":nth-child("+int+")");
				if($(tab_to_check).is(":visible"))
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
				if($(tab_to_check).is(":visible"))
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
			var entry = $("<div>"+$(tab).find(".NavigationButtonContent").text()+"</div>")
				.data("RelatesToTab", tab)
				.css("cursor","pointer")
				.click(function(){
					focus_tab($(this).data("RelatesToTab"));
					if(left)
					{
						$("#NavigationMenuArrowLeft").trigger("click");
					}
					else
					{
						$("#NavigationMenuArrowRight").trigger("click");
					}
				});
			return entry;
		}
	}
	
	function hide_hidden_tabs_menu()
	{
		$("#NavigationMenuHiddenTabsMenu").fadeOut(200, function(){
			$(this).remove();
		});
	}
	
	function hide_tabs_before(tab_num) //needed?
	{
		for (var int = tab_num; int > 0; int--) 
		{
			$(tab_container).children(":nth-child("+int+")").hide();
		}
	}
	
	function hide_tabs_after(tab_num)
	{
		for (var int = tab_num; int <= num_tabs; int++) 
		{
			$(tab_container).children(":nth-child("+int+")").hide();
		}
	}
	
	function focus_tab(tab)
	{
		$(tab_container).children().show();
		
		var tab_to_focus_min_x = $(tab).position().left;
		var tab_to_focus_max_x = $(tab).position().left + $(tab).width();
		
		var current_camera_min_x = offset;
		var current_camera_max_x = offset + max_total_tab_width;
				
		if(tab_to_focus_min_x >= current_camera_max_x || tab_to_focus_max_x >= current_camera_max_x)
		{ //tab is on the right side and not visible 	 //tab is on the right side and half visible
			offset += - (tab_to_focus_max_x - current_camera_max_x);
//			console.log("tab: "+tab_to_focus_min_x+" - "+tab_to_focus_max_x);
//			console.log("cam: "+current_camera_min_x+" - "+current_camera_max_x);
		}
		else if(tab_to_focus_max_x <= current_camera_min_x || tab_to_focus_min_x <= current_camera_min_x)
		{ //tab is on the left side and not visible			//tab is on the left side and half visible
			offset += current_camera_min_x - tab_to_focus_min_x;
		}
		else
		{//strange
			console.log("strange");
//			console.log("tab: "+tab_to_focus_min_x+" - "+tab_to_focus_max_x);
//			console.log("cam: "+current_camera_min_x+" - "+current_camera_max_x);
		}
		
		scroll_camera_to_offset(true);
//		hide_invisible_tabs_and_center();
	}
	
	function scroll_camera_to_offset(animate)
	{
		if(animate)
		{
			$("#NavigationMenuCamera").animate({
				"margin-left": (offset + 25)
			}, 100, function(){});
		}
		else
		{
			$("#NavigationMenuCamera").css("margin-left", (offset + 25));
		}
	}

	function hide_invisible_tabs_and_center()
	{
		var current_camera_min_x = offset;
		var current_camera_max_x = current_camera_min_x + max_total_tab_width;	
		
		
		for (var int = 1; int <= num_tabs; int++) 
		{
			var tab_to_check = $(tab_container).children(":nth-child("+int+")");
			var tab_to_check_min_x = $(tab_to_check).position().left;
			var tab_to_check_max_x = tab_to_check_min_x + $(tab_to_check).width();
			
			if(tab_to_check_max_x > current_camera_max_x)
			{
				console.log($(tab_to_check).find(".NavigationButtonContent").text()+" is on the right and will be hidden");
				$(tab_to_check).addClass("ToBeHidden")
				
//				console.log("tab: "+tab_to_check_min_x+" - "+tab_to_check_max_x);
//				console.log("cam: "+current_camera_min_x+" - "+current_camera_max_x))
			}
			else if(tab_to_check_min_x < current_camera_min_x)
			{
				console.log($(tab_to_check).find(".NavigationButtonContent").text()+" is on the left and will be hidden");
				$(tab_to_check).addClass("ToBeHidden");
			}
		}
		
		$(".ToBeHidden")
			.hide()
			.removeClass("ToBeHidden");
		
		var visible_tabs_width = 0;
		$(tab_container).children(":visible").each(function(){
			visible_tabs_width += $(this).width();
		});
//		rest_margin = max_total_tab_width - visible_tabs_width;
//		offset += offset + (rest_margin / 2);
//		scroll_camera_to_offset(false);
	}
}