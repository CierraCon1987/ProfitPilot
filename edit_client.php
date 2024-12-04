<?php
    session_start();
    include('db_connection.php');

    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }

    $error = ''; 

    if (isset($_GET['client_id'])) {
        $client_id = $_GET['client_id'];

        // Fetch client data from the database
        $stmt = $pdo->prepare("SELECT * FROM Clients WHERE client_id = ?");
        $stmt->execute([$client_id]);
        $client = $stmt->fetch();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Update client data
        $client_name = $_POST['client_name'];
        $phone_number = $_POST['phone_number'];
        $email = $_POST['email'];
        $address = $_POST['address'];

        // Validate client name, email, phone, and address
    if (empty($client_name) || empty($phone_number) || empty($email) || empty($address)) {
        $error = "All fields are required!";
    } elseif (!preg_match('/^[a-zA-Z\s]+$/', $client_name)) {
        $error = "Client name must contain only letters and spaces.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format!";
    } elseif (!preg_match('/^\d{10}$/', $phone_number)) {
        $error = "Phone number must be exactly 10 digits!";
    } elseif (empty($address)) {
        $error = "Address is required!";
    }

    // If there is no error, update the client data
    if (!$error) {
        // Update query
        $stmt = $pdo->prepare("UPDATE Clients SET client_name = ?, phone_number = ?, email = ?, address = ? WHERE client_id = ?");
        $stmt->execute([$client_name, $phone_number, $email, $address, $client_id]);

        // Redirect to the clients list page
        header("Location: view_clients.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Client</title>
    <link rel="stylesheet" href="mainstyle.css">
</head>
<body>
    <header>
        <h1>Edit Client</h1>
        <a href="view_clients.php">Back to Clients</a>
    </header>

    <main>
        <h2>Edit Client Details</h2>
        <?php if ($error): ?>
            <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <form method="POST" action="edit_client.php?client_id=<?php echo $client['client_id']; ?>">
            <label for="client_name">Client Name:</label>
            <input type="text" id="client_name" name="client_name" value="<?php echo htmlspecialchars($client['client_name']); ?>">

            <label for="phone_number">Phone Number:</label>
            <input type="text" id="phone_number" name="phone_number" value="<?php echo htmlspecialchars($client['phone_number']); ?>">

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($client['email']); ?>">

            <label for="address">Address:</label>
            <textarea id="address" name="address"><?php echo htmlspecialchars($client['address']); ?></textarea>

            <button type="submit">Update Client</button>
        </form>
    </main>
</body>
</html>
