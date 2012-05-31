//parse all values and their respective names to a json array
var value_id = $('#DataBrowserAddValue option:selected').val();
var json = '{';
$('#AjaxLoadedContent').find('input').each(function(){
	var name = $(this).attr('name');
	var value = $(this).val();
	json += '\"'+name+'\":\"'+value+'\",';
});	
$('#AjaxLoadedContent').find('select').each(function(){
	var name = $(this).attr('name');
	var value = $(this).children('option:selected').val();
	json += '\"'+name+'\":\"'+value+'\",';
});	
$('#AjaxLoadedContent').find('textarea').each(function(){
	var name = $(this).attr('name');
	var value = $(this).val();
	json += '\"'+name+'\":\"'+value+'\",';
});	
json = json.substr(0,json.length-1);
json += '}';

//hand the array to this same function (third call)
$.ajax({
	type : "POST",
	url : "ajax.php?session_id=[[SESSION_ID]]&nav=data&run=value_add",
	data : "folder_id=[[FOLDER_ID]]&type_id="+value_id+"&value_array="+json,
	success : function(data)
	{
		close_ui_window_and_reload();
	}
});