uploader.start_upload();
fadeout_ui_window();
function check_if_uploader_finished()
{
	if(uploader.is_finished() == true)
	{
		close_ui_window_and_reload();
	}
	else
	{
		setTimeout(check_if_uploader_finished , 200);
	}
}
check_if_uploader_finished();