<?php
    //Start new session if there is none
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    //Otherwise, logout of current session
    } else {
        header('Location: /logout.php');;
    }
?>

<html lang="en-US">

<head>
    <meta charset="UTF-8">
    <meta name="description" content="College Events Login">
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
			$end = "</h4>";

			$success = $usernameErr = $passwordErr = "";
			$username = $password = "";
			$missing_data = array();

			if ($_SERVER["REQUEST_METHOD"] == "POST"){
				
				$username = trim_input($_POST["username"]);
				$password = trim_input($_POST["password"]);

				if (empty($missing_data)) {
					
					$query = "SELECT * FROM users WHERE userid = '$username' AND password = '$password'";
					$check_user = mysqli_query($database, $query);
					
					if(mysqli_num_rows($check_user)>= 1){
						$row = mysqli_fetch_assoc($check_user);
						$success = $err."SUCCESS!".$end;
						$_SESSION['valid'] = true;
						$_SESSION['time'] = time();
						$_SESSION['username'] = $username;
						$_SESSION['password'] = $password;
						$_SESSION['user_type'] = $row['user_type'];
						mysqli_free_result($check_user);
						mysqli_close($database);
						header('Location: /dashboard.php');
					} else {
						$success = $err."Invalid username/password".$end;
						mysqli_free_result($check_user);
						mysqli_close($database);
					}
				}
			}

			function trim_input($data){
				$data = trim($data);
				$data = stripslashes($data);
				$data = htmlspecialchars($data);
				return $data;
			}
        ?>
    </div>
    
    <div class="container">
        <h2 style="text-align:center;">EVENT SITE LOGIN</h2>
        <form class="form-signin" role="form" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <?php echo $success; ?>

            <input class="form-control" type="text" name="username" placeholder="Username"
                   required autofocus value="<?php echo $username; ?>"><br>

            <input class="form-control" type="password" name="password" placeholder="Password"
                   required value="<?php echo $password;?>"><br>

            <button class="btn btn-lg btn-primary btn-block" type="submit" name="login">Login</button>
        </form>
		<br><br><br>

        <form class="form-signin" role="form" action="register.php">
            <h6 class="form-signin-heading">Don't have an account?</h2>
            <button class="btn btn-lg btn-primary btn-block" type="submit" name="register">Register</button>
        </form>
    </div>
</body>
</html>