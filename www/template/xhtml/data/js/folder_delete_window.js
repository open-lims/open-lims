var loading_image = $("<div><img src='images/loading.gif' /></div>")
	.css({
		"margin-left":"auto", 
		"margin-right": "auto",
		"width" : "220px"
		});

$("#DataBrowserLoadedAjaxContent").html(loading_image);
$.ajax({
	type : "POST",
	url : "ajax.php?nav=data&session_id=[[SESSION_ID]]&run=folder_delete",
	data : "folder_id=[[FOLDER_ID]]&sure=true",
	success : function(data) 
	{
		close_ui_window_and_reload();
	}
});