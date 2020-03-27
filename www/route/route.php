<?php session_start();?>
<!-- <?php if(session_status() == 0){session_start();} ?> -->

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

            $sql = "SELECT * FROM tbljourneys WHERE JourneyID = " . $conn -> real_escape_string($_GET["JourneyID"]);
            $result = $conn->query($sql);
            if ($result == null){
                echo "Journey not found, please return to the previous page and try the link again";
                exit(1);
            } elseif ($result->num_rows == 1) {
                // set values for journey
                $row = $result->fetch_assoc();
                $startDisplayName = htmlspecialchars($row['StartDisplayName']);
                $endDisplayName = htmlspecialchars($row['EndDisplayName']);
            } else {
                echo "Duplicate ID found, ID:" . htmlspecialchars($_GET["JourneyID"]) . ". Whoops, this one is on us.";
                exit();
            }

            // $result = $conn->query("SELECT COUNT(*) as total FROM tbljourneysusers WHERE JourneyID = " . $conn -> real_escape_string($_GET['JourneyID']));
            // $row = $result->fetch_assoc();
            // echo $row['total'];

            $start = explode(",", $row['StartLatLong']);
            $end = explode(",", $row['EndLatLong']);

            $sql = "SELECT * FROM tbljourneysusers WHERE (UserID = ". htmlspecialchars($_GET["UserID"]) ." AND JourneyID = ". htmlspecialchars($_GET["JourneyID"]) .")";
            $result = $conn->query($sql);
            if ($result->num_rows == 1) {
                // assign the covered distance
                $row = $result->fetch_assoc();
                $distanceCovered = $row["DistanceTravelled"]*10;
            } elseif ($result->num_rows == 0) {
                echo "No data for this user was found on this journey, please return to the previous page and try the link again";
                exit();
            } else {
                echo "Duplicate ID found, ID:" . htmlspecialchars($_GET["JourneyID"]) . ". Whoops, this one is on us.";
                exit();
            }
            
            $conn->close();

            //{lat: 50.066093, lng: -5.715103}

            // if(isset($_REQUEST['distanceUpdateSubmit']))
            // {
            //     $distanceCovered += $_POST['distanceUpdate']*1000;
            // }
            
            echo "<script>" .
            "var _origin = {lat:".$start[0].",lng:".$start[1]."};" .
            "var _destination = {lat:".$end[0].",lng:".$end[1]."};" .
            "var distance = " . $distanceCovered . ";" .
            "</script>";
        ?>

        <div id="map"></div>
        <div class="container-fluid">
            <div id="routeName" class="row">
                <div class="col-12">
                    <?php echo $startDisplayName . " to " . $endDisplayName;?>
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
            <div id="actionButtons" class="row">
                <div class="col-2 actionButtonContainer input-lg">
                    <div onclick="$('#config').show('fast');" class="actionButton"><img class="img-fluid" src="compassRose.png"></div>
                </div>
                <div class="col-2 actionButtonContainer input-lg">
                    <div onclick="$('#config').show('fast');" class="actionButton"><img class="img-fluid" src="open-book-silhouette.jpg"></div>
                </div>
                <div class="col-8"></div>
            </div>
        </div>

        <div id="config">
            <form action="addUpdate.php" method="post">
                <div class="form-group">
                  <input name="journeyID" type="hidden" value="<?php echo htmlspecialchars($_GET['JourneyID'])?>">
                  <input name="userID" type="hidden" value="<?php echo htmlspecialchars($_GET['UserID'])?>">
                  <input name="distanceCovered" type="hidden" value="<?php echo $distanceCovered?>">
                  <label for="distanceInput">Distance travelled (Kilometers)</label>
                  <input name="distanceUpdate" type="number" class="form-control" id="distanceInput" aria-describedby="distanceHelp" placeholder="How far did you go?" required>
                  <small id="distanceHelp" class="form-text text-muted">Should be to maximum two decimal places.</small>
                </div>
                <button name="distanceUpdateSubmit" type="submit" class="btn btn-primary">Update</button>
            </form>
            <br>
            <button onclick="$('#config').hide('fast');" class="btn btn-danger">Cancel</button>
        </div>

        <!-- <footer>View our cookie policy: https://www.termsfeed.com/cookies-policy/044a9bc1485cc0cf54b509fedb4fa29b</footer> -->

        <script src="route.js"></script>
        <!-- <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAn_3UQjVzZh01LHtMFPnfLFCkKiBK4Joc&callback=initMap"> -->
    </script>
    </body>
</html>