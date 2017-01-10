<?php
    if (session_status() == PHP_SESSION_ACTIVE) {
        header('Location: /index.php');;
    }
?>

<html lang="en-US">

<head>
    <meta charset="UTF-8">
    <meta name="description" content="College Events Registration">
    <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles.css">
	<title>Register Now</title>
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
            $success = $passwordErr = "";
            $username = $password = $password2 = $test = $email = $university = "";
			$name = [];
            $missing_data = array();
			
			
			$queryDB = "SELECT * FROM university";
			$result = mysqli_query($database, $queryDB);
			$test = mysqli_num_rows($result);
			if($test > 0) {
					while($test != 0){
						$row = mysqli_fetch_assoc($result);
						$name[] = $row["name"];
						--$test;
					}
			}
			mysqli_free_result($result);
			
            if ($_SERVER["REQUEST_METHOD"] == "POST"){
               
				$username = $_POST["username"];
				$password = $_POST["password"];
				$password2 = $_POST["password2"];
				if (strcmp($password, $password2) != 0) {
					$missing_data[] = "password";
					$missing_data[] = "password2";
					$passwordErr = $err."Passwords don't match".$end;
				}

                if (empty($missing_data)) {
					
                    $queryNew = "INSERT INTO users (userid, password, user_type) VALUES (?, ?, 'u')";
                    $stmtNew = mysqli_prepare($database, $queryNew);
                    mysqli_stmt_bind_param($stmtNew, "ss", $username, $password);
                    mysqli_stmt_execute($stmtNew);
					$affected_rows1 = mysqli_stmt_affected_rows($stmtNew);
					mysqli_stmt_close($stmtNew);
					
					if(!empty($_POST["uniCheck"])) {
						$university = $_POST["universitySel"];
						$query2 = "INSERT INTO student (student_id, university) VALUES (?, ?)";
						$stmt2 = mysqli_prepare($database, $query2);
						mysqli_stmt_bind_param($stmt2, "ss", $username, $university);
						mysqli_stmt_execute($stmt2);
						mysqli_stmt_close($stmt2);
					}

					if ($affected_rows1 == 1) 
                        $success = $suc."User has been created, please log in to access the site.".$end;
					else
                        $success = $err."Username already exists".$end;
                }
            }
            
            function stripHyphens($data) {
                $data = str_replace("-", " ", $data);
                return $data;
            }
        ?>
    </div>

    <div class="container">
        <h2 style="text-align:center;">SITE REGISTRATION</h2>
        <form class="form-signin" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <?php echo $success; ?>

            <input class="form-control" type="text" name="username" placeholder="Username"
                   required autofocus value="<?php echo $username; ?>"><br>

            <input class="form-control" type="password" name="password" placeholder="Password"
                   required value="<?php echo $password; ?>"><br>

            <?php echo $passwordErr; ?>
            <input class="form-control" type="password" name="password2" placeholder="Confirm Password"
                   required value="<?php echo $password2; ?>"><br>
				   
		    <input id="uniCheck" type="checkbox" name="uniCheck" value="student" onclick="showHideExtra()">
			University Student? </input><br>

			<select id="univSelect" class="form-control" name="universitySel" style="display:none;">
				<?php
				
					for($x = count($name); $x >= 0; $x--){
						if($x == count($name))
							echo "<option value='' selected>" . "Select your School..."  . "</option>";
						else
							echo "<option value='" . $name[$x] . "'>" . stripHyphens($name[$x])  . "</option>";
					}
				?>
			</select><br><br>	   
				   
            <button class="btn btn-lg btn-primary btn-block" type="submit" name="register">Register</button>
        </form>
			
        <form class="form-signin" role="form" action="logout.php">
            <h6>Already have an account?</h6>
            <button class="btn btn-lg btn-primary btn-block" type="submit" name="login">Log In</button>
        </form>
    </div>
</body>
</html>
<script>
function showHideExtra() {
	var y = document.getElementById("univSelect");
    if (y.style.display === 'none') {
		y.style.display = 'inline';
    } else {
		y.style.display = 'none';
    }
}
</script>