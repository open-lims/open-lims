function is_image(filename) 
{
	var allowed_image_types = ["jpg", "jpeg", "bmp", "png", "tiff", "tif", "gif"];
	var split = filename.split(".");
	var filetype = split[split.length-1];
	if($.inArray(filetype, allowed_image_types) != -1)
	{
		return true;
	}
	return false;
}