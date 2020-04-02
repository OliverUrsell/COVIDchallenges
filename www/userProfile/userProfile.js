currentDiv = $(".selected");
$("#" + currentDiv.attr("divid")).show();

$(".progressBar").each(function(){
	$(this).after("<div class=\"col-2\">" + $(this).children(":first").attr("distance") + "/" + $(this).children(":first").attr("distanceTotal") + "km</div>")
});

$(".progressBarContents").each(function(){
	var distPercentage = $(this).attr("distance")/$(this).attr("distanceTotal");
	if(distPercentage > 1){
		$(this).animate({width: "100%", backgroundColor:'green'}, 4000);
	}else{
		$(this).animate({width: String(distPercentage*100) + "%"}, 4000);
	}
});

$(".category").click(function(){
	currentDiv.removeClass("selected");
	$("#" + currentDiv.attr("divid")).hide();
	$(this).addClass("selected")
	$("#" + $(this).attr("divid")).show();
	currentDiv = $(this);
});