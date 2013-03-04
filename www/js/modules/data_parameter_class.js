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

DataParameter = function()
{
	var line_counter = 1;
	var limit_array = new Array();
	var limit_counter = 0;
	var current_limit = 0;
	var language_array = new Object();
	
	limit_array[limit_counter] = new Object();
	limit_array[limit_counter]['name'] = "First Limit";
	limit_array[limit_counter]['usl'] = new Array();
	limit_array[limit_counter]['lsl'] = new Array();
		
	init_admin = function(element)
	{
		$("#"+element).after("" +
			"<div id='DataParameterAdminTemplateLimitsDialog' title='Limits (USL/LSL)'>" +
			"	<div id='DataParameterAdminTemplateLimitsDialogSelect' class='Form'><select style='width: 500px;'></select></div>" +
			"	<div id='DataParameterAdminTemplateLimitsDialogButtons'>" +
			"		<a href='#' class='ListButton' id='DataParameterAdminTemplateLimitsDialogNewButton'>" +
			"		<img src='images/icons/add.png' alt='' />" +
			"		<div>New Limit</div>" +
			"		</a>" +
			"		<a href='#' class='ListButton' id='DataParameterAdminTemplateLimitsDialogRenameButton'>" +
			"		<img src='images/icons/delete.png' alt='' />" +
			"		<div>Rename current Limit</div>" +
			"		</a>" +
			"		<a href='#' class='ListButton' id='DataParameterAdminTemplateLimitsDialogDeleteButton'>" +
			"		<img src='images/icons/delete.png' alt='' />" +
			"		<div>Delete current Limit</div>" +
			"		</a>" +
			"	</div>" +
			"	<div style='clear: both;' class='Form'>" +
			"		<table style='width: 100%; text-align: left;' id='DataParameterTemplateLimitTable'>" +
			"			<thead>" +
			"				<tr>" +
			"					<th>Name</th>" +
			"					<th>USL (max)</th>" +
			"					<th>LSL (max)</th>" +
			"				</tr>" +
			"			</thead>" +
			"			<tbody>" +
			"			</tbody>" +
			"		</table>" +
			"	</div>" +
			"</div>" +
			"" +
			"<div id='DataParameterAdminTemplateLimitsDialogNew' title='New Limit' class='Form'>" +
			"Name: <input type='text' size='35' />" +
			"</div>" +
			"" +
			"<div id='DataParameterAdminTemplateLimitsDialogDelete' title='Delete Limit'>" +
			"Are you sure?" +
			"</div>" +
			"" +
			"<div id='DataParameterAdminTemplateLimitsDialogRename' title='Rename Limit'>" +
			"Name: <input type='text' size='35' />" +
			"</div>");
		
		init();
	}
	
	init_parameter = function()
	{
		
	}
	
	get_field_json = function(class_name)
	{
		var field_object = new Object();
		
		$("."+class_name).each(function()
		{
			var name = $(this).attr("name");
			if (name !== undefined)
			{
				var type = name.split("-")[0];
				var id = parseInt(name.split("-")[1]);
				var value = $(this).val();

				if ((type !== "lsl") && (type !== "usl"))
				{
					if (field_object[id] === undefined)
					{
						field_object[id] = new Object();
					}
					
					field_object[id][""+type+""] = value;
				}
				else
				{
					if (type === "lsl")
					{
						
					}
					else
					{
						
					}
				}
			}
		});
		
		return JSON.stringify(field_object);
	}
	
	get_limit_json = function(class_name)
	{	
		$("."+class_name).each(function()
		{
			var name = $(this).attr("name");
			if (name !== undefined)
			{
				var type = name.split("-")[0];
				var id = parseInt(name.split("-")[1]);
				var value = $(this).val();

				if ((type === "lsl") || (type === "usl"))
				{
					if (type === "lsl")
					{
						limit_array[0]['lsl'][id] = value;
					}
					else
					{
						limit_array[0]['usl'][id] = value;
					}
				}
			}
		});
		
		return JSON.stringify(limit_array);
	}
	
	set_language_json  = function(language_json)
	{
		language_array = jQuery.parseJSON(language_json);
	}
	
	set_limit_json = function(limit_json)
	{
		var tmp_limit_array = jQuery.parseJSON(limit_json);
		if ((tmp_limit_array !== null) && (tmp_limit_array !== undefined) && (tmp_limit_array.length > 0))
		{
			limit_array = tmp_limit_array;
		}
	}
	
	set_line_counter = function(local_line_counter)
	{
		line_counter = parseInt(local_line_counter);
	}
	
	set_limit_counter = function(local_limit_counter)
	{
		if (local_limit_counter > 0)
		{
			limit_counter = parseInt(local_limit_counter);
		}
	}
	
	this.init_admin = init_admin;
	this.init_parameter = init_parameter;
	this.get_field_json = get_field_json;
	this.get_limit_json = get_limit_json;
	this.set_language_json = set_language_json;
	this.set_limit_json = set_limit_json;
	this.set_line_counter = set_line_counter;
	this.set_limit_counter = set_limit_counter;
	
	get_language_label = function(address)
	{
		if ((language_array !== undefined))
		{
			if (language_array[address] !== undefined)
			{
				return language_array[address];
			}
			else
			{
				return address;
			}
		}
		else
		{
			return address;
		}
	}
	
	init = function()
	{
		// Limit Dialog
		$("#DataParameterAdminTemplateLimitsDialog").dialog(
		{
			autoOpen: false,
			height: 400,
			width: 530,
			buttons: 
			[{
				text: get_language_label("close_button"),
				click: function()
				{
				 	$(this).dialog("close");
				 	$("#DataParameterAdminTemplateLimitsDialogSelect select").trigger("onchange");
				 	$("#DataParameterTemplateTable tbody tr").each(function()
		 			{
				 		var id = $(this).attr("id").replace("DataParameterTemplateField","");
		 				$(this).children().children("input[name='usl-"+id+"']").val(limit_array[0]['usl'][id]);
		 				$(this).children().children("input[name='lsl-"+id+"']").val(limit_array[0]['lsl'][id]);
		 			});
				}
			}]
		});
		
		// New Limit
		$("#DataParameterAdminTemplateLimitsDialogNew").dialog(
		{
			autoOpen: false,
			height: 150,
			width: 350,
			buttons: 
			[{
				text: get_language_label("new_button"),
				click: function()
				{
				 	$(this).dialog("close");
				 	
				 	var name = $(this).find('input').val();
				 	
				 	limit_counter++;
				 	limit_array[limit_counter] = new Object();
				 	limit_array[limit_counter]['name'] = name;
				 	limit_array[limit_counter]['usl'] = new Array();
				 	limit_array[limit_counter]['lsl'] = new Array();
				 	
				 	var new_option = $("<option value='"+limit_counter+"'>"+name+"</option>")
				 	$("#DataParameterAdminTemplateLimitsDialogSelect select").append(new_option);
				 	$(new_option).attr("selected", "selected");
				 	
				 	base_form_init();
				 	
				 	$("#DataParameterAdminTemplateLimitsDialogSelect select").trigger("onchange");
				}
			},{
				text: get_language_label("cancel_button"),
				click: function()
				{
				 	$(this).dialog("close");
				}
			}]
		});
		
		// Delete Limit
		$("#DataParameterAdminTemplateLimitsDialogDelete").dialog(
		{
			autoOpen: false,
			height: 150,
			width: 350,
			buttons: 
			[{
				text: get_language_label("yes_button"),
				click: function()
				{
				 	$(this).dialog("close");
				 	
				 	limit_array[limit_counter] = undefined;
				 	
				 	$("#DataParameterAdminTemplateLimitsDialogSelect select").find("option:selected").remove();
					$("#DataParameterAdminTemplateLimitsDialogSelect select").find("option[value='0']").attr("selected", "selected");
				 	
				 	base_form_init();
				 	
				 	$("#DataParameterAdminTemplateLimitsDialogSelect select").trigger("onchange");
				}
			},{
				text: get_language_label("no_button"),
				click: function()
				{
				 	$(this).dialog("close");
				}
			}]
		});
	
		// Rename Limit
		$("#DataParameterAdminTemplateLimitsDialogRename").dialog(
		{
			autoOpen: false,
			height: 150,
			width: 350,
			buttons: 
			[{
				text: get_language_label("save_button"),
				click: function()
				{
				 	$(this).dialog("close");
				 	
				 	var name = $(this).find('input').val();
				 	
				 	limit_array[current_limit]['name'] = name;
				 	
				 	$("#DataParameterAdminTemplateLimitsDialogSelect select").find("option:selected").html(name);
				 	
				 	base_form_init();
				},
			},{
				text: get_language_label("cancel_button"),
				click: function()
				{
				 	$(this).dialog("close");
				}
			}]
		});

		// Limit Select Change
		$("#DataParameterAdminTemplateLimitsDialogSelect select").bind("onchange", function()
		{
			if (limit_array[current_limit] !== undefined)
			{
				$(".DataParameterAdminLimitValue").each(function()
				{
					if ($(this).is("input"))
					{
						var name = $(this).attr("name");
						
						if (name.indexOf("usl-") !== -1)
						{
							name = parseInt(name.replace("usl-",""));
							limit_array[current_limit]['usl'][name] = $(this).val();
						}
						else if (name.indexOf("lsl-") !== -1)
						{
							name = parseInt(name.replace("lsl-",""));
							limit_array[current_limit]['lsl'][name] = $(this).val();
						}
					}
				});
			}
			
			current_limit = $(this).children("option:selected").val();
			
			$(".DataParameterAdminLimitValue").each(function()
			{
				if ($(this).is("input"))
				{
					var name = $(this).attr("name");
					
					if (name.indexOf("usl-") !== -1)
					{
						name = parseInt(name.replace("usl-",""));
						$(this).val(limit_array[current_limit]['usl'][name]);
					}
					else if (name.indexOf("lsl-") !== -1)
					{
						name = parseInt(name.replace("lsl-",""));
						$(this).val(limit_array[current_limit]['lsl'][name]);
					}
				}
			});
			
			console.log(limit_array);
		});
	}
	
	// New Template Line
	$("#DataParameterTemplateFieldNewButton").click(function()
	{
		line_counter++;
		
		if ($("#DataParameterTemplateTable").children("tbody").children(":last-child").hasClass("odd"))
		{
			var tr_class = "even";	
		}
		else
		{
			var tr_class = "odd";	
		}
		
		var measuring_unit_select = $("#DataParameterTemplateTable").children("tbody").children(":first-child").find("select");
		measuring_unit_select = measuring_unit_select.clone();
		$(measuring_unit_select).removeClass("FormSelect");
		
		$("#DataParameterTemplateTable").children("tbody").children(":last-child").after("<tr class='"+tr_class+" DataParameterTemplateField' id='DataParameterTemplateField"+line_counter+"'>" +
			"<td><input type='text' name='name-"+line_counter+"' class='DataParameterAdminValue' /></td>" +
			"<td>"+$(measuring_unit_select).prop('outerHTML')+"</td>" +
			"<td><input type='text' size='6' name='usl-"+line_counter+"' class='DataParameterAdminValue' /></td>" +
			"<td><input type='text' size='6' name='lsl-"+line_counter+"' class='DataParameterAdminValue' /></td>" +
			"<td><input type='text' size='6' name='min-"+line_counter+"' class='DataParameterAdminValue' /></td>" +
			"<td><input type='text' size='6' name='max-"+line_counter+"' class='DataParameterAdminValue' /></td>" +
			"<td>Methods</td>" +
			"<td><a title='delete' style='cursor: pointer;' id='DataParameterTemplateFieldDeleteButton"+line_counter+"'><img src='images/icons/delete.png' alt='D' /></a></td>" +
			"</tr>");
		
		$("#DataParameterTemplateFieldDeleteButton"+line_counter+"").click(function()
		{
			var current_line_counter = $(this).attr("id").replace("DataParameterTemplateFieldDeleteButton","");
			
			$(this).parent().parent().fadeOut(400, function()
			{
				$(this).remove();

				var tmp_delete_counter = 1;
				
				$(".DataParameterTemplateField").each(function()
				{
					if ((tmp_delete_counter % 2) !== 0)
					{
						var tmp_tr_class = "even";	
					}
					else
					{
						var tmp_tr_class = "odd";	
					}
					
					$(this).removeClass("odd");
					$(this).removeClass("even");
					$(this).addClass(tmp_tr_class);
					
					tmp_delete_counter++;
				});
			});
		});
		
		base_form_init();
	});
	
	// Open Limit Dialog
	$("#DataParameterTemplateLimitButton").click(function()
	{
		$("#DataParameterAdminTemplateLimitsDialogSelect select").empty();
		
		for (var i=0;i<=limit_counter;i++)
		{
			$("#DataParameterAdminTemplateLimitsDialogSelect select").append("<option value='"+i+"'>"+limit_array[i]['name']+"</option>");
		}
		
		$("#DataParameterAdminTemplateLimitsDialogNewButton").click(function()
		{
			$("#DataParameterAdminTemplateLimitsDialogNew").find('input').val("");
			$("#DataParameterAdminTemplateLimitsDialogNew").dialog("open");
		});
		
		$("#DataParameterAdminTemplateLimitsDialogRenameButton").click(function()
		{
			$("#DataParameterAdminTemplateLimitsDialogRename").find('input').val(limit_array[current_limit]['name']);
			$("#DataParameterAdminTemplateLimitsDialogRename").dialog("open");	
		});
		
		$("#DataParameterAdminTemplateLimitsDialogDeleteButton").click(function()
		{
			$("#DataParameterAdminTemplateLimitsDialogDelete").dialog("open");	
		});
						
		$("#DataParameterTemplateLimitTable tbody").empty();
		$("#DataParameterTemplateTable tbody tr").each(function()
		{
			var id = $(this).attr("id").replace("DataParameterTemplateField","");
			var name = $(this).children(":first-child").children("input[type=text]").val();
			var usl = $(this).children().children("input[name='usl-"+id+"']").val();
			var lsl = $(this).children().children("input[name='lsl-"+id+"']").val();
			
			if ((name === undefined) || (name === ""))
			{
				name = "Field "+id;	
			}
						
			$("#DataParameterTemplateLimitTable tbody").append("<tr>" +
			"<td>"+name+"</td>" +
			"<td><input type='text' size='6' name='usl-"+id+"' class='DataParameterAdminLimitValue' value='"+usl+"' /></td>" +
			"<td><input type='text' size='6' name='lsl-"+id+"' class='DataParameterAdminLimitValue' value='"+lsl+"' /></td>" +
			"</tr>");
		});
		
		$("#DataParameterAdminTemplateLimitsDialogSelect select").find("option:selected").attr("selected", false);
		$("#DataParameterAdminTemplateLimitsDialogSelect select").find("option[value=0]").attr("selected", "selected");
		
		current_limit = 0;
		
		base_form_init();
		
		$("#DataParameterAdminTemplateLimitsDialog").dialog("open");
	});
}