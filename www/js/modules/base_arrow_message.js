/**
 * version: 0.4.0.0
 * author: Roman Konertz <konertz@open-lims.org>
 * copyright: (c) 2008-2016 by Roman Konertz
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

ArrowMessage = function()
{
	var arrow_element_array = [];
	
	new_arrow = function(element, message, additional_class) 
	{
		if (jQuery.inArray(element, arrow_element_array) === -1)
		{
			var element_offset = $(element).offset();
			var element_width = $(element).outerWidth();
			var element_height = $(element).outerHeight();
		
			var message_container = $("<div class='BaseArrowMessageContainer' style='display: none;'></div>");
			var message_arrow = $("<div class='BaseArrowMessageArrow'></div>");
			var message_element = $("<div class='BaseArrowMessage'></div>").html(message);
	
			message_container.append(message_arrow).append(message_element);
			
			if ((additional_class !== "") && (additional_class !== undefined))
			{
				message_container.addClass(additional_class);
			}
			
			if (element.next().hasClass("BaseArrowMessageContainer") === true)
			{
				element.next().remove();
			}
	
			element.after(message_container);
			
			var message_left = element_offset.left + element_width + 3;
			var message_top = element_offset.top - Math.round((message_container.outerHeight() - element_height)/2);
			
			message_container.css({'display':'block', 'position':'absolute'});
			message_container.offset({ top: message_top, left: message_left});	
			
			arrow_element_array.push(element);
		}
	}
	
	remove_arrow = function(element) 
	{
		if (jQuery.inArray(element, arrow_element_array) !== -1)
		{
			$(element).next().remove();
			arrow_element_array.splice($.inArray(element, arrow_element_array), 1);
		}
	}
	
	remove_all_arrows = function() 
	{
		for (var i=0; i<arrow_element_array.length; i++)
		{
			$(arrow_element_array[i]).next().remove();
		}
		arrow_element_array = [];
	}
	
	this.new_arrow = new_arrow;
	this.remove_arrow = remove_arrow;
	this.remove_all_arrows = remove_all_arrows;
}
