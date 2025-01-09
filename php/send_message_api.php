<?php
    header('Content-Type: application/json');
    session_start();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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

        // Get data from POST request
        $data = json_decode(file_get_contents('php://input'), true);
        $sender_id = $_SESSION['id']; // Sender is the logged-in user
        $receiver_id = $data['receiver_id'];
        $message = $data['message'];

        if (empty($receiver_id) || empty($message)) {
            echo json_encode(['success' => false, 'message' => 'Receiver ID and message are required']);
            exit;
        }

        // Insert message into the database
        $sql = "INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "iis", $sender_id, $receiver_id, $message);

        if (mysqli_stmt_execute($stmt)) {
            echo json_encode(['success' => true, 'message' => 'Message sent successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to send message']);
        }

        mysqli_close($conn);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    }
?>
