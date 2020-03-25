<html>
<body>

You travelled: <?php echo  $_POST["JourneyID"]?>Km
<?php
	//Add $_POST['distanceUpdate']; to some kind of database value then redirect:
	header('Location: ../index.php?JourneyID='.$_POST['JourneyID'].'&UserID='.$_POST['UserID']);
?>
</body>
</html>