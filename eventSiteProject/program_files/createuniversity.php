<?php
    session_start();
	if (session_status() == PHP_SESSION_NONE) {
       header('Location: /index.php');
    }
?>
<html lang="en-US">
	<head>
		<meta charset="UTF-8">
		<meta name="description" content="Creates a new university (super admin)">
			<title>Create A University</title>
			<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
			<link rel="stylesheet" href="styles.css">
	</head>
	
	<body>
		<div class="container form-signin">
			<?php       
			
				// Connect to db
				$database = mysqli_connect("events", "root", "", "firstsite");
				if (!$database) {
					echo "Error: Unable to connect to MySQL." . PHP_EOL;
					echo "Debugging errno: " . mysqli_connect_errno() . PHP_EOL;
					echo "Debugging error: " . mysqli_connect_error() . PHP_EOL;
					exit;
				}
			
				//HTML tags for error messages
				$err = "<h4 class=\"form-signin-error\">";
				$suc = "<h4 class=\"form-signin-success\">";
				$end = "</h4>";
				$success = $uName = $uLoc = $uDesc = $rssURL = $numStu = $imgURL = "";
				
						
				if ($_SERVER["REQUEST_METHOD"] == "POST") {
					
					$uName = $_POST["name"];
					$uLoc = $_POST["location"];
					
					if (!empty($_POST["description"])) {
						$uDesc = $_POST["description"];
					}
					if (!empty($_POST["rss"])) {
						$rssURL = $_POST["rss"];
					}
					if (!empty($_POST["students"])) {
						$numStu = $_POST["students"];
					}
					/*
					if (!empty($_POST["image"])) {
						$imgURL = $_POST["image"];
					}
					*/
					
					$query = "INSERT INTO university (name, rss_url, description, num_students, location) VALUES (?, ?, ?, ?, ?)";
					$stmt = mysqli_prepare($database, $query);
					mysqli_stmt_bind_param($stmt, "sssss", $uName, $rssURL, $uDesc, $numStu, $uLoc);
					mysqli_stmt_execute($stmt);
					$affected_rows = mysqli_stmt_affected_rows($stmt);
					if ($affected_rows == 1) {
						$success = $suc."University has been successfully created".$end;
						mysqli_stmt_close($stmt);
						mysqli_close($database);
					} else {
						$success = $err."University already exists".$end;
						mysqli_stmt_close($stmt);
						mysqli_close($database);
					}
				}
			?>
		</div>
		<div class="flex-container">
			<header> 
				<h1> CREATE A UNIVERSITY PROFILE</h1>
				<span><b><?php echo "Welcome, ". $_SESSION['username'] . "<br>";
						if($_SESSION['user_type']=='s'){ echo "Privilege level: Student Account";}
						elseif($_SESSION['user_type']=='a'){ echo "Privilege level: Admin Account";}
						elseif($_SESSION['user_type']=='u'){ echo "Privilege level: Public Account";}
						elseif($_SESSION['user_type']=='sa'){ echo "Privilege level: Super Admin Account";} ?></b></span><br>
	
				<a href="logout.php" target="_self">Log Out</a><br>
			</header>
			<nav class="nav">
				<ul>
					<?php
						if($_SESSION['user_type']== 'sa'){
							echo " 	<li><b> <a class = \"btn btn-mg btn-primary btn-block\" href=\"dashboard.php\" target=\"_self\"> Dashboard</a></b></li> 
									<li><b> <a class = \"btn btn-mg btn-primary btn-block\" href=\"createuniversity.php\" target=\"_self\"> Create University</a><br></b></li><li><b> <a class = \"btn btn-mg btn-primary btn-block\" href=\"displayuniversity.php\" target=\"_self\"> View Participating Universities</a><br></b></li>";
						}
						else {
							echo "<h1 style='color:red;'>You shouldn't be here!</h1>";
							sleep(3);
							header('Location: /index.php');
						}
					?>
				</ul>
			</nav>
			<article class="article">
				<div class="container">
					<form class="form-signin" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
						<?php echo $success; ?>
						<b>University Name: </b>
						<input class="form-control" type="text" name="name" size=20 required></input><br>
						<b>University Location: </b>
						<input class="form-control" type="text" name="location" size=20 required></input><br>
						<b>University description: </b>
						<textarea class="form-control" col=200 row=10 name="description"></textarea><br>
						<b>RSS Event Feed URL: </b>
						<input class="form-control" type="text" name="rss" size=20></input><br>
						<b>Number of Students: </b>
						<input class="form-control" type="text" name="students" size=20></input><br>
						<!-- <b>Image URL: </b>
						<input class="form-control" type="text" name="image" size=20></input><br> -->
						<input class="btn btn-lg btn-primary btn-block" type="submit" value="Submit"></input><br>
					</form>
				</div>
			</article>
		</div>
	</body>	
</html>