<?php
	session_start();
	
    if (session_status() == PHP_SESSION_NONE) {
       header('Location: /index.php');
    }
?>
<html lang="en-US">
	<head>
		<meta charset="UTF-8">
		<meta name="description" content="create rso">
		<title>Create RSO</title>
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
				$success = $name1 = $name2 = $name3 = $name4 = $rso =  "";
				$nameErr = $userErr = $uniErr= $rsoErr = "";
				$missing_data = [];
				$univerRSO = [];
				$users = [];
				$curUser = $_SESSION['username'];
				
				// Get user's university
				if($_SESSION['user_type']=='s'){
					$queryUni = "select * from student where student_id = '$curUser'";
					$resultUni = mysqli_query($database, $queryUni);
					$row = mysqli_fetch_assoc($resultUni);
					mysqli_free_result($resultUni);
					$userUni = $row['university'];
				}
				elseif($_SESSION['user_type']=='a'){
					$queryUni = "select * from admin where admin_id = '$curUser'";
					$resultUni = mysqli_query($database, $queryUni);
					$row = mysqli_fetch_assoc($resultUni);
					mysqli_free_result($resultUni);
					$userUni = $row['university'];
				}

				if ($_SERVER["REQUEST_METHOD"] == "POST") {	
					
					$name1 = trim_input($_POST["name1TXT"]);
					$name2 = trim_input($_POST["name2TXT"]);
					$name3 = trim_input($_POST["name3TXT"]);
					$name4 = trim_input($_POST["name4TXT"]);
					array_push($users, $curUser, $name1, $name2, $name3, $name4);
				
					// check if username only contains letters and digits
					if ((!preg_match("/^[a-zA-Z0-9]*$/", $name1)) || (!preg_match("/^[a-zA-Z0-9]*$/", $name2)) ||
					(!preg_match("/^[a-zA-Z0-9]*$/", $name3)) || (!preg_match("/^[a-zA-Z0-9]*$/", $name4))){
						$missing_data[] = "name";
						$nameErr = $err."Only letters and digits are allowed for usernames.".$end;
					}
					
					
					if (empty($_POST["rsoName"])) {
						$missing_data[] = "rso";
						$rsoErr = $err."RSO name is required".$end;
					} else {
						$rso = trim_input(stripSpaces($_POST["rsoName"]));
					}
					
					foreach($users as $uRSO) {
							$queryUR = "SELECT * FROM users WHERE userid = '$uRSO'";
							$resultUR = mysqli_query($database, $queryUR);
							if(mysqli_num_rows($resultUR) != 1) {	
								$missing_data[] = "user";
								$userErr = $err."All users must be registered at your university before joining a RSO".$end;
							}
							else {
								$rowUR = mysqli_fetch_assoc($resultUR);
								$userType = $rowUR['user_type'];
								mysqli_free_result($resultUR);
								
								if($userType == "s") {
									$queryUR = "SELECT * FROM student WHERE student_id = '$uRSO'";
									$resultUR = mysqli_query($database, $queryUR);
									$rowUR = mysqli_fetch_assoc($resultUR);
									mysqli_free_result($resultUR);
									$univerRSO[] = $rowUR['university'];
									
								}
								elseif($userType == "a") {
									$queryUR = "SELECT * FROM admin WHERE admin_id = '$uRSO'";
									$resultUR = mysqli_query($database, $queryUR);
									$rowUR = mysqli_fetch_assoc($resultUR);
									mysqli_free_result($resultUR);
									$univerRSO[] = $rowUR['university'];
								}
								else {
									$missing_data[] = "user";
									$userErr = $err."All users must be registered at your university before joining a RSO".$end;	
								}
							}
					}
						$curUni = array_shift($univerRSO);
						while($stuCur = array_shift($univerRSO)) {
							if(strcasecmp($curUni, $stuCur) != 0) {
								$missing_data[] = "university";
								$userErr = $err."Users must all attend your university to join.".$end;
							}
						}
						
					if (empty($missing_data)) {

						$queryAd = "SELECT * FROM admin WHERE admin_id = '$curUser'";
						$resultAd = mysqli_query($database, $queryAd);
						
						if(mysqli_num_rows($resultAd) == 0) {
							$queryCA = "INSERT INTO admin (admin_id, university) VALUES (?, ?)";
							$stmt = mysqli_prepare($database, $queryCA);
							mysqli_stmt_bind_param($stmt, "ss", $curUser, $curUni);
							mysqli_stmt_execute($stmt);
						}
					
						$query = "INSERT INTO rso (name, owned_by, university) VALUES (?, ?, ?)";
						$stmt = mysqli_prepare($database, $query);
						mysqli_stmt_bind_param($stmt, "sss", $rso, $curUser, $userUni);
						mysqli_stmt_execute($stmt);
						
						$affected_rows = mysqli_stmt_affected_rows($stmt);
						if ($affected_rows != 0) {
							$success = $suc."RSO has been created".$end;
							mysqli_stmt_close($stmt);
							
							foreach($users as $name) {
								$queryMember = "INSERT INTO member (student_id, rso) VALUES (?, ?)";
								$stmtMember = mysqli_prepare($database, $queryMember);
								mysqli_stmt_bind_param($stmtMember, "ss", $name, $rso);
								mysqli_stmt_execute($stmtMember);
								mysqli_stmt_close($stmtMember);
							}
						} 
						else {
							$success = $err."RSO already exists".$end;
							mysqli_stmt_close($stmt);
						}
					}
				}
					
				//process input data
				function trim_input($data)
				{
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
				<h1> CREATE RSO </h1>
				<span><b><?php echo "Welcome, ". $_SESSION['username'] . "<br>";
						if($_SESSION['user_type']=='s'){ echo "Privilege level: Student Account";}
						elseif($_SESSION['user_type']=='a'){ echo "Privilege level: Admin Account";}
						elseif($_SESSION['user_type']=='u'){ echo "Privilege level: Public Account"; }
						elseif($_SESSION['user_type']=='sa'){ echo "Privilege level: Super Admin Account";}?></b></span><br>
				
						
				<a href="logout.php" target="_self">Log Out</a><br>
			</header>
			<nav class="nav">
				<ul>
					
					<?php
						if($_SESSION['user_type'] == 's'){
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
						<input class="form-control" type="text" name="rsoName" placeholder="RSO Name" required value="<?php echo $rso; ?>" size=20></input>
						<br><b>Enter usernames of new RSO members:</b><br>
						<small>*Users must attend your university and be registered on the site</small><br>
						<?php echo $nameErr . "<br>". $userErr; ?>
						<input class="form-control" type="text" name="name1TXT" placeholder="user 1" required value="<?php echo $name1; ?>" size=20></input>
						<input class="form-control" type="text" name="name2TXT" placeholder="user 2" required value="<?php echo $name2; ?>" size=20></input>
						<input class="form-control" type="text" name="name3TXT" placeholder="user 3" required value="<?php echo $name3; ?>" size=20></input>
						<input class="form-control" type="text" name="name4TXT" placeholder="user 4" required value="<?php echo $name4; ?>" size=20></input>
						<input class="btn btn-lg btn-primary btn-block" type="submit" value="Create"></input><br>
					</form>
				</div>
			</article>
		<div>
	</body>
</html>