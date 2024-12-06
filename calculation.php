<!-- Cierra Bailey-Rice (8998948)
     Harpreet Kaur (8893116)
     Gurkamal Singh (9001186) -->

<?php

session_start();
include('db_connection.php');

$success_message = '';
$error_message = '';

// Default values for task hours, rate, and province
$task_hours = 0;
$rate = 0;
$province_id = "";

// Initialize calculation values
$total_before_tax = 0;
$total_tax = 0;
$total_after_tax = 0;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    echo '<pre>';
    print_r($_POST); // This will display the contents of the POST request for debugging
    echo '</pre>';
}

// Check if 'project_id' is set in POST and assign it to $project_id
$project_id = isset($_POST['project_id']) ? $_POST['project_id'] : null;
if (!$project_id) {
    $error_message = "Project ID is missing.";
}

// Fetch Tasks
try {
    $taskQuery = $pdo->query("SELECT task_id, task_name FROM tasks"); 
    $tasks = $taskQuery->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching tasks: " . $e->getMessage());
}

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve form data
    $task_hours = isset($_POST['task_hours']) ? $_POST['task_hours'] : 0;
    $rate = isset($_POST['rate']) ? $_POST['rate'] : 0;
    $province_id = isset($_POST['province']) ? $_POST['province'] : '';
    $task_id = isset($_POST['task']) ? $_POST['task'] : '';
    $project_id = isset($_POST['project_id']) ? $_POST['project_id'] : null;
   
    var_dump($_POST);  // Debugging the POST data

    // Validate the inputs (basic checks)
    if ($task_hours > 0 && $rate > 0 && $province_id != "" && $task_id && $project_id) {
        // Calculate total before tax
        $total_before_tax = $task_hours * $rate;

        // Fetch Provinces
        try {
            $stmt = $pdo->query("SELECT province_id, province_name FROM provinces");
            $provinces = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error fetching provinces: " . $e->getMessage();
            exit();
        }

        // Tax rates for provinces
        $taxRates = [
            'AB' => 0.05, 'BC' => 0.12, 'MB' => 0.13, 'NB' => 0.15, 
            'NL' => 0.15, 'NS' => 0.15, 'NT' => 0.05, 'NU' => 0.05,
            'ON' => 0.13, 'PE' => 0.15, 'QC' => 0.14975, 'SK' => 0.11, 'YT' => 0.05
        ];

        // Validate tax rate for selected province
        if (isset($taxRates[$province_id])) {
            $taxRate = $taxRates[$province_id];
            $total_tax = $total_before_tax * $taxRate;
            $total_after_tax = $total_before_tax + $total_tax;

            // Insert the calculation into the database
            try {
                $stmt = $pdo->prepare("
                    INSERT INTO calculations (
                        calc_id, project_id, task_id, total_hours, total_rate, province, tax_rate, total_amount_with_tax
                    ) VALUES (
                        UUID(), :project_id, :task_id, :total_hours, :total_rate, :province, :tax_rate, :total_amount_with_tax
                    )
                ");
                $stmt->execute([
                    ':project_id' => $project_id, // Use $project_id directly
                    ':task_id' => $task_id,
                    ':total_hours' => $task_hours,
                    ':total_rate' => $rate,
                    ':province' => $province_id,
                    ':tax_rate' => $taxRate,
                    ':total_amount_with_tax' => $total_after_tax
                ]);
                $success_message = "Calculation added successfully!";
            } catch (PDOException $e) {
                $error_message = "Error adding calculation: " . $e->getMessage();
            }
        } else {
            $error_message = "Invalid province selected.";
        }
    } else {
        $error_message = "Please fill in all fields correctly.";
    }
}

?>


<?php include('header.php'); ?>

<h2>Project and Task Calculation</h2>
<p>Fill out the details below to calculate your projects potential!</p>

<?php if (isset($success_message)): ?>
    <p class="success"><?= htmlspecialchars($success_message) ?></p>
<?php endif; ?>
<?php if (isset($error)): ?>
    <p class="error"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<!-- Project Dropdown -->
<form method="POST" action="calculation.php">
    <div>
        <label for="project_id">Select Project:</label>
        <select id="project_id" name="project_id" onchange="fetchTasks(this.value)">
            <option value="">-- Select Project --</option>
            <?php
            $stmt = $pdo->query("SELECT project_id, project_name FROM projects");
            while ($row = $stmt->fetch()) {
                echo "<option value='{$row['project_id']}'>{$row['project_name']}</option>";
            }
            ?>
        </select>
    </div>

    <!-- Task Dropdown -->
    <div>
        <label for="task">Task:</label>
        <select id="task" name="task">
            <option value="">Select a Task</option>
            <?php
            foreach ($tasks as $task) {
                echo "<option value='{$task['task_id']}'>{$task['task_name']}</option>";
            }
            ?>
        </select>
    </div>

    <div>
        <label for="task_hours">Task Hours:</label>
        <input type="number" name="task_hours" id="task_hours" required>
    </div>
    <div>
        <label for="rate">Rate:</label>
        <input type="number" name="rate" id="rate" required>
    </div>

    <!-- Province Dropdown -->
    <div>
        <label for="province">Province:</label>
        <select name="province" id="province" required>
            <?php foreach ($provinces as $province): ?>
                <option value="<?= $province['province_id'] ?>"><?= $province['province_name'] ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    

    <button type="submit">Calculate</button>

    <?php if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($error_message)): ?>
    <div class="calculation-breakdown">
        <h2>Calculation Breakdown</h2>
        <p><strong>Task Hours:</strong> <?php echo $task_hours; ?> hours</p>
        <p><strong>Rate per Hour:</strong> $<?php echo number_format($rate, 2); ?></p>
        <p><strong>Province Tax Rate:</strong> <?php echo number_format($tax_rate * 100, 2); ?>%</p>
        <p><strong>Tax Amount:</strong> $<?php echo number_format($total_tax, 2); ?></p>
        <p><strong>Total Before Tax:</strong> $<?php echo number_format($total_before_tax, 2); ?></p>
        <p><strong>Total After Tax:</strong> $<?php echo number_format($total_after_tax, 2); ?></p>
        <p><strong>Formula Used:</strong> (Task Hours * Rate per Hour) + (Task Hours * Rate per Hour * Tax Rate)</p>
    </div>
<?php elseif (isset($error_message)): ?>
    <p style="color: red;"><?php echo $error_message; ?></p>
<?php endif; ?>

</form>

<script>
async function fetchTasks(projectId) {
    const response = await fetch(`fetch_tasks.php?project_id=${projectId}`);
    const tasks = await response.json();
    const taskSelect = document.getElementById('task_id');
    taskSelect.innerHTML = '<option value="">-- Select Task --</option>';
    tasks.forEach(task => {
        const option = document.createElement('option');
        option.value = task.task_id;
        option.textContent = task.task_name;
        taskSelect.appendChild(option);
    });
}

async function fetchTaskDetails(taskId) {
    const response = await fetch(`fetch_task_details.php?task_id=${taskId}`);
    const taskDetails = await response.json();
    document.getElementById('total_hours').value = taskDetails.hours_worked;
    document.getElementById('total_rate').value = taskDetails.hourly_rate;
}
</script>

<?php include('footer.php'); ?>