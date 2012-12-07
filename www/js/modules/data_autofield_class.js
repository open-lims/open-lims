/**
 * version: 0.4.0.0
 * author: Roman Quiring <quiring@open-lims.org>
 * author: Roman Konertz <konertz@open-lims.org>
 * copyright: (c) 2008-2011 by Roman Quiring, Roman Konertz
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


function autofield(field_array_string, field_css_class)
{
	var field_array;
	
    if (typeof(autofield_prototype_called) == "undefined")
    {
    	field_array = new Array();
    	if(field_array_string != undefined && field_array_string != "[AUTOFIELD_STRING]")
    	{
    		var temp_array = unserialize(field_array_string);
        	for (key in temp_array) 
        	{
				var title = temp_array[key][0];
				var type = temp_array[key][1];
				var value = temp_array[key][2];
				var name = temp_array[key][3];
				var field = new Array(3);
				field[0] = title;
				field[1] = type;
				field[2] = value;
				field[3] = name;
				field_array.push(field);
			}
    	}
    	init();
    	$("#autofield_edit").click(function(evt)
    	{
    		evt.preventDefault();
    		open_edit_view();
    	});
    }
    
    /**
     * Initialise.
     */
    function init()
    {
    	var table = $("<table id='DataAutofieldTable'></table>")	
    	if(field_array.length > 0)
    	{
        	for (var int = 0; int < field_array.length; int++) 
        	{
    			var tr = $("<tr id='af-name-"+int+"'></tr>");
    			var td0 = $("<td>"+field_array[int][0]+"</td>");
    			var td1 = $("<td></td>");
    			var td2 = $("<td></td>");
    			var td3 = $("<td></td>");
    			var td4 = $("<td></td>");
    			
    			var title_input = $("<input type='hidden' name='af-"+field_array[int][3]+"-title'/>");
    			$(title_input).attr("value",field_array[int][0]);
    			$(title_input).addClass(field_css_class);
    			$(td1).append(title_input);
    			
    			if (field_array[int][1] == "int")
    			{
    				var vartype_class = "DataValueFieldTypeInteger";
    			}
    			else
				{
    				if (field_array[int][1] == "float")
        			{
    					var vartype_class = "DataValueFieldTypeFloat";
        			}
        			else
    				{
        				var vartype_class = "DataValueFieldTypeString";
    				}
				}
    			
    			var field_input = $("<input type='textfield' name='af-"+field_array[int][3]+"'/>");
    			$(field_input).attr("value",field_array[int][2]);
    			$(field_input).addClass(vartype_class);
    			$(field_input).addClass(field_css_class);
    			$(td2).append(field_input);
    			  
    			var vartype_input = $("<input type='hidden' name='af-"+field_array[int][3]+"-vartype'/>");
    			$(vartype_input).attr("value",field_array[int][1]);
    			$(vartype_input).addClass(field_css_class);
    			$(td3).append(vartype_input);
    			
    			var name_input = $("<input type='hidden' name='af-"+field_array[int][3]+"-name'/>");
    			$(name_input).attr("value",field_array[int][3]);
    			$(name_input).addClass(field_css_class);
    			$(td4).append(name_input);
    	
    			$(tr)
	    			.append(td0)
	    			.append(td2)
	    			.append(td1)
	    			.append(td3)
	    			.append(td4)
	    			.appendTo(table);
    		}	
    	}
    	$("#autofield_area").html(table);
    	base_form_init();
    }
    
    /**
     * Opens a dialog in which the variables can be edited.
     */
    function open_edit_view()
    {
    	if(field_array.length > 0)
    	{
        	for ( var int = 0; int < field_array.length; int++) 
        	{
        		var value = $("#af-name-"+int).children("td:nth-child(2)").children("input").val();
        		field_array[int][2] = value;
    		}
    	}
      
    	var edit_div = $("<div id='DataAutofieldFooterEdit'></div>");
    	var table = $("<table id='DataAutofieldFooterEditTable'></table>");
    	var tr0 = $("<tr id='af-description-tr'><td>Name (can be duplicate)</td><td>Type</td><td colspan='2' id='AdminDescriptionTag' style='display:none;'>Internal Name (must be unique)</td></tr>");
		$(table)
			.append(tr0)
			.appendTo(edit_div);
		
    	var button_div = $("<div id='AutofieldButtonContainer'></div>")
    	var button_add = $("<button type='button' id='DataAutofieldAddField' class='DataAutofieldButton'>add field</button>");
    	var button_save = $("<button type='button' id='DataAutofieldClose' class='DataAutofieldButton'>save/close</button>");
    	var button_admin = $("<button type='button' id='DataAutofieldAdmin' class='DataAutofieldButton'>admin view</button>");

    	$(button_div)
    		.append(button_add)
	    	.append(button_save)
	    	.append(button_admin);
    	
    	$(edit_div)
    		.append(button_div)
    		.dialog(
    		{
    			"title" : "Define Custom Variables",
    			"minHeight" : "100",
    			"width" : "400",
    			"close" : close_edit_view(false)
    		});
    	
    	if(field_array.length > 0)
    	{
    		for (var int = 0; int < field_array.length; int++) 
    		{
    			var tr = $("<tr id='af-"+field_array[int][3]+"-tr'></tr>");
    			var td1 = $("<td></td>");
    			var field_input = $("<input type='textfield' value='"+field_array[int][0]+"' name='af-"+field_array[int][3]+"' class='DataAutofieldTitleInput'/>");
    			$(td1)
    				.append(field_input)
    				.appendTo(tr);
    		
    			var td2 = $("<td></td>");
    			var select = $("<select></select>");
    			if (field_array[int][1] == "float") 
    			{
    				$(select).append(get_option("float"));
    				$(select).append(get_option("int"));
    				$(select).append(get_option("string"));
    			}
    			else
    			{
    				if (field_array[int][1] == "string") 
    				{
        				$(select).append(get_option("string"));
        				$(select).append(get_option("int"));
        				$(select).append(get_option("float"));
    				}
    				else
    				{
        				$(select).append(get_option("int"));
        				$(select).append(get_option("float"));
        				$(select).append(get_option("string"));
    				}
    			}
    			$(td2)
	    			.append(select)
	    			.appendTo(tr);
    			
        		var td3 = $("<td></td>");
    			var name_input = $("<input type='textfield' value='"+field_array[int][3]+"' class='DataAutofieldNameInput'/>");
        		$(td3)
        			.append(name_input)
        			.appendTo(tr);
        		
        		var td4 = $("<td></td>");
        		var button_remove = $("<button type='button' id='DataAutofieldRemoveField"+field_array[int][3]+"' class='DataAutofieldRemoveButton'>remove</button>");
    			$(td4)
    				.append(button_remove)
    				.appendTo(tr);

        		$(table).append(tr);
        	}
    	}
    	else
    	{
    		add_field();
    	}
    	
    	$("#DataAutofieldFooterEdit").click(function(evt)
    	{
    		evt.preventDefault();
    		
    		var target = $(evt.target).attr("id");
    		if(target == "DataAutofieldAddField")
    		{
    			add_field();
    		}
    		else if(target == "DataAutofieldClose")
    		{
    			close_edit_view(true);
    		}
    		else if(target.substr(0,24) == "DataAutofieldRemoveField")
    		{
    			remove_field(target.substr(24,target.length-1));
    		}
    		else if(target == "DataAutofieldAdmin")
    		{
    			toggle_admin_view();
    		}
    	});
    	
    	$(".DataAutofieldNameInput").parent().hide();
    }
    
    /**
     * Returns an option element representing the given type.
     * @param type the data type: string, int or float
     * @returns the option element
     */
    function get_option(type)
    {
    	var option;
    	switch(type)
    	{
	    	case "string":
	    		option = $("<option value='string'>String</option>");
	    		break;
	
			case "int":
				option = $("<option value='int'>Integer</option>");
				break;
	
			case "float":
				option = $("<option value='float'>Real Number</option>");
				break;
				
			default:
				break;
    	}
    	return option;
    }
    
    /**
     * Adds a new input field to the edit dialog form.
     */
    function add_field()
    {
    	$("#DataAutofieldClose").attr("disabled","disabled");
    	
    	var rand = Math.floor(Math.random(100) * 100);
    	while($("#af-NEW"+rand+"-tr").length > 0)
    	{
    		rand = Math.floor(Math.random(100) * 100);
    	}
    	
    	var tr = $("<tr id='af-NEW"+rand+"-tr'></tr>");
    	
    	var td1 = $("<td><input type='textfield' class='DataAutofieldNoNameGiven DataAutofieldTitleInput' /></td>");
    	$(td1).children().keyup(function()
		{
			title_change_handler(this);
		});
    	
    	var td2 = $("<td></td>");
    	var select = $("<select></select>")
	    	.append(get_option("int"))
	    	.append(get_option("float"))
	    	.append(get_option("string"))
	    	.appendTo(td2);
    	
    	var td3 = $("<td><input type='textfield' class='DataAutofieldNameInput'/></td>");
    	$(td3).children().keyup(function()
    	{
    		name_change_handler(this);
    	});

    	var td4 = $("<td><button type='button' id='DataAutofieldRemoveFieldNEW"+rand+"' class='DataAutofieldRemoveButton'>remove</button></td>");
    	$(tr)
	    	.append(td1)
	    	.append(td2)
	    	.append(td3)
    		.append(td4)
    		.appendTo("#DataAutofieldFooterEditTable");
    	
    	$(".DataAutofieldNameInput").parent().hide();
    	
    	if($("#DataAutofieldAdmin").text() == "normal view")
    	{ //reinitialises the admin view if a field gets added (would be deleted otherwise)
        	toggle_admin_view();
        	toggle_admin_view();
    	}
    }
    
    /**
     * Removes a input field from the edit dialog form.
     * @param name the name of the input
     */
    function remove_field(name)
    {
    	if($("#DataAutofieldFooterEditTable > tbody").children().length > 2)
    	{
    		if (name.indexOf("NEW",0) === 0)
			{
    			var to_remove = $("#af-"+name+"-tr");
            	$(to_remove).remove();
			}
    		else
			{
    			var to_remove = $("#af-"+name+"-tr");
        		var index_to_delete = parseInt($(to_remove).prevAll().length)-1;
        		if(field_array.length > 0)
        		{
        			if(field_array[index_to_delete][3] == name)
            		{
        				field_array.splice(index_to_delete,1);
            		}
        		}
            	$(to_remove).remove();
			}
    	}
    }
   
    /**
     * Closes the edit dialog.
     * @param save whether to save the edited values
     */
    function close_edit_view(save)
    {
    	if(save)
    	{
	    	new_field_array = new Array();
	    	
	    	var inputs = $(".DataAutofieldTitleInput");	
	    	if(inputs.length > 0)
	    	{
	    		for ( var int = 0; int < inputs.length; int++) 
	    		{
					var vartype = $(inputs[int]).parent().parent().children("td:nth-child(2)").children("select").children("option:selected").attr("value");
	    			var title = $(inputs[int]).attr("value"); 
	    			var name = $(inputs[int]).attr("name").replace("af-","");
	    			
	    			this_input_array = new Array(3);
	    			this_input_array[0] = title;
	    			this_input_array[1] = vartype;
	    			if(field_array[int] != undefined)
	    			{
		    			this_input_array[2] = field_array[int][2]; //content
	    			}
	    			else
	    			{
	    				this_input_array[2] = "";
	    			}
	    			this_input_array[3] = name;
	    			
	    			new_field_array.push(this_input_array);
	    		}
	    		field_array = new_field_array;	
	    	}
    	}
		$("#DataAutofieldFooterEdit").dialog("destroy").remove();
		init();
    }
    
    /**
     * Renames a string incrementally by adding a number in case it is already present in a given string array.
     * @param name the string to verify / rename
     * @param names the string array to check against
     * @returns
     */
    function verify_name(name, names)
    {
		if($.inArray(name,names) != -1) //name exists already, rename again
		{
			var count = 0;
			while(count != -1)
			{
				name = name + count;
				if($.inArray(name,names) != -1)
				{
					if(count < 10)
					{
						name = name.substr(0,name.length-1);
					}
					else
					{
						name = name.substr(0,name.length-2);
					}
					count++;
				}
				else
				{
					count = -1;
				}
			}
		}
    	return name;
    }
    
    /**
     * Assigns a name to an input element (title), based on its value. This can be duplicate.
     * Assigns a unique name to a second input element (name), based on the value of the first one.
     * @param that the input element
     */
    function title_change_handler(that)
    {
    	if($(that).val() == "")
    	{
    		$("#DataAutofieldClose").attr("disabled","disabled");
    		return false;
    	}
		$(that).removeClass("DataAutofieldNoNameGiven");
		
		if($(".DataAutofieldNoNameGiven").length == 0 && $(".DataAutofieldDuplicateNameError").length == 0)
		{
			$("#DataAutofieldClose").removeAttr("disabled");
		}
		
		var names = new Array();
		$(".DataAutofieldNameInput").each(function()
		{
			if($(this).attr("value") != "")
			{
				names.push($(this).attr("value"));
			}
		});
		var name = verify_name($(that).attr("value"),names);
		
		$(that).parent().parent()
			.attr("id","af-"+name+"-tr")
			.find(".DataAutofieldNameInput").attr("value",name)
			.find(".DataAutofieldRemoveButton").attr("id","DataAutofieldRemoveField"+name);
		$(that).attr("name","af-"+name);
	}
    
    /**
     * Assigns a name to an input element (name), based on its value. 
     * If the name is duplicate, the input element will be visually marked with a css error class.
     * @param that the input element to rename
     */
    function name_change_handler(that)
    {
    	if($(that).val() == "")
    	{
    		$("#DataAutofieldClose").attr("disabled","disabled");
    		$(that).parent().addClass("AutoFieldDuplicateNameError");
    		return false;
    	}
    	
		$(that).parent().parent().attr("id","");
		
		if($(that).parent().hasClass("AutoFieldDuplicateNameError"))
		{
			var ref = $(that).parent().attr("id").replace("SameNameAs","");

			$(that).parent()
				.removeClass("AutoFieldDuplicateNameError")
				.attr("id","");
			
			$("#ErrorRef"+ref)
				.removeClass("AutoFieldDuplicateNameError")
				.attr("id","");
			
			$("#DataAutofieldClose").removeAttr("disabled");
		}
		
		var new_name = $(that).attr("value");  		
		var same_name = $("#af-"+new_name+"-tr");
		
		if(same_name.length > 0)
		{
			$("#DataAutofieldClose").attr("disabled","disabled");
			
			$(same_name).children("td:nth-child(3)")
				.addClass("AutoFieldDuplicateNameError")
				.attr("id","ErrorRef"+new_name);
			
			$(that).parent()
				.addClass("AutoFieldDuplicateNameError")
				.attr("id","SameNameAs"+new_name);
		}
		else
		{
    		$(that).parent().parent()
    		    .attr("id","af-"+new_name+"-tr")
    			.find(".DataAutofieldRemoveButton").attr("id","DataAutofieldRemoveField"+new_name)
    			.find(".DataAutofieldTitleInput").attr("name","af-"+new_name);
		}
	}
    
    /**
     * Toggles the admin view (show variable name input)
     */
    function toggle_admin_view()
    {
    	if($("#DataAutofieldAdmin").text() == "admin view")
    	{
    		$("#AdminDescriptionTag").show();
    		$("#DataAutofieldAdmin").text("normal view");
    		$(".DataAutofieldNameInput").parent().show();
    		$("#DataAutofieldFooterEdit").dialog({"width" : "550"});
    	}
    	else
    	{
    		$("#AdminDescriptionTag").hide();
    		$("#DataAutofieldAdmin").text("admin view");
    		$(".DataAutofieldNameInput").parent().hide();
    		$("#DataAutofieldFooterEdit").dialog({"width" : "400"});
    	}
    }
}