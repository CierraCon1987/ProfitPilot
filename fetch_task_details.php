<!-- Cierra Bailey-Rice (8998948)
     Harpreet Kaur (8893116)
     Gurkamal Singh (9001186) -->

<?php
    include('db_connection.php');

    if (isset($_GET['task_id'])) {
        $task_id = $_GET['task_id'];
        $stmt = $pdo->prepare("SELECT hours_worked, hourly_rate FROM tasks WHERE task_id = :task_id");
        $stmt->execute([':task_id' => $task_id]);
        echo json_encode($stmt->fetch(PDO::FETCH_ASSOC));
    } else {
        echo json_encode(['error' => 'Invalid task ID.']);
    }
?>
