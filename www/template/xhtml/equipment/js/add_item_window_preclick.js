$("#[[WINDOW_ID]]").dialog(
{
	autoOpen: false
});

base_dialog("POST", "ajax.php?session_id=[[SESSION_ID]]&nav=equipment&run=equipment_add_as_item_window", 'get_array=[[GET_ARRAY]]&type_array=[[TYPE_ARRAY]]&category_array=[[CATEGORY_ARRAY]]', "[[CLICK_ID]]");