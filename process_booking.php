<?php
require_once 'config.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $slot_id = intval($_POST['slot_id']);
    $trainer_id = !empty($_POST['trainer_id']) ? intval($_POST['trainer_id']) : null;
    
    try {
        $pdo->beginTransaction();
        
        // Get slot details
        $stmt = $pdo->prepare("SELECT * FROM time_slots WHERE id = ? AND current_bookings < max_capacity");
        $stmt->execute([$slot_id]);
        $slot = $stmt->fetch();
        
        if (!$slot) {
            throw new Exception("Slot not available");
        }
        
        // Calculate total cost
        $base_cost = 15.00; // Base gym slot cost
        $trainer_cost = 0;
        
        if ($trainer_id) {
            $stmt = $pdo->prepare("SELECT hourly_rate FROM trainers WHERE id = ?");
            $stmt->execute([$trainer_id]);
            $trainer_rate = $stmt->fetchColumn();
            if ($trainer_rate) {
                $trainer_cost = $trainer_rate;
            }
        }
        
        $total_cost = $base_cost + $trainer_cost;
        
        // Check user balance
        $stmt = $pdo->prepare("SELECT wallet_balance FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $wallet_balance = $stmt->fetchColumn();
        
        if ($wallet_balance < $total_cost) {
            throw new Exception("Insufficient wallet balance");
        }
        
        // Check if user already has a booking for this slot
        $stmt = $pdo->prepare("SELECT id FROM bookings WHERE user_id = ? AND slot_id = ? AND booking_status = 'confirmed'");
        $stmt->execute([$_SESSION['user_id'], $slot_id]);
        if ($stmt->fetch()) {
            throw new Exception("You already have a booking for this slot");
        }
        
        // Create booking
        $stmt = $pdo->prepare("INSERT INTO bookings (user_id, slot_id, trainer_id, amount_paid) VALUES (?, ?, ?, ?)");
        $stmt->execute([$_SESSION['user_id'], $slot_id, $trainer_id, $total_cost]);
        
        // Update slot capacity
        $stmt = $pdo->prepare("UPDATE time_slots SET current_bookings = current_bookings + 1 WHERE id = ?");
        $stmt->execute([$slot_id]);
        
        // Update user wallet balance
        $stmt = $pdo->prepare("UPDATE users SET wallet_balance = wallet_balance - ? WHERE id = ?");
        $stmt->execute([$total_cost, $_SESSION['user_id']]);
        
        // Record transaction
        $description = "Gym slot booking";
        if ($trainer_id) {
            $stmt = $pdo->prepare("SELECT name FROM trainers WHERE id = ?");
            $stmt->execute([$trainer_id]);
            $trainer_name = $stmt->fetchColumn();
            $description .= " with trainer " . $trainer_name;
        }
        
        $stmt = $pdo->prepare("INSERT INTO transactions (user_id, transaction_type, amount, description) VALUES (?, 'debit', ?, ?)");
        $stmt->execute([$_SESSION['user_id'], $total_cost, $description]);
        
        $pdo->commit();
        header('Location: slots.php?booked=1');
        exit();
        
    } catch (Exception $e) {
        $pdo->rollBack();
        header('Location: slots.php?error=' . urlencode($e->getMessage()));
        exit();
    }
} else {
    header('Location: slots.php');
    exit();
}
