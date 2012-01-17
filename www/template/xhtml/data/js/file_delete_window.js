$("#DataBrowserLoadedAjaxContent").children(".center").html("<img src='images/loading.gif' />");
$.ajax({
	type : "POST",
	url : "ajax.php?session_id=[[SESSION_ID]]&nav=data&run=file_delete",
	data : "file_id=[[FILE_ID]]&sure=true",
	success : function(data) 
	{
		close_ui_window_and_reload();
	}
});