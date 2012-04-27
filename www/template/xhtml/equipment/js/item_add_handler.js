var type_id = $("#[[CONTAINER_VALUE_SELECT_ID]]").val();
			
$.ajax(
{
	type : "POST",
	url : "ajax.php?session_id=[[SESSION_ID]]&nav=equipment&run=equipment_item_add_action",
	data : 'get_array=[[GET_ARRAY]]&type_id='+type_id,
	success : function(data) 
	{
		$("[[CONTAINER_ID]]").dialog("close");
		if ((data + '').indexOf("EXCEPTION:",0) == 0)
		{
			var exception_message = data.replace("EXCEPTION: ","");
			ErrorDialog("Error", exception_message);
		}
		else
		{
			reload_menu();
		}
	}
});