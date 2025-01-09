<?php

    if(isset($_POST['signUpButton'])){  // Check if submit button is pressed

        $message = '';
        
        $name = $_POST['nameField'];
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
        $name = mysqli_real_escape_string($conn, $name);
        $email = mysqli_real_escape_string($conn, $email);
        $password = mysqli_real_escape_string($conn, $password);

        
        // SQL query to insert data into the database
        $sql = "INSERT INTO users (name, email, password) VALUES ('$name', '$email', '$password')"; 

        if (mysqli_query($conn, $sql)) {
            // login to main screen
            header("Location: ../login.php");
            exit();
            
        } else {
            $message = "Error: " . $sql . "<br>" . mysqli_error($conn);
        }
    
        mysqli_close($conn);
    }
?>