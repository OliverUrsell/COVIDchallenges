<?php session_start();?>

<!DOCTYPE HTML>
<html lang="en">
    <head>
        <meta charset="utf-8"> 
        <title>Choose a new Challenge</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
        <link rel="stylesheet" href="chooseJourney.css">
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
            if(isset($_SESSION['userID'])) {
                // User is logged in
                $loggedIn = TRUE;   
            }
        ?>

        <div class="container-fluid">
            <br>
            <h1><b>Try a new challenge!</b></h1>
            <h4>Compete with friends, family and/or co-workers to achieve something to be proud of!</h4>
            <br>

            <?php

                function echoChallenge($displayName, $count, $distanceTotal, $journeyID, $userLoggedIn){
                    echo "<div class=\"row challenge\">
                    <div class=\"col-6\">
                        <div class=\"row\">
                            <div class=\"title col offset-1\">
                                ". $displayName ."
                            </div>
                        </div>
                        <div class=\"row\"><br></div>
                        <div class=\"row offset-1\">
                            ";
                            if($count == 1){
                                echo $count ." team has attempted this challenge";
                            }else{
                                echo $count ." teams have attempted this challenge";
                            }
                        echo "</div>
                    </div>
                    <div class=\"col-2\">
                        ". $distanceTotal ."km
                    </div>";
                    if($userLoggedIn){
                        echo "<div class=\"col-3 offset-1\">
                            <form class=\"openChallenge\" action=\"../startJourney/startJourney.php\" method=\"post\">
                                <input name=\"journeyID\" type=\"hidden\" value=\"". $journeyID ."\">
                                <input type=\"submit\" class=\"openChallenge\" value=\"Do this challenge\">
                            </form>
                        </div>";
                    }else{
                        echo "<div class=\"col-3 offset-1\">
                            Log in to try this challenge<br>
                            (When your profile opens press: Add new challenge)
                        </div>";
                    }
                    echo "</div>";
                }

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

                $sql = "SELECT JourneyID, COUNT(*) as Total FROM tbljourneysusers GROUP BY JourneyID ORDER BY COUNT(*) DESC LIMIT 10";
                $journeysUsersResult = $conn->query($sql);
                if ($journeysUsersResult->num_rows == 0){
                    echo "<div class=\"challenge\">No Challenges were found</div>";
                    exit();
                } elseif ($journeysUsersResult->num_rows > 0) {
                    while($journeysUsersRow = $journeysUsersResult->fetch_assoc()){
                        $sql = "SELECT DisplayName, TotalDistance FROM tbljourneys WHERE JourneyID = " . $conn -> real_escape_string($journeysUsersRow["JourneyID"]);
                        $journeysResult = $conn->query($sql);
                        if ($journeysResult->num_rows == 0){
                            echo "Challenge not found, please reload the page";
                            exit();
                        } elseif ($journeysResult->num_rows == 1) {
                            // set values for journey
                            $journeysRow = $journeysResult->fetch_assoc();
                            echoChallenge(htmlspecialchars($journeysRow['DisplayName']), htmlspecialchars($journeysUsersRow["Total"]), htmlspecialchars($journeysRow["TotalDistance"])/100, htmlspecialchars($journeysUsersRow["JourneyID"]), $loggedIn);
                        } else {
                            echo "Duplicate ID found, ID:" . htmlspecialchars($_GET["multipleUserID"]) . ". Whoops, this one is on us.";
                            exit();
                        }
                    }
                } else {
                    echo "<div class=\"challenge\">Somehow a negative number of journeys were found, who knows what happened there</div>";
                    exit();
                }

            ?>

            <?php
                // Challenge block example
                // <div class="row challenge">
                //     <div class="col-6">
                //         <div class="row">
                //             <div class="title col offset-1">
                //                 Land's End to John O'Groats
                //             </div>
                //         </div>
                //         <div class="row"><br></div>
                //         <div class="row offset-1">
                //             25 teams have attempted this challenge
                //         </div>
                //     </div>
                //     <div class="col-2">
                //         1000000km
                //     </div>
                //     <div class="col-3 offset-1">
                //         <a href="../route/route.php?multipleUserID=1&journeyID=1"><button class="openChallenge">Do this challenge!</button></a>
                //     </div>
                // </div>
            ?>
        </div>

        <!-- <script src="userProfile.js"></script> -->
        <!-- <footer>View our cookie policy: https://www.termsfeed.com/cookies-policy/044a9bc1485cc0cf54b509fedb4fa29b</footer> -->
    </body>
</html>