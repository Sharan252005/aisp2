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
    <title>AI Study Planner - Login</title>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <div class="bg-grid"></div>
    <div class="login-page">
        <div class="login-card">
            <h1>AI Study Planner</h1>
            <p class="subtitle">Smart planning, intelligent learning</p>
            <div id="alert" class="alert hidden"></div>
            <form id="loginForm">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required placeholder="you@example.com">
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required placeholder="••••••••">
                </div>
                <button type="submit" class="btn btn-primary">Login</button>
            </form>
            <p style="text-align:center;margin-top:1.5rem;color:var(--text-muted);font-size:0.9rem">
                Don't have an account? <a href="register.php" style="color:var(--primary);text-decoration:none">Register</a>
            </p>
        </div>
    </div>
    <script>
        const form = document.getElementById('loginForm');
        const alertEl = document.getElementById('alert');
        function showAlert(msg, isError) {
            alertEl.textContent = msg;
            alertEl.className = 'alert ' + (isError ? 'alert-error' : 'alert-success');
            alertEl.classList.remove('hidden');
        }
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const fd = new FormData(form);
            fd.append('action', 'login');
            const res = await fetch('api/auth.php', { method: 'POST', body: fd });
            const data = await res.json();
            if (data.success) {
                showAlert(data.message, false);
                setTimeout(() => location.href = 'index.php', 600);
            } else {
                showAlert(data.message || 'Login failed', true);
            }
        });
    </script>
</body>
</html>
