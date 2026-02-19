<?php
require_once __DIR__ . '/config/auth.php';
requireLogin();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Study Planner - Dashboard</title>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <div class="bg-grid"></div>
    <div class="page-container">
        <div class="page-content">
            <header class="dashboard-header">
                <h1 class="animate-fade-in">Welcome, <?= htmlspecialchars($_SESSION['user_name'] ?? 'User') ?></h1>
                <div class="user-info animate-fade-in animate-delay-1">
                    <span><?= htmlspecialchars($_SESSION['user_email'] ?? '') ?></span>
                    <button class="logout-btn" onclick="logout()">Logout</button>
                </div>
            </header>
            <div class="cards-grid">
                <div class="feature-card animate-fade-in animate-delay-1" style="animation-delay: 0.1s">
                    <div class="icon">📚</div>
                    <h3>Study Planner</h3>
                    <p>Create plans with subjects, exam dates, and daily study hours. Track your progress.</p>
                    <a href="planner.php" class="open-btn">Open →</a>
                </div>
                <div class="feature-card animate-fade-in animate-delay-2" style="animation-delay: 0.2s">
                    <div class="icon">📄</div>
                    <h3>My Notes</h3>
                    <p>Upload PDF and DOC notes. Read them inside the app after completing your schedule.</p>
                    <a href="notes.php" class="open-btn">Open →</a>
                </div>
                <div class="feature-card animate-fade-in animate-delay-3" style="animation-delay: 0.3s">
                    <div class="icon">🎯</div>
                    <h3>AI Quiz</h3>
                    <p>Take AI-based quizzes from your notes after finishing the day's schedule.</p>
                    <a href="quiz.php" class="open-btn">Open →</a>
                </div>
                <div class="feature-card emergency animate-fade-in animate-delay-4" style="animation-delay: 0.4s">
                    <div class="icon">⚡</div>
                    <h3>Emergency Mode</h3>
                    <p>Closer to exams? Switch to emergency mode for focused intensive study.</p>
                    <a href="emergency.php" class="open-btn">Open →</a>
                </div>
                <?php if (isAdmin()): ?>
                <div class="feature-card animate-fade-in animate-delay-5" style="animation-delay: 0.5s">
                    <div class="icon">🔐</div>
                    <h3>Admin Panel</h3>
                    <p>Admin dashboard for managing users and viewing statistics.</p>
                    <a href="admin.php" class="open-btn">Open →</a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="chat-widget">
        <button class="chat-toggle" id="chatToggle" aria-label="AI Chatbot">💬</button>
        <div class="chat-panel" id="chatPanel">
            <div class="chat-header">AI Study Assistant</div>
            <div class="chat-messages" id="chatMessages"></div>
            <div class="chat-input-wrap">
                <form id="chatForm">
                    <input type="text" id="chatInput" placeholder="Ask about studying..." autocomplete="off">
                    <button type="submit" class="btn btn-primary">Send</button>
                </form>
            </div>
        </div>
    </div>
    <script src="assets/js/app.js"></script>
    <script>
        loadChatHistory();
        document.getElementById('chatToggle').addEventListener('click', () => {
            document.getElementById('chatPanel').classList.toggle('open');
        });
        document.getElementById('chatForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const input = document.getElementById('chatInput');
            const msg = input.value.trim();
            if (!msg) return;
            appendChatMsg('user', msg);
            input.value = '';
            const res = await fetch('api/chat.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'action=send&message=' + encodeURIComponent(msg)
            });
            const data = await res.json();
            if (data.success) appendChatMsg('assistant', data.response);
        });
        async function logout() {
            const fd = new FormData();
            fd.append('action', 'logout');
            await fetch('api/auth.php', { method: 'POST', body: fd });
            location.href = 'login.php';
        }
    </script>
</body>
</html>
