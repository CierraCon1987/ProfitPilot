<?php
include('db_connection.php');
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $client_id = strtoupper(uniqid('CLI')); // Generate a unique client ID
    $client_name = trim($_POST['client_name']);
    $email = trim($_POST['email']);
    $phone_number = trim($_POST['phone_number']);
    
    // Combine all address fields into one field
    $address = trim($_POST['address_line1']) . ' ' . trim($_POST['address_line2']) . ' ' . trim($_POST['city']) . ' ' . trim($_POST['province']) . ' ' . trim($_POST['postal_code']) . ' ' . trim($_POST['country']);
    
    // Validate client name
   // Validate client name
   if (empty($client_name) || empty($phone_number) || empty($email)) {
    $error = "Client name, phone number, and email are required!";
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $error = "Invalid email format!";
} elseif (!preg_match('/^\d{10}$/', $phone_number)) {
    $error = "Phone number must be exactly 10 digits!";
} elseif (empty($address)) {
    $error = "Address is required!";
} else {
        try {
            // Prepare SQL query to insert data into the Clients table
            $stmt = $pdo->prepare("
                INSERT INTO Clients (
                    client_id, client_name, email, phone_number, address
                ) VALUES (
                    ?, ?, ?, ?, ?
                )
            ");
            // Execute the query with the actual values
            $stmt->execute([
                $client_id, $client_name, $email, $phone_number, $address
            ]);
            $success = "Client added successfully!";
        } catch (PDOException $e) {
            // Catch any errors and display them
            $error = "Error adding client: " . $e->getMessage();
        }
    }
}

?>
<?php if ($success): ?>
    <div class="message success"><?php echo htmlspecialchars($success); ?></div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="message error"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ProfitPilot | Add Client</title>
    <link rel="stylesheet" href="mainstyle.css">
</head>

<body>
<header>
    <h1>Add New Client</h1>
    <a href="dashboard.php"class="button">Back to Dashboard</a>
    <a href="view_clients.php" class="button">View All Clients</a>
</header>

<main>
<script>
        // Automatically hide messages after 5 seconds
        setTimeout(() => {
            const messages = document.querySelectorAll('.message');
            messages.forEach(message => {
                message.style.opacity = '0';
                setTimeout(() => message.remove(), 500); // Wait for fade-out transition
            });
        }, 5000);
    </script>

    <form method="POST" action="add_client.php">
        <label for="client_name">Client Name: *</label>
        <input type="text" id="client_name" name="client_name">

        <label for="email">Email:</label>
        <input type="email" id="email" name="email">

        <label for="phone_number">Phone Number:</label>
        <input type="text" id="phone_number" name="phone_number">

        <label for="address_line1">Address Line 1:</label>
        <input type="text" id="address_line1" name="address_line1">

        <label for="address_line2">Address Line 2:</label>
        <input type="text" id="address_line2" name="address_line2">

        <label for="city">City:</label>
        <input type="text" id="city" name="city">

        <label for="province">Province:</label>
        <input type="text" id="province" name="province">

        <label for="postal_code">Postal Code:</label>
        <input type="text" id="postal_code" name="postal_code">

        <label for="country">Country:</label>
        <input type="text" id="country" name="country">

        <button type="submit">Add Client</button>
    </form>
</main>

</body>
</html>
