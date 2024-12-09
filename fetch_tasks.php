<?php
    session_start();
    include('db_connection.php');
    ob_end_clean(); 
    header('Content-Type: application/json');

    // Validate project_id input
    if (!isset($_GET['project_id']) || empty($_GET['project_id'])) {
        echo json_encode(['error' => 'Project ID is required.']);
        exit();
    }

    $project_id = $_GET['project_id'];

    try {
        $stmt = $pdo->prepare("SELECT task_id, task_name FROM tasks WHERE project_id = :project_id");
        $stmt->execute([':project_id' => $project_id]);
        $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($tasks);
    } catch (PDOException $e) {
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
exit();
