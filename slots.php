<?php 
require_once 'config.php'; // Include database and session helpers
requireLogin(); // Redirect to login if not logged in

// Get available slots for the next 7 days
$stmt = $pdo->prepare("SELECT * FROM time_slots WHERE date >= CURDATE() AND date <= DATE_ADD(CURDATE(), INTERVAL 7 DAY) AND current_bookings < max_capacity ORDER BY date, start_time");
$stmt->execute(); // Run the query
$available_slots = $stmt->fetchAll(); // Fetch all available slots

// Get all trainers for selection
$stmt = $pdo->prepare("SELECT * FROM trainers ORDER BY name");
$stmt->execute(); // Run the query
$trainers = $stmt->fetchAll(); // Fetch all trainers

$message = '';
if (isset($_GET['booked'])) {
    $message = 'Slot booked successfully!'; // Show message if slot was booked
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Slots - FitFlex</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="container">
        <?php include 'header.php'; ?>
        
        <div class="page-title">
            <h1>Book Your Gym Slots</h1>
            <p>Choose your preferred time and optional trainer</p>
        </div>
        
        <div class="wallet-display">
            <h3>Your Wallet Balance: $<?php 
                $stmt = $pdo->prepare("SELECT wallet_balance FROM users WHERE id = ?");
                $stmt->execute([$_SESSION['user_id']]);
                echo number_format($stmt->fetchColumn(), 2);
            ?></h3>
            <p>Slot booking fee: $15.00 | Personal trainer: +$20-$35 per hour</p>
        </div>
        
        <?php if ($message): ?>
            <div class="success-message"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>
        
        <?php if (empty($available_slots)): ?>
            <div class="no-slots">
                <p>No available slots at the moment. Please check back later!</p>
            </div>
        <?php else: ?>
            <div class="slots-grid">
                <?php foreach ($available_slots as $slot): ?>
                    <div class="slot-card">
                        <div class="slot-date"><?= date('l, F j, Y', strtotime($slot['date'])) ?></div>
                        <div class="slot-time"><?= date('g:i A', strtotime($slot['start_time'])) ?> - <?= date('g:i A', strtotime($slot['end_time'])) ?></div>
                        
                        <?php 
                        $available_spots = $slot['max_capacity'] - $slot['current_bookings'];
                        $capacity_class = $available_spots > 3 ? 'available' : 'limited';
                        ?>
                        <div class="slot-capacity <?= $capacity_class ?>">
                            <?= $available_spots ?> spots available (<?= $slot['max_capacity'] ?> total)
                        </div>
                        
                        <form method="POST" action="process_booking.php" class="book-form">
                            <input type="hidden" name="slot_id" value="<?= $slot['id'] ?>">
                            
                            <select name="trainer_id">
                                <option value="">No trainer (Gym only - $15)</option>
                                <?php foreach ($trainers as $trainer): ?>
                                    <option value="<?= $trainer['id'] ?>">
                                        <?= htmlspecialchars($trainer['name']) ?> - $<?= number_format($trainer['hourly_rate'] + 15, 2) ?> total
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            
                            <button type="submit" class="btn">Book This Slot</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
