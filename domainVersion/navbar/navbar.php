<head>
	<link rel="stylesheet" href="../navbar/navbar.css">
</head>
<body>
	<div id="navbar" class="container-fluid">
		<div class="row">
			<?php
				$navbarLoggedIn = FALSE;
				if(isset($_SESSION['userID'])){
					$servername = "oliverursell05154.domaincommysql.com";
					$username = "oliver_ursell";
					$password = "Database@52";
					$dbname = "covidchallenges";
					// Create connection
		            $conn = new mysqli($servername, $username, $password, $dbname);
		            // Check connection
		            if ($conn->connect_error) {
		                die("Connection failed: " . $conn->connect_error);
		            }

		            $sql = "SELECT DisplayName FROM tblusers WHERE UserID = " . $conn -> real_escape_string($_SESSION["userID"]);
		            $result = $conn->query($sql);
		            if ($result->num_rows == 0){
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
					echo "<div class=\"col-4 nav-text\">Logged in as: ". htmlspecialchars($row['DisplayName']) ."</div>
					<div class=\"col-2 offset-1\">
						<a href=\"/\"><button class=\"nav-button\">COVID challenges</button></a>
					</div>
					<div class=\"col-4 offset-1\">
						<form method=\"POST\" action='/login/logout.php'>
							<input class=\"nav-button nav-button-right\" type=\"submit\" name=\"button1\"  value=\"Logout\">
						</form> 
						<a href=\"/userProfile/userProfile.php?userID=". htmlspecialchars($_SESSION["userID"]) ."\"><button class=\"nav-button nav-button-right\">My Profile</button></a>
					</div>
					";
					$navbarLoggedIn = TRUE;
				}else{
					// User is not logged in
					echo "<div class=\"col-2 offset-5\">
						<a href=\"/\"><button class=\"nav-button\">COVID challenges</button></a>
					</div>
					<div class=\"col-4 offset-1\">
						<a href=\"/register/register.php\"><button class=\"nav-button nav-button-right\">Register</button></a>
						<a href=\"/login/login.php\"><button class=\"nav-button nav-button-right\">Login</button></a>
					</div>";
				}
			?>
		</div>
	</div>
</body>