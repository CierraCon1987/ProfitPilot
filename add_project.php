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

        $success = '';
        $error = '';

        // Client Dropdown
        $stmt = $pdo->prepare("SELECT client_id, client_name FROM Clients WHERE user_id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $clients = $stmt->fetchAll();

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
    
            header("Location: project_details.php?project_id=$project_id");
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

    <!-- Custom Styling -->
    <link rel="stylesheet" href="mainstyle.css">
</head>

    <header>
        <div id="admin">
            <h1>ProfitPilot</h1>
            <div class="buttons">
                <a href="dashboard.php" class="button">Dashboard</a>
                <a href="view_project.php" class="button">View All Projects</a>
                <a href="logout.php" class="button">Logout</a>
            </div>
        </div>
    </header>

<main>

    <h2>Create a New Project</h2>
    <p>Fill out the form below to add new project to the system</p>

    <form method="POST" action="add_project.php">
        <!-- Success/Error Message Section -->
        <?php if ($success): ?>
            <div class="success-message"><?php echo htmlspecialchars($success); ?></div>
        <?php elseif ($error): ?>
            <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <label for="project_name">Project Name:</label>
        <input type="text" id="project_name" name="project_name" required>

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

        <label for="budget">Estimated Budget:</label>
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
