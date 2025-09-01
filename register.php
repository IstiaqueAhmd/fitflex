<?php 
require_once 'config.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    if (empty($username) || empty($email) || empty($password)) {
        $error = 'Please fill all fields';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters';
    } else {
        // Check if username or email already exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        
        if ($stmt->fetch()) {
            $error = 'Username or email already exists';
        } else {
            // Create new user
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            
            if ($stmt->execute([$username, $email, $hashed_password])) {
                $success = 'Account created successfully! You can now login.';
            } else {
                $error = 'Registration failed. Please try again.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - FitFlex</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <?php include 'header.php'; ?>
    <div class="container">
        <div class="register-container">
            <h2 style="text-align:center; color:#ffeb3b; margin-bottom:1.5rem;">Register for FitFlex</h2>
            <?php if ($error): ?>
                <div class="error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="success"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>
            <form method="POST">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" placeholder="Enter username" value="<?= htmlspecialchars($username ?? '') ?>" required>
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Enter email" value="<?= htmlspecialchars($email ?? '') ?>" required>
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter password (min 6 characters)" required>
                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm password" required>
                <button type="submit" class="btn" style="width:100%; margin-top:0.5rem;">Register</button>
            </form>
            <div class="links">
                <p>Already have an account? <a href="login.php">Login</a></p>
                <p><a href="index.php">Back to Home</a></p>
            </div>
        </div>
    </div>
</body>
</html>
