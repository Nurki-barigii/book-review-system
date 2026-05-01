<?php include '../includes/auth_check.php'; ?>
<?php include '../includes/header.php'; ?>

<!-- Hero Section -->
<div class="hero-section" style="
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.9), rgba(118, 75, 162, 0.9)),
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
                    My Book Reviews
                </h1>
                
                <p style="
                    font-size: 1.2rem;
                    color: rgba(255, 255, 255, 0.9);
                    margin-bottom: 2rem;
                    line-height: 1.6;
                ">
                    Manage and view all your book reviews in one place. Track your reading journey and share your thoughts with our community.
                </p>
                
                <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                    <a href="../books/list.php" class="btn btn-primary" style="
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
                        Browse Books
                    </a>
                    
                    <a href="../books/search.php" class="btn btn-outline" style="
                        background: transparent;
                        border: 1px solid rgba(255, 255, 255, 0.5);
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
                        <i class="fas fa-search"></i>
                        Search Books
                    </a>
                </div>
            </div>
            
            <div class="review-stats" style="
                background: rgba(255, 255, 255, 0.15);
                backdrop-filter: blur(20px);
                border: 1px solid rgba(255, 255, 255, 0.3);
                padding: 2rem;
                border-radius: 16px;
                color: white;
                text-align: center;
                min-width: 200px;
            ">
                <?php
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
                
                <div style="font-size: 3rem; font-weight: 800; margin-bottom: 0.5rem;">
                    <?php echo $review_count; ?>
                </div>
                <div style="font-size: 1rem; font-weight: 600; margin-bottom: 1.5rem;">
                    Total Reviews
                </div>
                
                <div style="display: flex; justify-content: space-between; gap: 1rem;">
                    <div>
                        <div style="font-size: 1.5rem; font-weight: 700; color: #10b981;"><?php echo $approved_reviews; ?></div>
                        <div style="font-size: 0.8rem;">Approved</div>
                    </div>
                    <div>
                        <div style="font-size: 1.5rem; font-weight: 700; color: #f59e0b;"><?php echo $pending_reviews; ?></div>
                        <div style="font-size: 0.8rem;">Pending</div>
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
        <?php
        $stmt = $pdo->prepare("SELECT r.*, b.title, b.author, b.cover_image FROM reviews r JOIN books b ON r.book_id = b.book_id WHERE r.user_id = ? ORDER BY r.created_at DESC");
        $stmt->execute([$_SESSION['user_id']]);
        $reviews = $stmt->fetchAll();
        ?>
        
        <?php if(count($reviews) > 0): ?>
            <div class="reviews-grid" style="
                display: grid;
                grid-template-columns: 1fr;
                gap: 1.5rem;
            ">
                <?php foreach($reviews as $review): ?>
                    <div class="review-card" style="
                        background: white;
                        border-radius: 16px;
                        padding: 2rem;
                        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
                        border: 1px solid #e5e7eb;
                        transition: all 0.3s ease;
                    ">
                        <div style="
                            display: grid;
                            grid-template-columns: 120px 1fr auto;
                            gap: 1.5rem;
                            align-items: start;
                        ">
                            <!-- Book Cover -->
                            <div style="
                                width: 120px;
                                height: 160px;
                                border-radius: 8px;
                                overflow: hidden;
                                box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
                            ">
                                <?php if($review['cover_image']): ?>
                                    <img src="../covers/<?php echo htmlspecialchars($review['cover_image']); ?>" 
                                         alt="<?php echo htmlspecialchars($review['title']); ?>" 
                                         style="width: 100%; height: 100%; object-fit: cover;">
                                <?php else: ?>
                                    <div style="
                                        width: 100%;
                                        height: 100%;
                                        background: linear-gradient(135deg, #667eea, #764ba2);
                                        display: flex;
                                        align-items: center;
                                        justify-content: center;
                                        color: white;
                                        font-size: 2rem;
                                    ">
                                        <i class="fas fa-book"></i>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Review Content -->
                            <div style="flex: 1;">
                                <div style="
                                    display: flex;
                                    justify-content: space-between;
                                    align-items: flex-start;
                                    margin-bottom: 1rem;
                                ">
                                    <div>
                                        <h3 style="
                                            color: #1f2937;
                                            font-size: 1.5rem;
                                            font-weight: 700;
                                            margin: 0 0 0.5rem 0;
                                        "><?php echo htmlspecialchars($review['title']); ?></h3>
                                        <p style="
                                            color: #6b7280;
                                            margin: 0;
                                            font-size: 1rem;
                                        ">
                                            <i class="fas fa-user-pen"></i>
                                            By <?php echo htmlspecialchars($review['author']); ?>
                                        </p>
                                    </div>
                                    
                                    <div style="display: flex; align-items: center; gap: 1rem;">
                                        <div style="display: flex; gap: 0.25rem;">
                                            <?php for($i = 1; $i <= 5; $i++): ?>
                                                <i class="fas fa-star" style="color: <?php echo $i <= $review['rating'] ? '#fbbf24' : '#e5e7eb'; ?>"></i>
                                            <?php endfor; ?>
                                        </div>
                                        <span style="
                                            background: <?php 
                                                if($review['status'] == 'approved') echo '#10b981';
                                                elseif($review['status'] == 'pending') echo '#f59e0b';
                                                else echo '#ef4444';
                                            ?>;
                                            color: white;
                                            padding: 0.4rem 1rem;
                                            border-radius: 20px;
                                            font-size: 0.8rem;
                                            font-weight: 700;
                                            text-transform: uppercase;
                                            letter-spacing: 0.5px;
                                        ">
                                            <?php echo ucfirst($review['status']); ?>
                                        </span>
                                    </div>
                                </div>
                                
                                <div style="
                                    background: #f8fafc;
                                    padding: 1.25rem;
                                    border-radius: 10px;
                                    margin-bottom: 1.5rem;
                                    border-left: 4px solid #667eea;
                                ">
                                    <p style="
                                        color: #374151;
                                        line-height: 1.6;
                                        margin: 0;
                                        font-size: 1rem;
                                    "><?php echo htmlspecialchars($review['comment']); ?></p>
                                </div>
                                
                                <div style="
                                    display: flex;
                                    justify-content: space-between;
                                    align-items: center;
                                ">
                                    <small style="color: #6b7280; font-weight: 500;">
                                        <i class="fas fa-clock"></i>
                                        Posted on: <?php echo date('M j, Y g:i A', strtotime($review['created_at'])); ?>
                                    </small>
                                    
                                    <div style="display: flex; gap: 1rem;">
                                        <a href="../reviews/edit_review.php?id=<?php echo $review['review_id']; ?>" 
                                           class="btn btn-secondary" 
                                           style="
                                                background: #f1f5f9;
                                                color: #475569;
                                                padding: 0.6rem 1.2rem;
                                                border-radius: 8px;
                                                text-decoration: none;
                                                font-weight: 600;
                                                display: inline-flex;
                                                align-items: center;
                                                gap: 0.5rem;
                                                transition: all 0.3s ease;
                                                border: 1px solid #e2e8f0;
                                           ">
                                            <i class="fas fa-edit"></i>
                                            Edit
                                        </a>
                                        <a href="../reviews/delete_review.php?id=<?php echo $review['review_id']; ?>" 
                                           onclick="return confirm('Are you sure you want to delete this review?')"
                                           class="btn" 
                                           style="
                                                background: #fee2e2;
                                                color: #dc2626;
                                                padding: 0.6rem 1.2rem;
                                                border-radius: 8px;
                                                text-decoration: none;
                                                font-weight: 600;
                                                display: inline-flex;
                                                align-items: center;
                                                gap: 0.5rem;
                                                transition: all 0.3s ease;
                                                border: 1px solid #fecaca;
                                           ">
                                            <i class="fas fa-trash"></i>
                                            Delete
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state" style="
                text-align: center;
                padding: 4rem 2rem;
                background: white;
                border-radius: 20px;
                box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            ">
                <div style="
                    width: 120px;
                    height: 120px;
                    background: linear-gradient(135deg, #f1f5f9, #e2e8f0);
                    border-radius: 50%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    margin: 0 auto 2rem;
                    color: #94a3b8;
                    font-size: 3rem;
                ">
                    <i class="fas fa-star"></i>
                </div>
                <h3 style="
                    color: #475569;
                    font-size: 1.75rem;
                    font-weight: 700;
                    margin-bottom: 1rem;
                ">No Reviews Yet</h3>
                <p style="
                    color: #64748b;
                    font-size: 1.1rem;
                    margin-bottom: 2rem;
                    max-width: 500px;
                    margin-left: auto;
                    margin-right: auto;
                    line-height: 1.6;
                ">
                    You haven't posted any reviews yet. Start sharing your thoughts on books and help others discover great reads!
                </p>
                <a href="../books/list.php" class="btn btn-primary" style="
                    background: linear-gradient(135deg, #667eea, #764ba2);
                    color: white;
                    padding: 0.9rem 2rem;
                    border-radius: 10px;
                    text-decoration: none;
                    font-weight: 700;
                    display: inline-flex;
                    align-items: center;
                    gap: 0.75rem;
                    transition: all 0.3s ease;
                    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
                ">
                    <i class="fas fa-book-open"></i>
                    Browse Books to Review
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
    /* Hover Effects */
    .review-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.12);
    }

    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
    }

    .btn-primary:hover {
        background: rgba(255, 255, 255, 0.3) !important;
    }

    .btn-outline:hover {
        background: rgba(255, 255, 255, 0.1) !important;
    }

    .btn-secondary:hover {
        background: #e2e8f0 !important;
    }

    .btn[style*="background: #fee2e2"]:hover {
        background: #fecaca !important;
    }

    /* Responsive Design */
    @media (max-width: 1024px) {
        .hero-content {
            grid-template-columns: 1fr;
            gap: 2rem;
            text-align: center;
        }
        
        .review-stats {
            max-width: 300px;
            margin: 0 auto;
        }
    }

    @media (max-width: 768px) {
        .hero-section h1 {
            font-size: 2.25rem;
        }
        
        .review-card > div {
            grid-template-columns: 1fr !important;
            gap: 1rem !important;
        }
        
        .review-card > div > div:first-child {
            justify-self: center;
        }
        
        .review-card > div > div:last-child {
            text-align: center;
        }
        
        .review-card > div > div:last-child > div {
            flex-direction: column;
            gap: 1rem;
        }
        
        .review-card > div > div:last-child > div > div:last-child {
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
        
        .review-card {
            padding: 1.5rem !important;
        }
        
        .hero-section .btn {
            width: 100%;
            justify-content: center;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add hover effects to review cards
        const reviewCards = document.querySelectorAll('.review-card');
        reviewCards.forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-5px)';
                this.style.boxShadow = '0 10px 25px rgba(0, 0, 0, 0.12)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
                this.style.boxShadow = '0 4px 15px rgba(0, 0, 0, 0.08)';
            });
        });

        // Add click effects to buttons
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

<?php include '../includes/footer.php'; ?>