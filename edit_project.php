<!-- Cierra Bailey-Rice (8998948)
     Harpreet Kaur (8893116)
     Gurkamal Singh (9001186) -->
<?php
session_start();
include('db_connection.php');
include('functions.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if (isset($_GET['project_id'])) {
    $project_id = $_GET['project_id'];

    // Fetch project details to display in the form
    try {
        $stmt = $pdo->prepare("SELECT * FROM projects WHERE project_id = ? AND user_id = ?");
        $stmt->execute([$project_id, $user_id]);
        $project = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$project) {
            // Project not found or doesn't belong to the current user
            header("Location: dashboard.php");
            exit();
        }
    } catch (PDOException $e) {
        $error = "Error fetching project details: " . $e->getMessage();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Collect form data
        $project_name = $_POST['project_name'];
        $client_name = $_POST['client_name'];
        $start_date = $_POST['start_date'];
        $status = $_POST['status'];

        // Update project details in the database
        try {
            $stmt = $pdo->prepare("UPDATE projects SET project_name = ?, client_name = ?, start_date = ?, status = ? WHERE project_id = ? AND user_id = ?");
            $stmt->execute([$project_name, $client_name, $start_date, $status, $project_id, $user_id]);

            header("Location: dashboard.php"); // Redirect to the dashboard after update
            exit();
        } catch (PDOException $e) {
            $error = "Error updating project: " . $e->getMessage();
        }
    }
} else {
    // If no project ID is provided, redirect to dashboard
    header("Location: dashboard.php");
    exit();
}

include('header.php');
?>
<h2>Edit Project</h2>
<p>Update the project details below:</p>

<?php if (isset($error)): ?>
    <p style="color: red;"><?php echo $error; ?></p>
<?php endif; ?>

<form method="POST">
    <label for="project_name">Project Name:</label>
    <input type="text" id="project_name" name="project_name" value="<?php echo htmlspecialchars($project['project_name']); ?>" required><br>

    <label for="client_name">Client Name:</label>
    <input type="text" id="client_name" name="client_name" value="<?php echo htmlspecialchars($project['client_name']); ?>" required><br>

    <label for="start_date">Start Date:</label>
    <input type="date" id="start_date" name="start_date" value="<?php echo $project['start_date']; ?>" required><br>

    <label for="status">Status:</label>
    <select id="status" name="status">
        <option value="Not Started" <?php echo ($project['status'] == 'Not Started') ? 'selected' : ''; ?>>Not Started</option>
        <option value="In Progress" <?php echo ($project['status'] == 'In Progress') ? 'selected' : ''; ?>>In Progress</option>
        <option value="Completed" <?php echo ($project['status'] == 'Completed') ? 'selected' : ''; ?>>Completed</option>
    </select><br>

    <button type="submit">Update Project</button>
</form>

<?php include('footer.php'); ?>
