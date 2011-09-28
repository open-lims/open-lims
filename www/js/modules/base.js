/*
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

function tooltip(element_id, message)
{
	var offsetX = 20;
	var offsetY = 10;
	
	$("#"+element_id).hover(function(e){
		$("<div id='tooltip'>"+message+"</div>")
			.css("position","absolute")
			.css("background-color","white")
			.css("border","solid black 1px")
			.css("padding","2px 4px 2px 4px")
			.css({"font-family":"arial","font-size":"12px"})
			.css("top", e.pageY + offsetY)
			.css("left", e.pageX + offsetX)
			.hide()
			.appendTo('body')
			.fadeIn(300);
	},function(){
		$('#tooltip').remove();
	});
	
	$("#"+element_id).mousemove(function(e) {
		$("#tooltip").css("top", e.pageY + offsetY).css("left", e.pageX + offsetX);
	});
}

var upload_error_array = [];
upload_error_array[0] = "";
upload_error_array[1] = "";
upload_error_array[2] = "A non-specific error occurs during upload!";
upload_error_array[3] = "A non-specific error occurs during upload!";
upload_error_array[4] = "This file is too large!";
upload_error_array[5] = "This file equals previous version or already exists!";
upload_error_array[6] = "You have exceeded your quota!";
upload_error_array[7] = "This file-type is forbidden!";
upload_error_array[8] = "permission denied!";
