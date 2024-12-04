<!-- Cierra Bailey-Rice (8998948)
     Harpreet Kaur (8893116)
     Gurkamal Singh (9001186) -->

<?php

    include('db_connection.php');
    session_start();

    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }

    $project_id = $_GET['project_id'];

    // Get Project Details
    $stmt = $pdo->prepare("SELECT * FROM Projects WHERE project_id = ?");
    $stmt->execute([$project_id]);
    $project = $stmt->fetch();

    // Fetch related data, such as time tracking, costs, milestones, etc.
    $stmt_time = $pdo->prepare("SELECT * FROM TimeTracking WHERE project_id = ?");
    $stmt_time->execute([$project_id]);
    $time_entries = $stmt_time->fetchAll();

    // Additional queries for costs, milestones, etc., can be added here

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>ProfitPilot | Project Details</title>
</head>

<body>
    <h1><?php echo htmlspecialchars($project['project_name']); ?></h1>
    <h2>Time Entries</h2>
    <ul>
        <?php foreach ($time_entries as $entry): ?>
            <li><?php echo htmlspecialchars($entry['hours']); ?> hours - <?php echo htmlspecialchars($entry['task']); ?></li>
        <?php endforeach; ?>
    </ul>
    <!-- Add more sections for costs, milestones, etc. -->
</body>

</html>
