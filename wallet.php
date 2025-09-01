<?php 
require_once 'config.php';
requireLogin();

// Get user wallet balance
$stmt = $pdo->prepare("SELECT wallet_balance FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$wallet_balance = $stmt->fetchColumn();

// Get transaction history
$stmt = $pdo->prepare("SELECT * FROM transactions WHERE user_id = ? ORDER BY created_at DESC LIMIT 20");
$stmt->execute([$_SESSION['user_id']]);
$transactions = $stmt->fetchAll();

$message = '';
if (isset($_GET['added'])) {
    $message = 'Money added successfully!';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wallet - FitFlex</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="container">
        <?php include 'header.php'; ?>
        
        <div class="wallet-header">
            <h1>Your Wallet</h1>
            <div class="wallet-balance">$<?= number_format($wallet_balance, 2) ?></div>
            <p>Available balance for gym bookings and trainer sessions</p>
        </div>
        
        <?php if ($message): ?>
            <div class="success-message"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>
        
        <div class="wallet-actions">
            <div class="add-money-card">
                <h3>Add Money to Wallet</h3>
                <form method="POST" action="add_money.php">
                    <div class="amount-buttons">
                        <button type="button" class="amount-btn" onclick="setAmount(25)">$25</button>
                        <button type="button" class="amount-btn" onclick="setAmount(50)">$50</button>
                        <button type="button" class="amount-btn" onclick="setAmount(100)">$100</button>
                        <button type="button" class="amount-btn" onclick="setAmount(200)">$200</button>
                    </div>
                    <input type="number" name="amount" id="amount" class="custom-amount" placeholder="Enter custom amount" min="1" max="1000" step="0.01" required>
                    <button type="submit" class="add-btn">Add Money</button>
                </form>
            </div>
            
            <div class="transaction-card">
                <h3>Recent Transactions</h3>
                <?php if (empty($transactions)): ?>
                    <p>No transactions yet.</p>
                <?php else: ?>
                    <div style="max-height: 400px; overflow-y: auto;">
                        <?php foreach ($transactions as $transaction): ?>
                            <div class="transaction-item transaction-<?= $transaction['transaction_type'] ?>">
                                <div class="transaction-amount <?= $transaction['transaction_type'] ?>">
                                    <?= $transaction['transaction_type'] == 'credit' ? '+' : '-' ?>$<?= number_format($transaction['amount'], 2) ?>
                                </div>
                                <div><?= htmlspecialchars($transaction['description']) ?></div>
                                <small><?= date('M j, Y g:i A', strtotime($transaction['created_at'])) ?></small>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <script>
        function setAmount(amount) {
            document.getElementById('amount').value = amount;
        }
    </script>
</body>
</html>
