<?php

    session_start();
    
    header('Content-Type: application/json');
    
    // Assuming the current user's ID is stored in the session
    if (!isset($_SESSION['id'])) {
        echo json_encode(['success' => false, 'message' => 'User not logged in']);
        exit;
    }
    
    $currentUserId = $_SESSION['id'];
    $search_query = $_POST['searchQuery'];

    $mySQL_Hostname = "hostname";
    $mySQL_Username = "username";
    $mySQL_Password = "password";
    $mySQL_DB_name = "db_name";

    $conn = mysqli_connect($mySQL_Hostname, $mySQL_Username, $mySQL_Password, $mySQL_DB_name);

    if (!$conn) {
        // die("Connection failed: " . mysqli_connect_error());
        echo json_encode(['success' => false, 'message' => 'Database connection failed']);
        exit;
    }

    // Sanitize user input to prevent SQL injection
    $search_query = mysqli_real_escape_string($conn, $search_query);


    $sql="SELECT id, name FROM users WHERE name = '$search_query';";
    $result = mysqli_query($conn, $sql);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        
        $friendId = $row['id'];
        $friendName = $row['name'];
    
        // Check if the friendship already exists
        $checkQuery = "
            SELECT * FROM friends 
            WHERE (user_id = $currentUserId AND friend_id = $friendId) 
               OR (user_id = $friendId AND friend_id = $currentUserId)";
        $checkResult = mysqli_query($conn, $checkQuery);
        
        if (mysqli_num_rows($checkResult) == 0) {
            // Add the friendship
            $insertQuery = "INSERT INTO friends (user_id, friend_id) VALUES ($currentUserId, $friendId)";
            if (mysqli_query($conn, $insertQuery)) {
                echo json_encode([
                    'success' => true,
                    'message' => "Friend added: $friendName",
                    'name' => $friendName,
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error adding friend']);
            }
        } else {
            echo json_encode([
                'success' => true,
                'message' => "$friendName is already your friend",
                'name' => $friendName,
            ]);
        }

    } else {
        echo json_encode(['success' => false, 'message' => 'Name not found']);
    }
    mysqli_close($conn);
?>