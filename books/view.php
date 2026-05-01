<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/db.php';

if(!isset($_GET['id'])) {
    header("Location: list.php");
    exit();
}

$book_id = (int)$_GET['id'];

// Handle support prompt dismissal
if (isset($_GET['clear_support'])) {
    unset($_SESSION['show_support_prompt'], $_SESSION['support_reviewer_id'], $_SESSION['support_book_id']);
    header("Location: view.php?id=" . $book_id);
    exit();
}

// Handle order placement
$order_success = false;
$order_error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order']) && isset($_SESSION['user_id'])) {
    $order_type = $_POST['order_type'] ?? 'physical';
    $quantity = (int)($_POST['quantity'] ?? 1);
    
    if ($quantity < 1) {
        $order_error = "Quantity must be at least 1";
    } else {
        try {
            $pdo->beginTransaction();
            
            // Get book price
            $stmt = $pdo->prepare("SELECT price FROM books WHERE book_id = ?");
            $stmt->execute([$book_id]);
            $book_price = $stmt->fetchColumn();
            
            if (!$book_price || $book_price <= 0) {
                throw new Exception("Book price not available");
            }
            
            $total_amount = $book_price * $quantity;
            
            // Create order
            $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_amount, status, created_at) VALUES (?, ?, 'pending', NOW())");
            $stmt->execute([$_SESSION['user_id'], $total_amount]);
            $order_id = $pdo->lastInsertId();
            
            // Insert into sales table
            $stmt = $pdo->prepare("INSERT INTO sales (order_id, product_id, quantity_sold, total_price, created_at) VALUES (?, ?, ?, ?, NOW())");
            $stmt->execute([$order_id, $book_id, $quantity, $total_amount]);
            
            $pdo->commit();
            $order_success = true;
            
        } catch (Exception $e) {
            $pdo->rollBack();
            $order_error = "Failed to place order: " . $e->getMessage();
        }
    }
}

$stmt = $pdo->prepare("SELECT * FROM books WHERE book_id = ?");
$stmt->execute([$book_id]);
$book = $stmt->fetch();

if(!$book) {
    die("Book not found");
}

// Get approved reviews
$stmt = $pdo->prepare("SELECT r.*, u.name, u.user_id FROM reviews r JOIN users u ON r.user_id = u.user_id WHERE r.book_id = ? AND r.status = 'approved' ORDER BY r.created_at DESC");
$stmt->execute([$book_id]);
$reviews = $stmt->fetchAll();

// Average rating
$stmt = $pdo->prepare("SELECT AVG(rating) as avg_rating, COUNT(*) as review_count FROM reviews WHERE book_id = ? AND status = 'approved'");
$stmt->execute([$book_id]);
$rating_info = $stmt->fetch();

// User's own review (if any)
$user_review = null;
if(isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT * FROM reviews WHERE book_id = ? AND user_id = ?");
    $stmt->execute([$book_id, $_SESSION['user_id']]);
    $user_review = $stmt->fetch();
}

// Check if user has already ordered this book
$user_ordered = false;
if(isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("
        SELECT o.* FROM orders o 
        JOIN sales s ON o.id = s.order_id 
        WHERE o.user_id = ? AND s.product_id = ?
    ");
    $stmt->execute([$_SESSION['user_id'], $book_id]);
    $user_ordered = $stmt->fetch();
}

// Show support prompt if user just submitted a review
if (isset($_SESSION['just_submitted_review']) && $_SESSION['just_submitted_review'] == $book_id) {
    $_SESSION['show_support_prompt'] = true;
    $_SESSION['support_reviewer_id'] = $_SESSION['user_id'] ?? 1;
    $_SESSION['support_book_id'] = $book_id;
    unset($_SESSION['just_submitted_review']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($book['title']); ?> - BookReview</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            line-height: 1.6;
            color: #333;
            background: #f8fafc;
        }

        .main-container {
            max-width: 1400px;
            margin: 0 auto;
            width: 100%;
            padding: 0 2rem;
        }

        /* Book Header Section */
        .book-hero-section {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.9), rgba(118, 75, 162, 0.9)),
                        url('https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80');
            background-size: cover;
            background-position: center;
            padding: 4rem 2rem;
            border-radius: 20px;
            margin: 2rem 0 3rem 0;
            position: relative;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }

        .book-hero-content {
            display: grid;
            grid-template-columns: 300px 1fr;
            gap: 3rem;
            align-items: start;
            position: relative;
            z-index: 2;
        }

        .book-cover-large {
            position: relative;
        }

        .cover-placeholder {
            width: 100%;
            height: 400px;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.2), rgba(255, 255, 255, 0.1));
            backdrop-filter: blur(10px);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 4rem;
            border: 2px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
        }

        .rating-badge {
            position: absolute;
            bottom: -20px;
            left: 50%;
            transform: translateX(-50%);
            background: white;
            padding: 1.2rem 1.8rem;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            text-align: center;
            min-width: 140px;
        }

        .rating-number {
            display: block;
            font-size: 2.2rem;
            font-weight: 800;
            color: #1f2937;
            line-height: 1;
        }

        .review-count {
            display: block;
            color: #6b7280;
            font-size: 0.9rem;
            margin-top: 0.25rem;
        }

        .book-info {
            padding-top: 1rem;
        }

        .book-meta-header {
            display: flex;
            gap: 1rem;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
            align-items: center;
        }

        .book-category {
            background: linear-gradient(135deg, #fbbf24, #f59e0b);
            color: white;
            padding: 0.6rem 1.2rem;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
            backdrop-filter: blur(10px);
        }

        .book-year {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            padding: 0.6rem 1.2rem;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 500;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .book-price-tag {
            background: linear-gradient(135deg, #10b981, #34d399);
            color: white;
            padding: 0.6rem 1.2rem;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
            backdrop-filter: blur(10px);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .book-price-tag i {
            font-size: 0.8rem;
        }

        .book-title-main {
            color: white;
            font-size: 3rem;
            font-weight: 800;
            margin: 0 0 1rem 0;
            line-height: 1.2;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
        }

        .book-author-main {
            color: rgba(255, 255, 255, 0.9);
            font-size: 1.4rem;
            margin: 0 0 2rem 0;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-weight: 500;
        }

        .book-author-main i {
            color: #fbbf24;
        }

        .book-description-main {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(15px);
            padding: 2rem;
            border-radius: 16px;
            margin-bottom: 2.5rem;
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
            line-height: 1.7;
        }

        .book-actions-main {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .btn {
            padding: 1rem 2rem;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            font-size: 1rem;
        }

        .btn-primary {
            background: linear-gradient(135deg, #fbbf24, #f59e0b);
            color: white;
            box-shadow: 0 8px 20px rgba(251, 191, 36, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 25px rgba(251, 191, 36, 0.4);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: 2px solid rgba(255, 255, 255, 0.3);
            backdrop-filter: blur(10px);
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
        }

        .btn-success {
            background: linear-gradient(135deg, #10b981, #34d399);
            color: white;
            box-shadow: 0 8px 20px rgba(16, 185, 129, 0.3);
        }

        .btn-success:hover {
            background: linear-gradient(135deg, #0ea271, #2da788);
            transform: translateY(-2px);
            box-shadow: 0 12px 25px rgba(16, 185, 129, 0.4);
        }

        /* Order Section Styles */
        .order-section {
            background: white;
            border-radius: 20px;
            padding: 2.5rem;
            margin-bottom: 2.5rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            border: 1px solid #e5e7eb;
        }

        .order-section h2 {
            color: #1f2937;
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .order-section h2 i {
            color: #10b981;
        }

        .order-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 2rem;
        }

        .order-card {
            background: linear-gradient(135deg, #f9fafb, #f3f4f6);
            border-radius: 16px;
            padding: 2rem;
            border: 2px solid #e5e7eb;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .order-card:hover {
            transform: translateY(-5px);
            border-color: #10b981;
            box-shadow: 0 10px 30px rgba(16, 185, 129, 0.1);
        }

        .order-card.selected {
            border-color: #10b981;
            background: linear-gradient(135deg, #f0fdf4, #dcfce7);
        }

        .order-card-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .order-icon {
            width: 60px;
            height: 60px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            color: #10b981;
            border: 2px solid #10b981;
        }

        .order-card h3 {
            color: #1f2937;
            font-size: 1.4rem;
            font-weight: 700;
        }

        .order-price {
            font-size: 2rem;
            font-weight: 800;
            color: #10b981;
            margin-bottom: 1rem;
        }

        .order-features {
            list-style: none;
            margin: 1.5rem 0;
        }

        .order-features li {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 0.75rem;
            color: #4b5563;
        }

        .order-features i {
            color: #10b981;
            font-size: 1rem;
        }

        .order-quantity {
            margin: 1.5rem 0;
        }

        .quantity-selector {
            display: flex;
            align-items: center;
            gap: 1rem;
            background: white;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            padding: 0.5rem;
            max-width: 150px;
        }

        .quantity-btn {
            width: 40px;
            height: 40px;
            border: none;
            background: #f3f4f6;
            border-radius: 8px;
            font-size: 1.2rem;
            font-weight: 700;
            color: #1f2937;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .quantity-btn:hover {
            background: #10b981;
            color: white;
        }

        .quantity-input {
            width: 50px;
            text-align: center;
            font-weight: 700;
            font-size: 1.2rem;
            border: none;
            background: transparent;
        }

        .order-total {
            background: white;
            padding: 1rem;
            border-radius: 12px;
            margin: 1.5rem 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-weight: 700;
        }

        .order-total span:last-child {
            font-size: 1.4rem;
            color: #10b981;
        }

        .order-actions {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }

        .btn-order {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            padding: 1.2rem 2.5rem;
            border-radius: 50px;
            font-weight: 700;
            font-size: 1.1rem;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            flex: 1;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 1rem;
        }

        .btn-order:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(16, 185, 129, 0.4);
        }

        .btn-order:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
        }

        .order-success {
            background: linear-gradient(135deg, #f0fdf4, #dcfce7);
            border: 3px solid #10b981;
            border-radius: 16px;
            padding: 2rem;
            text-align: center;
            margin-bottom: 2rem;
        }

        .order-success i {
            font-size: 4rem;
            color: #10b981;
            margin-bottom: 1rem;
        }

        .order-success h3 {
            color: #166534;
            font-size: 1.8rem;
            margin-bottom: 1rem;
        }

        .order-success p {
            color: #047857;
            font-size: 1.1rem;
        }

        .order-error {
            background: #fee2e2;
            border: 2px solid #f87171;
            border-radius: 12px;
            padding: 1rem;
            color: #991b1b;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        /* M-PESA Donation Button - ALWAYS VISIBLE */
        .mpesa-donate-btn {
            background: linear-gradient(135deg, #00A651, #008f45);
            color: white;
            padding: 1.2rem 2.5rem;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 700;
            font-size: 1.2rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 1rem;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 10px 25px rgba(0, 166, 81, 0.4);
            border: 2px solid rgba(255, 255, 255, 0.3);
            letter-spacing: 0.5px;
        }

        .mpesa-donate-btn:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 166, 81, 0.6);
            background: linear-gradient(135deg, #008f45, #006b34);
        }

        .mpesa-donate-btn i {
            font-size: 1.4rem;
            color: #ffd700;
        }

        .mpesa-donate-btn-small {
            padding: 0.8rem 1.5rem;
            font-size: 1rem;
        }

        .mpesa-donate-btn-large {
            font-size: 1.4rem;
            padding: 1.5rem 3rem;
        }

        .mpesa-badge {
            background: #00A651;
            color: white;
            padding: 0.4rem 1rem;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .mpesa-badge i {
            color: #ffd700;
        }

        /* Fixed Donate Button */
        .donate-float {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: linear-gradient(135deg, #00A651, #008f45);
            color: white;
            padding: 1rem 2rem;
            border-radius: 50px;
            box-shadow: 0 10px 30px rgba(0, 166, 81, 0.4);
            z-index: 1000;
            display: flex;
            align-items: center;
            gap: 1rem;
            font-weight: 700;
            font-size: 1.1rem;
            border: 2px solid rgba(255, 255, 255, 0.3);
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            animation: pulse 2s infinite;
        }

        .donate-float:hover {
            transform: translateY(-5px) scale(1.05);
            box-shadow: 0 15px 35px rgba(0, 166, 81, 0.6);
        }

        .donate-float i {
            font-size: 1.5rem;
            color: #ffd700;
        }

        /* Reviews Section */
        .reviews-section {
            background: white;
            border-radius: 20px;
            padding: 3rem;
            margin-bottom: 3rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            border: 1px solid #e5e7eb;
        }

        .section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: 2.5rem;
            flex-wrap: wrap;
        }

        .section-header h2 {
            color: #1f2937;
            font-size: 2rem;
            font-weight: 700;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .review-count-badge {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 1rem;
            font-weight: 600;
        }

        .section-donate-btn {
            background: linear-gradient(135deg, #00A651, #008f45);
            color: white;
            padding: 0.8rem 2rem;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            transition: all 0.3s ease;
            border: 2px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 5px 15px rgba(0, 166, 81, 0.3);
        }

        .section-donate-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 166, 81, 0.5);
        }

        .section-donate-btn i {
            color: #ffd700;
        }

        /* Review Cards */
        .user-review-card,
        .add-review-card,
        .login-prompt-card {
            border-radius: 16px;
            padding: 2.5rem;
            margin-bottom: 2.5rem;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
            border: 1px solid #f1f5f9;
        }

        .user-review-card {
            background: linear-gradient(135deg, #f0f9ff, #e0f2fe);
            border: 2px solid #bae6fd;
        }

        .add-review-card {
            background: linear-gradient(135deg, #f0fdf4, #dcfce7);
            border: 2px solid #bbf7d0;
        }

        .login-prompt-card {
            background: linear-gradient(135deg, #fffbeb, #fef3c7);
            border: 2px dashed #fbbf24;
        }

        .review-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .review-card-header h3 {
            color: #0369a1;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 1.4rem;
        }

        .review-status {
            padding: 0.6rem 1.2rem;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
        }

        .review-status.approved {
            background: #10b981;
            color: white;
        }

        .review-status.pending {
            background: #f59e0b;
            color: white;
        }

        .rating-stars {
            display: flex;
            gap: 0.3rem;
        }

        .rating-stars i {
            color: #e5e7eb;
            font-size: 1.2rem;
        }

        .rating-stars i.active {
            color: #fbbf24;
        }

        .review-rating {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .rating-text {
            color: #6b7280;
            font-weight: 600;
            font-size: 1.1rem;
        }

        .review-content {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .review-content p {
            color: #374151;
            line-height: 1.6;
            margin: 0;
            font-size: 1.05rem;
        }

        .review-actions {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .btn-small {
            padding: 0.75rem 1.5rem;
            font-size: 0.9rem;
        }

        .btn-danger {
            background: #ef4444;
            color: white;
        }

        .btn-danger:hover {
            background: #dc2626;
            transform: translateY(-2px);
        }

        /* Review Form */
        .review-form {
            margin-top: 1.5rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            color: #1f2937;
            font-weight: 600;
            margin-bottom: 0.75rem;
            font-size: 1.1rem;
        }

        .rating-selector {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .rating-selector input {
            display: none;
        }

        .rating-selector label {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem 1.5rem;
            background: white;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .rating-selector label:hover {
            border-color: #667eea;
            background: #f8fafc;
            transform: translateX(5px);
        }

        .rating-selector input:checked + label {
            border-color: #667eea;
            background: rgba(102, 126, 234, 0.1);
        }

        .rating-selector i {
            color: #fbbf24;
            font-size: 1.1rem;
        }

        .form-input {
            width: 100%;
            padding: 1rem 1.5rem;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            font-size: 1rem;
            transition: all 0.3s ease;
            font-family: 'Inter', sans-serif;
        }

        .form-input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        /* Login Prompt */
        .prompt-content {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .prompt-content i {
            font-size: 2.5rem;
            color: #f59e0b;
        }

        .login-link {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }

        .login-link:hover {
            text-decoration: underline;
        }

        /* Community Reviews */
        .community-reviews-title {
            color: #1f2937;
            font-size: 1.6rem;
            margin: 0 0 2rem 0;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .reviews-grid {
            display: grid;
            gap: 1.5rem;
        }

        .community-review-card {
            background: white;
            border-radius: 16px;
            padding: 2rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            border: 1px solid #f1f5f9;
            transition: all 0.3s ease;
            position: relative;
        }

        .community-review-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
        }

        .reviewer-info {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .reviewer-avatar {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 1.5rem;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }

        .reviewer-details {
            flex: 1;
        }

        .reviewer-name {
            color: #1f2937;
            display: block;
            font-weight: 600;
            font-size: 1.1rem;
        }

        .review-date {
            color: #6b7280;
            font-size: 0.9rem;
        }

        /* Review Donation Section */
        .review-donate-section {
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 2px dashed #e5e7eb;
            display: flex;
            justify-content: flex-end;
        }

        .review-donate-btn {
            background: linear-gradient(135deg, #00A651, #008f45);
            color: white;
            padding: 0.8rem 1.8rem;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.95rem;
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(0, 166, 81, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .review-donate-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 166, 81, 0.5);
            background: linear-gradient(135deg, #008f45, #006b34);
        }

        .review-donate-btn i {
            color: #ffd700;
        }

        /* Support Prompt */
        .support-prompt-card {
            background: linear-gradient(135deg, #f0fdf4, #dcfce7);
            border: 3px solid #22c55e;
            border-radius: 20px;
            padding: 3rem;
            margin: 2.5rem 0;
            position: relative;
            overflow: hidden;
            box-shadow: 0 15px 35px rgba(34, 197, 94, 0.2);
            text-align: center;
        }

        .support-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background: #22c55e;
            color: white;
            padding: 0.5rem 1.5rem;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 700;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        .support-icon {
            font-size: 4rem;
            color: #22c55e;
            margin-bottom: 1.5rem;
        }

        .support-title {
            color: #166534;
            font-size: 2rem;
            margin-bottom: 1rem;
            font-weight: 700;
        }

        .support-subtitle {
            color: #166534;
            font-size: 1.3rem;
            margin-bottom: 1.5rem;
            line-height: 1.6;
        }

        .support-highlight {
            color: #059669;
            font-weight: 800;
            font-size: 1.4rem;
            margin-bottom: 2rem;
        }

        .support-button {
            background: #00A651;
            color: white;
            padding: 1.3rem 3rem;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 700;
            font-size: 1.2rem;
            box-shadow: 0 10px 30px rgba(0, 166, 81, 0.4);
            transition: all 0.3s ease;
            display: inline-block;
            border: none;
            cursor: pointer;
        }

        .support-button:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 166, 81, 0.5);
            background: #008f45;
        }

        .support-close {
            margin-top: 2rem;
        }

        .support-close a {
            color: #059669;
            text-decoration: underline;
            font-weight: 600;
            font-size: 1rem;
        }

        .support-close a:hover {
            color: #047857;
        }

        /* Empty States */
        .empty-reviews {
            text-align: center;
            padding: 4rem 2rem;
            color: #6b7280;
            background: #f8fafc;
            border-radius: 16px;
            border: 2px dashed #e5e7eb;
        }

        .empty-reviews i {
            font-size: 4rem;
            margin-bottom: 1.5rem;
            color: #e5e7eb;
        }

        .empty-reviews h4 {
            color: #6b7280;
            margin-bottom: 0.75rem;
            font-size: 1.5rem;
        }

        /* Donation Banner */
        .donate-banner {
            background: linear-gradient(135deg, #f0fdf4, #dcfce7);
            border: 2px solid #22c55e;
            border-radius: 16px;
            padding: 1.5rem 2rem;
            margin: 2rem 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 1.5rem;
        }

        .donate-banner-content {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            flex-wrap: wrap;
        }

        .donate-banner i {
            font-size: 2.5rem;
            color: #22c55e;
        }

        .donate-banner-text h3 {
            color: #166534;
            font-size: 1.3rem;
            margin-bottom: 0.25rem;
        }

        .donate-banner-text p {
            color: #059669;
        }

        /* Animations */
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        
        .pulse-heart { 
            animation: pulse 2s infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        .float {
            animation: float 3s ease-in-out infinite;
        }

        /* Responsive Design */
        @media (max-width: 1024px) {
            .book-hero-content {
                grid-template-columns: 250px 1fr;
                gap: 2rem;
            }
            
            .cover-placeholder {
                height: 350px;
            }
            
            .book-title-main {
                font-size: 2.5rem;
            }
            
            .order-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .main-container {
                padding: 0 1.5rem;
            }
            
            .book-hero-section {
                padding: 2rem 1.5rem;
            }
            
            .book-hero-content {
                grid-template-columns: 1fr;
                gap: 2rem;
                text-align: center;
            }
            
            .book-cover-large {
                max-width: 250px;
                margin: 0 auto;
            }
            
            .cover-placeholder {
                height: 300px;
            }
            
            .book-meta-header {
                justify-content: center;
            }
            
            .book-title-main {
                font-size: 2rem;
            }
            
            .book-author-main {
                font-size: 1.2rem;
            }
            
            .reviews-section {
                padding: 2rem;
            }
            
            .section-header {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .review-card-header {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .review-actions {
                flex-direction: column;
            }
            
            .book-actions-main {
                flex-direction: column;
                align-items: stretch;
            }
            
            .support-prompt-card {
                padding: 2rem 1.5rem;
            }
            
            .support-title {
                font-size: 1.6rem;
            }
            
            .support-subtitle {
                font-size: 1.1rem;
            }
            
            .donate-float {
                bottom: 20px;
                right: 20px;
                padding: 0.8rem 1.5rem;
                font-size: 1rem;
            }
            
            .donate-banner {
                flex-direction: column;
                text-align: center;
            }
            
            .donate-banner-content {
                justify-content: center;
            }
            
            .order-actions {
                flex-direction: column;
            }
        }

        @media (max-width: 480px) {
            .main-container {
                padding: 0 1rem;
            }
            
            .book-hero-section {
                padding: 1.5rem 1rem;
                margin: 1rem 0 2rem 0;
            }
            
            .book-title-main {
                font-size: 1.8rem;
            }
            
            .reviews-section {
                padding: 1.5rem;
            }
            
            .user-review-card,
            .add-review-card,
            .login-prompt-card {
                padding: 1.5rem;
            }
            
            .support-prompt-card {
                padding: 1.5rem 1rem;
            }
            
            .support-title {
                font-size: 1.4rem;
            }
            
            .support-icon {
                font-size: 3rem;
            }
            
            .mpesa-donate-btn-large {
                font-size: 1.2rem;
                padding: 1.2rem 2rem;
            }
            
            .order-card {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <!-- Floating Donate Button - ALWAYS VISIBLE -->
    <?php if(isset($_SESSION['user_id']) && count($reviews) > 0): ?>
        <a href="../payments/pay.php?reviewer_id=<?php echo $reviews[0]['user_id']; ?>&book_id=<?php echo $book_id; ?>" class="donate-float">
            <i class="fas fa-hand-holding-heart"></i>
            <span>Support a Reviewer</span>
            <i class="fas fa-arrow-right"></i>
        </a>
    <?php endif; ?>

    <div class="main-container">
        <!-- Book Hero Section -->
        <div class="book-hero-section">
            <div class="book-hero-content">
                <div class="book-cover-large">
                    <div class="cover-placeholder">
                        <i class="fas fa-book-open"></i>
                    </div>
                    <?php if($rating_info['avg_rating']): ?>
                    <div class="rating-badge">
                        <span class="rating-number"><?php echo number_format($rating_info['avg_rating'], 1); ?></span>
                        <div class="rating-stars">
                            <?php for($i = 1; $i <= 5; $i++): ?>
                                <i class="fas fa-star <?php echo $i <= round($rating_info['avg_rating']) ? 'active' : ''; ?>"></i>
                            <?php endfor; ?>
                        </div>
                        <span class="review-count"><?php echo $rating_info['review_count']; ?> reviews</span>
                    </div>
                    <?php endif; ?>
                </div>
                
                <div class="book-info">
                    <div class="book-meta-header">
                        <span class="book-category"><?php echo htmlspecialchars($book['category']); ?></span>
                        <span class="book-year">Published <?php echo htmlspecialchars($book['year']); ?></span>
                        <?php if(isset($book['price']) && $book['price'] > 0): ?>
                            <span class="book-price-tag">
                                <i class="fas fa-tag"></i>
                                KSh <?php echo number_format($book['price'], 2); ?>
                            </span>
                        <?php endif; ?>
                    </div>
                    
                    <h1 class="book-title-main"><?php echo htmlspecialchars($book['title']); ?></h1>
                    <p class="book-author-main">
                        <i class="fas fa-user-pen"></i>
                        By <?php echo htmlspecialchars($book['author']); ?>
                    </p>
                    
                    <div class="book-description-main">
                        <p><?php echo htmlspecialchars($book['description']); ?></p>
                    </div>
                    
                    <div class="book-actions-main">
                        <?php if(isset($_SESSION['user_id']) && !$user_review): ?>
                            <button class="btn btn-primary" onclick="document.getElementById('reviewForm').scrollIntoView({behavior: 'smooth'})">
                                <i class="fas fa-star"></i>
                                Write a Review
                            </button>
                        <?php endif; ?>
                        
                        <!-- Always Visible Donate Button in Hero Section -->
                        <?php if(isset($_SESSION['user_id']) && count($reviews) > 0): ?>
                            <a href="../payments/pay.php?reviewer_id=<?php echo $reviews[0]['user_id']; ?>&book_id=<?php echo $book_id; ?>" 
                               class="mpesa-donate-btn">
                                <i class="fas fa-hand-holding-heart"></i>
                                Support Reviewers (KSh <?php echo number_format($book['price'] ?? 50, 0); ?>)
                            </a>
                        <?php endif; ?>
                        
                        <a href="list.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i>
                            Back to Books
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Order Section -->
        <?php if(isset($book['price']) && $book['price'] > 0): ?>
        <div class="order-section">
            <h2>
                <i class="fas fa-shopping-cart"></i>
                Purchase This Book
            </h2>
            
            <?php if($order_success): ?>
                <div class="order-success">
                    <i class="fas fa-check-circle"></i>
                    <h3>Order Placed Successfully! 🎉</h3>
                    <p>Your order has been received and is being processed.</p>
                    <p style="margin-top: 1rem;">
                        <a href="../orders/my_orders.php" class="btn btn-success" style="text-decoration: none;">
                            View My Orders
                        </a>
                    </p>
                </div>
            <?php endif; ?>
            
            <?php if($order_error): ?>
                <div class="order-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <div><?php echo htmlspecialchars($order_error); ?></div>
                </div>
            <?php endif; ?>
            
            <?php if(!isset($_SESSION['user_id'])): ?>
                <div class="login-prompt-card" style="margin-bottom: 0;">
                    <div class="prompt-content">
                        <i class="fas fa-lock"></i>
                        <div>
                            <h4>Login to Purchase</h4>
                            <p>Please <a href="../auth/login.php" class="login-link">login</a> to order this book.</p>
                        </div>
                    </div>
                </div>
            <?php elseif($user_ordered): ?>
                <div class="order-success" style="background: #e0f2fe; border-color: #38bdf8;">
                    <i class="fas fa-check-circle" style="color: #0284c7;"></i>
                    <h3 style="color: #075985;">You've Already Ordered This Book!</h3>
                    <p style="color: #0369a1;">Check your order status in <a href="../orders/my_orders.php" style="color: #0284c7; font-weight: 700;">My Orders</a></p>
                </div>
            <?php else: ?>
                <form method="POST" id="orderForm">
                    <input type="hidden" name="place_order" value="1">
                    
                    <div class="order-grid">
                        <!-- Physical Copy Option -->
                        <div class="order-card <?php echo (!isset($_POST['order_type']) || $_POST['order_type'] == 'physical') ? 'selected' : ''; ?>" 
                             onclick="selectOrderType('physical')">
                            <div class="order-card-header">
                                <div class="order-icon">
                                    <i class="fas fa-book"></i>
                                </div>
                                <h3>Physical Copy</h3>
                            </div>
                            <div class="order-price">KSh <?php echo number_format($book['price'], 2); ?></div>
                            <ul class="order-features">
                                <li><i class="fas fa-check"></i> Hardcover/Paperback</li>
                                <li><i class="fas fa-check"></i> Free delivery within Nairobi</li>
                                <li><i class="fas fa-check"></i> Delivery in 2-3 business days</li>
                                <li><i class="fas fa-check"></i> Cash on delivery available</li>
                            </ul>
                            <div class="order-quantity" onclick="event.stopPropagation()">
                                <label style="display: block; margin-bottom: 0.5rem; color: #4b5563;">Quantity:</label>
                                <div class="quantity-selector">
                                    <button type="button" class="quantity-btn" onclick="updateQuantity('physical', -1)">-</button>
                                    <input type="number" name="quantity" id="physical-quantity" class="quantity-input" value="1" min="1" max="10" readonly>
                                    <button type="button" class="quantity-btn" onclick="updateQuantity('physical', 1)">+</button>
                                </div>
                            </div>
                            <input type="hidden" name="order_type" id="order-type" value="physical">
                        </div>
                        
                        <!-- E-Book Option -->
                        <div class="order-card <?php echo (isset($_POST['order_type']) && $_POST['order_type'] == 'ebook') ? 'selected' : ''; ?>" 
                             onclick="selectOrderType('ebook')">
                            <div class="order-card-header">
                                <div class="order-icon">
                                    <i class="fas fa-tablet-alt"></i>
                                </div>
                                <h3>E-Book</h3>
                            </div>
                            <div class="order-price">KSh <?php echo number_format($book['price'] * 0.7, 2); ?></div>
                            <ul class="order-features">
                                <li><i class="fas fa-check"></i> Instant download</li>
                                <li><i class="fas fa-check"></i> PDF, EPUB, MOBI formats</li>
                                <li><i class="fas fa-check"></i> Read on any device</li>
                                <li><i class="fas fa-check"></i> Lifetime access</li>
                            </ul>
                            <div class="order-total">
                                <span>Total:</span>
                                <span>KSh <span id="ebook-total"><?php echo number_format($book['price'] * 0.7, 2); ?></span></span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="order-actions">
                        <button type="submit" class="btn-order" id="placeOrderBtn">
                            <i class="fas fa-shopping-cart"></i>
                            Place Order
                            <span id="order-total-display">(KSh <?php echo number_format($book['price'], 2); ?>)</span>
                        </button>
                    </div>
                </form>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        
        <!-- Donation Banner - Always Visible when logged in -->
        <?php if(isset($_SESSION['user_id']) && count($reviews) > 0): ?>
            <div class="donate-banner">
                <div class="donate-banner-content">
                    <i class="fas fa-hand-holding-heart float"></i>
                    <div class="donate-banner-text">
                        <h3>Support the Review Community</h3>
                        <p>Show appreciation for helpful reviews with M-PESA</p>
                    </div>
                </div>
                <a href="../payments/pay.php?reviewer_id=<?php echo $reviews[0]['user_id']; ?>&book_id=<?php echo $book_id; ?>" 
                   class="mpesa-donate-btn mpesa-donate-btn-small">
                    <i class="fas fa-heart"></i>
                    Donate Now
                </a>
            </div>
        <?php endif; ?>

        <!-- Reviews Section -->
        <div class="reviews-section">
            <div class="section-header">
                <h2>
                    <i class="fas fa-star"></i>
                    Community Reviews
                    <span class="review-count-badge"><?php echo count($reviews); ?></span>
                </h2>
                
                <!-- Section Donate Button -->
                <?php if(isset($_SESSION['user_id']) && count($reviews) > 0): ?>
                    <a href="../payments/pay.php?reviewer_id=<?php echo $reviews[0]['user_id']; ?>&book_id=<?php echo $book_id; ?>" 
                       class="section-donate-btn">
                        <i class="fas fa-hand-holding-heart"></i>
                        Support Reviewers
                    </a>
                <?php endif; ?>
            </div>
            
            <!-- USER'S OWN REVIEW OR ADD REVIEW FORM -->
            <?php if(isset($_SESSION['user_id'])): ?>
                <?php if($user_review): ?>
                    <!-- Existing review -->
                    <div class="user-review-card">
                        <div class="review-card-header">
                            <h3><i class="fas fa-user-check"></i> Your Review</h3>
                            <span class="review-status <?php echo $user_review['status']; ?>">
                                <?php echo ucfirst($user_review['status']); ?>
                            </span>
                        </div>
                        <div class="review-rating">
                            <div class="rating-stars">
                                <?php for($i=1;$i<=5;$i++): ?>
                                    <i class="fas fa-star <?php echo $i <= $user_review['rating'] ? 'active' : ''; ?>"></i>
                                <?php endfor; ?>
                            </div>
                            <span class="rating-text">Rated <?php echo $user_review['rating']; ?>/5</span>
                        </div>
                        <div class="review-content">
                            <p><?php echo htmlspecialchars($user_review['comment']); ?></p>
                        </div>
                        <div class="review-actions">
                            <a href="../reviews/edit_review.php?id=<?php echo $user_review['review_id']; ?>" class="btn btn-secondary btn-small">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <a href="../reviews/delete_review.php?id=<?php echo $user_review['review_id']; ?>" 
                               onclick="return confirm('Delete this review?')" class="btn btn-danger btn-small">
                                <i class="fas fa-trash"></i> Delete
                            </a>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- Add review form -->
                    <div class="add-review-card" id="reviewForm">
                        <h3><i class="fas fa-pen"></i> Share Your Thoughts</h3>
                        <form method="POST" action="../reviews/add_review.php" class="review-form">
                            <input type="hidden" name="book_id" value="<?php echo $book_id; ?>">
                            <div class="form-group">
                                <label class="form-label">Your Rating</label>
                                <div class="rating-selector">
                                    <?php for($i=5;$i>=1;$i--): ?>
                                        <input type="radio" id="star<?php echo $i; ?>" name="rating" value="<?php echo $i; ?>" <?php echo $i==5?'checked':''; ?>>
                                        <label for="star<?php echo $i; ?>">
                                            <i class="fas fa-star"></i>
                                            <span><?php echo $i; ?> Star<?php echo $i>1?'s':''; ?></span>
                                        </label>
                                    <?php endfor; ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Your Review</label>
                                <textarea name="comment" rows="4" class="form-input" required placeholder="What did you think of this book?"></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane"></i> Submit Review
                            </button>
                        </form>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="login-prompt-card">
                    <div class="prompt-content">
                        <i class="fas fa-info-circle"></i>
                        <div>
                            <h4>Want to share your thoughts?</h4>
                            <p>Please <a href="../auth/login.php" class="login-link">login</a> to write a review.</p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- M-PESA SUPPORT PROMPT (Shows after review submission) -->
            <?php if (isset($_SESSION['show_support_prompt']) && $_SESSION['show_support_prompt'] === true): ?>
                <div class="support-prompt-card">
                    <div class="support-badge">SHOW APPRECIATION</div>
                    <i class="fas fa-hand-holding-heart support-icon pulse-heart"></i>
                    <h3 class="support-title">Thank you for your review!</h3>
                    <p class="support-subtitle">
                        Your honest thoughts help thousands discover great books like "<?php echo htmlspecialchars($book['title']); ?>"
                    </p>
                    <p class="support-highlight">
                        Want to support other reviewers who provide helpful insights?
                    </p>
                    <a href="../payments/pay.php?reviewer_id=<?php echo $_SESSION['support_reviewer_id']; ?>&book_id=<?php echo $_SESSION['support_book_id']; ?>" 
                       class="support-button">
                       <i class="fas fa-hand-holding-heart"></i> Send Support via M-Pesa (KSh <?php echo number_format($book['price'] ?? 50, 0); ?>)
                    </a>
                    <div class="support-close">
                        <a href="view.php?id=<?php echo $book_id; ?>&clear_support=1">
                            No thanks, maybe later
                        </a>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Community Reviews -->
            <div class="community-reviews">
                <h3 class="community-reviews-title">
                    <i class="fas fa-users"></i> Community Reviews
                    <?php if(count($reviews) > 0 && isset($_SESSION['user_id'])): ?>
                        <span class="mpesa-badge">
                            <i class="fas fa-hand-holding-heart"></i> Support Reviewers
                        </span>
                    <?php endif; ?>
                </h3>
                
                <?php if(count($reviews) > 0): ?>
                    <div class="reviews-grid">
                        <?php foreach($reviews as $review): ?>
                            <?php if(isset($_SESSION['user_id']) && $review['user_id'] == $_SESSION['user_id']) continue; ?>
                            <div class="community-review-card">
                                <div class="reviewer-info">
                                    <div class="reviewer-avatar"><?php echo strtoupper(substr($review['name'],0,1)); ?></div>
                                    <div class="reviewer-details">
                                        <strong class="reviewer-name"><?php echo htmlspecialchars($review['name']); ?></strong>
                                        <span class="review-date"><?php echo date('M j, Y', strtotime($review['created_at'])); ?></span>
                                    </div>
                                </div>
                                <div class="review-rating">
                                    <div class="rating-stars">
                                        <?php for($i=1;$i<=5;$i++): ?>
                                            <i class="fas fa-star <?php echo $i<=$review['rating']?'active':''; ?>"></i>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                                <div class="review-content">
                                    <p><?php echo htmlspecialchars($review['comment']); ?></p>
                                </div>
                                
                                <!-- Donate button for each review - ALWAYS VISIBLE when logged in -->
                                <?php if(isset($_SESSION['user_id'])): ?>
                                    <div class="review-donate-section">
                                        <a href="../payments/pay.php?reviewer_id=<?php echo $review['user_id']; ?>&book_id=<?php echo $book_id; ?>" 
                                           class="review-donate-btn">
                                            <i class="fas fa-hand-holding-heart"></i>
                                            Support This Reviewer (KSh <?php echo number_format($book['price'] ?? 50, 0); ?>)
                                            <i class="fas fa-arrow-right"></i>
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="empty-reviews">
                        <i class="fas fa-comment-slash"></i>
                        <h4>No reviews yet</h4>
                        <p>Be the first to share your thoughts!</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Animate cards on scroll
            const observer = new IntersectionObserver(entries => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            }, { threshold: 0.1 });

            document.querySelectorAll('.community-review-card').forEach(card => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                card.style.transition = 'all 0.6s ease';
                observer.observe(card);
            });

            // Smooth scroll for anchor links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({ behavior: 'smooth' });
                    }
                });
            });
        });

        // Order type selection
        function selectOrderType(type) {
            document.getElementById('order-type').value = type;
            
            // Update selected card styling
            document.querySelectorAll('.order-card').forEach(card => {
                card.classList.remove('selected');
            });
            
            // Find the clicked card and add selected class
            if (type === 'physical') {
                document.querySelectorAll('.order-card')[0].classList.add('selected');
                document.getElementById('order-total-display').textContent = '(KSh <?php echo number_format($book['price'], 2); ?>)';
            } else {
                document.querySelectorAll('.order-card')[1].classList.add('selected');
                document.getElementById('order-total-display').textContent = '(KSh <?php echo number_format($book['price'] * 0.7, 2); ?>)';
            }
        }

        // Update quantity for physical books
        function updateQuantity(type, change) {
            if (type === 'physical') {
                const input = document.getElementById('physical-quantity');
                let value = parseInt(input.value) + change;
                if (value < 1) value = 1;
                if (value > 10) value = 10;
                input.value = value;
                
                // Update total display
                const pricePerUnit = <?php echo $book['price']; ?>;
                const total = pricePerUnit * value;
                document.getElementById('order-total-display').textContent = `(KSh ${total.toFixed(2)})`;
            }
        }

        // Form validation before submit
        document.getElementById('orderForm')?.addEventListener('submit', function(e) {
            const placeOrderBtn = document.getElementById('placeOrderBtn');
            placeOrderBtn.disabled = true;
            placeOrderBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing Order...';
        });
    </script>

    <?php include '../includes/footer.php'; ?>
</body>
</html>