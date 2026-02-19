<?php
require_once __DIR__ . '/config/auth.php';
requireLogin();
$planId = intval($_GET['plan_id'] ?? 0);
if (!$planId) { header('Location: planner.php'); exit; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daily Schedule - AI Study Planner</title>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <div class="bg-grid"></div>
    <div class="page-container">
        <div class="page-content">
            <h1 class="page-title">
                <a href="planner.php" class="back-btn">← Back</a>
                Daily Schedule
            </h1>
            <div id="planInfo" class="plan-form" style="margin-bottom:2rem"></div>
            <div class="schedule-tracker">
                <h3>Today's Progress</h3>
                <div class="form-group">
                    <label>Hours Completed</label>
                    <input type="number" id="hoursCompleted" step="0.5" min="0" value="0" style="width:120px">
                </div>
                <div class="form-group">
                    <label>Notes (optional)</label>
                    <input type="text" id="scheduleNotes" placeholder="What did you cover?">
                </div>
                <div style="display:flex;align-items:center;gap:1rem;margin-top:1rem">
                    <label style="display:flex;align-items:center;gap:0.5rem;cursor:pointer">
                        <input type="checkbox" id="isCompleted"> Mark as completed
                    </label>
                    <button class="btn btn-primary" id="saveSchedule">Save Progress</button>
                </div>
                <div id="completionMsg" class="hidden" style="margin-top:1rem">
                    <p class="alert alert-success">Schedule completed! <a href="quiz.php?plan_id=<?= $planId ?>" style="color:var(--primary)">Take AI Quiz</a> or <a href="notes.php?plan_id=<?= $planId ?>" style="color:var(--primary)">Read Notes</a></p>
                </div>
            </div>
        </div>
    </div>
    <script>
        const planId = <?= $planId ?>;
        async function loadPlan() {
            const res = await fetch('api/plans.php?id=' + planId);
            const plan = await res.json();
            if (!plan || plan.success === false) {
                document.getElementById('planInfo').innerHTML = '<p>Plan not found.</p>';
                return;
            }
            document.getElementById('planInfo').innerHTML = `
                <strong>${plan.subject}</strong> | Exam: ${plan.exam_date} | ${plan.hours_per_day}h/day target
                ${plan.is_emergency ? '<span class="badge badge-emergency">⚡ Emergency Mode</span>' : ''}
            `;
        }
        async function loadSchedule() {
            const date = new Date().toISOString().slice(0,10);
            const res = await fetch(`api/schedules.php?plan_id=${planId}&date=${date}`);
            const s = await res.json();
            if (s) {
                document.getElementById('hoursCompleted').value = s.hours_completed || 0;
                document.getElementById('scheduleNotes').value = s.notes || '';
                document.getElementById('isCompleted').checked = !!s.is_completed;
                if (s.is_completed) document.getElementById('completionMsg').classList.remove('hidden');
            }
        }
        loadPlan(); loadSchedule();
        document.getElementById('saveSchedule').addEventListener('click', async () => {
            const data = {
                plan_id: planId,
                date: new Date().toISOString().slice(0,10),
                hours_completed: parseFloat(document.getElementById('hoursCompleted').value) || 0,
                is_completed: document.getElementById('isCompleted').checked ? 1 : 0,
                notes: document.getElementById('scheduleNotes').value.trim()
            };
            const res = await fetch('api/schedules.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify(data)
            });
            const result = await res.json();
            if (result.success) {
                alert('Saved!');
                if (data.is_completed) document.getElementById('completionMsg').classList.remove('hidden');
            }
        });
    </script>
</body>
</html>
