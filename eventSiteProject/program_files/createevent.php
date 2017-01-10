<?php
    session_start();
	
    if (session_status() == PHP_SESSION_NONE) {
       require_once('index.php');
    }
?>
<html lang="en-US">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="description" content="Creates an event">
		
		<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
		<link rel="stylesheet" type="text/css" href="styles.css">
		
		<style>
				article.article {border-style: solid;}
				h2.left {text-align: justify;}
		</style>
		
		<title>Create Event</title>
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
			
				$err = "<h4 class=\"form-signin-error\">";
				$suc = "<h4 class=\"form-signin-success\">";
				$end = "</h4>";
				$success = "";
				$missing_data = [];
				$name = [];
				
				$queryDB = "SELECT * FROM rso where university='$userUni'";
				$result = mysqli_query($database, $queryDB);
				if(mysqli_num_rows($result) > 0){
						while($row = mysqli_fetch_assoc($result)){
							array_push($name,$row['name']);
						}
				}
				mysqli_free_result($result);
				
				if ($_SERVER["REQUEST_METHOD"] == "POST"){
					$query = "INSERT INTO event (name, category, location, description, phone, email, created_by, event_type, rso_name, university, starttime, endtime) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
					
					if (empty($_POST["categoryTXT"])){
						$category = "";
					} else {
						$category = trim_input($_POST["categoryTXT"]);
					}
					
					if (empty($_POST["locTXT"])){
						$location = "";
					} else {
						$location = trim_input($_POST["locTXT"]);
					}
					
					if (empty($_POST["descTXT"])){
						$description = "";
					} else {
						$description = trim_input($_POST["descTXT"]);
					}
					
					if (empty($_POST["phoneTXT"])){
						$phone = "";
					} else {
						$phone = trim_input($_POST["phoneTXT"]);
					}
					
					if (empty($_POST["emailTXT"])){
						$email = "";
					} else {
						$email = trim_input($_POST["emailTXT"]);
					}
					
					if (empty($_POST["rsoName"])){
						$rsoName = "";
					} else {
						$rsoName = trim_input($_POST["rsoName"]);
					}
					$eName = $_POST["eventnameTXT"];
					$stmt = mysqli_prepare($database, $query);
					mysqli_stmt_bind_param($stmt, "ssssssssssss", $eName, $category, $location, $description, 
					$phone, $email, $_SESSION['username'], $_POST["typeSELECT"], $rsoName, $userUni, $_POST["startdateSel"], $_POST["enddateSel"]);
					mysqli_stmt_execute($stmt);
					$affected_rows = mysqli_stmt_affected_rows($stmt);
					if ($affected_rows == 1) {
						mysqli_stmt_close($stmt);
						mysqli_close($database);
						$success = $suc."You've create an event! Go to your dashboard to view it.".$end;
					} 
					else {
						$success = $err."Error occured while creating event.".$end;
						mysqli_stmt_close($stmt);
						mysqli_close($database);
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
				<h1> CREATE EVENT </h1>
				<span><b><?php echo "Welcome, ". $_SESSION['username'] . "<br>";
						if($_SESSION['user_type']=='s'){ echo "Privilege level: Student Account";}
						elseif($_SESSION['user_type']=='a'){ echo "Privilege level: Admin Account";}
						elseif($_SESSION['user_type']=='u'){ echo "Privilege level: Public Account"; }
						elseif($_SESSION['user_type']=='sa'){ echo "Privilege level: Super Admin Account";} ?></b></span><br>
				
						
				<a href="logout.php" target="_self"> Log Out</a><br>
			</header>
			<nav class="nav">
				<ul>
					<?php
						if($_SESSION['user_type'] == 's') {
							echo " 	<li><b> <a class = \"btn btn-mg btn-primary btn-block\" href=\"dashboard.php\" target=\"_self\"> Dashboard</a></b></li> 
									<li><b> <a class = \"btn btn-mg btn-primary btn-block\" href=\"joinrso.php\" target=\"_self\"> Join RSO</a></b></li> 
									<li><b> <a class = \"btn btn-mg btn-primary btn-block\" href=\"createrso.php\" target=\"_self\"> Create RSO</a><br></b></li><li><b> <a class = \"btn btn-mg btn-primary btn-block\" href=\"displayuniversity.php\" target=\"_self\"> View Participating Universities</a><br></b></li>";
						}
						elseif($_SESSION['user_type'] == 'a') {
							echo " 	<li><b> <a class = \"btn btn-mg btn-primary btn-block\" href=\"dashboard.php\" target=\"_self\"> Dashboard</a></b></li> 
									<li><b> <a class = \"btn btn-mg btn-primary btn-block\" href=\"createevent.php\" target=\"_self\"> Create Event</a><br></b></li>
									<li><b> <a class = \"btn btn-mg btn-primary btn-block\" href=\"joinrso.php\" target=\"_self\"> Join RSO</a></b></li>
									<li><b> <a class = \"btn btn-mg btn-primary btn-block\" href=\"createrso.php\" target=\"_self\"> Create RSO</a><br></b></li><li><b> <a class = \"btn btn-mg btn-primary btn-block\" href=\"displayuniversity.php\" target=\"_self\"> View Participating Universities</a><br></b></li>";
						}
						elseif($_SESSION['user_type'] == 'sa') {
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
				<div class="left">
					<form class="form-signin" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
						<?php echo $success; ?>
						<b>Event Name: </b><br>
						<input type="text" name="eventnameTXT" size=20 required></input><br>
						<b>Start Time: </b><br>
						<input type="datetime-local" name="startdateSel" required></input><br>
						<b>End Time: </b><br>
						<input type="datetime-local" name="enddateSel" required></input><br>
						<b>Description: </b><br>
						<textarea col=200 row=10 name="descTXT"></textarea><br>
						<b>Location Name: </b><br>
						<input type="text" name="locTXT" size=20></input><br>
						<b>Event Category: </b><br>
						<input type="text" name="categoryTXT" size=20></input><br><br>
						<b>Contact Phone: </b><br>
						<input type="text" name="phoneTXT" size=20></input><br><br>
						<b>Contact Email: </b><br>
						<input type="text" name="emailTXT" size=20></input><br><br>
						<b>Event Type: </b><br>
						<select id="typeSel" name="typeSELECT" onchange="showHideExtra()" required>
							<option value="" selected> Select event type... </option>
							<option value="Public"> Public </option>
							<option value="Private"> Private </option>
							<option value="RSO"> RSO </option>
						</select><br><br>
			
						<select id="rsoSel" name="rsoName" style="display:none;">
							<?php
							
								for($x = count($name); $x >= 0; $x--){
									if($x == count($name))
										echo "<option value='' selected>" . "Select your RSO..."  . "</option>";
									else
										echo "<option value='" . $name[$x] . "'>" . stripHyphens($name[$x])  . "</option>";
								}
							?>
						</select><br><br>
						<input class="btn btn-lg btn-primary btn-block" type="submit" value="Submit"></input><br></form>
				</div>
			</article>
		<div>
	</body>	
</html>
<script>
	function showHideExtra() {
		var x = document.getElementById("typeSel");
		var y = x.value;
		var z = document.getElementById("rsoSel");
		if(y == "RSO") {
			if (z.style.display == 'none') {
				z.style.display = 'inline';
			} 
			z.setAttribute("required", "true");
		}
		else if(y == "Public" || y == "Private") {
			if (z.style.display == 'inline') {
				z.style.display = 'none';
				z.removeAttribute("required");
			}
		}
	}
</script>