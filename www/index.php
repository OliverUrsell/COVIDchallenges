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
            $start = "John O'Groats";
            $end = "Land's End Signpost";
            $distanceCovered = (204+20+13)*1000;

            // if(isset($_REQUEST['distanceUpdateSubmit']))
            // {
            //     $distanceCovered += $_POST['distanceUpdate']*1000;
            // }
            
            echo "<script>" .
            "var _origin = {query: \"". $start ."\"};" .
            "var _destination = {query: \"". $end ."\"};" .
            "var distance = " . $distanceCovered . ";" .
            "</script>";
        ?>

        <div id="map" style=""></div>
        <div class="container-fluid">
            <div id="routeName" class="row">
                <div class="col-xs-12">
                    <?php echo $start . " to " . $end;?>
                </div>
            </div>
            <div id="toFromDisplay" class="row">
                <div id="progressBar" class="col-xs-10">
                    <div id="progressBarContents"></div>
                </div>
                <div id="letterValues" class="col-xs-2">
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
                  <label for="distanceInput">Distance travelled (Kilometers)</label>
                  <input name="distanceUpdate" type="number" class="form-control" id="distanceInput" aria-describedby="distanceHelp" placeholder="How far did you go?">
                  <small id="distanceHelp" class="form-text text-muted">Should be to maximum two decimal places.</small>
                </div>
                <button name="distanceUpdateSubmit" type="submit" class="btn btn-primary">Update</button>
            </form>
            <button onclick="$('#config').hide('slow');" class="btn btn-danger">Cancel</button>
        </div> 

        <script src="route/route.js"></script>
        <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAn_3UQjVzZh01LHtMFPnfLFCkKiBK4Joc&callback=initMap">
    </script>
    </body>
</html>