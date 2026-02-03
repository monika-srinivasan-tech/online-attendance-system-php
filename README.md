# 📊 Online Attendance System (PHP & MySQL)

A clean and simple **Online Attendance Management System** developed using  
**PHP, MySQL, HTML, and CSS**.  
This project helps teachers or admins to manage student attendance efficiently
with a user-friendly interface.

---

## ✨ Project Overview

Managing attendance manually is time-consuming and error-prone.  
This web application digitizes the attendance process and allows:

- Easy student management
- Daily attendance marking
- Instant attendance summary
- Clean and professional UI

This project is ideal for **college students / freshers** as a resume project.

---

## 🚀 Features

- 🔐 Secure Login using PHP Sessions
- 🏫 Class-wise Attendance Management
- 👩‍🎓 Add / Update / Delete Students
- ✅ Mark Attendance (Present / Absent)
- 📅 Date-based Attendance Tracking
- 📊 Attendance Summary & Percentage Report
- 🎨 Professional UI using Common CSS
- ⚡ Fast & Lightweight

---

## 🛠️ Technologies Used

| Technology | Description |
|----------|-------------|
| HTML | Structure |
| CSS | Styling & Layout |
| PHP | Backend Logic |
| MySQL | Database |
| XAMPP | Local Server |
| GitHub | Version Control |

---

## 📁 Project Folder Structure

online-attendance-system-php/
│
├── index.php
├── login.php
├── dashboard.php
├── attendance.php
├── summary.php
├── logout.php
├── db_connect.php
│
├── assets/
│ └── css/
│ └── common.css
│
└── README.md


---

## ⚙️ Installation & Setup

### Step 1: Install XAMPP
Download and install **XAMPP** from:
https://www.apachefriends.org

---

### Step 2: Move Project Folder
Copy the project folder to:
C:/xampp/htdocs/online-attendance-system-php

---

### Step 3: Start Server
Open **XAMPP Control Panel** and start:
- Apache
- MySQL

---

### Step 4: Create Database

1. Open browser and go to:
http://localhost/phpmyadmin
2. Create a database:
attendance_db
3. Create required tables (users, classes, students, attendance)

---

### Step 5: Configure Database
Edit `db_connect.php` file:

```php
$conn = new mysqli("localhost", "root", "", "attendance_db");
Step 6: Run Project

Open browser and visit:

http://localhost:8080/online-attendance-system-php/
