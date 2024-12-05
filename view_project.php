<!-- Cierra Bailey-Rice (8998948)
     Harpreet Kaur (8893116)
     Gurkamal Singh (9001186) -->

     <?php
    include('db_connection.php');
    session_start();

    // Redirect to login page if not logged in
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }

    // Get the user_id of the logged-in user
    $user_id = $_SESSION['user_id'];

    // Set up the search query if a search term is provided
    $search_query = '';
    if (isset($_GET['search'])) {
        $search_query = $_GET['search'];
    }

    // Get all projects for the logged-in user, with an optional search filter
    if ($search_query) {
        $stmt = $pdo->prepare("SELECT * FROM Projects WHERE user_id = ? AND project_name LIKE ?");
        $stmt->execute([$user_id, '%' . $search_query . '%']);
    } else {
        $stmt = $pdo->prepare("SELECT * FROM Projects WHERE user_id = ?");
        $stmt->execute([$user_id]);
    }

    $projects = $stmt->fetchAll();

    if (isset($_GET['project_id'])) {
        $project_id = $_GET['project_id'];

        $stmt = $pdo->prepare("SELECT * FROM Projects WHERE project_id = ?");
        $stmt->execute([$project_id]);
        $project = $stmt->fetch();

        $stmt_time = $pdo->prepare("SELECT * FROM TimeTracking WHERE project_id = ?");
        $stmt_time->execute([$project_id]);
        $time_entries = $stmt_time->fetchAll();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>ProfitPilot | Project Details</title>

    <!-- Custom Styling -->
    <link rel="stylesheet" href="mainstyle.css">
</head>

<header>
    <div id="admin">
        <h1>ProfitPilot</h1>
        <div class="buttons">
            <a href="dashboard.php" class="button">Dashboard</a>
            <a href="add_project.php" class="button">Add a Project</a>
            <a href="logout.php" class="button">Logout</a>
        </div>
    </div>
</header>

<main>
    <h2>All Projects</h2>

    <!-- Search Bar -->
    <form method="GET" action="edit_project.php">
        <input type="text" name="search" value="<?php echo htmlspecialchars($search_query); ?>" placeholder="Search by Project Name">
        <button type="submit">Search</button>
    </form>

    <table>
        <thead>
            <tr>
                <th>Project Name</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Status</th>
                <th>Budget</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($projects as $project): ?>
                <tr>
                    <td><?php echo htmlspecialchars($project['project_name']); ?></td>
                    <td><?php echo htmlspecialchars($project['start_date']); ?></td>
                    <td><?php echo htmlspecialchars($project['end_date']); ?></td>
                    <td><?php echo htmlspecialchars($project['status']); ?></td>
                    <td><?php echo htmlspecialchars($project['budget']); ?></td>
                    <td>
                        <a href="edit_project.php?project_id=<?php echo $project['project_id']; ?>">Edit</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <?php if (isset($project)): ?>
        <!-- Project Details for the selected project -->
        <h2>Project Details</h2>
        <h3><?php echo htmlspecialchars($project['project_name']); ?></h3>
        <p><strong>Start Date:</strong> <?php echo htmlspecialchars($project['start_date']); ?></p>
        <p><strong>End Date:</strong> <?php echo htmlspecialchars($project['end_date']); ?></p>
        <p><strong>Status:</strong> <?php echo htmlspecialchars($project['status']); ?></p>
        <p><strong>Budget:</strong> <?php echo htmlspecialchars($project['budget']); ?></p>

        <h3>Time Entries</h3>
        <ul>
            <?php foreach ($time_entries as $entry): ?>
                <li><?php echo htmlspecialchars($entry['hours']); ?> hours - <?php echo htmlspecialchars($entry['task']); ?></li>
            <?php endforeach; ?>
        </ul>
        <!-- Add more sections for costs, milestones, etc. -->
    <?php endif; ?>
</main>

</body>
</html>
