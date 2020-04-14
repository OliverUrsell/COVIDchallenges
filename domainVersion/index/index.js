
transitionTime = 10000;

slides = ["index/route66.jpg", "index/route66.jpg", "index/route66.jpg", "index/route66.jpg", "index/route66.jpg"];

$(".contents").hide("slow");
$(".prev").hide();

var slideIndex = 0;
var timeout = setTimeout(function(){plusSlides(1);}, transitionTime);;
showSlides(slideIndex);

// Next/previous controls
function plusSlides(n) {
  showSlides(slideIndex += n);
}

// Thumbnail image controls
function currentSlide(n) {
  showSlides(slideIndex = n);
}

function showSlides(n) {
  clearTimeout(timeout);
  if (n > slides.length - 1) {
    slideIndex = 0;
  }else if (n < 0) {
    slideIndex = slides.length-1;
  }

  $(".prev").show();
  $(".next").show();
  if(slideIndex == 0){
    $(".prev").hide();
    timeout = setTimeout(function(){plusSlides(1);}, transitionTime);
  }else if(slideIndex == slides.length-1){
    $(".next").hide();
  }else{
    timeout = setTimeout(function(){plusSlides(1);}, transitionTime);
  }

  $(".contents").hide("slow");
  $(".bg").css("background-image", 'url("' + slides[slideIndex] + '")');
  $("[slideIndex="+slideIndex+"]").show("slow");
}