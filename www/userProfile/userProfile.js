currentDiv = $(".selected");
$("#" + currentDiv.attr("divid")).show();

var latLongCount = 2;

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

function challengeFormValidity(){
	var latRegex = new RegExp("^[-+]?([1-8]?\\d(\\.\\d+)?|90(\\.0+)?)$", 'g');
  	var longRegex = new RegExp("^[-+]?(180(\\.0+)?|((1[0-7]\\d)|([1-9]?\\d))(\\.\\d+)?)$", 'g');
  	
  	alert(latRegex.test($(".lat")[0].value));
  	$(".lat").each(function(index, value){

  		setTimeout(function(){
		    if(latRegex.test(value.value)){
		      	alert("Here1");
		    }else{
		    	alert("Here2");
		    }
		}, 0);


  		if(latRegex.test(value.value)){
  			alert(index)
  			value.setCustomValidity("");
  		}else{
  			alert(index);
  			alert(latRegex.test(value.value));
  			value.setCustomValidity("That is not a valid Latitude");
  		}
  	});

  	// setTimeout(function(){
  	// 	$(".lat").each(function(index, value){
  	// 		value.setCustomValidity(!latRegex.test($(".lat")[0].value) ? "That is not a valid latitude" : "");
  	// 	});

  	// 	$(".long").each(function(index, value){
  	// 		value.setCustomValidity(!longRegex.test($(".long")[0].value) ? "That is not a valid longitude" : "");
  	// 	});
  	// }, 0);
}

$("#addLatLong").click(function(){
	if(latLongCount < 8){
		latLongCount++;
		$("#latLongCount").attr("value", latLongCount);
		$("#latLongButtons").before("<div class=\"row\">"+
                                "<div class=\"col\">"+
                                "    Latitude:"+
                                "    <input type=\"text\" class=\"form-control\" placeholder=\"Latitude " + latLongCount + "\" pattern=\"^[-+]?([1-8]?\d(\.\d+)?|90(\.0+)?)$\">"+
                                "</div>"+
                                "<div class=\"col\">"+
                                "    Longitude:"+
                                "    <input type=\"text\" class=\"form-control\" placeholder=\"Longitude " + latLongCount + "\" pattern=\"^[-+]?(180(\.0+)?|((1[0-7]\d)|([1-9]?\d))(\.\d+)?)$\">"+
                                "</div>"+
                            "</div>"+
                            "<br>");
	}
});

$("#removeLatLong").click(function(){
	if(latLongCount > 2){
		$(this).parent().prev().detach();
		$(this).parent().prev().detach();
		latLongCount--;
		$("#latLongCount").attr("value", latLongCount);
	}
});