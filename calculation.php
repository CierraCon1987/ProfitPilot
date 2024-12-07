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
    // Fetch form data
    $project_id = $_POST['project_id'];  // Already a string, keep it as is
    $task_id = $_POST['task'];  // Already a string, keep it as is
    $task_hours = (float) $_POST['task_hours'];  // Cast to float for calculations
    $rate = (float) $_POST['rate'];  // Cast to float for calculations
    $province_id = $_POST['province'];  // This seems to be a 2-letter province code (string)

    $province_tax_rates = [
        'AB' => 0.05, 'BC' => 0.12, 'MB' => 0.13, 'NB' => 0.15, 
        'NL' => 0.15, 'NS' => 0.15, 'NT' => 0.05, 'NU' => 0.05,
        'ON' => 0.13, 'PE' => 0.15, 'QC' => 0.14975, 'SK' => 0.11, 'YT' => 0.05
    ];
    // Perform calculations
    $total_rate = $rate * $task_hours;
    $taxRate = 0.13;  // Example: Assuming a 13% tax rate
    $total_after_tax = $total_rate + ($total_rate * $taxRate);

    // Prepare and execute the insert query
    try {
        $stmt = $pdo->prepare("
            INSERT INTO calculations (
                calc_id, project_id, task_id, total_hours, total_rate, total_amount,province, tax_rate, total_amount_with_tax
            ) VALUES (
                UUID(), :project_id, :task_id, :total_hours, :total_rate, :total_amount,:province, :tax_rate, :total_amount_with_tax
            )
        ");
        $stmt->execute([
            ':project_id' => $project_id,
            ':task_id' => $task_id,
            ':total_hours' => $task_hours,
            ':total_rate' => $total_rate,
            ':total_amount' => $task_hours *$total_rate,
            ':province' => $province_id,
            ':tax_rate' => $taxRate,
            ':total_amount_with_tax' => $total_after_tax
        ]);

        // Check for success
        if ($stmt->rowCount() > 0) {
            $success_message = "Calculation added successfully!";
        } else {
            $error_message = "Failed to add calculation to the database.";
        }

    } catch (PDOException $e) {
        $error_message = "Error adding calculation: " . $e->getMessage();
    }
}

// Fetch Tasks
try {
    $taskQuery = $pdo->query("SELECT task_id, task_name FROM tasks"); 
    $tasks = $taskQuery->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching tasks: " . $e->getMessage());
}

// Fetch Provinces
// Fetch Provinces
try {
    $stmt = $pdo->query("SELECT province_id, province_name, tax_rate FROM provinces");
    $provinces = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error fetching provinces: " . $e->getMessage();
    exit();
}


?>


<?php include('header.php'); ?>

<h2>Project and Task Calculation</h2>
<p>Fill out the details below to calculate your project's potential!</p>

<?php if (isset($success_message)): ?>
    <p id="success-message" class="success"><?= htmlspecialchars($success_message) ?></p>
<?php endif; ?>
<?php if (isset($error_message)): ?>
    <p id="error-message" class="error"><?= htmlspecialchars($error_message) ?></p>
<?php endif; ?>


<!-- Project Dropdown -->
<form method="POST" action="calculation.php">
    <div>
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
    <div>
        <label for="task">Task:</label>
        <select id="task" name="task" required onchange="fetchTaskDetails(this.value)" class="input-style">
            <option value="">Select a Task</option>

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
        
        <select name="province" id="province" required class="input-style" >
        <option value="">-- Select Province --</option>
        <?php foreach ($provinces as $province): ?>
    <option value="<?= $province['province_id'] ?>" data-tax-rate="<?= $province['tax_rate'] ?>">
        <?= $province['province_name'] ?>
    </option>
<?php endforeach; ?>

        </select>
    </div>
    <input type="hidden" id="tax_rate" name="tax_rate">

    <button type="submit">Calculate</button>
            </form>
            <script>
    // Hide success message after 5 seconds
    setTimeout(function() {
        const successMessage = document.getElementById('success-message');
        if (successMessage) {
            successMessage.style.display = 'none';
        }
    }, 5000);

    // Hide error message after 5 seconds
    setTimeout(function() {
        const errorMessage = document.getElementById('error-message');
        if (errorMessage) {
            errorMessage.style.display = 'none';
        }
    }, 5000);
</script>
    <script>
async function fetchTasks(projectId) {
    // Check if projectId is selected
    if (projectId) {
        try {
            // Make the fetch request
            const response = await fetch(`fetch_tasks.php?project_id=${projectId}`);
            
            // Check if the response is valid
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }

            // Parse the JSON response
            const tasks = await response.json();

            // Get the task dropdown element
            const taskSelect = document.getElementById('task');

            // Clear existing tasks (to avoid appending old data)
            taskSelect.innerHTML = '<option value="">Select Task</option>';

            // Check if tasks are returned
            if (tasks.length > 0) {
                tasks.forEach(task => {
                    // Create new option element for each task
                    const option = document.createElement('option');
                    option.value = task.task_id;
                    option.textContent = task.task_name;

                    // Append the new option to the dropdown
                    taskSelect.appendChild(option);
                });
            } else {
                // If no tasks were returned, inform the user
                const option = document.createElement('option');
                option.value = '';
                option.textContent = 'No tasks available for this project';
                taskSelect.appendChild(option);
            }
        } catch (error) {
            console.error('Error fetching tasks:', error);
        }
    } else {
        // If no project is selected, clear the task dropdown
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
    document.getElementById('tax_rate').value = taxRate || 0; // Default to 0 if no tax rate
}
</script>

<?php include('footer.php'); ?>
