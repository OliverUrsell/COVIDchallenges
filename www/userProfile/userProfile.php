<?php session_start();?>

<!DOCTYPE HTML>
<html lang="en">
    <head>
        <meta charset="utf-8"> 
        <title>User's Journeys</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
        <link rel="stylesheet" href="userProfile.css">
        <script src="../jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    </head>
    <body>

        <?php
            include_once("../navbar/navbar.php");

            $loggedIn = FALSE;
            if(isset($_SESSION['userID'])) {
                // User is logged in
                if($_SESSION['userID'] == $_GET['userID']){
                    // this is their profile
                    $loggedIn = TRUE;   
                }
            }

            if(isset($_GET['userID'])){
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

                $sql = "SELECT * FROM tblusers WHERE UserID = " . $conn -> real_escape_string($_GET["userID"]);
                $result = $conn->query($sql);
                if ($result->num_rows == 0){
                    echo "This user could not be found, please try again";
                    exit();
                } elseif ($result->num_rows == 1) {
                    $row = $result->fetch_assoc();
                    if($row['Public'] == 0 && !$loggedIn){
                        //This profile is not public, and we're not the owner
                        echo "This is a private profile. You must be the owner to view it. This user can make their profile public by going to their Profile -> Settings -> Privacy Settings";
                        exit();
                    }
                } else {
                    echo "Duplicate ID found, ID:" . htmlspecialchars($_GET["userID"]) . ". Whoops, this one is on us.";
                    exit();
                }
            }else{
                echo "ERROR:The UserID has not been specified, please return to the previous link and try again.";
                exit();
            }

            $edited = FALSE;
            $passValid = FALSE;
            $updateSuccess = FALSE;
            if(isset($_POST['apply'])){
                $edited = TRUE;
                if(TRUE){
                    $passValid = TRUE;
                    if($_POST['public'] == "Public"){
                        $public = 1;
                    }else{
                        $public = 0;
                    }
                    $newEmail = $conn -> real_escape_string($_POST['email']);
                    if($_POST['password'] == ""){
                        $hash = $conn -> real_escape_string($row['Password']);
                    }else{
                        $newPassword = $conn -> real_escape_string($_POST['password']);
                        // Hash and salt the password
                        $options = [
                            'cost' => 11,
                        ];

                        $hash = password_hash($newPassword, PASSWORD_BCRYPT, $options);
                    }

                    $newDisplayName = $conn -> real_escape_string($_POST['displayName']);

                    // Edit account
                    $sql = "UPDATE `tblusers` SET `Email` = '". $newEmail ."', `Password` = '". $hash ."', `DisplayName` = '". $newDisplayName ."', `Public` = '". $public ."' WHERE `tblusers`.`UserID` = ". $_GET['userID'];
                    
                    if ($conn->query($sql) === TRUE) {
                        // Record updated successfully
                        $updateSuccess = TRUE;
                    } else {
                        echo "Error updating record: " . $conn->error;
                    }
                }
            }
        ?>

        <div class="container-fluid">
            <div class="row">
                <div id="selector" class="col-10 block offset-1">
                    <?php 
                        if($edited){
                            if($passValid){
                                if($updateSuccess){
                                    echo "<span id=\"detailChange\">✓ Details changed successfully</span><br><br>";
                                }else{
                                    echo "<span id=\"detailChangeFailed\">? The update could not be made. Check your internet connection.</span><br><br>";
                                }
                            }else{
                                echo "<span id=\"wrongPass\"> 🗙 The password was incorrect</span><br><br>";
                            }
                        }?>
                    <div id="name" class="row">
                        <div class="col-11 offset-1">
                            <?php if($updateSuccess){echo htmlspecialchars($newDisplayName);}else{echo htmlspecialchars($row["DisplayName"]);} ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <button class="category selected" divid="journeys">Challenges</button>
                        </div>
                        <div class="col">
                            <button class="category" divid="statistics">Statistics</button>
                        </div>
                        
                        <?php
                            if($loggedIn){
                                echo '<div class="col">
                                    <button class="category" divid="settings">Settings</button>
                                </div>';
                            }
                        ?>
                    </div>
                </div>
            </div>
            <div class="row">
                <div id="journeys" class="block col-10 block offset-1">
                    Journey Div
                </div>
                <div id="statistics" class="block col-10 block offset-1">
                    Total distance travelled: 
                </div>
                <div id="settings" class="block col-10 block offset-1">
                    Settings
                    <form action="userProfile.php?userID=<?php echo htmlspecialchars($_GET['userID']); ?>" method="post" oninput='password2.setCustomValidity(password2.value != password.value ? "Passwords do not match." : "");' autocomplete="off"><br>
                        <input type="text" style="display:none">
                        <input type="email" style="display:none">
                        <input type="password" style="display:none">
                        <div class="form-group row">
                            <div class="col-2">
                                <label for="exampleFormControlSelect1">Privacy settings:</label>
                            </div>
                            <div class="col-2">
                                <select name="public" class="form-control" id="exampleFormControlSelect1">
                                    <option><?php if($row["Public"] == 1){echo"Public";}else{echo "Private";}?></option>
                                    <option><?php if($row["Public"] == 0){echo"Public";}else{echo "Private";}?></option>
                                </select>
                            </div>
                            <div class="col-8">
                                Public: Other users / guests can view your profile, including public challenges and combined statistics of public adventures.<br>Private: Other users / guests cannot view your profile, and cannot access public or private challenges through it.
                            </div>
                        </div>
                        <br>
                        <div class="form-group">
                            <input name="email" type="email" class="form-control" aria-describedby="emailHelp" placeholder="Email" value="<?php if($updateSuccess){echo htmlspecialchars($newEmail);}else{echo htmlspecialchars($row["Email"]);} ?>" autocomplete="off" required>
                            <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small>
                        </div>
                        <div class="row">
                            <div class="col">
                                <input name="password" type="password" class="form-control" placeholder="New Password"
                                 autocomplete="off"><br>
                            </div>
                            <div class="col">
                                <input name="password2" type="password" class="form-control" placeholder="Re-type New Password"><br>
                            </div>
                        </div>
                        <small id="emailHelp" class="form-text text-muted">Leave empty to keep old password</small>
                        <input name="displayName" type="text" class="form-control" placeholder="Display Name" value="<?php if($updateSuccess){echo htmlspecialchars($newDisplayName);}else{echo htmlspecialchars($row["DisplayName"]);} ?>" required><br>
                        <div class="row">
                            <div class="col">
                                <input name="validationPassword" type="password" class="form-control" placeholder="Enter your password to change settings">
                            </div>
                            <div class="col">
                                <button name="apply" type="submit" class="btn btn-warning">Apply</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <script src="userProfile.js"></script>
        <!-- <footer>View our cookie policy: https://www.termsfeed.com/cookies-policy/044a9bc1485cc0cf54b509fedb4fa29b</footer> -->
    </body>
</html>