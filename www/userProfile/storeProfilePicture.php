<?php
session_start();

$servername = "localhost";
$username = "Ollie";
$password = "databasepassword";
$dbname = "main";


/* Attempt MySQL server connection. Assuming you are running MySQL
server with default setting (user 'root' with no password) */
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if($conn -> connect_error){
    die("Connection failed: " . $conn->connect_error);
}

//Renaming file to eventID
$filename=$_FILES["image"]["name"];
$fileExploded = explode(".", $filename);
$extension=end($fileExploded);
$newfilename=$_POST["userID"].".".$extension;

$target_dir = "profilePictures/";
$target_file = $target_dir . basename($filename);
$uploadOk = 1;
$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
// Check if image file is a actual image or fake image
if(isset($_POST["submit"])) {
    $check = getimagesize($_FILES["image"]["tmp_name"]);
    if($check !== false) {
        echo "File is an image - " . $check["mime"] . ".";
        $uploadOk = 1;
    } else {
        echo "File is not an image.";
        $uploadOk = 0;
    }
}

// Check if file already exists
$withoutExtension = "profilePictures/". $_POST["userID"];
if(file_exists($withoutExtension .".png")){
    unlink($withoutExtension .".png");
}elseif(file_exists($withoutExtension .".jpg")){
    unlink($withoutExtension .".jpg");
}elseif(file_exists($withoutExtension .".gif")){
    unlink($withoutExtension .".gif");
}

// Check file size
if ($_FILES["image"]["size"] > 500000) {
    echo "Sorry, your file is too large.";
    $uploadOk = 0;
}
// Allow certain file formats
if(!in_array($imageFileType, array("jpg", "png", "gif"))) {
    echo "Sorry, only PNG, JPG or GIF files are allowed.";
    $uploadOk = 0;
}



$target_file = $target_dir . basename($newfilename);

// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {
    echo "Sorry, your file was not uploaded.";
    $_SESSION['status'] = "Event could not be uploaded";
// if everything is ok, try to upload file
} else {
    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
        echo "The file ". basename($newfilename). " has been uploaded.";
    } else {
        echo "Sorry, there was an error uploading your file.";
    }
}



// Close connection
$conn->close();

//Link back to user page
header('Location: userProfile.php?userID='. htmlspecialchars($_POST['userID']));

?>