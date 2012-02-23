$(document).ready(function()
{
	$("#InstallStart").click(function()
	{
		$.ajax(
		{
			type: "POST",
			url: "ajax.php?run=get_modules",
			data: '',
			success: function(data)
			{	
				if (data)
				{
					var modules = $.parseJSON(data);
					
					for(var i = 0; i<=(modules.length-1); i++)
					{
						$.ajax(
						{
							type: "POST",
							url: "ajax.php?run=install",
							data: 'module='+modules[i],
							async: false,
							success: function(data)
							{
								
							}
						});
					}
				}
			}
		});
	});
});