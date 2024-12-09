<?php
    session_start();
    include('db_connection.php');

    // Check for ProjectID
    if (!isset($_GET['project_id'])) {
        die("Project ID is required.");
    }

    $project_id = $_GET['project_id'];

    // Delete Project from DB
    try {
        $stmt = $pdo->prepare("DELETE FROM tasks WHERE project_id = :project_id");
        $stmt->execute([':project_id' => $project_id]);

        $stmt = $pdo->prepare("DELETE FROM projects WHERE project_id = :project_id");
        $stmt->execute([':project_id' => $project_id]);

        header("Location: dashboard.php");
        exit();
    } catch (PDOException $e) {
        die("Error deleting project: " . $e->getMessage());
    }
?>
