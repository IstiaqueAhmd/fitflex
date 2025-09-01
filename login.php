<?php 
require_once 'config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
    if (!empty($username) && !empty($password)) {
        $stmt = $pdo->prepare("SELECT id, username, password FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $username]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            header('Location: dashboard.php');
            exit();
        } else {
            $error = 'Invalid credentials';
        }
    } else {
        $error = 'Please fill all fields';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - FitFlex</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <?php include 'header.php'; ?>
    <div class="container">
        <div class="login-container">
            <h2 style="text-align:center; color:#ffeb3b; margin-bottom:1.5rem;">Login to FitFlex</h2>
            <?php if ($error): ?>
                <div class="error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <form method="POST">
                <label for="username">Username or Email</label>
                <input type="text" id="username" name="username" placeholder="Enter username or email" required>
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter password" required>
                <button type="submit" class="btn" style="width:100%; margin-top:0.5rem;">Login</button>
            </form>
            <div class="links">
                <p>Don't have an account? <a href="register.php">Register</a></p>
                <p><a href="index.php">Back to Home</a></p>
            </div>
        </div>
    </div>
</body>
</html>
