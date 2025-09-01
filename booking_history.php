<?php 
require_once 'config.php';
requireLogin();

// Get user's booking history
$stmt = $pdo->prepare("
    SELECT b.*, ts.date, ts.start_time, ts.end_time, t.name as trainer_name 
    FROM bookings b 
    JOIN time_slots ts ON b.slot_id = ts.id 
    LEFT JOIN trainers t ON b.trainer_id = t.id 
    WHERE b.user_id = ? 
    ORDER BY ts.date DESC, ts.start_time DESC
");
$stmt->execute([$_SESSION['user_id']]);
$bookings = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking History - FitFlex</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="container">
        <?php include 'header.php'; ?>
        
        <div class="page-title">
            <h1>Your Booking History</h1>
            <p>View all your past and upcoming gym sessions</p>
        </div>
        
        <?php if (empty($bookings)): ?>
            <div class="no-bookings">
                <p>No bookings found.</p>
                <p><a href="slots.php">Book your first gym slot here!</a></p>
            </div>
        <?php else: ?>
            <?php foreach ($bookings as $booking): ?>
                <div class="booking-card booking-<?= $booking['booking_status'] ?>">
                    <div class="booking-header">
                        <div class="booking-date">
                            <?= date('l, F j, Y', strtotime($booking['date'])) ?>
                        </div>
                        <div class="booking-status status-<?= $booking['booking_status'] ?>">
                            <?= $booking['booking_status'] ?>
                        </div>
                    </div>
                    
                    <div class="booking-details">
                        <div>
                            <div class="booking-time">
                                🕒 <?= date('g:i A', strtotime($booking['start_time'])) ?> - <?= date('g:i A', strtotime($booking['end_time'])) ?>
                            </div>
                            <?php if ($booking['trainer_name']): ?>
                                <div class="booking-trainer">
                                    👨‍💼 Trainer: <?= htmlspecialchars($booking['trainer_name']) ?>
                                </div>
                            <?php else: ?>
                                <div>📍 Gym Only Session</div>
                            <?php endif; ?>
                        </div>
                        
                        <div style="text-align: right;">
                            <div class="booking-cost">$<?= number_format($booking['amount_paid'], 2) ?></div>
                            <div><small>Booked: <?= date('M j, Y', strtotime($booking['created_at'])) ?></small></div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>
</html>
