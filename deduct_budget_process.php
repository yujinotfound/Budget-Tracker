<?php 
session_start();
header('Content-Type: application/json'); 
include "db_conn.php"; 

try { 
    if ($_SERVER['REQUEST_METHOD'] === 'POST') { 
        // Retrieve POST data 
        $budget_id = isset($_POST['budget_id']) ? intval($_POST['budget_id']) : null; 
        $amount = isset($_POST['amount']) ? floatval($_POST['amount']) : null; 
        $user_id = $_SESSION['id']; // Ensure this matches your session variable

        // Check for missing or invalid values 
        if ($budget_id === null || $amount === null || $amount <= 0) { 
            echo json_encode(['status' => 'error', 'message' => 'Please provide valid budget category and deduction amount.']); 
            exit; 
        } 

        // Fetch budget details for the selected budget and ensure it belongs to the user
        $stmt = $conn->prepare("SELECT remaining_amount FROM budgets WHERE id = ? AND user_id = ? FOR UPDATE"); 
        $stmt->bind_param("ii", $budget_id, $user_id); 
        $stmt->execute(); 
        $result = $stmt->get_result(); 

        if ($result->num_rows === 0) { 
            echo json_encode(['status' => 'error', 'message' => 'Budget category not found or does not belong to the user.']);
            exit; 
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
    } else { 
        echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']); 
    } 
} catch (Exception $e) { 
    // Rollback the transaction in case of error
    $conn->rollback();
    echo json_encode(['status' => 'error', 'message' => 'An unexpected error occurred. Please contact support.']); 
    error_log($e->getMessage()); // Log the actual error for debugging 
} finally {
    $stmt->close();
    $conn->close();
}
?>