<?php
    session_start();
    
    // Destroy the session to log out the user
    session_unset(); // Unset all session variables
    session_destroy(); // Destroy the session
    
    header("Location: login.php");
    exit();
?>