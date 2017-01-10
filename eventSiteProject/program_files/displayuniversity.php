<?php
    session_start();
	
    if (session_status() == PHP_SESSION_NONE) {
       header('Location: /index.php');
    }
?>
<html lang="en-US">
	<head>
			<meta charset="UTF-8">
			<meta name="description" content="Dashboard for the Event DB">
			<title>Event Dashboard</title>
			<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
			<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
			<link rel="stylesheet" href="styles.css">
			<style>
				h2.left {text-align: left;color:#1b4275;}
			</style>
	</head>
	<body>
		<div class="container dashboard-tbl">
			<?php
				// Connect to db
				$database = mysqli_connect("events", "root", "", "firstsite");
				if (!$database) {
					echo "Error: Unable to connect to MySQL." . PHP_EOL;
					echo "Debugging errno: " . mysqli_connect_errno() . PHP_EOL;
					echo "Debugging error: " . mysqli_connect_error() . PHP_EOL;
					exit;
				}
			
				$curUser = $_SESSION['username'];
				$unis = [];
				
				$queryDB = "SELECT * FROM university";
				$result = mysqli_query($database, $queryDB);
				$test = mysqli_num_rows($result);
				if($test > 0) {
						while($test != 0){
							$row = mysqli_fetch_assoc($result);
							$unis[] = $row;
							--$test;
						}
				}
				mysqli_free_result($result);
				mysqli_close($database);
			?>
		</div>
		<div class="flex-container">
			<header> 
				<h1 style="text-align:center;"> College Events Dashboard </h1>
				<span><b><?php echo "Welcome, ". $_SESSION['username'] . "<br>";
						if($_SESSION['user_type']=='s'){ echo "Privilege level: Student Account"; }
						elseif($_SESSION['user_type']=='a'){ echo "Privilege level: Admin Account"; }
						elseif($_SESSION['user_type']=='u'){ echo "Privilege level: Public Account"; }
						elseif($_SESSION['user_type']=='sa'){ echo "Privilege level: Super Admin Account"; }?></b></span><br>
				<a href="logout.php" target="_self"> Log Out</a><br>
			</header>
			<nav class="nav">
				<ul>
					<?php
						/* Show the appropriate navigation menu based on user type */ 
					
						if($_SESSION['user_type']== 's'){
							echo " 	<li><b> <a class = \"btn btn-mg btn-primary btn-block\" href=\"dashboard.php\" target=\"_self\"> Dashboard</a></b></li> 
									<li><b> <a class = \"btn btn-mg btn-primary btn-block\" href=\"joinrso.php\" target=\"_self\"> Join RSO</a></b></li> 
									<li><b> <a class = \"btn btn-mg btn-primary btn-block\" href=\"createrso.php\" target=\"_self\"> Create RSO</a><br></b></li><li><b> <a class = \"btn btn-mg btn-primary btn-block\" href=\"displayuniversity.php\" target=\"_self\"> View Participating Universities</a><br></b></li>";
						}
						elseif($_SESSION['user_type']== 'a'){
							echo " 	<li><b> <a class = \"btn btn-mg btn-primary btn-block\" href=\"dashboard.php\" target=\"_self\"> Dashboard</a></b></li> 
									<li><b> <a class = \"btn btn-mg btn-primary btn-block\" href=\"createevent.php\" target=\"_self\"> Create Event</a><br></b></li>
									<li><b> <a class = \"btn btn-mg btn-primary btn-block\" href=\"joinrso.php\" target=\"_self\"> Join RSO</a></b></li>
									<li><b> <a class = \"btn btn-mg btn-primary btn-block\" href=\"createrso.php\" target=\"_self\"> Create RSO</a><br></b></li><li><b> <a class = \"btn btn-mg btn-primary btn-block\" href=\"displayuniversity.php\" target=\"_self\"> View Participating Universities</a><br></b></li>";
						}
						elseif($_SESSION['user_type']== 'sa'){
							echo " 	<li><b> <a class = \"btn btn-mg btn-primary btn-block\" href=\"dashboard.php\" target=\"_self\"> Dashboard</a></b></li> 
									<li><b> <a class = \"btn btn-mg btn-primary btn-block\" href=\"createuniversity.php\" target=\"_self\"> Create University</a><br></b></li><li><b> <a class = \"btn btn-mg btn-primary btn-block\" href=\"displayuniversity.php\" target=\"_self\"> View Participating Universities</a><br></b></li>";
						}
						elseif($_SESSION['user_type']== 'u'){
							echo " 	<li><b> <a class = \"btn btn-mg btn-primary btn-block\" href=\"dashboard.php\" target=\"_self\"> Dashboard</a><br></b></li> 
									<li><b> <a class = \"btn btn-mg btn-primary btn-block\" href=\"displayuniversity.php\" target=\"_self\"> View Participating Universities</a></b></li>
									";
						}
					?>
				</ul>
			</nav>
			<article id="uniContainer" class="article">
				<div>
					<?php
						// Connect to db
						$database = mysqli_connect("events", "root", "", "firstsite");
						if (!$database) {
							echo "Error: Unable to connect to MySQL." . PHP_EOL;
							echo "Debugging errno: " . mysqli_connect_errno() . PHP_EOL;
							echo "Debugging error: " . mysqli_connect_error() . PHP_EOL;
							exit;
						}
						
						/* Display universities on the page */
						
						if(count($unis) > 0){
							foreach($unis as $uni) {
								$uniName = noBreakSpaces($uni['name']);
								echo "<div>";
								echo "<h2 class='left'>" . $uniName . "</h2>";
								echo "<hr>";
								if(isset($uni['description'])){
									echo "<p><b><big>" . $uni['description'] . "</big></b></p>";
								}
								if(isset($uni['location'])){
									echo "Located in " . $uni['location'] . ".<br>";
								}
								if(isset($uni['num_students'])){
									echo "Student population: " . $uni['num_students'] . "<br>";
								}
								echo "</div>";
							}	
						}
						
						function noBreakSpaces($data){
							$data = str_replace("-", "&nbsp;", $data);
							return $data;
						}
					?>
				</div>
			</article>
		</div>
	</body>
</html>