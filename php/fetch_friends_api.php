<?php

    header('Content-Type: application/json');
    session_start();

    $user_id = $_SESSION['id']; // Ensure 'id' is correctly set in the session

    $mySQL_Hostname = "hostname";
    $mySQL_Username = "username";
    $mySQL_Password = "password";
    $mySQL_DB_name = "db_name";

    $conn = mysqli_connect($mySQL_Hostname, $mySQL_Username, $mySQL_Password, $mySQL_DB_name);

    if (!$conn) {
        echo json_encode(['success' => false, 'message' => 'Database connection failed']);
        exit;
    }

    // Query to fetch mutual friendships
    $sql = "SELECT users.id, users.name 
            FROM friends 
            JOIN users ON users.id = friends.friend_id 
            WHERE friends.user_id = ? 
            UNION 
            SELECT users.id, users.name 
            FROM friends 
            JOIN users ON users.id = friends.user_id 
            WHERE friends.friend_id = ?";

    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        echo json_encode(['success' => false, 'message' => 'Failed to prepare statement']);
        exit;
    }

    // Bind the user_id parameter twice (for both parts of the UNION query)
    mysqli_stmt_bind_param($stmt, "ii", $user_id, $user_id);

    // Execute the query
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (!$result) {
        echo json_encode(['success' => false, 'message' => 'Query execution failed']);
        exit;
    }

    $friends = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $friends[] = $row;
    }

    echo json_encode(['success' => true, 'friends' => $friends]);

    mysqli_close($conn);

?>
