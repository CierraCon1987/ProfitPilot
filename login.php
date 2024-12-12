<!-- Cierra Bailey-Rice (8998948)
     Harpreet Kaur (8893116)
     Gurkamal Singh (9001186) -->

<?php

    session_start();
    include('db_connection.php');

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email_or_username = $_POST['email_or_username'];
        $password = $_POST['password'];
    
        $stmt = $pdo->prepare("SELECT * FROM Users WHERE (email = ? OR username = ?)");
        $stmt->execute([$email_or_username, $email_or_username]);
        $user = $stmt->fetch();
    
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['first_name'] = $user['first_name'];
            $_SESSION['last_name'] = $user['last_name'];
            $_SESSION['username'] = $user['username'];
    
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Invalid credentials.";
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ProfitPilot | Profile</title>

    <!-- Custom Styling -->
    <link rel="stylesheet" href="mainstyle.css">
</head>

<body>
    <main>

        <header>
            <div id="admin">
                <h1>ProfitPilot</h1>
            </div>
        </header>

    <h2>Login</h2>


    <?php if (isset($error)) { echo "<p style='color: red;'>$error</p>"; } ?>

    <form action="login.php" method="POST">
        <label for="email_or_username">Email or Username:</label>
        <input type="text" id="email_or_username" name="email_or_username" >
        <br>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password">
        <br>

        <input type="submit" value="Login">
    </form>

    <p>Don't have an account? <a href="register.php" class="button">Register here</a></p>

    <?php include('footer.php'); ?>