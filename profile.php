<!-- Cierra Bailey-Rice (8998948)
     Harpreet Kaur (8893116)
     Gurkamal Singh (9001186) -->

     <?php
        include('db_connection.php');
        session_start();

        // Redirect to login.php if user is not already logged in
        if (!isset($_SESSION['user_id'])) {
            header("Location: login.php");
            exit();
        }

        // Get current user info
        $user_id = $_SESSION['user_id'];
        $stmt = $pdo->prepare("SELECT first_name, last_name, email FROM Users WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        $error = "";
        $success = "";

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $first_name = trim($_POST['first_name']);
            $last_name = trim($_POST['last_name']);
            $email = trim($_POST['email']);
            $password = trim($_POST['password']);
            $confirm_password = trim($_POST['confirm_password']);

            if (empty($first_name) || empty($last_name) || empty($email)) {
                $error = "All fields except password are required.";
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = "Invalid email format.";
            } elseif (!empty($password) && $password !== $confirm_password) {
                $error = "Passwords do not match.";
            } else {

                $update_query = "UPDATE Users SET first_name = ?, last_name = ?, email = ?";
                $params = [$first_name, $last_name, $email];

                if (!empty($password)) {
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    $update_query .= ", password = ?";
                    $params[] = $hashed_password;
                }

                $update_query .= " WHERE user_id = ?";
                $params[] = $user_id;

                $stmt = $pdo->prepare($update_query);

                if ($stmt->execute($params)) {
                    $success = "Profile updated successfully.";
                    $_SESSION['first_name'] = $first_name;
                } else {
                    $error = "Failed to update profile.";
                }
            }
        }
?>


<?php include('header.php'); ?>


        <h2>Your Account Information</h2>
        <p>Update Your Account Information</p>

        <?php if (!empty($error)): ?>
            <p class="error-message"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        
        <?php if (!empty($success)): ?>
            <p class="success-message"><?php echo htmlspecialchars($success); ?></p>
        <?php endif; ?>

        <form method="POST" action="profile.php">
            <label for="first_name">First Name:</label>
            <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" required>

            <label for="last_name">Last Name:</label>
            <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

            <label for="password">New Password:</label>
            <input type="password" id="password" name="password" placeholder="Leave blank to keep current password">

            <label for="confirm_password">Confirm New Password:</label>
            <input type="password" id="confirm_password" name="confirm_password" placeholder="Leave blank to keep current password">

            <button type="submit">Update Profile</button>
        </form>
</main>
<?php include('footer.php'); ?>

</body>
</html>
