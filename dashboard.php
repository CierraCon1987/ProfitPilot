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

    // Search and Filtering
    $search = $_GET['search'] ?? '';
    $filter_status = $_GET['filter_status'] ?? '';

    // Base query
    $query = "SELECT * FROM Projects WHERE user_id = ?";
    $params = [$_SESSION['user_id']];

    // Search
    if (!empty($search)) {
        $query .= " AND project_name LIKE ?";
        $params[] = "%" . $search . "%";
    }

    // Filters
    if (!empty($filter_status)) {
        $query .= " AND status = ?";
        $params[] = $filter_status;
    }

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $projects = $stmt->fetchAll();

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title> ProfitPilot | Dashboard</title>

    <!-- Custom Styling -->
    <link rel="stylesheet" href="mainstyle.css">
</head>

<body>
    <header>
    <h1>Welcome, to ProfitPilot <?php echo htmlspecialchars($_SESSION['first_name']); ?>!</h1>

    <div id="admin">
        <a href="profile.php" class="button">Profile</a>
        <a href="logout.php"  class="button">Logout</a>
    </div>
</header>

<main>

    <h2>Your Projects</h2>
    <!-- Search and Filter Form -->
    <form method="GET" action="dashboard.php">
            <input type="text" name="search" placeholder="Search projects..." value="<?php echo htmlspecialchars($search); ?>">
            <select name="filter_status">
                <option value="">All Statuses</option>
                <option value="Pending" <?php if ($filter_status == 'Pending') echo 'selected'; ?>>Pending</option>
                <option value="In Progress" <?php if ($filter_status == 'In Progress') echo 'selected'; ?>>In Progress</option>
                <option value="Completed" <?php if ($filter_status == 'Completed') echo 'selected'; ?>>Completed</option>
            </select>
            <button type="submit">Apply</button>
        </form>

        <!-- Add New Project -->
        <a href="add_project.php"  class="button">Add New Project</a>

        <!-- Add New Client -->
        <a href="add_client.php"  class="button">Add New Client</a>

        <!-- View Clients -->
        <a href="view_clients.php"  class="button">View Your Clients</a>

        <!-- Project List -->
        <div class="project-list">
            <?php foreach ($projects as $project): ?>
                <div class="project-card">
                    <h3 class="project-title"><?php echo htmlspecialchars($project['project_name']); ?></h3>
                    <p class="project-status">
                        <strong>Status:</strong> <?php echo htmlspecialchars($project['status']); ?>
                    </p>
                    <div class="project-actions">
                        <a href="edit_project.php?project_id=<?php echo urlencode($project['project_id']); ?>" class="edit-btn">Edit</a>
                        <a href="delete_project.php?project_id=<?php echo urlencode($project['project_id']); ?>" 
                        class="delete-btn" 
                        onclick="return confirm('Are you sure you want to delete this project?');">
                        Delete
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

</main>

</body>
</html>
