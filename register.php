<!-- Cierra Bailey-Rice (8998948)
     Harpreet Kaur (8893116)
     Gurkamal Singh () -->

<?php

    include('db_connection.php');

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $first_name = htmlspecialchars($_POST['first_name']);
        $last_name = htmlspecialchars($_POST['last_name']);
        $username = htmlspecialchars($_POST['username']);
        $email = htmlspecialchars($_POST['email']);
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

        if (empty($first_name) || empty($last_name) || empty($username) || empty($email) || empty($password)) {
            $error = "All fields are required!";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Invalid email format!";
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
        // Generates a unique UserID without AUTOINC
        $user_id = uniqid('USER-');


        // Insert User Info into DB
        try {
            $stmt = $pdo->prepare("INSERT INTO Users (user_id, first_name, last_name, username, email, password) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$user_id, $first_name, $last_name, $username, $email, $hashedPassword]);

            // Redirect to login page after successful registration
            header("Location: login.php");
            exit();
        } catch (PDOException $e) {
            $error = "Error registering user: " . $e->getMessage();
        }
    }
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ProfitPilot | Register</title>

    <!-- Custom Styling -->
    <link rel="stylesheet" href="mainstyle.css">
</head>

<body>
    <h1>Create an Account</h1>

    <?php if (isset($error)) { echo "<p style='color: red;'>$error</p>"; } ?>

    <form action="register.php" method="POST">
        <label for="first_name">First Name:</label>
        <input type="text" id="first_name" name="first_name" required>
        <br>

        <label for="last_name">Last Name:</label>
        <input type="text" id="last_name" name="last_name" required>
        <br>
        
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>
        <br>

        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>
        <br>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
        <br>

        <input type="submit" value="Register">
    </form>

    <p>Already have an account? <a href="login.php">Login here</a></p>

</body>
</html>
