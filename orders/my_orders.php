<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit();
}

// Get user's orders with payment status
$stmt = $pdo->prepare("
    SELECT o.*, s.product_id, s.quantity_sold, s.total_price as sale_total, 
           b.title, b.author, b.book_id, b.price,
           p.payment_status, p.id as payment_id
    FROM orders o 
    JOIN sales s ON o.id = s.order_id 
    JOIN books b ON s.product_id = b.book_id 
    LEFT JOIN payments p ON p.order_id = o.id
    WHERE o.user_id = ? 
    ORDER BY o.created_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
$orders = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - BookReview</title>
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
            color: #10b981;
        }

        .orders-grid {
            display: grid;
            gap: 1.5rem;
        }

        .order-card {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            border: 1px solid #e5e7eb;
            transition: all 0.3s ease;
            display: grid;
            grid-template-columns: auto 1fr auto;
            gap: 2rem;
            align-items: center;
        }

        .order-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.12);
        }

        .order-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #f0fdf4, #dcfce7);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: #10b981;
            border: 3px solid #bbf7d0;
        }

        .order-details h3 {
            color: #1f2937;
            font-size: 1.4rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .order-details .author {
            color: #6b7280;
            margin-bottom: 1rem;
        }

        .order-meta {
            display: flex;
            gap: 2rem;
            flex-wrap: wrap;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #4b5563;
        }

        .meta-item i {
            color: #10b981;
        }

        .order-status {
            display: inline-block;
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .status-pending {
            background: #fef3c7;
            color: #92400e;
        }

        .status-paid {
            background: #dcfce7;
            color: #166534;
        }

        .status-processing {
            background: #dbeafe;
            color: #1e40af;
        }

        .status-shipped {
            background: #e0f2fe;
            color: #0369a1;
        }

        .status-delivered {
            background: #f0fdf4;
            color: #166534;
        }

        .order-actions {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            min-width: 200px;
        }

        .btn {
            padding: 0.9rem 1.5rem;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            font-size: 0.95rem;
        }

        .btn-primary {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(16, 185, 129, 0.3);
        }

        .btn-success {
            background: linear-gradient(135deg, #00A651, #008f45);
            color: white;
        }

        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0, 166, 81, 0.3);
        }

        .btn-secondary {
            background: #f3f4f6;
            color: #1f2937;
        }

        .btn-secondary:hover {
            background: #e5e7eb;
        }

        .btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            background: white;
            border-radius: 20px;
            border: 2px dashed #e5e7eb;
        }

        .empty-state i {
            font-size: 4rem;
            color: #9ca3af;
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

        .payment-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 600;
            margin-left: 0.5rem;
        }

        .payment-paid {
            background: #10b981;
            color: white;
        }

        .payment-pending {
            background: #f59e0b;
            color: white;
        }

        @media (max-width: 768px) {
            .order-card {
                grid-template-columns: 1fr;
                text-align: center;
            }
            
            .order-icon {
                margin: 0 auto;
            }
            
            .order-meta {
                justify-content: center;
            }
            
            .order-actions {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <div class="main-container">
        <div class="page-header">
            <h1>
                <i class="fas fa-shopping-bag"></i>
                My Orders
            </h1>
            <a href="../books/list.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i>
                Continue Shopping
            </a>
        </div>
        
        <?php if(count($orders) > 0): ?>
            <div class="orders-grid">
                <?php foreach($orders as $order): ?>
                    <div class="order-card">
                        <div class="order-icon">
                            <i class="fas fa-book"></i>
                        </div>
                        
                        <div class="order-details">
                            <h3><?php echo htmlspecialchars($order['title']); ?></h3>
                            <p class="author">By <?php echo htmlspecialchars($order['author']); ?></p>
                            
                            <div class="order-meta">
                                <div class="meta-item">
                                    <i class="fas fa-hashtag"></i>
                                    <span>Order #<?php echo $order['id']; ?></span>
                                </div>
                                <div class="meta-item">
                                    <i class="fas fa-calendar"></i>
                                    <span><?php echo date('M j, Y', strtotime($order['created_at'])); ?></span>
                                </div>
                                <div class="meta-item">
                                    <i class="fas fa-cubes"></i>
                                    <span>Qty: <?php echo $order['quantity_sold']; ?></span>
                                </div>
                                <div class="meta-item">
                                    <i class="fas fa-tag"></i>
                                    <span>KSh <?php echo number_format($order['sale_total'], 2); ?></span>
                                </div>
                            </div>
                            
                            <div style="margin-top: 1rem;">
                                <span class="order-status status-<?php echo $order['status']; ?>">
                                    <?php echo ucfirst($order['status']); ?>
                                </span>
                                <?php if($order['payment_status']): ?>
                                    <span class="payment-badge payment-<?php echo $order['payment_status']; ?>">
                                        Payment: <?php echo ucfirst($order['payment_status']); ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="order-actions">
                            <?php if($order['status'] == 'pending' && $order['payment_status'] != 'completed'): ?>
                                <a href="../payments/pay.php?order_id=<?php echo $order['id']; ?>&book_id=<?php echo $order['book_id']; ?>&amount=<?php echo $order['sale_total']; ?>" 
                                   class="btn btn-success">
                                    <i class="fas fa-hand-holding-heart"></i>
                                    Pay with M-PESA
                                </a>
                            <?php endif; ?>
                            
                            <?php if($order['payment_status'] == 'completed'): ?>
                                <button class="btn btn-primary" disabled>
                                    <i class="fas fa-check-circle"></i>
                                    Payment Complete
                                </button>
                            <?php endif; ?>
                            
                            <a href="../books/view.php?id=<?php echo $order['book_id']; ?>" class="btn btn-secondary">
                                <i class="fas fa-eye"></i>
                                View Book
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-shopping-bag"></i>
                <h2>No Orders Yet</h2>
                <p>Looks like you haven't placed any orders. Start shopping now!</p>
                <a href="../books/list.php" class="btn btn-primary" style="text-decoration: none;">
                    Browse Books
                </a>
            </div>
        <?php endif; ?>
    </div>
    
    <?php include '../includes/footer.php'; ?>
</body>
</html>