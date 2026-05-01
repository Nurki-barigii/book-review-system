<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit();
}

$userId = $_SESSION['user_id'];

// Get ALL payments made by this user (both support tips and order payments)
$stmt = $pdo->prepare("
    SELECT p.*,
           u.name AS recipient_name,
           b.title AS book_title,
           o.id AS order_id,
           o.status AS order_status
    FROM payments p
    LEFT JOIN users u ON p.support_to_user_id = u.user_id
    LEFT JOIN books b ON p.book_id = b.book_id
    LEFT JOIN orders o ON p.order_id = o.id
    WHERE p.user_id = ?
    ORDER BY p.created_at DESC
");
$stmt->execute([$userId]);
$payments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate totals
$totalAmount = 0;
$completedAmount = 0;
$pendingCount = 0;
$completedCount = 0;

foreach ($payments as $payment) {
    $totalAmount += $payment['amount'];
    if ($payment['payment_status'] == 'completed') {
        $completedAmount += $payment['amount'];
        $completedCount++;
    } elseif ($payment['payment_status'] == 'pending') {
        $pendingCount++;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Payments & Support History</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #f8fafc;
            color: #1e293b;
            line-height: 1.6;
        }

        .main-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1.5rem;
        }

        /* Header Section */
        .page-header {
            background: linear-gradient(135deg, #22c55e, #16a34a);
            color: white;
            padding: 2.5rem;
            border-radius: 24px;
            margin-bottom: 2rem;
            box-shadow: 0 20px 40px rgba(34, 197, 94, 0.2);
        }

        .page-header h1 {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .page-header p {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        /* Stats Cards */
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
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            text-align: center;
            border: 1px solid #e2e8f0;
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            background: #ecfdf5;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            color: #22c55e;
            font-size: 1.5rem;
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 800;
            color: #166534;
            margin-bottom: 0.25rem;
        }

        .stat-label {
            color: #64748b;
            font-size: 0.9rem;
            font-weight: 500;
        }

        /* Filters */
        .filters {
            background: white;
            padding: 1.5rem;
            border-radius: 16px;
            margin-bottom: 2rem;
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            align-items: center;
            border: 1px solid #e2e8f0;
        }

        .filter-select {
            padding: 0.75rem 1.5rem;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-family: 'Inter', sans-serif;
            font-weight: 500;
            color: #1e293b;
            background: white;
            cursor: pointer;
            min-width: 200px;
        }

        .filter-select:focus {
            outline: none;
            border-color: #22c55e;
        }

        .search-box {
            flex: 1;
            position: relative;
        }

        .search-box input {
            width: 100%;
            padding: 0.75rem 1rem 0.75rem 3rem;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-family: 'Inter', sans-serif;
        }

        .search-box i {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
        }

        /* Table Styles */
        .table-container {
            background: white;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            border: 1px solid #e2e8f0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: #f8fafc;
            padding: 1.2rem 1rem;
            text-align: left;
            font-weight: 700;
            color: #334155;
            border-bottom: 3px solid #e2e8f0;
            font-size: 0.95rem;
        }

        td {
            padding: 1.2rem 1rem;
            border-bottom: 1px solid #e2e8f0;
            color: #475569;
        }

        tr:hover {
            background: #f8fafc;
        }

        tr:last-child td {
            border-bottom: none;
        }

        /* Payment Type Badge */
        .type-badge {
            display: inline-block;
            padding: 0.4rem 0.8rem;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 0.25rem;
        }

        .type-support {
            background: #dcfce7;
            color: #166534;
        }

        .type-order {
            background: #dbeafe;
            color: #1e40af;
        }

        .type-badge i {
            margin-right: 0.3rem;
        }

        /* Amount */
        .amount {
            font-weight: 700;
            color: #166534;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .amount i {
            color: #22c55e;
            font-size: 1rem;
        }

        /* Status Badge */
        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
            display: inline-block;
        }

        .status-pending {
            background: #fef3c7;
            color: #92400e;
            border: 1px solid #fde68a;
        }

        .status-completed {
            background: #d1fae5;
            color: #166534;
            border: 1px solid #a7f3d0;
        }

        .status-failed {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .status-cancelled {
            background: #f1f5f9;
            color: #475569;
            border: 1px solid #cbd5e1;
        }

        /* Receipt */
        .receipt {
            font-family: monospace;
            background: #f1f5f9;
            padding: 0.4rem 0.8rem;
            border-radius: 8px;
            font-size: 0.9rem;
            color: #334155;
            display: inline-block;
        }

        /* Reference */
        .reference {
            font-size: 0.85rem;
            color: #64748b;
            background: #f8fafc;
            padding: 0.3rem 0.6rem;
            border-radius: 6px;
            font-family: monospace;
        }

        /* Order Link */
        .order-link {
            color: #3b82f6;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .order-link:hover {
            text-decoration: underline;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: #64748b;
        }

        .empty-state i {
            font-size: 5rem;
            color: #cbd5e1;
            margin-bottom: 1.5rem;
        }

        .empty-state h3 {
            color: #334155;
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }

        .empty-state p {
            margin-bottom: 2rem;
        }

        .empty-state .btn {
            background: #22c55e;
            color: white;
            padding: 1rem 2rem;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            display: inline-block;
            transition: all 0.3s ease;
        }

        .empty-state .btn:hover {
            background: #16a34a;
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(34, 197, 94, 0.3);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .page-header h1 {
                font-size: 2rem;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .filters {
                flex-direction: column;
            }

            .filter-select, .search-box {
                width: 100%;
            }

            table, thead, tbody, th, td, tr {
                display: block;
            }

            thead tr {
                display: none;
            }

            tr {
                margin-bottom: 1rem;
                border: 1px solid #e2e8f0;
                border-radius: 12px;
                padding: 1rem;
            }

            td {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 0.75rem 0;
                border-bottom: 1px dashed #e2e8f0;
            }

            td:last-child {
                border-bottom: none;
            }

            td::before {
                content: attr(data-label);
                font-weight: 600;
                color: #475569;
                margin-right: 1rem;
            }

            .type-badge {
                margin: 0;
            }
        }
    </style>
</head>
<body>

<?php include '../includes/header.php'; ?>

<div class="main-container">
    <!-- Page Header -->
    <div class="page-header">
        <h1>
            <i class="fas fa-hand-holding-heart"></i>
            My Payments & Support History
        </h1>
        <p>Track all your M-Pesa transactions, support tips, and order payments</p>
    </div>

    <!-- Stats Cards -->
    <?php if (count($payments) > 0): ?>
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-hand-holding-heart"></i>
            </div>
            <div class="stat-value"><?= count($payments) ?></div>
            <div class="stat-label">Total Transactions</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-money-bill-wave"></i>
            </div>
            <div class="stat-value">KSh <?= number_format($totalAmount, 0) ?></div>
            <div class="stat-label">Total Amount</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-value"><?= $completedCount ?></div>
            <div class="stat-label">Completed</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-value"><?= $pendingCount ?></div>
            <div class="stat-label">Pending</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-trophy"></i>
            </div>
            <div class="stat-value">KSh <?= number_format($completedAmount, 0) ?></div>
            <div class="stat-label">Successfully Sent</div>
        </div>
    </div>

    <!-- Filters -->
    <div class="filters">
        <select class="filter-select" id="statusFilter">
            <option value="all">All Statuses</option>
            <option value="completed">Completed</option>
            <option value="pending">Pending</option>
            <option value="failed">Failed</option>
            <option value="cancelled">Cancelled</option>
        </select>

        <select class="filter-select" id="typeFilter">
            <option value="all">All Types</option>
            <option value="support">Support Tips Only</option>
            <option value="order">Order Payments Only</option>
        </select>

        <div class="search-box">
            <i class="fas fa-search"></i>
            <input type="text" id="searchInput" placeholder="Search by book, recipient, or reference...">
        </div>
    </div>
    <?php endif; ?>

    <!-- Payments Table -->
    <div class="table-container">
        <?php if (count($payments) > 0): ?>
            <table id="paymentsTable">
                <thead>
                    <tr>
                        <th>Date & Time</th>
                        <th>Type</th>
                        <th>Amount</th>
                        <th>Recipient / Order</th>
                        <th>Book</th>
                        <th>Status</th>
                        <th>Receipt</th>
                        <th>Reference</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($payments as $payment): ?>
                    <tr class="payment-row" 
                        data-status="<?= $payment['payment_status'] ?>"
                        data-type="<?= $payment['order_id'] ? 'order' : 'support' ?>"
                        data-search="<?= strtolower($payment['recipient_name'] ?? '' . $payment['book_title'] ?? '' . $payment['account_reference']) ?>">
                        
                        <td data-label="Date">
                            <?= date('d M Y', strtotime($payment['created_at'])) ?><br>
                            <small style="color:#64748b;"><?= date('g:i A', strtotime($payment['created_at'])) ?></small>
                        </td>
                        
                        <td data-label="Type">
                            <?php if ($payment['order_id']): ?>
                                <span class="type-badge type-order">
                                    <i class="fas fa-shopping-cart"></i> Order Payment
                                </span>
                                <br>
                                <small>
                                    <a href="../orders/my_orders.php#order-<?= $payment['order_id'] ?>" class="order-link">
                                        Order #<?= $payment['order_id'] ?>
                                    </a>
                                </small>
                            <?php else: ?>
                                <span class="type-badge type-support">
                                    <i class="fas fa-heart"></i> Support Tip
                                </span>
                            <?php endif; ?>
                        </td>
                        
                        <td data-label="Amount" class="amount">
                            <i class="fas fa-money-bill"></i>
                            KSh <?= number_format($payment['amount'], 2) ?>
                        </td>
                        
                        <td data-label="Recipient">
                            <?php if ($payment['order_id']): ?>
                                <strong>Order Payment</strong>
                            <?php elseif ($payment['recipient_name']): ?>
                                <strong><?= htmlspecialchars($payment['recipient_name']) ?></strong>
                                <br>
                                <small>Reviewer</small>
                            <?php else: ?>
                                <em>—</em>
                            <?php endif; ?>
                        </td>
                        
                        <td data-label="Book">
                            <?php if ($payment['book_title']): ?>
                                <strong><?= htmlspecialchars(substr($payment['book_title'], 0, 30)) ?></strong>
                                <?= strlen($payment['book_title']) > 30 ? '...' : '' ?>
                            <?php else: ?>
                                <em>—</em>
                            <?php endif; ?>
                        </td>
                        
                        <td data-label="Status">
                            <span class="status-badge status-<?= $payment['payment_status'] ?>">
                                <?= ucfirst($payment['payment_status']) ?>
                            </span>
                        </td>
                        
                        <td data-label="Receipt">
                            <?php if ($payment['mpesa_receipt_number']): ?>
                                <span class="receipt">
                                    <i class="fas fa-receipt"></i>
                                    <?= htmlspecialchars($payment['mpesa_receipt_number']) ?>
                                </span>
                            <?php else: ?>
                                <em style="color:#94a3b8;">Not processed</em>
                            <?php endif; ?>
                        </td>
                        
                        <td data-label="Reference">
                            <span class="reference">
                                <?= htmlspecialchars($payment['account_reference']) ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-hand-holding-heart"></i>
                <h3>No Transactions Yet</h3>
                <p>You haven't made any payments or sent any support tips yet.</p>
                <a href="../books/list.php" class="btn">
                    <i class="fas fa-book-open"></i>
                    Browse Books
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Filtering Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const statusFilter = document.getElementById('statusFilter');
    const typeFilter = document.getElementById('typeFilter');
    const searchInput = document.getElementById('searchInput');
    const rows = document.querySelectorAll('.payment-row');

    function filterTable() {
        const statusValue = statusFilter?.value || 'all';
        const typeValue = typeFilter?.value || 'all';
        const searchValue = searchInput?.value.toLowerCase() || '';

        rows.forEach(row => {
            const status = row.dataset.status;
            const type = row.dataset.type;
            const searchText = row.dataset.search || '';

            let statusMatch = statusValue === 'all' || status === statusValue;
            let typeMatch = typeValue === 'all' || type === typeValue;
            let searchMatch = searchValue === '' || searchText.includes(searchValue);

            if (statusMatch && typeMatch && searchMatch) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    if (statusFilter) {
        statusFilter.addEventListener('change', filterTable);
    }
    if (typeFilter) {
        typeFilter.addEventListener('change', filterTable);
    }
    if (searchInput) {
        searchInput.addEventListener('input', filterTable);
    }
});
</script>

<?php include '../includes/footer.php'; ?>
</body>
</html>