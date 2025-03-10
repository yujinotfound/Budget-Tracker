<?php
session_start();
include "db_conn.php";

header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    // Validate inputs
    $budget_id = filter_input(INPUT_POST, 'budget_id', FILTER_VALIDATE_INT);
    $amount = filter_input(INPUT_POST, 'amount', FILTER_VALIDATE_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

    if (!$budget_id || !$amount) {
        throw new Exception('Please provide valid budget and amount values');
    }

    if ($amount <= 0) {
        throw new Exception('Deduction amount must be greater than zero');
    }

    // Begin a transaction
    $conn->begin_transaction();

    // Check current budget amount with locking
    $stmt = $conn->prepare("SELECT remaining_amount FROM budgets WHERE id = ? FOR UPDATE");
    $stmt->bind_param("i", $budget_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        throw new Exception('Budget not found');
    }

    $budget = $result->fetch_assoc();
    $remaining_amount = $budget['remaining_amount'];

    if ($amount > $remaining_amount) {
        throw new Exception('Deduction exceeds remaining budget');
    }

    // Deduct the amount
    $new_remaining_amount = $remaining_amount - $amount;
    $update_stmt = $conn->prepare("UPDATE budgets SET remaining_amount = ? WHERE id = ?");
    $update_stmt->bind_param("di", $new_remaining_amount, $budget_id);
    $update_stmt->execute();

    // Log the transaction
    $log_stmt = $conn->prepare("INSERT INTO budget_transactions (budget_id, amount, transaction_type, transaction_date) VALUES (?, ?, 'DEDUCT', NOW())");
    $log_stmt->bind_param("id", $budget_id, $amount);
    $log_stmt->execute();

    // Commit the transaction
    $conn->commit();

    echo json_encode([
        'status' => 'success',
        'message' => "Successfully deducted PHP " . number_format($amount, 2) . ". New remaining amount: PHP " . number_format($new_remaining_amount, 2)
    ]);

} catch (Exception $e) {
    // Rollback the transaction in case of error
    if ($conn->errno) {
        $conn->rollback();
    }

    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
} finally {
    $stmt->close();
    $conn->close();
}
