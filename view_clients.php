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

        $user_id = $_SESSION['user_id'];

        // Get all clients related to the logged-in user
        $search = $_GET['search'] ?? '';
        $query = "
        SELECT *, 
            CONCAT(address_line1, ' ', IFNULL(address_line2, ''), ' ', city, ' ', province, ' ', postal_code, ' ', country) AS full_address
            FROM Clients
            WHERE user_id = ? OR user_id IS NULL
            AND (client_name LIKE ? OR email LIKE ? OR city LIKE ? OR country LIKE ?)
        ";
        $stmt = $pdo->prepare($query);
        $stmt->execute([
            $_SESSION['user_id'], 
            "%$search%", "%$search%", "%$search%", "%$search%"
        ]);
        $clients = $stmt->fetchAll();
    ?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ProfitPilot | View Clients</title>
</head>

<body>
    <header>
        <h1>Manage Clients</h1>
        <a href="dashboard.php">Back to Dashboard</a>
        <a href="logout.php">Logout</a>
    </header>

    <main>
        <h2>Your Clients</h2>

        <!-- Error Message Section -->
        <?php if (isset($error)): ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>

        <!-- Search Bar -->
        <form method="GET" action="view_clients.php">
            <input type="text" name="search" placeholder="Search clients..." value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit">Search</button>
        </form>

        <!-- Clients Table Info -->
        <?php if (count($clients) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Client Name</th>
                    <th>Contact</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Address</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($clients as $client): ?>
                    <tr>
                    <td><?php echo htmlspecialchars($client['client_name']); ?></td>
                    <td><?php echo htmlspecialchars($client['contact_first'] . ' ' . $client['contact_last']); ?></td>
                    <td><?php echo htmlspecialchars($client['phone_number']); ?></td>
                    <td><?php echo htmlspecialchars($client['email']); ?></td>
                    <td><?php echo htmlspecialchars($client['full_address']); ?></td>
                    <td>
                        <a href="edit_client.php?client_id=<?php echo $client['client_id']; ?>">Edit</a>
                        <a href="delete_client.php?client_id=<?php echo $client['client_id']; ?>" onclick="return confirm('Are you sure you want to delete this client?');">Delete</a>
                    </td>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="delete_client_id" value="<?php echo $client['client_id']; ?>">
                                <button type="submit" onclick="return confirm('Are you sure you want to delete this client?');">Delete</button>
                            </form>
                            <a href="edit_client.php?client_id=<?php echo $client['client_id']; ?>">Edit</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
            <p>No clients found. <a href="add_client.php">Add your first client!</a></p>
        <?php endif; ?>

        <h3>Other Actions</h3>
        <a href="add_client.php">Add Client</a>
    </main>
</body>
</html>
