<?php
session_start();
require_once '../config/db.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Add PHPMailer - SIMPLE PATH LIKE THE WORKING EXAMPLE
require_once '../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Email Configuration - Using constants like the working example
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_USERNAME', 'siremmanuel1999@gmail.com');
define('SMTP_PASSWORD', 'vpnc kxah syyn eosz'); // Your app password without spaces
define('SMTP_PORT', 587);

// Function to send payment confirmation email
function sendPaymentConfirmationEmail($userEmail, $userName, $paymentType, $amount, $bookTitle, $recipientName, $orderId = null) {
    $mail = new PHPMailer(true);
    
    try {
        // SMTP Configuration (like the working example)
        $mail->isSMTP();
        $mail->Host = SMTP_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = SMTP_USERNAME;
        $mail->Password = SMTP_PASSWORD;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = SMTP_PORT;
        $mail->Timeout = 30;

        // Email setup
        $mail->setFrom(SMTP_USERNAME, 'BookReview App');
        $mail->addReplyTo(SMTP_USERNAME, 'BookReview Support');
        $mail->addAddress($userEmail, $userName);
        
        // Content
        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';
        
        if ($paymentType == 'order') {
            $mail->Subject = "Order Confirmation #$orderId - BookReview";
            $mail->Body = "
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                    .header { background: linear-gradient(135deg, #667eea, #764ba2); color: white; padding: 25px; text-align: center; border-radius: 10px 10px 0 0; }
                    .content { padding: 25px; background: #f8f9fa; }
                    .order-details { background: white; padding: 20px; border-radius: 8px; margin: 20px 0; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
                    .amount { font-size: 24px; color: #667eea; font-weight: bold; }
                    .footer { background: #343a40; color: white; padding: 20px; text-align: center; border-radius: 0 0 10px 10px; }
                </style>
            </head>
            <body>
                <div style='max-width: 600px; margin: 0 auto;'>
                    <div class='header'>
                        <h2>Order Confirmation</h2>
                    </div>
                    <div class='content'>
                        <h3>Thank you for your order, $userName!</h3>
                        <div class='order-details'>
                            <p><strong>Order #:</strong> $orderId</p>
                            <p><strong>Book:</strong> $bookTitle</p>
                            <p><strong>Amount:</strong> <span class='amount'>KSh " . number_format($amount) . "</span></p>
                        </div>
                        <p>Your payment request has been sent to your M-Pesa. Please complete the payment with your PIN.</p>
                        <p>You will receive a confirmation once the payment is processed.</p>
                        <p>Happy Reading!</p>
                    </div>
                    <div class='footer'>
                        <p style='margin: 0;'><strong>BookReview App</strong><br>Email: " . SMTP_USERNAME . "</p>
                    </div>
                </div>
            </body>
            </html>
            ";
        } else {
            $mail->Subject = "Support Payment Confirmation - BookReview";
            $mail->Body = "
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                    .header { background: linear-gradient(135deg, #22c55e, #16a34a); color: white; padding: 25px; text-align: center; border-radius: 10px 10px 0 0; }
                    .content { padding: 25px; background: #f8f9fa; }
                    .support-details { background: white; padding: 20px; border-radius: 8px; margin: 20px 0; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
                    .amount { font-size: 24px; color: #22c55e; font-weight: bold; }
                    .footer { background: #343a40; color: white; padding: 20px; text-align: center; border-radius: 0 0 10px 10px; }
                </style>
            </head>
            <body>
                <div style='max-width: 600px; margin: 0 auto;'>
                    <div class='header'>
                        <h2>Support Payment Confirmation</h2>
                    </div>
                    <div class='content'>
                        <h3>Thank you for your support, $userName!</h3>
                        <div class='support-details'>
                            <p><strong>To:</strong> $recipientName</p>
                            <p><strong>Book:</strong> $bookTitle</p>
                            <p><strong>Amount:</strong> <span class='amount'>KSh " . number_format($amount) . "</span></p>
                        </div>
                        <p>Your support payment request has been sent to your M-Pesa. Please complete the payment with your PIN.</p>
                        <p>Your support means a lot to the reviewer community!</p>
                        <p>Happy Reading!</p>
                    </div>
                    <div class='footer'>
                        <p style='margin: 0;'><strong>BookReview App</strong><br>Email: " . SMTP_USERNAME . "</p>
                    </div>
                </div>
            </body>
            </html>
            ";
        }
        
        $mail->AltBody = strip_tags($mail->Body);
        
        $mail->send();
        error_log("Email sent successfully to $userEmail");
        return ['success' => true, 'message' => 'Email sent successfully'];
    } catch (Exception $e) {
        error_log("Email sending failed: " . $mail->ErrorInfo);
        return ['success' => false, 'message' => $mail->ErrorInfo];
    }
}

// Function to send welcome email for new registrations
function sendWelcomeEmail($userEmail, $userName) {
    $mail = new PHPMailer(true);
    
    try {
        // SMTP Configuration (like the working example)
        $mail->isSMTP();
        $mail->Host = SMTP_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = SMTP_USERNAME;
        $mail->Password = SMTP_PASSWORD;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = SMTP_PORT;
        $mail->Timeout = 30;

        // Email setup
        $mail->setFrom(SMTP_USERNAME, 'BookReview App');
        $mail->addReplyTo(SMTP_USERNAME, 'BookReview Support');
        $mail->addAddress($userEmail, $userName);
        
        // Content
        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';
        $mail->Subject = 'Welcome to BookReview Community!';
        $mail->Body = "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .header { background: linear-gradient(135deg, #667eea, #764ba2); color: white; padding: 25px; text-align: center; border-radius: 10px 10px 0 0; }
                .content { padding: 25px; background: #f8f9fa; }
                .feature { background: white; padding: 20px; border-radius: 8px; margin: 20px 0; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
                .button { display: inline-block; padding: 12px 25px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; }
                .footer { background: #343a40; color: white; padding: 20px; text-align: center; border-radius: 0 0 10px 10px; }
            </style>
        </head>
        <body>
            <div style='max-width: 600px; margin: 0 auto;'>
                <div class='header'>
                    <h1>📚 Welcome to BookReview, $userName!</h1>
                </div>
                <div class='content'>
                    <h3>Hello $userName,</h3>
                    <p>Thank you for joining the BookReview community!</p>
                    
                    <div class='feature'>
                        <h4>✨ What you can do now:</h4>
                        <ul>
                            <li>📖 Browse thousands of books</li>
                            <li>✍️ Write and share reviews</li>
                            <li>💬 Connect with fellow readers</li>
                            <li>🏆 Earn badges and recognition</li>
                            <li>💝 Support reviewers you appreciate</li>
                        </ul>
                    </div>
                    
                    <div style='text-align: center;'>
                        <a href='http://localhost/book_review/books.php' class='button'>Start Exploring Books</a>
                    </div>
                    
                    <p style='margin-top: 30px;'>Happy Reading!<br><strong>The BookReview Team</strong></p>
                </div>
                <div class='footer'>
                    <p style='margin: 0;'>&copy; " . date('Y') . " BookReview. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>
        ";
        
        $mail->AltBody = strip_tags($mail->Body);
        
        $mail->send();
        error_log("Welcome email sent successfully to $userEmail");
        return ['success' => true, 'message' => 'Welcome email sent'];
    } catch (Exception $e) {
        error_log("Welcome email failed: " . $mail->ErrorInfo);
        return ['success' => false, 'message' => $mail->ErrorInfo];
    }
}

// Handle user registration if this is a registration request
if (isset($_POST['register'])) {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    $errors = [];
    
    if (empty($name)) $errors[] = "Name is required";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Valid email is required";
    if (strlen($password) < 6) $errors[] = "Password must be at least 6 characters";
    
    // Check if email exists
    $stmt = $pdo->prepare("SELECT user_id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) $errors[] = "Email already registered";
    
    if (empty($errors)) {
        try {
            $pdo->beginTransaction();
            
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            
            // YOUR ORIGINAL INSERT STATEMENT
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'user')");
            $result = $stmt->execute([$name, $email, $hashedPassword]);
            
            if (!$result) {
                throw new Exception("Failed to insert user");
            }
            
            $userId = $pdo->lastInsertId();
            
            // Send welcome email
            $emailResult = sendWelcomeEmail($email, $name);
            
            $pdo->commit();
            
            $_SESSION['user_id'] = $userId;
            $_SESSION['name'] = $name;
            $_SESSION['email'] = $email;
            $_SESSION['success_message'] = "Registration successful! " . 
                ($emailResult['success'] ? "Welcome email sent." : "Welcome to BookReview!");
            
            header('Location: ../index.php');
            exit();
            
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = "Registration failed: " . $e->getMessage();
            error_log("Registration error: " . $e->getMessage());
        }
    } else {
        $_SESSION['registration_errors'] = $errors;
        header('Location: ../auth/register.php');
        exit();
    }
}

// Original payment code continues here
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit();
}

// Determine payment type (support or order)
$paymentType = 'support'; // default
$supportAmount = 50; // default fallback
$supportToUserId = 0;
$bookId = 0;
$orderId = 0;
$recipientName = "the reviewer";
$senderName = $_SESSION['name'] ?? "User";
$bookTitle = "Book Review";

// Check if this is an order payment
if (isset($_GET['order_id']) && isset($_GET['book_id']) && isset($_GET['amount'])) {
    $paymentType = 'order';
    $orderId = (int)$_GET['order_id'];
    $bookId = (int)$_GET['book_id'];
    $supportAmount = (float)$_GET['amount'];
    
    // Get order and book details
    $stmt = $pdo->prepare("
        SELECT o.*, b.title, b.price 
        FROM orders o 
        JOIN sales s ON o.id = s.order_id 
        JOIN books b ON s.product_id = b.book_id 
        WHERE o.id = ? AND o.user_id = ?
    ");
    $stmt->execute([$orderId, $_SESSION['user_id']]);
    $order = $stmt->fetch();
    
    if ($order) {
        $bookTitle = $order['title'];
        $recipientName = "Order #$orderId";
    } else {
        header('Location: ../orders/my_orders.php');
        exit();
    }
} 
// This is a support payment
else if (isset($_GET['reviewer_id']) && isset($_GET['book_id'])) {
    $supportToUserId = (int)$_GET['reviewer_id'];
    $bookId = (int)$_GET['book_id'];

    // Get book details including price
    $stmt = $pdo->prepare("SELECT title, price FROM books WHERE book_id = ?");
    $stmt->execute([$bookId]);
    if ($book = $stmt->fetch()) {
        $bookTitle = $book['title'];
        $supportAmount = max(50, (int)$book['price']);
    }

    // Get recipient details
    $stmt = $pdo->prepare("SELECT name FROM users WHERE user_id = ?");
    $stmt->execute([$supportToUserId]);
    if ($user = $stmt->fetch()) {
        $recipientName = $user['name'];
    }
} else {
    header('Location: ../index.php');
    exit();
}

// Get sender's saved phone
$phone = '';
$stmt = $pdo->prepare("SELECT phone_number FROM users WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
if ($row = $stmt->fetch()) {
    $saved = preg_replace('/[^0-9]/', '', $row['phone_number'] ?? '');
    if (strlen($saved) === 9) $phone = '254' . $saved;
    elseif (strlen($saved) === 10 && $saved[0] === '0') $phone = '254' . substr($saved, 1);
    elseif (strlen($saved) === 12 && str_starts_with($saved, '254')) $phone = $saved;
}

// M-Pesa Config
$consumerKey       = 'vSTRQdZptcIErXIk9stUJ4wyv8VOejOYhqQC904Dq0pq1Dx8';
$consumerSecret    = 'SWvSF0GRzToKpv6gqWyXmTDgLABNemhiI3PAONAgROW4SA8pp5qKPIdcWuNlMTUg';
$BusinessShortCode = '174379';
$Passkey           = 'bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919';
$CallbackURL       = 'https://morning-forest-72309.herokuapp.com/callback_url.php';
$baseURL           = 'https://sandbox.safaricom.co.ke';

$success = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['register'])) {
    $phoneInput = preg_replace('/[^0-9]/', '', $_POST['phone']);
    if (!preg_match('/^2547[0-9]{8}$/', $phoneInput)) {
        $error = "Please enter a valid M-Pesa number starting with 2547";
    } else {
        try {
            $pdo->beginTransaction();

            // Generate account reference based on payment type
            if ($paymentType == 'order') {
                $accountRef = "ORDER" . $orderId . time();
            } else {
                $accountRef = "SUPPORT" . time() . rand(100,999);
            }
            
            // Insert into payments table based on payment type
            if ($paymentType == 'order') {
                $stmt = $pdo->prepare("INSERT INTO payments 
                    (user_id, order_id, amount, phone_number, account_reference, payment_status, book_id) 
                    VALUES (?, ?, ?, ?, ?, 'pending', ?)");
                $stmt->execute([$_SESSION['user_id'], $orderId, $supportAmount, $phoneInput, $accountRef, $bookId]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO payments 
                    (user_id, amount, phone_number, account_reference, payment_status, support_to_user_id, book_id) 
                    VALUES (?, ?, ?, ?, 'pending', ?, ?)");
                $stmt->execute([$_SESSION['user_id'], $supportAmount, $phoneInput, $accountRef, $supportToUserId, $bookId]);
            }
            
            $paymentId = $pdo->lastInsertId();

            // Get Access Token
            $credentials = base64_encode($consumerKey . ':' . $consumerSecret);
            $tokenResponse = @file_get_contents($baseURL.'/oauth/v1/generate?grant_type=client_credentials', false,
                stream_context_create(['http' => ['header' => "Authorization: Basic $credentials\r\n"]])
            );
            
            if (!$tokenResponse) {
                throw new Exception("Could not connect to M-Pesa. Please try again.");
            }
            
            $token = json_decode($tokenResponse)->access_token;

            // STK Push
            $timestamp = date('YmdHis');
            $password = base64_encode($BusinessShortCode . $Passkey . $timestamp);

            // Set transaction description based on payment type
            if ($paymentType == 'order') {
                $transactionDesc = "Payment for Order #$orderId - $bookTitle";
            } else {
                $transactionDesc = "Support to $recipientName for $bookTitle review";
            }

            $payload = json_encode([
                'BusinessShortCode' => $BusinessShortCode,
                'Password'          => $password,
                'Timestamp'         => $timestamp,
                'TransactionType'   => 'CustomerPayBillOnline',
                'Amount'            => $supportAmount,
                'PartyA'            => $phoneInput,
                'PartyB'            => $BusinessShortCode,
                'PhoneNumber'       => $phoneInput,
                'CallBackURL'       => $CallbackURL,
                'AccountReference'  => $accountRef,
                'TransactionDesc'   => $transactionDesc
            ]);

            $ch = curl_init($baseURL . '/mpesa/stkpush/v1/processrequest');
            curl_setopt_array($ch, [
                CURLOPT_POST           => true,
                CURLOPT_POSTFIELDS     => $payload,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER     => [
                    'Content-Type: application/json',
                    'Authorization: Bearer ' . $token
                ],
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_TIMEOUT        => 30
            ]);
            $response = curl_exec($ch);
            
            if (curl_error($ch)) {
                throw new Exception("CURL Error: " . curl_error($ch));
            }
            
            curl_close($ch);

            $result = json_decode($response, true);

            if (!isset($result['ResponseCode']) || $result['ResponseCode'] !== '0') {
                $errorMessage = $result['errorMessage'] ?? 'STK Push failed. Please try again.';
                if (isset($result['ResponseDescription'])) {
                    $errorMessage = $result['ResponseDescription'];
                }
                throw new Exception($errorMessage);
            }

            // Save M-Pesa IDs
            $stmt = $pdo->prepare("UPDATE payments SET merchant_request_id = ?, checkout_request_id = ? WHERE id = ?");
            $stmt->execute([$result['MerchantRequestID'], $result['CheckoutRequestID'], $paymentId]);

            // Send payment confirmation email
            $userEmail = $_SESSION['email'] ?? '';
            if ($userEmail) {
                $emailResult = sendPaymentConfirmationEmail(
                    $userEmail,
                    $senderName,
                    $paymentType,
                    $supportAmount,
                    $bookTitle,
                    $recipientName,
                    $paymentType == 'order' ? $orderId : null
                );
                
                if (!$emailResult['success']) {
                    error_log("Payment confirmation email failed: " . $emailResult['message']);
                }
            }

            $pdo->commit();
            $success = true;

            // Clear support prompt if this was a support payment
            if ($paymentType == 'support' && isset($_SESSION['show_support_prompt'])) {
                unset($_SESSION['show_support_prompt'], $_SESSION['support_reviewer_id'], $_SESSION['support_book_id']);
            }

        } catch (Exception $e) {
            $pdo->rollBack();
            $error = $e->getMessage();
            error_log("Payment error: " . $e->getMessage());
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $paymentType == 'order' ? 'Pay for Order' : 'Send Support'; ?> - BookReview</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { 
            font-family: 'Inter', sans-serif; 
            background: linear-gradient(135deg, <?php echo $paymentType == 'order' ? '#e0f2fe, #bae6fd' : '#f0fdf4, #dcfce7'; ?>); 
            padding: 2rem; 
            min-height: 100vh; 
            display: flex; 
            align-items: center;
            justify-content: center;
        }
        .card { 
            max-width: 480px; 
            width: 100%;
            background: white; 
            border-radius: 24px; 
            padding: 3rem; 
            box-shadow: 0 20px 50px <?php echo $paymentType == 'order' ? 'rgba(14, 165, 233, 0.2)' : 'rgba(34,197,94,0.2)'; ?>; 
            text-align: center; 
        }
        .icon { 
            font-size: 4.5rem; 
            color: <?php echo $paymentType == 'order' ? '#0ea5e9' : '#22c55e'; ?>; 
            margin-bottom: 1rem; 
        }
        h1 {
            color: <?php echo $paymentType == 'order' ? '#0c4a6e' : '#166534'; ?>;
            margin-bottom: 1rem;
            font-size: 2rem;
        }
        .amount { 
            font-size: 3.2rem; 
            font-weight: 800; 
            color: <?php echo $paymentType == 'order' ? '#0c4a6e' : '#166534'; ?>; 
            margin: 1.5rem 0; 
        }
        .message { 
            font-size: 1.1rem; 
            color: #374151; 
            margin-bottom: 2rem;
            line-height: 1.6;
        }
        .recipient-info {
            background: <?php echo $paymentType == 'order' ? '#f0f9ff' : '#ecfdf5'; ?>;
            padding: 1rem;
            border-radius: 12px;
            margin: 1.5rem 0;
            border-left: 4px solid <?php echo $paymentType == 'order' ? '#0ea5e9' : '#22c55e'; ?>;
        }
        input { 
            width: 100%; 
            padding: 1rem 1.2rem; 
            margin: 1rem 0; 
            border: 2px solid <?php echo $paymentType == 'order' ? '#bae6fd' : '#bbf7d0'; ?>; 
            border-radius: 16px; 
            font-size: 1.1rem; 
        }
        input:focus {
            outline: none;
            border-color: <?php echo $paymentType == 'order' ? '#0ea5e9' : '#22c55e'; ?>;
        }
        button { 
            width: 100%; 
            padding: 1.2rem; 
            background: <?php echo $paymentType == 'order' ? '#0ea5e9' : '#22c55e'; ?>; 
            color: white; 
            border: none; 
            border-radius: 16px; 
            font-size: 1.2rem; 
            font-weight: 700; 
            cursor: pointer; 
            transition: 0.3s; 
            margin-top: 1rem;
        }
        button:hover { 
            background: <?php echo $paymentType == 'order' ? '#0284c7' : '#16a34a'; ?>; 
            transform: translateY(-2px); 
            box-shadow: 0 10px 25px <?php echo $paymentType == 'order' ? 'rgba(14, 165, 233, 0.3)' : 'rgba(34, 197, 94, 0.3)'; ?>;
        }
        .success-box { 
            background: <?php echo $paymentType == 'order' ? '#f0f9ff' : '#ecfdf5'; ?>; 
            border: 2px solid <?php echo $paymentType == 'order' ? '#0ea5e9' : '#22c55e'; ?>; 
            padding: 2rem; 
            border-radius: 16px; 
            color: <?php echo $paymentType == 'order' ? '#0c4a6e' : '#166534'; ?>; 
        }
        .error { 
            background: #fee2e2; 
            color: #991b1b; 
            padding: 1rem; 
            border-radius: 12px; 
            border: 1px solid #fca5a5; 
            margin-bottom: 1.5rem;
        }
        .back { 
            margin-top: 1.5rem; 
            color: <?php echo $paymentType == 'order' ? '#0ea5e9' : '#22c55e'; ?>; 
            font-weight: 600; 
        }
        .back a {
            color: <?php echo $paymentType == 'order' ? '#0ea5e9' : '#22c55e'; ?>;
            text-decoration: none;
        }
        .back a:hover {
            text-decoration: underline;
        }
        .book-reference {
            font-style: italic;
            color: #6b7280;
            margin-top: 0.5rem;
            font-size: 0.95rem;
        }
        .price-note {
            font-size: 0.9rem;
            color: #6b7280;
            margin-top: 0.5rem;
        }
        .payment-badge {
            display: inline-block;
            padding: 0.25rem 1rem;
            background: <?php echo $paymentType == 'order' ? '#0ea5e9' : '#22c55e'; ?>;
            color: white;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 600;
            margin-bottom: 1rem;
            text-transform: uppercase;
        }
        .email-notice {
            font-size: 0.8rem;
            color: #6b7280;
            margin-top: 1rem;
            padding: 0.5rem;
            background: #f3f4f6;
            border-radius: 8px;
        }
        .email-notice i {
            color: <?php echo $paymentType == 'order' ? '#0ea5e9' : '#22c55e'; ?>;
            margin-right: 0.5rem;
        }
    </style>
</head>
<body>

<div class="card">
    <?php if ($success): ?>
        <div class="success-box">
            <i class="fas fa-check-circle icon"></i>
            <h2><?php echo $paymentType == 'order' ? 'Payment Request Sent!' : 'Support Request Sent!'; ?></h2>
            <p><strong>KSh <?= number_format($supportAmount) ?></strong> request sent to your phone</p>
            <p>Please complete the payment with your M-Pesa PIN</p>
            <div class="email-notice">
                <i class="fas fa-envelope"></i>
                A confirmation email has been sent to your registered email address.
            </div>
            <?php if($paymentType == 'order'): ?>
                <p style="margin-top: 1rem; font-size: 0.9rem;">Order #<?= $orderId ?> will be processed after payment confirmation</p>
            <?php endif; ?>
            <div class="back">
                Redirecting in <span id="countdown">3</span> seconds...
            </div>
        </div>

        <script>
            let seconds = 3;
            const countdown = document.getElementById('countdown');
            const timer = setInterval(() => {
                seconds--;
                countdown.textContent = seconds;
                if (seconds <= 0) {
                    clearInterval(timer);
                    window.location.href = "<?php echo $paymentType == 'order' ? '../orders/my_orders.php' : '../books/view.php?id=' . $bookId; ?>";
                }
            }, 1000);
        </script>

    <?php else: ?>
        <div class="payment-badge">
            <?php echo $paymentType == 'order' ? '📦 ORDER PAYMENT' : '💝 SUPPORT PAYMENT'; ?>
        </div>
        
        <i class="fas fa-<?php echo $paymentType == 'order' ? 'shopping-cart' : 'heart'; ?> icon"></i>
        <h1><?php echo $paymentType == 'order' ? 'Complete Payment' : 'Send Support'; ?></h1>
        <div class="message">
            <?php echo $paymentType == 'order' ? 'Complete your order payment securely with M-Pesa' : 'Show appreciation for helpful reviews'; ?>
        </div>
        <div class="amount">KSh <?= number_format($supportAmount) ?></div>
        
        <div class="recipient-info">
            <?php if($paymentType == 'order'): ?>
                <p><strong>Order #:</strong> <?= $orderId ?></p>
            <?php else: ?>
                <p><strong>To:</strong> <?= htmlspecialchars($recipientName) ?></p>
            <?php endif; ?>
            <p><strong>From:</strong> <?= htmlspecialchars($senderName) ?></p>
            <p class="book-reference">For "<?= htmlspecialchars($bookTitle) ?>"</p>
            <?php if($paymentType == 'order'): ?>
                <p class="price-note">Order amount: KSh <?= number_format($supportAmount) ?></p>
            <?php else: ?>
                <p class="price-note">Amount based on book price: KSh <?= number_format($supportAmount) ?></p>
            <?php endif; ?>
        </div>

        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST">
            <input type="text" 
                   name="phone" 
                   value="<?= htmlspecialchars($phone) ?>" 
                   placeholder="254712345678" 
                   required 
                   pattern="2547[0-9]{8}"
                   title="Enter your M-Pesa number starting with 2547"
                   oninput="this.value = this.value.replace(/\D/g,'').substring(0,12); if(!this.value.startsWith('254')) this.value='254'+this.value.slice(3);">
            <button type="submit">
                <i class="fas fa-<?php echo $paymentType == 'order' ? 'shopping-cart' : 'paper-plane'; ?>"></i> 
                <?php echo $paymentType == 'order' ? 'Pay via M-Pesa' : 'Send Support via M-Pesa'; ?>
            </button>
        </form>
        
        <div class="email-notice">
            <i class="fas fa-envelope"></i>
            A confirmation email will be sent to <?= htmlspecialchars($_SESSION['email'] ?? 'your email') ?> after payment
        </div>
        
        <div class="back" style="margin-top: 1.5rem;">
            <a href="<?php echo $paymentType == 'order' ? '../orders/my_orders.php' : '../books/view.php?id=' . $bookId; ?>">
                <i class="fas fa-arrow-left"></i> 
                Back to <?php echo $paymentType == 'order' ? 'My Orders' : 'Book'; ?>
            </a>
        </div>
    <?php endif; ?>
</div>

</body>
</html>