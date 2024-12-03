<!-- Cierra Bailey-Rice (8998948)
     Harpreet Kaur (8893116)
     Gurkamal Singh () -->

<?php

    session_start();
    include('db_connection.php');

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        // Allow User to Login with Username or Email
        $email_or_username = htmlspecialchars($_POST['email_or_username']);
        $password = $_POST['password'];

        // Verify User Info
        if (empty($email_or_username) || empty($password)) {
            $error = "Please enter both email/username and password!";
        } else {
            try {

                $stmt = $pdo->prepare("SELECT * FROM Users WHERE email = ? OR username = ?");
                $stmt->execute([$email_or_username, $email_or_username]);
                $user = $stmt->fetch();

                if ($user) {

                    if (password_verify($password, $user['password'])) {

                    $_SESSION['user_id'] = $user['user_id'];
                    $_SESSION['first_name'] = $user['first_name'];
                    $_SESSION['last_name'] = $user['last_name'];
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['username'] = $user['username'];

                    header("Location: dashboard.php");
                    exit();
                } else {
                    $error = "Invalid password!";
                }
            } else {
                $error = "User not found!";
            }
        } catch (PDOException $e) {
            $error = "Error logging in: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ProfitPilot | Login</title>

    <!-- Custom Styling -->
    <link rel="stylesheet" href="mainstyle.css">
</head>

<body>
    <h1>Login</h1>

    <?php if (isset($error)) { echo "<p style='color: red;'>$error</p>"; } ?>

    <form action="login.php" method="POST">
        <label for="email_or_username">Email or Username:</label>
        <input type="text" id="email_or_username" name="email_or_username" required>
        <br>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
        <br>

        <input type="submit" value="Login">
    </form>

    <p>Don't have an account? <a href="register.php">Register here</a></p>

</body>
</html>