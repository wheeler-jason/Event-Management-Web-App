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
			
			<script>
				$(document).ready(function(){
					$("#uniSel").change(function(){
						$("#publicDiv").load("getdashboard.php", {uName:$("#uniSel").val()});
					});
				});
			</script>
	</head>
	<body onload="showOrHide()">
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
				$ismember = $userUni = "";
				$uNames = [];
				
				$queryDB = "SELECT * FROM university";
				$result = mysqli_query($database, $queryDB);
				$test = mysqli_num_rows($result);
				if($test > 0) {
						while($test != 0){
							$row = mysqli_fetch_assoc($result);
							$uNames[] = $row["name"];
							--$test;
						}
				}
				mysqli_free_result($result);
				
				//Gets user's university if applicable
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
			<article id="eventContainer" class="article">
				<div>
					<div id="filterEvents" style="display:none;">
						Filter events by 
						<span align="left"><select id="typeSel" name="typeSel" onchange="myTypeFilter()">
							<option value="default" selected> Access Level </option>
							<option value="Public"> Public </option>
							<option value="Private"> Private </option>
							<option value="RSO"> RSO </option>
						</select></span>
						or by 
						<span align="left"><select id="uniSel" name="universitySel" onchange="myUniFilter()">
							<?php
								for($x = count($uNames); $x >= 0; $x--){
									if($x == count($uNames))
										echo "<option value='default' selected>" . "University"  . "</option>";
									else
										echo "<option value='" . $uNames[$x] . "'>" . stripHyphens($uNames[$x])  . "</option>";
								}
								// Process output data
								function stripHyphens($data) {
									$data = str_replace("-", " ", $data);
									return $data;
								}
							?>
						</select></span>
						<small>*You will only be shown public events for universities that you don't attend.</small><br>
					</div>
					<?php
						// Connect to db
						$database = mysqli_connect("events", "root", "", "firstsite");
						if (!$database) {
							echo "Error: Unable to connect to MySQL." . PHP_EOL;
							echo "Debugging errno: " . mysqli_connect_errno() . PHP_EOL;
							echo "Debugging error: " . mysqli_connect_error() . PHP_EOL;
							exit;
						}
						
						// Declare variables	
						$member_array = []; // All rso_names that this user is a member of
						$member_events_array = array(); 
						$public_events_array = array();
						$private_events_array = array();
						$where_in = "";
						$show_public = false;
						$show_private = false;
						$show_rso = false;
						
						/* Show different events based on user_type */
			
						// Students and admins have the same event viewing privileges
						if($_SESSION['user_type'] == 'a' || $_SESSION['user_type'] == 's'){
							$show_public = true;
							$show_private = true;
							$show_rso = true;
							// Stores all public events into $public_events_array
							$queryDB1 = "select * from event where event_type='Public' ORDER BY university, starttime;";
							$result1 = mysqli_query($database, $queryDB1);
							if(mysqli_num_rows($result1) > 0){
									while($row1 = mysqli_fetch_assoc($result1)){
										array_push($public_events_array, $row1);
									}
							}
							mysqli_free_result($result1);
							
							// Stores all private events that user is affiliated with into $private_events_array
							$queryDB2 = "select * from event where event_type='Private' and university='$userUni' ORDER BY starttime;";
							$result2 = mysqli_query($database, $queryDB2);
							if(mysqli_num_rows($result2) > 0){
									while($row2 = mysqli_fetch_assoc($result2)){
										array_push($private_events_array, $row2);
									}
							}
							mysqli_free_result($result2);
							
							// Stores all rso names that the user is a member of into $member_array 
							$queryDB4 = "select * from member where student_id='$curUser';";
							$result4 = mysqli_query($database, $queryDB4);
							if(mysqli_num_rows($result4) > 0){
									while($row4 = mysqli_fetch_assoc($result4)){
										array_push($member_array,$row4['rso']);
									}
							}
							mysqli_free_result($result4);
	
							// Stores all RSO events relevant to user into $member_events_array
							$queryDB3 = "SELECT * FROM event WHERE event_type='RSO' and rso_name IN (select rso from member where student_id='$curUser') ORDER BY rso_name, starttime;";
							$result3 = mysqli_query($database, $queryDB3);
							
							if(mysqli_num_rows($result3) > 0){
									while($row3 = mysqli_fetch_assoc($result3)){
										array_push($member_events_array, $row3);
									}
							}
							mysqli_free_result($result3);
						}
				
						// Unaffiliated users have access to public events only
						elseif($_SESSION['user_type'] == 'u'){
							$show_public = true;
							// Stores all public events into $public_events_array
							$queryDB1 = "select * from event where event_type='Public' ORDER BY university, starttime;";
							$result1 = mysqli_query($database, $queryDB1);
							if(mysqli_num_rows($result1) > 0){
									while($row1 = mysqli_fetch_assoc($result1)){
										array_push($public_events_array, $row1);
									}
							}
							mysqli_free_result($result1);
						}
						// No events for super admins, they dont have time for that
						elseif($_SESSION['user_type'] == 'sa'){
							
						}
						
						/* Display events on the page */
						
						//$pubArrayLength = count($public_events_array);
						//$privArrayLength = count($private_events_array);
						//$rsoArrayLength = count($member_events_array);
						
						if(count($public_events_array) > 0 && $show_public == true){
							echo "<div id='publicDiv'>";
							echo "<h2 class='left'>Public&nbsp;Events</h2>";
							echo "<hr>";
							// index for the loop - this will uniquely identify elements
							$p = 0;
							foreach($public_events_array as $event){
								echo "<p><b>Event Name</b>: " . $event['name'] . "</p>"; 
								if(isset($event['location'])){
									echo "<p><b>Location name</b>: " . $event['location'] . "</p>"; 
								}
								echo "<p><b>Start time</b>: " . date('m-d-Y h:i A', strtotime($event['starttime'])) . "</p>";
								echo "<p><b>End time</b>: " . date('m-d-Y h:i A', strtotime($event['endtime'])) . "</p>";
								
								if(isset($event['description'])){
									echo "<p><b>Description</b>: " . $event['description'] . "</p>";
								}
								if(isset($event['category'])){
									echo "<p><b>Event category</b>: " . $event['category'] . "</p>"; 
								}
								if(isset($event['phone'])){
									echo "<p><b>Contact phone</b>: " . $event['phone'] . "</p>"; 
								}
								if(isset($event['email'])){
									echo "<p><b>Contact email</b>: " . $event['email'] . "</p>"; 
								}
								echo "<p><b>University Name</b>: " . stripHyphens($event['university']) . "</p>"; 
								echo "<hr>";
								$p += 1;
							}
							$p = 0;
							echo "</div>";
						}
						if(count($private_events_array) > 0 && $show_private == true){
							echo "<div id='privateDiv'>";
							echo "<br><h2 class='left'>Private&nbsp;Events</h2>";
							echo "<hr>";
							foreach($private_events_array as $event){
							
								echo "<p><b>Event Name</b>: " . $event['name'] . "</p>"; 
								if(isset($event['location'])){
									echo "<p><b>Location name</b>: " . $event['location'] . "</p>"; 
								}
								echo "<p><b>Start time</b>: " . date('m-d-Y h:i A', strtotime($event['starttime'])) . "</p>";
								echo "<p><b>End time</b>: " . date('m-d-Y h:i A', strtotime($event['endtime'])) . "</p>";
								
								if(isset($event['description'])){
									echo "<p><b>Description</b>: " . $event['description'] . "</p>";
								}
								if(isset($event['category'])){
									echo "<p><b>Event category</b>: " . $event['category'] . "</p>"; 
								}
								if(isset($event['phone'])){
									echo "<p><b>Contact phone</b>: " . $event['phone'] . "</p>"; 
								}
								if(isset($event['email'])){
									echo "<p><b>Contact email</b>: " . $event['email'] . "</p>"; 
								}
								echo "<hr>";
							}
							echo "</div>";
						}
						if(count($member_events_array) > 0 && $show_rso == true) {
							echo "<div id='rsoDiv'>";
							echo "<br><h2 class='left'>RSO&nbsp;Events</h2>";
							echo "<hr>";
							
							$rsoArrayLength = count($member_events_array);
							foreach($member_events_array as $event){
								// How can I identify each of these uniquely? change the foreach loop to have an index
								echo "<p><b>Event Name</b>: " . $event['name'] . "</p>"; 
								echo "<p><b>RSO Name</b>: " . $event['rso_name'] . "</p>";
								if(isset($event['location'])){
									echo "<p><b>Location name</b>: " . $event['location'] . "</p>"; 
								}
								echo "<p><b>Start time</b>: " . date('m-d-Y h:i A', strtotime($event['starttime'])) . "</p>";
								echo "<p><b>End time</b>: " . date('m-d-Y h:i A', strtotime($event['endtime'])) . "</p>";
								
								if(isset($event['description'])){
									echo "<p><b>Description</b>: " . $event['description'] . "</p>";
								}
								if(isset($event['category'])){
									echo "<p><b>Event category</b>: " . $event['category'] . "</p>"; 
								}
								if(isset($event['phone'])){
									echo "<p><b>Contact phone</b>: " . $event['phone'] . "</p>"; 
								}
								if(isset($event['email'])){
									echo "<p><b>Contact email</b>: " . $event['email'] . "</p>"; 
								}
								echo "<hr>";
							}
							echo "</div>";
						}
					?>
				</div>
			</article>
		</div>
		<input id="userUniElement" type="text" value="<?php echo $userUni;?>" style="visibility:hidden;"/>
		<input id="userTypeElement" type="text" value="<?php echo $_SESSION['user_type'];?>" style="visibility:hidden;"/>
	</body>
</html>
<script>
	function myTypeFilter() {
		var x = document.getElementById("typeSel");
		var pubDiv = document.getElementById("publicDiv");
		var privDiv = document.getElementById("privateDiv");
		var memDiv = document.getElementById("rsoDiv");

		if(x.value == "default") {
			pubDiv.style.display = 'inline';
			privDiv.style.display = 'inline';
			memDiv.style.display = 'inline';
		}
		else if(x.value == "Public") {
			pubDiv.style.display = 'inline';
			privDiv.style.display = 'none';
			memDiv.style.display = 'none';
		}
		else if(x.value == "Private") {
			pubDiv.style.display = 'none';
			privDiv.style.display = 'inline';
			memDiv.style.display = 'none';
		}
		else {
			pubDiv.style.display = 'none';
			privDiv.style.display = 'none';
			memDiv.style.display = 'inline';
		}
	}
	function myUniFilter() {
		var y = document.getElementById("uniSel").value;
		var curUni = document.getElementById("userUniElement").value;
		var pubDiv = document.getElementById("publicDiv");
		var privDiv = document.getElementById("privateDiv");
		var memDiv = document.getElementById("rsoDiv");
		
		if(y.localeCompare(curUni) != 0) {
			pubDiv.style.display = 'inline';
			privDiv.style.display = 'none';
			memDiv.style.display = 'none';
		} else if (y == 'default'){
			location.reload(true);
		}
		else {
			privDiv.style.display = 'inline';
			memDiv.style.display = 'inline';
		}
	}
	
	function showOrHide() {
		var eleFilter = document.getElementById("filterEvents");
		var eleUT = document.getElementById("userTypeElement");
		var usrType = eleUT.value;
		
		if(usrType != 'sa') {
			eleFilter.style.display = 'inline';
		}
	}
</script>