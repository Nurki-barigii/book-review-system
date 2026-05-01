<?php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: auth/login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Get all paid digital books for this user with PDF file path
$stmt = $pdo->prepare("
    SELECT 
        o.id as order_id, 
        o.created_at as order_date,
        o.status as order_status,
        b.book_id, 
        b.title, 
        b.author, 
        b.price,
        b.pdf_file,
        s.quantity_sold, 
        s.total_price,
        p.payment_status, 
        p.updated_at as payment_date,
        p.id as payment_id
    FROM orders o
    INNER JOIN sales s ON o.id = s.order_id
    INNER JOIN books b ON s.product_id = b.book_id
    LEFT JOIN payments p ON p.order_id = o.id
    WHERE o.user_id = ? 
AND (o.status = 'paid' OR p.payment_status = 'completed')
    ORDER BY o.created_at DESC
");
$stmt->execute([$user_id]);
$digital_books = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Digital Library - BookReview</title>
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
            max-width: 1200px;
            margin: 0 auto;
            width: 100%;
            padding: 2rem;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2.5rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .page-header h1 {
            color: #1f2937;
            font-size: 2.5rem;
            font-weight: 800;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .page-header h1 i {
            color: #22c55e;
        }

        .back-link {
            color: #6b7280;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .back-link:hover {
            color: #22c55e;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.5rem;
            margin-bottom: 2.5rem;
        }

        .stat-card {
            background: white;
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            border: 1px solid #e5e7eb;
            text-align: center;
        }

        .stat-card i {
            font-size: 2rem;
            color: #22c55e;
            margin-bottom: 0.5rem;
        }

        .stat-card .stat-value {
            font-size: 2rem;
            font-weight: 800;
            color: #1f2937;
        }

        .stat-card .stat-label {
            color: #6b7280;
            font-size: 0.9rem;
        }

        .books-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 1.5rem;
        }

        .book-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            border: 1px solid #e5e7eb;
            transition: all 0.3s ease;
            position: relative;
        }

        .book-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(34, 197, 94, 0.15);
            border-color: #22c55e;
        }

        .book-header {
            background: linear-gradient(135deg, #22c55e, #16a34a);
            color: white;
            padding: 1.5rem;
            position: relative;
            overflow: hidden;
        }

        .book-header::after {
            content: 'PDF';
            position: absolute;
            top: -10px;
            right: 10px;
            font-size: 5rem;
            font-weight: 800;
            opacity: 0.1;
            color: white;
            transform: rotate(15deg);
        }

        .book-header h3 {
            font-size: 1.4rem;
            margin-bottom: 0.5rem;
            position: relative;
            z-index: 2;
        }

        .book-header .author {
            opacity: 0.9;
            font-size: 1rem;
            position: relative;
            z-index: 2;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .book-content {
            padding: 1.5rem;
        }

        .book-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px dashed #e5e7eb;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #4b5563;
            font-size: 0.9rem;
        }

        .meta-item i {
            color: #22c55e;
        }

        .payment-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 50px;
            font-size: 0.7rem;
            font-weight: 600;
            background: #22c55e;
            color: white;
            margin-left: 0.5rem;
        }

        .purchase-info {
            background: #f0fdf4;
            border-radius: 12px;
            padding: 1rem;
            margin: 1rem 0;
            border-left: 4px solid #22c55e;
        }

        .purchase-info p {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #166534;
            margin: 0.25rem 0;
            font-size: 0.9rem;
        }

        .purchase-info i {
            color: #22c55e;
            width: 20px;
        }

        /* Copyable link box */
        .pdf-link-box {
            background: #f8fafc;
            border: 1px dashed #22c55e;
            border-radius: 10px;
            padding: 0.75rem 1rem;
            margin-bottom: 0.75rem;
        }

        .pdf-link-box label {
            font-size: 0.75rem;
            font-weight: 600;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            display: block;
            margin-bottom: 0.4rem;
            cursor: default;
        }

        .pdf-link-row {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .pdf-link-input {
            flex: 1;
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 0.4rem 0.75rem;
            font-size: 0.78rem;
            color: #374151;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .copy-btn {
            background: #22c55e;
            color: white;
            border: none;
            border-radius: 8px;
            padding: 0.4rem 0.75rem;
            font-size: 0.78rem;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.4rem;
            white-space: nowrap;
            transition: background 0.2s;
        }

        .copy-btn:hover {
            background: #16a34a;
        }

        .download-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1rem;
            background: linear-gradient(135deg, #22c55e, #16a34a);
            color: white;
            text-decoration: none;
            padding: 1.2rem;
            border-radius: 12px;
            font-weight: 700;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            margin-top: 1rem;
            border: none;
            width: 100%;
            cursor: pointer;
        }

        .download-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(34, 197, 94, 0.4);
        }

        .download-btn i {
            font-size: 1.3rem;
        }

        .download-btn.disabled {
            background: #9ca3af;
            cursor: not-allowed;
            pointer-events: none;
            opacity: 0.6;
        }

        .download-note {
            text-align: center;
            margin-top: 1rem;
            font-size: 0.8rem;
            color: #9ca3af;
        }

        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            background: white;
            border-radius: 20px;
            border: 2px dashed #e5e7eb;
            grid-column: 1 / -1;
        }

        .empty-state i {
            font-size: 4rem;
            color: #22c55e;
            margin-bottom: 1rem;
        }

        .empty-state h2 {
            color: #374151;
            margin-bottom: 0.5rem;
        }

        .empty-state p {
            color: #6b7280;
            margin-bottom: 2rem;
        }

        .btn-primary {
            background: linear-gradient(135deg, #22c55e, #16a34a);
            color: white;
            padding: 1rem 2rem;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(34, 197, 94, 0.3);
        }

        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .books-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="main-container">
        <div class="page-header">
            <h1>
                <i class="fas fa-tablet-alt"></i>
                My Digital Library
            </h1>
            <a href="books/list.php" class="back-link">
                <i class="fas fa-arrow-left"></i>
                Browse More Books
            </a>
        </div>

        <?php if(count($digital_books) > 0): ?>
            <!-- Stats Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <i class="fas fa-book"></i>
                    <div class="stat-value"><?php echo count($digital_books); ?></div>
                    <div class="stat-label">Digital Books</div>
                </div>
                <div class="stat-card">
                    <i class="fas fa-download"></i>
                    <div class="stat-value"><?php echo array_sum(array_column($digital_books, 'quantity_sold')); ?></div>
                    <div class="stat-label">Total Purchases</div>
                </div>
                <div class="stat-card">
                    <i class="fas fa-clock"></i>
                    <div class="stat-value">24/7</div>
                    <div class="stat-label">Access Available</div>
                </div>
            </div>

            <!-- Books Grid -->
            <div class="books-grid">
                <?php foreach($digital_books as $book):
                    $base_url = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/book_review/';
                    $copy_url = $base_url . "downloads.php?order_id={$book['order_id']}&book_id={$book['book_id']}";
                ?>
                    <div class="book-card">
                        <div class="book-header">
                            <h3><?php echo htmlspecialchars($book['title']); ?></h3>
                            <p class="author">
                                <i class="fas fa-user-pen"></i>
                                <?php echo htmlspecialchars($book['author']); ?>
                            </p>
                        </div>
                        
                        <div class="book-content">
                            <div class="book-meta">
                                <span class="meta-item">
                                    <i class="fas fa-hashtag"></i>
                                    Order #<?php echo $book['order_id']; ?>
                                </span>
                                <span class="meta-item">
                                    <i class="fas fa-calendar"></i>
                                    <?php echo date('M j, Y', strtotime($book['order_date'])); ?>
                                </span>
                            </div>

                            <div class="purchase-info">
                                <p><i class="fas fa-check-circle"></i> Status: 
                                    <span class="payment-badge">PAID</span>
                                </p>
                                <p><i class="fas fa-tag"></i> Amount: KSh <?php echo number_format($book['total_price'], 2); ?></p>
                                <p><i class="fas fa-copy"></i> Quantity: <?php echo $book['quantity_sold']; ?></p>
                                <?php if($book['pdf_file']): ?>
                                    <p><i class="fas fa-file-pdf"></i> PDF: <span style="color: #22c55e;">Available</span></p>
                                <?php else: ?>
                                    <p><i class="fas fa-exclamation-triangle"></i> PDF: <span style="color: #f59e0b;">Not uploaded yet</span></p>
                                <?php endif; ?>
                            </div>

                            <!-- COPYABLE LINK BOX -->
                            <?php if($book['pdf_file']): ?>
                                <div class="pdf-link-box">
                                    <label><i class="fas fa-link"></i> Your PDF Access Link</label>
                                    <div class="pdf-link-row">
                                        <input type="text"
                                               class="pdf-link-input"
                                               id="link-<?php echo $book['order_id']; ?>"
                                               value="<?php echo htmlspecialchars($copy_url); ?>"
                                               readonly>
                                        <button class="copy-btn" onclick="copyLink(<?php echo $book['order_id']; ?>, this)">
                                            <i class="fas fa-copy"></i> Copy
                                        </button>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <!-- DOWNLOAD BUTTON - Only enabled if PDF exists -->
                            <?php if($book['pdf_file']): ?>
                                <a href="downloads.php?order_id=<?php echo $book['order_id']; ?>&book_id=<?php echo $book['book_id']; ?>" 
                                   class="download-btn"
                                   onclick="return confirm('Download <?php echo htmlspecialchars(addslashes($book['title'])); ?>?')">
                                    <i class="fas fa-download"></i>
                                    Download PDF Now
                                    <i class="fas fa-arrow-right"></i>
                                </a>
                            <?php else: ?>
                                <div class="download-btn disabled">
                                    <i class="fas fa-clock"></i>
                                    PDF Coming Soon
                                </div>
                            <?php endif; ?>
                            
                            <div class="download-note">
                                <i class="fas fa-shield-alt"></i>
                                Secure download • PDF format • Read anywhere
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <!-- Empty State -->
            <div class="empty-state">
                <i class="fas fa-books"></i>
                <h2>Your Digital Library is Empty</h2>
                <p>Purchase digital books to build your collection!</p>
                <a href="books/list.php" class="btn-primary">
                    <i class="fas fa-shopping-cart"></i>
                    Browse Digital Books
                </a>
            </div>
        <?php endif; ?>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script>
        function copyLink(id, btn) {
            const input = document.getElementById('link-' + id);
            input.select();
            input.setSelectionRange(0, 99999);

            navigator.clipboard.writeText(input.value).then(() => {
                btn.innerHTML = '<i class="fas fa-check"></i> Copied!';
                btn.style.background = '#3b82f6';
                setTimeout(() => {
                    btn.innerHTML = '<i class="fas fa-copy"></i> Copy';
                    btn.style.background = '#22c55e';
                }, 2000);
            }).catch(() => {
                document.execCommand('copy');
                btn.innerHTML = '<i class="fas fa-check"></i> Copied!';
                btn.style.background = '#3b82f6';
                setTimeout(() => {
                    btn.innerHTML = '<i class="fas fa-copy"></i> Copy';
                    btn.style.background = '#22c55e';
                }, 2000);
            });
        }
    </script>
</body>
</html>
