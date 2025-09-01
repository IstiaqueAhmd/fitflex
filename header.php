<?php
require_once 'config.php';
?>
<header>
    <nav class="container">
        <div class="logo">FitFlex</div>
        <div class="nav-links">
            <?php if (isLoggedIn()): ?>
                <a href="dashboard.php">Dashboard</a>
                <a href="slots.php">Book Slots</a>
                <a href="trainers.php">Trainers</a>
                <a href="wallet.php">Wallet</a>
                <a href="logout.php">Logout</a>
            <?php else: ?>
                <a href="login.php">Login</a>
                <a href="register.php">Register</a>
            <?php endif; ?>
        </div>
    </nav>
</header>
