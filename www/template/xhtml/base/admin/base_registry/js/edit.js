var value = $("#BaseAdminRegistryEditValue").val();
var id = $("#BaseAdminRegistryEditId").val();

$.ajax(
{
	type : "POST",
	url : "ajax.php?session_id="+get_array['session_id']+"&nav=base&run=admin_registry_edit_handler",
	data : "value="+value+"&id="+id,
	success : function(data) 
	{
		$("#BaseAdminRegistryEditWindow").dialog("close");
		if ((data + '').indexOf("EXCEPTION:",0) == 0)
		{
			var exception_message = data.replace("EXCEPTION: ","");
			ErrorDialog("Error", exception_message);
		}
		else
		{
			list.reload();
		}
	}
});