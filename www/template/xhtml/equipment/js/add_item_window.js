var type_id = $("#[[CONTAINER_VALUE_SELECT_ID]]").val();
			

if ((type_id != parseInt(type_id)) || (type_id === undefined))
{
	$("#EquipmentAddWindowError").html("You have to select an entry!");
}
else
{
	$("#EquipmentAddWindowError").html("");
	
	$.ajax(
	{
		type : "POST",
		url : "ajax.php?session_id=[[SESSION_ID]]&nav=equipment&run=equipment_add_as_item",
		data : 'get_array=[[GET_ARRAY]]&type_id='+type_id,
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