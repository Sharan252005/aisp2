<?php
require_once __DIR__ . '/config/auth.php';
requireLogin();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Emergency Mode - AI Study Planner</title>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <div class="bg-grid"></div>
    <div class="page-container">
        <div class="page-content">
            <h1 class="page-title">
                <a href="index.php" class="back-btn">← Back</a>
                ⚡ Emergency Mode
            </h1>
            <div class="plan-form" style="border-color:var(--emergency);background:linear-gradient(135deg,rgba(239,68,68,0.1),transparent)">
                <h3 style="color:#fca5a5">Exams Close? Focus Mode Activated</h3>
                <p style="color:var(--text-muted);margin-bottom:1rem">Plans with exam dates within 3 days appear here. Prioritize high-impact topics.</p>
            </div>
            <h3 style="margin-bottom:1rem">Emergency Plans</h3>
            <div class="plans-list" id="emergencyPlans"></div>
            <p id="noEmergency" class="hidden" style="color:var(--text-muted);margin-top:1rem">No exams within 3 days. You're good!</p>
        </div>
    </div>
    <div class="chat-widget">
        <button class="chat-toggle" onclick="document.getElementById('chatPanel').classList.toggle('open')">💬</button>
        <div class="chat-panel" id="chatPanel">
            <div class="chat-header">AI Study Assistant</div>
            <div class="chat-messages" id="chatMessages"></div>
            <div class="chat-input-wrap">
                <form id="chatForm">
                    <input type="text" id="chatInput" placeholder="Ask about emergency prep...">
                    <button type="submit" class="btn btn-primary">Send</button>
                </form>
            </div>
        </div>
    </div>
    <script src="assets/js/app.js"></script>
    <script>
        async function loadEmergency() {
            const res = await fetch('api/plans.php?emergency=1');
            const plans = await res.json();
            const list = document.getElementById('emergencyPlans');
            const noPlan = document.getElementById('noEmergency');
            if (!Array.isArray(plans) || plans.length === 0) {
                list.innerHTML = '';
                noPlan.classList.remove('hidden');
                return;
            }
            noPlan.classList.add('hidden');
            list.innerHTML = plans.map(p => `
                <div class="plan-item emergency">
                    <div>
                        <div class="subject">${escapeHtml(p.subject)}</div>
                        <div class="meta">Exam: ${p.exam_date} | ${p.days_until_exam} days left | ${p.hours_per_day}h/day</div>
                    </div>
                    <div style="display:flex;gap:0.75rem">
                        <a href="schedule.php?plan_id=${p.id}" class="btn btn-emergency">View Schedule</a>
                        <a href="notes.php?plan_id=${p.id}" class="btn btn-secondary">Notes</a>
                        <a href="quiz.php?plan_id=${p.id}" class="btn btn-success">Take Quiz</a>
                    </div>
                </div>
            `).join('');
        }
        document.getElementById('chatForm').addEventListener('submit', chatSubmit);
        loadEmergency();
    </script>
</body>
</html>
