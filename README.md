# 📚 Book Review System

A web-based **Book Review System** developed using PHP, MySQL, HTML, CSS, and JavaScript. The system allows users to register and log in, view books, submit reviews, and read reviews from other users.

The project was developed as a practical web development project to demonstrate database management, user authentication, CRUD operations, and interactive web features.

## 🚀 Features

* 🔐 User registration and login
* 📚 Book listing and browsing
* ⭐ Submit book reviews and ratings
* 💬 Read reviews from other users
* 🤖 Book chatbot for helping users find available/popular books
* 🗄️ MySQL database integration
* 🔒 User authentication
 📱 Responsive web interface
* ✏️ CRUD operations for book/review data

## 🛠️ Technologies Used

### Frontend

* HTML5
* CSS3
* JavaScript

### Backend

* PHP

### Database

* MySQL

### Development Environment

* XAMPP
* Apache
* MySQL
* Visual Studio Code

## 📂 Project Structure

```text
book_review/
│
├── index.php
├── login.php
├── register.php
├── logout.php
│
├── css/
│   └── style.css
│
├── js/
│   └── script.js
│
├── images/
│
├── includes/
│
├── chatbot/
│
└── database/
    └── book_review.sql
```

> The exact file structure may vary depending on the current version of the project.

## ⚙️ Installation

### 1. Install XAMPP

Download and install XAMPP with:

* Apache
* MySQL
* PHP

### 2. Clone the repository

```bash
git clone https://github.com/Nurki-barigii/book-review-system.git
```

Or download the repository as a ZIP file and extract it into:

```text
C:\xampp\htdocs\
```

### 3. Start XAMPP

Open XAMPP Control Panel and start:

```text
Apache
MySQL
```

### 4. Create the database

Open:

```text
http://localhost/phpmyadmin
```

Create a database named:

```text
book_review
```

Import the project's SQL database file into the newly created database.

### 5. Configure the database connection

Update the database connection file with your local MySQL settings.

Example:

```php
$host = "localhost";
$username = "root";
$password = "";
$database = "book_review";
```

### 6. Run the application

Open your browser and visit:

```text
http://localhost/book_review/
```

## 👤 User Workflow

1. Register an account.
2. Log in to the system.
3. Browse available books.
4. Select a book.
5. Submit a rating and review.
6. Read reviews from other users.
7. Use the chatbot to find information about books.

## 🔐 Security

The project demonstrates basic web application security practices such as:

* User authentication
* Password handling
* Session management
* Database validation
* Input validation
* Prepared statements/PDO where applicable

> This project is intended for educational and portfolio purposes and should receive additional security hardening before production deployment.

## 🎯 Project Objectives

The main objectives of this project are to:

* Build a functional database-driven web application.
* Practice PHP backend development.
* Work with MySQL databases.
* Implement authentication.
* Implement CRUD functionality.
* Practice frontend development.
* Integrate an interactive chatbot.
* Develop practical software engineering experience.

## 🔮 Future Improvements

Possible future improvements include:

* Advanced book search
* Book categories
* User profiles
* Admin dashboard
* Book recommendation system
* AI-powered recommendations
* Better chatbot capabilities
* Email notifications
* REST API
* Deployment to a cloud server
* Improved security and authorization

## 👨‍💻 Author

**Noor Ali**

Computer Science | Software Development | AI/ML | Network Security

GitHub: [Nurki-barigii](https://github.com/Nurki-barigii)


This project is intended for educational and portfolio purposes.
