$.ajaxSetup({cache: false});

$.fn.center = function () {
	this.css("position","absolute");
	this.css("top", ( $(window).height() - this.height() ) / 2+$(window).scrollTop() + "px"); 
	this.css("left", ( $(window).width() - this.width() ) / 2 +$(window).scrollLeft() + "px"); 
	
	return this;
}

$(document).ready(function()
{
	$("#InstallStart").click(function()
	{
		$("#InstallStart").attr("disabled", "true");
		
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
						
						var status_td = $("#InstallStatus"+modules[i][0]);
						
						$.ajax(
						{
							type: "POST",
							url: url,
							data: 'module='+modules[i][0],
							async: false,
							beforeSend: function()
							{
								status_td.html("<img src='images/circle.gif' alt='' />");
							},
							success: function(data)
							{
								if((data == "1") || (data == "-1"))
								{
									$.ajax(
									{
										type: "POST",
										url: "ajax.php?run=get_table_row",
										data: 'module='+modules[i][0],
										async: false,
										success: function(data)
										{
											status_td.parent().replaceWith(data);
										}
									});
								}
								else
								{
									status_td.html("<img src='images/failed.png' alt='' />");
								}
							}
						});
					}
				} 
			}
		});
	});
});