<?php if(session_status() == 0){session_start();} ?>

<!DOCTYPE HTML>
<html lang="en">
    <head>
        <meta charset="utf-8"> 
        <title>Login</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
        <link rel="stylesheet" href="login.css">
        <script src="../jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
    </head>
    <body>
        <div class="bg"></div>
        <div id="navbar">Hello world</div>

        <?php
            // password
            $incorrect = FALSE;

            if(isset($_SESSION["userID"])) {
                //User is already logged in
                header('Location: ../userProfile/userProfile.php?UserID='.htmlspecialchars($_SESSION["userID"]));
                exit(1);
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
                if ($result === null || $result->num_rows == 0){
                    //Acount not found
                    $incorrect = TRUE;
                } elseif ($result->num_rows == 1) {
                    $row = $result->fetch_assoc();
                    // test the password
                    $passwordFromPost = htmlspecialchars($_POST["password"]);
                    $hashedPasswordFromDB = htmlspecialchars($row["Password"]);

                    if (password_verify($passwordFromPost, $hashedPasswordFromDB)) {
                        // Valid password
                        $_SESSION['userID'] = htmlspecialchars($row["UserID"]);
                        header('Location: ../userProfile/userProfile.php?UserID='.htmlspecialchars($row['UserID']));
                        exit(1);
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
            <?php if($incorrect){echo "<span id=\"incorrectMessage\">Email or password is incorrect. Please try again</span><br><br>";}?>
            <form action="login.php" method="post">
                <input name="email" type="email" class="form-control" placeholder="Email" required><br>
                <input name="password" type="password" class="form-control" placeholder="Password" required><br>
                <button name="loginSubmit" type="submit" class="btn btn-primary">Login</button>
            </form>
        </div>

        <!-- <footer>View our cookie policy: https://www.termsfeed.com/cookies-policy/044a9bc1485cc0cf54b509fedb4fa29b</footer> -->
    </script>
    </body>
</html>