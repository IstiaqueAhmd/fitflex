<?php require_once 'config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FitFlex - Smart Gym Booking System</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <?php include 'header.php'; ?>

    <main class="container">
        <section class="hero" style="text-align:center; color:white; padding:4rem 0;">
            <h1 style="font-size:3rem; margin-bottom:1rem;">Welcome to <span style="color:#ffeb3b;">FitFlex</span></h1>
            <p style="font-size:1.2rem; margin-bottom:2rem; opacity:0.9;">Your Smart Gym Booking & Personal Trainer Selection System</p>
            <?php if (isLoggedIn()): ?>
                <a href="slots.php" class="btn">Book a Slot</a>
                <a href="trainers.php" class="btn btn-secondary">Browse Trainers</a>
            <?php else: ?>
                <a href="register.php" class="btn">Get Started</a>
                <a href="login.php" class="btn btn-secondary">Login</a>
            <?php endif; ?>
        </section>

        <section class="features" style="display:grid; grid-template-columns:repeat(auto-fit,minmax(300px,1fr)); gap:2rem; margin:4rem 0;">
            <div class="feature-card">
                <h3 style="color:#ffeb3b;">🕒 Smart Slot Booking</h3>
                <p>Book your preferred gym time slots in advance. Real-time availability with limited capacity management.</p>
            </div>
            <div class="feature-card">
                <h3 style="color:#ffeb3b;">👨‍💼 Expert Trainers</h3>
                <p>Choose from our qualified trainers with detailed profiles, specializations, and flexible schedules.</p>
            </div>
            <div class="feature-card">
                <h3 style="color:#ffeb3b;">💳 Virtual Wallet</h3>
                <p>Secure virtual wallet system for easy payments. Top up your balance and track all transactions.</p>
            </div>
        </section>
    </main>
</body>
</html>
