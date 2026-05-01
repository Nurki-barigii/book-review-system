<?php include '../includes/auth_check.php'; ?>
<?php include '../includes/header.php'; ?>

<!-- Hero Section -->
<div class="hero-section" style="
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.9), rgba(118, 75, 162, 0.9)),
                url('https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80');
    background-size: cover;
    background-position: center;
    padding: 4rem 2rem;
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
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
            align-items: center;
        ">
            <div class="hero-text">
                <h1 style="
                    font-size: 3.5rem;
                    font-weight: 800;
                    color: white;
                    margin-bottom: 1rem;
                    line-height: 1.1;
                ">
                    Welcome Back,<br>
                    <span style="
                        background: linear-gradient(45deg, #fbbf24, #f59e0b);
                        -webkit-background-clip: text;
                        -webkit-text-fill-color: transparent;
                        background-clip: text;
                    "><?php echo htmlspecialchars($_SESSION['name']); ?></span>
                </h1>
                
                <p style="
                    font-size: 1.3rem;
                    color: rgba(255, 255, 255, 0.9);
                    margin-bottom: 2rem;
                    line-height: 1.6;
                ">
                    Manage your profile, track your reading activity, and explore your contributions to our book community.
                </p>
            </div>
            
            <div class="profile-avatar" style="
                display: flex;
                justify-content: center;
            ">
                <div style="
                    background: rgba(255, 255, 255, 0.15);
                    backdrop-filter: blur(20px);
                    border: 2px solid rgba(255, 255, 255, 0.3);
                    width: 200px;
                    height: 200px;
                    border-radius: 50%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    color: white;
                    font-size: 4rem;
                    font-weight: 700;
                    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
                ">
                    <?php echo strtoupper(substr($_SESSION['name'], 0, 1)); ?>
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
        $stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch();
        
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM reviews WHERE user_id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $review_count = $stmt->fetchColumn();
        
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM reviews WHERE user_id = ? AND status = 'approved'");
        $stmt->execute([$_SESSION['user_id']]);
        $approved_reviews = $stmt->fetchColumn();
        
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM reviews WHERE user_id = ? AND status = 'pending'");
        $stmt->execute([$_SESSION['user_id']]);
        $pending_reviews = $stmt->fetchColumn();
        ?>
        
        <div class="profile-grid" style="
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            margin-bottom: 2rem;
        ">
            <!-- Personal Information Card -->
            <div class="profile-card" style="
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
                        background: linear-gradient(135deg, #667eea, #764ba2);
                        width: 60px;
                        height: 60px;
                        border-radius: 12px;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        color: white;
                        font-size: 1.5rem;
                    ">
                        <i class="fas fa-user-circle"></i>
                    </div>
                    <div>
                        <h2 style="
                            color: #1f2937;
                            font-size: 1.5rem;
                            font-weight: 700;
                            margin: 0 0 0.25rem 0;
                        ">Personal Information</h2>
                        <p style="color: #6b7280; margin: 0;">Your account details and membership information</p>
                    </div>
                </div>
                
                <div style="display: flex; flex-direction: column; gap: 1.25rem;">
                    <div style="
                        display: flex;
                        justify-content: space-between;
                        align-items: center;
                        padding: 1rem 1.5rem;
                        background: #f8fafc;
                        border-radius: 12px;
                        border: 1px solid #e5e7eb;
                    ">
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <i class="fas fa-user" style="color: #667eea; font-size: 1.1rem;"></i>
                            <span style="font-weight: 600; color: #374151;">Full Name</span>
                        </div>
                        <span style="color: #6b7280; font-weight: 500;"><?php echo htmlspecialchars($user['name']); ?></span>
                    </div>
                    
                    <div style="
                        display: flex;
                        justify-content: space-between;
                        align-items: center;
                        padding: 1rem 1.5rem;
                        background: #f8fafc;
                        border-radius: 12px;
                        border: 1px solid #e5e7eb;
                    ">
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <i class="fas fa-envelope" style="color: #667eea; font-size: 1.1rem;"></i>
                            <span style="font-weight: 600; color: #374151;">Email Address</span>
                        </div>
                        <span style="color: #6b7280; font-weight: 500;"><?php echo htmlspecialchars($user['email']); ?></span>
                    </div>
                    
                    <div style="
                        display: flex;
                        justify-content: space-between;
                        align-items: center;
                        padding: 1rem 1.5rem;
                        background: #f8fafc;
                        border-radius: 12px;
                        border: 1px solid #e5e7eb;
                    ">
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <i class="fas fa-shield-alt" style="color: #667eea; font-size: 1.1rem;"></i>
                            <span style="font-weight: 600; color: #374151;">Account Role</span>
                        </div>
                        <span style="
                            background: <?php echo $user['role'] == 'admin' ? 'linear-gradient(135deg, #dc2626, #ef4444)' : 'linear-gradient(135deg, #4f46e5, #7c3aed)'; ?>;
                            color: white;
                            padding: 0.5rem 1rem;
                            border-radius: 20px;
                            font-size: 0.875rem;
                            font-weight: 700;
                        ">
                            <?php echo ucfirst($user['role']); ?>
                        </span>
                    </div>
                    
                    <div style="
                        display: flex;
                        justify-content: space-between;
                        align-items: center;
                        padding: 1rem 1.5rem;
                        background: #f8fafc;
                        border-radius: 12px;
                        border: 1px solid #e5e7eb;
                    ">
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <i class="fas fa-calendar-plus" style="color: #667eea; font-size: 1.1rem;"></i>
                            <span style="font-weight: 600; color: #374151;">Member Since</span>
                        </div>
                        <span style="color: #6b7280; font-weight: 500;"><?php echo date('F j, Y', strtotime($user['created_at'])); ?></span>
                    </div>
                </div>
            </div>

            <!-- Activity Stats Card -->
            <div class="profile-card" style="
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
                        background: linear-gradient(135deg, #f093fb, #f5576c);
                        width: 60px;
                        height: 60px;
                        border-radius: 12px;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        color: white;
                        font-size: 1.5rem;
                    ">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                    <div>
                        <h2 style="
                            color: #1f2937;
                            font-size: 1.5rem;
                            font-weight: 700;
                            margin: 0 0 0.25rem 0;
                        ">Reading Activity</h2>
                        <p style="color: #6b7280; margin: 0;">Your contributions and review statistics</p>
                    </div>
                </div>
                
                <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                    <!-- Total Reviews -->
                    <div style="
                        background: linear-gradient(135deg, #667eea, #764ba2);
                        color: white;
                        padding: 2rem;
                        border-radius: 16px;
                        text-align: center;
                        position: relative;
                        overflow: hidden;
                    ">
                        <div style="
                            position: absolute;
                            top: -20px;
                            right: -20px;
                            width: 80px;
                            height: 80px;
                            background: rgba(255, 255, 255, 0.1);
                            border-radius: 50%;
                        "></div>
                        <div style="font-size: 3rem; font-weight: 800; margin-bottom: 0.5rem; position: relative; z-index: 2;">
                            <?php echo $review_count; ?>
                        </div>
                        <div style="font-size: 1.1rem; font-weight: 600; position: relative; z-index: 2;">
                            Total Reviews Written
                        </div>
                    </div>
                    
                    <!-- Review Status Grid -->
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div style="
                            background: #f0f9ff;
                            padding: 1.5rem;
                            border-radius: 12px;
                            text-align: center;
                            border: 2px solid #bae6fd;
                        ">
                            <div style="
                                font-size: 2rem;
                                font-weight: 800;
                                color: #16a34a;
                                margin-bottom: 0.5rem;
                            ">
                                <?php echo $approved_reviews; ?>
                            </div>
                            <div style="
                                color: #15803d;
                                font-weight: 600;
                                font-size: 0.9rem;
                            ">
                                Approved Reviews
                            </div>
                        </div>
                        
                        <div style="
                            background: #fffbeb;
                            padding: 1.5rem;
                            border-radius: 12px;
                            text-align: center;
                            border: 2px solid #fde68a;
                        ">
                            <div style="
                                font-size: 2rem;
                                font-weight: 800;
                                color: #d97706;
                                margin-bottom: 0.5rem;
                            ">
                                <?php echo $pending_reviews; ?>
                            </div>
                            <div style="
                                color: #92400e;
                                font-weight: 600;
                                font-size: 0.9rem;
                            ">
                                Pending Reviews
                            </div>
                        </div>
                    </div>
                    
                    <!-- Action Button -->
                    <a href="my_reviews.php" style="
                        background: linear-gradient(135deg, #667eea, #764ba2);
                        color: white;
                        padding: 1rem 2rem;
                        border-radius: 12px;
                        text-decoration: none;
                        font-weight: 600;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        gap: 0.75rem;
                        transition: all 0.3s ease;
                        margin-top: 0.5rem;
                    ">
                        <i class="fas fa-star"></i>
                        View All My Reviews
                    </a>
                </div>
            </div>
        </div>

        <!-- Quick Actions Section -->
        <div class="quick-actions" style="
            background: white;
            border-radius: 20px;
            padding: 2.5rem;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
            border: 1px solid #e5e7eb;
        ">
            <h2 style="
                color: #1f2937;
                font-size: 1.5rem;
                font-weight: 700;
                margin-bottom: 2rem;
                display: flex;
                align-items: center;
                gap: 0.75rem;
            ">
                <i class="fas fa-bolt"></i>
                Quick Actions
            </h2>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem;">
                <a href="../books/list.php" style="
                    background: #f8fafc;
                    padding: 1.5rem;
                    border-radius: 12px;
                    text-decoration: none;
                    color: #374151;
                    border: 2px solid #e5e7eb;
                    transition: all 0.3s ease;
                    display: flex;
                    align-items: center;
                    gap: 1rem;
                ">
                    <div style="
                        background: linear-gradient(135deg, #667eea, #764ba2);
                        width: 50px;
                        height: 50px;
                        border-radius: 10px;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        color: white;
                        font-size: 1.25rem;
                    ">
                        <i class="fas fa-book-open"></i>
                    </div>
                    <div>
                        <div style="font-weight: 600; margin-bottom: 0.25rem;">Browse Books</div>
                        <div style="color: #6b7280; font-size: 0.875rem;">Explore our book collection</div>
                    </div>
                </a>
                
                <a href="my_reviews.php" style="
                    background: #f8fafc;
                    padding: 1.5rem;
                    border-radius: 12px;
                    text-decoration: none;
                    color: #374151;
                    border: 2px solid #e5e7eb;
                    transition: all 0.3s ease;
                    display: flex;
                    align-items: center;
                    gap: 1rem;
                ">
                    <div style="
                        background: linear-gradient(135deg, #f093fb, #f5576c);
                        width: 50px;
                        height: 50px;
                        border-radius: 10px;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        color: white;
                        font-size: 1.25rem;
                    ">
                        <i class="fas fa-star"></i>
                    </div>
                    <div>
                        <div style="font-weight: 600; margin-bottom: 0.25rem;">My Reviews</div>
                        <div style="color: #6b7280; font-size: 0.875rem;">Manage your book reviews</div>
                    </div>
                </a>
                
                <a href="../books/search.php" style="
                    background: #f8fafc;
                    padding: 1.5rem;
                    border-radius: 12px;
                    text-decoration: none;
                    color: #374151;
                    border: 2px solid #e5e7eb;
                    transition: all 0.3s ease;
                    display: flex;
                    align-items: center;
                    gap: 1rem;
                ">
                    <div style="
                        background: linear-gradient(135deg, #4facfe, #00f2fe);
                        width: 50px;
                        height: 50px;
                        border-radius: 10px;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        color: white;
                        font-size: 1.25rem;
                    ">
                        <i class="fas fa-search"></i>
                    </div>
                    <div>
                        <div style="font-weight: 600; margin-bottom: 0.25rem;">Search Books</div>
                        <div style="color: #6b7280; font-size: 0.875rem;">Find specific books</div>
                    </div>
                </a>
                
                <?php if($user['role'] == 'admin'): ?>
                <a href="../auth/admin/dashboard.php" style="
                    background: #f8fafc;
                    padding: 1.5rem;
                    border-radius: 12px;
                    text-decoration: none;
                    color: #374151;
                    border: 2px solid #e5e7eb;
                    transition: all 0.3s ease;
                    display: flex;
                    align-items: center;
                    gap: 1rem;
                ">
                    <div style="
                        background: linear-gradient(135deg, #43e97b, #38f9d7);
                        width: 50px;
                        height: 50px;
                        border-radius: 10px;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        color: white;
                        font-size: 1.25rem;
                    ">
                        <i class="fas fa-crown"></i>
                    </div>
                    <div>
                        <div style="font-weight: 600; margin-bottom: 0.25rem;">Admin Panel</div>
                        <div style="color: #6b7280; font-size: 0.875rem;">Manage the platform</div>
                    </div>
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
    /* Hover Effects */
    .profile-card {
        transition: all 0.3s ease;
    }

    .profile-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
    }

    .quick-actions a:hover {
        background: linear-gradient(135deg, #667eea, #764ba2) !important;
        color: white !important;
        border-color: transparent !important;
        transform: translateY(-3px);
    }

    .quick-actions a:hover div:first-child {
        background: rgba(255, 255, 255, 0.2) !important;
    }

    /* Smooth Transitions */
    .profile-card,
    .quick-actions a {
        transition: all 0.3s ease;
    }

    /* Responsive Design */
    @media (max-width: 1024px) {
        .hero-content {
            grid-template-columns: 1fr;
            gap: 2rem;
            text-align: center;
        }
        
        .profile-grid {
            grid-template-columns: 1fr;
        }
        
        .quick-actions > div {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 768px) {
        .hero-section h1 {
            font-size: 2.5rem;
        }
        
        .profile-avatar div {
            width: 150px !important;
            height: 150px !important;
            font-size: 3rem !important;
        }
        
        .quick-actions > div {
            grid-template-columns: 1fr;
        }
        
        .profile-card {
            padding: 2rem !important;
        }
    }

    @media (max-width: 480px) {
        .hero-section {
            padding: 2rem 1rem;
        }
        
        .main-content-wide {
            padding: 1.5rem 1rem;
        }
        
        .profile-card {
            padding: 1.5rem !important;
        }
        
        .quick-actions {
            padding: 1.5rem !important;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add hover effects to profile cards
        const profileCards = document.querySelectorAll('.profile-card');
        profileCards.forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-5px)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });

        // Add click effects to quick action links
        const quickActions = document.querySelectorAll('.quick-actions a');
        quickActions.forEach(action => {
            action.addEventListener('click', function(e) {
                // Add a small loading effect
                const originalContent = this.innerHTML;
                this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading...';
                
                setTimeout(() => {
                    this.innerHTML = originalContent;
                }, 1000);
            });
        });
    });
</script>

<?php include '../includes/footer.php'; ?>