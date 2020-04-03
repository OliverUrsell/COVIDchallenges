<?php session_start();?>

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
        exit();
    }

    // This might be useful
    // UPDATE `tblmultipleusers` SET `UserDistanceTravelled`= `UserDistanceTravelled` + 9990 WHERE MultipleUserID = 2

	//Add $_POST['distanceUpdate']; to some kind of database value then redirect:
	// $newDistance = 24700;
	// $newDistance = 250000;

	$loggedIn = FALSE;
    if(isset($_SESSION['userID'])) {
        // User is logged in
        $sql = "SELECT COUNT(UserID) as Total FROM `tblmultipleusers` WHERE (UserID=". $conn->real_escape_string($_SESSION["userID"]) ." AND MultipleUserID=". $conn->real_escape_string($_POST["multipleUserID"]) .")";
        $result = $conn->query($sql);
        if($result->fetch_assoc()["Total"] == 1){
            // they are a member of this challenge
            $loggedIn = TRUE;
        }
    }

    echo $_SESSION["userID"];
    echo $_POST["multipleUserID"];

	if($loggedIn){
		$sql = "SELECT COUNT(UpdateIndex) as Total FROM `tblupdates` WHERE MultipleUserID=". $conn->real_escape_string($_POST["multipleUserID"]);
        $result = $conn->query($sql);
        $updateIndex = $result->fetch_assoc()["Total"];


		$sql = "INSERT INTO `tblupdates` (`MultipleUserID`, `UserID`, `UpdateIndex`, `UpdateDistance`) VALUES ('". $conn -> real_escape_string($_POST["multipleUserID"]) ."', '". $conn -> real_escape_string($_SESSION["userID"]) ."', '". $conn -> real_escape_string($updateIndex) ."', '". $conn -> real_escape_string($_POST["distanceUpdate"])*100 ."')";
		if ($conn->query($sql) === TRUE) {
		    echo "Record Inserted Successfully";

		    $sql = "UPDATE `tblmultipleusers` SET `UserDistanceTravelled`=UserDistanceTravelled + ". $conn -> real_escape_string($_POST["distanceUpdate"]*100) ." WHERE (MultipleUserID = ". $conn -> real_escape_string($_POST["multipleUserID"]) ." AND UserID = ". $conn -> real_escape_string($_SESSION["userID"]) .")";
			if ($conn->query($sql) === TRUE) {
			    echo "Record updated successfully";
			} else {
			    echo "Error updating record: " . $conn->error;
			    exit();
			}


		    $sql = "UPDATE tbljourneysusers SET DistanceTravelled = DistanceTravelled + ". $conn -> real_escape_string($_POST["distanceUpdate"]*100) ." WHERE `MultipleUserID` = ". $conn -> real_escape_string($_POST["multipleUserID"]);
			if ($conn->query($sql) === TRUE) {
			    echo "Record updated successfully";
			} else {
			    echo "Error updating record: " . $conn->error;
			    exit();
			}
		} else {
		    echo "Error inserting record: " . $conn->error;
		    exit();
		}

		
	}

	$conn->close();
	header('Location: route.php?journeyID='.htmlspecialchars($_POST['journeyID']).'&multipleUserID='.htmlspecialchars($_POST['multipleUserID']));
?>
</body>
</html>