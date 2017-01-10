<?php
    session_start();
	
    if (session_status() == PHP_SESSION_NONE) {
       header('Location: /index.php');
    }
?>
<html lang="en-US">
	<head>
			<meta charset="UTF-8">
			<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
			<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
			<link rel="stylesheet" href="styles.css">
			<style>
				h2.left {text-align: left;color:#1b4275;}
			</style>
		
	</head>
	<body>
		<div id="poop">
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
						 
						$public_events_array = array();
						$univ = $_POST['uName'];
						// Stores all public events into $public_events_array
						$queryDB1 = "select * from event where event_type='Public' AND university='$univ' ORDER BY starttime;";
						$result1 = mysqli_query($database, $queryDB1);
						if(mysqli_num_rows($result1) > 0){
								while($row1 = mysqli_fetch_assoc($result1)){
									array_push($public_events_array, $row1);
								}
						}
						mysqli_free_result($result1);
						mysqli_close($database);
						
						/* Display events on the page */
						
						if(count($public_events_array) > 0){
							echo "<div id='publicDiv2'>";
							echo "<h2 class='left'>Public&nbsp;Events</h2>";
							echo "<hr>";
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
							}
							echo "</div>";
						}
						
						// Process output data
						function stripHyphens($data) {
							$data = str_replace("-", " ", $data);
							return $data;
						}
					?>
		</div>
	</body>
</html>