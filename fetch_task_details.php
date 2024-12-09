<?php
    header('Content-Type: application/json');
    ob_start();
    include('db_connection.php');
    ob_clean();

    if (isset($_GET['task_id'])) {
        $task_id = $_GET['task_id'];

        try {
            $stmt = $pdo->prepare("SELECT task_id, task_name, hours_worked, hourly_rate, total_amount, task_description, status 
                                FROM tasks WHERE task_id = :task_id");
            $stmt->execute([':task_id' => $task_id]);
            $taskDetails = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($taskDetails) {
                echo json_encode($taskDetails);
            } else {
                echo json_encode(['error' => 'Task not found']);
            }
        } catch (PDOException $e) {
            echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['error' => 'Task ID not provided.']);
    }
?>

