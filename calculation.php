<!-- Cierra Bailey-Rice (8998948)
     Harpreet Kaur (8893116)
     Gurkamal Singh (9001186) -->

<?php
    session_start();
    include('db_connection.php');

    $success_message = '';
    $error_message = '';
    $calculation_breakdown = '';

    // Get Provinces and Tasks
    try {
        $taskQuery = $pdo->query("SELECT task_id, task_name FROM tasks"); 
        $tasks = $taskQuery->fetchAll(PDO::FETCH_ASSOC);
        $stmt = $pdo->query("SELECT province_id, province_name, tax_rate FROM provinces");
        $provinces = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("Error fetching data: " . $e->getMessage());
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $action = $_POST['action'] ?? '';

        if ($action === 'calculate') {
            $project_id = $_POST['project_id'];
            $task_id = $_POST['task'];
            $task_hours = (float) $_POST['task_hours'];
            $rate = (float) $_POST['rate'];
            $province_id = $_POST['province'];

            // Province tax rate calculation
            $province_tax_rates = [
                'AB' => 0.05, 'BC' => 0.12, 'MB' => 0.13, 'NB' => 0.15, 
                'NL' => 0.15, 'NS' => 0.15, 'NT' => 0.05, 'NU' => 0.05,
                'ON' => 0.13, 'PE' => 0.15, 'QC' => 0.14975, 'SK' => 0.11, 'YT' => 0.05
            ];
            $taxRate = $province_tax_rates[$province_id] ?? 0;

            try {
                //fetching hourly rate from tasks table
                $stmt = $pdo->prepare("SELECT hourly_rate FROM tasks WHERE task_id = :task_id");
                $stmt->execute([':task_id' => $task_id]);
                $task = $stmt->fetch(PDO::FETCH_ASSOC);

                // Calculation of total rate and total amount
                if ($task) {
                    $hourly_rate = (float) $task['hourly_rate'];
                    $total_amount = $hourly_rate * $task_hours;
                    $total_tax = $total_amount * $taxRate;
                    $total_after_tax = $total_amount + $total_tax;

                    // Insert total calculations into the calculations table
                    $stmt = $pdo->prepare("
                        INSERT INTO calculations (
                            calc_id, project_id, task_id, total_hours, total_rate, total_amount, province, tax_rate, total_amount_with_tax
                        ) VALUES (
                            UUID(), :project_id, :task_id, :total_hours, :total_rate, :total_amount, :province, :tax_rate, :total_amount_with_tax
                        )
                    ");

                    $stmt->execute([
                        ':project_id' => $project_id,
                        ':task_id' => $task_id,
                        ':total_hours' => $task_hours,
                        ':total_rate' => $hourly_rate,
                        ':total_amount' => $total_amount,
                        ':province' => $province_id,
                        ':tax_rate' => $taxRate,
                        ':total_amount_with_tax' => $total_after_tax
                    ]);

                    if ($stmt->rowCount() > 0) {
                        $_SESSION['calculation_breakdown'] = "
                            <h3>Calculation Breakdown:</h3>
                            <p><strong>Task:</strong> $task_id</p>
                            <p><strong>Hours:</strong> $task_hours</p>
                            <p><strong>Rate per Hour:</strong> $$hourly_rate</p>
                            <p><strong>Total Before Tax:</strong> $$total_amount</p>
                            <p><strong>Tax Rate:</strong> " . ($taxRate * 100) . "%</p>
                            <p><strong>Total Tax:</strong> $$total_tax</p>
                            <p><strong>Total After Tax:</strong> $$total_after_tax</p>
                        ";
                        header('Location: calculation_breakdown.php');
                        exit();
                    } else {
                        $error_message = "Failed to add calculation to the database.";
                    }
                } else {
                    $error_message = "Task not found. Please select a valid task.";
                }
            } catch (PDOException $e) {
                $error_message = "Database error: " . $e->getMessage();
            }
        }
    }
?>


<?php include('header.php'); ?>

<h2>Project and Task Calculation</h2>
<p>Fill out the details below to calculate your project's potential!</p>

<!-- View All Calculations -->
<a href="calculation_breakdown.php"  class="button">See All Previous Calculations</a>

    <?php if (isset($success_message)): ?>
        <p id="success-message" class="success"><?= htmlspecialchars($success_message) ?></p>
    <?php endif; ?>
    <?php if (isset($error_message)): ?>
        <p id="error-message" class="error"><?= htmlspecialchars($error_message) ?></p>
    <?php endif; ?>

    <!-- Project Dropdown -->
    <form method="POST" action="calculation.php">
        <input type="hidden" name="action" value="calculate">

        <div class="cal-form">
            <label for="project_id">Select Project:</label>
            <select id="project_id" name="project_id" onchange="fetchTasks(this.value)" required class="input-style">
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
     <!--task will be fetched from the project selected-->
    <div class="cal-form">
        <label for="task">Task:</label>
        <select id="task" name="task" required onchange="fetchTaskDetails(this.value)" class="input-style">
            <option value="">Select a Task</option>
        </select>
    </div>

    <!-- Task Hours Section -->
      <!-- Task Hours fetched from tasks table according to task selected -->
    <div class="cal-form">
        <label for="task_hours">Task Hours:</label>
        <input type="number" name="task_hours" id="task_hours" required>
    </div>

    <!-- Hourly Rate Section -->
    <div class="cal-form">
        <label for="rate">Hourly Rate:</label>
        <input type="number" name="rate" id="rate" required>
    </div>

    <!-- Province Dropdown -->
    <div class="cal-form">
        <label for="province">Province:</label>
        <select name="province" id="province" required class="input-style" >
        <option value="">-- Select Province --</option>
            <?php foreach ($provinces as $province): ?>
                <option value="<?= htmlspecialchars($province['province_id']) ?>" data-tax-rate="<?= htmlspecialchars($province['tax_rate']) ?>">
                    <?= htmlspecialchars($province['province_name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <input type="hidden" id="tax_rate" name="tax_rate">

    <button type="submit" class="button">Calculate</button>
</form>


<!-- JS Section -->
 <!-- sucess and error message will be disappear after 10 seconds -->
    <script>
        setTimeout(() => {
            const successMessage = document.getElementById('success-message');
            if (successMessage) successMessage.style.display = 'none';
            
            const errorMessage = document.getElementById('error-message');
            if (errorMessage) errorMessage.style.display = 'none';
        }, 10000);
    </script>

    <script>
        //fetching tasks from task table that associated to a project
        async function fetchTasks(projectId) {
            // Check if projectId is selected
            if (projectId) {
                try {
                    const response = await fetch(`fetch_tasks.php?project_id=${projectId}`);
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }

                    // JSON response
                    const tasks = await response.json();
                    const taskSelect = document.getElementById('task');

                    taskSelect.innerHTML = '<option value="">Select Task</option>';

                    // Check if tasks are returned
                    if (tasks.length > 0) {
                        tasks.forEach(task => {
                            const option = document.createElement('option');
                            option.value = task.task_id;
                            option.textContent = task.task_name;
                            taskSelect.appendChild(option);
                        });
                    } else {
                        const option = document.createElement('option');
                        option.value = '';
                        option.textContent = 'No tasks available for this project';
                        taskSelect.appendChild(option);
                    }
                } catch (error) {
                    console.error('Error fetching tasks:', error);
                }
            } else {
                document.getElementById('task').innerHTML = '<option value="">Select Task</option>';
            }
        }

        async function fetchTaskDetails(taskId) {
            if (taskId) {
                try {
                    const response = await fetch(`fetch_task_details.php?task_id=${taskId}`);
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    const taskDetails = await response.json();

                    // Auto-fill task hours and rate
                    document.getElementById('task_hours').value = taskDetails.hours_worked;
                    document.getElementById('rate').value = taskDetails.hourly_rate;
                } catch (error) {
                    console.error('Error fetching task details:', error);
                }
            }
        }
    </script>

    <script>
        function updateTaxRate() {
            const provinceSelect = document.getElementById('province');
            const selectedOption = provinceSelect.options[provinceSelect.selectedIndex];
            const taxRate = selectedOption.getAttribute('data-tax-rate');
            document.getElementById('tax_rate').value = taxRate || 0; // Default is 0 if no tax rate
        }
    </script>

<?php include('footer.php'); ?>
