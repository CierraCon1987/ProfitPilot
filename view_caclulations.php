<?php
include('db_connection.php');
try {
    $stmt = $pdo->query("SELECT * FROM calculations");
    $calculations = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching calculations: " . $e->getMessage());
}
?>

<h2>Calculations</h2>
<table>
    <thead>
        <tr>
            <th>Calc ID</th>
            <th>Project ID</th>
            <th>Task ID</th>
            <th>Total Hours</th>
            <th>Total Rate</th>
            <th>Province</th>
            <th>Tax Rate</th>
            <th>Total Amount with Tax</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($calculations as $calc): ?>
            <tr>
                <td><?= htmlspecialchars($calc['calc_id']) ?></td>
                <td><?= htmlspecialchars($calc['project_id']) ?></td>
                <td><?= htmlspecialchars($calc['task_id']) ?></td>
                <td><?= htmlspecialchars($calc['total_hours']) ?></td>
                <td><?= htmlspecialchars($calc['total_rate']) ?></td>
                <td><?= htmlspecialchars($calc['province']) ?></td>
                <td><?= htmlspecialchars($calc['tax_rate']) ?></td>
                <td><?= htmlspecialchars($calc['total_amount_with_tax']) ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
