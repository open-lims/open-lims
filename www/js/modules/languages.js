
var upload_error_array_english = [9];
upload_error_array_english[0] = "";
upload_error_array_english[1] = "";
upload_error_array_english[2] = "A non-specific error occurs during upload!";
upload_error_array_english[3] = "A non-specific error occurs during upload!";
upload_error_array_english[4] = "This file is too large!";
upload_error_array_english[5] = "This file equals previous version or already exists!";
upload_error_array_english[6] = "You have exceeded your quota!";
upload_error_array_english[7] = "This file-type is forbidden!";
upload_error_array_english[8] = "Permission denied!";

var upload_status_array_english = [8];
upload_status_array_english[0] = "No File selected!";
upload_status_array_english[1] = "Checking for Errors...";
upload_status_array_english[2] = "An Error occurred. See the Log for details.";
upload_status_array_english[3] = "VAR Errors occurred. See the Log for details."
upload_status_array_english[4] = "The File was successfully uploaded.";
upload_status_array_english[5] = "All VAR Files were successfully uploaded.";
upload_status_array_english[6] = "VAR seconds remaining";
upload_status_array_english[7] = "1 second remaining";


function get_local_array(type)
{
	var local_language = "english"; //get & set the local language here
	switch(local_language)
	{
		case "english":
			switch(type)
		    {
			    case "upload_error":
			    	return upload_error_array_english;
			    	break;
			    case "upload_status":
			    	return upload_status_array_english;
			    	break;
		    }
			break;
	}
}
//THESE HAVE TO EXIST
var upload_error_array = get_local_array("upload_error");
var upload_status_array = get_local_array("upload_status");