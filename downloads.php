<?php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: auth/login.php');
    exit();
}

$order_id = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;
$book_id = isset($_GET['book_id']) ? (int)$_GET['book_id'] : 0;

if (!$order_id || !$book_id) {
    die("❌ Invalid download request - Missing order_id or book_id");
}

// Verify this is a paid digital book for this user and get PDF file path
$stmt = $pdo->prepare("
    SELECT 
        o.id as order_id, 
        o.created_at,
        o.status as order_status,
        b.title, 
        b.author, 
        b.book_id,
        b.pdf_file,
        b.has_digital,
        s.quantity_sold, 
        s.total_price,
        u.name as user_name,
        u.email as user_email,
        p.payment_status
    FROM orders o
    JOIN sales s ON o.id = s.order_id
    JOIN books b ON s.product_id = b.book_id
    JOIN users u ON o.user_id = u.user_id
    LEFT JOIN payments p ON p.order_id = o.id
    WHERE o.id = ? 
    AND o.user_id = ?
    AND (o.status = 'paid' OR p.payment_status = 'completed')
");
$stmt->execute([$order_id, $_SESSION['user_id']]);
$order = $stmt->fetch();

if (!$order) {
    die("❌ Book not found or payment not completed. Order ID: $order_id, User ID: " . $_SESSION['user_id']);
}

// Check if PDF file exists in database
if (empty($order['pdf_file'])) {
    die("❌ PDF file not available for this book. Please contact support.");
}

// Construct the FULL path correctly
$pdf_path = __DIR__ . '/' . $order['pdf_file']; // This gives: C:\xampp\htdocs\book_review/uploads/books/filename.pdf

// Alternative if above doesn't work, use this:
// $pdf_path = 'uploads/books/' . basename($order['pdf_file']);

if (!file_exists($pdf_path)) {
    // Try alternative path
    $pdf_path = 'uploads/books/' . basename($order['pdf_file']);
    
    if (!file_exists($pdf_path)) {
        die("❌ PDF file not found on server. Path tried: " . $pdf_path);
    }
}

// Log the download
try {
    // Create download_logs table if it doesn't exist
    $pdo->exec("CREATE TABLE IF NOT EXISTS download_logs (
        id INT AUTO_INCREMENT PRIMARY KEY,
        order_id INT NOT NULL,
        user_id INT NOT NULL,
        ip_address VARCHAR(45),
        downloaded_at DATETIME NOT NULL
    )");
    
    $stmt = $pdo->prepare("INSERT INTO download_logs (order_id, user_id, ip_address, downloaded_at) VALUES (?, ?, ?, NOW())");
    $stmt->execute([$order_id, $_SESSION['user_id'], $_SERVER['REMOTE_ADDR']]);
} catch (Exception $e) {
    // Log error but continue with download
    error_log("Download logging failed: " . $e->getMessage());
}

// Get file info
$file_size = filesize($pdf_path);
$file_name = basename($pdf_path);
$book_title = $order['title'];

// Clean filename for download
$download_name = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $book_title) . '.pdf';

// Set headers for PDF download
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="' . $download_name . '"');
header('Content-Length: ' . $file_size);
header('Cache-Control: private, max-age=0, must-revalidate');
header('Pragma: public');
header('Expires: 0');

// Clear output buffer
ob_clean();
flush();

// Read file and output to browser
readfile($pdf_path);
exit();
?>