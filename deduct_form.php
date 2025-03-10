<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Deduct from Budget</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>

<body>
    <div class="budget-form-container">
        <h2>Deduct from Budget</h2>
        <div id="message"></div>
        <form id="deductForm">
            <div class="form-group">
                <label for="budget_id">Select Budget:</label>
                <select name="budget_id" id="budget_id" required>
                    <option value="">Select a budget</option>
                    <?php
                    session_start();
                    include "db_conn.php";

                    if (isset($_SESSION['id'])) {
                        $user_id = $_SESSION['id']; // Ensure this matches your session variable
                        $result = $conn->query("SELECT id, Purpose, remaining_amount FROM budgets WHERE user_id = $user_id");

                        while ($row = $result->fetch_assoc()) {
                            echo "<option value='{$row['id']}'>{$row['Purpose']} - Remaining: PHP " . number_format($row['remaining_amount'], 2) . "</option>";
                        }
                    } else {
                        header("Location: index.php");
                        exit();
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="amount">Amount to Deduct (PHP):</label>
                <input type="number" name="amount" id="amount" step="0.01" min="0" required>
            </div>
            <button type="submit" class="submit-btn">Deduct Amount</button>
        </form>
    </div>
</body>

</html>