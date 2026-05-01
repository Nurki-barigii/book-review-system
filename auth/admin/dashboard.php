<?php
require_once __DIR__ . '/../../includes/auth_check.php';
if ($_SESSION['role'] != 'admin') {
    header('Location: ../../index.php');
    exit();
}
require_once __DIR__ . '/../../config/db.php';
?>

<?php include __DIR__ . '/../../includes/header.php'; ?>

<!-- Hero Section -->
<div class="hero-section" style="
    background: linear-gradient(135deg, rgba(79, 70, 229, 0.9), rgba(99, 102, 241, 0.9)),
                url('https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80');
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
                    Admin Dashboard
                </h1>
                
                <p style="
                    font-size: 1.2rem;
                    color: rgba(255, 255, 255, 0.9);
                    margin-bottom: 2rem;
                    line-height: 1.6;
                ">
                    Welcome back, <strong><?php echo htmlspecialchars($_SESSION['name']); ?></strong>. 
                    Manage your book review system and monitor platform activity.
                </p>
                
                <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                    <a href="../../index.php" class="btn btn-outline" style="
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
                        <i class="fas fa-home"></i>
                        Back to Site
                    </a>
                    
                    <a href="manage_reviews.php" class="btn btn-primary" style="
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
                        <i class="fas fa-star"></i>
                        Moderate Reviews
                    </a>
                </div>
            </div>
            
            <div class="admin-profile" style="
                background: rgba(255, 255, 255, 0.15);
                backdrop-filter: blur(20px);
                border: 1px solid rgba(255, 255, 255, 0.3);
                padding: 2rem;
                border-radius: 16px;
                color: white;
                text-align: center;
                min-width: 200px;
            ">
                <div style="
                    background: rgba(255, 255, 255, 0.2);
                    width: 80px;
                    height: 80px;
                    border-radius: 50%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    margin: 0 auto 1rem;
                    font-size: 2rem;
                    font-weight: 700;
                ">
                    <?php echo strtoupper(substr($_SESSION['name'], 0, 1)); ?>
                </div>
                <div style="font-weight: 700; margin-bottom: 0.25rem;"><?php echo htmlspecialchars($_SESSION['name']); ?></div>
                <div style="
                    background: rgba(255, 255, 255, 0.3);
                    color: white;
                    padding: 0.25rem 0.75rem;
                    border-radius: 20px;
                    font-size: 0.8rem;
                    font-weight: 600;
                    display: inline-block;
                ">
                    <i class="fas fa-crown"></i>
                    Administrator
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
        <?php
        // Get system statistics
        $total_books = $pdo->query("SELECT COUNT(*) FROM books")->fetchColumn();
        $total_users = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
        $total_reviews = $pdo->query("SELECT COUNT(*) FROM reviews")->fetchColumn();
        $pending_reviews = $pdo->query("SELECT COUNT(*) FROM reviews WHERE status = 'pending'")->fetchColumn();
        $approved_reviews = $pdo->query("SELECT COUNT(*) FROM reviews WHERE status = 'approved'")->fetchColumn();
        $recent_books = $pdo->query("SELECT COUNT(*) FROM books WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)")->fetchColumn();
        $recent_users = $pdo->query("SELECT COUNT(*) FROM users WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)")->fetchColumn();
        
        // Get recent activity
        $recent_reviews = $pdo->query("SELECT r.*, u.name as user_name, b.title as book_title FROM reviews r JOIN users u ON r.user_id = u.user_id JOIN books b ON r.book_id = b.book_id ORDER BY r.created_at DESC LIMIT 5")->fetchAll();
        ?>
        
        <!-- Statistics Cards -->
        <div class="stats-grid" style="
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 3rem;
        ">
            <div class="stat-card" style="
                background: white;
                border-radius: 16px;
                padding: 2rem;
                box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
                border: 1px solid #e5e7eb;
                text-align: center;
                transition: all 0.3s ease;
            ">
                <div style="
                    background: linear-gradient(135deg, #4f46e5, #7c3aed);
                    width: 60px;
                    height: 60px;
                    border-radius: 12px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    margin: 0 auto 1rem;
                    color: white;
                    font-size: 1.5rem;
                ">
                    <i class="fas fa-book"></i>
                </div>
                <div style="font-size: 2.5rem; font-weight: 800; color: #1f2937; margin-bottom: 0.5rem;"><?php echo $total_books; ?></div>
                <div style="color: #6b7280; font-weight: 600;">Total Books</div>
                <?php if($recent_books > 0): ?>
                    <div style="color: #10b981; font-size: 0.8rem; font-weight: 600; margin-top: 0.5rem;">
                        +<?php echo $recent_books; ?> this week
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="stat-card" style="
                background: white;
                border-radius: 16px;
                padding: 2rem;
                box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
                border: 1px solid #e5e7eb;
                text-align: center;
                transition: all 0.3s ease;
            ">
                <div style="
                    background: linear-gradient(135deg, #10b981, #059669);
                    width: 60px;
                    height: 60px;
                    border-radius: 12px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    margin: 0 auto 1rem;
                    color: white;
                    font-size: 1.5rem;
                ">
                    <i class="fas fa-users"></i>
                </div>
                <div style="font-size: 2.5rem; font-weight: 800; color: #1f2937; margin-bottom: 0.5rem;"><?php echo $total_users; ?></div>
                <div style="color: #6b7280; font-weight: 600;">Total Users</div>
                <?php if($recent_users > 0): ?>
                    <div style="color: #10b981; font-size: 0.8rem; font-weight: 600; margin-top: 0.5rem;">
                        +<?php echo $recent_users; ?> this week
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="stat-card" style="
                background: white;
                border-radius: 16px;
                padding: 2rem;
                box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
                border: 1px solid #e5e7eb;
                text-align: center;
                transition: all 0.3s ease;
            ">
                <div style="
                    background: linear-gradient(135deg, #f59e0b, #d97706);
                    width: 60px;
                    height: 60px;
                    border-radius: 12px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    margin: 0 auto 1rem;
                    color: white;
                    font-size: 1.5rem;
                ">
                    <i class="fas fa-star"></i>
                </div>
                <div style="font-size: 2.5rem; font-weight: 800; color: #1f2937; margin-bottom: 0.5rem;"><?php echo $total_reviews; ?></div>
                <div style="color: #6b7280; font-weight: 600;">Total Reviews</div>
                <div style="color: #10b981; font-size: 0.8rem; font-weight: 600; margin-top: 0.5rem;">
                    <?php echo $approved_reviews; ?> approved
                </div>
            </div>
            
            <div class="stat-card" style="
                background: white;
                border-radius: 16px;
                padding: 2rem;
                box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
                border: 1px solid #e5e7eb;
                text-align: center;
                transition: all 0.3s ease;
            ">
                <div style="
                    background: linear-gradient(135deg, #ef4444, #dc2626);
                    width: 60px;
                    height: 60px;
                    border-radius: 12px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    margin: 0 auto 1rem;
                    color: white;
                    font-size: 1.5rem;
                ">
                    <i class="fas fa-clock"></i>
                </div>
                <div style="font-size: 2.5rem; font-weight: 800; color: #1f2937; margin-bottom: 0.5rem;"><?php echo $pending_reviews; ?></div>
                <div style="color: #6b7280; font-weight: 600;">Pending Reviews</div>
                <?php if($pending_reviews > 0): ?>
                    <div style="color: #ef4444; font-size: 0.8rem; font-weight: 600; margin-top: 0.5rem;">
                        Needs attention
                    </div>
                <?php else: ?>
                    <div style="color: #10b981; font-size: 0.8rem; font-weight: 600; margin-top: 0.5rem;">
                        All caught up!
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Main Actions Grid -->
        <div class="actions-section" style="margin-bottom: 3rem;">
            <h2 style="
                color: #1f2937;
                font-size: 1.75rem;
                font-weight: 700;
                margin-bottom: 2rem;
                display: flex;
                align-items: center;
                gap: 0.75rem;
            ">
                <i class="fas fa-bolt"></i>
                Quick Actions
            </h2>
            
            <div class="actions-grid" style="
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
                gap: 1.5rem;
            ">
                <a href="manage_books.php" class="action-card" style="
                    background: white;
                    border-radius: 16px;
                    padding: 2.5rem;
                    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
                    border: 1px solid #e5e7eb;
                    text-decoration: none;
                    color: inherit;
                    transition: all 0.3s ease;
                    display: block;
                ">
                    <div style="display: flex; align-items: center; gap: 1.5rem;">
                        <div style="
                            background: linear-gradient(135deg, #4f46e5, #7c3aed);
                            width: 70px;
                            height: 70px;
                            border-radius: 12px;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            color: white;
                            font-size: 1.5rem;
                        ">
                            <i class="fas fa-book"></i>
                        </div>
                        <div style="flex: 1;">
                            <h3 style="color: #1f2937; font-size: 1.25rem; font-weight: 700; margin: 0 0 0.5rem 0;">Manage Books</h3>
                            <p style="color: #6b7280; margin: 0; line-height: 1.5;">Add, edit, or delete books from your library collection</p>
                        </div>
                        <div style="color: #4f46e5; font-size: 1.25rem;">
                            <i class="fas fa-arrow-right"></i>
                        </div>
                    </div>
                </a>
                
                <a href="manage_users.php" class="action-card" style="
                    background: white;
                    border-radius: 16px;
                    padding: 2.5rem;
                    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
                    border: 1px solid #e5e7eb;
                    text-decoration: none;
                    color: inherit;
                    transition: all 0.3s ease;
                    display: block;
                ">
                    <div style="display: flex; align-items: center; gap: 1.5rem;">
                        <div style="
                            background: linear-gradient(135deg, #10b981, #059669);
                            width: 70px;
                            height: 70px;
                            border-radius: 12px;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            color: white;
                            font-size: 1.5rem;
                        ">
                            <i class="fas fa-users"></i>
                        </div>
                        <div style="flex: 1;">
                            <h3 style="color: #1f2937; font-size: 1.25rem; font-weight: 700; margin: 0 0 0.5rem 0;">Manage Users</h3>
                            <p style="color: #6b7280; margin: 0; line-height: 1.5;">View and manage user accounts and permissions</p>
                        </div>
                        <div style="color: #10b981; font-size: 1.25rem;">
                            <i class="fas fa-arrow-right"></i>
                        </div>
                    </div>
                </a>
                
                <a href="manage_reviews.php" class="action-card" style="
                    background: white;
                    border-radius: 16px;
                    padding: 2.5rem;
                    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
                    border: 1px solid #e5e7eb;
                    text-decoration: none;
                    color: inherit;
                    transition: all 0.3s ease;
                    display: block;
                ">
                    <div style="display: flex; align-items: center; gap: 1.5rem;">
                        <div style="
                            background: linear-gradient(135deg, #f59e0b, #d97706);
                            width: 70px;
                            height: 70px;
                            border-radius: 12px;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            color: white;
                            font-size: 1.5rem;
                        ">
                            <i class="fas fa-star"></i>
                        </div>
                        <div style="flex: 1;">
                            <h3 style="color: #1f2937; font-size: 1.25rem; font-weight: 700; margin: 0 0 0.5rem 0;">Manage Reviews</h3>
                            <p style="color: #6b7280; margin: 0; line-height: 1.5;">Approve, reject, or delete user reviews</p>
                        </div>
                        <div style="color: #f59e0b; font-size: 1.25rem;">
                            <i class="fas fa-arrow-right"></i>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <!-- Recent Activity Section -->
        <div class="recent-activity" style="
            background: white;
            border-radius: 20px;
            padding: 2.5rem;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
            border: 1px solid #e5e7eb;
        ">
            <h2 style="
                color: #1f2937;
                font-size: 1.75rem;
                font-weight: 700;
                margin-bottom: 2rem;
                display: flex;
                align-items: center;
                gap: 0.75rem;
            ">
                <i class="fas fa-history"></i>
                Recent Activity
            </h2>
            
            <?php if(count($recent_reviews) > 0): ?>
                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    <?php foreach($recent_reviews as $review): ?>
                        <div class="activity-item" style="
                            display: flex;
                            align-items: center;
                            gap: 1rem;
                            padding: 1.25rem;
                            background: #f8fafc;
                            border-radius: 12px;
                            border: 1px solid #e5e7eb;
                            transition: all 0.3s ease;
                        ">
                            <div style="
                                background: <?php echo $review['status'] == 'approved' ? '#10b981' : ($review['status'] == 'rejected' ? '#ef4444' : '#f59e0b'); ?>;
                                color: white;
                                width: 40px;
                                height: 40px;
                                border-radius: 10px;
                                display: flex;
                                align-items: center;
                                justify-content: center;
                                font-size: 1rem;
                            ">
                                <i class="fas fa-star"></i>
                            </div>
                            <div style="flex: 1;">
                                <div style="font-weight: 600; color: #1f2937; margin-bottom: 0.25rem;">
                                    <?php echo htmlspecialchars($review['user_name']); ?> reviewed "<?php echo htmlspecialchars($review['book_title']); ?>"
                                </div>
                                <div style="color: #6b7280; font-size: 0.875rem;">
                                    <?php echo date('M j, Y g:i A', strtotime($review['created_at'])); ?>
                                </div>
                            </div>
                            <span style="
                                background: <?php echo $review['status'] == 'approved' ? '#d1fae5' : ($review['status'] == 'rejected' ? '#fee2e2' : '#fef3c7'); ?>;
                                color: <?php echo $review['status'] == 'approved' ? '#065f46' : ($review['status'] == 'rejected' ? '#991b1b' : '#92400e'); ?>;
                                padding: 0.4rem 1rem;
                                border-radius: 20px;
                                font-size: 0.8rem;
                                font-weight: 600;
                                text-transform: uppercase;
                            ">
                                <?php echo ucfirst($review['status']); ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div style="text-align: center; padding: 2rem; color: #6b7280;">
                    <i class="fas fa-inbox" style="font-size: 3rem; color: #e5e7eb; margin-bottom: 1rem;"></i>
                    <h3 style="color: #6b7280; margin-bottom: 0.5rem;">No Recent Activity</h3>
                    <p>User activity will appear here once reviews are submitted.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
    /* Hover Effects */
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }

    .action-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        border-color: #4f46e5;
    }

    .activity-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
    }

    .btn-primary:hover {
        background: rgba(255, 255, 255, 0.3) !important;
    }

    .btn-outline:hover {
        background: rgba(255, 255, 255, 0.15) !important;
    }

    /* Responsive Design */
    @media (max-width: 1024px) {
        .hero-content {
            grid-template-columns: 1fr;
            gap: 2rem;
            text-align: center;
        }
        
        .admin-profile {
            max-width: 300px;
            margin: 0 auto;
        }
    }

    @media (max-width: 768px) {
        .hero-section h1 {
            font-size: 2.25rem;
        }
        
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
        
        .actions-grid {
            grid-template-columns: 1fr;
        }
        
        .action-card {
            padding: 2rem !important;
        }
        
        .action-card > div {
            gap: 1rem !important;
        }
        
        .action-card > div > div:first-child {
            width: 50px !important;
            height: 50px !important;
            font-size: 1.25rem !important;
        }
    }

    @media (max-width: 480px) {
        .hero-section {
            padding: 2rem 1rem;
        }
        
        .main-content-wide {
            padding: 1.5rem 1rem;
        }
        
        .stats-grid {
            grid-template-columns: 1fr;
        }
        
        .recent-activity {
            padding: 1.5rem !important;
        }
        
        .activity-item {
            flex-direction: column;
            text-align: center;
            gap: 0.75rem !important;
        }
        
        .activity-item > div:last-child {
            align-self: center;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add hover effects to stat cards
        const statCards = document.querySelectorAll('.stat-card');
        statCards.forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-5px)';
                this.style.boxShadow = '0 8px 25px rgba(0, 0, 0, 0.15)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
                this.style.boxShadow = '0 4px 15px rgba(0, 0, 0, 0.08)';
            });
        });

        // Add hover effects to action cards
        const actionCards = document.querySelectorAll('.action-card');
        actionCards.forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-5px)';
                this.style.boxShadow = '0 8px 25px rgba(0, 0, 0, 0.15)';
                this.style.borderColor = '#4f46e5';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
                this.style.boxShadow = '0 4px 15px rgba(0, 0, 0, 0.08)';
                this.style.borderColor = '#e5e7eb';
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

        // Add click effects to action cards
        actionCards.forEach(card => {
            card.addEventListener('click', function(e) {
                const originalContent = this.innerHTML;
                this.innerHTML = '<div style="display: flex; align-items: center; justify-content: center; gap: 0.75rem; padding: 1rem;"><i class="fas fa-spinner fa-spin"></i> Loading...</div>';
                
                setTimeout(() => {
                    this.innerHTML = originalContent;
                }, 1000);
            });
        });
    });
</script>

<?php include __DIR__ . '/../../includes/footer.php'; ?>