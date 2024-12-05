<!-- Cierra Bailey-Rice (8998948)
     Harpreet Kaur (8893116)
     Gurkamal Singh (9001186) -->

<?php
    session_start();
    require 'db_connection.php';

    if (!isset($_SESSION['user_id'])) {
        die("Access Denied: Please log in.");
    }

    $user_id = $_SESSION['user_id'];

    $clients_query = $pdo->prepare("SELECT client_id, client_name FROM Clients");
    $clients_query->execute();
    $clients = $clients_query->fetchAll(PDO::FETCH_ASSOC);

    $projects_query = $pdo->prepare("SELECT * FROM Projects WHERE user_id = :user_id");
    $projects_query->bindParam(':user_id', $user_id);
    $projects_query->execute();
    $projects = $projects_query->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Collection</title>
    
    <!-- Custom Styling -->
    <link rel="stylesheet" href="mainstyle.css">
</head>

<body>
    <h1>Client and Project Management</h1>
    
    <h2>Add New Client</h2>
    <form action="add_client.php" method="post">
        <label for="client_name">Client Name:</label>
        <input type="text" name="client_name" required>
        <label for="contact_name">Contact Name:</label>
        <input type="text" name="contact_name">
        <label for="email">Email:</label>
        <input type="email" name="email">
        <label for="phone_number">Phone Number:</label>
        <input type="text" name="phone_number">
        <label for="address">Address:</label>
        <textarea name="address"></textarea>
        <button type="submit">Add Client</button>
    </form>
    
    <h2>Add New Project</h2>
    <form action="add_project.php" method="post">
        <label for="project_name">Project Name:</label>
        <input type="text" name="project_name" required>
        <label for="client_id">Client:</label>
        <select name="client_id" required>
            <?php foreach ($clients as $client): ?>
                <option value="<?= $client['client_id']; ?>"><?= $client['client_name']; ?></option>
            <?php endforeach; ?>
        </select>
        <label for="start_date">Start Date:</label>
        <input type="date" name="start_date" required>
        <label for="end_date">End Date:</label>
        <input type="date" name="end_date">
        <label for="budget">Budget:</label>
        <input type="number" step="0.01" name="budget" required>
        <label for="status">Status:</label>
        <select name="status">
            <option value="Planning">Planning</option>
            <option value="In Progress">In Progress</option>
            <option value="Completed">Completed</option>
            <option value="On Hold">On Hold</option>
        </select>
        <button type="submit">Add Project</button>
    </form>
    
    <h2>Existing Projects</h2>
    <table border="1">
        <thead>
            <tr>
                <th>Project Name</th>
                <th>Client</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Budget</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($projects as $project): ?>
                <tr>
                    <td><?= $project['project_name']; ?></td>
                    <td>
                        <?php
                        $client_query = $pdo->prepare("SELECT client_name FROM Clients WHERE client_id = :client_id");
                        $client_query->bindParam(':client_id', $project['client_id']);
                        $client_query->execute();
                        $client = $client_query->fetch(PDO::FETCH_ASSOC);
                        echo $client['client_name'] ?? 'Unknown';
                        ?>
                    </td>
                    <td><?= $project['start_date']; ?></td>
                    <td><?= $project['end_date']; ?></td>
                    <td><?= $project['budget']; ?></td>
                    <td><?= $project['status']; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
