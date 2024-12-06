<!-- Cierra Bailey-Rice (8998948)
     Harpreet Kaur (8893116)
     Gurkamal Singh (9001186) -->

<?php

    include('db_connection.php');

    $error = ''; 

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
    
        // If no errors, proceed to register the user
        if (empty($error)) {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $user_id = strtoupper(uniqid('USR'));
    
            try {
                $stmt = $pdo->prepare("INSERT INTO Users (user_id, username, email, password) VALUES (?, ?, ?, ?)");
                $stmt->execute([$user_id, $username, $email, $hashedPassword]);
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
    <header>
        <h2>Create an Account</h2>
    </header>

    <!-- Registration Form -->
    <?php if ($error): ?>
        <div class="error"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <label for="username">Username:</label>
        <input type="text" name="username" required><br>

        <label for="email">Email:</label>
        <input type="email" name="email" required><br>

        <label for="password">Password:</label>
        <input type="password" name="password" required><br>

        <button type="submit">Register</button>
    </form>

    <p>Already have an account? <a href="login.php" class="button">Login here</a></p>

</body>
</html>
