base_form_init = function()
{
	$(".Form input").each(function()
	{
		$(this).focus(function()
		{
			 $(this).addClass("FormFocused");
		});
		
		$(this).blur(function()
		{
			 $(this).removeClass("FormFocused");
		});
	});	
}


$(document).ready(function()
{
	base_form_init();
});