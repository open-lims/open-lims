var json = '{';
$('#DataBrowserLoadedAjaxContent').find('input').each(function(){
	if($(this).attr('type') != 'hidden') 
	{
		if($(this).is(':checkbox:checked'))
		{
			json += '\"'+$(this).attr('name')+'\":\"'+$(this).attr('value')+'\",';
		}
		else
		{
			json += '\"'+$(this).attr('name')+'\":\"0\",';
		}
	}
});
json = json.substr(0,json.length-1); //cut last ,
json += '}';

$.ajax({
	type : "POST",
	url : "ajax.php?nav=data&session_id=[[SESSION_ID]]&run=folder_permission",
	data : "permissions="+json+"&folder_id=[[FOLDER_ID]]",
	success : function(data) 
	{
		close_ui_window_and_reload();
	}
});