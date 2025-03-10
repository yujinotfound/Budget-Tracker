<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Budgets</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
    
</head>
<body>
    <h2>All Budgets</h2>

    <div class="search-container">
    <input type="text" id="searchBar" placeholder="Search by Purpose..." onkeyup="searchTable()">
</div>

<?php
session_start();
include "db_conn.php";

if (isset($_SESSION['id'])) {
    $user_id = $_SESSION['id']; // Ensure this matches your session variable

    // Fetch budgets for the logged-in user
    $result = $conn->query("SELECT * FROM budgets WHERE user_id = $user_id");

    if ($result && mysqli_num_rows($result) > 0) {
        echo "<table id='budgetTable'>";
        echo "<thead><tr><th onclick='sortTable(0)'>Purpose</th><th onclick='sortTable(1)'>Total Amount (PHP)</th><th onclick='sortTable(2)'>Remaining Amount (PHP)</th></tr></thead>";
        echo "<tbody>";
        while ($row = mysqli_fetch_assoc($result)) {
            $purpose = htmlspecialchars($row['Purpose']);
            $total_amount = number_format($row['total_amount'], 2);
            $remaining_amount = number_format($row['remaining_amount'], 2);
            echo "<tr>";
            echo "<td>$purpose</td>";
            echo "<td>PHP $total_amount</td>";
            echo "<td>PHP $remaining_amount</td>";
            echo "</tr>";
        }
        echo "</tbody></table>";
    } else {
        echo "<p class='no-data'>No budgets available.</p>";
    }
} else {
    header("Location: index.php");
    exit();
}
?>
 <script src="script.js"></script>
</body>
</html>
