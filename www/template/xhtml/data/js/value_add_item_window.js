var json = value_handler.get_json();

if (json !== false)
{	
	$.ajax({
		type : "POST",
		url : "ajax.php?session_id=[[SESSION_ID]]&nav=data&run=value_add_as_item",
		data : 'folder_id=[[FOLDER_ID]]&type_id=[[TYPE_ID]]&value_array='+json+'&get_array=[[GET_ARRAY]]',
		success : function(data)
		{
			$("[[CONTAINER_ID]]").dialog("close");
			
			if (data == "1")
			{
				reload_menu();
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
}