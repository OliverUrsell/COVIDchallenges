var directionDisplay;
var map;
var infowindow;
var polyline = null;
var gmarkers = [];
var distance = (204+20+13)*1000;

function initMap() {
  var directionsService = new google.maps.DirectionsService();
  infowindow = new google.maps.InfoWindow();
  var directionsService = new google.maps.DirectionsService;
  var directionsDisplay = new google.maps.DirectionsRenderer;
  map = new google.maps.Map(document.getElementById('map'), {
    zoom: 7,
    center: {
      lat: 41.85,
      lng: -87.65
    }
  });
  polyline = new google.maps.Polyline({
    path: [],
    strokeColor: '#FF0000',
    strokeWeight: 3
  });


  directionsDisplay.setMap(map);
  calculateAndDisplayRoute(directionsService, directionsDisplay);
}

function calculateAndDisplayRoute(directionsService, directionsDisplay) {
  directionsService.route({
    origin: {query: "John O'Groats Harbour"},
    // destination: {lat: 50.066093, lng: -5.715103},
    destination: {query: "Land's End Signpost"},
    travelMode: 'BICYCLING'
  }, function(response, status) {
    if (status == google.maps.DirectionsStatus.OK) {
      polyline.setPath([]);
      var bounds = new google.maps.LatLngBounds();
      startLocation = new Object();
      endLocation = new Object();
      directionsDisplay.setDirections(response);
      var route = response.routes[0];
      // For each route, display summary information.
      var path = response.routes[0].overview_path;
      var legs = response.routes[0].legs;
      for (i = 0; i < legs.length; i++) {
        if (i == 0) {
          startLocation.latlng = legs[i].start_location;
          startLocation.address = legs[i].start_address;
          // marker = google.maps.Marker({map:map,position: startLocation.latlng});
          marker = createMarker(legs[i].start_location, "start", legs[i].start_address, "green");
        }
        endLocation.latlng = legs[i].end_location;
        endLocation.address = legs[i].end_address;
        var steps = legs[i].steps;
        for (j = 0; j < steps.length; j++) {
          var nextSegment = steps[j].path;
          for (k = 0; k < nextSegment.length; k++) {
            polyline.getPath().push(nextSegment[k]);
            bounds.extend(nextSegment[k]);
          }
        }
      }

      polyline.setMap(map);
      for (var i = 0; i < gmarkers.length; i++) {
        gmarkers[i].setMap(null);
      }
      gmarkers = [];
      var point = GetPointsAtDistance(polyline, distance)[0];
      var marker = new google.maps.Marker({
        map: map,
        position: point,
        title: distance/1000 + " Kilometer(s)"
      });
      marker.addListener('click', openInfoWindow);

    } else {
      alert("directions response " + status);
    }
  });


  /*  function(response, status) {
      if (status === 'OK') {
        directionsDisplay.setDirections(response);
      } else {
        window.alert('Directions request failed due to ' + status);
      }
    }); */
}

function createMarker(latlng, label, html, color) {
  // alert("createMarker("+latlng+","+label+","+html+","+color+")");
  var contentString = '<b>' + label + '</b><br>' + html;
  var marker = new google.maps.Marker({
    position: latlng,
    // draggable: true, 
    map: map,
    icon: getMarkerImage(color),
    title: label,
    zIndex: Math.round(latlng.lat() * -100000) << 5
  });
  marker.myname = label;
  gmarkers.push(marker);

  google.maps.event.addListener(marker, 'click', function() {
    infowindow.setContent(contentString);
    infowindow.open(map, marker);
  });
  return marker;
}
var icons = new Array();
icons["red"] = {
  url: "http://maps.google.com/mapfiles/ms/micons/red.png"
};

function getMarkerImage(iconColor) {
  if ((typeof(iconColor) == "undefined") || (iconColor == null)) {
    iconColor = "red";
  }
  if (!icons[iconColor]) {
    icons[iconColor] = {
      url: "http://maps.google.com/mapfiles/ms/micons/" + iconColor + ".png"
    };
  }
  return icons[iconColor];

}

function openInfoWindow() {
  var contentString = this.getTitle() + "<br>" + this.getPosition().toUrlValue(6);
  infowindow.setContent(contentString);
  infowindow.open(map, this);
}

// === A method which returns an array of GLatLngs of points a given interval along the path ===
function GetPointsAtDistance(poly, metres) {
  var next = metres;
  var points = [];
  // some awkward special cases
  if (metres <= 0) return points;
  var dist = 0;
  var olddist = 0;
  for (var i = 1;
    (i < poly.getPath().getLength()); i++) {
    olddist = dist;
    dist += google.maps.geometry.spherical.computeDistanceBetween(poly.getPath().getAt(i), poly.getPath().getAt(i - 1));
    while (dist > next) {
      var p1 = poly.getPath().getAt(i - 1);
      var p2 = poly.getPath().getAt(i);
      var m = (next - olddist) / (dist - olddist);
      points.push(new google.maps.LatLng(p1.lat() + (p2.lat() - p1.lat()) * m, p1.lng() + (p2.lng() - p1.lng()) * m));
      next += metres;
    }
  }
  return points;
}