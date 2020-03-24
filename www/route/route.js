// Initialize and add the map
function initMap() {
  // The location of Uluru
  // The map, centered at Uluru
  var map = new google.maps.Map(
      document.getElementById('map'), {zoom: 4, center: uluru});
  // The marker, positioned at Uluru
  var marker = new google.maps.Marker({position: uluru, map: map});
}

function whichAnimationEvent(){
    var t;
    var el = document.createElement('fakeelement');
    var animations = {
      'animation':'animationend',
      'OTransition':'oAnimationEnd',
      'MozTransition':'animationmend',
      'WebkitTransition':'webkitAnimationEnd'
    }

    for(a in animations){
        if( el.style[a] !== undefined ){
            return animations[a];
        }
    }
}

var animationEnd = whichAnimationEvent();
document.getElementById("progressBarContents").addEventListener(animationEnd, () => {
  $("#letterValues").show("fast");
});