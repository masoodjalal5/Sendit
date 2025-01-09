<?php
    header('Content-Type: application/json');
    session_start();

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        // Database connection
        $mySQL_Hostname = "hostname";
		$mySQL_Username = "username";
		$mySQL_Password = "password";
		$mySQL_DB_name = "db_name";

        $conn = mysqli_connect($mySQL_Hostname, $mySQL_Username, $mySQL_Password, $mySQL_DB_name);

        if (!$conn) {
            echo json_encode(['success' => false, 'message' => 'Database connection failed']);
            exit;
        }

        $user_id = $_SESSION['id']; // Logged-in user
        $contact_id = $_GET['contact_id'];

        // Fetch messages between the logged-in user and the selected contact
        $sql = "SELECT sender_id, message, timestamp 
                FROM messages 
                WHERE (sender_id = ? AND receiver_id = ?) 
                   OR (sender_id = ? AND receiver_id = ?)
                ORDER BY timestamp ASC";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "iiii", $user_id, $contact_id, $contact_id, $user_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        $messages = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $messages[] = [
                'type' => $row['sender_id'] == $user_id ? 'sent' : 'received',
                'text' => $row['message'],
                'timestamp' => $row['timestamp'],
            ];
        }

        echo json_encode(['success' => true, 'messages' => $messages]);
        mysqli_close($conn);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    }
?>
