<!-- Cierra Bailey-Rice (8998948)
     Harpreet Kaur (8893116)
     Gurkamal Singh (9001186) -->
     
     <?php
        session_start();
        include('db_connection.php');

        if (!isset($_SESSION['user_id'])) {
            header("Location: login.php");
            exit();
        }

        // Client Dropdown
        $stmt = $pdo->prepare("SELECT client_id, client_name FROM Clients WHERE user_id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $clients = $stmt->fetchAll();

        var_dump($clients); // Add this line to check the result
        var_dump($_SESSION['user_id']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $project_name = htmlspecialchars($_POST['project_name']);
            $client_id = $_POST['client_id'];
            $start_date = $_POST['start_date'];
            $end_date = $_POST['end_date'];
            $budget = $_POST['budget'];
            $status = $_POST['status'];

            if (!isset($_SESSION['user_id'])) {
                echo "Error: user_id is not set in the session.";
                exit();
            }

            try {
                $stmt = $pdo->prepare("
                INSERT INTO Projects (user_id, project_name, client_id, start_date, end_date, budget, status)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$_SESSION['user_id'], $project_name, $client_id, $start_date, $end_date, $budget, $status]);
    
            // Redirect to the dashboard after successful creation? Maybe just have the page reload and then the user can start adding further details? 
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

<header>
        <h1>Create A New Project</h1>
        <a href="dashboard.php">Back to Dashboard</a>
    </header>

    <form method="post">
        <label for="project_name">Project Name:</label>
        <input type="text" id="project_name" name="project_name" required>
        <br>

        <label for="client_id">Client:</label>
        <select id="client_id" name="client_id">
            <?php if (!empty($clients)): ?>
                <?php foreach ($clients as $client): ?>
                    <option value="<?php echo htmlspecialchars($client['client_id']); ?>">
                        <?php echo htmlspecialchars($client['client_name']); ?>
                    </option>
                <?php endforeach; ?>
            <?php else: ?>
                <option>No clients found</option>
            <?php endif; ?>
        </select>
        <br>

        <label for="start_date">Start Date:</label>
        <input type="date" id="start_date" name="start_date" required>
        <br>

        <label for="end_date">End Date:</label>
        <input type="date" id="end_date" name="end_date" required>
        <br>

        <label for="budget">Budget:</label>
        <input type="number" id="budget" name="budget" required>
        <br>

        <label for="status">Status:</label>
        <select id="status" name="status">
            <option value="Planning">Planning</option>
            <option value="In Progress">In Progress</option>
            <option value="Completed">Completed</option>
            <option value="On Hold">On Hold</option>
        </select>
        <br>

        <input type="submit" value="Create Project">
    </form>
</body>

</html>
