/*
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

error_dialog_counter = 1;

ErrorDialog = function(title, message)
{	
	var dialog_error_id = "BaseErrorDialog"+error_dialog_counter;
	
	error_dialog_counter++;
	
	$("#BaseErrorDialogs").append("<div id='"+dialog_error_id+"' title='"+title+"' style='display: none;'></div>");
	
	$("#"+dialog_error_id).dialog({
		autoOpen: false,
		width: 400,
		height: 200,
		close : function()
		{
			$(this).remove();
		},
		buttons: 
		{
			"OK": function()
			{
                $(this).dialog("close");
			}
		}
	});
	
	$("#"+dialog_error_id).html(message);
	$("#"+dialog_error_id).dialog("open");
}