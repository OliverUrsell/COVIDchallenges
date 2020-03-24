<?php
	header("Content-type: text/css; charset: UTF-8");
	define("distanceCovered",100);
    define("distanceTotal",200);
?>

body{
	background-color: black;
	color:white;
	overflow-x: none;
}

#navbar{
	background-color: red;
	color:white;
	height:10vh;
}

#map{
	width:100%;
	height: min(50vw, 50vh);
}

#toFromDisplay{
	background-color: grey;
	padding:1em;
	font-size: 200%;
	border-left: solid red 2vw;
}

#progressBar{
	height: 1.5em;
	background-color: red;
	border: solid black 0.1em;
	padding:0;
}

@keyframes load {
  0% {width:0%;}
  95% {width:<?php echo (distanceCovered/distanceTotal)*100 + 0.2;?>%;}
  100%{width:<?php echo (distanceCovered/distanceTotal)*100;?>%;}
}

#progressBarContents{
	height:1.3em;
	width:0%;
	background-color: blue;
	animation: load 4s forwards;
}

#letterValues{
	display:none;
}