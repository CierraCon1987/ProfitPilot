<!-- Cierra Bailey-Rice (8998948)
     Harpreet Kaur (8893116)
     Gurkamal Singh () -->
     
     <?php
session_start();

if (!isset($_SESSION['user_id'])) {
    // Redirect if the user is not logged in
    header("Location: login.php");
    exit();
}

include('db_connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $project_name = htmlspecialchars($_POST['project_name']);

    // Ensure user_id is set in the session
    if (!isset($_SESSION['user_id'])) {
        echo "Error: user_id is not set in the session.";
        exit();
    }

    try {
        // Adjust SQL query to match your database schema
        $stmt = $pdo->prepare("INSERT INTO Projects (user_id, project_name) VALUES (?, ?)");
        $stmt->execute([$_SESSION['user_id'], $project_name]);

        // Redirect to dashboard after successful creation
        header("Location: dashboard.php");
        exit();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>ProfitPilot | Create Project</title>
</head>

<body>
    <h1>Create New Project</h1>
    <form method="post">
        <label for="project_name">Project Name:</label>
        <input type="text" id="project_name" name="project_name" required>
        <br>
        <label for="client_id">Client:</label>
        <select id="client_id" name="client_id">
            <?php foreach ($clients as $client): ?>
                <option value="<?php echo $client['client_id']; ?>"><?php echo $client['client_name']; ?></option>
            <?php endforeach; ?>
        </select>
        <br>
        <input type="submit" value="Create Project">
    </form>
</body>

</html>
