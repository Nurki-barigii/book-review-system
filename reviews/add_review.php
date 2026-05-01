<?php
// reviews/add_review.php

require_once '../includes/auth_check.php';
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../index.php");
    exit();
}

$book_id = $_POST['book_id'] ?? null;
$user_id = $_SESSION['user_id'];
$rating  = $_POST['rating'] ?? null;
$comment = trim($_POST['comment'] ?? '');

// Basic validation
if (!$book_id || !$rating || empty($comment)) {
    $_SESSION['error'] = "All fields are required.";
    $_SESSION['error'] = "All fields are required.";
    header("Location: ../books/view.php?id=" . (int)$book_id);
    exit();
}

// Prevent duplicate reviews
$stmt = $pdo->prepare("SELECT review_id FROM reviews WHERE book_id = ? AND user_id = ?");
$stmt->execute([$book_id, $user_id]);

if ($stmt->rowCount() > 0) {
    $_SESSION['error'] = "You have already reviewed this book.";
    header("Location: ../books/view.php?id=" . (int)$book_id);
    exit();
}

// Set status: admin reviews are auto-approved
$status = ($_SESSION['role'] === 'admin') ? 'approved' : 'pending';

$stmt = $pdo->prepare("INSERT INTO reviews (book_id, user_id, rating, comment, status) VALUES (?, ?, ?, ?, ?)");

if ($stmt->execute([$book_id, $user_id, $rating, $comment, $status])) {
    $_SESSION['success'] = "Review submitted successfully!" .
        ($status === 'pending' ? " It will appear after admin approval." : "");

    // Trigger M-Pesa tip prompt (shown only once)
    $_SESSION['show_tip_prompt'] = true;
    $_SESSION['tip_reviewer_id'] = $user_id;   // person who wrote the review
    $_SESSION['tip_book_id']     = $book_id;
} else {
    $_SESSION['error'] = "Something went wrong. Please try again.";
}

// Redirect back to the book page
header("Location: ../books/view.php?id=" . (int)$book_id);
exit();
?>