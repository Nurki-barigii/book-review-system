<?php
session_start();
require_once __DIR__ . '/../../config/db.php';

// Admin only
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../index.php");
    exit();
}

// Handle status change for payments
if (isset($_GET['set_payment_status']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $newStatus = $_GET['set_payment_status'];
    $allowed = ['pending', 'completed', 'failed', 'cancelled'];
    
    if (in_array($newStatus, $allowed)) {
        $stmt = $pdo->prepare("UPDATE payments SET payment_status = ? WHERE id = ?");
        $stmt->execute([$newStatus, $id]);
        
        // If payment is completed and it's for an order, update order status
        if ($newStatus == 'completed') {
            // Get order_id from this payment
            $stmt = $pdo->prepare("SELECT order_id FROM payments WHERE id = ?");
            $stmt->execute([$id]);
            $payment = $stmt->fetch();
            
            if ($payment && $payment['order_id']) {
                // Update order status to 'paid'
                $stmt = $pdo->prepare("UPDATE orders SET status = 'paid' WHERE id = ?");
                $stmt->execute([$payment['order_id']]);
            }
        }
        
        $success = "Payment status updated to <strong>" . ucfirst($newStatus) . "</strong>";
    }
}

// Handle status change for orders
if (isset($_GET['set_order_status']) && isset($_GET['order_id'])) {
    $orderId = (int)$_GET['order_id'];
    $newStatus = $_GET['set_order_status'];
    $allowed = ['pending', 'paid', 'processing', 'shipped', 'delivered', 'cancelled'];
    
    if (in_array($newStatus, $allowed)) {
        $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->execute([$newStatus, $orderId]);
        $success = "Order #$orderId status updated to <strong>" . ucfirst($newStatus) . "</strong>";
    }
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM payments WHERE id = ?");
    if ($stmt->execute([$id])) {
        $success = "Payment record deleted permanently.";
    }
}

// Stats for payments
$totalPayments = $pdo->query("SELECT COUNT(*) FROM payments")->fetchColumn();
$totalAmount = $pdo->query("SELECT SUM(amount) FROM payments WHERE payment_status = 'completed'")->fetchColumn() ?? 0;
$pendingPayments = $pdo->query("SELECT COUNT(*) FROM payments WHERE payment_status = 'pending'")->fetchColumn();
$completedPayments = $pdo->query("SELECT COUNT(*) FROM payments WHERE payment_status = 'completed'")->fetchColumn();

// Stats for orders
$totalOrders = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$pendingOrders = $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'pending'")->fetchColumn();
$paidOrders = $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'paid'")->fetchColumn();

// Get all payments (both support and orders)
$stmt = $pdo->prepare("
    SELECT p.*, 
           u1.name AS sender_name,
           u2.name AS recipient_name,
           b.title AS book_title,
           b.price AS book_price,
           o.status AS order_status,
           o.id AS order_id,
           'payment' AS record_type
    FROM payments p
    LEFT JOIN users u1 ON p.user_id = u1.user_id
    LEFT JOIN users u2 ON p.support_to_user_id = u2.user_id
    LEFT JOIN books b ON p.book_id = b.book_id
    LEFT JOIN orders o ON p.order_id = o.id
    ORDER BY p.created_at DESC
");
$stmt->execute();
$allPayments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get all orders (including those without payments yet)
$stmtOrders = $pdo->prepare("
    SELECT o.*, 
           u.name AS customer_name,
           GROUP_CONCAT(b.title SEPARATOR ', ') AS book_titles,
           SUM(s.total_price) AS order_total,
           'order' AS record_type
    FROM orders o
    LEFT JOIN users u ON o.user_id = u.user_id
    LEFT JOIN sales s ON o.id = s.order_id
    LEFT JOIN books b ON s.product_id = b.book_id
    GROUP BY o.id
    ORDER BY o.created_at DESC
");
$stmtOrders->execute();
$allOrders = $stmtOrders->fetchAll(PDO::FETCH_ASSOC);

// Combine for display (optional)
$activeTab = isset($_GET['tab']) ? $_GET['tab'] : 'payments';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Payments & Orders Report</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root {
            --green: #22c55e;
            --red: #ef4444;
            --yellow: #f59e0b;
            --blue: #3b82f6;
            --purple: #8b5cf6;
            --gray: #64748b;
        }
        body {
            font-family: 'Inter', sans-serif;
            background: #f1f5f9;
            color: #1e293b;
            margin: 0;
            padding: 0;
        }
        .container { max-width: 1400px; margin: 2rem auto; padding: 0 1rem; }
        
        .header {
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            color: white;
            padding: 3rem 2rem;
            border-radius: 20px;
            text-align: center;
            margin-bottom: 2rem;
        }
        .header h1 { margin: 0; font-size: 2.5rem; font-weight: 800; }
        .header p { font-size: 1.1rem; opacity: 0.9; margin: 0.5rem 0 0; }
        
        .tabs {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
            background: white;
            padding: 0.5rem;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }
        .tab {
            flex: 1;
            padding: 1rem;
            text-align: center;
            border-radius: 8px;
            text-decoration: none;
            color: #64748b;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .tab.active {
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            color: white;
        }
        .tab i { margin-right: 0.5rem; }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            text-align: center;
            transition: transform 0.3s ease;
        }
        .stat-card:hover { transform: translateY(-5px); }
        .stat-card i { font-size: 2rem; color: var(--blue); margin-bottom: 0.5rem; }
        .stat-card h3 { margin: 0.5rem 0; font-size: 1.8rem; color: #1e293b; }
        .stat-card p { color: var(--gray); margin: 0; font-size: 0.9rem; }

        .section-title {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin: 2rem 0 1.5rem;
        }
        .section-title h2 {
            font-size: 1.8rem;
            font-weight: 700;
            color: #1e293b;
        }
        .section-title i {
            font-size: 2rem;
            color: var(--blue);
        }

        table {
            width: 100%;
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            border-collapse: collapse;
        }
        th {
            background: #f8fafc;
            padding: 1.2rem 1rem;
            text-align: left;
            font-weight: 700;
            color: #334155;
            border-bottom: 3px solid #e2e8f0;
        }
        td {
            padding: 1rem;
            border-bottom: 1px solid #e2e8f0;
        }
        tr:hover { background: #f8fafc; }

        .status {
            padding: 0.4rem 0.8rem;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.85rem;
            display: inline-block;
        }
        .pending   { background: #fffbeb; color: #d97706; border: 1px solid #fde68a; }
        .completed,
        .paid      { background: #ecfdf5; color: #166534; border: 1px solid #a7f3d0; }
        .failed    { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }
        .cancelled { background: #f3f4f6; color: #4b5563; border: 1px solid #d1d5db; }
        .processing { background: #dbeafe; color: #1e40af; border: 1px solid #bfdbfe; }
        .shipped   { background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; }
        .delivered { background: #ecfdf5; color: #166534; border: 1px solid #bbf7d0; }

        .amount-display {
            font-weight: 700;
            color: #166534;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .actions {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }
        .actions a, .actions button {
            padding: 0.5rem 0.8rem;
            border-radius: 8px;
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
        }
        .btn-complete { background: #d1fae5; color: #166534; }
        .btn-complete:hover { background: #a7f3d0; }
        .btn-fail     { background: #fee2e2; color: #991b1b; }
        .btn-fail:hover { background: #fecaca; }
        .btn-processing { background: #dbeafe; color: #1e40af; }
        .btn-processing:hover { background: #bfdbfe; }
        .btn-shipped { background: #e0f2fe; color: #0369a1; }
        .btn-shipped:hover { background: #bae6fd; }
        .btn-delete   { background: #fee2e2; color: #dc2626; }
        .btn-delete:hover { background: #fecaca; }

        .success {
            background: #d1fae5;
            color: #166534;
            padding: 1rem 1.5rem;
            border-radius: 12px;
            border: 1px solid #a7f3d0;
            margin-bottom: 1.5rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        .success i { font-size: 1.2rem; }

        .empty-state {
            text-align: center;
            padding: 4rem;
            color: #64748b;
        }
        .empty-state i { font-size: 4rem; color: #e2e8f0; margin-bottom: 1.5rem; }
        .empty-state h3 { color: #475569; margin-bottom: 0.5rem; }

        .type-badge {
            padding: 0.3rem 0.6rem;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
            background: #e2e8f0;
            color: #475569;
        }
        .type-support {
            background: #dcfce7;
            color: #166534;
        }
        .type-order {
            background: #dbeafe;
            color: #1e40af;
        }

        @media (max-width: 768px) {
            table, thead, tbody, th, td, tr { display: block; }
            thead tr { display: none; }
            tr { margin-bottom: 1.5rem; border: 1px solid #e2e8f0; border-radius: 12px; }
            td {
                text-align: right;
                padding-left: 50%;
                position: relative;
            }
            td::before {
                content: attr(data-label);
                position: absolute;
                left: 1rem;
                font-weight: 700;
                color: #475569;
            }
            .actions {
                justify-content: flex-end;
            }
        }
    </style>
</head>
<body>

<?php include '../../includes/header.php'; ?>

<div class="container">

    <div class="header">
        <h1>Admin - Payments & Orders Management</h1>
        <p>Manage M-Pesa payments, support tips, and order statuses</p>
    </div>

    <div class="tabs">
        <a href="?tab=payments" class="tab <?php echo $activeTab == 'payments' ? 'active' : ''; ?>">
            <i class="fas fa-hand-holding-heart"></i> Payments & Support
        </a>
        <a href="?tab=orders" class="tab <?php echo $activeTab == 'orders' ? 'active' : ''; ?>">
            <i class="fas fa-shopping-cart"></i> Orders Management
        </a>
    </div>

    <?php if (isset($success)): ?>
        <div class="success">
            <i class="fas fa-check-circle"></i>
            <?= $success ?>
        </div>
    <?php endif; ?>

    <?php if ($activeTab == 'payments'): ?>
        <!-- PAYMENTS SECTION -->
        <div class="stats-grid">
            <div class="stat-card">
                <i class="fas fa-heart"></i>
                <h3><?= number_format($totalPayments) ?></h3>
                <p>Total Payments</p>
            </div>
            <div class="stat-card">
                <i class="fas fa-money-bill-wave"></i>
                <h3>KSh <?= number_format($totalAmount, 2) ?></h3>
                <p>Total Amount</p>
            </div>
            <div class="stat-card">
                <i class="fas fa-clock"></i>
                <h3><?= $pendingPayments ?></h3>
                <p>Pending</p>
            </div>
            <div class="stat-card">
                <i class="fas fa-check-circle"></i>
                <h3><?= $completedPayments ?></h3>
                <p>Completed</p>
            </div>
        </div>

        <?php if (!empty($allPayments)): ?>
        <table>
            <thead>
                <tr>
                    <th>Date & Time</th>
                    <th>Type</th>
                    <th>From User</th>
                    <th>To</th>
                    <th>Book</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Receipt</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($allPayments as $payment): ?>
                <tr>
                    <td data-label="Date">
                        <?= date('d M Y', strtotime($payment['created_at'])) ?><br>
                        <small style="color:#64748b"><?= date('g:i A', strtotime($payment['created_at'])) ?></small>
                    </td>
                    <td data-label="Type">
                        <?php if ($payment['order_id']): ?>
                            <span class="type-badge type-order"><i class="fas fa-shopping-cart"></i> Order Payment</span>
                        <?php else: ?>
                            <span class="type-badge type-support"><i class="fas fa-heart"></i> Support Tip</span>
                        <?php endif; ?>
                        <?php if ($payment['order_id']): ?>
                            <br><small>Order #<?= $payment['order_id'] ?></small>
                        <?php endif; ?>
                    </td>
                    <td data-label="From">
                        <strong><?= htmlspecialchars($payment['sender_name'] ?? 'Unknown') ?></strong><br>
                        <small>ID: <?= $payment['user_id'] ?></small>
                    </td>
                    <td data-label="To">
                        <?php if ($payment['recipient_name']): ?>
                            <strong><?= htmlspecialchars($payment['recipient_name']) ?></strong><br>
                            <small>ID: <?= $payment['support_to_user_id'] ?></small>
                        <?php elseif ($payment['order_id']): ?>
                            <em>Order Payment</em>
                        <?php else: ?>
                            <em>—</em>
                        <?php endif; ?>
                    </td>
                    <td data-label="Book">
                        <?php if ($payment['book_title']): ?>
                            <strong><?= htmlspecialchars(substr($payment['book_title'], 0, 25)) ?><?= strlen($payment['book_title']) > 25 ? '...' : '' ?></strong>
                        <?php else: ?>
                            <em>General</em>
                        <?php endif; ?>
                    </td>
                    <td data-label="Amount">
                        <div class="amount-display">
                            <i class="fas fa-money-bill"></i>
                            KSh <?= number_format($payment['amount'], 2) ?>
                        </div>
                    </td>
                    <td data-label="Status">
                        <span class="status <?= $payment['payment_status'] ?>">
                            <?= ucfirst($payment['payment_status']) ?>
                        </span>
                    </td>
                    <td data-label="Receipt">
                        <?php if ($payment['mpesa_receipt_number']): ?>
                            <code><?= htmlspecialchars($payment['mpesa_receipt_number']) ?></code>
                        <?php else: ?>
                            <em>—</em>
                        <?php endif; ?>
                    </td>
                    <td data-label="Actions" class="actions">
                        <?php if ($payment['payment_status'] !== 'completed'): ?>
                            <a href="?set_payment_status=completed&id=<?= $payment['id'] ?>&tab=payments" class="btn-complete" title="Mark as Completed">
                                <i class="fas fa-check"></i>
                            </a>
                        <?php endif; ?>
                        <?php if ($payment['payment_status'] !== 'failed'): ?>
                            <a href="?set_payment_status=failed&id=<?= $payment['id'] ?>&tab=payments" class="btn-fail" title="Mark as Failed">
                                <i class="fas fa-times"></i>
                            </a>
                        <?php endif; ?>
                        <a href="?delete=<?= $payment['id'] ?>&tab=payments" 
                           onclick="return confirm('Are you sure you want to delete this payment record permanently? This action cannot be undone.')"
                           class="btn-delete" title="Delete">
                            <i class="fas fa-trash"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-heart"></i>
                <h3>No payments yet</h3>
                <p>No payment records found in the system.</p>
            </div>
        <?php endif; ?>

    <?php else: ?>
        <!-- ORDERS SECTION -->
        <div class="stats-grid">
            <div class="stat-card">
                <i class="fas fa-shopping-bag"></i>
                <h3><?= number_format($totalOrders) ?></h3>
                <p>Total Orders</p>
            </div>
            <div class="stat-card">
                <i class="fas fa-clock"></i>
                <h3><?= $pendingOrders ?></h3>
                <p>Pending Orders</p>
            </div>
            <div class="stat-card">
                <i class="fas fa-check-circle"></i>
                <h3><?= $paidOrders ?></h3>
                <p>Paid Orders</p>
            </div>
        </div>

        <?php if (!empty($allOrders)): ?>
        <table>
            <thead>
                <tr>
                    <th>Order #</th>
                    <th>Date</th>
                    <th>Customer</th>
                    <th>Books</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($allOrders as $order): ?>
                <tr>
                    <td data-label="Order #">
                        <strong>#<?= $order['id'] ?></strong>
                    </td>
                    <td data-label="Date">
                        <?= date('d M Y', strtotime($order['created_at'])) ?><br>
                        <small><?= date('g:i A', strtotime($order['created_at'])) ?></small>
                    </td>
                    <td data-label="Customer">
                        <strong><?= htmlspecialchars($order['customer_name'] ?? 'Unknown') ?></strong><br>
                        <small>ID: <?= $order['user_id'] ?></small>
                    </td>
                    <td data-label="Books">
                        <small><?= htmlspecialchars(substr($order['book_titles'] ?? 'No books', 0, 50)) ?><?= strlen($order['book_titles'] ?? '') > 50 ? '...' : '' ?></small>
                    </td>
                    <td data-label="Total">
                        <div class="amount-display">
                            <i class="fas fa-money-bill"></i>
                            KSh <?= number_format($order['order_total'] ?? $order['total_amount'], 2) ?>
                        </div>
                    </td>
                    <td data-label="Status">
                        <span class="status <?= $order['status'] ?>">
                            <?= ucfirst($order['status']) ?>
                        </span>
                    </td>
                    <td data-label="Actions" class="actions">
                        <select onchange="if(this.value) window.location.href='?set_order_status='+this.value+'&order_id=<?= $order['id'] ?>&tab=orders'" style="padding: 0.4rem; border-radius: 6px; border: 1px solid #d1d5db;">
                            <option value="">Change Status</option>
                            <option value="pending">Pending</option>
                            <option value="paid">Paid</option>
                            <option value="processing">Processing</option>
                            <option value="shipped">Shipped</option>
                            <option value="delivered">Delivered</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                        
                        <?php if ($order['status'] == 'pending'): ?>
                            <a href="?set_order_status=paid&order_id=<?= $order['id'] ?>&tab=orders" class="btn-complete" title="Mark as Paid">
                                <i class="fas fa-check"></i> Paid
                            </a>
                        <?php endif; ?>
                        
                        <?php if ($order['status'] == 'paid'): ?>
                            <a href="?set_order_status=processing&order_id=<?= $order['id'] ?>&tab=orders" class="btn-processing" title="Start Processing">
                                <i class="fas fa-cog"></i>
                            </a>
                        <?php endif; ?>
                        
                        <?php if ($order['status'] == 'processing'): ?>
                            <a href="?set_order_status=shipped&order_id=<?= $order['id'] ?>&tab=orders" class="btn-shipped" title="Mark as Shipped">
                                <i class="fas fa-truck"></i>
                            </a>
                        <?php endif; ?>
                        
                        <?php if ($order['status'] == 'shipped'): ?>
                            <a href="?set_order_status=delivered&order_id=<?= $order['id'] ?>&tab=orders" class="btn-complete" title="Mark as Delivered">
                                <i class="fas fa-check-circle"></i>
                            </a>
                        <?php endif; ?>
                        
                        <?php if ($order['status'] != 'cancelled' && $order['status'] != 'delivered'): ?>
                            <a href="?set_order_status=cancelled&order_id=<?= $order['id'] ?>&tab=orders" class="btn-fail" title="Cancel Order" onclick="return confirm('Cancel this order?')">
                                <i class="fas fa-ban"></i>
                            </a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-shopping-bag"></i>
                <h3>No orders yet</h3>
                <p>No orders have been placed yet.</p>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?php include '../../includes/footer.php'; ?>
</body>
</html>