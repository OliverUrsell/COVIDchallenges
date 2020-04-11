<?php session_start();?>

<!DOCTYPE HTML>
<html lang="en">
    <head>
        <meta charset="utf-8"> 
        <title>Join a new Challenge?</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
        <link rel="stylesheet" href="joinJourney.css">
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
		    	echo "<div id=\"contents\">You need to log in / register before you can join this challenge! Login and visit this link again</div>";
		    	exit();
		    }

		    $multipleUserID = null;

		    $passwordIncorrect=FALSE;

		    if(isset($_GET['multipleUserID'])){

		    	if(isset($_POST["confirm"])){
			    	// User has pressed confirm and submitted a password to join the challenge in $_GET["multipleUserID"]

			    	$sql = "SELECT Password FROM tbljourneysusers WHERE MultipleUserID = '". $conn -> real_escape_string($_GET["multipleUserID"]) ."'";
	                $result = $conn->query($sql);
	                if ($result->num_rows == 0){
	                    //Account not found
	                    $incorrect = TRUE;
	                } elseif ($result->num_rows == 1) {
	                    $row = $result->fetch_assoc();
	                    // test the password
	                    $passwordFromPost = htmlspecialchars($_POST["password"]);
	                    $hashedPasswordFromDB = htmlspecialchars($row["Password"]);

	                    if (password_verify($passwordFromPost, $hashedPasswordFromDB)) {
	                        // Valid password

	                    	// Add new account to challenge
		                    $sql = "INSERT INTO `tblmultipleusers` (`MultipleUserID`, `UserID`, `UserDistanceTravelled`, `TravelMode`) VALUES ('". $conn->real_escape_string($_GET["multipleUserID"]) ."', '". $conn->real_escape_string($_SESSION["userID"]) ."', '0', '";
		                    switch($conn->real_escape_string($_POST["travelMode"])){
		                        case "Cycle":
		                            $sql = $sql ."BICYCLING";
		                            break;
		                        case "Row":
		                            $sql = $sql ."ROWING";
		                            break;
		                        case "Run":
		                            $sql = $sql ."WALKING";
		                            break;
		                        default:
		                            echo $_POST["travelMode"]. "<div id=\"register\">Something went wrong with the travel mode!</div>";
		                            exit();
		                    }

		                    $sql = $sql . "')";
		                    if ($conn->query($sql) === TRUE) {
		                        // Record updated successfully
		                        header('Location: ../route/route.php?multipleUserID='. htmlspecialchars($_GET["multipleUserID"]));
		                        exit();
		                    } else {
		                        echo "<div id=\"register\">Error updating record: " . $conn->error ."</div>";
		                        exit();
		                    }
	                        exit();
	                    } else {
	                        // Invalid password
	                        $passwordIncorrect = TRUE;
	                    }
	                } else {
	                    $row = $result->fetch_assoc();
	                    echo "Duplicate account found, ID:" . htmlspecialchars($row["UserID"]) . ". Whoops, this one is on us.";
	                }
			    }


		    	if($_GET['multipleUserID'] != ""){
			    	$sql = "SELECT JourneyID, MainUserID FROM tbljourneysusers WHERE MultipleUserID = " . $conn -> real_escape_string($_GET["multipleUserID"]);
	                $result = $conn->query($sql);
	                if ($result->num_rows == 0){
	                    echo "<div id=\"contents\">Challenge not found, please check the link has been copied correctly</div>";
	                    exit();
	                } elseif ($result->num_rows == 1) {

	                    $journeysUsersRow = $result->fetch_assoc();

	                    $sql = "SELECT * FROM tbljourneys WHERE JourneyID = " . $conn -> real_escape_string($journeysUsersRow["JourneyID"]);
		                $result = $conn->query($sql);
		                if ($result->num_rows == 0){
		                    echo "<div id=\"contents\">Challenge not found, please check the link has been copied correctly</div>";
		                    exit();
		                } elseif ($result->num_rows == 1) {
		                    // set values for journey
		                    $journeysRow = $result->fetch_assoc();

		                    $sql = "SELECT * FROM tblusers WHERE UserID = " . $conn -> real_escape_string($journeysUsersRow["MainUserID"]);
			                $result = $conn->query($sql);
			                if ($result->num_rows == 0){
			                    echo "<div id=\"contents\">Main user not found, please check the link has been copied correctly</div>";
			                    exit();
			                } elseif ($result->num_rows == 1) {
			                    // set values for journey
			                    $usersRow = $result->fetch_assoc();
			                } else {
			                    echo "<div id=\"contents\">Duplicate user ID found, ID:" . htmlspecialchars($journeysUsersRow["MainUserID"]) . ". Whoops, this one is on us.</div>";
			                    exit();
			                }
		                    
		                    
		                } else {
		                    echo "<div id=\"contents\">Duplicate ID found, ID:" . htmlspecialchars($_GET["multipleUserID"]) . ". Whoops, this one is on us.</div>";
		                    exit();
		                }


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

			$conn->close();
		?>

		<br>
		<div id="contents" class="container">
			<?php if($passwordIncorrect){echo "<span id=\"failedPassword\"> ! Password is incorrect. Please try again</span><br><br>";}?>
			<div class="row">
				<div class="col-12">
					Join the new challenge <?php echo htmlspecialchars($journeysRow["DisplayName"])?> with <?php echo htmlspecialchars($usersRow["DisplayName"])?>?
				</div>
			</div>
			<br>
			<div class="row">
				<div class="col">
					<form action="joinJourney.php?multipleUserID=<?php echo htmlspecialchars($_GET["multipleUserID"]) ?>" method="post">
						<div id="travelModeContainer" class="form-group">
		                    <label for="travelMode">I am going to:</label>
		                    <select name="travelMode" class="form-control" id="travelMode">
		                        <option>Run</option>
		                        <option>Cycle</option>
		                        <option>Row</option>
		                    </select>
		                </div>
		                <input name="password" type="password" placeholder=" challenge password" required>
						<input id="yes" name="confirm" class="btn btn-success" type="submit" value="Confirm">
					</form>
					<a href="../login/login.php"><button id="no" class="btn btn-danger">Cancel</button></a>
				</div>
			</div>
		</div>
	</body>
</html>