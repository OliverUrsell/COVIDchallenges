<?php session_start();?>

<!DOCTYPE HTML>
<html lang="en">
    <head>
        <meta charset="utf-8"> 
        <title>Login</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
        <link rel="stylesheet" href="login.css">
        <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    </head>
    <body>
        <div class="bg"></div>

        <?php include '../navbar/navbar.php';?>

        <?php

            // password
            $incorrect = FALSE;

            if(isset($_SESSION["userID"])) {
                //User is already logged in
                header('Location: ../userProfile/userProfile.php?userID='.htmlspecialchars($_SESSION["userID"]));
                exit();
            }

            if(isset($_REQUEST['loginSubmit'])){
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
                $sql = "SELECT UserID, Password FROM tblusers WHERE Email = '". $conn -> real_escape_string($_POST["email"]) ."'";
                $result = $conn->query($sql);
                if ($result->num_rows == 0){
                    //Account not found
                    $incorrect = TRUE;
                } elseif ($result->num_rows == 1) {
                    $row = $result->fetch_assoc();
                    // test the password
                    $passwordFromPost = htmlspecialchars($_POST["password"]);
                    $hashedPasswordFromDB = htmlspecialchars($row["Password"]);

                    if (password_verify($passwordFromPost, $hashedPasswordFromDB)) {
                        // Valid password
                        $_SESSION['userID'] = htmlspecialchars($row["UserID"]);
                        header('Location: ../userProfile/userProfile.php?userID='.htmlspecialchars($row['UserID']));
                        exit();
                    } else {
                        // Invalid password
                        $incorrect = TRUE;
                    }
                } else {
                    $row = $result->fetch_assoc();
                    echo "Duplicate account found, ID:" . htmlspecialchars($row["UserID"]) . ". Whoops, this one is on us.";
                }

                $conn->close();
            }
        ?>

        <div id="login">
            <h2>Login</h2>
            <br>
            <?php if($incorrect){echo "<span id=\"incorrectMessage\"> ! Email or password is incorrect. Please try again</span><br><br>";}?>
            <form action="login.php" method="post">
                <input name="email" type="email" class="form-control form-control-lg" placeholder="Email" required><br>
                <input name="password" type="password" class="form-control form-control-lg" placeholder="Password" required><br>
                <button name="loginSubmit" type="submit" class="btn btn-primary">Login</button>
            </form>
            <button type="button" class="btn btn-link" onclick="window.location.href = '../register/register.php';">New User? Register here</button>
        </div>

        <!-- <footer>View our cookie policy: https://www.termsfeed.com/cookies-policy/044a9bc1485cc0cf54b509fedb4fa29b</footer> -->
    </script>
    </body>
</html>