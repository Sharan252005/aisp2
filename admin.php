<?php
require_once __DIR__ . '/config/auth.php';
requireAdmin();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - AI Study Planner</title>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <div class="bg-grid"></div>
    <div class="page-container">
        <div class="page-content">
            <h1 class="page-title">
                <a href="index.php" class="back-btn">← Back</a>
                Admin Panel
            </h1>
            <div class="admin-grid" id="adminStats"></div>
            <h3 style="margin-bottom:1rem">Recent Users</h3>
            <div class="plan-form" style="overflow-x:auto">
                <table style="width:100%;border-collapse:collapse">
                    <thead>
                        <tr style="text-align:left;border-bottom:1px solid var(--border)">
                            <th style="padding:0.75rem">ID</th>
                            <th style="padding:0.75rem">Email</th>
                            <th style="padding:0.75rem">Name</th>
                            <th style="padding:0.75rem">Admin</th>
                            <th style="padding:0.75rem">Created</th>
                        </tr>
                    </thead>
                    <tbody id="usersTable"></tbody>
                </table>
            </div>
        </div>
    </div>
    <script>
        async function loadStats() {
            const res = await fetch('api/admin.php?action=stats');
            const data = await res.json();
            if (data.success) {
                const s = data.stats;
                document.getElementById('adminStats').innerHTML = `
                    <div class="admin-stat"><div class="number">${s.users}</div><div class="label">Users</div></div>
                    <div class="admin-stat"><div class="number">${s.plans}</div><div class="label">Study Plans</div></div>
                    <div class="admin-stat"><div class="number">${s.notes}</div><div class="label">Notes</div></div>
                    <div class="admin-stat"><div class="number">${s.quizzes}</div><div class="label">Quiz Attempts</div></div>
                    <div class="admin-stat"><div class="number">${s.chats}</div><div class="label">Chat Messages</div></div>
                `;
            }
        }
        async function loadUsers() {
            const res = await fetch('api/admin.php?action=users');
            const users = await res.json();
            const tbody = document.getElementById('usersTable');
            if (!Array.isArray(users) || users.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" style="padding:1rem;color:var(--text-muted)">No users</td></tr>';
                return;
            }
            tbody.innerHTML = users.map(u => `
                <tr style="border-bottom:1px solid var(--border)">
                    <td style="padding:0.75rem">${u.id}</td>
                    <td style="padding:0.75rem">${escapeHtml(u.email)}</td>
                    <td style="padding:0.75rem">${escapeHtml(u.full_name)}</td>
                    <td style="padding:0.75rem">${u.is_admin ? 'Yes' : 'No'}</td>
                    <td style="padding:0.75rem">${u.created_at}</td>
                </tr>
            `).join('');
        }
        loadStats();
        loadUsers();
    </script>
</body>
</html>
