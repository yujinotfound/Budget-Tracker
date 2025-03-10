<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Budget Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet" href="deduct_style_form.css">
    <style>
        
    </style>
</head>

<body>
    <div class="budget-container">
        <h2 class="text-3xl font-bold mb-6 text-center">Budget Management</h2>
        <div id="message" class="message" style="display:none;"></div>
        <form id="deductForm" class="space-y-4">
            <div class="form-control">
                <label for="budget_id" class="form-label">Select Budget Category</label>
                <select name="budget_id" id="budget_id" class="form-input" required>
                    <option value="">Choose a budget category</option>
                    <?php 
                    session_start();
                    include "db_conn.php";
                    if (isset($_SESSION['id'])) {
                        $user_id = $_SESSION['id']; // Ensure this matches your session variable
                        $result = $conn->query("SELECT id, Purpose, remaining_amount FROM budgets WHERE user_id = $user_id");
                        while ($row = $result->fetch_assoc()) {
                            echo "<option value='{$row['id']}'>{$row['Purpose']} - ₱" . 
                                 number_format($row['remaining_amount'], 2) . "</option>";
                        }
                    } else {
                        header("Location: index.php");
                        exit();
                    }
                    ?>
                </select>
            </div>
            <div class="form-control">
                <label for="amount" class="form-label">Deduction Amount (₱)</label>
                <input type="number" name="amount" id="amount" class="form-input" step="0.01" min="0" placeholder="Enter amount" required>
            </div>
            <div class="button-container">
                <button type="submit" class="submit-button w-full">Process Deduction</button>
                <a href="home.php" class="back-button">Back to Main Menu</a>
            </div>
        </form>
    </div>
    <script>
        document.getElementById('deductForm').addEventListener('submit', async function (e) {
            e.preventDefault();
            const submitButton = this.querySelector('button[type="submit"]');
            submitButton.disabled = true;
            submitButton.innerHTML = 'Processing...';
            const messageDiv = document.getElementById('message');
            messageDiv.className = ''; // Reset any existing message class
            messageDiv.innerHTML = ''; // Clear previous messages
            try {
                const formData = new FormData(this);
                const response = await fetch('deduct_budget_process.php', {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();
                messageDiv.className = data.status === 'success' ? 'message success' : 'message error';
                messageDiv.innerHTML = data.message;
                messageDiv.style.display = 'block';
                if (data.status === 'success') {
                    setTimeout(() => location.reload(), 1500);
                }
            } catch (error) {
                messageDiv.className = 'message error';
                messageDiv.innerHTML = 'An unexpected error occurred. Please try again.';
                messageDiv.style.display = 'block';
            } finally {
                submitButton.disabled = false;
                submitButton.innerHTML = 'Process Deduction';
            }
        });
    </script>
</body>

</html>