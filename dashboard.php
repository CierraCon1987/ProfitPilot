<!-- Cierra Bailey-Rice (8998948)
     Harpreet Kaur (8893116)
     Gurkamal Singh () -->

<?php

    include('db_connection.php');
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}


    // Projects related to the logged-in user
    $stmt = $pdo->prepare("SELECT * FROM Projects WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $projects = $stmt->fetchAll();

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title> ProfitPilot | Dashboard</title>
</head>

<body>
    <header>
    <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
    <a href="logout.php">Logout</a>
</header>
<main>
    <h2>Your Projects</h2>
    <ul>
        <?php foreach ($projects as $project): ?>
            <li><?php echo htmlspecialchars($project['project_name']); ?></li>
        <?php endforeach; ?>
    </ul>
        </main>
</body>

</html>
