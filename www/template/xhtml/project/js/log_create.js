var comment = $("#ProjectLogCreateWindowReason").val();
			
$.ajax(
{
	type : "POST",
	url : "ajax.php?session_id=[[SESSION_ID]]&nav=project&run=log_create_handler",
	data : 'get_array=[[GET_ARRAY]]&comment='+comment,
	success : function(data) 
	{
		$("#ProjectLogCreateWindow").dialog("close");
		if ((data + '').indexOf("EXCEPTION:",0) == 0)
		{
			var exception_message = data.replace("EXCEPTION: ","");
			ErrorDialog("Error", exception_message);
		}
		else
		{
			if ($("#ProjectLogCreateSuccessfulWindow").length > 0)
			{
				$("#ProjectLogCreateSuccessfulWindow").dialog(
				{
					buttons: 
					{
						OK: function() {
							$( this ).dialog( "close" );
						}
					}
				});
			}
		}
	}
});