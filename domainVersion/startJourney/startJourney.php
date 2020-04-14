<?php session_start();?>

<!DOCTYPE HTML>
<html lang="en">
    <head>
        <meta charset="utf-8"> 
        <title>Setup a new Challenge</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
        <link rel="stylesheet" href="startJourney.css">
        <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    </head>
    <body>
        <div class="bg"></div>

        <?php include '../navbar/navbar.php';?>

        <?php

            // password
            // $taken = FALSE;

            // if(isset($_SESSION["userID"])) {
            //     //User is already logged in
            //     header('Location: ../userProfile/userProfile.php?UserID='.htmlspecialchars($_SESSION["userID"]));
            //     exit();
            // }

            if(!$navbarLoggedIn){
                echo "<div id=\"register\">You must be logged in to start a challenge!</div>";
                exit();
            }

            if(isset($_REQUEST['totalDistance'])){
                echo $_POST["totalDistance"];
                $servername = "oliverursell05154.domaincommysql.com";
                $username = "oliver_ursell";
                $password = "Database@52";
                $dbname = "covidchallenges";

                // Create connection
                $conn = new mysqli($servername, $username, $password, $dbname);
                // Check connection
                if ($conn->connect_error) {
                    die("<div id=\"register\">Connection failed: " . $conn->connect_error ."</div>");
                }
                    //Insert the new account, set the session variable and redirect to the new userpage
                    // Hash and salt the password
                $options = [
                    'cost' => 11,
                ];
                $passwordFromPost = $conn -> real_escape_string($_POST["password"]);

                $hash = password_hash($passwordFromPost, PASSWORD_BCRYPT, $options);

                // Create new challenge for this user

                // Get new multiple users ID
                $sql = "SELECT `AUTO_INCREMENT` FROM  INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = '". $dbname ."' AND TABLE_NAME = 'tbljourneysusers';";
                $result = $conn->query($sql);
                $multipleUserID = $result->fetch_assoc()["AUTO_INCREMENT"];

                // Update journeys users
                $sql = "INSERT INTO `tbljourneysusers` (`MultipleUserID`, `JourneyID`, `DistanceTravelled`, `Public`, `MainUserID`, `TotalDistance`, `Password`, `CharityLink`) VALUES (NULL, '". $_POST["journeyID"] ."', '0', '1', '". $conn->real_escape_string($_SESSION["userID"]) ."', '". $conn->real_escape_string($_POST["totalDistance"]) ."', '". $hash ."', '". $conn->real_escape_string($_POST["charityLink"]) ."')";
                if ($conn->query($sql) === TRUE) {
                    // Record updated successfully

                    //Update multipleUsers
                    $sql = "INSERT INTO `tblmultipleusers` (`MultipleUserID`, `UserID`, `UserDistanceTravelled`, `TravelMode`) VALUES ('". $conn->real_escape_string($multipleUserID) ."', '". $conn->real_escape_string($_SESSION["userID"]) ."', '0', '";
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
                    $sql = $sql ."')";

                    if ($conn->query($sql) === TRUE) {
                        // Record updated successfully
                        $URL='../route/route.php?multipleUserID='.htmlspecialchars($multipleUserID);
                        echo "<script type='text/javascript'>document.location.href='{$URL}';</script>";
                        echo '<META HTTP-EQUIV="refresh" content="0;URL=' . $URL . '">';
                        exit();
                    } else {
                        //Note: technically I should delete the journeys users record now
                        echo "<div id=\"register\">Error updating record: " . $conn->error ."</div>";
                        exit();
                    }
                    
                } else {
                    echo "<div id=\"register\">Error updating record: " . $conn->error ."</div>";
                    exit();
                }

                $conn->close();
            }else if(isset($_POST["journeyID"])){
                $sql = "SELECT * FROM tbljourneys WHERE JourneyID = " . $conn -> real_escape_string($_POST["journeyID"]);
                $result = $conn->query($sql);
                if ($result->num_rows == 0){
                    echo "<div id=\"register\">Challenge not found, please return to the previous page and try the link again</div>";
                    exit();
                } elseif ($result->num_rows == 1) {
                    // set values for journey
                    $journeysRow = $result->fetch_assoc();
                    $displayName = htmlspecialchars($journeysRow['DisplayName']);
                } else {
                    echo "<div id=\"register\">Duplicate ID found, ID:" . htmlspecialchars($_GET["multipleUserID"]) . ". Whoops, this one is on us.</div>";
                    exit();
                }

                $sql = "SELECT Latitude, Longitude FROM tbllatlongs WHERE JourneyID = ". $conn -> real_escape_string($_POST["journeyID"]) ." ORDER BY CoordinateIndex";
                $latLongsResult = $conn->query($sql);

                if ($latLongsResult->num_rows == 0 || $latLongsResult->num_rows == 1){
                    echo "<div id='register'>There should be at least 2 latitude, longitude pairs but there aren't enough</div>";
                    exit();
                } elseif ($latLongsResult->num_rows > 1) {
                    // set values for journey
                    $latLongs = "[";
                    while($latLongsRow = $latLongsResult->fetch_assoc()){
                        $latLongs = $latLongs. "['" . $latLongsRow["Latitude"]. "','" .$latLongsRow["Longitude"]. "'],";
                    }
                    $latLongs = rtrim($latLongs, ","). "]";
                } else {
                    echo "<div id='register'>Somehow a negative number of latitude, longitude pairs was found</div>";
                    exit();
                }

                // $result = $conn->query("SELECT COUNT(*) as total FROM tbljourneysusers WHERE JourneyID = " . $conn -> real_escape_string($_GET['JourneyID']));
                // $row = $result->fetch_assoc();
                // echo $row['total'];

                //{lat: 50.066093, lng: -5.715103}

                // if(isset($_REQUEST['distanceUpdateSubmit']))
                // {
                //     $distanceCovered += $_POST['distanceUpdate']*1000;
                // }
                
                echo "<script>" .
                // "var latLongs = [['51.507570', '-0.127784'], ['55.863583', '-4.254418']];" .
                "var latLongs = ". $latLongs .";".
                "</script>";

            }else{
                echo "<div id=\"register\"> You need to access this page via either the choose challenge page (your profile -> add new challenge) or from an invitational link (ask the person who setup the original challenge to get the link from the share tab)</div>";
                exit();
            }
        ?>

        <div id="register">
            <h2>Start new challenge</h2>
            <h5><?php echo $displayName ?></h5><br>
            <div id="incorrectMessage"> ! Sorry, google couldn't find a route for this challenge that uses your selected travel mode (tip: try selecting a different travel mode and then using your preffered exercise method and using that to fill it in)</div>
            <br>
            <form id="startJourneyForm" action="startJourney.php" method="post" oninput='password2.setCustomValidity(password2.value != password.value ? "Passwords do not match." : "")' onsubmit="return calculateRoute()">
                <input id="distanceTotal" name="totalDistance" type="hidden" value="0">
                <input name="journeyID" type="hidden" value="<?php echo htmlspecialchars($_POST["journeyID"])?>">
                <input name="password" type="password" class="form-control" placeholder="Enter a password for inviting people to your challenge" required><br>
                <input name="password2" type="password" class="form-control" placeholder="Re-type Password" required><br>
                <div class="form-group">
                    <label for="travelMode">I am going to:</label>
                    <select id="travelModeSelect" name="travelMode" class="form-control" id="travelMode">
                        <option value="Run">Run</option>
                        <option value="Cycle">Cycle</option>
                        <option value="Row">Row</option>
                    </select>
                </div>
                <input name="charityLink" class="form-control" type="text" placeholder="Charity Link (optional)"><br>
                <input name="newJourneySubmit" type="submit" class="btn btn-success" value="start">
            </form>
        </div>

        <!-- <footer>View our cookie policy: https://www.termsfeed.com/cookies-policy/044a9bc1485cc0cf54b509fedb4fa29b</footer> -->
        <script src="startJourney.js"></script>
        <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAn_3UQjVzZh01LHtMFPnfLFCkKiBK4Joc&callback=initMap"></script>
    </body>
</html>