<?php
$projectRoot = dirname(__DIR__, 2);
require_once $projectRoot . '/config/db.php';
require_once $projectRoot . '/includes/auth_check.php';

if ($_SESSION['role'] != 'admin') {
    header('Location: ../../index.php');
    exit();
}

if (isset($_GET['approve'])) {
    $review_id = (int) $_GET['approve'];
    $stmt = $pdo->prepare("UPDATE reviews SET status = 'approved' WHERE review_id = ?");
    if ($review_id > 0 && $stmt->execute([$review_id])) {
        $success = 'Review approved successfully!';
    }
}

if (isset($_GET['reject'])) {
    $review_id = (int) $_GET['reject'];
    $stmt = $pdo->prepare("UPDATE reviews SET status = 'rejected' WHERE review_id = ?");
    if ($review_id > 0 && $stmt->execute([$review_id])) {
        $success = 'Review rejected successfully!';
    }
}

if (isset($_GET['delete'])) {
    $review_id = (int) $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM reviews WHERE review_id = ?");
    if ($review_id > 0 && $stmt->execute([$review_id])) {
        $success = 'Review deleted successfully!';
    }
}

// Get review statistics
$stmt = $pdo->query("SELECT COUNT(*) as total_reviews FROM reviews");
$total_reviews = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) as pending_reviews FROM reviews WHERE status = 'pending'");
$pending_reviews_count = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) as approved_reviews FROM reviews WHERE status = 'approved'");
$approved_reviews = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) as rejected_reviews FROM reviews WHERE status = 'rejected'");
$rejected_reviews = $stmt->fetchColumn();

// Get pending reviews
$stmt = $pdo->query("SELECT r.*, u.name, b.title, b.author FROM reviews r JOIN users u ON r.user_id = u.user_id JOIN books b ON r.book_id = b.book_id WHERE r.status = 'pending' ORDER BY r.created_at DESC");
$pending_reviews = $stmt->fetchAll();

// Get all reviews
$stmt = $pdo->query("SELECT r.*, u.name, b.title, b.author FROM reviews r JOIN users u ON r.user_id = u.user_id JOIN books b ON r.book_id = b.book_id ORDER BY r.created_at DESC");
$all_reviews = $stmt->fetchAll();
?>

<?php include $projectRoot . '/includes/header.php'; ?>

<!-- Hero Section -->
<div class="hero-section" style="
    background: linear-gradient(135deg, rgba(245, 158, 11, 0.9), rgba(217, 119, 6, 0.9)),
                url('https://images.unsplash.com/photo-1481627834876-b7833e8f5570?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2028&q=80');
    background-size: cover;
    background-position: center;
    padding: 3rem 2rem;
    position: relative;
    overflow: hidden;
">
    <div class="container-wide" style="
        max-width: 1400px;
        margin: 0 auto;
        width: 100%;
    ">
        <div class="hero-content" style="
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 4rem;
            align-items: center;
        ">
            <div class="hero-text">
                <h1 style="
                    font-size: 3rem;
                    font-weight: 800;
                    color: white;
                    margin-bottom: 1rem;
                    line-height: 1.1;
                ">
                    Review Management
                </h1>
                
                <p style="
                    font-size: 1.2rem;
                    color: rgba(255, 255, 255, 0.9);
                    margin-bottom: 2rem;
                    line-height: 1.6;
                ">
                    Moderate book reviews, ensure quality content, and maintain community standards.
                </p>
                
                <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                    <a href="dashboard.php" class="btn btn-outline" style="
                        background: rgba(255, 255, 255, 0.1);
                        backdrop-filter: blur(10px);
                        border: 1px solid rgba(255, 255, 255, 0.3);
                        color: white;
                        padding: 0.75rem 1.5rem;
                        border-radius: 8px;
                        text-decoration: none;
                        font-weight: 600;
                        display: inline-flex;
                        align-items: center;
                        gap: 0.5rem;
                        transition: all 0.3s ease;
                    ">
                        <i class="fas fa-arrow-left"></i>
                        Back to Dashboard
                    </a>
                    
                    <a href="manage_books.php" class="btn btn-primary" style="
                        background: rgba(255, 255, 255, 0.2);
                        backdrop-filter: blur(10px);
                        border: 1px solid rgba(255, 255, 255, 0.3);
                        color: white;
                        padding: 0.75rem 1.5rem;
                        border-radius: 8px;
                        text-decoration: none;
                        font-weight: 600;
                        display: inline-flex;
                        align-items: center;
                        gap: 0.5rem;
                        transition: all 0.3s ease;
                    ">
                        <i class="fas fa-book"></i>
                        Manage Books
                    </a>
                </div>
            </div>
            
            <div class="admin-stats" style="
                background: rgba(255, 255, 255, 0.15);
                backdrop-filter: blur(20px);
                border: 1px solid rgba(255, 255, 255, 0.3);
                padding: 2rem;
                border-radius: 16px;
                color: white;
                min-width: 280px;
            ">
                <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1.5rem;">
                    <div style="
                        background: rgba(255, 255, 255, 0.2);
                        width: 50px;
                        height: 50px;
                        border-radius: 10px;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        font-size: 1.5rem;
                    ">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                    <div>
                        <div style="font-size: 0.9rem; opacity: 0.8;">Review Analytics</div>
                        <div style="font-size: 1.1rem; font-weight: 700;">System Overview</div>
                    </div>
                </div>
                
                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span>Total Reviews</span>
                        <span style="font-weight: 700; font-size: 1.2rem;"><?php echo $total_reviews; ?></span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span>Pending Review</span>
                        <span style="font-weight: 700; font-size: 1.2rem; color: #fbbf24;"><?php echo $pending_reviews_count; ?></span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span>Approved</span>
                        <span style="font-weight: 700; font-size: 1.2rem; color: #34d399;"><?php echo $approved_reviews; ?></span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span>Rejected</span>
                        <span style="font-weight: 700; font-size: 1.2rem; color: #f87171;"><?php echo $rejected_reviews; ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="main-content-wide" style="
    padding: 3rem 2rem;
    background: #f8fafc;
    min-height: 60vh;
">
    <div class="container-wide" style="
        max-width: 1400px;
        margin: 0 auto;
        width: 100%;
    ">
        <?php if(isset($success)): ?>
            <div class="alert alert-success" style="
                background: #d1fae5;
                color: #065f46;
                padding: 1rem 1.5rem;
                border-radius: 10px;
                border: 1px solid #a7f3d0;
                margin-bottom: 2rem;
                display: flex;
                align-items: center;
                gap: 0.75rem;
                font-weight: 600;
            ">
                <i class="fas fa-check-circle" style="color: #10b981;"></i>
                <?php echo $success; ?>
            </div>
        <?php endif; ?>
        
        <div class="reviews-management-grid" style="
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
        ">
            <!-- Pending Reviews Section -->
            <div class="pending-reviews-card" style="
                background: white;
                border-radius: 20px;
                padding: 2.5rem;
                box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
                border: 1px solid #e5e7eb;
                height: fit-content;
            ">
                <div style="
                    display: flex;
                    align-items: center;
                    gap: 1rem;
                    margin-bottom: 2rem;
                    padding-bottom: 1.5rem;
                    border-bottom: 2px solid #fef3c7;
                ">
                    <div style="
                        background: linear-gradient(135deg, #f59e0b, #d97706);
                        width: 60px;
                        height: 60px;
                        border-radius: 12px;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        color: white;
                        font-size: 1.5rem;
                    ">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div>
                        <h2 style="
                            color: #1f2937;
                            font-size: 1.5rem;
                            font-weight: 700;
                            margin: 0 0 0.25rem 0;
                        ">Pending Reviews</h2>
                        <p style="color: #6b7280; margin: 0;">Awaiting moderation and approval</p>
                    </div>
                    <div style="
                        background: #f59e0b;
                        color: white;
                        padding: 0.5rem 1.2rem;
                        border-radius: 20px;
                        font-weight: 700;
                        margin-left: auto;
                        font-size: 0.9rem;
                    ">
                        <?php echo count($pending_reviews); ?> Pending
                    </div>
                </div>
                
                <?php if(count($pending_reviews) > 0): ?>
                    <div style="display: flex; flex-direction: column; gap: 1.5rem; max-height: 600px; overflow-y: auto; padding-right: 0.5rem;">
                        <?php foreach($pending_reviews as $review): ?>
                            <div class="review-item" style="
                                background: #fffbeb;
                                padding: 1.5rem;
                                border-radius: 12px;
                                border: 2px solid #fef3c7;
                                transition: all 0.3s ease;
                            ">
                                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem;">
                                    <div style="flex: 1;">
                                        <h4 style="
                                            color: #92400e;
                                            font-size: 1.1rem;
                                            font-weight: 700;
                                            margin: 0 0 0.5rem 0;
                                        "><?php echo htmlspecialchars($review['title']); ?></h4>
                                        <p style="
                                            color: #b45309;
                                            margin: 0 0 0.25rem 0;
                                            display: flex;
                                            align-items: center;
                                            gap: 0.5rem;
                                            font-weight: 600;
                                        ">
                                            <i class="fas fa-user-pen"></i>
                                            <?php echo htmlspecialchars($review['author']); ?>
                                        </p>
                                        <p style="
                                            color: #d97706;
                                            margin: 0;
                                            font-size: 0.9rem;
                                            display: flex;
                                            align-items: center;
                                            gap: 0.5rem;
                                        ">
                                            <i class="fas fa-user"></i>
                                            Reviewed by: <?php echo htmlspecialchars($review['name']); ?>
                                        </p>
                                    </div>
                                    <div style="display: flex; flex-direction: column; align-items: flex-end; gap: 0.5rem;">
                                        <div style="display: flex; gap: 0.25rem;">
                                            <?php for($i = 1; $i <= 5; $i++): ?>
                                                <i class="fas fa-star" style="color: <?php echo $i <= $review['rating'] ? '#fbbf24' : '#fef3c7'; ?>"></i>
                                            <?php endfor; ?>
                                        </div>
                                        <span style="
                                            background: #f59e0b;
                                            color: white;
                                            padding: 0.4rem 1rem;
                                            border-radius: 20px;
                                            font-size: 0.8rem;
                                            font-weight: 700;
                                            text-transform: uppercase;
                                            letter-spacing: 0.5px;
                                        ">
                                            Awaiting Review
                                        </span>
                                    </div>
                                </div>
                                
                                <div style="
                                    background: white;
                                    padding: 1.25rem;
                                    border-radius: 8px;
                                    margin-bottom: 1.5rem;
                                    border-left: 4px solid #f59e0b;
                                ">
                                    <p style="
                                        color: #92400e;
                                        line-height: 1.6;
                                        margin: 0;
                                        font-size: 0.95rem;
                                    "><?php echo htmlspecialchars($review['comment']); ?></p>
                                </div>
                                
                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                    <small style="color: #b45309; font-weight: 500;">
                                        <i class="fas fa-clock"></i>
                                        Submitted: <?php echo date('M j, Y g:i A', strtotime($review['created_at'])); ?>
                                    </small>
                                    <div style="display: flex; gap: 0.75rem;">
                                        <a href="?approve=<?php echo $review['review_id']; ?>" class="btn" style="
                                            background: linear-gradient(135deg, #10b981, #059669);
                                            color: white;
                                            padding: 0.6rem 1.2rem;
                                            border-radius: 8px;
                                            text-decoration: none;
                                            font-weight: 600;
                                            font-size: 0.8rem;
                                            display: inline-flex;
                                            align-items: center;
                                            gap: 0.5rem;
                                            transition: all 0.3s ease;
                                        " title="Approve Review">
                                            <i class="fas fa-check"></i>
                                            Approve
                                        </a>
                                        <a href="?reject=<?php echo $review['review_id']; ?>" class="btn" style="
                                            background: linear-gradient(135deg, #f59e0b, #d97706);
                                            color: white;
                                            padding: 0.6rem 1.2rem;
                                            border-radius: 8px;
                                            text-decoration: none;
                                            font-weight: 600;
                                            font-size: 0.8rem;
                                            display: inline-flex;
                                            align-items: center;
                                            gap: 0.5rem;
                                            transition: all 0.3s ease;
                                        " title="Reject Review">
                                            <i class="fas fa-times"></i>
                                            Reject
                                        </a>
                                        <a href="?delete=<?php echo $review['review_id']; ?>" 
                                           onclick="return confirm('Are you sure you want to delete this review? This action cannot be undone.')"
                                           class="btn" style="
                                            background: linear-gradient(135deg, #ef4444, #dc2626);
                                            color: white;
                                            padding: 0.6rem 1.2rem;
                                            border-radius: 8px;
                                            text-decoration: none;
                                            font-weight: 600;
                                            font-size: 0.8rem;
                                            display: inline-flex;
                                            align-items: center;
                                            gap: 0.5rem;
                                            transition: all 0.3s ease;
                                        " title="Delete Review">
                                            <i class="fas fa-trash"></i>
                                            Delete
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div style="
                        text-align: center;
                        padding: 3rem 2rem;
                        color: #6b7280;
                    ">
                        <div style="
                            width: 100px;
                            height: 100px;
                            background: #fef3c7;
                            border-radius: 50%;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            margin: 0 auto 1.5rem;
                            color: #f59e0b;
                            font-size: 2.5rem;
                        ">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <h3 style="color: #92400e; margin-bottom: 0.5rem; font-size: 1.5rem;">All Caught Up!</h3>
                        <p style="margin: 0; color: #b45309;">No pending reviews. All reviews have been moderated.</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- All Reviews Section -->
            <div class="all-reviews-card" style="
                background: white;
                border-radius: 20px;
                padding: 2.5rem;
                box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
                border: 1px solid #e5e7eb;
            ">
                <div style="
                    display: flex;
                    align-items: center;
                    gap: 1rem;
                    margin-bottom: 2rem;
                    padding-bottom: 1.5rem;
                    border-bottom: 2px solid #f1f5f9;
                ">
                    <div style="
                        background: linear-gradient(135deg, #4f46e5, #7c3aed);
                        width: 60px;
                        height: 60px;
                        border-radius: 12px;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        color: white;
                        font-size: 1.5rem;
                    ">
                        <i class="fas fa-list"></i>
                    </div>
                    <div>
                        <h2 style="
                            color: #1f2937;
                            font-size: 1.5rem;
                            font-weight: 700;
                            margin: 0 0 0.25rem 0;
                        ">All Reviews</h2>
                        <p style="color: #6b7280; margin: 0;">Complete review history</p>
                    </div>
                    <div style="
                        background: #4f46e5;
                        color: white;
                        padding: 0.5rem 1.2rem;
                        border-radius: 20px;
                        font-weight: 700;
                        margin-left: auto;
                        font-size: 0.9rem;
                    ">
                        <?php echo count($all_reviews); ?> Total
                    </div>
                </div>
                
                <div style="max-height: 600px; overflow-y: auto; padding-right: 0.5rem;">
                    <div style="display: flex; flex-direction: column; gap: 1rem;">
                        <?php foreach($all_reviews as $review): ?>
                            <div class="review-item-small" style="
                                background: #f8fafc;
                                padding: 1.25rem;
                                border-radius: 10px;
                                border-left: 4px solid <?php 
                                    echo $review['status'] == 'approved' ? '#10b981' : 
                                         ($review['status'] == 'rejected' ? '#ef4444' : '#f59e0b'); 
                                ?>;
                                transition: all 0.3s ease;
                            ">
                                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.75rem;">
                                    <div style="flex: 1;">
                                        <h4 style="
                                            color: #1f2937;
                                            font-size: 0.95rem;
                                            font-weight: 700;
                                            margin: 0 0 0.25rem 0;
                                        "><?php echo htmlspecialchars($review['title']); ?></h4>
                                        <p style="
                                            color: #6b7280;
                                            margin: 0 0 0.25rem 0;
                                            font-size: 0.8rem;
                                            font-weight: 600;
                                        ">
                                            By <?php echo htmlspecialchars($review['author']); ?>
                                        </p>
                                        <p style="
                                            color: #9ca3af;
                                            margin: 0;
                                            font-size: 0.75rem;
                                        ">
                                            <i class="fas fa-user"></i>
                                            <?php echo htmlspecialchars($review['name']); ?>
                                        </p>
                                    </div>
                                    <div style="display: flex; flex-direction: column; align-items: flex-end; gap: 0.5rem;">
                                        <div style="display: flex; gap: 0.2rem;">
                                            <?php for($i = 1; $i <= 5; $i++): ?>
                                                <i class="fas fa-star" style="color: <?php echo $i <= $review['rating'] ? '#fbbf24' : '#e5e7eb'; ?>; font-size: 0.7rem;"></i>
                                            <?php endfor; ?>
                                        </div>
                                        <span style="
                                            background: <?php 
                                                echo $review['status'] == 'approved' ? '#10b981' : 
                                                       ($review['status'] == 'rejected' ? '#ef4444' : '#f59e0b'); 
                                            ?>;
                                            color: white;
                                            padding: 0.3rem 0.8rem;
                                            border-radius: 20px;
                                            font-size: 0.7rem;
                                            font-weight: 700;
                                            text-transform: uppercase;
                                            letter-spacing: 0.5px;
                                        ">
                                            <?php echo ucfirst($review['status']); ?>
                                        </span>
                                    </div>
                                </div>
                                
                                <p style="
                                    color: #6b7280;
                                    font-size: 0.8rem;
                                    line-height: 1.4;
                                    margin-bottom: 0.75rem;
                                    display: -webkit-box;
                                    -webkit-line-clamp: 2;
                                    -webkit-box-orient: vertical;
                                    overflow: hidden;
                                "><?php echo htmlspecialchars($review['comment']); ?></p>
                                
                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                    <small style="color: #9ca3af; font-size: 0.7rem; font-weight: 500;">
                                        <i class="fas fa-calendar"></i>
                                        <?php echo date('M j, Y', strtotime($review['created_at'])); ?>
                                    </small>
                                    <a href="?delete=<?php echo $review['review_id']; ?>" 
                                       onclick="return confirm('Are you sure you want to delete this review? This action cannot be undone.')"
                                       class="btn" style="
                                        background: #fee2e2;
                                        color: #dc2626;
                                        padding: 0.4rem 0.8rem;
                                        border-radius: 6px;
                                        text-decoration: none;
                                        font-weight: 600;
                                        font-size: 0.7rem;
                                        display: inline-flex;
                                        align-items: center;
                                        gap: 0.25rem;
                                        transition: all 0.3s ease;
                                    " title="Delete Review">
                                        <i class="fas fa-trash"></i>
                                        Delete
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Hover Effects */
    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15);
    }

    .btn-primary:hover {
        background: rgba(255, 255, 255, 0.3) !important;
    }

    .btn-outline:hover {
        background: rgba(255, 255, 255, 0.15) !important;
    }

    .review-item:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(245, 158, 11, 0.15);
        border-color: #f59e0b !important;
    }

    .review-item-small:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        background: white !important;
    }

    /* Scrollbar Styling */
    .pending-reviews-card > div::-webkit-scrollbar,
    .all-reviews-card > div::-webkit-scrollbar {
        width: 6px;
    }

    .pending-reviews-card > div::-webkit-scrollbar-track,
    .all-reviews-card > div::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 10px;
    }

    .pending-reviews-card > div::-webkit-scrollbar-thumb,
    .all-reviews-card > div::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 10px;
    }

    .pending-reviews-card > div::-webkit-scrollbar-thumb:hover,
    .all-reviews-card > div::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }

    /* Responsive Design */
    @media (max-width: 1024px) {
        .hero-content {
            grid-template-columns: 1fr;
            gap: 2rem;
            text-align: center;
        }
        
        .reviews-management-grid {
            grid-template-columns: 1fr;
        }
        
        .admin-stats {
            max-width: 400px;
            margin: 0 auto;
        }
    }

    @media (max-width: 768px) {
        .hero-section h1 {
            font-size: 2.25rem;
        }
        
        .pending-reviews-card,
        .all-reviews-card {
            padding: 2rem !important;
        }
        
        .pending-reviews-card > div:first-child,
        .all-reviews-card > div:first-child {
            flex-direction: column;
            text-align: center;
            gap: 1rem;
        }
        
        .pending-reviews-card > div:first-child > div:last-child,
        .all-reviews-card > div:first-child > div:last-child {
            margin-left: 0 !important;
        }
        
        .review-item > div:last-child > div {
            flex-direction: column;
            gap: 0.5rem;
            align-items: flex-start;
        }
        
        .review-item > div:last-child > div > div {
            align-self: stretch;
        }
        
        .review-item > div:last-child > div > div a {
            flex: 1;
            justify-content: center;
        }
    }

    @media (max-width: 480px) {
        .hero-section {
            padding: 2rem 1rem;
        }
        
        .main-content-wide {
            padding: 1.5rem 1rem;
        }
        
        .pending-reviews-card,
        .all-reviews-card {
            padding: 1.5rem !important;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add hover effects to review items
        const reviewItems = document.querySelectorAll('.review-item');
        reviewItems.forEach(item => {
            item.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-3px)';
                this.style.boxShadow = '0 8px 20px rgba(245, 158, 11, 0.15)';
                this.style.borderColor = '#f59e0b';
            });
            
            item.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
                this.style.boxShadow = 'none';
                this.style.borderColor = '#fef3c7';
            });
        });

        // Add hover effects to small review items
        const smallReviewItems = document.querySelectorAll('.review-item-small');
        smallReviewItems.forEach(item => {
            item.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-2px)';
                this.style.boxShadow = '0 4px 12px rgba(0, 0, 0, 0.1)';
                this.style.background = 'white';
            });
            
            item.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
                this.style.boxShadow = 'none';
                this.style.background = '#f8fafc';
            });
        });

        // Add loading state to buttons
        const buttons = document.querySelectorAll('.btn');
        buttons.forEach(button => {
            button.addEventListener('mousedown', function() {
                this.style.transform = 'translateY(0)';
            });
            
            button.addEventListener('mouseup', function() {
                this.style.transform = 'translateY(-2px)';
            });
        });
    });
</script>

<?php include $projectRoot . '/includes/footer.php'; ?>