var number_of_batches = $("#BaseBatchTestStartWindowNumberOfBatches").val();

if (number_of_batches == "")
{
	number_of_batches = 1;
}

$.ajax(
{
	type : "POST",
	url : "ajax.php?session_id=[[SESSION_ID]]&nav=base&run=batch_start_test_handler",
	data : 'number_of_batches='+number_of_batches,
	success : function(data) 
	{
		$("#BaseBatchTestStartWindow").dialog("close");
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