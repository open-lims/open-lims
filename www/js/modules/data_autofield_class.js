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

function autofield(field_array_string)
{
	var field_array;
	
    if (typeof(autofield_prototype_called) == "undefined")
    {
    	autofield_prototype_called = true;
    	if(field_array_string != undefined && field_array_string != '')
    	{
        	field_array = unserialize(field_array_string);
        	console.log(field_array);
    	}
    	else
    	{
    		field_array = new Array();
    	}
    	init();
    	$("#autofield_edit").click(function(evt){
    		evt.preventDefault();
    		open_edit_view();
    	});
    }
    
    function init()
    {
    	var table = $("<table id='DataAutofieldTable'></table>")	
    	if(field_array.length > 0)
    	{
        	for (var int = 0; int < field_array.length; int++) {
    			var tr = $("<tr id='af-name-"+int+"'></tr>");
    			var td1 = $("<td>"+field_array[int][0]+"</td>");
    			var td2 = $("<td></td>");
    			var td3 = $("<td></td>");
    			var td4 = $("<td></td>");
    			var field_input = $("<input type='textfield' name='af-"+field_array[int][3]+"'/>");
    			$(field_input).attr("value",field_array[int][2]);
    			$(td2).append(field_input);
    			var vartype_input = $("<input type='hidden' name='af-"+field_array[int][3]+"-vartype'/>");
    			$(vartype_input).attr("value",field_array[int][1]);
    			$(td3).append(vartype_input);
    			var name_input = $("<input type='hidden' name='af-"+field_array[int][3]+"-name'/>");
    			$(name_input).attr("value",field_array[int][3]);
    			$(td4).append(name_input);
    			$(tr).append(td1)
    			$(tr).append(td2);
    			$(tr).append(td3);
    			$(tr).append(td4);
    			$(table).append(tr);
    		}	
    	}
    	$("#autofield_area").html(table);
    }
    
    function open_edit_view()
    {
      if(field_array.length > 0)
    	{
        	for ( var int = 0; int < field_array.length; int++) {
        		var value = $("#af-name-"+int).children("td:nth-child(2)").children("input").val();
        		field_array[int][2] = value;
    		}
    	}
    	var edit_div = $("<div id='DataAutofieldFooterEdit'></div>");
    	var table = $("<table id='DataAutofieldFooterEditTable'></table>");
    	var tr0 = $("<tr id='af-description-tr'><td>Name (can be duplicate)</td><td>Type</td><td colspan='2' id='AdminDescriptionTag' style='display:none;'>Internal Name (must be unique)</td></tr>");
		$(table).append(tr0);
    	$(edit_div).append(table);
    	var button_div = $("<div id='AutofieldButtonContainer'></div>")
    	var button_add = $("<button type='button' id='DataAutofieldAddField' class='DataAutofieldButton'>add field</button>");
    	$(button_div).append(button_add);
    	var button_save = $("<button type='button' id='DataAutofieldClose' class='DataAutofieldButton'>save/close</button>");
    	$(button_div).append(button_save);
    	var button_admin = $("<button type='button' id='DataAutofieldAdmin' class='DataAutofieldButton'>admin view</button>");
    	$(button_div).append(button_admin);
    	$(edit_div).append(button_div);
    	$(edit_div).dialog({"title" : "Define Custom Variables" ,  "minHeight" : "100" , "width" : "400" , "close" : close_edit_view(false)});
    	if(field_array.length > 0)
    	{
    		for (var int = 0; int < field_array.length; int++) {
    			var tr = $("<tr id='af-"+field_array[int][3]+"-tr'></tr>");
    			var td1 = $("<td></td>");
    			var field_input = $("<input type='textfield' value='"+field_array[int][0]+"' name='af-"+field_array[int][3]+"' class='DataAutofieldTitleInput'/>");
    			$(tr).append(td1);
    			$(td1).append(field_input);
    		
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
    				if (field_array[int][1] == "string") {
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
    			$(tr).append(td2);
    			$(td2).append(select);
        		var td3 = $("<td></td>");
    			var name_input = $("<input type='textfield' value='"+field_array[int][3]+"' class='DataAutofieldNameInput'/>");
        		$(tr).append(td3);
        		$(td3).append(name_input);
        		
        		var td4 = $("<td></td>");
        		var button_remove = $("<button type='button' id='DataAutofieldRemoveField"+field_array[int][3]+"' class='DataAutofieldRemoveButton'>remove</button>");
    			$(tr).append(td4);
    			$(td4).append(button_remove);

        		$(table).append(tr);
        	}
    	}
    	else
    	{
    		add_field();
    	}
    	$("#DataAutofieldFooterEdit").click(function(evt){
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
    	}
    	return option;
    }
    
    function add_field()
    {
    	$("#DataAutofieldClose").attr("disabled","disabled");
    	var rand = Math.floor(Math.random(100)*100);
    	while($("#af-NEW"+rand+"-tr").length > 0)
    	{
    		rand = Math.floor(Math.random(100)*100);
    	}
    	var tr = $("<tr id='af-NEW"+rand+"-tr'></tr>");
    	var td1 = $("<td><input type='textfield' class='DataAutofieldNoNameGiven DataAutofieldTitleInput'/></td>");
    	$(tr).append(td1);
    	$(td1).children().keyup(function(){
    		title_change_handler(this);
    	});
    	var td2 = $("<td></td>");
    	var select = $("<select></select>");
    	$(select).append(get_option("int"));
    	$(select).append(get_option("float"));
    	$(select).append(get_option("string"));
    	$(tr).append(td2);
    	$(td2).append(select);
    	var td3 = $("<td><input type='textfield' class='DataAutofieldNameInput'/></td>");
    	$(td3).children().keyup(function(){
    		name_change_handler(this);
    	});
    	$(tr).append(td3);
    	var td4 = $("<td><button type='button' id='DataAutofieldRemoveFieldNEW"+rand+"' class='DataAutofieldRemoveButton'>remove</button></td>");
    	$(tr).append(td4);
    	$("#DataAutofieldFooterEditTable").append(tr);
    	$(".DataAutofieldNameInput").parent().hide();
    }
    
    function remove_field(name)
    {
    	if($("#DataAutofieldFooterEditTable > tbody").children().length > 2)
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
    
    function title_change_handler(that)
    {
		$(that).removeClass("DataAutofieldNoNameGiven");
		if($(".DataAutofieldNoNameGiven").length == 0 && $(".DataAutofieldDuplicateNameError").length == 0)
		{
			$("#DataAutofieldClose").removeAttr("disabled");
		}
		var names = new Array();
		$(".DataAutofieldNameInput").each(function(){
			if($(this).attr("value") != "")
			{
				names.push($(this).attr("value"));
			}
		});
		var name = verify_name($(that).attr("value"),names);
		$(that).parent().parent().find(".DataAutofieldNameInput").attr("value",name);
		$(that).parent().parent().find(".DataAutofieldRemoveButton").attr("id","DataAutofieldRemoveField"+name);
		$(that).parent().parent().attr("id","af-"+name+"-tr");
		$(that).attr("name","af-"+name);
	}
    
    function name_change_handler(that)
    {
		$(that).parent().parent().attr("id",""); 
		var new_name = $(that).attr("value");  		
		if($(that).parent().hasClass("AutoFieldDuplicateNameError"))
		{
			$(that).parent().removeClass("AutoFieldDuplicateNameError");
			var ref = $(that).parent().attr("id").replace("SameNameAs","");
			$(that).parent().attr("id","");
			$("#ErrorRef"+ref).removeClass("AutoFieldDuplicateNameError")
			$("#ErrorRef"+ref).attr("id","");
			$("#DataAutofieldClose").removeAttr("disabled");
		}
		var same_name = $("#af-"+new_name+"-tr");
		if(same_name.length > 0)
		{
			$("#DataAutofieldClose").attr("disabled","disabled");
			$(same_name).children("td:nth-child(3)").addClass("AutoFieldDuplicateNameError");
			$(same_name).children("td:nth-child(3)").attr("id","ErrorRef"+new_name);
			$(that).parent().addClass("AutoFieldDuplicateNameError");
			$(that).parent().attr("id","SameNameAs"+new_name);
		}
		else
		{
    		$(that).parent().parent().find(".DataAutofieldRemoveButton").attr("id","DataAutofieldRemoveField"+new_name);
    		$(that).parent().parent().attr("id","af-"+new_name+"-tr");
    		$(that).parent().parent().find(".DataAutofieldTitleInput").attr("name","af-"+new_name);
		}
	}
    
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