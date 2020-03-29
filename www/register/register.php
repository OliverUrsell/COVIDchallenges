<?php session_start();?>

<!DOCTYPE HTML>
<html lang="en">
    <head>
        <meta charset="utf-8"> 
        <title>Register</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
        <link rel="stylesheet" href="register.css">
        <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    </head>
    <body>
        <div class="bg"></div>

        <?php include '../navbar/navbar.php';?>

        <!-- <?php
            $options = [
                'cost' => 11,
            ];
            // Get the password from post
            $passwordFromPost = "password";

            echo password_hash($passwordFromPost, PASSWORD_BCRYPT, $options);
        ?> -->

        <?php

            // password
            $taken = FALSE;

            if(isset($_SESSION["userID"])) {
                //User is already logged in
                header('Location: ../userProfile/userProfile.php?UserID='.htmlspecialchars($_SESSION["userID"]));
                exit();
            }

            if(isset($_REQUEST['registerSubmit'])){
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

                $result = $conn->query("SELECT COUNT(*) as total FROM tblusers WHERE Email = '". $conn -> real_escape_string($_POST["email"]) ."'");
                $row = $result->fetch_assoc();
                if($row['total'] != 0){
                    // The email the user has submitted is already being used
                    $taken = TRUE;
                }else{
                    // The account is new
                    //Insert the new account, set the session variable and redirect to the new userpage
                    // Hash and salt the password
                    $options = [
                        'cost' => 11,
                    ];
                    $passwordFromPost = $conn -> real_escape_string($_POST["password"]);

                    $hash = password_hash($passwordFromPost, PASSWORD_BCRYPT, $options);

                    // Create new account
                    $sql = "INSERT INTO tblusers (UserID, Email, Password, DisplayName) VALUES (NULL, '". $conn -> real_escape_string($_POST["email"]) ."', '". $hash ."', '". $conn -> real_escape_string($_POST["displayName"]) ."')";
                    if ($conn->query($sql) === TRUE) {
                        // Record updated successfully
                        //Log user in
                        $sql = "SELECT UserID, Password FROM tblusers WHERE Email = '". $conn -> real_escape_string($_POST["email"]) ."'";
                        $result = $conn->query($sql);
                        if ($result->num_rows == 0){
                            //Acount not found
                            echo "Account not successfully created";
                        } elseif ($result->num_rows == 1) {
                            // Account ID found
                            $row = $result->fetch_assoc();
                            $_SESSION['userID'] = htmlspecialchars($row["UserID"]);
                            header('Location: ../userProfile/userProfile.php?UserID='.htmlspecialchars($row['UserID']));
                            exit();
                        } else {
                            $row = $result->fetch_assoc();
                            echo "Duplicate account found, ID:" . htmlspecialchars($row["UserID"]) . ". Whoops, this one is on us.";
                        }
                    } else {
                        echo "Error updating record: " . $conn->error;
                    }

                    $conn->close();
                }

                $conn->close();
            }
        ?>

        <div id="register">
            <h2>Register</h2><br>
            <?php if($taken){echo "<span id=\"emailTaken\"> ! That email has already been used.</span><br><br>";}?>
            <form action="register.php" method="post" oninput='password2.setCustomValidity(password2.value != password.value ? "Passwords do not match." : "")'>
                <div class="form-group">
                    <input name="email" type="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Email" required>
                    <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small>
                </div>
                <input name="password" type="password" class="form-control" placeholder="Password" required><br>
                <input name="password2" type="password" class="form-control" placeholder="Re-type Password" required><br>
                <input name="displayName" type="text" class="form-control" placeholder="Display Name" required><br>
                <button name="registerSubmit" type="submit" class="btn btn-warning">Register</button>
            </form>

            <button type="button" class="btn btn-link" onclick="window.location.href = '../login/login.php';">Already got an account? Log in here</button>
        </div>

        <!-- <footer>View our cookie policy: https://www.termsfeed.com/cookies-policy/044a9bc1485cc0cf54b509fedb4fa29b</footer> -->
    </script>
    </body>
</html>