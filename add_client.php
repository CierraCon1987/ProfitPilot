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
        $client_id = strtoupper(uniqid('CLI')); 
        $client_name = trim($_POST['client_name']);
        $contact_first = trim($_POST['contact_first']);
        $contact_last = trim($_POST['contact_last']);
        $email = trim($_POST['email']);
        $phone_number = trim($_POST['phone_number']);
        
        // Separate address fields
        $address_line1 = trim($_POST['address_line1']);
        $address_line2 = trim($_POST['address_line2']);
        $city = trim($_POST['city']);
        $province = trim($_POST['province']);
        $postal_code = trim($_POST['postal_code']);
        $country = trim($_POST['country']);
        
        // Validations
        if (empty($client_name) || empty($contact_first) || empty($contact_last) || empty($email) || empty($phone_number)) {
            $error = "All fields are required!";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Invalid email format!";
        } elseif (!preg_match('/^\d{3}-\d{3}-\d{4}$/', $phone_number)) {
            $error = "Phone number must be in the format XXX-XXX-XXXX!";
        } elseif (empty($address_line1) || empty($city) || empty($province) || empty($postal_code) || empty($country)) {
            $error = "All address fields are required!";
        } else {
            try {
                $stmt = $pdo->prepare("
                    INSERT INTO Clients (
                        client_id, user_id, client_name, contact_first, contact_last, email, phone_number, 
                        address_line1, address_line2, city, province, postal_code, country
                    ) VALUES (
                        ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
                    )
                ");
                $stmt->execute([ 
                    $client_id, $_SESSION['user_id'], $client_name, $contact_first, $contact_last, $email, $phone_number, 
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
    
    <!-- Custom Styling -->
    <link rel="stylesheet" href="mainstyle.css">
</head>

<body>

<header>
    <div id="admin">
        <h1>ProfitPilot</h1>
        <div class="buttons">
            <a href="dashboard.php" class="button">Dashboard</a>
            <a href="view_clients.php" class="button">View All Clients</a>
            <a href="logout.php" class="button">Logout</a>
        </div>
    </div>
</header>

<main>

<h2>Add a New Client</h2>
<p>Fill out the form below to add your client to the system</p>

    <form method="POST" action="add_client.php">
    <!-- Success/Error Message Section -->
    <?php if ($success): ?>
        <div class="success-message"><?php echo htmlspecialchars($success); ?></div>
    <?php elseif ($error): ?>
        <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <label for="client_name">Client Name:</label>
    <input type="text" id="client_name" name="client_name" value="<?php echo isset($_POST['client_name']) ? htmlspecialchars($_POST['client_name']) : ''; ?>">

    <label for="contact_first">Contact First Name:</label>
    <input type="text" id="contact_first" name="contact_first" value="<?php echo isset($_POST['contact_first']) ? htmlspecialchars($_POST['contact_first']) : ''; ?>">

    <label for="contact_last">Contact Last Name:</label>
    <input type="text" id="contact_last" name="contact_last" value="<?php echo isset($_POST['contact_last']) ? htmlspecialchars($_POST['contact_last']) : ''; ?>">

    <label for="email">Email:</label>
    <input type="email" id="email" name="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">

    <label for="phone_number">Phone Number:</label>
    <input type="text" id="phone_number" name="phone_number" value="<?php echo isset($_POST['phone_number']) ? htmlspecialchars($_POST['phone_number']) : ''; ?>">

    <label for="address_line1">Address Line 1:</label>
    <input type="text" id="address_line1" name="address_line1" value="<?php echo isset($_POST['address_line1']) ? htmlspecialchars($_POST['address_line1']) : ''; ?>">

    <label for="address_line2">Address Line 2:</label>
    <input type="text" id="address_line2" name="address_line2" value="<?php echo isset($_POST['address_line2']) ? htmlspecialchars($_POST['address_line2']) : ''; ?>">

    <label for="city">City:</label>
    <input type="text" id="city" name="city" value="<?php echo isset($_POST['city']) ? htmlspecialchars($_POST['city']) : ''; ?>">

    <label for="province">Province:</label>
    <input type="text" id="province" name="province" value="<?php echo isset($_POST['province']) ? htmlspecialchars($_POST['province']) : ''; ?>">

    <label for="postal_code">Postal Code:</label>
    <input type="text" id="postal_code" name="postal_code" value="<?php echo isset($_POST['postal_code']) ? htmlspecialchars($_POST['postal_code']) : ''; ?>">

    <label for="country">Country:</label>
    <input type="text" id="country" name="country" value="<?php echo isset($_POST['country']) ? htmlspecialchars($_POST['country']) : ''; ?>">

    <button type="submit">Add Client</button>
</form>

</main>

</body>
</html>
