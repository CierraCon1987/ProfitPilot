<!-- Cierra Bailey-Rice (8998948)
     Harpreet Kaur (8893116)
     Gurkamal Singh (9001186) -->

<?php
    session_start();
    include('db_connection.php');

    // Check taskID and projectID are provided
    if (!isset($_GET['task_id']) || !isset($_GET['project_id'])) {
        die("Task ID and Project ID are required.");
    }

    $task_id = $_GET['task_id'];
    $project_id = $_GET['project_id'];

    // Remove the task from the DB
    try {
        $stmt = $pdo->prepare("DELETE FROM tasks WHERE task_id = :task_id");
        $stmt->execute([':task_id' => $task_id]);

        // Redirect back to the project page after successful deletion
        header("Location: edit_project.php?project_id=" . urlencode($project_id));
        exit();
    } catch (PDOException $e) {
        die("Error removing task: " . $e->getMessage());
    }
?>
