<html>
<body>
<?php
	$servername = "localhost";
    $username = "Ollie";
    $password = "databasepassword";
    $dbname = "main";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

	//Add $_POST['distanceUpdate']; to some kind of database value then redirect:
	$distanceCovered = htmlspecialchars($_POST["distanceCovered"]);
	$distanceUpdate = htmlspecialchars($_POST["distanceUpdate"]);
	$newDistance = $distanceCovered/10 + $distanceUpdate*100;
	// $newDistance = 24700;
	// $newDistance = 250000;
	$sql = "UPDATE tbljourneysusers SET DistanceTravelled = ". $newDistance ." WHERE JourneyID = ". $conn -> real_escape_string($_POST["journeyID"]) ." AND `UserID` = ". $conn -> real_escape_string($_POST["userID"]);
	if ($conn->query($sql) === TRUE) {
	    echo "Record updated successfully";
	} else {
	    echo "Error updating record: " . $conn->error;
	}

	$conn->close();
	header('Location: route.php?JourneyID='.htmlspecialchars($_POST['journeyID']).'&UserID='.htmlspecialchars($_POST['userID']));
?>
</body>
</html>