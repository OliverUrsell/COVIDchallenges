<?php session_start();?>

<!DOCTYPE HTML>
<html lang="en">
<head>
	<meta charset="utf-8"> 
	<title>Login</title>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
	<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
	<link rel="stylesheet" href="navbar/navbar.css">
	<link rel="stylesheet" href="index/index.css">
</head>
<body>
	<div class="bg"></div>
	<?php include 'navbar/navbar.php';?>

	<div slideIndex="0" class="contents">
		<h1>HELP</h1>
		Charities<br>
		Physical Fitness<br>
		Your Immune System<br>
		Mental Health<br>
		Wellbeing<br>

		<h1>DEFEAT COVID-19</h1>
		<small>Image courtesy: Sam Topping https://creativecommons.org/licenses/by/2.0/</small>
	</div>

	<div slideIndex="1" class="contents">
		<h2>Challenge yourself</h2>
		Run Route 66<br>
		Cycle from Brisbane to Perth<br>
		Play table tennis until the ball's covered the length of Africa<br>
		Or Anything you can think of...<br>
		<h2>Covid challenges will track it<br>all while you isolate at home</h2>
	</div>

	<div slideIndex="2" class="contents">
		<h2>Collaborate and Compete with family and friends</h2>
		Add anyone to your challenges by providing a link and a password<br>
		Work together to achieve a common goal<br>
		Compete to see who can go the furthest before you finish!<br>
		<h2>Improve your social wellbeing and achieve something together</h2>
	</div>

	<div slideIndex="3" class="contents">
		<h2>Raise money for charity</h2>
		The cancellation of charity events around the world<br>
		means an expected loss upwards of 
		<a href="https://www.civilsociety.co.uk/news/charities-face-closure-as-sector-set-to-lose-4bn-over-12-weeks.html">£4,300,000,000</a> (£4.3 billion) in 12 weeks<br><br>
		<h2>Easily add a link to a charity donation page.<br>
		To help someone or something you think is important</h2>
	</div>

	<div slideIndex="4" class="contents">
		<h2>Get Started</h2><br>
		<a href="login/login.php"><button class="btn btn-success">Login</button></a><br>
		or<br>
		<a href="register/register.php"><button class="btn btn-success">Register</button></a><br>
		<h2>Go do something brilliant!</h2>
	</div>

	<!-- Next and previous buttons -->
	<a class="prev" onclick="plusSlides(-1)">&#10094;</a>
	<a class="next" onclick="plusSlides(1)">&#10095;</a>

	<script src="index/index.js"></script>

</body>
</html>