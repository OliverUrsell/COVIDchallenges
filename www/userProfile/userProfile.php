<?php session_start();?>
<?php if(session_status() == 0){session_start();} ?>

<!DOCTYPE HTML>
<html lang="en">
    <head>
        <meta charset="utf-8"> 
        <title>Login</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
        <!-- <link rel="stylesheet" href="login.css"> -->
        <script src="../jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
    </head>
    <body>

        <?php
            include_once("../navbar/navbar.php");
            if(isset($_SESSION['userID'])) {
                //User is logged in
                echo "UserID logged in is: " . htmlspecialchars($_SESSION['userID']);
            } else {
                //User isn't logged in
                echo "User isn't logged in";
            }
        ?>

        <!-- <footer>View our cookie policy: https://www.termsfeed.com/cookies-policy/044a9bc1485cc0cf54b509fedb4fa29b</footer> -->
    </script>
    </body>
</html>