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
	$mainUserLoggedIn = FALSE; // Set true after checking journeysusers table
    if(isset($_SESSION['userID'])) {
        // User is logged in
        $sql = "SELECT COUNT(UserID) as Total FROM `tblmultipleusers` WHERE (UserID=". $conn->real_escape_string($_SESSION["userID"]) ." AND MultipleUserID=". $conn->real_escape_string($_POST["multipleUserID"]) .")";
        $result = $conn->query($sql);
        if($result->fetch_assoc()["Total"] == 1){
            // they are a member of this challenge
            $loggedIn = TRUE;
        }
    }

    $sql = "SELECT MainUserID FROM tbljourneysusers WHERE MultipleUserID = ". $conn->real_escape_string($_POST["multipleUserID"]);
	$result = $conn->query($sql);
	if ($result->num_rows == 1) {
	    if($loggedIn){
	        // Figure out wether the main challenger is logged in
	        $journeysUsersRow = $result->fetch_assoc();
	        if($journeysUsersRow["MainUserID"] == $_SESSION["userID"]){
	            // Yes they are
	            $mainUserLoggedIn = TRUE;
	        }
	    }
	} elseif ($result->num_rows == 0) {
	    echo "No data for these users was found on this challenge, please return to the previous page and try the link again";
	    exit();
	} else {
	    echo "Duplicate ID found, ID:" . htmlspecialchars($_GET["multipleUserID"]) . ". Whoops, this one is on us.";
	    exit();
	}

	if($loggedIn && isset($_POST["remove"])){
		if($mainUserLoggedIn || $_SESSION["userID"] == $_POST["userID"]){
			$sql = "SELECT UpdateDistance FROM `tblupdates` WHERE MultipleUserID=". $conn->real_escape_string($_POST["multipleUserID"]) ." AND UpdateIndex=". $conn->real_escape_string($_POST["updateIndex"]);
	        $result = $conn->query($sql);
	        $distanceRow = $result->fetch_assoc();
			$distanceUpdate = $distanceRow["UpdateDistance"];


			$sql = "DELETE FROM `tblupdates` WHERE MultipleUserID=". $conn->real_escape_string($_POST["multipleUserID"]) ." AND UpdateIndex=". $conn->real_escape_string($_POST["updateIndex"]);
			if ($conn->query($sql) === TRUE) {
			    echo "Update record deleted Successfully";

			    $sql = "UPDATE `tblupdates` SET `UpdateIndex`=UpdateIndex - 1 WHERE (MultipleUserID=". $conn -> real_escape_string($_POST["multipleUserID"]) ." AND updateIndex > ". $conn->real_escape_string($_POST["updateIndex"]) .")";
			    if($conn->query($sql) === TRUE){
			    	echo "Update Indexes updated successfully";

			    	$sql = "UPDATE `tblmultipleusers` SET `UserDistanceTravelled`=UserDistanceTravelled - ". $conn -> real_escape_string($_POST["distanceUpdate"]*100) ." WHERE (MultipleUserID = ". $conn -> real_escape_string($_POST["multipleUserID"]) ." AND UserID = ". $conn -> real_escape_string($_POST["userID"]) .")";

					if ($conn->query($sql) === TRUE) {
					    echo "Multiple users updated successfully";

					    $sql = "UPDATE tbljourneysusers SET DistanceTravelled = DistanceTravelled - ". $conn -> real_escape_string($_POST["distanceUpdate"]*100) ." WHERE `MultipleUserID` = ". $conn -> real_escape_string($_POST["multipleUserID"]);
						if ($conn->query($sql) === TRUE) {
						    echo "Journeys users updated successfully";
						} else {
						    echo "Error updating record: " . $conn->error;
						    exit();
						}

					} else {
					    echo "Error updating record: " . $conn->error;
					    exit();
					}
			    }

			    
			} else {
			    echo "Error inserting record: " . $conn->error;
			    exit();
			}
		}
	}

	$conn->close();
	header('Location: route.php?journeyID='.htmlspecialchars($_POST['journeyID']).'&multipleUserID='.htmlspecialchars($_POST['multipleUserID']));
?>
</body>
</html>