
function tooltip(element_id, message)
{
	var offsetX = 20;
	var offsetY = 10;
	
	$("#"+element_id).hover(function(e){
		$("<div id='tooltip'>"+message+"</div>")
			.css("position","absolute")
			.css("background-color","white")
			.css("border","solid black 1px")
			.css("padding","2px 4px 2px 4px")
			.css({"font-family":"arial","font-size":"12px"})
			.css("top", e.pageY + offsetY)
			.css("left", e.pageX + offsetX)
			.hide()
			.appendTo('body')
			.fadeIn(300);
	},function(){
		$('#tooltip').remove();
	});
	
	$("#"+element_id).mousemove(function(e) {
		$("#tooltip").css("top", e.pageY + offsetY).css("left", e.pageX + offsetX);
	});
}
