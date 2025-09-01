<?php 
require_once 'config.php';
requireLogin();

// Get all trainers
$stmt = $pdo->prepare("SELECT * FROM trainers ORDER BY name");
$stmt->execute();
$trainers = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trainers - FitFlex</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="container">
        <?php include 'header.php'; ?>
        
        <div class="page-title">
            <h1>Our Expert Trainers</h1>
            <p>Choose from our qualified professionals to enhance your workout</p>
        </div>
        
        <div class="trainers-grid">
            <?php foreach ($trainers as $trainer): ?>
                <div class="trainer-card">
                    <div class="trainer-header">
                        <div class="trainer-avatar">
                            <?= strtoupper(substr($trainer['name'], 0, 1)) ?>
                        </div>
                        <div class="trainer-info">
                            <h3><?= htmlspecialchars($trainer['name']) ?></h3>
                            <div class="trainer-rate">$<?= number_format($trainer['hourly_rate'], 2) ?>/hour</div>
                        </div>
                    </div>
                    
                    <div class="trainer-specialization">
                        <?= htmlspecialchars($trainer['specialization']) ?>
                    </div>
                    
                    <div class="trainer-qualifications">
                        <strong>Qualifications:</strong><br>
                        <?= htmlspecialchars($trainer['qualifications']) ?>
                    </div>
                    
                    <div class="trainer-bio">
                        <?= htmlspecialchars($trainer['bio']) ?>
                    </div>
                    
                    <a href="slots.php" class="book-trainer-btn">Book Session with <?= htmlspecialchars(explode(' ', $trainer['name'])[0]) ?></a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>
