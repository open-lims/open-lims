var json = '{';

$('#DataBrowserLoadedAjaxContent').find('input').each(function(){
	if($(this).attr('type') != 'hidden') 
	{
		if($(this).is(':checkbox:checked'))
		{
			json += '"'+$(this).attr('name')+'":"'+$(this).attr('value')+'",';
		}
		else
		{
			json += '"'+$(this).attr('name')+'":"0",';
		}
	}
});

json = json.substr(0,json.length-1); //cut last ,
json += '}';

$.ajax({
	type : "POST",
	url : "ajax.php?session_id=[[SESSION_ID]]&nav=data&run=file_permission",
	data : "file_id=[[FILE_ID]]&permissions="+json,
	success : function(data) 
	{
		close_ui_window_and_reload();
	}
});