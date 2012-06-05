$.ajax(
{
	type : "POST",
	url : "ajax.php?session_id=[[SESSION_ID]]&nav=sample&run=delete_handler",
	data : 'get_array=[[GET_ARRAY]]',
	beforeSend: function()
	{
		$("#SampleDeleteWindow").dialog("close");
		$("#SampleDeleteProceed").html("<div id='AssistantLoading'><img src='images/animations/loading_circle_small.gif' alt='Loading...' />Please wait while deleting</div>");
		$.blockUI({ message: $('#SampleDeleteProceed'), css: { width: '275px' } }); 
		$('.blockUI.blockMsg').center();
	},
	success : function(data) 
	{
		if (data == "1")
		{
			$.unblockUI();
			window.setTimeout('window.location = "index.php?username=[[USERNAME]]&session_id=[[SESSION_ID]]&nav=sample"',500);
		}
		else
		{						
			if ((data + '').indexOf("EXCEPTION",0) == 0)
			{
				var exception_message = data.replace("EXCEPTION: ","");
				$.unblockUI();
				ErrorDialog("Error", exception_message);
				return false;
			}
			else
			{
				$.unblockUI();
				ErrorDialog("Error", "An error occured");
				return false;
			}
		}
	}
});