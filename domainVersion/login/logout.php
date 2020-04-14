<?php session_start();?>
<html>
<body>
<?php
	unset($_SESSION['userID']);
	echo "If you are not redirected please <a href=\"login.php\">click here</a>.";
	// header("location: /login/login.php");
	$URL="../login/login.php";
	echo "<script type='text/javascript'>document.location.href='{$URL}';</script>";
	echo '<META HTTP-EQUIV="refresh" content="0;URL=' . $URL . '">';
	echo "Hello world";
	exit();
?>
</body>
</html>