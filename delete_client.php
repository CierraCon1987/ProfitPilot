<?php
    session_start();
    include('db_connection.php');

    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }

    // Ensure client_id is provided in the URL
    if (isset($_GET['client_id'])) {
        $client_id = $_GET['client_id'];

        // Delete client from the database
        $stmt = $pdo->prepare("DELETE FROM Clients WHERE client_id = ?");
        $stmt->execute([$client_id]);

        // Redirect to the view clients page after deletion
        header("Location: view_clients.php");
        exit();
    } else {
        // If client_id is not set in the URL, redirect to the view clients page
        header("Location: view_clients.php");
        exit();
    }
?>
