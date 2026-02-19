<?php
require_once __DIR__ . '/config/auth.php';
if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Study Planner - Register</title>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <div class="bg-grid"></div>
    <div class="login-page">
        <div class="login-card">
            <h1>Create Account</h1>
            <p class="subtitle">Start your smart study journey</p>
            <div id="alert" class="alert hidden"></div>
            <form id="registerForm">
                <div class="form-group">
                    <label for="full_name">Full Name</label>
                    <input type="text" id="full_name" name="full_name" required placeholder="John Doe">
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required placeholder="you@example.com">
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required placeholder="••••••••" minlength="6">
                </div>
                <button type="submit" class="btn btn-primary">Register</button>
            </form>
            <p style="text-align:center;margin-top:1.5rem;color:var(--text-muted);font-size:0.9rem">
                Already have an account? <a href="login.php" style="color:var(--primary);text-decoration:none">Login</a>
            </p>
        </div>
    </div>
    <script>
        document.getElementById('registerForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const form = e.target;
            const fd = new FormData(form);
            fd.append('action', 'register');
            const res = await fetch('api/auth.php', { method: 'POST', body: fd });
            const data = await res.json();
            const alertEl = document.getElementById('alert');
            alertEl.textContent = data.message;
            alertEl.className = 'alert ' + (data.success ? 'alert-success' : 'alert-error');
            alertEl.classList.remove('hidden');
            if (data.success) setTimeout(() => location.href = 'index.php', 800);
        });
    </script>
</body>
</html>
