<html>
<body>

You travelled: <?php echo  $_POST["distanceUpdate"]?>Km
<?php
	//Add $_POST["distanceUpdate"]; to some kind of database value then redirect:
	header('Location: ../index.php');
?>
</body>
</html>