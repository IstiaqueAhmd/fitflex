<?php 
require_once 'config.php';
requireLogin();

// Get user info
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// Get recent bookings
$stmt = $pdo->prepare("
    SELECT b.*, ts.date, ts.start_time, ts.end_time, t.name as trainer_name 
    FROM bookings b 
    JOIN time_slots ts ON b.slot_id = ts.id 
    LEFT JOIN trainers t ON b.trainer_id = t.id 
    WHERE b.user_id = ? 
    ORDER BY b.created_at DESC 
    LIMIT 5
");
$stmt->execute([$_SESSION['user_id']]);
$recent_bookings = $stmt->fetchAll();

// Get recent transactions
$stmt = $pdo->prepare("SELECT * FROM transactions WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
$stmt->execute([$_SESSION['user_id']]);
$recent_transactions = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - FitFlex</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="container">
        <?php include 'header.php'; ?>
        
        <div class="welcome">
            <h1>Welcome back, <?= htmlspecialchars($user['username']) ?>!</h1>
            <p>Ready for your next workout?</p>
        </div>
        
        <div class="quick-actions">
            <a href="slots.php" class="action-btn">Book New Slot</a>
            <a href="trainers.php" class="action-btn">Browse Trainers</a>
            <a href="wallet.php" class="action-btn">Manage Wallet</a>
            <a href="booking_history.php" class="action-btn">View All Bookings</a>
        </div>
        
        <div class="dashboard-grid">
            <div class="card">
                <h3>Wallet Balance</h3>
                <div class="wallet-balance">$<?= number_format($user['wallet_balance'], 2) ?></div>
                <p style="text-align: center; opacity: 0.8;">Available for bookings</p>
            </div>
            
            <div class="card">
                <h3>Account Stats</h3>
                <p>Member since: <?= date('F Y', strtotime($user['created_at'])) ?></p>
                <p>Total bookings: <?php 
                    $stmt = $pdo->prepare("SELECT COUNT(*) FROM bookings WHERE user_id = ?");
                    $stmt->execute([$_SESSION['user_id']]);
                    echo $stmt->fetchColumn();
                ?></p>
                <p>Total spent: $<?php 
                    $stmt = $pdo->prepare("SELECT SUM(amount) FROM transactions WHERE user_id = ? AND transaction_type = 'debit'");
                    $stmt->execute([$_SESSION['user_id']]);
                    echo number_format($stmt->fetchColumn() ?? 0, 2);
                ?></p>
            </div>
        </div>
        
        <div class="dashboard-grid">
            <div class="card">
                <h3>Recent Bookings</h3>
                <?php if (empty($recent_bookings)): ?>
                    <p>No bookings yet. <a href="slots.php" style="color: #ffeb3b;">Book your first slot!</a></p>
                <?php else: ?>
                    <?php foreach ($recent_bookings as $booking): ?>
                        <div class="booking-item status-<?= $booking['booking_status'] ?>">
                            <strong><?= date('M j, Y', strtotime($booking['date'])) ?></strong><br>
                            <?= date('g:i A', strtotime($booking['start_time'])) ?> - <?= date('g:i A', strtotime($booking['end_time'])) ?><br>
                            <?php if ($booking['trainer_name']): ?>
                                Trainer: <?= htmlspecialchars($booking['trainer_name']) ?><br>
                            <?php endif; ?>
                            <small>Status: <?= ucfirst($booking['booking_status']) ?> | $<?= number_format($booking['amount_paid'], 2) ?></small>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            
            <div class="card">
                <h3>Recent Transactions</h3>
                <?php if (empty($recent_transactions)): ?>
                    <p>No transactions yet.</p>
                <?php else: ?>
                    <?php foreach ($recent_transactions as $transaction): ?>
                        <div class="transaction-item transaction-<?= $transaction['transaction_type'] ?>">
                            <strong><?= $transaction['transaction_type'] == 'credit' ? '+' : '-' ?>$<?= number_format($transaction['amount'], 2) ?></strong><br>
                            <?= htmlspecialchars($transaction['description']) ?><br>
                            <small><?= date('M j, Y g:i A', strtotime($transaction['created_at'])) ?></small>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
