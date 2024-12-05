<!-- Cierra Bailey-Rice (8998948)
     Harpreet Kaur (8893116)
     Gurkamal Singh (9001186) -->

<?php
     $servername = "localhost";
     $username = "root";
     $password = "";
     $dbname = "profitpilot";

     try {
     $db = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
     $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
     } catch (PDOException $e) {
     die("Connection failed: " . $e->getMessage());
     }

    // Check if the project_id is set in the URL
    if (!isset($_GET['project_id'])) {
        die("Project ID not provided.");
    }

    $project_id = $_GET['project_id'];

    $success = '';
    $error = '';

    try {
        $query = $db->prepare("SELECT * FROM Projects WHERE project_id = :project_id");
        $query->bindParam(':project_id', $project_id, PDO::PARAM_STR);
        $query->execute();
        $project = $query->fetch(PDO::FETCH_ASSOC);

        if (!$project) {
            die("Project not found.");
        }
    } catch (PDOException $e) {
        die("Error fetching project: " . $e->getMessage());
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_project'])) {
        $project_id = $_POST['project_id'];
        $project_name = $_POST['project_name'];
        $start_date = $_POST['start_date'];
        $end_date = $_POST['end_date'];
        $budget = $_POST['budget'];
        $status = $_POST['status'];

        try {
            $updateQuery = $db->prepare("
                UPDATE Projects 
                SET project_name = :project_name,
                    start_date = :start_date,
                    end_date = :end_date,
                    budget = :budget,
                    status = :status,
                    updated_at = NOW()
                WHERE project_id = :project_id
            ");

            $updateQuery->execute([
                ':project_name' => $project_name,
                ':start_date' => $start_date,
                ':end_date' => $end_date,
                ':budget' => $budget,
                ':status' => $status,
                ':project_id' => $project_id
            ]);

            header("Location: dashboard.php?message=Project+updated+successfully");
            exit;
        } catch (PDOException $e) {
            die("Error updating project: " . $e->getMessage());
        }
    }
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ProfitPilot | Edit Project</title>

    <!-- Custom Styling -->
    <link rel="stylesheet" href="mainstyle.css">
</head>

<body>
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
        <h2>Edit Project</h2>
        <p>Fill out the form to update the project information</p>

        <form method="POST" action="edit_project.php?project_id=<?php echo $project['project_id']; ?>">
          <!-- Success/Error Message Section -->
          <?php if ($success): ?>
               <div class="success-message"><?php echo htmlspecialchars($success); ?></div>
          <?php elseif ($error): ?>
               <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
          <?php endif; ?> 
          
          <input type="hidden" name="project_id" value="<?php echo htmlspecialchars($project['project_id']); ?>">

            <label for="project_name">Project Name:</label>
            <input type="text" id="project_name" name="project_name" 
                   value="<?php echo htmlspecialchars($project['project_name']); ?>" required>

            <label for="start_date">Start Date:</label>
            <input type="date" id="start_date" name="start_date" 
                   value="<?php echo htmlspecialchars($project['start_date']); ?>" required>

            <label for="end_date">End Date:</label>
            <input type="date" id="end_date" name="end_date" 
                   value="<?php echo htmlspecialchars($project['end_date']); ?>">

            <label for="budget">Budget:</label>
            <input type="number" id="budget" name="budget" step="0.01" 
                   value="<?php echo htmlspecialchars($project['budget']); ?>" required>

            <label for="status">Status:</label>
            <select id="status" name="status" required>
                <?php 
                $statuses = ['Planning', 'In Progress', 'Completed', 'On Hold'];
                foreach ($statuses as $status) {
                    $selected = $status === $project['status'] ? 'selected' : '';
                    echo "<option value='$status' $selected>$status</option>";
                }
                ?>
            </select>

            <button type="submit" name="update_project">Update Project</button>
        </form>
    </main>
</body>
</html>
