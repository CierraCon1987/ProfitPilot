<?php
    session_start();
    include('db_connection.php');

    // Fetch project details
    $project_id = $_GET['project_id'];
    $sql_project = "SELECT * FROM projects WHERE project_id = ?";
    $stmt = $pdo->prepare($sql_project);
    $stmt->execute([$project_id]);
    $project = $stmt->fetch();

    // Fetch tasks and calculations for the project
    $sql_tasks = "SELECT * FROM tasks WHERE project_id = ?";
    $stmt_tasks = $pdo->prepare($sql_tasks);
    $stmt_tasks->execute([$project_id]);
    $tasks = $stmt_tasks->fetchAll();

    $sql_calcs = "SELECT * FROM calculations WHERE project_id = ?";
    $stmt_calcs = $pdo->prepare($sql_calcs);
    $stmt_calcs->execute([$project_id]);
    $calculations = $stmt_calcs->fetchAll();
?>

<?php include('header.php'); ?>

<h2>Project: <?= $project['project_name'] ?></h2>
<p>Client: <?= $project['client_name'] ?></p>
<p>Status: <?= $project['status'] ?></p>
<p>Start Date: <?= $project['start_date'] ?></p>

<h3>Tasks</h3>
<ul>
    <?php foreach ($tasks as $task): ?>
        <li><?= $task['task_name'] ?> - <?= $task['hours_worked'] ?> hours</li>
    <?php endforeach; ?>
</ul>

<h3>Calculations</h3>
<ul>
    <?php foreach ($calculations as $calc): ?>
        <li>Total Hours: <?= $calc['total_hours'] ?> | Total Amount: <?= $calc['total_amount'] ?></li>
    <?php endforeach; ?>
</ul>

<?php include('footer.php'); ?>
