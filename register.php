<!-- Cierra Bailey-Rice (8998948)
     Harpreet Kaur (8893116)
     Gurkamal Singh (9001186) -->

<?php

    include('db_connection.php');

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $first_name = htmlspecialchars($_POST['first_name']);
        $last_name = htmlspecialchars($_POST['last_name']);
        $username = htmlspecialchars($_POST['username']);
        $email = htmlspecialchars($_POST['email']);
        $password = $_POST['password'];
    
        // Validate password complexity
        if (!preg_match('/^(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $password)) {
            $error = "Password must be at least 8 characters long and include one uppercase letter, one number, and one special character.";
        }
    
        // Check for duplicate email/username
        $stmt = $pdo->prepare("SELECT * FROM Users WHERE email = ? OR username = ?");
        $stmt->execute([$email, $username]);
        if ($stmt->fetch()) {
            $error = "Email or username already exists!";
        }
    
        if (!isset($error)) {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $user_id = uniqid('USER-');
    
            try {
                $stmt = $pdo->prepare("INSERT INTO Users (user_id, first_name, last_name, username, email, password) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$user_id, $first_name, $last_name, $username, $email, $hashedPassword]);
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
