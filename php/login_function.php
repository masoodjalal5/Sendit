<?php

    session_start();

    if(isset($_POST['loginButton'])){  // Check if submit button is pressed

        $message = '';

        $email = $_POST['emailField'];
        $password = $_POST['passwordField'];

        $mySQL_Hostname = "hostname";
		$mySQL_Username = "username";
		$mySQL_Password = "password";
		$mySQL_DB_name = "db_name";

        $conn = mysqli_connect($mySQL_Hostname, $mySQL_Username, $mySQL_Password, $mySQL_DB_name);

        if (!$conn) {
            die("Connection failed: " . mysqli_connect_error());
        }

        // Sanitize user input to prevent SQL injection
        $email = mysqli_real_escape_string($conn, $email);


        $sql="SELECT id, name, password FROM users WHERE email = '$email';";
        $result = mysqli_query($conn, $sql);
        
        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);

            // Check if the password matches
            if ($password === $row['password']) {
                
                // save session and redirect to app panel
                $_SESSION['id'] = $row['id'];
                $_SESSION['name'] = $row['name'];
                
                header("Location: ../app.php");
                exit();

            } else {
                $message = "Email or password incorrect.";
            }
        } else {
            $message = "Email not found";
        }
        mysqli_close($conn);
    }
?>