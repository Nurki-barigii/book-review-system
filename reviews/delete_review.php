<?php
require_once '../includes/auth_check.php';
require_once '../config/db.php'; // Add this line to include database connection

if(!isset($_GET['id'])) {
    header("Location: ../user/my_reviews.php");
    exit();
}

$review_id = $_GET['id'];

// Check if user owns the review or is admin
$stmt = $pdo->prepare("SELECT * FROM reviews WHERE review_id = ?");
$stmt->execute([$review_id]);
$review = $stmt->fetch();

if(!$review) {
    header("Location: ../user/my_reviews.php");
    exit();
}

if($review['user_id'] != $_SESSION['user_id'] && $_SESSION['role'] != 'admin') {
    header("Location: ../user/my_reviews.php");
    exit();
}

$stmt = $pdo->prepare("DELETE FROM reviews WHERE review_id = ?");
if($stmt->execute([$review_id])) {
    $_SESSION['success'] = "Review deleted successfully.";
} else {
    $_SESSION['error'] = "Failed to delete review.";
}

$redirect = ($_SESSION['role'] == 'admin') ? "../auth/admin/manage_reviews.php" : "../user/my_reviews.php";
header("Location: $redirect");
exit();
?>