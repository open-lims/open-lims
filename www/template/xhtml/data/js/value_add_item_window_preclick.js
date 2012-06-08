$("#[[WINDOW_ID]]").dialog(
{
	autoOpen: false
});

base_dialog("POST", "ajax.php?session_id=[[SESSION_ID]]&nav=data&run=value_add_as_item_window", 'get_array=[[GET_ARRAY]]&type_array=[[TYPE_ARRAY]]&folder_id=[[FOLDER_ID]]', "[[CLICK_ID]]");