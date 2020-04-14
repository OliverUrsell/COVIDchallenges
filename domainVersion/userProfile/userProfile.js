var travelMode = "WALKING";
var directionsService;
var map;
var infowindow;
var polyline = null;
var gmarkers = [];

function initMap() {
  directionsService = new google.maps.DirectionsService();
  polyline = new google.maps.Polyline({
    path: [],
    strokeColor: '#FF0000',
    strokeWeight: 3
  });
}

function calculateRoute() {

  var latLongs = [];
  for (var i = 1; i <= $("[name='latLongCount']").val(); i++) {
  	newLatLong = [];
  	newLatLong.push($("[name='lat"+ i +"']").val());
  	newLatLong.push($("[name='long"+ i +"']").val());
  	latLongs.push(newLatLong);
  }

  var _origin = new google.maps.LatLng(latLongs[0][0], latLongs[0][1]);
  var _destination = new google.maps.LatLng(latLongs[latLongs.length-1][0], latLongs[latLongs.length-1][1]);
  latLongs.splice(0, 1);
  latLongs.splice(latLongs.length-1, 1);

  // Hide failure message to make it pop again
  $("#incorrectMessage").hide("fast");

  //Create waypoints
  waypointLatLongs = []
  latLongs.forEach(function(item, index){
    waypointLatLongs.push({
      location: new google.maps.LatLng(item[0], item[1]),
      stopover: false
    });
  });

  directionsService.route({
    origin: _origin,
    destination: _destination,
    travelMode: travelMode,
    waypoints: waypointLatLongs
  }, function(response, status) {
    if (status == google.maps.DirectionsStatus.OK) {
      polyline.setPath([]);
      var bounds = new google.maps.LatLngBounds();
      startLocation = new Object();
      endLocation = new Object();
      var route = response.routes[0];
      // For each route, display summary information.
      var path = response.routes[0].overview_path;
      var legs = response.routes[0].legs;
      for (i = 0; i < legs.length; i++) {
        var steps = legs[i].steps;
        for (j = 0; j < steps.length; j++) {
          var nextSegment = steps[j].path;
          for (k = 0; k < nextSegment.length; k++) {
            polyline.getPath().push(nextSegment[k]);
            bounds.extend(nextSegment[k]);
          }
        }
      }
      var distanceTotal = google.maps.geometry.spherical.computeLength(polyline.getPath().getArray());
      distanceTotal = Math.round(distanceTotal/10);
      $("#distanceTotal").attr("value", distanceTotal);
      var formToSubmit = document.getElementById("createChallengeForm");
      if(formToSubmit.checkValidity()){
      	formToSubmit.submit();
      }
    } else {
      // alert("directions response " + status);
      $("#incorrectMessage").show("fast");
    }
  });
  return false;
}
 
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

$("#addLatLong").click(function(){
	if(latLongCount < 8){
		latLongCount++;
		$("#latLongCount").attr("value", latLongCount);
		$("#latLongButtons").before("<div class=\"row\">"+
                                "<div class=\"col\">"+
                                "    Latitude:"+
                                "    <input name=\"lat"+ latLongCount +"\" type=\"text\" class=\"form-control\" placeholder=\"Latitude " + latLongCount + "\" required>"+
                                "</div>"+
                                "<div class=\"col\">"+
                                "    Longitude:"+
                                "    <input name=\"long"+ latLongCount +"\" type=\"text\" class=\"form-control\" placeholder=\"Longitude " + latLongCount + "\" required>"+
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