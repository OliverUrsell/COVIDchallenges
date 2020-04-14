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

			$servername = "oliverursell05154.domaincommysql.com";
			$username = "oliver_ursell";
			$password = "Database@52";
			$dbname = "covidchallenges";

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

		    if(isset($_GET['journeyID'])){
		    	if($_GET['journeyID'] != ""){
			    	$sql = "SELECT * FROM tbljourneys WHERE JourneyID = " . $conn -> real_escape_string($_GET["journeyID"]);
	                $result = $conn->query($sql);
	                if ($result->num_rows == 0){
	                    echo "<div id=\"contents\">Challenge not found, please check the link has been copied correctly</div>";
	                    exit();
	                } elseif ($result->num_rows == 1) {
	                    // set values for journey
	                    $journeysRow = $result->fetch_assoc();
	                    $displayName = htmlspecialchars($journeysRow['DisplayName']);

	                    
	                } else {
	                    echo "<div id=\"contents\">Duplicate ID found, ID:" . htmlspecialchars($_GET["journeyID"]) . ". Whoops, this one is on us.</div>";
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
				<div class="row">
					<div class="col-12">
						Start the new challenge <?php echo $displayName ." as ". htmlspecialchars($row["DisplayName"]) ?>?
					</div>
				</div>
				<br>
				<div class="row">
					<div class="col">
						<form action="../startJourney/startJourney.php" method="post">
							<input name="journeyID" type="hidden" value="<?php echo $_GET["journeyID"]; ?>">
							<input class="btn btn-success" type="submit" value="Yes">
						</form>
						<a href="../login/login.php"><button class="btn btn-danger">No</button></a>
					</div>
				</div>
			</div>";
		

	</body>
</html>