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

    $error = ''; 
    $success = '';  

    if (isset($_GET['client_id'])) {
        $client_id = $_GET['client_id'];

        $stmt = $pdo->prepare("SELECT * FROM Clients WHERE client_id = ?");
        $stmt->execute([$client_id]);
        $client = $stmt->fetch();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $client_name = $_POST['client_name'];
        $phone_number = $_POST['phone_number'];
        $email = $_POST['email'];
        $address_line1 = $_POST['address_line1'];
        $address_line2 = $_POST['address_line2'];
        $city = $_POST['city'];
        $province = $_POST['province'];
        $postal_code = $_POST['postal_code'];
        $country = $_POST['country'];
        $contact_first = $_POST['contact_first'];
        $contact_last = $_POST['contact_last'];

        // Validations
        if (
            empty($client_name) || empty($phone_number) || empty($email) || 
            empty($address_line1) || empty($city) || empty($province) || 
            empty($postal_code) || empty($country) || empty($contact_first) || empty($contact_last)
        ) {
            $error = "All fields are required!";
        } else {
            // Individual field validations
            if (!preg_match('/^[a-zA-Z\s\-]+$/', $contact_first)) {
                $error = "Contact first name must contain only letters, spaces, and hyphens.";
            } elseif (!preg_match('/^[a-zA-Z\s\-]+$/', $contact_last)) {
                $error = "Contact last name must contain only letters, spaces, and hyphens.";
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = "Invalid email format.";
            } elseif (!preg_match('/^\d{3}-\d{3}-\d{4}$/', $phone_number)) {
                $error = "Phone number must be in XXX-XXX-XXXX format.";
            } elseif (!preg_match('/^[A-Za-z]\d[A-Za-z][ -]?\d[A-Za-z]\d$/', $postal_code)) {
                $error = "Invalid postal code format.";
            }
        }

        // If fields valid, update client data
        if (!$error) {
            $stmt = $pdo->prepare("UPDATE Clients SET client_name = ?, phone_number = ?, email = ?, address_line1 = ?, address_line2  = ?, city = ?, province = ?, postal_code = ?, country = ?, contact_first = ?, contact_last = ? WHERE client_id = ?");
            $stmt->execute([$client_name, $phone_number, $email, $address_line1, $address_line2, $city, $province, $postal_code, $country, $contact_first, $contact_last, $client_id]);

            $success = "Client details updated successfully!";
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ProfitPilot | Edit Client</title>

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
        <h2>Edit Client Details</h2>
        <p>Fill out all of the form fields to edit your client information</p>

        <form method="POST" action="edit_client.php?client_id=<?php echo $client['client_id']; ?>">
            <!-- Success/Error Message Section -->
            <?php if ($success): ?>
                <div class="success-message"><?php echo htmlspecialchars($success); ?></div>
            <?php elseif ($error): ?>
                <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <label for="client_name">Client Name:</label>
            <input type="text" id="client_name" name="client_name" value="<?php echo htmlspecialchars($client['client_name']); ?>">

            <label for="contact_first">Contact First Name:</label>
            <input type="text" id="contact_first" name="contact_first" value="<?php echo htmlspecialchars($client['contact_first']); ?>">

            <label for="contact_last">Contact Last Name:</label>
            <input type="text" id="contact_last" name="contact_last" value="<?php echo htmlspecialchars($client['contact_last']); ?>">

            <label for="phone_number">Phone Number:</label>
            <input type="text" id="phone_number" name="phone_number" value="<?php echo htmlspecialchars($client['phone_number']); ?>">

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($client['email']); ?>">

            <label for="address_line1">Address Line 1:</label>
            <input type="text" id="address_line1" name="address_line1" 
                value="<?php echo isset($_POST['address_line1']) ? htmlspecialchars($_POST['address_line1']) : htmlspecialchars($client['address_line1']); ?>">

            <label for="address_line2">Address Line 2:</label>
            <input type="text" id="address_line2" name="address_line2" 
                value="<?php echo isset($_POST['address_line2']) ? htmlspecialchars($_POST['address_line2']) : htmlspecialchars($client['address_line2']); ?>">

            <label for="city">City:</label>
            <input type="text" id="city" name="city" 
                value="<?php echo isset($_POST['city']) ? htmlspecialchars($_POST['city']) : htmlspecialchars($client['city']); ?>">

            <label for="province">Province:</label>
            <input type="text" id="province" name="province" 
                value="<?php echo isset($_POST['province']) ? htmlspecialchars($_POST['province']) : htmlspecialchars($client['province']); ?>">

            <label for="postal_code">Postal Code:</label>
            <input type="text" id="postal_code" name="postal_code" 
                value="<?php echo isset($_POST['postal_code']) ? htmlspecialchars($_POST['postal_code']) : htmlspecialchars($client['postal_code']); ?>">

            <label for="country">Country:</label>
            <input type="text" id="country" name="country" 
                value="<?php echo isset($_POST['country']) ? htmlspecialchars($_POST['country']) : htmlspecialchars($client['country']); ?>">

            <button type="submit">Update Client</button>
        </form>
    </main>
</body>
</html>