<!-- Cierra Bailey-Rice (8998948)
     Harpreet Kaur (8893116)
     Gurkamal Singh () -->

<?php

    include('db_connection.php');
    session_start();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $project_name = $_POST['project_name'];
        $client_id = $_POST['client_id']; 

        // Insert Project Data into DB
        $stmt = $pdo->prepare("INSERT INTO Projects (project_id, user_id, project_name, client_id) VALUES (?, ?, ?, ?)");
        $stmt->execute([uniqid('PROJ-'), $_SESSION['user_id'], $project_name, $client_id]);

        // Redirect to dashboard
        header("Location: dashboard.php");
        exit();
    }

    // Fetch clients for dropdown
    $stmt = $pdo->prepare("SELECT * FROM Clients");
    $stmt->execute();
    $clients = $stmt->fetchAll();

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
