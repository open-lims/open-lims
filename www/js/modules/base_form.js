/**
 * version: 0.4.0.0
 * author: Roman Konertz <konertz@open-lims.org>
 * copyright: (c) 2008-2011 by Roman Konertz
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

base_form_init = function()
{	
	
	$(".Form input").each(function()
	{
		$(this).focus(function()
		{
			 $(this).addClass("FormFocused");
		});
		
		$(this).blur(function()
		{
			 $(this).removeClass("FormFocused");
		});
	});	
	
	$(".Form textarea").each(function()
	{
		$(this).focus(function()
		{
			 $(this).addClass("FormFocused");
		});
		
		$(this).blur(function()
		{
			 $(this).removeClass("FormFocused");
		});
	});	
	
	$(".Form select").each(function()
	{
		if (($(this).attr("size") === undefined) || ($(this).attr("size") === 1))
		{
			$(this).wrap("<div class='FormSelectContainer'></div>").width($(this).width());
			
			var option_list = $("<div class='FormSelectList'><ul></ul></div>").css({"display":"none","min-width":$(this).width()});
			var entry = $("<div class='FormSelectEntry'></div>"); //.width(($(this).width()-35));
			
			var button = $("<div class='FormSelectButton'><img src='images/down.png' alt=''></div>").bind("click", function(event)
			{
				event.preventDefault();
				
				var data = event.data;
				
				if ($(option_list).css("display") === "none")
				{
					var max_window_height = $(window).height() - $(this).offset().top - 26;
					var max_document_height = $(document).height() - $(this).offset().top - 26;
					
					$(option_list).css("display", "block");
					
					var current_height = $(option_list).height();
					var max_entry_height = $(option_list).find("ul").children(":first-child").height()*10+60;

					if ((current_height < max_window_height) && (current_height < max_entry_height))
					{
						// List is short than 10 entries and fits => Do nothing
					}
					else if ((current_height > max_entry_height) && (max_entry_height < max_window_height))
					{
						// List is longer than 10 entries and fits
						$(option_list).height(max_entry_height);
						$(option_list).jScrollPane();
					}
					else
					{
						// List does not fit
						if ((current_height < max_entry_height))
						{
							// List is shorter than 10 entries
							if ((current_height < max_document_height))
							{
								// Scroll-Down
								$('html,body').animate({
									scrollTop: $(this).offset().top
								}, 2000);
								
								$(option_list).height(current_height);
							}
							else
							{
								$(option_list).height(max_document_height-10);
							}
						}
						else
						{
							// List is longer than 10 entries
							if ((max_entry_height < max_document_height))
							{
								// Scroll-Down
								$('html,body').animate({
									scrollTop: $(this).offset().top
								}, 2000);
								
								$(option_list).height(max_entry_height);
							}
							else
							{
								$(option_list).height(max_document_height-10);
							}
						}

						$(option_list).jScrollPane();
					}

					/*
					$(document).bind("click", {option: $(option_list)}, function(event)
					{
						var data = event.data;
						$(data.option).css("display", "none");
					}); */
				}
				else
				{
					$(option_list).css("display", "none");
				}
			});
							
			if ($(this).children("option:selected"))
			{
				$(entry).html($(this).children("option:selected").html());
			}
			
			if (($(entry).width() == 0) && ($(this).width() < 45))
			{
				$(this).parent().width(45);
			}
			else
			{
				$(this).parent().width($(this).width());
			}
			
			
			$(this).parent().bind("keydown", {option: $(this)}, function(event)
			{
				var data = event.data;
				
				switch(event.keyCode)
				{
					case 40: 
						// Down
						if ($(data.option).children("option:selected") !== $(data.option).children("option:last-child"))
						{
							var next_element = $(data.option).children("option:selected").next();
							$(data.option).children("option:selected").attr("selected", false);
							$(next_element).attr("selected", true);
							$(entry).html($(data.option).children("option:selected").html());
						}
					break;
					
					case 38:
						// Up
						if ($(data.option).children("option:selected") !== $(data.option).children("option:first-child"))
						{
							var previous_element = $(data.option).children("option:selected").prev();
							$(data.option).children("option:selected").attr("selected", false);
							$(previous_element).attr("selected", true);
							$(entry).html($(data.option).children("option:selected").html());
						}
					break;
					
					default:
						return;
					break;
				}
			}); 
			
			$(this).children("option").each(function()
			{
				var local_option = $(this);
				var list_entry = $("<li>"+$(this).html()+"</li>").bind("click", {option: $(this)}, function(event)
				{
					var data = event.data;
					$(option_list).css("display", "none");
					$(data.option).parent().children("option:selected").removeAttr("selected");
					$(data.option).attr("selected", "selected");
					$(entry).html($(data.option).html());
					$(data.option).parent().trigger("onchange");
					
					$(this).parent().children().each(function()
					{
						$(this).removeClass("selected");
					})
					$(this).addClass("selected");
				});
				
				if ($(this).attr("selected") === "selected")
				{
					$(list_entry).addClass("selected");
				}
								
				$(option_list).children("ul").append(list_entry);
			});
			
			$(this).parent().append(entry);
			$(this).parent().append(button);
			$(this).parent().append("<div style='clear: both;'></div>");
			$(this).parent().after(option_list);
			
			$(this).css({"opacity":"0", "z-index":"-1", "position":"relative"});
			
			$(this).focus(function()
			{
				 $(this).parent().addClass("FormFocused");
			});
			
			$(this).blur(function()
			{
				 $(this).parent().removeClass("FormFocused");
			});
		}
	});
}


$(document).ready(function()
{
	base_form_init();
});