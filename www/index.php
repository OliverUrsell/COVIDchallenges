<!DOCTYPE HTML>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Route Name</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
        <link rel="stylesheet" href="route/route.php">
        <script src="jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
    </head>
    <body id="test">
        <div id="navbar">Hello world</div>
        <?php
            $start = "John O'Groats";
            $end = "Land's End";
            define("distanceCovered",100);
            define("distanceTotal",200);
            echo "<script>var uluru = {lat: -25.344, lng: 131.036};</script>"
        ?>
        <div id="map" style=""></div>
        <div class="container-fluid">
            <div id="toFromDisplay" class="row">
                <div class="col-xs-4">
                    <?php echo $start . " to " . $end;?>
                </div>
                <div id="progressBar" class="col-xs-6">
                    <div id="progressBarContents"></div>
                </div>
                <div id="letterValues" class="col-xs-2">
                    <?php echo distanceCovered . " / " . distanceTotal . "Km";?>
                </div>
            </div>
        </div>

        <script src="route/route.js"></script>
        <!-- <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAn_3UQjVzZh01LHtMFPnfLFCkKiBK4Joc&callback=initMap"> -->
    </script>
    </body>
</html>