# AI Study Planner

An AI-powered study planner with subject tracking, exam dates, daily study hours, note uploads (PDF/DOC), in-app reading, AI quizzes, and emergency mode for close exam days.

## Features

- **Login / Register** – User authentication
- **Dashboard** – Card-based navigation with animated UI
- **Study Planner** – Subjects, exam dates, hours per day
- **My Notes** – Upload PDF & DOC notes, view inside the app
- **AI Quiz** – AI-based quizzes from your notes after completing daily schedule
- **Emergency Mode** – Prioritizes plans with exams within 3 days
- **AI Chatbot** – Study assistant available on all pages
- **Admin Panel** – Stats and user list (admin only)

## Admin Access

- Email: `sharantv25@gmail.com`
- Password: `admin123`

## Tech Stack

- **Frontend:** HTML, CSS, JavaScript
- **Backend:** PHP
- **Database:** MySQL

## Setup

1. **Requirements:** PHP 7.4+, MySQL 5.7+, Apache (or PHP built-in server)

2. **Database:**
   - Create MySQL database `ai_study_planner` (or run `database/schema.sql`)
   - Update `config/database.php` with your DB credentials

3. **Install:**
   ```bash
   php setup.php
   ```
   This creates tables and sets the admin password.

4. **Run:**
   ```bash
   php -S localhost:8000
   ```
   Or use XAMPP/WAMP and place files in `htdocs`.

5. Open: `http://localhost:8000/login.php`

## Project Structure

```
aispl/
├── api/           # Backend API endpoints
├── assets/
│   ├── css/       # Styles
│   └── js/        # Shared JS
├── config/        # Auth & DB config
├── database/      # Schema
├── uploads/       # Uploaded notes (created on first upload)
├── login.php
├── register.php
├── index.php      # Dashboard
├── planner.php
├── schedule.php
├── notes.php
├── quiz.php
├── emergency.php
├── admin.php
└── setup.php
```

## AI Integration

The chatbot and quiz use simulated AI responses. To add real AI:
- Replace `api/chat.php` logic with OpenAI/Claude API calls
- Replace `api/quiz.php` logic with AI-generated questions from note content
