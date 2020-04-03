<?php session_start();?>

<!DOCTYPE HTML>
<html lang="en">
    <head>
        <meta charset="utf-8"> 
        <title>Route Name</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
        <link rel="stylesheet" href="route.css">
        <script src="../jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    </head>
    <body>

        <?php include '../navbar/navbar.php';?>

        <?php
            if(isset($_GET["multipleUserID"])){
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

                $loggedIn = FALSE;
                $mainUserLoggedIn = FALSE; // Calculated when the data from tbljourneysusers is fetched
                if(isset($_SESSION['userID'])) {
                    // User is logged in
                    $sql = "SELECT COUNT(UserID) as Total FROM `tblmultipleusers` WHERE (UserID=". $conn->real_escape_string($_SESSION["userID"]) ." AND MultipleUserID=". $conn->real_escape_string($_GET["multipleUserID"]) .")";
                    $result = $conn->query($sql);
                    if($result->fetch_assoc()["Total"] == 1){
                        // they are a member of this challenge
                        $loggedIn = TRUE;
                    }
                }

                $sql = "SELECT * FROM tbljourneysusers WHERE MultipleUserID = ". $conn->real_escape_string($_GET["multipleUserID"]);
                $result = $conn->query($sql);
                if ($result->num_rows == 1) {
                    //Check to see if the user is logged in
                    $journeysUsersRow = $result->fetch_assoc();
                    if($journeysUsersRow["Public"] == 0 && !$loggedIn){
                        echo "This challenge is private";
                        exit();
                    }

                    if($loggedIn){
                        // Figure out wether the main challenger is logged in
                        if($journeysUsersRow["MainUserID"] == $_SESSION["userID"]){
                            // Yes they are
                            $mainUserLoggedIn = TRUE;
                        }
                    }
                    // assign the covered distance
                    $distanceCovered = $journeysUsersRow["DistanceTravelled"]*10;
                } elseif ($result->num_rows == 0) {
                    echo "No data for these users was found on this challenge, please return to the previous page and try the link again";
                    exit();
                } else {
                    echo "Duplicate ID found, ID:" . htmlspecialchars($_GET["multipleUserID"]) . ". Whoops, this one is on us.";
                    exit();
                }

                $sql = "SELECT * FROM tbljourneys WHERE JourneyID = " . $conn -> real_escape_string($journeysUsersRow["JourneyID"]);
                $result = $conn->query($sql);
                if ($result->num_rows == 0){
                    echo "Challenge not found, please return to the previous page and try the link again";
                    exit();
                } elseif ($result->num_rows == 1) {
                    // set values for journey
                    $journeysRow = $result->fetch_assoc();
                    $displayName = htmlspecialchars($journeysRow['DisplayName']);
                } else {
                    echo "Duplicate ID found, ID:" . htmlspecialchars($_GET["multipleUserID"]) . ". Whoops, this one is on us.";
                    exit();
                }

                // $result = $conn->query("SELECT COUNT(*) as total FROM tbljourneysusers WHERE JourneyID = " . $conn -> real_escape_string($_GET['JourneyID']));
                // $row = $result->fetch_assoc();
                // echo $row['total'];

                $start = explode(",", htmlspecialchars($journeysRow['StartLatLong']));
                $end = explode(",", htmlspecialchars($journeysRow['EndLatLong']));

                //{lat: 50.066093, lng: -5.715103}

                // if(isset($_REQUEST['distanceUpdateSubmit']))
                // {
                //     $distanceCovered += $_POST['distanceUpdate']*1000;
                // }
                
                echo "<script>" .
                "var _origin = {lat:".$start[0].",lng:".$start[1]."};" .
                "var _destination = {lat:".$end[0].",lng:".$end[1]."};" .
                "var distance = " . htmlspecialchars($distanceCovered) . ";" .
                "</script>";
            }else{
                echo "MultipleUserID has not been specified, please return to the previous page and try the link again!";
                exit();
            }
        ?>

        <div id="map"></div>
        <div class="container-fluid">
            <div id="routeName" class="row">
                <div class="col-12">
                    <?php echo htmlspecialchars($displayName);?>
                </div>
            </div>
            <div id="toFromDisplay" class="row">
                <div id="progressBar" class="col-9">
                    <div id="progressBarContents"></div>
                </div>
                <div id="letterValues" class="col-3">
                    There was an error! This should be updated!
                </div>
            </div>

            <div id="challengers" class="row">
                <div class="container">
                    <div class="row col">
                        Challengers
                    </div>

                    <?php
                        function echoChallengerRow($position, $imageLink, $displayName, $travelMode, $userDistanceTravelled, $distanceTravelled, $firstDistanceTravelled, $nextDistanceTravelled, $mainUser, $userID){
                            echo "<div class=\"row col challenger\">
                                <div class=\"col-sm-2\">
                                    #". htmlspecialchars($position);
                                switch(htmlspecialchars($travelMode)){
                                    case "BICYCLING":
                                        echo "🚲";
                                        break;
                                    case "ROWING":
                                        echo "🚣";
                                        break;
                                    case "WALKING":
                                        echo "🏃";
                                        break;
                                }
                                echo "<br>";
                                if($mainUser){
                                    echo "Main Challenger";
                                }
                                echo "</div>
                                <div class=\"col-sm-2\">
                                    <img src=". htmlspecialchars($imageLink) ." height=\"100px\" width=\"100px\"><br>
                                    ". htmlspecialchars($displayName) ."
                                </div>
                                <div class=\"col-sm-3 offset-2\">";
                                switch($travelMode){
                                    case "BICYCLING":
                                        echo "Cycled";
                                        break;
                                    case "ROWING":
                                        echo "Rowed";
                                        break;
                                    case "WALKING":
                                        echo "Run";
                                        break;
                                }
                                echo ": " . htmlspecialchars($userDistanceTravelled) ."km so far<br>";
                                if($distanceTravelled == 0){
                                    if($userDistanceTravelled == 0){
                                        echo "100% of total progress<br><br>";
                                    }else{
                                        echo "0% of total progress<br><br>";
                                    }
                                }else{
                                    echo round(htmlspecialchars(($userDistanceTravelled/$distanceTravelled)*100), 2) ."% of total progress<br><br>";
                                }
                                echo htmlspecialchars($nextDistanceTravelled - $userDistanceTravelled) ."km from next position<br>".
                                    htmlspecialchars($firstDistanceTravelled - $userDistanceTravelled) ."km from 1st place
                                </div>
                                <div class=\"col-sm-3\">
                                    <a href=\"../userProfile/userProfile.php?userID=". htmlspecialchars($userID) ."\"><button class=\"openUserProfile\">View Profile</button></a>
                                </div>
                            </div>";
                        }

                        $sql = "SELECT * FROM tblmultipleusers WHERE MultipleUserID=". $conn -> real_escape_string($_GET["multipleUserID"]) ." ORDER BY UserDistanceTravelled DESC";
                        $multipleUsersResult = $conn->query($sql);

                        if ($multipleUsersResult->num_rows == 0){
                            echo "No users are recorded for this challenge, please return to the previous page and try the link again";
                            exit();
                        } elseif ($result->num_rows > 0) {
                            // set values for journey
                            $i = 0;
                            $firstDistance = 0;
                            $previousDistance = 0;
                            while($multipleUsersRow = $multipleUsersResult->fetch_assoc()){
                                if($i == 0){
                                    $firstDistance = htmlspecialchars($multipleUsersRow["UserDistanceTravelled"]/100);
                                    $previousDistance = $firstDistance;
                                }

                                $sql = "SELECT DisplayName, Public FROM tblusers WHERE UserID = ". $conn->real_escape_string($multipleUsersRow["UserID"]);
                                $usersResult = $conn->query($sql);
                                if ($usersResult->num_rows == 0){
                                    echo "This user didn't exist? Please try again";
                                    exit();
                                } elseif ($usersResult->num_rows == 1) {
                                    $usersRow = $usersResult->fetch_assoc();
                                    if($usersRow["Public"] == 0 && !$loggedIn){
                                        echo "<div class=\"row col challenger\">#". ($i + 1) ." This user's account is private</div>";
                                    }else{
                                        // Display this user's info specific to this journey
                                        $withoutExtension = "../userProfile/profilePictures/". htmlspecialchars($multipleUsersRow["UserID"]);
                                        if(file_exists($withoutExtension .".png")){
                                            $withExtension = $withoutExtension .".png";
                                        }elseif(file_exists($withoutExtension .".jpg")){
                                            $withExtension = $withoutExtension .".jpg";
                                        }elseif(file_exists($withoutExtension .".gif")){
                                            $withExtension = $withoutExtension .".gif";
                                        }else{
                                            $withExtension = "profilePictures/default.png";
                                        }
                                        echoChallengerRow($i + 1, $withExtension, htmlspecialchars($usersRow['DisplayName']), htmlspecialchars($multipleUsersRow["TravelMode"]), htmlspecialchars($multipleUsersRow["UserDistanceTravelled"]/100), htmlspecialchars($distanceCovered/1000), $firstDistance, $previousDistance, $journeysUsersRow["MainUserID"] == $multipleUsersRow["UserID"], $multipleUsersRow["UserID"]);
                                    }

                                } else {
                                    echo "Duplicate user ID found, ID:" . htmlspecialchars($multipleUsersRow["UserID"]) . ". Whoops, this one is on us.";
                                    exit();
                                }


                                $previousDistance = htmlspecialchars($multipleUsersRow["UserDistanceTravelled"]/100);
                                $i = $i + 1;
                            }
                            $displayName = htmlspecialchars($journeysRow['DisplayName']);
                        } else {
                            echo "Somehow a negative value was recieved";
                            exit();
                        }
                    ?>

                    <?php
                        // Example of the challenger view the php is generating, in php to hide comments from inspector
                        // <div class="row col challenger">
                        //     <div class="col-sm-2">
                        //         #1🚲<br>
                        //         Main User
                        //     </div>
                        //     <div class="col-sm-2">
                        //         <img src="../userProfile/profilePictures/1.gif"><br>
                        //         Oliver Ursell
                        //     </div>
                        //     <div class="col-sm-3 offset-2">
                        //         Cycled: 500km so far<br>
                        //         50% of total progress<br><br>
                        //         0km from 1st place<br>
                        //         0km from next position
                        //     </div>
                        //     <div class="col-sm-3">
                        //         <a href="../userProfile/userProfile.php?userID=1"><button class="openUserProfile">View Profile</button></a>
                        //     </div>
                        // </div>
                    ?>
                </div>
            </div>

            <div id="actionButtons" class="row">
                <div class="col-2 actionButtonContainer input-lg">
                <?php
                    if($loggedIn){
                        echo "<div onclick=\"$('#addUpdate').show('fast');\" class=\"actionButton\"><img class=\"img-fluid\" src=\"compassRose.png\"></div>";
                    }
                ?>
                </div>
                <div class="col-2 actionButtonContainer input-lg">
                    <div onclick="$('#viewUpdates').show('fast');" class="actionButton"><img class="img-fluid" src="open-book-silhouette.jpg"></div>
                </div>
                <div class="col-8"></div>
            </div>
        </div>

        <?php
            if($loggedIn){
                echo '<div id="addUpdate" class="config">
                    <form action="addUpdate.php" method="post">
                        <div class="form-group">
                          <input name="multipleUserID" type="hidden" value="'. htmlspecialchars($_GET['multipleUserID']) .'">
                          <label for="distanceInput">Distance travelled (Kilometers)</label>
                          <input name="distanceUpdate" type="text" class="form-control" id="distanceInput" aria-describedby="distanceHelp" placeholder="How far did you go?" pattern="^\d*(\.\d{0,2})?$" required>
                          <small id="distanceHelp" class="form-text text-muted">Should be to maximum two decimal places.</small>
                        </div>
                        <button name="distanceUpdateSubmit" type="submit" class="btn btn-primary">Update</button>
                    </form>
                    <br>
                    <button onclick="$(\'#addUpdate\').hide(\'fast\');" class="btn btn-danger">Cancel</button>
                </div>';
            }
        ?>
        

        <div id="viewUpdates" class="config">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-6">
                        Updates
                        <br>
                        Ordered by: Most recent
                    </div>
                    <div class="col-2 offset-3">
                        <button onclick="$('#viewUpdates').hide('fast');" class="btn btn-danger">Close</button>
                    </div>
                </div>
                <br>

                <?php
                    function echoUpdate($imageLink, $displayName, $distance, $mainUser, $challenger, $userID, $updateIndex){
                        echo "<br>
                        <div class=\"update row\">
                            <img width=\"50px\" height=\"50px\" src=\"". $imageLink ."\">
                            <div class=\"col-7\">".
                                $displayName ."<br>".
                                $distance ."km
                            </div>";
                        if($mainUser){
                            echo "<div class=\"col-1\"><form action=\"removeUpdate.php\" method=\"post\">
                                <input name=\"multipleUserID\" type=\"hidden\" value=\"". htmlspecialchars($_GET["multipleUserID"]) ."\">
                                <input name=\"userID\" type=\"hidden\" value=\"". htmlspecialchars($userID) ."\">
                                <input name=\"updateIndex\" type=\"hidden\" value=\"". htmlspecialchars($updateIndex) ."\">
                                <input name=\"distanceUpdate\" type=\"hidden\" value=\"". htmlspecialchars($distance) ."\">
                                <input name=\"remove\" type=\"submit\" class=\"btn btn-danger\" value=\"X\">
                            </form></div>";
                        }else if($challenger){
                            if($userID == $_SESSION["userID"]){
                                //This user posted this update
                                echo "<div class=\"col-1\"><form action=\"removeUpdate.php\" method=\"post\">
                                    <input name=\"multipleUserID\" type=\"hidden\" value=\"". htmlspecialchars($_GET["multipleUserID"]) ."\">
                                    <input name=\"userID\" type=\"hidden\" value=\"". htmlspecialchars($userID) ."\">
                                    <input name=\"updateIndex\" type=\"hidden\" value=\"". htmlspecialchars($updateIndex) ."\">
                                    <input name=\"distanceUpdate\" type=\"hidden\" value=\"". htmlspecialchars($distance) ."\">
                                    <input name=\"remove\" type=\"submit\" class=\"btn btn-danger\" value=\"X\">
                                </form></div>";
                            }
                        }
                        echo "</div>";
                    }

                    $sql = "SELECT UserID, UpdateIndex, UpdateDistance FROM tblupdates WHERE MultipleUserID=". $conn -> real_escape_string($_GET["multipleUserID"]) ." ORDER BY UpdateIndex DESC";
                        $updatesResult = $conn->query($sql);

                    if ($updatesResult->num_rows == 0){
                        echo "No updates are recorded for this challenge";
                    } elseif ($result->num_rows > 0) {
                        while($updatesRow = $updatesResult->fetch_assoc()){
                            $sql = "SELECT DisplayName, Public FROM tblusers WHERE UserID = ". $conn->real_escape_string($updatesRow["UserID"]);
                            $usersResult = $conn->query($sql);
                            if ($usersResult->num_rows == 0){
                                echo "This user didn't exist? Please try again";
                                exit();
                            } elseif ($usersResult->num_rows == 1) {
                                $usersRow = $usersResult->fetch_assoc();
                                if($usersRow["Public"] == 0 && !$loggedIn){
                                    echo "<div class=\"update row\"> This user's account is private</div>";
                                }else{
                                    // Display this user's info specific to this journey
                                    $withoutExtension = "../userProfile/profilePictures/". htmlspecialchars($updatesRow["UserID"]);
                                    if(file_exists($withoutExtension .".png")){
                                        $withExtension = $withoutExtension .".png";
                                    }elseif(file_exists($withoutExtension .".jpg")){
                                        $withExtension = $withoutExtension .".jpg";
                                    }elseif(file_exists($withoutExtension .".gif")){
                                        $withExtension = $withoutExtension .".gif";
                                    }else{
                                        $withExtension = "profilePictures/default.png";
                                    }

                                    echoUpdate($withExtension, $usersRow["DisplayName"], $updatesRow["UpdateDistance"]/100, $mainUserLoggedIn, $loggedIn, $updatesRow["UserID"], $updatesRow["UpdateIndex"]);
                                }

                            } else {
                                echo "Duplicate user ID found, ID:" . htmlspecialchars($multipleUsersRow["UserID"]) . ". Whoops, this one is on us.";
                                exit();
                            }
                        }
                        $displayName = htmlspecialchars($journeysRow['DisplayName']);
                    } else {
                        echo "Somehow a negative value was recieved";
                        exit();
                    }
                    $conn->close();   
                ?>
            </div>
        </div>

        <!-- <footer>View our cookie policy: https://www.termsfeed.com/cookies-policy/044a9bc1485cc0cf54b509fedb4fa29b</footer> -->

        <script src="route.js"></script>
        <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAn_3UQjVzZh01LHtMFPnfLFCkKiBK4Joc&callback=initMap">
    </script>
    </body>
</html>