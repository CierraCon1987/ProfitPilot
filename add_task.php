
<?php
    session_start();
    include('db_connection.php');

    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }

    $user_id = $_SESSION['user_id'];

    if (!isset($_GET['project_id'])) {
        header("Location: dashboard.php");
        exit();
    }

    $project_id = $_GET['project_id'];

    try {
        $stmt = $pdo->prepare("SELECT * FROM projects WHERE project_id = ? AND user_id = ?");
        $stmt->execute([$project_id, $user_id]);
        $project = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$project) {
            // If no project found, redirect to dashboard
            header("Location: dashboard.php");
            exit();
        }
    } catch (PDOException $e) {
        $error = "Error checking project: " . $e->getMessage();
    }

    function generateCustomID($prefix = 'TAS') {
        return strtoupper(uniqid($prefix));  
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $task_name = htmlspecialchars($_POST['task_name']);
        $task_description = htmlspecialchars($_POST['task_description']);
        $hours_worked = $_POST['hours_worked'];
        $hourly_rate = $_POST['hourly_rate'];
        $status = $_POST['status'];

        $task_id = strtoupper(uniqid('TAS'));

        try {
            $stmt = $pdo->prepare("INSERT INTO Tasks (task_id, project_id, user_id, task_name, task_description, hours_worked, hourly_rate, status) 
                                   VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$task_id, $project_id, $_SESSION['user_id'], $task_name, $task_description, $hours_worked, $hourly_rate, $status]);
            
            header("Location: dashboard.php");
            exit();
        } catch (PDOException $e) {
            $error = "Error adding task: " . $e->getMessage();
        }
    }
?>

<?php include('header.php'); ?>
<h2>Add New Task</h2>
<p>Fill out the details below to add a new task to a project.</p>

<!-- HTML form for adding task -->
<form method="POST" action="add_task.php?project_id=<?php echo urlencode($project_id); ?>">
    <label for="task_name">Task Name:</label>
    <input type="text" id="task_name" name="task_name" required>
    
    <label for="task_description">Task Description:</label>
    <textarea id="task_description" name="task_description" required></textarea>

    <label for="hours_worked">Hours Worked</label>
    <input type="number" name="hours_worked" id="hours_worked" step="0.01" required>

    <label for="hourly_rate">Hourly Rate</label>
    <input type="number" name="hourly_rate" id="hourly_rate" step="0.01" required>

    <label for="status">Status</label>
    <select name="status" id="status" required>
        <option value="Not Started" selected>Not Started</option>
        <option value="In Progress">In Progress</option>
        <option value="Completed">Completed</option>
    </select>

    <label for="due_date">Due Date:</label>
    <input type="date" id="due_date" name="due_date" required>

    <button type="submit">Add Task</button>
</form>

<?php if (isset($error)): ?>
    <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
<?php endif; ?>

<?php include('footer.php'); ?>
