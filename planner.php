<?php
require_once __DIR__ . '/config/auth.php';
requireLogin();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Study Planner - AI Study Planner</title>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <div class="bg-grid"></div>
    <div class="page-container">
        <div class="page-content">
            <h1 class="page-title">
                <a href="index.php" class="back-btn">← Back</a>
                Study Planner
            </h1>
            <div class="plan-form">
                <h3 style="margin-bottom:1rem">Add Study Plan</h3>
                <form id="planForm">
                    <div class="form-row">
                        <div class="form-group" style="flex:2">
                            <label>Subject</label>
                            <input type="text" name="subject" id="subject" placeholder="e.g. Mathematics" required>
                        </div>
                        <div class="form-group">
                            <label>Exam Date</label>
                            <input type="date" name="exam_date" id="exam_date" required>
                        </div>
                        <div class="form-group">
                            <label>Hours/Day</label>
                            <input type="number" name="hours_per_day" id="hours_per_day" value="2" min="0.5" step="0.5" placeholder="2">
                        </div>
                        <div class="form-group" style="align-self:flex-end">
                            <button type="submit" class="btn btn-primary">Add Plan</button>
                        </div>
                    </div>
                </form>
            </div>
            <h3 style="margin-bottom:1rem">Your Plans</h3>
            <div class="plans-list" id="plansList"></div>
        </div>
    </div>
    <div class="chat-widget">
        <button class="chat-toggle" onclick="document.getElementById('chatPanel').classList.toggle('open')">💬</button>
        <div class="chat-panel" id="chatPanel">
            <div class="chat-header">AI Study Assistant</div>
            <div class="chat-messages" id="chatMessages"></div>
            <div class="chat-input-wrap">
                <form id="chatForm">
                    <input type="text" id="chatInput" placeholder="Ask about studying...">
                    <button type="submit" class="btn btn-primary">Send</button>
                </form>
            </div>
        </div>
    </div>
    <script src="assets/js/app.js"></script>
    <script>
        loadPlans();
        document.getElementById('planForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const form = e.target;
            const data = {
                subject: form.subject.value.trim(),
                exam_date: form.exam_date.value,
                hours_per_day: parseFloat(form.hours_per_day.value) || 2
            };
            const res = await fetch('api/plans.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify(data)
            });
            const result = await res.json();
            if (result.success) {
                form.reset();
                loadPlans();
            }
        });
        async function loadPlans() {
            const res = await fetch('api/plans.php');
            const plans = await res.json();
            const list = document.getElementById('plansList');
            if (!Array.isArray(plans) || plans.length === 0) {
                list.innerHTML = '<p style="color:var(--text-muted)">No plans yet. Add one above.</p>';
                return;
            }
            list.innerHTML = plans.map(p => `
                <div class="plan-item ${p.is_emergency ? 'emergency' : ''}">
                    <div>
                        <div class="subject">${escapeHtml(p.subject)}</div>
                        <div class="meta">Exam: ${p.exam_date} | ${p.hours_per_day}h/day | ${p.days_until_exam >= 0 ? p.days_until_exam + ' days left' : 'Past'}</div>
                    </div>
                    <div style="display:flex;align-items:center;gap:0.75rem">
                        <span class="badge ${p.is_emergency ? 'badge-emergency' : 'badge-active'}">${p.is_emergency ? '⚡ Emergency' : 'Active'}</span>
                        <button class="btn btn-secondary" onclick="viewSchedule(${p.id})">Schedule</button>
                        <button class="btn btn-secondary" onclick="deletePlan(${p.id})">Delete</button>
                    </div>
                </div>
            `).join('');
        }
        async function deletePlan(id) {
            if (!confirm('Delete this plan?')) return;
            await fetch('api/plans.php?id=' + id, { method: 'DELETE' });
            loadPlans();
        }
        function viewSchedule(planId) {
            location.href = 'schedule.php?plan_id=' + planId;
        }
        document.getElementById('chatForm').addEventListener('submit', chatSubmit);
    </script>
</body>
</html>
