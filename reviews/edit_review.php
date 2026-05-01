<?php
require_once '../includes/auth_check.php';
require_once '../config/db.php'; // Add this line

if(!isset($_GET['id'])) {
    header("Location: ../user/my_reviews.php");
    exit();
}

$review_id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM reviews WHERE review_id = ? AND user_id = ?");
$stmt->execute([$review_id, $_SESSION['user_id']]);
$review = $stmt->fetch();

if(!$review) {
    header("Location: ../user/my_reviews.php");
    exit();
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $rating = $_POST['rating'];
    $comment = $_POST['comment'];
    
    $stmt = $pdo->prepare("UPDATE reviews SET rating = ?, comment = ?, status = 'pending' WHERE review_id = ?");
    if($stmt->execute([$rating, $comment, $review_id])) {
        $_SESSION['success'] = "Review updated successfully! Waiting for admin approval.";
        header("Location: ../user/my_reviews.php");
        exit();
    } else {
        $error = "Failed to update review.";
    }
}

include '../includes/header.php';
?>

<div class="card p-4">
    <div style="text-align: center; margin-bottom: 2rem;">
        <div style="background: #4f46e5; color: white; width: 80px; height: 80px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;">
            <i class="fas fa-edit" style="font-size: 2rem;"></i>
        </div>
        <h1 style="color: #1f2937; margin-bottom: 0.5rem;">Edit Review</h1>
        <p style="color: #6b7280;">Update your book review</p>
    </div>

    <?php if(isset($error)): ?>
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i>
            <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <div style="max-width: 600px; margin: 0 auto;">
        <form method="POST">
            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-star"></i>
                    Rating
                </label>
                <select name="rating" class="form-input" required>
                    <?php for($i = 1; $i <= 5; $i++): ?>
                        <option value="<?php echo $i; ?>" <?php echo $i == $review['rating'] ? 'selected' : ''; ?>>
                            <?php echo str_repeat('★', $i) . str_repeat('☆', 5 - $i) . " ($i Stars)"; ?>
                        </option>
                    <?php endfor; ?>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-comment"></i>
                    Your Review
                </label>
                <textarea name="comment" rows="6" class="form-input" required placeholder="Share your updated thoughts about this book..."><?php echo htmlspecialchars($review['comment']); ?></textarea>
            </div>

            <div style="display: flex; gap: 1rem;">
                <button type="submit" class="btn btn-primary" style="flex: 1; justify-content: center;">
                    <i class="fas fa-save"></i>
                    Update Review
                </button>
                <a href="../user/my_reviews.php" class="btn btn-secondary" style="flex: 1; justify-content: center; text-decoration: none;">
                    <i class="fas fa-times"></i>
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>