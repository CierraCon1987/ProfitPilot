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

try {
    $stmt = $pdo->prepare("
        SELECT Projects.project_name, Projects.start_date, Projects.end_date, Projects.description, 
               Clients.name AS client_name, Clients.email AS client_email, Clients.phone AS client_phone 
        FROM Projects
        JOIN Clients ON Projects.client_id = Clients.client_id
        WHERE Projects.user_id = ?
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $projects = $stmt->fetchAll();
} catch (PDOException $e) {
    echo "Error fetching data: " . $e->getMessage();
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Projects</title>

    <!-- Custom Styling -->
    <link rel="stylesheet" href="mainstyle.css">
</head>
<body>

    <header>
        <div id="admin">
            <h1>ProfitPilot</h1>
            <div class="buttons">
                <a href="dashboard.php" class="button">Dashboard</a>
                <a href="add_client.php" class="button">Add Client</a>
                <a href="logout.php" class="button">Logout</a>
            </div>
        </div>
    </header>

    <main>
        <h2>Your Projects</h2>

    <?php if (empty($projects)): ?>
        <p>No projects found!</p>
    <?php else: ?>
        <table border="1">
            <thead>
                <tr>
                    <th>Project Name</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Description</th>
                    <th>Client Name</th>
                    <th>Client Email</th>
                    <th>Client Phone</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($projects as $project): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($project['project_name']); ?></td>
                        <td><?php echo htmlspecialchars($project['start_date']); ?></td>
                        <td><?php echo htmlspecialchars($project['end_date']); ?></td>
                        <td><?php echo htmlspecialchars($project['description']); ?></td>
                        <td><?php echo htmlspecialchars($project['client_name']); ?></td>
                        <td><?php echo htmlspecialchars($project['client_email']); ?></td>
                        <td><?php echo htmlspecialchars($project['client_phone']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
    </main>
</body>
</html>
