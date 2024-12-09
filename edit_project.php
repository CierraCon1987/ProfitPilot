<?php
    session_start();
    include('db_connection.php');

    $success_message = '';
    $error_message = '';

    // Check for ProjectID
    if (!isset($_GET['project_id'])) {
        die("Project ID is required.");
    }

    $project_id = $_GET['project_id'];

    // Get project details
    try {
        $stmt = $pdo->prepare("SELECT * FROM projects WHERE project_id = :project_id");
        $stmt->execute([':project_id' => $project_id]);
        $project = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$project) {
            die("Project not found.");
        }
    } catch (PDOException $e) {
        die("Error fetching project details: " . $e->getMessage());
    }

    // Get tasks for the project
    $tasks = [];
    try {
        $stmt = $pdo->prepare("SELECT * FROM tasks WHERE project_id = :project_id");
        $stmt->execute([':project_id' => $project_id]);
        $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("Error fetching tasks: " . $e->getMessage());
    }

    // Update project details
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $project_name = $_POST['project_name'];
        $client_name = $_POST['client_name'];
        $start_date = $_POST['start_date'];
        $end_date = $_POST['end_date'];
        $status = $_POST['status'];

        try {
            $stmt = $pdo->prepare("
                UPDATE projects
                SET project_name = :project_name,
                    client_name = :client_name,
                    start_date = :start_date,
                    end_date = :end_date,
                    status = :status
                WHERE project_id = :project_id
            ");
            $stmt->execute([
                ':project_name' => $project_name,
                ':client_name' => $client_name,
                ':start_date' => $start_date,
                ':end_date' => $end_date,
                ':status' => $status,
                ':project_id' => $project_id
            ]);

            if ($stmt->rowCount() > 0) {
                $success_message = "Project updated successfully!";
                // Refresh project details
                $project['project_name'] = $project_name;
                $project['client_name'] = $client_name;
                $project['start_date'] = $start_date;
                $project['end_date'] = $end_date;
                $project['status'] = $status;
            } else {
                $error_message = "No changes made.";
            }
        } catch (PDOException $e) {
            $error_message = "Error updating project: " . $e->getMessage();
        }
    }
?>

<?php include('header.php'); ?>

<h2>Edit Project</h2>

<?php if ($success_message): ?>
    <p class="success"><?= htmlspecialchars($success_message) ?></p>
<?php endif; ?>
<?php if ($error_message): ?>
    <p class="error"><?= htmlspecialchars($error_message) ?></p>
<?php endif; ?>

<form method="POST" action="edit_project.php?project_id=<?= htmlspecialchars($project_id) ?>">
    <div>
        <label for="project_name">Project Name:</label>
        <input type="text" id="project_name" name="project_name" value="<?= htmlspecialchars($project['project_name']) ?>" required>
    </div>
    <div>
        <label for="client_name">Client Name:</label>
        <input type="text" id="client_name" name="client_name" value="<?= htmlspecialchars($project['client_name']) ?>" required>
    </div>
    <div>
        <label for="start_date">Start Date:</label>
        <input type="date" id="start_date" name="start_date" value="<?= htmlspecialchars($project['start_date']) ?>" required>
    </div>
    <div>
        <label for="end_date">End Date:</label>
        <input type="date" id="end_date" name="end_date" value="<?= htmlspecialchars($project['end_date']) ?>" required>
    </div>
    <div>
        <label for="status">Status:</label>
        <select id="status" name="status" required>
            <option value="Not Started" <?= $project['status'] == 'Not Started' ? 'selected' : '' ?>>Not Started</option>
            <option value="In Progress" <?= $project['status'] == 'In Progress' ? 'selected' : '' ?>>In Progress</option>
            <option value="Completed" <?= $project['status'] == 'Completed' ? 'selected' : '' ?>>Completed</option>
        </select>
    </div>
    <button type="submit">Update Project</button>
</form>

<h3>Manage Tasks</h3>
<?php if (count($tasks) > 0): ?>
    <table>
        <thead>
            <tr>
                <th>Task Name</th>
                <th>Hourly Rate</th>
                <th>Hours Worked</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($tasks as $task): ?>
                <tr>
                    <td><?= htmlspecialchars($task['task_name']) ?></td>
                    <td><?= htmlspecialchars($task['hourly_rate']) ?></td>
                    <td><?= htmlspecialchars($task['hours_worked']) ?></td>
                    <td>
                    <a href="edit_task.php?task_id=<?= htmlspecialchars($task['task_id']) ?>&project_id=<?= htmlspecialchars($project['project_id']) ?>">Edit</a>                    | 
                    <a href="remove_task.php?task_id=<?= htmlspecialchars($task['task_id']) ?>&project_id=<?= htmlspecialchars($project_id) ?>" onclick="return confirm('Are you sure you want to delete this task?');">Remove</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>No tasks available for this project.</p>
<?php endif; ?>

<a href="add_task.php?project_id=<?= htmlspecialchars($project_id) ?>" class="button">Add New Task</a>

<?php include('footer.php'); ?>
