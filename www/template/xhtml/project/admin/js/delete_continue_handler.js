$.ajax(
{
	type : "POST",
	url : "ajax.php?session_id=[[SESSION_ID]]&nav=project&run=delete_handler",
	data : 'get_array=[[GET_ARRAY]]',
	beforeSend: function()
	{
		$("#ProjectDeleteWindow").dialog("close");
		$("#ProjectDeleteProceed").html("<div id='AssistantLoading'><img src='images/animations/loading_circle_small.gif' alt='Loading...' />Please wait while deleting</div>");
		$.blockUI({ message: $('#ProjectDeleteProceed'), css: { width: '275px' } }); 
		$('.blockUI.blockMsg').center();
	},
	success : function(data) 
	{
		if ((data + '').indexOf("EXCEPTION:",0) == 0)
		{
			var exception_message = data.replace("EXCEPTION: ","");
			$.unblockUI();
			ErrorDialog("Error", exception_message);
		}
		else
		{
			$.unblockUI();
			if ([[PROJECT_DELETED]] == true)
			{
				window.setTimeout('window.location = "index.php?username=[[USERNAME]]&session_id=[[SESSION_ID]]&nav=project"',500);
			}
			else
			{
				reload_admin_menu();
			}
		}
	}
});