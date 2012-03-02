function data_browser() 
{
	var data_browser_table;
	var current_folder_id;
	var current_virtual_folder_id;
	var click_from_left_nav_menu = false;
	
	if (typeof(_data_browser_prototype_called) == "undefined")
	{  
		data_browser.prototype.open_link_in_ui = open_link_in_ui;
		init();
	}

	/**
	 * Initialise
	 */
	function init()
	{
		data_browser_table = $(".ListTable").children("tbody");
		var children = $(data_browser_table).children();
		
		if($("#DataBrowserAjaxActionSelect").html() == "")
		{
			var selector = $("<select id='DataBrowserActionSelect'></select>");
			$(selector).append("<option>Delete</option>");
			$("#DataBrowserAjaxActionSelect")
				.append(selector)
				.append("<button id='DataBrowserAction'>OK</button>");
		}
		
		if(children.length == 1 && $(children).hasClass("ListLoadingContents"))
		{ //not loaded contents yet. children can be an empty div due to list animate function -> base_list_class.js#126
			setTimeout(init,200);
		}
		else
		{
			var argument_array = list.get_argument_array();
			var argument_parts = argument_array.split("],[");
			current_folder_id = argument_parts[0].replace("[[\"folder_id\",","").replace(/"/g,"");
			current_virtual_folder_id = argument_parts[1].replace("\"virtual_folder_id\",","").replace(/]/g,"").replace(/"/g,"");
			
			//hack for resizing the "parent folder"-column
			var entry_height = $(".ListTable > tbody > tr:nth-child(2)").height();
			$(".DataBrowserParentFolderRow").height(entry_height);

			
			init_list_handler();
			init_base_tree_nav_link_handler();
			init_menu(current_folder_id);
			init_menu_handler();
			
			if(click_from_left_nav_menu)
			{
				show_current_dir_path_and_clear_stack();
				click_from_left_nav_menu = false;
			}
			else
			{
				show_current_dir_path();	
			}
		}
	}
	
	/**
	 * Initialise all event handlers within the browser.
	 */
	function init_list_handler() 
	{	
		$(data_browser_table).children("tr").each(function() 
		{
			if($(this).children("td").length < 2) 
			{ //no clickable link ("no results found!"): continue
				return true; 
			}

			var link = $(this).children("td:nth-child(3)").children();
			if ($(link).is("div")) 
			{
				link = $(link).children("a");
			}
			var href = $(link).attr("href");
			
			var linked_folder_id  = href.split("&folder_id=")[1];
			if(linked_folder_id != undefined)
			{
				linked_folder_id = linked_folder_id.split("&")[0];
			}
			
			var linked_virtual_folder_id = href.split("&vfolder_id=")[1];
			if(linked_virtual_folder_id != undefined)
			{
				linked_virtual_folder_id = linked_virtual_folder_id.split("&")[0];
			}
			
			var linked_file_id  = href.split("&file_id=")[1];
			if(linked_file_id != undefined)
			{
				linked_file_id = linked_file_id.split("&")[0];
			}
			
			$(this)
				.unbind("mouseover mouseleave click")
				.mouseover(function(event)
				{
					var text = $(this).children("td:nth-child(4)").text();
					if(!$(this).hasClass("DataBrowserFileSelected") && text != "Virtual Folder" && text != "Parent Folder")
					{
						$(this)
						.addClass("DataBrowserFileHover")
						.children().each(function(){
							if($(this).hasClass("ListTableColumnFirstEntry"))
							{
								$(this).css("border-left","solid #c3c3c3 2px");
							}
							else if($(this).hasClass("ListTableColumnLastEntry"))
							{
								$(this).css("border-right","solid #c3c3c3 2px");
							}
							$(this).css({
								"margin-bottom":"2px",
								"padding":"0px 4px",
								"border-bottom":"solid #c3c3c3 2px",
								"border-top":"solid #c3c3c3 2px"
							});	
						});
						
						
					}
				})
				.mouseleave(function()
				{
					$(this).removeClass("DataBrowserFileHover");
					if(!$(this).hasClass("DataBrowserFileSelected"))
					{
						var color = "white";
						if($(this).hasClass("trLightGrey"))
						{
							color = "#e0e0e0";
						}
						$(this).children().each(function()	
							{
								if($(this).hasClass("ListTableColumnFirstEntry"))
								{
									$(this).css("border-left","solid "+color+" 2px");
								}
								else if($(this).hasClass("ListTableColumnLastEntry"))
								{
									$(this).css("border-right","solid "+color+" 2px");
								}
								$(this).css(
								{
									"border-top":"solid "+color+" 2px",
									"border-bottom":"solid "+color+" 2px"
								});
							});
					}		
				})
				.click(function(evt)
				{
					evt.preventDefault();
					evt.stopImmediatePropagation();
					if(($(evt.target)[0] == $(link)[0]))
					{ //clicked on a link
						if(linked_folder_id == undefined && linked_virtual_folder_id == undefined)
						{ 
							close_data_browser_file_dialog();
							if(href.indexOf("&action=file_detail") != -1)
							{
								//clicked on file
								href = href.replace("&action=file_detail","").replace("index.php?","download.php?");
								$(location).attr("href",href);
							}
							else
							{ //clicked on value
								$(location).attr("href",href);
							}
						}
						else
						{ //clicked on folder
							close_data_browser_file_dialog();
							load_folder(linked_folder_id,linked_virtual_folder_id);		
						}
					}
					else
					{ 
						if($(evt.target).hasClass("DataBrowserDeleteCheckbox"))
						{ //clicked checkbox
							var box = $(evt.target);
							var parent = $(box).parent();
							var new_box; //IE sucks
							if($(box).is(":checked"))
							{
								new_box = $("<input type='checkbox' class='DataBrowserDeleteCheckbox' value='' name='' checked='checked'></input>");
							}
							else
							{
								new_box = $("<input type='checkbox' class='DataBrowserDeleteCheckbox' value='' name=''></input>");
							}
							$(box).remove();
							$(parent).append(new_box);
						}
						else
						{ //clicked somewhere inside row
							if($(this).hasClass("DataBrowserFileSelected"))
							{
								close_data_browser_file_dialog()
							}
							else 
							{
								open_data_browser_file_dialog(this);	
							}	
						}
					 }
				})
				.children("td:nth-child(3)").each(function()
				{ //bind thumbnail handler
					var filename = $(this).children().text();
					if(is_image(filename))
					{						
						show_thumbnail($(this).children(),"<div><img src='image.php?session_id="+get_array['session_id']+"&file_id="+linked_file_id+"&max_width=100&max_height=100' alt='' /></div>");
					}			
				});
		});
	}
	
	/**
	 * Determines the selected checkboxes and deletes the corresponding files.
	 */
	function delete_selected_files()
	{
		var action = $("#DataBrowserActionSelect").children("option:selected").val();
		$(".DataBrowserDeleteCheckbox:checked").each(function()
		{
			var type = $(this).parent().parent().parent().children("td:nth-child(4)").children();
			if($(type).text() == "Folder")
			{
				var link = $(this).parent().parent().parent().children("td:nth-child(3)").children().children().attr("href");
				var folder_id = link.split("&folder_id=")[1];
				$.ajax(
				{
					async : false,
					type : "POST",
					url : "ajax.php?nav=data&session_id="+get_array['session_id']+"&run=folder_delete",
					data : "folder_id="+folder_id+"&sure=true",
					success : function(data) {}
				});
			}
			else if($(type).text() == "Value")
			{
				var link = $(this).parent().parent().parent().children("td:nth-child(3)").children().attr("href");
				var split = link.split("&nav=data&value_id=");
				var value_id = split[1].replace("&action=value_detail","");
				$.ajax(
				{
					async : false,
					type : "POST",
					url : "ajax.php?nav=data&session_id="+get_array['session_id']+"&run=value_delete",
					data : "value_id="+value_id+"&sure=true",
					success : function(data) {}
				});
			}
			else
			{
				var link = $(this).parent().parent().parent().children("td:nth-child(3)").children().attr("href");
				var split = link.split("&nav=data&file_id=");
				var file_id = split[1].replace("&action=file_detail","");
				$.ajax(
				{
					async : false,
					type : "POST",
					url : "ajax.php?nav=data&session_id="+get_array['session_id']+"&run=file_delete",
					data : "file_id="+file_id+"&sure=true",
					success : function(data) {}
				});
			}
		});
	}
	
	/**
	 * Displays the path of the current directory in the upper right corner of the browser.
	 */
	function show_current_dir_path()
	{
		$.ajax(
		{
			type : "POST",
			url : "ajax.php?nav=data&session_id="+get_array['session_id']+"&run=get_data_browser_path",
			data : "folder_id="+current_folder_id+"&virtual_folder_id="+current_virtual_folder_id,
			success : function(data) 
			{
				$(data_browser_table).parent().parent().children(".OverviewTableRight").html(data);
			}
		});
	}
	
	/**
	 * Displays the path of the current directory in the upper right corner of the browser and clears the path history.
	 */
	function show_current_dir_path_and_clear_stack()
	{
		$.ajax(
		{
			type : "POST",
			url : "ajax.php?nav=data&session_id="+get_array['session_id']+"&run=get_data_browser_path_cleared",
			data : "folder_id="+current_folder_id+"&virtual_folder_id="+current_virtual_folder_id,
			success : function(data) 
			{
				$(data_browser_table).parent().parent().children(".OverviewTableRight").html(data);
			}
		});
	}
	
	/**
	 * Displays the contents of a folder or virtual folder in the browser.
	 * If both params are null, the home folder will be loaded.
	 * @param folder_id the folder id
	 * @param virtual_folder_id the virtual folder id
	 */
	function load_folder(folder_id,virtual_folder_id)
	{
		if(folder_id == undefined)
		{
			folder_id = null;
		}
		if(folder_id != null)
		{
			folder_id = "\""+folder_id+"\"";
		}
		
		if(virtual_folder_id == undefined)
		{
			virtual_folder_id = null;
		}
		if(virtual_folder_id != null)
		{
			virtual_folder_id = "\""+virtual_folder_id+"\"";
		}
		
		var argument_array = "[[\"folder_id\","+folder_id+"],[\"virtual_folder_id\","+virtual_folder_id+"]]";
		list.set_argument_array(argument_array);
		
		reinit();
	}
	
	/**
	 * Initialises the event handler listening for clicks from the left navigation menu.
	 */
	function init_base_tree_nav_link_handler()
	{
		if(typeof(tree_nav) == "undefined")
		{ //navigation might not be loaded yet
			setTimeout(init_base_tree_nav_link_handler,100);
		}
		else
		{
			var handler = function(href)
			{ // if list is instanciated: push the contents directly				
				click_from_left_nav_menu = true;
				var folder_id = href.split("&folder_id=")[1];
				load_folder(folder_id);
				return true;
			}
			tree_nav.set_follow_link_handler(handler);
		}
	}
	
	/**
	 * Opens the dropdown dialog for a selected row
	 * @param element the tr element that was selected
	 * @returns {Boolean} true if a valid element was selected (not a virtual folder)
	 */
	function open_data_browser_file_dialog(element)
	{
		close_data_browser_file_dialog();
		close_add_dialog();
		
		var dialog_width = 145;
		var pos = $(element).position();
		var width = $(element).width();
		var height = $(element).height();
		var offset_x = pos.left + width - dialog_width - 8;
		var offset_y = pos.top + height - 2;
		var dialog = $("<div id='DataBrowserFileDialog'>Permission denied.</div>");
		
		if($(element).children("td:nth-child(3)").children().hasClass("DataBrowserIsFolder"))
		{
			var link = $(element).children("td:nth-child(3)").children().children().attr("href");
			var folder_id = link.split("&folder_id=")[1];
			load_context_sensitive_dialog(folder_id,"folder");
		}
		else
		{
			var link = $(element).children("td:nth-child(3)").children().attr("href");
			if($(element).children("td:nth-child(4)").text() == "File")
			{
				var split = link.split("&nav=data&file_id=");
				var file_id = split[1].replace("&action=file_detail","");
				load_context_sensitive_dialog(file_id,"file");
			}
			else if($(element).children("td:nth-child(4)").text() == "Value")
			{
				var split = link.split("&nav=data&value_id=");
				var value_id = split[1].replace("&action=value_detail","");
				load_context_sensitive_dialog(value_id,"value");
			}
			else
			{
				return false;
			}
		}
		$(element)
			.addClass("DataBrowserFileSelected")
			.children("td").each(function()
			{				
				if($(this).hasClass("ListTableColumnFirstEntry"))
				{
					$(this).css("border-left","solid #669acc 2px");
				}
				else if($(this).hasClass("ListTableColumnLastEntry"))
				{
					$(this).css("border-right","solid #669acc 2px");
				}
				$(this).css(
				{
					"border-top":"solid #669acc 2px",
					"border-bottom":"solid #669acc 2px"
				});
			});

		$(dialog)
			.css(
			{
				"width":dialog_width,
				"position":"absolute",
				"top":offset_y,
				"left":offset_x
			})
			.hide()
			.appendTo("#main");

		setTimeout(bind_dialog_close_click_handler, 20);
		
		return true;
	}
	
	/**
	 * Closes the open dropdown dialog
	 */
	function close_data_browser_file_dialog() 
	{
		$("#DataBrowserFileDialog").remove();
		$(".DataBrowserFileSelected")
			.removeClass("DataBrowserFileSelected")
			.children("td").each(function()
			{
				var color = "white";
				if($(this).parent().hasClass("trLightGrey"))
				{
					color = "#e0e0e0";
				}
				if($(this).parent().hasClass("DataBrowserFileHover"))
				{
					color = "#c3c3c3";
				}
				if($(this).hasClass("ListTableColumnFirstEntry"))
				{
					$(this).css("border-left","solid "+color+" 2px");
				}
				else if($(this).hasClass("ListTableColumnLastEntry"))
				{
					$(this).css("border-right","solid "+color+" 2px");
				}
				$(this).css(
				{
					"border-top":"solid "+color+" 2px",
					"border-bottom":"solid "+color+" 2px"
				});
			});
	}
	
	/**
	 * Applies a click handler to the body listening for clicks outside the data browser to close open dialogs.
	 */
	function bind_dialog_close_click_handler(){
		$("body").click(function(evt)
		{
			close_data_browser_file_dialog();
			close_add_dialog();
			$("body").unbind("click");
		});
	}
	
	/**
	 * Sets the content of the dropdown menu
	 * @param item_id the id of the selected item
	 * @param type the type (folder, file, value)
	 */
	function load_context_sensitive_dialog(item_id, type)
	{
		var data = "file_id="+item_id;
		var action;
		switch(type)
		{
			case "folder":
				action = "get_context_sensitive_folder_menu";
				break;
			case "file":
				action = "get_context_sensitive_file_menu";
				break;
			case "value":
				action = "get_context_sensitive_value_menu";
				break;
			default: 
				break;
		}
		
		$.ajax({
			type : "POST",
			url : "ajax.php?session_id="+get_array['session_id']+"&nav=data&run="+action,
			data : data,
			success : function(data) 
			{
				if(data != "")
				{
					$("#DataBrowserFileDialog")
						.click(function(evt)
						{
							evt.preventDefault();
							var target = evt.target;
							var href = $(target).attr("href");
							if($(target).hasClass("DataBrowserDialogLinkFollowDirectly"))
							{
								$(location).attr("href",href);
							}
							else
							{
								var split = href.split(/&(.+)/);
								var action = split[0];
								var additional_params = split[1];
								open_link_in_ui(action, additional_params);
							}
						})
						.html(data);
				}
				$("#DataBrowserFileDialog").slideDown(200);
			}
		});
	}
	
	/**
	 * Opens a link in a popup dialog and assigns event handlers to the buttons
	 * @param type the type (folder, file, value)
	 * @param link the url
	 */
	function open_link_in_ui(action, additional_params)
	{
		close_data_browser_file_dialog();
		close_add_dialog();
		url = "ajax.php?session_id="+get_array['session_id']+"&nav=data&"+action;
		
		$.ajax({
			type : "POST",
			url : url,
			data : additional_params,
			success : function(data) 
			{
				var json = $.parseJSON(data);
				var click_handler = json["handler"];
				var click_handler_caption = json["handler_caption"];
				var html_template = json["content"];
				var html_template_caption = json["content_caption"];
				var container = $("<div id='DataBrowserLoadedAjaxContent'></div>").html(html_template);
				var additional_script = json["additional_script"];
				var dialog_width = 400;
				var position = ["center","center"];
				if(html_template_caption == "Add Value")
				{
					dialog_width = 600;
					position = ["center",200];
				}
				$(container).dialog({
					"title" : html_template_caption ,  
					"minHeight" : "100" , 
					"maxHeight" : "500" , 
					"position" : position,
					"width" : dialog_width, 
					"close" : function(){
						$(container).remove();
					},
					"buttons" : [
					{
						text : click_handler_caption , 
						click : function()
						{
					    	eval(click_handler);
					    }
					} , 
			        {
				    	text : "Cancel", 
				    	click : function()
				    	{
			         		$(container).dialog("close").remove();
			            }
					}]
				});
				if(additional_script != undefined)
				{
					eval(additional_script);
				}
			}
		});
	}
	
	/**
	 * Closes the popup dialog and reloads the browser
	 */
	function close_ui_window_and_reload()
	{
		$("#DataBrowserAddFileDialog").remove();
		$("#DataBrowserLoadedAjaxContent")
			.dialog("close")
			.remove();
		reinit();
	}
	
	/**
	 * Closes the popup dialog
	 */
	function fadeout_ui_window()
	{
		$(".ui-dialog").fadeOut(400);
	}
	
	/**
	 * Opens the add dialog in the top left corner of the browser 
	 */
	function open_add_dialog()
	{
		close_data_browser_file_dialog();
		$("#DataBrowserMenuAdd").css(
		{
			"border":"solid #669acc 2px",
			"border-bottom":"solid white 2px",
			"z-index":"99"
		});
		$("#DataBrowserAddFileDialog")
			.unbind("click")
			.click(function(evt)
			{
				evt.preventDefault();
				evt.stopPropagation();
				var target = evt.target;
				if(!$(target).is("a"))
				{
					return false;
				}
				var href = $(target).attr("href");
				var split = href.split(/&(.+)/);
				var action = split[0];
				var additional_params = split[1];
				open_link_in_ui(action, additional_params);
			})
			.slideDown(200);
		$("#DataBrowserAddFileCornerContainer").show();
		$("#DataBrowserAddFileCorner").show();
		
		bind_dialog_close_click_handler();
	}
	
	/**
	 * Closes the add dialog
	 */
	function close_add_dialog()
	{
		$("#DataBrowserMenuAdd").css("border","solid white 2px");
		$("#DataBrowserAddFileDialog").hide();
		$("#DataBrowserAddFileCornerContainer").hide();
		$("#DataBrowserAddFileCorner").hide();
	}
	
	/**
	 * Initialise the menu specific assets (css, images) for the current folder
	 * @param folder_id the current folder id
	 */
	function init_menu(folder_id)
	{
		$(".ListTable > thead > tr > th:first").append("<input type='checkbox' id='DataBrowserActionMasterCheckbox' name='' value=''></input>")
		
		$.ajax({
			type : "POST",
			url : "ajax.php?nav=data&session_id="+get_array['session_id']+"&run=get_browser_menu",
			data : "folder_id="+folder_id,
			success : function(data) 
			{
				var json = $.parseJSON(data);
				var add = json["add"];
				var image_browser = json["image_browser"];
				if(add)
				{
					var pos = $("#DataBrowserMenuAdd").position();
					var height = $("#DataBrowserMenuAdd").height();
					var width = $("#DataBrowserMenuAdd").width();
					var offset_x = pos.left;
					var offset_y = pos.top + height + 6;
					
					var dialog = $("<div id='DataBrowserAddFileDialog'></div>")
						.css({"position":"absolute","top":offset_y,"left":offset_x,"z-index":"98"})
						.appendTo("#main")
						.html(json["add_list"])
						.hide();

					var corner_offset_x = offset_x + width + 18;
					var corner_offset_y = offset_y - height + 2;
					var corner_container = $("<div id='DataBrowserAddFileCornerContainer'></div>")
						.css(
						{
							"position":"absolute",
							"top":corner_offset_y + 1,
							"left":corner_offset_x,
							"z-index":"100",
							"width":height-1,
							"height":height-1, 
							"background-color":"white",
							"margin":"0",
							"padding":"0"
						})
						.hide()
						.appendTo("#main");
					var corner = $("<div id='DataBrowserAddFileCorner'></div>")
						.css(
						{
							"position":"absolute",
							"top":corner_offset_y,
							"left":corner_offset_x,
							"z-index":"101",
							"width":height-2,
							"height":height-2
						})
						.hide()
						.appendTo("#main");
					
					$("#DataBrowserMenuAdd").children("img").attr("src","images/icons/add.png");
					$("#DataBrowserMenuAdd").removeClass("Deactivated");
				}
				else
				{
					$("#DataBrowserMenuAdd").children("img").attr("src","images/icons/add_na.png");
					$("#DataBrowserMenuAdd").addClass("Deactivated");
				}
				if(image_browser)
				{
					//implement future image browser functionality here
					
					$("#DataBrowserMenuImageBrowser").children("img").attr("src","images/icons/images.png");
					$("#DataBrowserMenuImageBrowser").removeClass("Deactivated");
				}
				else
				{
					$("#DataBrowserMenuImageBrowser").children("img").attr("src","images/icons/images_d.png");
					$("#DataBrowserMenuImageBrowser").addClass("Deactivated");
				}
			}
		});
	}
	
	/**
	 * Initialises all menu event handlers
	 */
	function init_menu_handler()
	{
		$(".DataBrowserAjaxPage").unbind("click");
		list.reinit_page_handler();
		$(".DataBrowserAjaxPage").bind("click",function(event) 
		{ 
			reinit_without_reload(); //re-init on page change
		});
		
		$(".DataBrowserAjaxColumn").unbind("click");
		list.reinit_sort_handler();
		$(".DataBrowserAjaxColumn").bind("click",function(event) 
		{ 
			reinit_without_reload(); //re-init on page change
		});
		
		
		$("#DataBrowserMenuAdd")
			.unbind("click")
			.click(function(event)
			{
				if(!$(this).hasClass("Deactivated"))
				{
					event.preventDefault();
					event.stopImmediatePropagation();
					if($("#DataBrowserAddFileDialog").is(':visible'))
					{
						close_add_dialog();
					}
					else
					{
						open_add_dialog();
					}
				}
			});
		
		$("#DataBrowserMenuHomeFolder")
			.unbind("click")
			.click(function(event)
			{
				event.preventDefault();
				$.ajax({
					async:false,
					type : "POST",
					url : "ajax.php?nav=data&session_id="+get_array['session_id']+"&run=delete_stack",
					data : "",
					success : function(data) 
					{
						load_folder(null, null);
					}
				});
			});
		
		$("#DataBrowserAction")
			.unbind("click")
			.click(function()
			{
				if($(".DataBrowserDeleteCheckbox:checked").length === 0)
				{
					return;
				}
				
				var container = $("<div>Do you really want to delete the selected items?</div>")
				$(container).css({"text-align":"center","padding-top":"20px"});
				$(container).dialog({"title" : "Confirm" ,  
					"close" : function(){$(container).remove();},
					"buttons" : [
					     {text : "Yes" , click : function(){
					    	var options = { buttons: {}};
					    	$(container).dialog('option', options);
					    	$(container).html("<img src='images/loading.gif' alt=''></img>");
					    	delete_selected_files();
					    	$(container).dialog("close").remove();
							reinit();
					     }} , 
			             {text : "No", click : function(){
			         		$(container).dialog("close").remove();
			             }}]
				});
			});
		
		$("#DataBrowserActionMasterCheckbox")
			.unbind("click")
			.click(function()
			{
				if($(this).attr("checked"))
				{
					$(".DataBrowserDeleteCheckbox").each(function(){
						if($(this).attr("disabled") !== true)
						{
							$(this).attr("checked","checked");
						}
					});
				}
				else
				{
					$(".DataBrowserDeleteCheckbox").removeAttr("checked");
				}
			});
	}
	
	/**
	 * Displays a thumbnail tooltip for an image element
	 * @param element the image element to be tooltipped
	 * @param image the image to be displayed
	 */
	function show_thumbnail(element, image) 
	{
		var offsetX = 20;
		var offsetY = 10;
		$(element).hover(function(e)
		{
			$("<div id='thumbnail'>"+image+"</div>")
				.css(
				{
					"position":"absolute",
					"background-color":"white",
					"border":"solid black 1px",
					"padding":"2px 4px 2px 4px",
					"font-family":"arial",
					"font-size":"12px",
					"top": e.pageY + offsetY,
					"left": e.pageX + offsetX,
					"border-radius": "5px",
					"-moz-border-radius": "5px",
					"-webkit-border-radius": "5px"
				})
				.hide()
				.appendTo('body')
				.fadeIn(300);
		},function()
		{
			$('#thumbnail').remove();
		});
		$(element).mousemove(function(e) 
		{
			$("#thumbnail").css(
			{
				"top": e.pageY + offsetY,
				"left": e.pageX + offsetX
			});
		});
	}
	
	/**
	 * Closes all open dialogs and reinitialises all event handlers
	 */
	function reinit()
	{
		$("#DataBrowserAddFileDialog").remove();
		close_data_browser_file_dialog();
		close_add_dialog();
		list.reload();
		init();
	}
	
	/**
	 * Closes all open dialogs and reinitialises all event handlers
	 */
	function reinit_without_reload()
	{
		$("#DataBrowserAddFileDialog").remove();
		close_data_browser_file_dialog();
		close_add_dialog();
		init();
	}
	
}