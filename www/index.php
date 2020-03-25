<!DOCTYPE HTML>
<html lang="en">
    <head>
        <meta charset="utf-8"> 
        <title>Route Name</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
        <link rel="stylesheet" href="route/route.css">
        <script src="jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
    </head>
    <body id="test">

        <div id="navbar">Hello world</div>

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
                // output data of each row
                $row = $result->fetch_assoc();
                $startDisplayName = htmlspecialchars($row['StartDisplayName']);
                $endDisplayName = htmlspecialchars($row['EndDisplayName']);
            } else {
                echo "Duplicate ID found, ID:" . htmlspecialchars($_GET["JourneyID"]) . ". Whoops, this one is on us.";
                exit(1);
            }

            $start = explode(",", $row['StartLatLong']);
            $end = explode(",", $row['EndLatLong']);

            $sql = "SELECT * FROM tbljourneysusers WHERE (UserID = ". htmlspecialchars($_GET["UserID"]) ." AND JourneyID = ". htmlspecialchars($_GET["JourneyID"]) .")";
            $result = $conn->query($sql);
            if ($result->num_rows == 1) {
                // output data of each row
                $row = $result->fetch_assoc();
                $distanceCovered = $row["DistanceTravelled"]*10;
            } elseif ($result->num_rows == 0) {
                echo "No data for this user was found on this journey, please return to the previous page and try the link again";
                exit(1);
            } else {
                echo "Duplicate ID found, ID:" . htmlspecialchars($_GET["JourneyID"]) . ". Whoops, this one is on us.";
                exit(1);
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

        <div id="map" style=""></div>
        <div class="container-fluid">
            <div id="routeName" class="row">
                <div class="col-xs-12">
                    <?php echo $startDisplayName . " to " . $endDisplayName;?>
                </div>
            </div>
            <div id="toFromDisplay" class="row">
                <div id="progressBar" class="col-xs-9">
                    <div id="progressBarContents"></div>
                </div>
                <div id="letterValues" class="col-xs-3">
                    There was an error! This should be updated!
                </div>
            </div>
            <div id="actionButtons" class="row">
                <div class="col-xs-2 actionButtonContainer input-lg">
                    <div onclick="$('#config').show('slow');" class="actionButton"><img class="img-responsive" src="route/compassRose.png"></div>
                </div>
                <div class="col-xs-2 actionButtonContainer input-lg">
                    <div onclick="$('#config').show('slow');" class="actionButton"><img class="img-responsive" src="route/open-book-silhouette.jpg"></div>
                </div>
            </div>
        </div>

        <div id="config">
            <form action="route/addUpdate.php" method="post">
                <div class="form-group">
                  <input name="JourneyID" type="hidden" value="<?php echo htmlspecialchars($_GET['JourneyID'])?>">
                  <input name="UserID" type="hidden" value="<?php echo htmlspecialchars($_GET['UserID'])?>">
                  <label for="distanceInput">Distance travelled (Kilometers)</label>
                  <input name="distanceUpdate" type="number" class="form-control" id="distanceInput" aria-describedby="distanceHelp" placeholder="How far did you go?">
                  <small id="distanceHelp" class="form-text text-muted">Should be to maximum two decimal places.</small>
                </div>
                <button name="distanceUpdateSubmit" type="submit" class="btn btn-primary">Update</button>
            </form>
            <button onclick="$('#config').hide('slow');" class="btn btn-danger">Cancel</button>
        </div> 

        <script src="route/route.js"></script>
        <!-- <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAn_3UQjVzZh01LHtMFPnfLFCkKiBK4Joc&callback=initMap"> -->
    </script>
    </body>
</html>