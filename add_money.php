<?php
require_once 'config.php'; // Include database and session helpers
requireLogin(); // Redirect to login if not logged in

if ($_SERVER['REQUEST_METHOD'] == 'POST') { // Check if form was submitted
    $amount = floatval($_POST['amount']); // Get the amount from the form
    
    if ($amount > 0 && $amount <= 1000) { // Validate amount
        try {
            $pdo->beginTransaction(); // Start database transaction
            
            // Update user wallet balance
            $stmt = $pdo->prepare("UPDATE users SET wallet_balance = wallet_balance + ? WHERE id = ?"); // Prepare update query
            $stmt->execute([$amount, $_SESSION['user_id']]); // Run update with amount and user id
            
            // Record transaction
            $stmt = $pdo->prepare("INSERT INTO transactions (user_id, transaction_type, amount, description) VALUES (?, 'credit', ?, 'Wallet top-up')"); // Prepare insert query
            $stmt->execute([$_SESSION['user_id'], $amount]); // Run insert with user id and amount
            
            $pdo->commit(); // Commit transaction
            header('Location: wallet.php?added=1'); // Redirect to wallet with success
            exit();
        } catch (Exception $e) {
            $pdo->rollBack(); // Rollback on error
            header('Location: wallet.php?error=1'); // Redirect to wallet with error
            exit();
        }
    } else {
        header('Location: wallet.php?error=invalid'); // Redirect if invalid amount
        exit();
    }
} else {
    header('Location: wallet.php'); // Redirect if not POST
    exit();
}
