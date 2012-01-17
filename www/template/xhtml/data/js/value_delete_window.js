$("#DataBrowserLoadedAjaxContent").children(".center").html("<img src='images/loading.gif' />");
$.ajax({
	type : "POST",
	url : "ajax.php?session_id=[[SESSION_ID]]&nav=data&run=value_delete",
	data : "value_id=[[VALUE_ID]]&sure=true",
	success : function(data) 
	{
		close_ui_window_and_reload();
	}
});