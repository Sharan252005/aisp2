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
    <title>My Notes - AI Study Planner</title>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <div class="bg-grid"></div>
    <div class="page-container">
        <div class="page-content">
            <h1 class="page-title">
                <a href="index.php" class="back-btn">← Back</a>
                My Notes
            </h1>
            <div class="plan-form">
                <h3 style="margin-bottom:1rem">Upload Notes (PDF, DOC, DOCX)</h3>
                <div class="upload-zone" id="uploadZone">
                    <p style="color:var(--text-muted)">Drag & drop or click to upload</p>
                    <input type="file" id="fileInput" accept=".pdf,.doc,.docx" style="display:none">
                </div>
                <form id="uploadForm" class="hidden">
                    <div class="form-group">
                        <label>Title (optional)</label>
                        <input type="text" id="noteTitle" placeholder="Note title">
                    </div>
                    <button type="submit" class="btn btn-primary">Upload</button>
                </form>
            </div>
            <h3 style="margin-bottom:1rem">Your Notes</h3>
            <div class="notes-grid" id="notesGrid"></div>
        </div>
    </div>
    <div class="modal-overlay hidden" id="viewerModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modalTitle">Note</h3>
                <button class="btn btn-secondary" onclick="closeViewer()">Close</button>
            </div>
            <div class="modal-body">
                <iframe id="noteFrame" title="Note viewer" style="width:100%;min-height:500px;border:none;border-radius:8px"></iframe>
                <div id="docDownloadArea" class="hidden" style="padding:2rem"></div>
            </div>
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
        const planId = <?= $planId ? "parseInt('$planId')" : "null" ?>;
        let selectedFile = null;
        const uploadZone = document.getElementById('uploadZone');
        const fileInput = document.getElementById('fileInput');
        const uploadForm = document.getElementById('uploadForm');
        uploadZone.addEventListener('click', () => fileInput.click());
        uploadZone.addEventListener('dragover', (e) => { e.preventDefault(); uploadZone.classList.add('dragover'); });
        uploadZone.addEventListener('dragleave', () => uploadZone.classList.remove('dragover'));
        uploadZone.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadZone.classList.remove('dragover');
            const files = e.dataTransfer.files;
            if (files.length && /\.(pdf|doc|docx)$/i.test(files[0].name)) {
                selectedFile = files[0];
                document.getElementById('noteTitle').value = files[0].name.replace(/\.[^.]+$/, '');
                uploadForm.classList.remove('hidden');
            }
        });
        fileInput.addEventListener('change', () => {
            const f = fileInput.files[0];
            if (f && /\.(pdf|doc|docx)$/i.test(f.name)) {
                selectedFile = f;
                document.getElementById('noteTitle').value = f.name.replace(/\.[^.]+$/, '');
                uploadForm.classList.remove('hidden');
            }
        });
        uploadForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            if (!selectedFile) return;
            const fd = new FormData();
            fd.append('file', selectedFile);
            fd.append('title', document.getElementById('noteTitle').value.trim() || selectedFile.name);
            if (planId) fd.append('plan_id', planId);
            const res = await fetch('api/notes.php', { method: 'POST', body: fd });
            const data = await res.json();
            if (data.success) {
                selectedFile = null;
                fileInput.value = '';
                uploadForm.classList.add('hidden');
                document.getElementById('noteTitle').value = '';
                loadNotes();
            } else {
                alert(data.message || 'Upload failed');
            }
        });
        async function loadNotes() {
            const url = planId ? `api/notes.php?plan_id=${planId}` : 'api/notes.php';
            const res = await fetch(url);
            const notes = await res.json();
            const grid = document.getElementById('notesGrid');
            if (!Array.isArray(notes) || notes.length === 0) {
                grid.innerHTML = '<p style="color:var(--text-muted)">No notes yet. Upload PDF or DOC files above.</p>';
                return;
            }
            grid.innerHTML = notes.map(n => `
                <div class="note-card" onclick='openNote(${n.id}, ${JSON.stringify(n.title)}, ${JSON.stringify(n.file_path)}, ${JSON.stringify(n.file_type)})'>
                    <div class="file-type">${n.file_type === 'pdf' ? '📕' : '📘'}</div>
                    <div style="font-weight:600">${escapeHtml(n.title)}</div>
                    <div style="color:var(--text-muted);font-size:0.85rem">${n.file_type.toUpperCase()}</div>
                </div>
            `).join('');
        }
        function openNote(id, title, path, type) {
            const frame = document.getElementById('noteFrame');
            const docArea = document.getElementById('docDownloadArea');
            document.getElementById('modalTitle').textContent = title;
            if (type === 'pdf') {
                frame.classList.remove('hidden');
                docArea.classList.add('hidden');
                frame.src = path;
            } else {
                frame.classList.add('hidden');
                docArea.classList.remove('hidden');
                docArea.innerHTML = 'Word documents (.doc/.docx): <a href="download.php?path=' + encodeURIComponent(path) + '" download style="color:var(--primary);text-decoration:underline">Download ' + escapeHtml(title) + '</a>';
            }
            document.getElementById('viewerModal').classList.remove('hidden');
        }
        function closeViewer() {
            document.getElementById('noteFrame').src = '';
            document.getElementById('noteFrame').classList.remove('hidden');
            document.getElementById('docDownloadArea').classList.add('hidden');
            document.getElementById('viewerModal').classList.add('hidden');
        }
        document.getElementById('chatForm').addEventListener('submit', chatSubmit);
        loadNotes();
    </script>
</body>
</html>
