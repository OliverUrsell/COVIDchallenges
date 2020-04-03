<?php session_start();?>

<!DOCTYPE HTML>
<html lang="en">
    <head>
        <meta charset="utf-8"> 
        <title>User's Journeys</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
        <link rel="stylesheet" href="userProfile.css">
        <script src="../jquery.min.js"></script>
        <script   src="https://code.jquery.com/color/jquery.color-2.1.2.min.js"   integrity="sha256-H28SdxWrZ387Ldn0qogCzFiUDDxfPiNIyJX7BECQkDE="   crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    </head>
    <body>
        <div class="bg"></div>        
        <?php
            include_once("../navbar/navbar.php");

            $loggedIn = FALSE;
            if(isset($_SESSION['userID']) && isset($_GET["userID"])) {
                // User is logged in
                if($_SESSION['userID'] == $_GET['userID']){
                    // this is their profile
                    $loggedIn = TRUE;   
                }
            }

            if(isset($_GET['userID'])){
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
                if(isset($_GET["userID"]) && $_GET["userID"] != ""){
                    $sql = "SELECT * FROM tblusers WHERE UserID = " . $conn -> real_escape_string($_GET["userID"]);
                    $result = $conn->query($sql);
                    if ($result->num_rows == 0){
                        echo "<div id=\"name\">This user could not be found, please try again</div>";
                        exit();
                    } elseif ($result->num_rows == 1) {
                        $row = $result->fetch_assoc();
                        if($row['Public'] == 0 && !$loggedIn){
                            //This profile is not public, and we're not the owner
                            echo "<div id=\"name\">This is a private profile. You must be the owner to view it. This user can make their profile public by going to their Profile -> Settings -> Privacy Settings</div>";
                            exit();
                        }
                    } else {
                        echo "<div id=\"name\">Duplicate ID found, ID:" . htmlspecialchars($_GET["userID"]) . ". Whoops, this one is on us.<\div>";
                        exit();
                    }
                }else{
                    echo "<div id=\"name\">This link has not specified a UserID, please return to the previous page and try again.</div>";
                    exit();
                }
            }else{
                echo "<div id=\"name\">The UserID has not been specified, please return to the previous link and try again.</div>";
                exit();
            }

            $edited = FALSE;
            $passValid = FALSE;
            $updateSuccess = FALSE;
            //Check logged in so can't just send a POST request
            if(isset($_POST['apply']) && $loggedIn){
                $edited = TRUE;
                if(password_verify($_POST['validationPassword'], $row['Password'])){
                    $passValid = TRUE;
                    if($_POST['public'] == "Public"){
                        $public = 1;
                    }else{
                        $public = 0;
                    }
                    $newEmail = $conn -> real_escape_string($_POST['email']);
                    if($_POST['password'] == ""){
                        $hash = $conn -> real_escape_string($row['Password']);
                    }else{
                        $newPassword = $conn -> real_escape_string($_POST['password']);
                        // Hash and salt the password
                        $options = [
                            'cost' => 11,
                        ];

                        $hash = password_hash($newPassword, PASSWORD_BCRYPT, $options);
                    }

                    $newDisplayName = $conn -> real_escape_string($_POST['displayName']);

                    // Edit account
                    $sql = "UPDATE `tblusers` SET `Email` = '". $newEmail ."', `Password` = '". $hash ."', `DisplayName` = '". $newDisplayName ."', `Public` = '". $public ."' WHERE `tblusers`.`UserID` = ". $_GET['userID'];
                    
                    if ($conn->query($sql) === TRUE) {
                        // Record updated successfully
                        $updateSuccess = TRUE;
                    } else {
                        echo "Error updating record: " . $conn->error;
                    }
                }
            }
        ?>

        <div class="container-fluid">
            <div class="row">
                <div id="selector" class="col-10 block offset-1">
                    <?php 
                        if($edited){
                            if($passValid){
                                if($updateSuccess){
                                    echo "<span id=\"detailChange\">✓ Details changed successfully</span><br><br>";
                                }else{
                                    echo "<span id=\"detailChangeFailed\">? The update could not be made. Check your internet connection.</span><br><br>";
                                }
                            }else{
                                echo "<span id=\"wrongPass\"> 🗙 The password was incorrect</span><br><br>";
                            }
                        }?>
                    <div id="name" class="row">
                        <div class="col-2">
                            <img src="
                            <?php
                                $withoutExtension = "profilePictures/". $_GET["userID"];
                                if(file_exists($withoutExtension .".png")){
                                    echo $withoutExtension .".png";
                                }elseif(file_exists($withoutExtension .".jpg")){
                                    echo $withoutExtension .".jpg";
                                }elseif(file_exists($withoutExtension .".gif")){
                                    echo $withoutExtension .".gif";
                                }else{
                                    echo "profilePictures/default.png";
                                }
                            ?>" height="100" width="100" alt="<?php if($updateSuccess){echo htmlspecialchars($newDisplayName);}else{echo htmlspecialchars($row["DisplayName"]);}?>'s profile picture"/>
                        </div>
                        <div class="col-8 offset-2">
                            <?php if($updateSuccess){echo htmlspecialchars($newDisplayName);}else{echo htmlspecialchars($row["DisplayName"]);} ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <button class="category selected" divid="challenges">Challenges</button>
                        </div>
                        <div class="col">
                            <button class="category" divid="statistics">Statistics</button>
                        </div>
                        
                        <?php
                            if($loggedIn){
                                echo '<div class="col">
                                    <button class="category" divid="settings">Settings</button>
                                </div>';
                            }
                        ?>
                    </div>
                </div>
            </div>
            <div class="row">
                <?php // Settings stays at the top as it has to access values from first table query?>
                <div id="settings" class="block col-10 block offset-1">
                    Settings

                    <form action="storeProfilePicture.php" method="post" enctype="multipart/form-data">
                        <input name="userID" type="hidden" value="<?php echo $_GET["userID"]; ?>">
                        Change profile picture:
                        <input type="file" name="image" accept="image/*">
                        <input type="submit">
                    </form>

                    <form action="userProfile.php?userID=<?php echo htmlspecialchars($_GET['userID']); ?>" method="post" oninput='password2.setCustomValidity(password2.value != password.value ? "Passwords do not match." : "");' autocomplete="off"><br>
                        <input type="text" style="display:none">
                        <input type="email" style="display:none">
                        <input type="password" style="display:none">
                        <div class="form-group row">
                            <div class="col-2">
                                <label for="privacy">Privacy settings:</label>
                            </div>
                            <div class="col-4">
                                <select name="public" class="form-control" id="privacy">
                                    <?php if($updateSuccess){$newValue = $public;}else{$newValue = $row["Public"];} ?>
                                    <option><?php if($newValue == 1){echo"Public";}else{echo "Private";}?></option>
                                    <option><?php if($newValue == 0){echo"Public";}else{echo "Private";}?></option>
                                </select>
                            </div>
                            <div class="col-6">
                                Public: Other users / guests can view your profile, including public challenges and combined statistics of public adventures.<br><br>Private: Other users / guests cannot view your profile, and cannot access public or private challenges through it. Anyone doing a challenge with you can see your record in that challenge, but cannot view your user profile
                            </div>
                        </div>
                        <br>
                        <div class="form-group">
                            <input name="email" type="email" class="form-control" aria-describedby="emailHelp" placeholder="Email" value="<?php if($updateSuccess){echo htmlspecialchars($newEmail);}else{echo htmlspecialchars($row["Email"]);} ?>" autocomplete="off" required>
                            <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small>
                        </div>
                        <div class="row">
                            <div class="col">
                                <input name="password" type="password" class="form-control" placeholder="New Password"
                                 autocomplete="off"><small class="form-text text-muted">Leave password blank to keep your old one</small>
                            </div>
                            <div class="col">
                                <input name="password2" type="password" class="form-control" placeholder="Re-type New Password"><br><br>
                            </div>
                        </div>
                        <input name="displayName" type="text" class="form-control" placeholder="Display Name" value="<?php if($updateSuccess){echo htmlspecialchars($newDisplayName);}else{echo htmlspecialchars($row["DisplayName"]);} ?>" required><br>
                        <div class="row">
                            <div class="col">
                                <input name="validationPassword" type="password" class="form-control" placeholder="Enter your password to change settings" required>
                            </div>
                            <div class="col">
                                <button name="apply" type="submit" class="btn btn-warning">Apply</button>
                            </div>
                        </div>
                    </form>
                </div>

                <div id="challenges" class="col-10 offset-1 block">
                    <?php
                    if($loggedIn){
                        echo "<button class=\"btn btn-success\">+ Add New Challenge</button>
                        <button class=\"btn btn-success\">+ Create your own challenge</button>";
                    }

                        function echoChallengeRow($travelMode, $routeDisplayName, $distance, $totalDistance, $loggedIn, $displayName, $userDistance, $multipleUserID){
                            echo "<div class=\"challenge\">
                                    <div class=\"row\">
                                        <div class=\"col-8\">
                                            <div class=\"row\">
                                                <div class=\"col offset-1\">";
                                                    switch($travelMode){
                                                        case "BICYCLING":
                                                            echo "Cycling ";
                                                            break;
                                                        case "ROWING":
                                                            echo "Rowing ";
                                                            break;
                                                        case "WALKING":
                                                            echo "Running ";
                                                            break;
                                                    }
                                                    echo htmlspecialchars($routeDisplayName) ."
                                                </div>
                                            </div>
                                            <div class=\"row\"><br></div>
                                            <div class=\"row progressBarContentsontainer\">
                                                <div class=\"progressBar col-9\">
                                                    <div class=\"progressBarContents\" distance=". htmlspecialchars($distance) ." distanceTotal=". htmlspecialchars($totalDistance) .">";
                                                        switch($travelMode){
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
                                                    echo "</div>
                                                </div>
                                            </div>
                                            <div class=\"row offset-1\">";
                                                if($loggedIn){
                                                    echo "You have";
                                                }else{
                                                    echo $displayName ." has";
                                                }
                                                echo " completed ". $userDistance ."km(s) of this challenge
                                            </div>
                                        </div>
                                        <div class=\"col-3 offset-1\">
                                            <a href=\"../route/route.php?multipleUserID=". $multipleUserID ."\"><button class=\"openChallenge\">Open <img src=\"link.png\" height=\"10px\" width=\"10px\"    ></button></a>
                                        </div>
                                    </div>
                                </div>";
                        }


                        $challengeDisplayed = FALSE; //Keep track to display no challenges message

                        // Select values from tblmultiple users
                        $sql = "SELECT * FROM tblmultipleusers WHERE UserID = " . $conn -> real_escape_string($_GET["userID"]);
                        
                        $multipleUsersResult = $conn->query($sql);

                        if ($multipleUsersResult->num_rows > 0) {
                            while($multipleUsersRow = $multipleUsersResult->fetch_assoc()) {
                                // For all values in multiple users where the userID matches this profile
                                // select value from tbljourneysusers
                                if($loggedIn){
                                    $sql = "SELECT * FROM tbljourneysusers WHERE MultipleUserID = " . $conn -> real_escape_string($multipleUsersRow["MultipleUserID"]);
                                }else{
                                    $sql = "SELECT * FROM tbljourneysusers WHERE MultipleUserID = " . $conn -> real_escape_string($multipleUsersRow["MultipleUserID"]) ." AND Public = 1";
                                }
                                $resultJourneysUsers = $conn->query($sql);
                                if ($resultJourneysUsers->num_rows == 0){
                                    // This challenge is private, or there's something wrong
                                    if($loggedIn){
                                        echo "Journey not found, there is an error in tbljourneysusers / tblmultipleusers, multiple user ID: ". htmlspecialchars($multipleUsersRow["MultipleUserID"]);
                                            exit();
                                    }else{
                                        //This challenge is private
                                    }
                                } elseif ($resultJourneysUsers->num_rows == 1) {
                                    //Journeys user successfully recieved
                                    $journeysUsersRow = $resultJourneysUsers->fetch_assoc();

                                    // select value from tbljourneys
                                    $sql = "SELECT * FROM tbljourneys WHERE JourneyID = " . $conn -> real_escape_string($journeysUsersRow["JourneyID"]);
                                    $resultJourneys = $conn->query($sql);
                                    if ($resultJourneys->num_rows == 0){
                                        echo "Journey not found, there is an error in tbljourneysusers / tbljourneys users, journey ID: ". htmlspecialchars($journeysUsersRow["JourneyID"]);
                                        exit();
                                    } elseif ($resultJourneys->num_rows == 1) {
                                        $journeyRow = $resultJourneys->fetch_assoc();

                                        //Echo challenge row block
                                        if($journeysUsersRow["Public"] == 1 || $loggedIn){

                                            if($updateSuccess){
                                                $currentDisplayName = htmlspecialchars($newDisplayName);
                                            }else{
                                                $currentDisplayName = htmlspecialchars($row["DisplayName"]);
                                            }

                                            echoChallengeRow($multipleUsersRow["TravelMode"], $journeyRow["DisplayName"], $journeysUsersRow["DistanceTravelled"]/100, $journeyRow["TotalDistance"]/100,$loggedIn ,$currentDisplayName , $multipleUsersRow["UserDistanceTravelled"]/100, $multipleUsersRow["MultipleUserID"]);
                                            $challengeDisplayed = TRUE;
                                        }


                                    } else {
                                        echo "Duplicate journey ID found, journey ID:" . htmlspecialchars($journeysUsersRow["JourneyID"]) . ". Whoops, this one is on us.";
                                        exit();
                                    }

                                } else {
                                    echo "Duplicate multiple users ID found, ID:" . htmlspecialchars($multipleUsersRow["MultipleUserID"]) . ". Whoops, this one is on us.";
                                    exit();
                                }

                            }
                        } else {
                            if($loggedIn){
                                echo "<br><br>You haven't started any Challenges yet! Get on it!";
                            }else{
                                echo "<br><br>";
                                if($updateSuccess){echo htmlspecialchars($newDisplayName);}else{echo htmlspecialchars($row["DisplayName"]);}
                                echo " hasn't started any challenges yet, time to set him one????";
                            }
                            $challengeDisplayed = TRUE;
                        }

                        if($challengeDisplayed == FALSE){
                            //No challange, or no challenge message has been displayed, they're all private or there aren't any
                            if($loggedIn){
                                echo "<br><br>You haven't started any Challenges yet! Get on it!";
                            }else{
                                echo "<br><br>";
                                if($updateSuccess){echo htmlspecialchars($newDisplayName);}else{echo htmlspecialchars($row["DisplayName"]);}
                                echo " hasn't started any challenges yet, time to set him one????";
                            }
                        }
                    ?>

                    <?php
                    // Example of challenge div, inside php so you can't view it from inspector
                    // <div class="challenge">
                    //     <div class="row">
                    //         <div class="col-8">
                    //             <div class="row">
                    //                 <div class="col offset-1">
                    //                     Cycling Land's End to John O'Groats
                    //                 </div>
                    //             </div>
                    //             <div class="row"><br></div>
                    //             <div class="row progressBarContentsontainer">
                    //                 <div class="progressBar col-9">
                    //                     <div class="progressBarContents" distance="1500" distanceTotal="5000">
                    //                         🚲
                    //                     </div>
                    //                 </div>
                    //             </div>
                    //             <div class="row offset-1">
                    //                 This user has cycled 300km(s) of this challenge
                    //             </div>
                    //         </div>
                    //         <div class="col-3 offset-1">
                    //             <a href="../route/route.php?multipleUserID=1&journeyID=1"><button class="openChallenge">Open</button></a>
                    //         </div>
                    //     </div>
                    // </div> 
                    ?>
                </div>
                <div id="statistics" class="block col-10 block offset-1">
                    Total distance travelled: 
                </div>
            </div>
        </div>

        <script src="userProfile.js"></script>
        <!-- <footer>View our cookie policy: https://www.termsfeed.com/cookies-policy/044a9bc1485cc0cf54b509fedb4fa29b</footer> -->
    </body>
</html>