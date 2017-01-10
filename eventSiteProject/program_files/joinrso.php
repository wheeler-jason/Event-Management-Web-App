<?php
    session_start();
	
    if (session_status() == PHP_SESSION_NONE) {
       header('Location: /index.php');
    }
?>
<html lang="en-US">
	<head>
		<meta charset="UTF-8">
		<meta name="description" content="Join a RSO">
			<title>Join RSO</title>
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
			
				$err = "<h4 class=\"form-signin-error\">";
				$suc = "<h4 class=\"form-signin-success\">";
				$end = "</h4>";
				$success = $rso = "";
				$rsoErr =  "";
				$missing_data = [];
				$name = [];
				$curUser = $_SESSION['username'];
				
				// Gets user's university
				if($_SESSION['user_type']=='s'){
					$queryUni = "select * from student where student_id = '$curUser'";
					$resultUni1 = mysqli_query($database, $queryUni);
					$row1 = mysqli_fetch_assoc($resultUni1);
					mysqli_free_result($resultUni1);
					$userUni = $row1['university'];
				}
				elseif($_SESSION['user_type']=='a'){
					$queryUni = "select * from admin where admin_id = '$curUser'";
					$resultUni2 = mysqli_query($database, $queryUni);
					$row2 = mysqli_fetch_assoc($resultUni2);
					mysqli_free_result($resultUni2);
					$userUni = $row2['university'];
				}

				$queryDB = "SELECT * FROM rso where university='$userUni'";
				$result = mysqli_query($database, $queryDB);
				if(mysqli_num_rows($result) > 0){
						while($row = mysqli_fetch_assoc($result)){
							array_push($name,$row['name']);
						}
				}
				mysqli_free_result($result);

				if ($_SERVER["REQUEST_METHOD"] == "POST") {
					
					if (empty($_POST["rsoSel"])) {
					$missing_data[] = "RSO";
					$rsoErr = $err."RSO is required".$end;
					} else {
						$rso = $_POST["rsoSel"];
					}
					
					if (empty($missing_data)) {

						$query = "INSERT INTO member (student_id, rso) VALUES (?, ?)";
						$stmt = mysqli_prepare($database, $query);
						mysqli_stmt_bind_param($stmt, "ss", $_SESSION['username'], $rso);
						mysqli_stmt_execute($stmt);
						$affected_rows = mysqli_stmt_affected_rows($stmt);
						if ($affected_rows == 1) {
							mysqli_stmt_close($stmt);
							mysqli_close($database);
							$success = $suc."Success! RSO joined.".$end;
						} 
						else {
							$success = $err."You're already in that RSO.".$end;
							mysqli_stmt_close($stmt);
							mysqli_close($database);
						}
					}
				}
					
				//process input data
				function trim_input($data) {
					$data = trim($data);
					$data = stripslashes($data);
					$data = htmlspecialchars($data);
					return $data;
				}
				// Process output data
				function stripHyphens($data) {
					$data = str_replace("-", " ", $data);
					return $data;
				}
				// Process output data
				function stripSpaces($data) {
					$data = str_replace(" ", "-", $data);
					return $data;
				}
			?>
		</div>
		<div class="flex-container">
			<header> 
				<h1> JOIN RSO </h1>
				<span><b><?php echo "Welcome, ". $_SESSION['username'] . "<br>";
						if($_SESSION['user_type']=='s'){ echo "Privilege level: Student Account";}
						elseif($_SESSION['user_type']=='a'){ echo "Privilege level: Admin Account";}
						elseif($_SESSION['user_type']=='u'){ echo "Privilege level: Public Account"; }
						elseif($_SESSION['user_type']=='sa'){ echo "Privilege level: Super Admin Account";}?></b></span><br>
				
						
				<a href="logout.php" target="_self"> Log Out</a><br>
			</header>
			<nav class="nav">
				<ul>
					
					<?php
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
						elseif($_SESSION['user_type'] == 'u') {
							echo " 	<li><b> <a class = \"btn btn-mg btn-primary btn-block\" href=\"dashboard.php\" target=\"_self\"> Dashboard</a></b></li> 
									";
						}
					?>
				</ul>
			</nav>
			<article class="article">
				<div class="container">
					<form class="form-signin" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
						<?php echo $success; ?>
						<?php echo $rsoErr; ?>
						<select class="form-control" name="rsoSel">
						<?php
							for($x = 0; $x < count($name); $x++){
								if($x == 0)
									echo "<option value='' selected>" . "Select an RSO..." . "</option>";
								echo "<option value=" . $name[$x] . ">" . stripHyphens($name[$x])  . "</option>";
							}	
						?>
						<input class = "btn btn-lg btn-primary btn-block" type="submit" value="Join"></input><br>
					</form>
				</div>
			</article>
		<div>
	</body>
</html>