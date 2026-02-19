<?php
require_once __DIR__ . '/config/auth.php';
requireLogin();
$planId = isset($_GET['plan_id']) ? intval($_GET['plan_id']) : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Quiz - AI Study Planner</title>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <div class="bg-grid"></div>
    <div class="page-container">
        <div class="page-content">
            <h1 class="page-title">
                <a href="index.php" class="back-btn">← Back</a>
                AI Quiz
            </h1>
            <div id="quizSetup" class="plan-form">
                <h3>Start Quiz</h3>
                <p style="color:var(--text-muted);margin-bottom:1rem">Take an AI-based quiz on your study material.</p>
                <div class="form-group">
                    <label>Subject (based on your notes)</label>
                    <input type="text" id="quizSubject" placeholder="e.g. Mathematics" required>
                </div>
                <div class="form-group">
                    <label>Note (optional - for context)</label>
                    <select id="quizNote" style="width:100%;padding:0.75rem;background:var(--bg-elevated);border:1px solid var(--border);border-radius:8px;color:var(--text)">
                        <option value="">-- Select note --</option>
                    </select>
                </div>
                <button class="btn btn-primary" id="startQuiz">Start Quiz</button>
            </div>
            <div id="quizContainer" class="quiz-container hidden">
                <div id="quizQuestions"></div>
                <button class="btn btn-primary" id="submitQuiz">Submit Quiz</button>
            </div>
            <div id="quizResult" class="plan-form hidden">
                <h3>Quiz Result</h3>
                <p id="resultText"></p>
                <button class="btn btn-primary" onclick="location.reload()">Take Another Quiz</button>
            </div>
        </div>
    </div>
    <script src="assets/js/app.js"></script>
    <script>
        let questions = [];
        const planId = <?= $planId ? "parseInt('$planId')" : "null" ?>;
        async function loadNotes() {
            const res = await fetch('api/notes.php');
            const notes = await res.json();
            const sel = document.getElementById('quizNote');
            sel.innerHTML = '<option value="">-- Select note --</option>' + (notes || []).map(n => 
                `<option value="${n.id}">${escapeHtml(n.title)}</option>`
            ).join('');
        }
        loadNotes();
        document.getElementById('startQuiz').addEventListener('click', async () => {
            const subject = document.getElementById('quizSubject').value.trim();
            const noteId = document.getElementById('quizNote').value;
            if (!subject) { alert('Enter a subject'); return; }
            const params = new URLSearchParams({ action: 'generate', subject });
            if (noteId) params.append('note_id', noteId);
            const res = await fetch('api/quiz.php?' + params);
            const data = await res.json();
            if (!data.success || !data.questions) { alert('Failed to generate quiz'); return; }
            questions = data.questions;
            document.getElementById('quizSetup').classList.add('hidden');
            document.getElementById('quizContainer').classList.remove('hidden');
            renderQuestions(data.subject);
        });
        function renderQuestions(subject) {
            const html = questions.map((q, i) => `
                <div class="quiz-question" data-id="${q.id}">
                    <strong>Q${i+1}:</strong> ${escapeHtml(q.question)}
                    <div class="quiz-options">
                        ${q.options.map((o, j) => `
                            <div class="quiz-option" data-q="${q.id}" data-a="${j}" onclick="selectOption(this)">
                                ${escapeHtml(o)}
                            </div>
                        `).join('')}
                    </div>
                </div>
            `).join('');
            document.getElementById('quizQuestions').innerHTML = html;
        }
        function selectOption(el) {
            const qId = el.dataset.q;
            const parent = el.closest('.quiz-question');
            parent.querySelectorAll('.quiz-option').forEach(o => o.classList.remove('selected'));
            el.classList.add('selected');
            el.dataset.selected = '1';
        }
        document.getElementById('submitQuiz').addEventListener('click', async () => {
            const answers = {};
            document.querySelectorAll('.quiz-question').forEach(q => {
                const id = parseInt(q.dataset.id);
                const opt = q.querySelector('.quiz-option.selected');
                answers[id] = opt ? parseInt(opt.dataset.a) : -1;
            });
            const res = await fetch('api/quiz.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({
                    action: 'submit',
                    answers,
                    questions,
                    subject: document.getElementById('quizSubject').value.trim()
                })
            });
            const data = await res.json();
            if (data.success) {
                document.getElementById('quizContainer').classList.add('hidden');
                document.getElementById('quizResult').classList.remove('hidden');
                document.getElementById('resultText').textContent = `Score: ${data.score}% (${data.correct}/${data.total} correct)`;
            }
        });
    </script>
</body>
</html>
