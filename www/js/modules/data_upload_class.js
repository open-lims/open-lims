/**
 * version: 0.4.0.0
 * author: Roman Quiring <quiring@open-lims.org>
 * copyright: (c) 2008-2013 by Roman Quiring
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


function base_upload(unique_id, session_id) 
{
	var unique_id_local = unique_id;
	var session_id_local = session_id;
	var num_files_to_upload;
	var num_uploaded_files = 0;
	var track_uploads = [];	
	var errors_occurred = 0;
	var php_upload_size_script = true;
	var started = 0;
	var scroll_api = undefined;
	var scroll_height = 100;
	var finished = false;
	
    if (typeof(base_upload_prototype_called) == "undefined")
    {
    	base_upload.prototype.init = init;
    	base_upload.prototype.start_upload = start_upload;
    	base_upload.prototype.is_finished = is_finished;
    	init();
    }
	
    /**
     * Initialise.
     */
	function init()
	{
		$("#uploader_add_fields").click(function(evt)
		{
			evt.preventDefault();
			var num_fields_to_add = $("#uploader_number_of_fields").attr("value");
			for (var int = 1; int <= num_fields_to_add; int++) 
			{
				var current_fields = $(".FileInput").length;
				var field_num = current_fields +1;
				var new_element = $("<tr id='lastFileInput'><td><input type='file' name='file-"+field_num+"' id='file-"+field_num+"' value='' class='FileInput'/></td></tr>")
				$("#lastFileInput").attr("id","").after(new_element);
			}
		});
		
		$("#uploader_upload").click(function(evt)
		{
			evt.preventDefault();
			start_upload();
		});
	}
	
	/**
	 * Initialise the upload. Submits form data and enters upload loop.
	 * @returns {Boolean} false if no files were selected.
	 */
	function start_upload()
	{
		var files = new Array();
		$(".FileInput").each(function()
		{
			var file = $(this).attr("value");
			if(file != "")
			{
				var filename = file.split("\\");
				if(filename.length > 0)
				{
					file = filename[filename.length-1];
				}
				files.push(file);
			}
		});
		if(files.length == 0) 
		{
			$("#ErrorMessage").html(upload_status_array[0]);
			return false;
		}
		else
		{
			$("#ErrorMessage").html("");
		}
		
		num_files_to_upload = files.length;
		$("#uploader_file_amount").attr("value",num_files_to_upload);
		for (var int = 0; int < num_files_to_upload; int++) 
		{
			track_uploads[int] = -1;
		}
		
		var block_ui_content = $("<div id='UploadUI'>Upload</div>");
		var global_upload_progressbar = $("<div id='GlobalProgressBar' class='UploadProgressbar'></div>");
		$(global_upload_progressbar).progressbar();
		
		var file_list_container = $("<div id='FileListContainer'></div>");
		var file_list = $("<ul id='FileList'></ul>").appendTo(file_list_container);
		for (var int = 0; int < num_files_to_upload; int++)
		{
			$("<li id='FileListItem"+parseInt(int+1)+"'>"+parseInt(int+1)+". "+files[int]+"<span class='FileListImageContainer'></span><span class='FileListProgressInfo'></span></li>").appendTo(file_list);
		}
		
		var upload_info = $("<div id='UploadInfo'></div>");
		var ok_button = $("<button id='FinishUpload'>OK</button>")
			.attr("disabled","true")
			.click(function()
			{
				var retrace = get_retrace();
				if(retrace == undefined)
				{
					$.unblockUI();
				}
				else
				{
					$(location).attr("href",retrace);
				}
			});
		
		$(block_ui_content)
			.append(file_list_container)
			.append(global_upload_progressbar)
			.append(upload_info)
			.append(ok_button);
		$.blockUI({ message: block_ui_content , css: {"width":"550px"}});
		
		init_scrollbar();
		$("#UploadForm").submit();
		started = new Date().getTime();
		upload_loop();
	}
	
	/**
	 * Initialise the scrollbar.
	 * @returns {Boolean} false if scrollbar is not necessary.
	 */
	function init_scrollbar()
	{
		var list_item_height = parseInt($("#FileList > li").css("height").replace("px",""));
		var displayable_items = scroll_height / list_item_height;	
		if(num_files_to_upload <= displayable_items)
		{
			return false;
		}
		$("#FileListContainer").jScrollPane();
		scroll_api = $("#FileList").parent().parent().parent().data("jsp");
		$(".jspContainer").css(
		{
			"height":scroll_height,
			"border":"dotted #D0D0D0 1px"
		});
		scroll_api.reinitialise();
	}

	/**
	 * Main upload loop. Exits if all files are uploaded.
	 * @returns {Boolean} true if all uploads are complete.
	 */
	function upload_loop()
	{
		var done = false;
		var current_item = $("#FileListItem"+parseInt(num_uploaded_files + 1)).children(".FileListImageContainer");
		if($(current_item).html() == "")
		{
			image = $("<img class='FileListImage' src='images/animations/loading_circle_small.gif'/>");
			$(current_item).append(image);
		}
		
		if(php_upload_size_script)
		{
			if(check_upload_progress()) // all complete
			{
				$("#UploadInfo").html(upload_status_array[1]);
				php_upload_size_script = false;
				if(check_upload_state())
				{
					done = true;
				}
			}
		}
		else
		{
			if(check_upload_state())
			{
				done = true;
			}
		}
		if(done)
		{
			finished = true;
			update_scrollbar();
			if(errors_occurred > 0)
			{
				if(errors_occurred == 1)
				{
					$("#UploadInfo").html(upload_status_array[2]);
				}
				else
				{
					$("#UploadInfo").html(upload_status_array[3].replace("VAR",errors_occurred));
				}
			}
			else
			{
				if(num_files_to_upload == 1)
				{
					$("#UploadInfo").html(upload_status_array[4]);
				}
				else
				{
					$("#UploadInfo").html(upload_status_array[5].replace("VAR",num_files_to_upload));
				}
			}
			$("#FinishUpload").removeAttr("disabled");
			return false;
		}
		setTimeout(upload_loop,250);
	}
	
	/**
	 * Checks for the state of the uploads the old-fashioned way. 
	 * Marks finished uploads as complete and updates progressbar.
	 * @returns {Boolean} true if all uploads are complete, otherwise false.
	 */
	function check_upload_state()
	{
		var done = false;
		var json_state = "";
		$.ajax(
		{
			async: false,
	    	type : "GET",
	    	url : "core/modules/data/file_check.upload.php?session_id="+session_id_local+"&unique_id="+unique_id_local,
	    	data : null,
	    	success : function(data) 
	    	{
	    		json_state = data;
	    	}
		});
		
		if(json_state != "No Array")
		{
			if(json_state.substr(0,6) == "ALL_OK")
			{
				json_state = json_state.replace("ALL_OK","");
				done = true;
			}
			var state = jQuery.parseJSON(json_state);
			
			for (var int = 1; int <= num_files_to_upload; int++) 
			{
				var code = state[int];
				if(code != 0)
				{
					if(code == 1)
					{ //finished
						set_upload_complete(int, true)
					}
					else
					{ //error
						set_upload_error(int, code);
					}
				}
			}
		}
		if(done)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * Checks for the progress of the uploads via PHP uploadprogress extension. 
	 * Marks finished uploads as complete and updates progress bar (accurately) as well as time left.
	 * @returns {Boolean} false if either PHP extension is not installed or upload in progress. True if all complete.
	 */
	function check_upload_progress()
	{
		var done = false;
		$.ajax(
		{
			async: false,
	    	type : "GET",
	    	url : "core/modules/data/file_check.upload.progress.php?unique_id="+unique_id_local,
	    	data : null,
	    	success : function(data) 
	    	{
	    		if(data == "NOT_INSTALLED")
	    		{
	    			php_upload_size_script = false;
	    			return false;
	    		}
	    		if(data != -1)
	    		{
		    		var params = data.split(" ");
		    		var percent = params[0];
		    		var time_left = params[1];
		    		var speed = params[2];
		    		var total_complete = params[3];
		    		
		    		$("#GlobalProgressBar").progressbar('option', 'value', parseInt(percent));
		    		$("#FileListItem"+parseInt(num_uploaded_files + 1)).children(".FileListProgressInfo").html(speed+" kb/s");
		    		
		    		if(time_left != 1)
		    		{
			    		$("#UploadInfo").html(upload_status_array[6].replace("VAR",time_left));
		    		}
		    		else
		    		{
			    		$("#UploadInfo").html(upload_status_array[7]);
		    		}
		    		
		    		if(total_complete > num_uploaded_files) //new upload complete
		    		{
		    			for (var int = num_uploaded_files + 1; int <= total_complete; int++) 
		    			{
			    			set_upload_complete(int, false);
						}
		    			if(total_complete == num_files_to_upload)
		    			{
		    				done = true;
		    			}
		    		}
	    		}
	    		else
	    		{
	    			var finish_now = false;
	    			var value = $("#GlobalProgressBar").progressbar("option","value");
	    			if(value != 0) //all finished
	    			{
	    				finish_now = true;
	    			}
	    			else
	    			{
		    			var current_time = new Date().getTime()
		    			if(current_time - started > 5000) // timeout before marking all uploads as finished 
		    			{	    						  // (small files in low quantity might not be registered as finished)
		    				finish_now = true;
		    			}
	    			}
	    			if(finish_now)
	    			{
	    				for (var int = num_uploaded_files + 1; int <= num_files_to_upload; int++) 
	    				{
		    				set_upload_complete(int, false);
						}
	    				done = true;
	    			}
	    		}
	    	}
		});
		if(done)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * Marks an upload as complete.
	 * @param num the number of the file that is uploaded.
	 * @param definitive whether the upload is definitely complete or if it needs an error check.
	 */
	function set_upload_complete(num, definitive)
	{
		if(track_uploads[num-1] != 1)
		{
			num_uploaded_files++;
			track_uploads[num-1] = 1;
		}
		
		var image;
		if($("#FileListItem"+num).children(".FileListImageContainer").html() == "")
		{
			image = $("<img class='FileListImage'/>");
			$("#FileListItem"+num).children(".FileListImageContainer").append(image);
		}
		else
		{
			image = $("#FileListItem"+num).children(".FileListImageContainer").children(".FileListImage");
		}
		
		if(definitive)
		{
			$(image).attr("src","images/icons/permission_ok_active.png");
		}
		else
		{
			$(image).attr("src","images/icons/permission_ok_active_na.png");	
		}
		
		update_progressbar();
		update_scrollbar();
		
		if(php_upload_size_script)
		{
			$("#FileListItem"+num).children(".FileListProgressInfo").html("");
		}	
	}
	
	/**
	 * Marks an upload as failed due to an error. 
	 * @param num the number of the file that was not successfully uploaded.
	 * @param code The error code.
	 */
	function set_upload_error(num, code)
	{
		if(track_uploads[num-1] != 1)
		{
			num_uploaded_files++;
		}
		if(track_uploads[num-1] != code)
		{
			errors_occurred++;
		}
		
		var image;
		if($("#FileListItem"+num).children(".FileListImageContainer").html() == "")
		{
			image = $("<img class='FileListImage'/>");
			$("#FileListItem"+num).children(".FileListImageContainer").append(image);
		}
		else
		{
			image = $("#FileListItem"+num).children(".FileListImageContainer").children(".FileListImage");
		}
		$(image).attr("src","images/icons/error.png");	

		$("#FileListItem"+num).children(".FileListProgressInfo").html(upload_error_array[code]); 
		track_uploads[num-1] = code;
		update_progressbar();
		update_scrollbar();
	}
	
	/**
	 * Updates the progressbar.
	 */
	function update_progressbar()
	{
		var global_percentage = 100 / (num_files_to_upload / num_uploaded_files);
		$('#GlobalProgressBar').progressbar('option', 'value', global_percentage);	
	}
	
	/**
	 * Updates the scrollbar.
	 * @returns {Boolean} false if there is no need for a scroll bar.
	 */
	function update_scrollbar()
	{
		if(scroll_api == undefined)
		{
			return false;
		}
		var list_element_height = $("#FileList").children("li").outerHeight();
		var current_position = num_uploaded_files * list_element_height;
		if(current_position > scroll_height / 2)
		{
			var position_to_scroll = current_position - ((scroll_height / 2) - (list_element_height / 2));
			scroll_api.scrollTo(0,position_to_scroll,true);
		}
	}
	
	/**
	 * Gets the retrace url to jump to when the upload is finished.
	 * @returns string the url.
	 */
	function get_retrace() 
	{
		var current_url = $(location).attr("href");
		var url_retrace = current_url.split("&retrace=");
		if(url_retrace.length == 2)
		{
			var retrace = current_url.split("&nav=")[0];
			var decoded = base64_decode(url_retrace[1]);
			var unserialized = unserialize(decoded);
			for (var key in unserialized)
			{
				var value = unserialized[key];
				retrace += "&"+key+"="+value;
			}
			return retrace;
		}
		
		var url_split = current_url.split("&action=file_add");
		if(url_split.length == 2)
		{
			return url_split[0];
		}	
	}

	/**
	 * Returns whether all uploads are finished. Called from data_browser to determine when to reload the list after an upload.
	 * @returns {Boolean} true if the uploads are done.
	 */
	function is_finished()
	{
		return finished;
	}
	
}