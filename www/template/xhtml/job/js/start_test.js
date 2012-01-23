var number_of_jobs = $("#JobTestStartWindowNumberOfJobs").val();

if (number_of_jobs == "")
{
	number_of_jobs = 1;
}

$.ajax(
{
	type : "POST",
	url : "ajax.php?session_id=[[SESSION_ID]]&nav=job&run=start_test_handler",
	data : 'number_of_jobs='+number_of_jobs,
	success : function(data) 
	{
		$("#JobTestStartWindow").dialog("close");
		if ((data + '').indexOf("EXCEPTION:",0) == 0)
		{
			var exception_message = data.replace("EXCEPTION: ","");
			ErrorDialog("Error", exception_message);
		}
	}
});