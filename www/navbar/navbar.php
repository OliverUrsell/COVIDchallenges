<head>
	<link rel="stylesheet" href="../navbar/navbar.css">
</head>
<body>
	<div id="navbar" class="container-fluid align-middle">
		<div class="row">
			<div class="col-5"></div>
			<div class="col-2">
				<a href="/"><button class="nav-button">COVID challenges</button></a>
			</div>
			<?php
				if(isset($_SESSION['userID'])){
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

		            $sql = "SELECT FirstName, LastName FROM tblusers WHERE UserID = " . $conn -> real_escape_string($_SESSION["userID"]);
		            $result = $conn->query($sql);
		            if ($result == null){
		                echo "The logged in user was not found";
		                exit(1);
		            } elseif ($result->num_rows == 1) {
		                // set values for journey
		                $row = $result->fetch_assoc();
		            } else {
		                echo "Duplicate ID found, ID:" . htmlspecialchars($_SESSION["userID"]) . ". Whoops, this one is on us.";
		                exit();
		            }
					// User is logged in
					echo "<div class=\"col-3\"></div>
					<div class=\"col-2\">
						<a href=\"../userProfile/userProfile.php?UserID=". $_SESSION["userID"] ."\"><button class=\"nav-button nav-button-right\">". $row["FirstName"] ." ". $row["LastName"] ."</button></a>
					</div>";
				}else{
					// User is not logged in
					
					echo "<div class=\"col-3\"></div>
					<div class=\"col-2\">
						<a href=\"../login/login.php\"><button class=\"nav-button nav-button-right\">Login</button></a>
					</div>";
				}
			?>
		</div>
	</div>
</body>