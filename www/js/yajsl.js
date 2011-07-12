
function insertInArray(current_array, position, element) {
	
	var current_array_length = current_array.length - 1;
	
	if (position <= current_array_length) {
	
		for (var i=current_array_length; i>=position; i--) {
			current_array[i+1] = current_array[i];
		}
		current_array[position] = element;
		
	}else{
		current_array[position] = element;
	}
	
	return current_array;
	
}

function deleteInArray(current_array, position) {
	
	var current_array_length = current_array.length - 1;
	
	if (position <= current_array_length) {
	
		for (var i=position; i<=current_array_length; i++) {
			current_array[i] = current_array[i+1];
		}
		current_array.length = current_array.length - 1;
		
	}else{
		current_array.length = current_array.length - 1;
	}
	
	return current_array;
	
}

function getGetParamArray() {
	
	   var get_params_array = new Array();
	   
	   if(location.search.length > 0) {
		   
	      var get_param_string = location.search.substring(1, location.search.length);
	      var get_params = get_param_string.split("&");
	      
	      for(i=0; i<get_params.length; i++) {
	    	  
	         var key_value = get_params[i].split("=");
	         if(key_value.length == 2) {
	        	 
	            var key = key_value[0];
	            var value = key_value[1];
	            get_params_array[key] = value;
	            
	         }
	         
	      }
	   }
	   
	   return(get_params_array);
	   
	}
	 
function getGetParam(key) {
   var get_params = getGetParamArray();
   if(get_params[key]) {
      return(get_params[key]);
   }else{
      return null;
   }
}    

function MiniCalendar(return_id) {

	this.toggleFrame 	= toggleFrame;
	this.prevMonth 		= prevMonth;
	this.nextMonth		= nextMonth;
	this.setDate		= setDate;

	var today_date = new Date();
	var current_date = new Date();
	
	var event;
	
	// I hate IE :-(
	if (document.captureEvents) {
		document.captureEvents(Event.CLICK | Event.MOUSEDOWN | Event.MOUSEUP);
	}
	document.onclick = eventHandlerClick;
	
	function eventHandlerClick(e) {
		
		// event handler
		if (window.event) {
			var ev = window.event;
				ev.cancelBubble = true;
			var target = ev.srcElement;
		}else{
			var target = e.target;
		}

		switch (target.className) {
		
			case "date":
				setDate(target.id);
			break;
			
			case "next":
				nextMonth();
			break;
				
			case "prev":
				prevMonth();
			break;
			
			case "close":
				closeFrame();
			break;
		
		}
	
		
	}

	
	// Public
	function toggleFrame(event) {

		var body = document.getElementsByTagName("body")[0];

		var position = mousePos(event);
		
		var entry;
		
		if ((entry = document.getElementById("mini_cal")) != null) {

			entry.parentNode.removeChild(entry);

		}else{

			body.appendChild(initFrame());
			
		}
		
	}

	// Public
	function prevMonth() {
		if (current_date.getMonth() >= 1) {
			current_date.setMonth(current_date.getMonth() - 1);
		}else{
			current_date.setFullYear(current_date.getFullYear() - 1);
			current_date.setMonth(11);
		}
		refreshFrame();
	}

	// Public
	function nextMonth() {
		if (current_date.getMonth() < 11) {
			current_date.setMonth(current_date.getMonth() + 1);
		}else{
			current_date.setFullYear(current_date.getFullYear() + 1);
			current_date.setMonth(0);
		}
		refreshFrame();
	}

	function closeFrame() {
		
		if ((entry = document.getElementById("mini_cal")) != null) {

			entry.parentNode.removeChild(entry);

		}
		
	}
	
	// Public
	function setDate(string) {

		if ((entry =  document.getElementById("mini_cal")) != null) {

			entry.parentNode.removeChild(entry);

		}
		
		var input = document.getElementById(return_id);	
			input.value = string;

	}
	
	// Private
	function mousePos(event) {

		position = new Array();
		
		if (event.pageX && event.pageY) {
			position[0] = event.pageX;
			position[1] = event.pageY;
		}else{
			event = window.event;
			position[0] = event.clientX + document.body.scrollLeft + document.documentElement.scrollLeft;
			position[1] = event.clientY + document.body.scrollTop + document.documentElement.scrollTop;
		}

		return position;

	}

	// Private 
	function initFrame() {
		
		var new_frame = document.createElement("div");
			new_frame.style.left = position[0] + "px";
			new_frame.style.visibility = "visible";
			new_frame.style.opacity = "1";
			new_frame.style.position = "absolute";
			new_frame.style.top = position[1] + "px";
			new_frame.style.zIndex = "1000";
			new_frame.style.display = "block";
			new_frame.id = "mini_cal";
			new_frame.className = "mini_cal";
	
			new_frame.appendChild(dateHeader());
			
			new_frame.appendChild(dateFrame());
			
		var div_footer = document.createElement("div");
		
			div_footer.innerHTML = "<div id='mini_cal_close' class='close'>close</div>" +
									"<div class='mini_cal_clear'></div>";

			div_footer.id = "mini_cal_footer";
				
			new_frame.appendChild(div_footer);
				
		return new_frame;
		
	}
	
	// Private 
	function refreshFrame() {
		
		if (((header = document.getElementById("mini_cal_header")) != null) && 
				((table = document.getElementById("mini_cal_table")) != null) &&
				((footer = document.getElementById("mini_cal_footer")) != null)) {

			new_frame = header.parentNode;
			
			header.parentNode.removeChild(header);
			table.parentNode.removeChild(table);
			footer.parentNode.removeChild(footer);
	
			new_frame.appendChild(dateHeader());
			
			new_frame.appendChild(dateFrame());
			
			var div_footer = document.createElement("div");
				
				div_footer.innerHTML = "<div id='mini_cal_close' class='close'>close</div>" +
										"<div class='mini_cal_clear'></div>";
	
				div_footer.id = "mini_cal_footer";
					
				new_frame.appendChild(div_footer);

		}
		
	}
	
	// Private
	function dateHeader() {

		var month_array = new Array("January","February","March","April","May","June","July","August","September","October","November","December");

		var current_month = current_date.getMonth() + 1;
		var current_year = current_date.getFullYear();
		
		var current_month_name = month_array[current_date.getMonth()];
		
		var div_header = document.createElement("div");

		div_header.innerHTML = "<div id='mini_cal_left' class='prev'></div>" +
								"<div id='mini_cal_right' class='next'></div>" +
								"<div id='mini_cal_middle'>" + current_month_name + " " + current_year + "</div>" +
								"" +
								"<div class='mini_cal_clear'></div>";
		
		div_header.id = "mini_cal_header";
		
		return div_header;
		
	}
	
	// Private
	function dateFrame() {

		counter = 1;

		pointer_date = new Date(current_date.getFullYear(), current_date.getMonth(), counter);
		
		var current_month = current_date.getMonth() + 1;
		var current_year = current_date.getFullYear();
		
		var table = document.createElement("table");
			table.className = "mini_cal_table";
			table.id =  "mini_cal_table";
		
		var tr = document.createElement("tr");

		var th = document.createElement("th");
			th.innerHTML = "Sun";
			tr.appendChild(th);	
		
		var th = document.createElement("th");
			th.innerHTML = "Mon";
			tr.appendChild(th);

		var th = document.createElement("th");
			th.innerHTML = "Tue";
			tr.appendChild(th);

		var th = document.createElement("th");
			th.innerHTML = "Wed";
			tr.appendChild(th);

		var th = document.createElement("th");
			th.innerHTML = "Thu";
			tr.appendChild(th);

		var th = document.createElement("th");
			th.innerHTML = "Fri";
			tr.appendChild(th);

		var th = document.createElement("th");
			th.innerHTML = "Sat";
			tr.appendChild(th);

		table.appendChild(tr);	

		
		
		
		for (var i=1;i<=6;i++) {

			var tr = document.createElement("tr");
			
			for (var j=0;j<=6;j++) {

				var td = document.createElement("td");

				if (((pointer_date.getDay() == j) || (counter >= 2)) && (counter != 0)) {

					var month = pointer_date.getMonth()+1;
					
					//td.innerHTML = "<a href='#' onclick='mini_cal.set_date(\""+pointer_date.getFullYear()+"-"+month+"-"+pointer_date.getDate()+"\")'>" + pointer_date.getDate() + "</a>";
					td.innerHTML = pointer_date.getDate();
					td.id = current_year + "-" + current_month + "-" + pointer_date.getDate();
					td.className = "date";
					
					pointer_date.setDate(++counter);

					if ((pointer_date.getDate() == 1) && (counter > 2)) {
						counter = 0;
					}
					
				}

				tr.appendChild(td);
				
			}

			table.appendChild(tr);

		}
		
		return table;

	}
	

}


function Uploader() {
	
	this.setUniqueId = setUniqueId;
	this.setSessionId = setSessionId;
	this.setRetrace = setRetrace;
	this.getUploadReload = getUploadReload;
	this.proceed = proceed;
	this.reload = reload;
	this.stop = stop;
	this.enableField = enableField;
	this.setNumberOfUploads = setNumberOfUploads;
	this.error = error;
	
	var number_of_files = 1;
	
	var unique_id;
	var session_id;
	
	var upload_reload = false;
	var upload_status_array;
	
	if (document.captureEvents) {
		document.captureEvents(Event.CLICK | Event.MOUSEDOWN | Event.MOUSEUP);
	}
	document.onclick = eventHandlerClick;
	
	function eventHandlerClick(e) {
		
		// event handler
		if (window.event) {
			var ev = window.event;
				ev.cancelBubble = true;
			var target = ev.srcElement;
		}else{
			var target = e.target;
		}
		switch (target.id) {
			
			case "uploader_add_fields":
				addFields();
			break;
			
			case "uploader_upload":
				upload();
			break;
	
		}
		
	}
	
	function setUniqueId(id) {
		unique_id = id;
	}
	
	function setSessionId(id) {
		session_id = id;
	}
	
	function setRetrace(id) {
		retrace = id;
	}
	
	function getUploadReload()
	{
		return upload_reload;
	}
	
	function addFields() {
		
		var tbody = document.getElementById("uploader_tbody");
		
		var tr_break = document.getElementById("uploader_break");

		
		var number_of_fields = document.getElementById("uploader_number_of_fields").value;
		
		if (isNaN(number_of_fields)) {
			number_of_fields = 1;
		}else{
			if (number_of_fields < 1) {
				number_of_fields = 1;
			}
		}
		
		for (var i=1; i<=number_of_fields; i++) {
			
			number_of_files = number_of_files + 1;
			
			var file_amount = document.getElementById("uploader_file_amount");
				file_amount.value = number_of_files;
			
				
			var tr = document.createElement("tr");
	
			var td = document.createElement("td");
				td.innerHTML = number_of_files + ". file";
				
				tr.appendChild(td);
							
			tbody.insertBefore(tr, tr_break);	
			
				
			var tr = document.createElement("tr");
				
			var td = document.createElement("td");
				td.innerHTML = "<input type='file' name='file-" + number_of_files + "' id='file-" + number_of_files + "' value='' />";
			
				tr.appendChild(td);
				
			tbody.insertBefore(tr, tr_break);
			
			
			var tr = document.createElement("tr");
			
			var td = document.createElement("td");
				td.innerHTML = "<span class='formError' id='file-" + number_of_files + "-error'></span>";
			
				tr.appendChild(td);
				
			tbody.insertBefore(tr, tr_break);
			
		}
			
	}
	
	function enableField(field_no) {
		if ((field = top.document.getElementById("file-" + field_no)) != null) {
			field.disabled = false;
		}
	}
	
	function upload() {
		
		for (var i=1; i<=number_of_files; i++) {
			if ((span = document.getElementById("file-" + i + "-error")) != null) {
				span.innerHTML = "";
			}
		} 
		
		upload_reload = true;
		window.hidden_upload_checker.location.reload();
					
		// Proceed Bar
		AjaxProceed("Please wait while uploading file(s)",0);
	}
	
	function reload() {
		window.setTimeout('location.reload(true)',500);
	}
	
	function proceed() {

		upload_reload = false;
		
		var target = "";
		
		var get_param_string = location.search.substring(1, location.search.length);
	    var get_params = get_param_string.split("&");
	    
	    if (retrace == "") {
   			for(i=0; i<get_params.length; i++) {
   	    	  
   		       var key_value = get_params[i].split("=");
   		       
   		       if(key_value.length == 2) {
   		        	 
   		          if ((key_value[0] != "action") && (key_value[0] != "file_id")) {
   		        	  if (key_value[0] == "nav") {
   		        		  if (target == "") {
   		  					target = "nav=data";
   		  	        	  }else{
   		  					target = target + "&nav=data";
   		  	        	  }
   		        	  }else{
   		        		  if (target == "") {
   		  					target = key_value[0] + "=" + key_value[1];
   		  	        	  }else{
   		  					target = target + "&" + key_value[0] + "=" + key_value[1];
   		  	        	  }
   		        	  }
   		          }
   	   
   		       }
   			}
	    }
	    else
	    {
	    	target_array = unserialize(retrace);
	    	if (target_array != null)
	    	{
	    		if (target_array.length > 0)
	    		{
	    			for(i=0; i<get_params.length; i++) {  
    	   		       var key_value = get_params[i].split("=");
    	   		       
    	   		       if(key_value.length == 2) {
    	   		        	 
    	   		          if (key_value[0] == "username" || key_value[0] == "session_id") {
	   		        		  if (target == "") {
	   		  					target = key_value[0] + "=" + key_value[1];
	   		  	        	  }else{
	   		  					target = target + "&" + key_value[0] + "=" + key_value[1];
	   		  	        	  }
    	   		          }
    	   	   
    	   		       }
    	   			}
	    			
	    			for (i=0; i<target_array.length; i++)
	    			{
	    				target = target+"&"+target_array[i][0]+"="+target_array[i][1];
	    			}
	    		}
	    		else
	    		{
	    			for(i=0; i<get_params.length; i++) {  
    	   		       var key_value = get_params[i].split("=");
    	   		       
    	   		       if(key_value.length == 2) {
    	   		        	 
    	   		    	   if ((key_value[0] != "action") && (key_value[0] != "file_id")) {
    	   		        	  if (key_value[0] == "nav") {
    	   		        		  if (target == "") {
    	   		  					target = "nav=data";
    	   		  	        	  }else{
    	   		  					target = target + "&nav=data";
    	   		  	        	  }
    	   		        	  }else{
    	   		        		  if (target == "") {
    	   		  					target = key_value[0] + "=" + key_value[1];
    	   		  	        	  }else{
    	   		  					target = target + "&" + key_value[0] + "=" + key_value[1];
    	   		  	        	  }
    	   		        	  }
    	   		          }
    	   	   
    	   		       }
    	   			}
	    		}
	    	}
	    	else
	    	{
	    		for(i=0; i<get_params.length; i++) {  
 	   		       var key_value = get_params[i].split("=");
 	   		       
 	   		       if(key_value.length == 2) {
 	   		        	 
 	   		          if (key_value[0] != "run") {
 	   		        	  if (key_value[0] == "nav") {
 	   		        		  if (target == "") {
 	   		  					target = "nav=data";
 	   		  	        	  }else{
 	   		  					target = target + "&nav=data";
 	   		  	        	  }
 	   		        	  }else{
 	   		        		  if (target == "") {
 	   		  					target = key_value[0] + "=" + key_value[1];
 	   		  	        	  }else{
 	   		  					target = target + "&" + key_value[0] + "=" + key_value[1];
 	   		  	        	  }
 	   		        	  }
 	   		          }
 	   	   
 	   		       }
 	   			}	
	    	}
	    }
		
	    target = "/index.php?" + target;
	    
		location.replace(target); 
		
	}
	
	function setNumberOfUploads(total, complete) {
		subtext = top.document.getElementById("AjaxProceedWindowSubtext");
		subtext.innerHTML = complete + " of " + total + " file(s) uploaded";
	}

	function error(field_no, error_code, upload_type) {
		
		if ((span = top.document.getElementById("file-" + field_no + "-error")) != null) {
					
			switch (error_code) {
			
				case 2:
					span.innerHTML = "A non-specific error occurs during upload!";
				break;
				
				case 3:
					span.innerHTML = "A non-specific error occurs during upload!";
				break;
				
				case 4:
					span.innerHTML = "This file is too large!";
				break;
				
				case 5:
					if (upload_type == "update") {
						span.innerHTML = "This file equals previous version!";
					}else{
						span.innerHTML = "This file already exists! Update it.";
					}
				break;
				
				case 6:
					span.innerHTML = "You have exceeded your quota!";
				break;
				
				case 7:
					span.innerHTML = "This file-type is forbidden!";
				break;
				
				case 8:
					span.innerHTML = "Permission denied!";
				break;		
			
			}
		
		}
		
	}
	
	function stop(number_of_files) {
	
		upload_reload = false;
		
		// Stopps upload in main frame
		if ((div = top.document.getElementById("AjaxProceed")) != null) {
			div.style.display = "none";
			
			if ((div_shade = top.document.getElementById("AjaxProceedShade")) != null) {
				div.removeChild(div_shade);
			}
			
			if ((div_window = top.document.getElementById("AjaxProceedWindow")) != null) {
				div.removeChild(div_window);
			}
			
		}
		
		for (var i=1; i<=number_of_files; i++) {
			if ((field = top.document.getElementById("file-" + i)) != null) {
				field.disabled = true;
			}
		} 
		
	}
	
}


function AutoField(field_array_string) {

	this.setFieldArray = setFieldArray;
	this.initialize = initialize;
	
	if (field_array_string) {
		var field_array = unserialize(field_array_string);
	}else{
		var field_array = new Array();
	}

	// I hate IE :-(
	if (document.captureEvents) {
		document.captureEvents(Event.CLICK | Event.MOUSEDOWN | Event.MOUSEUP);
	}
	document.onclick = eventHandlerClick;
	
	function eventHandlerClick(e) {
		
		// event handler
		if (window.event) {
			var ev = window.event;
				ev.cancelBubble = true;
			var target = ev.srcElement;
		}else{
			var target = e.target;
		}

		switch (target.id) {
		
			case "autofield_edit":
				toogleEdit();
			break;
			
			case "autofield_add_field":
				addField();
			break;
	
		}

		if (target.id.indexOf("autofield_remove_") != -1) {
			var fieldId = target.id.replace("autofield_remove_","");
			removeField(fieldId);
		}
		
	}


	function setFieldArray(field_array_string) {
		// imports an external array
		field_array = unserialize(field_array_string);
	}
	
	function initialize() {
		
		// initializer
		var autofield_area = document.getElementById("autofield_area");
		autofield_area.appendChild(build());
		
	}
	
	function toogleEdit() {
	
		// toogle function of the edit/autofield-view
		
		var footer = document.getElementById("autofield_footer");
		
		var autofield_footer_edit;
		
		if (autofield_footer_edit = document.getElementById("autofield_footer_edit")) {
			
			// Array Saving
			
			if (field_array  && (typeof(field_array == "object"))) {
				
				var field_array_length = field_array.length - 1;
			
				for (var i=0;i<=field_array_length;i++) {
					
					var name_field = document.getElementById("af-" + i);
					if (name_field.value) {
						field_array[i][0] = name_field.value;
					}
					
					var vartype_field = document.getElementById("af-" + i + "-vartype");
					if (vartype_field.value) {
						field_array[i][1] = vartype_field.value;
					}
					
				}
				
			}
			
			
			// Illustration
			
			autofield_footer_edit.parentNode.removeChild(autofield_footer_edit);
			
			var autofield_area = document.getElementById("autofield_area");
				autofield_area.appendChild(build());
			
			var edit_button = document.createElement("button");
				edit_button.innerHTML = "edit";
				edit_button.setAttribute('type', 'button');
				edit_button.className = "autofield_button";
				edit_button.id = "autofield_edit";
			
			footer.appendChild(edit_button);

		}else{
			
			// Array Saving
			
			if (field_array  && (typeof(field_array == "object"))) {
				
				field_array_length = field_array.length - 1;
			
				for (var i=0;i<=field_array_length;i++) {
					
					var content_field = document.getElementById("af-" + i + "-content");
					if (content_field.value) {
						field_array[i][2] = content_field.value;
					}
					
				}
				
			}
			
			// Illustration
			
			var autofield_table;
			
			if ((autofield_table = document.getElementById("autofield_table")) != null) {
				
				autofield_table.parentNode.removeChild(autofield_table);
				
			}
			
			var autofield_edit;
			
			if ((autofield_edit = document.getElementById("autofield_edit")) != null) {

				autofield_edit.parentNode.removeChild(autofield_edit);
				
			}
			
			footer.appendChild(edit());
			
		}
		
			
			
	}
	
	function addField() {
		
		// adds an element
		
		var table = document.getElementById("autofield_footer_edit_table");
		
		if (field_array  && (typeof(field_array == "object"))) {
			var new_id = field_array.length;
		}else{
			var new_id = 0;
		}
		
		var entry_array = new Array();
			entry_array[0] = null;	// Name
			entry_array[1] = null;	// Type
			entry_array[2] = null;	// Content
		
		field_array.push(entry_array);
		
		var tr = document.createElement("tr");
			tr.id = "af-" + new_id + "-tr"; 
		
		
		// TD1
		var td = document.createElement("td");
		
		var field = document.createElement("input");
			field.setAttribute('type', 'textfield');
			field.name = "af-" + new_id;
			field.id = "af-" + new_id;
		
		td.appendChild(field);
		
		tr.appendChild(td);
		
		
		// TD2
		td = document.createElement("td");
		td.innerHTML = "<select name='af-" + new_id + "-vartype' id='af-" + new_id + "-vartype'><option value='int'>Integer</option><option value='float'>Real Number</option><option value='string'>String</option></select>";
		
		tr.appendChild(td);
		
		
		// TD3
		td = document.createElement("td");
		
		var remove_button = document.createElement("button");
			remove_button.innerHTML = "remove";
			remove_button.setAttribute('type', 'button');
			remove_button.id = "autofield_remove_" + new_id;
			remove_button.className = "autofield_button";
		
		td.appendChild(remove_button);
		
		tr.appendChild(td);
		
		table.appendChild(tr);
		
	}
	
	function removeField(fieldId) {
		
		// removes an element
		
		var tr = document.getElementById("af-" + fieldId + "-tr");
		tr.parentNode.removeChild(tr);
		
		if (field_array  && (typeof(field_array == "object"))) {
			
			var field_array_length = field_array.length - 2;
		
			if (fieldId < (field_array.length-1)) {
			
				for (var i=fieldId;i<=field_array_length;i++) {
					
					var new_id = parseInt(i) + parseInt(1);
					
					field_array[i] = field_array[new_id];
					
					var tr = document.getElementById("af-" + new_id + "-tr");
					tr.id = "af-" + i + "-tr";
					
					var field = document.getElementById("af-" + new_id);
						field.id = "af-" + i;
					
					var vartype_field = document.getElementById("af-" + new_id + "-vartype");
						vartype_field.id = "af-" + i + "-vartype";
					
					var remove_button = document.getElementById("autofield_remove_" + new_id);
						remove_button.id = "autofield_remove_" + i;
					
				}
			
				field_array.length = field_array.length - 1; 
				
			}else{
				if (field_array.length > 1) {
					field_array.length = field_array.length - 1; 
				}
			}
			
		}
		
	}
	
	function edit() {
		
		// build the edit-view
		
		var edit_div = document.createElement("div");
			edit_div.id = "autofield_footer_edit";
		
		var table = document.createElement("table");
			table.id = "autofield_footer_edit_table";
		
		if (field_array  && (typeof(field_array == "object"))) {
		
			var field_array_length = field_array.length - 1;
		
			for (var i=0;i<=field_array_length;i++) {
				
				var tr = document.createElement("tr");
					tr.id = "af-" + i + "-tr";
				
				// TD1
				var td = document.createElement("td");
				
				var field = document.createElement("input");
					field.setAttribute('type', 'textfield');
					field.name = "af-" + i;
					field.id = "af-" + i;
				
				if (field_array[i][0]) {
					field.value = field_array[i][0];
				}
				
				td.appendChild(field);
				
				tr.appendChild(td);
				
				
				// TD2
				var td = document.createElement("td");
				
				var select = document.createElement("select");
					select.name = "af-" + i + "-vartype";
					select.id =  "af-" + i + "-vartype";
				
				if (field_array[i][1] == "float") {
					
					var option = document.createElement("option");
						option.value = "float";
						option.innerHTML = "Real Number";
					
					select.appendChild(option);
					
					var option = document.createElement("option");
						option.value = "int";
						option.innerHTML = "Integer";
					
					select.appendChild(option);

					var option = document.createElement("option");
						option.value = "string";
						option.innerHTML = "String";
					
					select.appendChild(option);
				
				}else{
				
					if (field_array[i][1] == "string") {
						
						var option = document.createElement("option");
							option.value = "string";
							option.innerHTML = "String";
						
						select.appendChild(option);
						
						var option = document.createElement("option");
							option.value = "int";
							option.innerHTML = "Integer";
						
						select.appendChild(option);
						
						var option = document.createElement("option");
							option.value = "float";
							option.innerHTML = "Real Number";
						
						select.appendChild(option);
						
					}else{
						
						var option = document.createElement("option");
							option.value = "int";
							option.innerHTML = "Integer";
						
						select.appendChild(option);
						
						var option = document.createElement("option");
							option.value = "float";
							option.innerHTML = "Real Number";
						
						select.appendChild(option);
						
						var option = document.createElement("option");
							option.value = "string";
							option.innerHTML = "String";
						
						select.appendChild(option);
						
					}
					
				}
				
				td.appendChild(select);
				
				tr.appendChild(td);
				
				
				// TD3
				var td = document.createElement("td");
				
				var remove_button = document.createElement("button");
					remove_button.innerHTML = "remove";
					remove_button.setAttribute('type', 'button');
					remove_button.id = "autofield_remove_" + i;
					remove_button.className = "autofield_button";
				
				td.appendChild(remove_button);
				
				tr.appendChild(td);
				
				table.appendChild(tr);
				
			}
		
		}else{
			
			var tr = document.createElement("tr");
			
			var td = document.createElement("td");
				td.innerHTML = "empty";
			
			tr.appendChild(td);
			
			table.appendChild(tr);
			
		}
		
		edit_div.appendChild(table);
		
		var add_button = document.createElement("button");
			add_button.innerHTML = "add field";
			add_button.setAttribute('type', 'button');
			add_button.id = "autofield_add_field";
			add_button.className = "autofield_button";
		
		edit_div.appendChild(add_button);
		
		var save_button = document.createElement("button");
			save_button.innerHTML = "save/close";
			save_button.setAttribute('type', 'button');
			save_button.id = "autofield_edit";
			save_button.className = "autofield_button";
		
		edit_div.appendChild(save_button);

		return edit_div;

	}
	
	function build() {
		
		// Build the autofield-view
		
		var table = document.createElement("table");
			table.id = "autofield_table";
		
		if (field_array  && (typeof(field_array == "object"))) {
			
			var field_array_length = field_array.length - 1;
		
			for (var i=0;i<=field_array_length;i++) {
				
				var tr = document.createElement("tr");
				
				var td = document.createElement("td");
					td.innerHTML = field_array[i][0];

				tr.appendChild(td);
				
				var	td = document.createElement("td");
				
				var field = document.createElement("input");
					field.setAttribute('type', 'textfield');
					field.name = "af-" + i;
					field.id = "af-" + i + "-content";
				
				if (field_array[i][2]) {
					field.value = field_array[i][2];
				}
				
				td.appendChild(field);
				
				var vartype_field = document.createElement("input");
					vartype_field.setAttribute('type', 'hidden');
					vartype_field.name = "af-" + i + "-vartype";
					vartype_field.value = field_array[i][1];
				
				td.appendChild(vartype_field);
				
				var name_field = document.createElement("input");
					name_field.setAttribute('type', 'hidden');
					name_field.name = "af-" + i + "-name";
					name_field.value = field_array[i][0];
				
				td.appendChild(name_field);
				
				tr.appendChild(td);
				
				table.appendChild(tr);
				
			}
		
		}
		
		return table;
		
	}
	
	
}


function AjaxProceed(text) {
	
	if ((div = document.getElementById("AjaxProceed")) != null) {
		
		div.style.left = "0px";
		div.style.top = "0px";
		div.style.width = "100%";
		div.style.height = "100%";
		div.style.position = "absolute";
		div.style.zIndex = "1000";
		div.style.display = "block";
		
		shade = document.createElement("div");
		
		shade.id = "AjaxProceedShade";
		shade.style.left = "0px";
		shade.style.top = "0px";
		shade.style.width = "100%";
		shade.style.height = "100%";
		shade.style.position = "absolute";
		shade.style.zIndex = "1000";
		shade.style.display = "block";
		
		div.appendChild(shade);
		
		box = document.createElement("div");
		
		box.id = "AjaxProceedWindow";
		box.style.width = "400px";
		box.style.height = "250px";
		box.style.position = "relative";
		box.style.zIndex = "1000";
		box.style.display = "block";
		box.style.border = "1px solid #D0D0D0";
		box.style.marginTop = "25%";
		box.style.marginLeft = "auto";
		box.style.marginRight = "auto";
		box.style.backgroundColor = "white";
		
		box.innerHTML = "<div id='AjaxProceedWindowImage'><img src='images/animations/loading_upload.gif' alt='' /></div>" +
						"<div id='AjaxProceedWindowText'>" + text + "</div>" +
						"<div id='AjaxProceedWindowSubtext'></div>";
		
		div.appendChild(box);
		
	}
	
}

