/**
 * version: 0.4.0.0
 * author: Roman Konertz <konertz@open-lims.org>
 * copyright: (c) 2008-2013 by Roman Konertz
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
	var open_select_option_list_button = null;
	var open_select_option_list_global_close_handler = null;
	
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
		// Invisible check durch Klassen-Check ersetzen
		// Invisible ohne Klasse ignorieren
		// Bei rebuild: Liste und aktuellen Eintrag akualisieren
		
		var selected_element_exists = false;
		var selected_element;
		var focused_element;
		var list_entry_array = new Object();
		
		if ($(this).parent().hasClass("FormSelectContainer"))
		{
			var container = $(this).parent();
		}
		else
		{
			var container = new $("<div class='FormSelectContainer'></div>");
			if ($(this).attr("class") !== undefined)
			{
				var select_classes = $(this).attr("class");
				select_classes = select_classes.replace("FormSelect","");
				$(container).attr("class", $(container).attr("class")+" "+select_classes);
			}
			
			if ($(this).css("display") === "block")
			{
				$(container).css("display", "block");	
			}
			else
			{
				$(container).css("display", "inline-block");
			}
						
			$(this).wrap(container);
		}
		
		if ($(this).parent().next().hasClass("FormSelectList"))
		{
			var option_list = $(this).parent().next();
		}
		else
		{
			var option_list = $("<div class='FormSelectList'><ul></ul></div>").css({"display":"block","min-width":$(this).parent().width()+8});
			// $(option_list).jScrollPane({autoReinitialise: true}).hide();
			$(option_list).hide();
		}
		
		if ($(this).next().next().hasClass("FormSelectEntry"))
		{
			var entry = $(this).next().next();
		}
		else
		{
			var entry = $("<div class='FormSelectEntry'></div>");
		}
		
		if ($(this).next().next().next().hasClass("FormSelectButton"))
		{
			var button = $(this).next().next().next();
		}
		else
		{
			var button = $("<div class='FormSelectButton'><img src='images/down.png' alt=''></div>").bind("click", function(event)
			{				
				event.preventDefault();
				event.stopPropagation();
				
				var data = event.data;
				
				// if ($(option_list).css("display") === "none")
				if ($(option_list).is(":hidden"))
				{
					if (open_select_option_list_button !== null)
					{
						open_select_option_list_button.trigger("click");
					}

					open_select_option_list_button = $(this);
					
					// Length
					var max_window_height = $(window).height() - $(this).offset().top - 26;
					var max_document_height = $(document).height() - $(this).offset().top - 26;
					
					// $(option_list).css("display", "block");
					$(option_list).show();

					var current_height = $(option_list).height();
					var entry_height = $(option_list).find("ul").children(":first-child").height();
					var max_entry_height = entry_height*10;

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
								$(option_list).height(max_entry_height);
								
								$('html,body').animate({
									scrollTop: $(this).offset().top
								}, 2000);
							}
							else
							{
								$(option_list).height(max_document_height-10);
							}
						}

						$(option_list).jScrollPane();
					}

					// Set selected
					if (selected_element_exists  === true)
					{
						$(selected_element).children("a").focus();
					}
					
					// Key up and down
					$(option_list).bind("keydown", function(event)
					{
						switch(event.keyCode)
						{
							case 40: 
								event.preventDefault();
								if ($(option_list).children("li.selected") !== $(option_list).children("li:last-child"))
								{
									$(focused_element).next().children("a").focus();
								}
							break;
							
							case 38:
								event.preventDefault();
								if ($(option_list).children("li.selected") !== $(option_list).children("li:first-child"))
								{
									$(focused_element).prev().children("a").focus();
								}
							break;
							
							default:
								return;
							break;
						}
					}); 
											
					open_select_option_list_global_close_handler = function(event)
					{
						$(button).trigger("click");
					}
					
					$(document).bind("click", open_select_option_list_global_close_handler);
				}
				else
				{
					$(option_list).hide()
					// $(option_list).css("display", "none");
					$(option_list).unbind("keydown");
					$(document).unbind("click", open_select_option_list_global_close_handler);
					open_select_option_list_button = null;
				}
			});
		}
		
		function build_list(element)
		{
			$(option_list).children("ul").empty();
			list_entry_array = new Object();
			
			$(element).children("option").each(function()
			{								
				var local_option = $(this);
				
				if ($(this).is(":disabled"))
				{
					var list_entry = $("<li><span>"+$(this).html()+"</span></li>");
				}
				else
				{
					var list_entry = $("<li><a href='#'>"+$(this).html()+"</a></li>").bind("click", {option: $(this)}, function(event)
					{
						event.preventDefault();
						event.stopPropagation();
						
						var data = event.data;
						$(data.option).parent().children("option:selected").removeAttr("selected");
						$(data.option).attr("selected", "selected");
						$(entry).html($(data.option).html());
						$(data.option).parent().trigger("onchange");
						
						selected_element = $(this);
						
						$(button).trigger("click");
					});
					
					list_entry.children("a").bind("focus", function(event)
					{
						$(this).parent().parent().children().each(function()
						{
							$(this).removeClass("selected");
						});
						$(this).parent().addClass("selected");
						focused_element = $(this).parent();
					});
					
					if ($(this).attr("selected") === "selected")
					{
						selected_element_exists = true;
						selected_element = list_entry;
					}
					
					var value = $(this).attr("value")
					
					if ((value !== undefined) && (value !== ""))
					{
						list_entry_array[value] = list_entry;
					}
				}
		
				$(option_list).children("ul").append(list_entry);
			});
		}
		
		if ($(this).children("option:selected"))
		{
			$(entry).html($(this).children("option:selected").html());
		}

		if ($(this).hasClass("FormSelect"))
		{
			build_list($(this));
		}
		else
		{
			$(this).addClass("FormSelect");
			// Wenn Klasse da, dann option_list (El. nach parent von this), container (parent von this), entry (2. El. nach this) auslesen , sonst erzeugen
			
			if (($(this).attr("size") === undefined) || ($(this).attr("size") === 1))
			{				
				option_list.bind("click", function(event)
				{
					event.preventDefault();
					event.stopPropagation();
				});

				if (($(entry).width() == 0) && ($(this).width() < 45) && ($(this).css("display") !== "block"))
				{
					$(this).parent().width(45);
					$(option_list).width(53);
				}
				
				build_list($(this));
				
				var focus_element = $("<a href='#'></a>");
				$(focus_element).css({"opacity":"0", "z-index":"-1", "position":"relative", "display":"block"});
				$(focus_element).height($(this).parent().height());
				$(focus_element).width($(this).width());
				
				$(this).parent().append(focus_element);
				$(this).parent().append(entry);
				$(this).parent().append(button);
				$(this).parent().append("<div style='clear: both;'></div>");
				$(this).parent().after(option_list);
				
				
				$(this).css({"display":"none", "position":"relative"});
				
				$(this).parent().children("a").focus(function()
				{
					 $(this).parent().addClass("FormFocused");
				});
				
				$(this).parent().children("a").blur(function()
				{
					 $(this).parent().removeClass("FormFocused");
				});
			}
		}
		
		$(this).parent().unbind("keydown");
		$(this).parent().bind("keydown", {option: $(this)}, function(event)
		{
			var data = event.data;
			
			switch(event.keyCode)
			{
				case 40: 
					// Down
					event.preventDefault();
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
					event.preventDefault();
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
		
	});
}


$(document).ready(function()
{
	base_form_init();
});