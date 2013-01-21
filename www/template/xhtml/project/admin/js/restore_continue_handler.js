$.ajax(
{
	type : "POST",
	url : "ajax.php?session_id=[[SESSION_ID]]&nav=project&run=restore_handler",
	data : 'get_array=[[GET_ARRAY]]',
	success : function(data) 
	{
		$("#ProjectRestoreWindow").dialog("close");
		
		if (data == "1")
		{
			reload_admin_menu();
		}
		else
		{						
			if ((data + '').indexOf("EXCEPTION",0) == 0)
			{
				var exception_message = data.replace("EXCEPTION: ","");
				ErrorDialog("Error", exception_message);
				return false;
			}
			else
			{
				ErrorDialog("Error", "An error occured");
				return false;
			}
		}
	}
});