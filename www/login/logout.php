<?php session_start();?>
<html>
<body>
<?php
	unset($_SESSION['userID']);
	echo "If you are not redirected please <a href=\"login.php\">click here</a>.";
	header("Location: ../login/login.php");
?>
</body>
</html>