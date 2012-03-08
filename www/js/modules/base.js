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
 * Appends a tooltip with a given message to the cursor if a given element is hovered.
 * @param element_id the id of the element to tooltip
 * @param message the tooltip message
 */
function tooltip(element_id, message)
{
	$('#tooltip').remove();
	
	var offsetX = 15;
	var offsetY = 10;
	
	$("#"+element_id)
		.unbind("mouseover mouseout mousemove")
		.mouseover(function(e)
		{
			$("<div id='tooltip'>"+message+"</div>")
				.css(
				{
					"top": e.pageY + offsetY,
					"left": e.pageX + offsetX
				})
				.hide()
				.appendTo('body')
				.fadeIn(300);
		})
		.mouseout(function(e)
		{
			$('#tooltip').remove();
		})	
		.mousemove(function(e) 
		{
			$("#tooltip").css(
			{
				"top": e.pageY + offsetY,
				"left": e.pageX + offsetX
			});
		}); 
}

function base_dialog_reuqest(type, url, data_params)
{
	$.ajax(
	{
		type : type,
		url : url,
		data : data_params,
		async: false,
		success : function(data) 
		{
			if (data)
			{
				var json = $.parseJSON(data);
				
				var continue_button_click_handler 	= json["continue_handler"];
				var cancel_button_click_handler 	= json["cancel_handler"];			
				var additional_script 				= json["additional_script"];
				
				var continue_button_caption = json["continue_caption"];
				var cancel_button_caption 	= json["cancel_caption"];
				
				var html_content 			= json["content"];
				var html_content_caption 	= json["content_caption"];
				var container 				= json["container"];
				
				var dialog_width 			= json["width"];
				var dialog_height 			= json["height"];
					
				var dialog_min_width 		= json["min_width"];
				var dialog_min_height 		= json["min_height"];
				
				var dialog_vposition 		= json["vposition"];
				var dialog_hposition 		= json["hposition"];
				var position 				= [dialog_hposition,dialog_vposition];
				
				$(container).dialog(
				{
					"title" : html_content_caption ,  
					"minHeight" : dialog_min_width , 
					"maxHeight" : dialog_min_height , 
					"position" : position,
					"height" : dialog_height,
					"width" : dialog_width, 
					"buttons" : [
					{
						text : continue_button_caption , 
						click : function()
						{
							if (continue_button_click_handler)
							{
								eval(continue_button_click_handler);
							}
							else
							{
								$(container).dialog("close");
							}
					    }
					} , 
			        {
				    	text : cancel_button_caption, 
				    	click : function()
						{
							if (cancel_button_click_handler)
							{
								eval(cancel_button_click_handler);
							}
							else
							{
								$(container).dialog("close");
							}
					    }
					}]
				});
				
				if(additional_script != undefined)
				{
					eval(additional_script);
				}
				
				$(container).html(html_content);
				$(container).dialog("open");
			}
		}
	});	
}

function base_dialog(type, url, data_params, open_id)
{
	if (open_id == null)
	{
		base_dialog_reuqest(type, url, data_params);
	}
	else
	{
		$("#"+open_id).click("click",function()
		{
			base_dialog_reuqest(type, url, data_params);		
		});
		return false;
	}
}
