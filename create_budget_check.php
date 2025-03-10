<?php
session_start();
include "db_conn.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the logged-in user's ID
    $user_id = $_SESSION['id']; // Ensure this matches your session variable

    $purpose = $_POST['purpose'];
    $amount = $_POST['amount'];

    // Insert the new budget into the database with user_id
    $sql = "INSERT INTO budgets (user_id, purpose, total_amount, remaining_amount) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'isdd', $user_id, $purpose, $amount, $amount);

    if (mysqli_stmt_execute($stmt)) {
        header("Location: create_budget.php?success=You have created a new budget successfully");
        exit(); 
    } else {
        echo "Error: " . mysqli_error($conn);
    }
    mysqli_stmt_close($stmt);
}
?>