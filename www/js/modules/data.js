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


function is_image(filename) 
{
	if(typeof(allowed_image_types) === "undefined")
	{
		$.ajax({
			async: false,
			type : "POST",
			url : "ajax.php?session_id="+get_array['session_id']+"&nav=data&run=get_allowed_image_types",
			data : "",
			success : function(data) 
			{
				allowed_image_types = data;
			}
		});
	}

	var split = filename.split(".");
	if(split.length > 1) 
	{
		var filetype = split[split.length-1];
		if(allowed_image_types.indexOf(filetype) !== -1)
		{
			return true;
		}
	}
	return false;
}

function image_browser()
{
	init();
	
	function init()
	{
		var browser_container = $("<div></div>")
			.dialog({
				"title" : "Image Browser" ,
				"height" : 600,
				"width" : 400
			});
		
		var large_image = $("<div id='ImageBrowserFullImage'>large image</div>")
			.css({
				"outline": "solid green 1px",
				"width": "100%",
				"height": "40%"
			})
			.appendTo(browser_container);
		
		for (var int = 1; int < 10; int++) 
		{
			var small_image = $("<div class='ImageBrowserThumbnail'>"+int+"</div>")
				.css({
					"outline": "solid red 1px",
					"width": "30%",
					"height": "15%",
					"float": "left",
					"margin-top": "5%"
				})
				.click(function(){
					$("#ImageBrowserFullImage").html($(this).html());
				})
				.appendTo(browser_container);
			
			if(int % 3 !== 0)
			{
				$(small_image).css("margin-right", "5%");
			}
		}
	}
}
	
ValueHandler = function(field_class)
{
	$("."+field_class).each(function()
	{	
		if ($(this).hasClass("DataValueFieldRequiredImportant"))
		{
			if ($(this).val() != "")
			{
				$(this).removeClass("DataValueFieldRequiredImportant");
			}
			
			$(this).focus(function()
			{
				 $(this).removeClass("DataValueFieldRequiredImportant");
			});
			
			$(this).blur(function()
			{
				if ($(this).val() == "")
				{
					 $(this).addClass("DataValueFieldRequiredImportant");
				}
			});
		}
	});
	
	get_json = function()
	{
		var error = false;
		var json = '{';
		
		$("."+field_class+":radio:checked").each(function()
		{
			var name = $(this).attr("name");
			var value = $(this).val();
			json += '\"'+name+'\":\"'+value+'\",';
		});
		
		$("."+field_class+":checkbox").each(function()
		{
			if ($(this).is(":checkbox:checked"))
			{
				var name = $(this).attr("name");
				var value =  $(this).val();
				json += '\"'+name+'\":\"'+value+'\",';
			}
			else
			{
				var name = $(this).attr("name");
				var value = 0;
				json += '\"'+name+'\":\"'+value+'\",';
			}
		});
		
		$("."+field_class+"").each(function()
		{	
			if ($(this).hasClass("DataValueFieldError"))
			{
				$(this).removeClass("DataValueFieldError");
			}
			
			$(this).parent().children(".FormError").remove();
			
			var classes = undefined;
			
			if ($(this).hasClass("DataValueFieldMinValue"))
			{
				if (( $(this).val() == parseInt($(this).val()) ) && ( $(this).val() !== "" ) )
				{
					var current_element = $(this);
					classes = $(this).attr('class').split(' ');
					
					$(classes).each(function()
					{
						if (this.indexOf("DataValueFieldMinValue-",0) === 0)
						{
							var min_value = this.replace("DataValueFieldMinValue-", "");
							if (parseInt($(current_element).val()) < parseInt(min_value))
							{
								error = true;
								$(current_element).after("<span class='FormError'><br />Please enter a value >= "+min_value+"</span>");
								$(current_element).addClass("DataValueFieldError");
								return;
							}
						}
					});
				}
			}
			
			if ($(this).hasClass("DataValueFieldMaxValue"))
			{
				if (( $(this).val() == parseInt($(this).val()) ) && ( $(this).val() !== "" ) )
				{
					var current_element = $(this);
					if (classes === undefined)
					{
						classes = $(this).attr('class').split(' ');
					}
					
					$(classes).each(function()
					{
						if (this.indexOf("DataValueFieldMaxValue-",0) === 0)
						{
							var max_value = this.replace("DataValueFieldMaxValue-", "");
							if (parseInt($(current_element).val()) > parseInt(max_value))
							{
								error = true;
								$(current_element).after("<span class='FormError'><br />Please enter a value <= "+max_value+"</span>");
								$(current_element).addClass("DataValueFieldError");
								return;
							}
						}
					});
				}
			}
			
			if ($(this).hasClass("DataValueFieldRequired"))
			{
				if ($(this).val() === "")
				{
					error = true;
					$(this).after("<span class='FormError'><br />Please enter a value</span>");
					$(this).addClass("DataValueFieldError");
					return;
				}
			}
			
			if ($(this).hasClass("DataValueFieldTypeInteger"))
			{
				if (( $(this).val() != parseInt($(this).val()) ) && ( $(this).val() !== "" ) )
				{
					error = true;
					$(this).after("<span class='FormError'><br />Please enter a valid number without decimal</span>");
					$(this).addClass("DataValueFieldError");
				}
			}
			else
			{
				if ($(this).hasClass("DataValueFieldTypeFloat"))
				{
					$(this).val($(this).val().replace(",","."));
					
					if (($(this).val() != parseFloat($(this).val())) && ( $(this).val() !== "" ) )
					{
						error = true;
						$(this).after("<span class='FormError'><br />Please enter a valid number with or without decimal</span>");
						$(this).addClass("DataValueFieldError");
					}
				}
			}
		
			if (($(this).is(":input") == true) && ($(this).is(":radio") == false) && ($(this).is(":checkbox") == false))
			{
				var name = $(this).attr("name");
				var value = $(this).val();
				json += '\"'+name+'\":\"'+value+'\",';
			}
		});
		
		json = json.substr(0,json.length-1);
		json += '}';
		
		if (error === true)
		{
			return false;
		}
		else
		{
			return json;
		}
	}
	
	this.get_json = get_json;
}