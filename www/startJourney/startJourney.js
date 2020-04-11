var directionsService;
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

  var travelModeSelect = document.getElementById("travelModeSelect");
  var travelMode = null;
  switch(travelModeSelect.options[travelModeSelect.selectedIndex].value){
      case "Cycle":
          var travelMode = "BICYCLING";
          break;
      case "Row":
          var travelMode = "WALKING";
          break;
      case "Run":
          var travelMode = "WALKING";
          break;
      default:
          var travelMode = "WALKING";
          break;
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
      alert(distanceTotal);
      $("#distanceTotal").attr("value", distanceTotal);
      var formToSubmit = document.getElementById("startJourneyForm");
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