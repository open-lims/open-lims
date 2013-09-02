$("#DataBrowserLoadedAjaxContent").children(".center").html("<img src='images/loading.gif' />");
$.ajax({
	type : "POST",
	url : "ajax.php?session_id=[[SESSION_ID]]&nav=data&run=parameter_delete",
	data : "parameter_id=[[PARAMETER_ID]]&sure=true",
	success : function(data) 
	{
		close_ui_window_and_reload();
	}
});