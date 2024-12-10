<?php
session_start();
include('db_connection.php');

// Check if project ID is provided
if (!isset($_GET['project_id'])) {
    die("Project ID is required.");
}

$project_id = $_GET['project_id'];

// Delete the project from the database
try {
    // First, delete the tasks related to this project (if any)
    $stmt = $pdo->prepare("DELETE FROM tasks WHERE project_id = :project_id");
    $stmt->execute([':project_id' => $project_id]);

    // Then, delete the project itself
    $stmt = $pdo->prepare("DELETE FROM projects WHERE project_id = :project_id");
    $stmt->execute([':project_id' => $project_id]);

    // back to the dashboard after successful deletion
    header("Location: dashboard.php");
    exit();
} catch (PDOException $e) {
    die("Error deleting project: " . $e->getMessage());
}
?>
