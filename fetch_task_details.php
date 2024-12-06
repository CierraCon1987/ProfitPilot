<?php
// fetch_task_details.php

// Ensure the user is logged in
session_start();
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["error" => "User not logged in"]);
    exit();
}

include('db_connection.php');

if (isset($_GET['task_id'])) {
    $task_id = $_GET['task_id'];

    // Fetch the task details from the database
    $stmt = $pdo->prepare("SELECT total_hours, total_rate FROM tasks WHERE task_id = :task_id AND user_id = :user_id");
    $stmt->execute([
        ':task_id' => $task_id,
        ':user_id' => $_SESSION['user_id']
    ]);

    $task = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($task) {
        // Return the task details as a JSON response
        echo json_encode($task);
    } else {
        echo json_encode(["error" => "Task not found"]);
    }
} else {
    echo json_encode(["error" => "Task ID not provided"]);
}
?>
