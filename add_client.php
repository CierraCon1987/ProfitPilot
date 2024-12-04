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

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $client_id = strtoupper(uniqid('CLI')); //makes a unique primary key automatically without AUTOINC - also adds CLI to the beginning of it so we know it is an ID for clients. 
    $client_name = trim($_POST['client_name']);
    $contact_first = trim($_POST['contact_first']);
    $contact_last = trim($_POST['contact_last']);
    $email = trim($_POST['email']);
    $phone_number = trim($_POST['phone_number']);
    $address_line1 = trim($_POST['address_line1']);
    $address_line2 = trim($_POST['address_line2']);
    $city = trim($_POST['city']);
    $province = trim($_POST['province']);
    $postal_code = trim($_POST['postal_code']);
    $country = trim($_POST['country']);

    if (empty($client_name)) {
        $error = 'Client name is required.';
    } else {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO Clients (
                    client_id, client_name, contact_first, contact_last, email, phone_number, 
                    address_line1, address_line2, city, province, postal_code, country
                ) VALUES (
                    ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
                )
            ");
            $stmt->execute([
                $client_id, $client_name, $contact_first, $contact_last, $email, $phone_number,
                $address_line1, $address_line2, $city, $province, $postal_code, $country
            ]);
            $success = "Client added successfully!";
        } catch (PDOException $e) {
            $error = "Error adding client: " . $e->getMessage();
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ProfitPilot | Add Client</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
<header>
    <h1>Add New Client</h1>
    <a href="dashboard.php">Back to Dashboard</a>
    <a href="view_clients.php">View All Clients</a>
</header>

<main>
    <?php if ($error): ?>
        <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>
    <?php if ($success): ?>
        <p style="color: green;"><?php echo htmlspecialchars($success); ?></p>
    <?php endif; ?>

    <form method="POST" action="add_client.php">
        <label for="client_name">Client Name: *</label>
        <input type="text" id="client_name" name="client_name" required>

        <label for="contact_first">Contact First Name:</label>
        <input type="text" id="contact_first" name="contact_first">

        <label for="contact_last">Contact Last Name:</label>
        <input type="text" id="contact_last" name="contact_last">

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
