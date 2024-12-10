<?php
    session_start();
    include('db_connection.php');

    $calculation_breakdown = '';

    $calculation_breakdown = $_SESSION['calculation_breakdown'] ?? 'No calculation data available.';
    unset($_SESSION['calculation_breakdown']); 

    $previous_calculations = [];

    try {
        $stmt = $pdo->query("
            SELECT 
                c.calc_id, 
                c.total_hours, 
                c.total_rate, 
                c.total_amount, 
                c.total_amount_with_tax,
                t.task_name, 
                p.project_name
            FROM calculations c
            JOIN tasks t ON c.task_id = t.task_id
            JOIN projects p ON c.project_id = p.project_id
            ORDER BY c.calc_id DESC
        ");
        $previous_calculations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Error fetching calculations: " . $e->getMessage();
        exit();
    }
    
    // Fetch full details of a selected calculation
    if (isset($_GET['calc_id'])) {
        $calc_id = $_GET['calc_id'];
        
        try {
            $stmt = $pdo->prepare("
                SELECT 
                    c.calc_id, 
                    c.total_hours, 
                    c.total_rate, 
                    c.total_amount, 
                    c.total_amount_with_tax,
                    t.task_name, 
                    p.project_name, 
                    p.start_date, 
                    p.end_date, 
                    p.status,
                    t.created_at
                FROM calculations c
                JOIN tasks t ON c.task_id = t.task_id
                JOIN projects p ON c.project_id = p.project_id
                WHERE c.calc_id = :calc_id
            ");
            $stmt->execute([':calc_id' => $calc_id]);
            $calculation_details = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($calculation_details) {
                $calculation_breakdown = "
                    <h3>Calculation Breakdown (ID: {$calculation_details['calc_id']})</h3>
                    <p><strong>Task Name:</strong> {$calculation_details['task_name']}</p>
                    <p><strong>Project Name:</strong> {$calculation_details['project_name']}</p>
                    <p><strong>Project Start Date:</strong> {$calculation_details['start_date']}</p>
                    <p><strong>Project End Date:</strong> {$calculation_details['end_date']}</p>
                    <p><strong>Project Status:</strong> {$calculation_details['status']}</p>
                    <p><strong>Task Creation Date:</strong> {$calculation_details['created_at']}</p>
                    <p><strong>Hours Worked:</strong> {$calculation_details['total_hours']}</p>
                    <p><strong>Rate per Hour:</strong> \${$calculation_details['total_rate']}</p>
                    <p><strong>Total Before Tax:</strong> \${$calculation_details['total_amount']}</p>
                    <p><strong>Total (With Tax):</strong> \${$calculation_details['total_amount_with_tax']}</p>
                ";
            } else {
                $calculation_breakdown = "<p>Calculation not found.</p>";
            }
        } catch (PDOException $e) {
            $calculation_breakdown = "Error fetching calculation details: " . $e->getMessage();
        }
    }   
?>

<?php include('header.php'); ?>

    <h2>Calculation Breakdown</h2>
    <div class="calculation-results">
        <?= $calculation_breakdown ?>

    <!-- Print Button -->
    <button class="btn-print" onclick="window.print()">Print Calculation</button>
    </div>

    <h3>Previous Calculations</h3>
    <table class="calculation-list">
        <thead>
            <tr>
                <th>Task Name</th>
                <th>Project Name</th>
                <th>Total Amount</th>
                <th>Total (With Tax)</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($previous_calculations as $calc): ?>
                <tr>
                    <td><?= htmlspecialchars($calc['task_name']) ?></td>
                    <td><?= htmlspecialchars($calc['project_name']) ?></td>
                    <td>$<?= number_format($calc['total_amount'], 2) ?></td>
                    <td>$<?= number_format($calc['total_amount_with_tax'], 2) ?></td>
                    <td>
                        <a href="calculation_breakdown.php?calc_id=<?= $calc['calc_id'] ?>">View Details</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

<?php include('footer.php'); ?>

