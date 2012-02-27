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
						var url = "";
						
						if (modules[i][1] == "update")
						{
							url = "ajax.php?run=update";
						}
						else
						{
							url = "ajax.php?run=install";
						}
						
						$.ajax(
						{
							type: "POST",
							url: url,
							data: 'module='+modules[i][0],
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