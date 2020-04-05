<?php session_start();?>

<!DOCTYPE HTML>
<html lang="en">
    <head>
        <meta charset="utf-8"> 
        <title>Start a new Challenge?</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
        <link rel="stylesheet" href="shareStartJourney.css">
        <script src="../jquery.min.js"></script>
        <script   src="https://code.jquery.com/color/jquery.color-2.1.2.min.js"   integrity="sha256-H28SdxWrZ387Ldn0qogCzFiUDDxfPiNIyJX7BECQkDE="   crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    </head>
<body>
		<?php
			include_once("../navbar/navbar.php");

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

		    if(!$navbarLoggedIn){
		    	echo "<div id=\"contents\">You need to log in / register before you can start this challenge!</div>";
		    	exit();
		    }

		    if(isset($_GET['multipleUserID'])){
		    	if($_GET['multipleUserID'] != ""){
			    	$sql = "SELECT * FROM tbljourneys WHERE JourneyID = " . $conn -> real_escape_string($_GET["journeyID"]);
	                $result = $conn->query($sql);
	                if ($result->num_rows == 0){
	                    echo "<div id=\"contents\">Challenge not found, please check the link has been copied correctly</div>";
	                    exit();
	                } elseif ($result->num_rows == 1) {
	                    // set values for journey
	                    $journeysRow = $result->fetch_assoc();
	                    $displayName = htmlspecialchars($journeysRow['DisplayName']);

	                    echo "<br>
						<div id=\"contents\" class=\"container\">
							<div class=\"row\">
								<div class=\"col-12\">
									Start the new challenge ". $displayName ." as ". $row["DisplayName"] ."?
								</div>
							</div>
							<br>
							<div class=\"row\">
								<div class=\"col\">
									<form action=\"../startJourney/startJourney.php\" method=\"post\">
										<input name=\"journeyID\" type=\"hidden\" value=\"". $_GET["journeyID"] ."\">
										<input class=\"btn btn-success\" type=\"submit\" value=\"Yes\">
									</form>
									<a href=\"../login/login.php\"><button class=\"btn btn-danger\">No</button></a>
								</div>
							</div>
						</div>";
	                } else {
	                    echo "<div id=\"contents\">Duplicate ID found, ID:" . htmlspecialchars($_GET["multipleUserID"]) . ". Whoops, this one is on us.</div>";
	                    exit();
	                }
	            }else{
	            	echo "<div id=\"contents\">No challenge was specified, please check the link you followed was copied correctly</div>";
		    		exit();
	            }
		    }else{
		    	echo "<div id=\"contents\">No challenge was specified, please check the link you followed was copied correctly</div>";
		    	exit();
		    }

		    // This might be useful
		    // UPDATE `tblmultipleusers` SET `UserDistanceTravelled`= `UserDistanceTravelled` + 9990 WHERE MultipleUserID = 2

			//Add $_POST['distanceUpdate']; to some kind of database value then redirect:
			// $newDistance = 24700;
			// $newDistance = 250000;

		    // if(isset($_GET["JourneyID"])){
		    // 	$sql = "SELECT COUNT(*) as Total FROM `tblmultipleusers` WHERE (UserID=". $conn->real_escape_string($_SESSION["userID"]) ." AND MultipleUserID=". $conn->real_escape_string($_POST["multipleUserID"]) .")";
			   //  $result = $conn->query($sql);
			   //  if($result->fetch_assoc()["Total"] == 0){
			   //      // this challenge hasn't been assigned directly
			   //      $loggedIn = TRUE;
			   //  }
		    // }

			// if($loggedIn){
			// 	$sql = "SELECT COUNT(UpdateIndex) as Total FROM `tblupdates` WHERE MultipleUserID=". $conn->real_escape_string($_POST["multipleUserID"]);
		 //        $result = $conn->query($sql);
		 //        $updateIndex = $result->fetch_assoc()["Total"];


			// 	$sql = "INSERT INTO `tblupdates` (`MultipleUserID`, `UserID`, `UpdateIndex`, `UpdateDistance`) VALUES ('". $conn -> real_escape_string($_POST["multipleUserID"]) ."', '". $conn -> real_escape_string($_SESSION["userID"]) ."', '". $conn -> real_escape_string($updateIndex) ."', '". $conn -> real_escape_string($_POST["distanceUpdate"])*100 ."')";
			// 	if ($conn->query($sql) === TRUE) {
			// 	    echo "Record Inserted Successfully";

			// 	    $sql = "UPDATE `tblmultipleusers` SET `UserDistanceTravelled`=UserDistanceTravelled + ". $conn -> real_escape_string($_POST["distanceUpdate"]*100) ." WHERE (MultipleUserID = ". $conn -> real_escape_string($_POST["multipleUserID"]) ." AND UserID = ". $conn -> real_escape_string($_SESSION["userID"]) .")";
			// 		if ($conn->query($sql) === TRUE) {
			// 		    echo "Record updated successfully";
			// 		} else {
			// 		    echo "Error updating record: " . $conn->error;
			// 		    exit();
			// 		}


			// 	    $sql = "UPDATE tbljourneysusers SET DistanceTravelled = DistanceTravelled + ". $conn -> real_escape_string($_POST["distanceUpdate"]*100) ." WHERE `MultipleUserID` = ". $conn -> real_escape_string($_POST["multipleUserID"]);
			// 		if ($conn->query($sql) === TRUE) {
			// 		    echo "Record updated successfully";
			// 		} else {
			// 		    echo "Error updating record: " . $conn->error;
			// 		    exit();
			// 		}
			// 	} else {
			// 	    echo "Error inserting record: " . $conn->error;
			// 	    exit();
			// 	}

				
			// }

			$conn->close();
			// header('Location: route.php?journeyID='.htmlspecialchars($_POST['journeyID']).'&multipleUserID='.htmlspecialchars($_POST['multipleUserID']));
		?>
		

	</body>
</html>