<?php session_start();?>

<!DOCTYPE HTML>
<html lang="en">
    <head>
        <meta charset="utf-8"> 
        <title>Setup a new Challenge</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
        <link rel="stylesheet" href="createJourney.css">
        <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    </head>
    <body>
        <div class="bg"></div>

        <?php include '../navbar/navbar.php';?>

        <?php
            if($navbarLoggedIn && isset($_POST["distanceTotal"])){
                // The user is logged in and the post values have been set

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

                // Get new multiple users ID
                $sql = "SELECT `AUTO_INCREMENT` FROM  INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = '". $dbname ."' AND TABLE_NAME = 'tbljourneys';";
                $result = $conn->query($sql);
                $journeyID = $result->fetch_assoc()["AUTO_INCREMENT"];

                $sql = "INSERT INTO `tbljourneys` (`JourneyID`, `DisplayName`, `TotalDistance`) VALUES (NULL,'". $conn->real_escape_string($_POST["challengeName"]) ."', '". $conn->real_escape_string($_POST["distanceTotal"]) ."')";
                if ($conn->query($sql) === TRUE) {
                    // Record updated successfully
                    for ($i=0; $i < $_POST["latLongCount"]; $i++) { 
                         
                        $sql = "INSERT INTO `tbllatlongs` (`JourneyID`, `CoordinateIndex`, `Latitude`, `Longitude`) VALUES ('". $journeyID ."', '". $i ."', '". $_POST["lat". ($i + 1)] ."', '". $_POST["long". ($i + 1)] ."')";
                        if ($conn->query($sql) === TRUE){
                            header('Location: ../shareStartJourney/shareStartJourney.php?journeyID='.htmlspecialchars($journeyID));
                        }else{
                            // Record updated failed
                            echo "Error updating record: " . $conn->error;
                            exit();
                        }
                     } 
                } else {
                    echo "Error updating record: " . $conn->error;
                }

                $conn->close();
            }else{
                echo "<div id=\"register\"> You need to access this page after creating your own challenge from your user profile! (Log In / Register if you haven't already.</div>";
                exit();
            }
        ?>

        <!-- <footer>View our cookie policy: https://www.termsfeed.com/cookies-policy/044a9bc1485cc0cf54b509fedb4fa29b</footer> -->
        <script src="createJourney.js"></script>
    </body>
</html>