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

    $user_id = $_SESSION['user_id'];
    $stmt = $pdo->prepare("SELECT * FROM Projects WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->execute([$user_id]);
    $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<?php include('header.php'); ?>

<main>

    <h2>Your Projects</h2>
        <!-- Add New Project -->
        <a href="add_project.php"  class="button">Add New Project</a>

        <!-- Add New Task -->
        <a href="add_task.php"  class="button">Add New Task</a>

        <!-- View Calculation -->
        <a href="calculation.php"  class="button">Calculate</a>

        <!-- Project List -->
        <div class="project-list">
                <?php if (empty($projects)): ?>
                    <p>No projects found. <a href="add_project.php">Start a new project now</a>.</p>
                <?php else: ?>
                    <?php foreach ($projects as $project): ?>
                        <div class="project-card">
                            <h3 class="project-title"><?php echo htmlspecialchars($project['project_name']); ?></h3>
                            <p class="project-status">
                                <strong>Status:</strong> <?php echo htmlspecialchars($project['status']); ?>
                            </p>
                            <div class="project-actions">
                                <a href="add_task.php?project_id=<?php echo urlencode($project['project_id']); ?>" class="edit-btn">Edit</a>
                                <a href="delete_project.php?project_id=<?php echo urlencode($project['project_id']); ?>" 
                                class="delete-btn" 
                                onclick="return confirm('Are you sure you want to delete this project?');">
                                    Delete
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

</main>
<?php include('footer.php'); ?>
</body>
</html>
