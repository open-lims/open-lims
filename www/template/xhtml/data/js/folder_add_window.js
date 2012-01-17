var new_name = $('#NewFolderName').val();

$.ajax({
	type : "POST",
	url : "ajax.php?nav=data&session_id=[[SESSION_ID]]&run=folder_add",
	data : "folder_id=[[FOLDER_ID]]&folder_name="+new_name,
	success : function(data) 
	{
		close_ui_window_and_reload();
	}
});