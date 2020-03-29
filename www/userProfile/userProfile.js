currentDiv = $(".selected");
$("#" + currentDiv.attr("divid")).show();

$(".category").click(function(){
	currentDiv.removeClass("selected");
	$("#" + currentDiv.attr("divid")).hide();
	$(this).addClass("selected")
	$("#" + $(this).attr("divid")).show();
	currentDiv = $(this);
});