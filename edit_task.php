<?php
session_start();
include('db_connection.php');
include('functions.php');

// Check if task ID and project ID are provided
if (!isset($_GET['task_id']) || !isset($_GET['project_id'])) {
    die("Task ID and Project ID are required.");
}

$task_id = $_GET['task_id'];
$project_id = $_GET['project_id'];

// Fetch project details to ensure the project exists
try {
    $stmt = $pdo->prepare("SELECT * FROM Projects WHERE project_id = :project_id");
    $stmt->execute([':project_id' => $project_id]);
    $project = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$project) {
        die("Project not found.");
    }
} catch (PDOException $e) {
    die("Error fetching project details: " . $e->getMessage());
}

// Fetch task details
try {
    $stmt = $pdo->prepare("SELECT * FROM tasks WHERE task_id = :task_id");
    $stmt->execute([':task_id' => $task_id]);
    $task = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$task) {
        die("Task not found.");
    }
} catch (PDOException $e) {
    die("Error fetching task details: " . $e->getMessage());
}

// Update task details
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve form inputs
    $task_name = $_POST['task_name'];
    $hourly_rate = $_POST['hourly_rate'];
    $hours_worked = $_POST['hours_worked'];

    try {
        $stmt = $pdo->prepare("
            UPDATE tasks
            SET task_name = :task_name,
                hourly_rate = :hourly_rate,
                hours_worked = :hours_worked
            WHERE task_id = :task_id
        ");
        $stmt->execute([
            ':task_name' => $task_name,
            ':hourly_rate' => $hourly_rate,
            ':hours_worked' => $hours_worked,
            ':task_id' => $task_id
        ]);

        // Check if the task was updated
        if ($stmt->rowCount() > 0) {
            header("Location: edit_project.php?project_id=" . urlencode($project_id));
            exit();
        } else {
            $error_message = "No changes made.";
        }
    } catch (PDOException $e) {
        $error_message = "Error updating task: " . $e->getMessage();
    }
}
?>

<?php include('header.php'); ?>

<h2>Edit Task</h2>

<?php if (isset($error_message)): ?>
    <p class="error"><?= htmlspecialchars($error_message) ?></p>
<?php endif; ?>

<form method="POST" action="edit_task.php?task_id=<?= htmlspecialchars($task_id) ?>&project_id=<?= htmlspecialchars($project_id) ?>">
    <div>
        <label for="task_name">Task Name:</label>
        <input type="text" id="task_name" name="task_name" value="<?= htmlspecialchars($task['task_name']) ?>" required>
    </div>
    <div>
        <label for="hourly_rate">Hourly Rate:</label>
        <input type="number" step="0.01" id="hourly_rate" name="hourly_rate" value="<?= htmlspecialchars($task['hourly_rate']) ?>" required>
    </div>
    <div>
        <label for="hours_worked">Hours Worked:</label>
        <input type="number" step="0.1" id="hours_worked" name="hours_worked" value="<?= htmlspecialchars($task['hours_worked']) ?>" required>
    </div>
    <button type="submit">Update Task</button>
</form>


<?php include('footer.php'); ?>
