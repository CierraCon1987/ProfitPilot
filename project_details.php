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

    $project_id = $_GET['project_id'];

    $stmt = $pdo->prepare("SELECT * FROM Projects WHERE project_id = ?");
    $stmt->execute([$project_id]);
    $project = $stmt->fetch();

    if (!$project) {
        echo "Project not found!";
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Collect form data from all tabs
        $hours = $_POST['hours'];
        $task = $_POST['task'];
        $date_logged = $_POST['date_logged'];
        $description = $_POST['description'];
        $invoice_id = $_POST['invoice_id'];
        $amount = $_POST['amount'];
        $due_date = $_POST['due_date'];
        $paid = $_POST['paid'];
        $amount_paid = $_POST['amount_paid'];
        $payment_date = $_POST['payment_date'];
        $payment_method = $_POST['payment_method'];
        $cost_type = $_POST['cost_type'];
        $cost_amount = $_POST['cost_amount'];
        $expense_type = $_POST['expense_type'];
        $expense_amount = $_POST['expense_amount'];
        $expense_description = $_POST['expense_description'];
        $milestone_name = $_POST['milestone_name'];
        $milestone_description = $_POST['milestone_description'];
        $milestone_due_date = $_POST['milestone_due_date'];
        $milestone_status = $_POST['milestone_status'];
        $note_title = $_POST['note_title'];
        $note_content = $_POST['note_content'];
        $task_name = $_POST['task_name'];
        $task_description = $_POST['task_description'];
        $task_due_date = $_POST['task_due_date'];
        $task_status = $_POST['task_status'];

        try {
            // TimeTracking data
            $stmt = $pdo->prepare("INSERT INTO TimeTracking (project_id, user_id, hours, date_logged, description) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$project_id, $_SESSION['user_id'], $hours, $date_logged, $description]);

            // Invoice data
            $stmt = $pdo->prepare("INSERT INTO Invoices (invoice_id, project_id, amount, due_date, paid) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$invoice_id, $project_id, $amount, $due_date, $paid]);

            // Payment data
            $stmt = $pdo->prepare("INSERT INTO Payments (invoice_id, amount_paid, payment_date, payment_method) VALUES (?, ?, ?, ?)");
            $stmt->execute([$invoice_id, $amount_paid, $payment_date, $payment_method]);

            // ProjectCosts data
            $stmt = $pdo->prepare("INSERT INTO ProjectCosts (project_id, cost_type, cost_amount, description) VALUES (?, ?, ?, ?)");
            $stmt->execute([$project_id, $cost_type, $cost_amount, $description]);

            // Expenses data
            $stmt = $pdo->prepare("INSERT INTO Expenses (project_id, expense_type, expense_amount, description) VALUES (?, ?, ?, ?)");
            $stmt->execute([$project_id, $expense_type, $expense_amount, $expense_description]);

            // ProjectMilestones data
            $stmt = $pdo->prepare("INSERT INTO ProjectMilestones (project_id, milestone_name, milestone_description, milestone_due_date, milestone_status) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$project_id, $milestone_name, $milestone_description, $milestone_due_date, $milestone_status]);

            // ProjectNotes data
            $stmt = $pdo->prepare("INSERT INTO ProjectNotes (project_id, note_title, note_content) VALUES (?, ?, ?)");
            $stmt->execute([$project_id, $note_title, $note_content]);

            // ProjectTasks data
            $stmt = $pdo->prepare("INSERT INTO ProjectTasks (project_id, task_name, task_description, task_due_date, task_status) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$project_id, $task_name, $task_description, $task_due_date, $task_status]);

            $success = "Details added successfully!";
        } catch (PDOException $e) {
            $error = "Error: " . $e->getMessage();
        }
    }
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>ProfitPilot | Project Details</title>

    <link rel="stylesheet" href="mainstyle.css">
</head>

<header>
    <div id="admin">
        <h1>ProfitPilot</h1>
        <div class="buttons">
            <a href="dashboard.php" class="button">Dashboard</a>
            <a href="view_project.php" class="button">View All Projects</a>
            <a href="logout.php" class="button">Logout</a>
        </div>
    </div>
</header>

<main>

    <h2>Project Details for <?php echo htmlspecialchars($project['project_name']); ?></h2>

       <!-- Tab navigation -->
       <div class="tabs">
        <div class="tab active-tab" onclick="openTab(event, 'timeTracking')">Time Tracking</div>
        <div class="tab" onclick="openTab(event, 'invoiceDetails')">Invoice Details</div>
        <div class="tab" onclick="openTab(event, 'payments')">Payments</div>
        <div class="tab" onclick="openTab(event, 'projectCosts')">Project Costs</div>
        <div class="tab" onclick="openTab(event, 'expenses')">Expenses</div>
        <div class="tab" onclick="openTab(event, 'milestones')">Milestones</div>
        <div class="tab" onclick="openTab(event, 'notes')">Notes</div>
        <div class="tab" onclick="openTab(event, 'notes')">Tasks</div>
    </div>

        <!-- Time Tracking Section -->
        <div id="timeTracking" class="form-section active-form">
            <form method="POST" action="project_details.php?project_id=<?php echo $project_id; ?>">
                <?php if ($success): ?>
                    <div class="success-message"><?php echo htmlspecialchars($success); ?></div>
                <?php elseif ($error): ?>
                    <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                <h3>Add Time Tracking Entry</h3>
                <label for="hours">Hours:</label>
                <input type="number" id="hours" name="hours" required><br>
                <label for="task">Task:</label>
                <input type="text" id="task" name="task" required><br>
                <label for="date_logged">Date Logged:</label>
                <input type="date" id="date_logged" name="date_logged" required><br>
                <label for="description">Description:</label>
                <textarea id="description" name="description"></textarea><br>
                <input type="submit" value="Add Time Entry">
            </form>
        </div>

        <!-- Invoice Details Section -->
        <div id="invoiceDetails" class="form-section">
            <form method="POST" action="add_invoice.php">
                <?php if ($success): ?>
                    <div class="success-message"><?php echo htmlspecialchars($success); ?></div>
                <?php elseif ($error): ?>
                    <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                <input type="hidden" name="project_id" value="<?php echo htmlspecialchars($project_id); ?>">
                <label for="invoice_id">Invoice ID:</label>
                <input type="text" id="invoice_id" name="invoice_id" required><br>
                <label for="amount">Amount:</label>
                <input type="number" id="amount" name="amount" step="0.01" required><br>
                <label for="due_date">Due Date:</label>
                <input type="date" id="due_date" name="due_date" required><br>
                <label for="paid">Paid:</label>
                <select id="paid" name="paid">
                    <option value="0">No</option>
                    <option value="1">Yes</option>
                </select><br>
                <input type="submit" value="Add Invoice">
            </form>
        </div>

        <!-- Payment Details Section -->
        <div id="payments" class="form-section">
            <form method="POST" action="add_payment.php">
                <?php if ($success): ?>
                <div class="success-message"><?php echo htmlspecialchars($success); ?></div>
                <?php elseif ($error): ?>
                    <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <input type="hidden" name="invoice_id" value="<?php echo htmlspecialchars($invoice_id); ?>">

                <label for="payment_id">Payment ID:</label>
                <input type="text" id="payment_id" name="payment_id" required>

                <label for="amount_paid">Amount Paid:</label>
                <input type="number" id="amount_paid" name="amount_paid" step="0.01" required>

                <label for="payment_date">Payment Date:</label>
                <input type="date" id="payment_date" name="payment_date" required>

                <label for="payment_method">Payment Method:</label>
                <select id="payment_method" name="payment_method">
                    <option value="Credit Card">Credit Card</option>
                    <option value="Bank Transfer">Bank Transfer</option>
                    <option value="Cash">Cash</option>
                    <option value="Cheque">Cheque</option>
                </select>

                <input type="submit" value="Add Payment">
            </form>
        </div>

        <!-- Project Costs Section -->
        <div id="projectCosts" class="form-section">
            <form method="POST" action="add_project_cost.php">
                <?php if ($success): ?>
                <div class="success-message"><?php echo htmlspecialchars($success); ?></div>
                <?php elseif ($error): ?>
                    <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <input type="hidden" name="project_id" value="<?php echo htmlspecialchars($project_id); ?>">

                <label for="cost_type">Cost Type:</label>
                <select id="cost_type" name="cost_type">
                    <option value="Labor">Labor</option>
                    <option value="Materials">Materials</option>
                    <option value="Travel">Travel</option>
                    <option value="Other">Other</option>
                </select>

                <label for="cost_amount">Cost Amount:</label>
                <input type="number" id="cost_amount" name="cost_amount" step="0.01" required>

                <label for="description">Description:</label>
                <textarea id="description" name="description" rows="4" required></textarea>

                <input type="submit" value="Add Project Cost">
            </form>
        </div>

        <!-- Expenses Details Section -->
        <div id="expenses" class="form-section">
            <form method="POST" action="add_expense.php">
                <?php if ($success): ?>
                <div class="success-message"><?php echo htmlspecialchars($success); ?></div>
                <?php elseif ($error): ?>
                    <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <input type="hidden" name="project_id" value="<?php echo htmlspecialchars($project_id); ?>">

                <label for="expense_type">Expense Type:</label>
                <select id="expense_type" name="expense_type">
                    <option value="Supplies">Supplies</option>
                    <option value="Contractor">Contractor</option>
                    <option value="Miscellaneous">Miscellaneous</option>
                </select>

                <label for="expense_amount">Expense Amount:</label>
                <input type="number" id="expense_amount" name="expense_amount" step="0.01" required>

                <label for="expense_description">Description:</label>
                <textarea id="expense_description" name="expense_description" rows="4" required></textarea>

                <input type="submit" value="Add Expense">
            </form>
        </div>

        <!-- Milestones Details Section -->
        <div id="milestones" class="form-section">
            <form method="POST" action="add_milestone.php">
                <?php if ($success): ?>
                <div class="success-message"><?php echo htmlspecialchars($success); ?></div>
                <?php elseif ($error): ?>
                    <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <input type="hidden" name="project_id" value="<?php echo htmlspecialchars($project_id); ?>">

                <label for="milestone_name">Milestone Name:</label>
                <input type="text" id="milestone_name" name="milestone_name" required>

                <label for="milestone_description">Milestone Description:</label>
                <textarea id="milestone_description" name="milestone_description" rows="4" required></textarea>

                <label for="milestone_due_date">Due Date:</label>
                <input type="date" id="milestone_due_date" name="milestone_due_date" required>

                <label for="milestone_status">Status:</label>
                <select id="milestone_status" name="milestone_status">
                    <option value="Not Started">Not Started</option>
                    <option value="In Progress">In Progress</option>
                    <option value="Completed">Completed</option>
                </select>

                <input type="submit" value="Add Milestone">
            </form>
        </div>

        <!-- Project Tasks Section -->
        <div id="tasks" class="form-section">
            <form method="POST" action="add_task.php">
                <?php if ($success): ?>
                <div class="success-message"><?php echo htmlspecialchars($success); ?></div>
                <?php elseif ($error): ?>
                    <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <input type="hidden" name="project_id" value="<?php echo htmlspecialchars($project_id); ?>">

                <label for="task_name">Task Name:</label>
                <input type="text" id="task_name" name="task_name" required>

                <label for="task_description">Task Description:</label>
                <textarea id="task_description" name="task_description" rows="4" required></textarea>

                <label for="task_due_date">Due Date:</label>
                <input type="date" id="task_due_date" name="task_due_date" required>

                <label for="task_status">Status:</label>
                <select id="task_status" name="task_status">
                    <option value="Not Started">Not Started</option>
                    <option value="In Progress">In Progress</option>
                    <option value="Completed">Completed</option>
                </select>

                <input type="submit" value="Add Task">
            </form>
        </div>

        <!-- Project Notes Section -->
        <div id="notes" class="form-section">
            <form method="POST" action="add_note.php">
                <?php if ($success): ?>
                <div class="success-message"><?php echo htmlspecialchars($success); ?></div>
                <?php elseif ($error): ?>
                    <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <input type="hidden" name="project_id" value="<?php echo htmlspecialchars($project_id); ?>">

                <label for="note_title">Note Title:</label>
                <input type="text" id="note_title" name="note_title" required>

                <label for="note_content">Note Content:</label>
                <textarea id="note_content" name="note_content" rows="4" required></textarea>

                <input type="submit" value="Add Note">
            </form>
        </div>
</main>
    <script>
        function openTab(event, tabName) {
            // Hide all sections
            var sections = document.querySelectorAll('.form-section');
            for (var i = 0; i < sections.length; i++) {
                sections[i].classList.remove('active-form');
            }

            // Remove active class from all tabs
            var tabs = document.querySelectorAll('.tab');
            for (var i = 0; i < tabs.length; i++) {
                tabs[i].classList.remove('active-tab');
            }

            // Show the clicked tab's corresponding section
            document.getElementById(tabName).classList.add('active-form');
            event.currentTarget.classList.add('active-tab');
        }
    </script>
    </body>
</html>
