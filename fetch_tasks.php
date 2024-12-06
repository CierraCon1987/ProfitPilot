<!-- Cierra Bailey-Rice (8998948)
     Harpreet Kaur (8893116)
     Gurkamal Singh (9001186) -->

<?php
    include('db_connection.php');

    if (isset($_GET['project_id'])) {
        $project_id = $_GET['project_id'];
        $stmt = $pdo->prepare("SELECT task_id, task_name FROM tasks WHERE project_id = :project_id");
        $stmt->execute([':project_id' => $project_id]);
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    } else {
        echo json_encode(['error' => 'Invalid project ID.']);
    }
?>
