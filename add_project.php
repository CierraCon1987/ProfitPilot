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

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $project_id = strtoupper(uniqid('PRO'));
        $project_name = $_POST['project_name'];
        $client_name = $_POST['client_name'];
        $start_date = $_POST['start_date'];
        $end_date=$_POST['end_date'];
        $status = $_POST['status'];
        
    try {
        $stmt = $pdo->prepare("INSERT INTO Projects (project_id, user_id, project_name, client_name, start_date, end_date, status) 
                               VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$project_id, $user_id, $project_name, $client_name, $start_date, $end_date, $status]);

        header("Location: dashboard.php");
        exit();
    } catch (PDOException $e) {
        $error = "Error adding project: " . $e->getMessage();
    }
}
?>

<?php include('header.php'); ?>
<h2>Add New Project</h2>
<p>Fill out the details below to add a project</p>

<form method="POST">
    <label for="project_name">Project Name:</label>
    <input type="text" id="project_name" name="project_name" required><br>

    <label for="client_name">Client Name:</label>
    <input type="text" id="client_name" name="client_name" required><br>

    <label for="start_date">Start Date:</label>
    <input type="date" id="start_date" name="start_date" required><br>

    <label for="end_date">End Date:</label>
    <input type="date" id="end_date" name="end_date" required><br>

    <label for="status">Status:</label>
    <select id="status" name="status">
        <option value="Not Started">Not Started</option>
        <option value="In Progress">In Progress</option>
        <option value="Completed">Completed</option>
    </select><br>

    <button type="submit">Create Project</button>
</form>
<?php include('footer.php'); ?>