<?php
    session_start();
    include('db_connection.php');

    $success_message = '';
    $error = '';
    $total_hours = '';
    $total_rate = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $task_id = $_POST['task_id'];
        $total_hours = floatval($_POST['total_hours']);
        $total_rate = floatval($_POST['total_rate']); 
        $province = $_POST['province'];
        
        // Tax rates for provinces
        $taxRates = [
            'AB' => 0.05, 'BC' => 0.12, 'MB' => 0.13, 'NB' => 0.15, 
            'NL' => 0.15, 'NS' => 0.15, 'NT' => 0.05, 'NU' => 0.05,
            'ON' => 0.13, 'PE' => 0.15, 'QC' => 0.14975, 'SK' => 0.11, 'YT' => 0.05
        ];
        
        // Calculation for tax and total amount with tax
        if ($total_hours && $total_rate && isset($taxRates[$province])) {
            $taxRate = $taxRates[$province];
            $taxAmount = $total_hours * $total_rate * $taxRate;
            $totalAmountWithTax = ($total_hours * $total_rate) + $taxAmount;
        } else {
            echo "Invalid input. Please make sure all fields are filled out correctly.";
            exit;
        }
    
        try {
            $stmt = $pdo->prepare("INSERT INTO calculations (calc_id, project_id, total_hours, total_rate, total_amount, province, tax_rate, total_amount_with_tax) 
                VALUES (UUID(), :project_id, :total_hours, :total_rate, :total_amount, :province, :tax_rate, :total_amount_with_tax)");
            
            $stmt->execute([
                ':project_id' => $project_id,
                ':total_hours' => $total_hours,
                ':total_rate' => $total_rate,
                ':total_amount' => $total_hours * $total_rate,
                ':province' => $province,
                ':tax_rate' => $taxRate,
                ':total_amount_with_tax' => $totalAmountWithTax
            ]);
    
            echo "Calculation added successfully!";
        } catch (PDOException $e) {
            echo "Error adding calculation: " . $e->getMessage();
        }
    }
?>

<?php include('header.php'); ?>
<h2>Project Calculation</h2>
<p>Fill out the details below to calculate your projects potential!</p>

<?php if (isset($success_message)): ?>
    <p class="success"><?= htmlspecialchars($success_message) ?></p>
<?php endif; ?>
<?php if (isset($error)): ?>
    <p class="error"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<!-- Project Selection -->
<form action="calculation.php" method="POST">
    <div>
        <label for="task_id">Select Task:</label>
        <select name="task_id" id="task_id" onchange="updateFields()">
                <option value="">-- Select Task --</option>
                <?php
                    $stmt = $pdo->prepare("SELECT task_id, task_description FROM tasks WHERE user_id = :user_id");
                    $stmt->execute([':user_id' => $_SESSION['user_id']]);
                    $tasks = $stmt->fetchAll();
                    
                    foreach ($tasks as $task) {
                        echo "<option value='{$task['task_id']}'>{$task['task_description']}</option>";
                    }
                ?>
            </select>
    </div>

    <div>
        <label for="total_hours">Hours Worked:</label>
        <input type="number" name="total_hours" id="total_hours" value="5" required>
    </div>

    <div>
        <label for="total_rate">Hourly Rate:</label>
        <input type="number" name="total_rate" id="total_rate" value="50" required>
    </div>

    <div>
        <label for="province">Select Province:</label>
        <select name="province" id="province">
        <option value="">Select Province</option>
        <option value="AB">Alberta</option>
        <option value="BC">British Columbia</option>
        <option value="MB">Manitoba</option>
        <option value="NB">New Brunswick</option>
        <option value="NL">Newfoundland and Labrador</option>
        <option value="NS">Nova Scotia</option>
        <option value="NT">Northwest Territories</option>
        <option value="NU">Nunavut</option>
        <option value="ON">Ontario</option>
        <option value="PE">Prince Edward Island</option>
        <option value="QC">Quebec</option>
        <option value="SK">Saskatchewan</option>
        <option value="YT">Yukon</option>
        </select>
    </div>

    <div>
        <label for="total_amount">Total Amount (Before Tax):</label>
        <input type="text" id="total_amount" value="0" disabled>
    </div>

    <div>
        <label for="tax_rate">Tax Rate:</label>
        <input type="text" id="tax_rate" value="0%" disabled>
    </div>

    <div>
        <label for="total_amount_with_tax">Total Amount (With Tax):</label>
        <input type="text" id="total_amount_with_tax" value="0" disabled>
    </div>

    <button type="submit">Calculate</button>
</form>

<!-- Display Calculated Total Amount -->
<?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
    <div class="calculation">
        <h3>Calculated Total Amount</h3>
        <p><strong>Total Amount: $</strong><?= number_format($total_hours * $total_rate, 2) ?></p>
        <p><strong>Tax Rate: </strong><?= (isset($taxRate) ? $taxRate * 100 : 0) ?>%</p>
        <p><strong>Total Amount with Tax: $</strong><?= number_format($totalAmountWithTax, 2) ?></p>
    </div>
    <?php endif; ?>

<?php include('footer.php'); ?>

<script>
    function updateFields() {
        var taskSelect = document.getElementById("task_id");
        var selectedTask = taskSelect.options[taskSelect.selectedIndex];
        var hours = selectedTask.getAttribute("data-hours");
        var rate = selectedTask.getAttribute("data-rate");

        // Update the form fields with the selected task's values
        document.getElementById("total_hours").value = hours;
        document.getElementById("total_rate").value = rate;

        updateCalculation();  // Recalculate totals based on the new values
    }

    function updateCalculation() {
        var hours = parseFloat(document.getElementById("total_hours").value);
        var rate = parseFloat(document.getElementById("total_rate").value);
        var provinceSelect = document.getElementById("province");
        var province = provinceSelect.value;

        var taxRates = {
            'AB': 0.05, 'BC': 0.12, 'MB': 0.13, 'NB': 0.15, 
            'NL': 0.15, 'NS': 0.15, 'NT': 0.05, 'NU': 0.05,
            'ON': 0.13, 'PE': 0.15, 'QC': 0.14975, 'SK': 0.11, 'YT': 0.05
        };

        if (taxRates[province]) {
            var taxRate = taxRates[province];
            var totalBeforeTax = hours * rate;
            var taxAmount = totalBeforeTax * taxRate;
            var totalWithTax = totalBeforeTax + taxAmount;

            // Update the form fields with the calculated values
            document.getElementById("total_amount").value = totalBeforeTax.toFixed(2);
            document.getElementById("tax_rate").value = (taxRate * 100).toFixed(2) + '%';
            document.getElementById("total_amount_with_tax").value = totalWithTax.toFixed(2);
        }
    }

    // Initial call to set values based on the default task
    updateFields();
</script>