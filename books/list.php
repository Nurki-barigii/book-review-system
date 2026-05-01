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
            grid-template-columns: 2fr 1fr;
            gap: 4rem;
            align-items: center;
        ">
            <div class="hero-text">
                <h1 style="
                    font-size: 3.5rem;
                    font-weight: 800;
                    color: white;
                    margin-bottom: 1.5rem;
                    line-height: 1.1;
                ">
                    Explore Our<br>
                    <span style="
                        background: linear-gradient(45deg, #fbbf24, #f59e0b);
                        -webkit-background-clip: text;
                        -webkit-text-fill-color: transparent;
                        background-clip: text;
                    ">Book Collection</span>
                </h1>
                
                <p style="
                    font-size: 1.3rem;
                    color: rgba(255, 255, 255, 0.9);
                    margin-bottom: 2.5rem;
                    line-height: 1.6;
                    max-width: 600px;
                ">
                    Discover thousands of books from various genres and authors. 
                    Find your next favorite read in our carefully curated collection.
                </p>
            </div>
            
            <div class="hero-stats" style="
                background: rgba(255, 255, 255, 0.1);
                backdrop-filter: blur(20px);
                border: 1px solid rgba(255, 255, 255, 0.2);
                border-radius: 20px;
                padding: 2.5rem;
                color: white;
            ">
                <h3 style="
                    font-size: 1.5rem;
                    margin-bottom: 2rem;
                    font-weight: 600;
                ">Collection Stats</h3>
                
                <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-size: 1.1rem;">Total Books</span>
                        <span style="
                            font-size: 2rem;
                            font-weight: 700;
                            color: #fbbf24;
                        "><?php
                            $stmt = $pdo->query("SELECT COUNT(*) FROM books");
                            echo number_format($stmt->fetchColumn());
                        ?>+</span>
                    </div>
                    
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-size: 1.1rem;">Categories</span>
                        <span style="
                            font-size: 2rem;
                            font-weight: 700;
                            color: #fbbf24;
                        ">25+</span>
                    </div>
                    
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-size: 1.1rem;">Active Readers</span>
                        <span style="
                            font-size: 2rem;
                            font-weight: 700;
                            color: #fbbf24;
                        "><?php
                            $stmt = $pdo->query("SELECT COUNT(*) FROM users");
                            echo number_format($stmt->fetchColumn());
                        ?>+</span>
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
        <!-- Search and Filter Section -->
        <div class="search-filter-section" style="
            background: white;
            border-radius: 16px;
            padding: 2rem;
            margin-bottom: 3rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border: 1px solid #e5e7eb;
        ">
            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem; align-items: end;">
                <!-- Search Form -->
                <div>
                    <form method="GET" class="search-form-wide">
                        <div style="margin-bottom: 1rem;">
                            <label style="
                                display: block;
                                color: #1f2937;
                                font-weight: 600;
                                margin-bottom: 0.5rem;
                                font-size: 1.1rem;
                            ">Search Books</label>
                            <div style="
                                display: flex;
                                background: #f8fafc;
                                border-radius: 12px;
                                border: 2px solid #e5e7eb;
                                overflow: hidden;
                                transition: all 0.3s ease;
                            ">
                                <input 
                                    type="text" 
                                    name="search" 
                                    placeholder="Search by title, author, or category..." 
                                    value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>"
                                    style="
                                        flex: 1;
                                        border: none;
                                        padding: 1rem 1.5rem;
                                        background: transparent;
                                        font-size: 1rem;
                                    "
                                >
                                <button type="submit" style="
                                    background: linear-gradient(135deg, #667eea, #764ba2);
                                    color: white;
                                    border: none;
                                    padding: 1rem 2rem;
                                    font-weight: 600;
                                    cursor: pointer;
                                    transition: all 0.3s ease;
                                    display: flex;
                                    align-items: center;
                                    gap: 0.5rem;
                                ">
                                    <i class="fas fa-search"></i>
                                    Search
                                </button>
                            </div>
                        </div>
                    </form>

                    <?php
                    // Initialize books variable
                    $books = [];
                    
                    if(isset($_GET['search']) && !empty($_GET['search'])) {
                        $search = "%{$_GET['search']}%";
                        $stmt = $pdo->prepare("SELECT * FROM books WHERE title LIKE ? OR author LIKE ? OR category LIKE ? ORDER BY created_at DESC LIMIT 24");
                        $stmt->execute([$search, $search, $search]);
                        $books = $stmt->fetchAll();
                    } else {
                        // If no search, get all books
                        $stmt = $pdo->prepare("SELECT * FROM books ORDER BY created_at DESC LIMIT 24");
                        $stmt->execute();
                        $books = $stmt->fetchAll();
                    }
                    ?>

                    <?php if(isset($_GET['search']) && !empty($_GET['search'])): ?>
                    <div style="
                        display: flex;
                        justify-content: space-between;
                        align-items: center;
                        padding: 1rem 1.5rem;
                        background: #f0f9ff;
                        border-radius: 10px;
                        border-left: 4px solid #4f46e5;
                    ">
                        <div style="display: flex; align-items: center; gap: 1rem;">
                            <span style="
                                background: #4f46e5;
                                color: white;
                                padding: 0.5rem 1rem;
                                border-radius: 20px;
                                font-weight: 600;
                                font-size: 0.9rem;
                            ">
                                <?php echo count($books); ?> results
                            </span>
                            <span style="color: #6b7280; font-weight: 500;">
                                for "<?php echo htmlspecialchars($_GET['search']); ?>"
                            </span>
                        </div>
                        <a href="list.php" style="
                            color: #6b7280;
                            text-decoration: none;
                            display: flex;
                            align-items: center;
                            gap: 0.5rem;
                            padding: 0.5rem 1rem;
                            border-radius: 6px;
                            transition: all 0.2s ease;
                        ">
                            <i class="fas fa-times"></i>
                            Clear
                        </a>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Quick Actions -->
                <div>
                    <div style="display: flex; gap: 1rem; justify-content: flex-end;">
                        <a href="search.php" style="
                            background: linear-gradient(135deg, #667eea, #764ba2);
                            color: white;
                            padding: 1rem 1.5rem;
                            border-radius: 10px;
                            text-decoration: none;
                            font-weight: 600;
                            display: flex;
                            align-items: center;
                            gap: 0.75rem;
                            transition: all 0.3s ease;
                        ">
                            <i class="fas fa-sliders-h"></i>
                            Advanced Search
                        </a>
                    </div>
                </div>
            </div>

            <!-- Quick Categories -->
            <div style="margin-top: 2rem;">
                <h3 style="
                    color: #1f2937;
                    margin-bottom: 1rem;
                    font-size: 1.1rem;
                    font-weight: 600;
                ">Browse Categories</h3>
                <div class="categories-scroll-wide" style="
                    display: flex;
                    gap: 1rem;
                    overflow-x: auto;
                    padding-bottom: 0.5rem;
                ">
                    <a href="list.php" class="category-tag-wide <?php echo !isset($_GET['search']) ? 'active' : ''; ?>" style="
                        background: <?php echo !isset($_GET['search']) ? 'linear-gradient(135deg, #667eea, #764ba2)' : '#f8fafc'; ?>;
                        color: <?php echo !isset($_GET['search']) ? 'white' : '#64748b'; ?>;
                        padding: 0.75rem 1.5rem;
                        border-radius: 25px;
                        text-decoration: none;
                        font-weight: 600;
                        white-space: nowrap;
                        border: 2px solid <?php echo !isset($_GET['search']) ? 'transparent' : '#e2e8f0'; ?>;
                        transition: all 0.3s ease;
                    ">
                        All Books
                    </a>
                    <a href="list.php?search=Fiction" class="category-tag-wide" style="
                        background: #f8fafc;
                        color: #64748b;
                        padding: 0.75rem 1.5rem;
                        border-radius: 25px;
                        text-decoration: none;
                        font-weight: 600;
                        white-space: nowrap;
                        border: 2px solid #e2e8f0;
                        transition: all 0.3s ease;
                    ">
                        Fiction
                    </a>
                    <a href="list.php?search=Science Fiction" class="category-tag-wide" style="
                        background: #f8fafc;
                        color: #64748b;
                        padding: 0.75rem 1.5rem;
                        border-radius: 25px;
                        text-decoration: none;
                        font-weight: 600;
                        white-space: nowrap;
                        border: 2px solid #e2e8f0;
                        transition: all 0.3s ease;
                    ">
                        Science Fiction
                    </a>
                    <a href="list.php?search=Mystery" class="category-tag-wide" style="
                        background: #f8fafc;
                        color: #64748b;
                        padding: 0.75rem 1.5rem;
                        border-radius: 25px;
                        text-decoration: none;
                        font-weight: 600;
                        white-space: nowrap;
                        border: 2px solid #e2e8f0;
                        transition: all 0.3s ease;
                    ">
                        Mystery
                    </a>
                    <a href="list.php?search=Romance" class="category-tag-wide" style="
                        background: #f8fafc;
                        color: #64748b;
                        padding: 0.75rem 1.5rem;
                        border-radius: 25px;
                        text-decoration: none;
                        font-weight: 600;
                        white-space: nowrap;
                        border: 2px solid #e2e8f0;
                        transition: all 0.3s ease;
                    ">
                        Romance
                    </a>
                    <a href="list.php?search=Fantasy" class="category-tag-wide" style="
                        background: #f8fafc;
                        color: #64748b;
                        padding: 0.75rem 1.5rem;
                        border-radius: 25px;
                        text-decoration: none;
                        font-weight: 600;
                        white-space: nowrap;
                        border: 2px solid #e2e8f0;
                        transition: all 0.3s ease;
                    ">
                        Fantasy
                    </a>
                </div>
            </div>
        </div>

        <!-- Books Grid -->
        <div class="books-section-wide">
            <?php if(count($books) > 0): ?>
                <div class="books-grid-wide" style="
                    display: grid;
                    grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
                    gap: 2rem;
                    margin-bottom: 3rem;
                ">
                    <?php foreach($books as $book): 
                        $rating_stmt = $pdo->prepare("SELECT AVG(rating) as avg_rating, COUNT(*) as review_count FROM reviews WHERE book_id = ? AND status = 'approved'");
                        $rating_stmt->execute([$book['book_id']]);
                        $rating_data = $rating_stmt->fetch();
                        $avg_rating = $rating_data['avg_rating'] ? round($rating_data['avg_rating'], 1) : null;
                        $review_count = $rating_data['review_count'] ?: 0;
                    ?>
                        <div class="book-card-modern" style="
                            background: white;
                            border-radius: 16px;
                            overflow: hidden;
                            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
                            transition: all 0.3s ease;
                            border: 1px solid #e5e7eb;
                            display: flex;
                            flex-direction: column;
                            height: 100%;
                        ">
                            <div style="
                                background: linear-gradient(135deg, #667eea, #764ba2);
                                color: white;
                                padding: 1.5rem;
                                position: relative;
                                overflow: hidden;
                                flex-shrink: 0;
                            ">
                                <div style="
                                    position: absolute;
                                    top: -30px;
                                    right: -30px;
                                    width: 100px;
                                    height: 100px;
                                    background: rgba(255, 255, 255, 0.1);
                                    border-radius: 50%;
                                "></div>
                                <h3 style="
                                    margin: 0;
                                    font-size: 1.3rem;
                                    line-height: 1.4;
                                    position: relative;
                                    z-index: 2;
                                    font-weight: 600;
                                "><?php echo htmlspecialchars($book['title']); ?></h3>
                                <p style="
                                    margin: 0.5rem 0 0 0;
                                    opacity: 0.9;
                                    font-size: 0.95rem;
                                    position: relative;
                                    z-index: 2;
                                    display: flex;
                                    align-items: center;
                                    gap: 0.5rem;
                                ">
                                    <i class="fas fa-user-pen"></i>
                                    <?php echo htmlspecialchars($book['author']); ?>
                                </p>
                            </div>
                            
                            <div style="padding: 1.5rem; flex-grow: 1; display: flex; flex-direction: column;">
                                <div style="margin-bottom: 1rem; flex-grow: 1;">
                                    <p style="
                                        color: #374151;
                                        font-size: 0.95rem;
                                        line-height: 1.5;
                                        margin-bottom: 1rem;
                                    ">
                                        <?php echo substr(htmlspecialchars($book['description']), 0, 120); ?>...
                                    </p>
                                </div>
                                
                                <div style="
                                    display: flex;
                                    justify-content: space-between;
                                    align-items: center;
                                    margin-bottom: 1rem;
                                    padding-top: 1rem;
                                    border-top: 1px solid #f1f5f9;
                                ">
                                    <?php if($avg_rating): ?>
                                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                                        <div style="display: flex; gap: 0.1rem;">
                                            <?php for($i = 1; $i <= 5; $i++): ?>
                                                <i class="fas fa-star" style="
                                                    color: <?php echo $i <= $avg_rating ? '#fbbf24' : '#e5e7eb'; ?>;
                                                    font-size: 0.9rem;
                                                "></i>
                                            <?php endfor; ?>
                                        </div>
                                        <span style="
                                            color: #1f2937;
                                            font-weight: 600;
                                            font-size: 0.9rem;
                                        "><?php echo $avg_rating; ?></span>
                                        <span style="color: #9ca3af; font-size: 0.8rem;">
                                            (<?php echo $review_count; ?>)
                                        </span>
                                    </div>
                                    <?php else: ?>
                                    <div style="display: flex; align-items: center; gap: 0.5rem; color: #9ca3af; font-size: 0.9rem;">
                                        <i class="fas fa-star"></i>
                                        <span>No ratings yet</span>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <span style="
                                        background: #f1f5f9;
                                        color: #475569;
                                        padding: 0.4rem 1rem;
                                        border-radius: 20px;
                                        font-size: 0.875rem;
                                        font-weight: 600;
                                        border: 1px solid #e2e8f0;
                                    ">
                                        <?php echo htmlspecialchars($book['category']); ?>
                                    </span>
                                </div>
                                
                                <div style="
                                    display: flex;
                                    justify-content: space-between;
                                    align-items: center;
                                ">
                                    <span style="color: #6b7280; font-size: 0.9rem; display: flex; align-items: center; gap: 0.5rem;">
                                        <i class="fas fa-calendar"></i>
                                        <?php echo htmlspecialchars($book['year']); ?>
                                    </span>
                                    
                                    <a href="view.php?id=<?php echo $book['book_id']; ?>" style="
                                        background: linear-gradient(135deg, #667eea, #764ba2);
                                        color: white;
                                        padding: 0.75rem 1.5rem;
                                        border-radius: 10px;
                                        text-decoration: none;
                                        font-weight: 600;
                                        font-size: 0.9rem;
                                        transition: all 0.3s ease;
                                        display: flex;
                                        align-items: center;
                                        gap: 0.5rem;
                                    ">
                                        <i class="fas fa-eye"></i>
                                        View Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Load More -->
                <div style="text-align: center;">
                    <button class="load-more-btn" style="
                        background: transparent;
                        border: 2px solid #667eea;
                        color: #667eea;
                        padding: 1rem 2.5rem;
                        border-radius: 10px;
                        font-weight: 600;
                        font-size: 1rem;
                        cursor: pointer;
                        transition: all 0.3s ease;
                        display: inline-flex;
                        align-items: center;
                        gap: 0.75rem;
                    ">
                        <i class="fas fa-chevron-down"></i>
                        Load More Books
                    </button>
                </div>
                
            <?php else: ?>
                <div class="empty-state-modern" style="
                    text-align: center;
                    padding: 4rem 2rem;
                    background: white;
                    border-radius: 16px;
                    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
                    border: 1px solid #e5e7eb;
                ">
                    <div style="font-size: 4rem; color: #e5e7eb; margin-bottom: 1.5rem;">
                        <i class="fas fa-search"></i>
                    </div>
                    <h3 style="
                        color: #6b7280;
                        font-size: 1.5rem;
                        margin-bottom: 0.5rem;
                    ">No books found</h3>
                    <p style="
                        color: #9ca3af;
                        margin-bottom: 2rem;
                        font-size: 1.1rem;
                    ">Try a different search term or browse all categories</p>
                    <a href="list.php" style="
                        background: linear-gradient(135deg, #667eea, #764ba2);
                        color: white;
                        padding: 1rem 2rem;
                        border-radius: 10px;
                        text-decoration: none;
                        font-weight: 600;
                        display: inline-flex;
                        align-items: center;
                        gap: 0.75rem;
                        transition: all 0.3s ease;
                    ">
                        <i class="fas fa-books"></i>
                        Browse All Books
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
    /* Hover Effects */
    .category-tag-wide:hover {
        background: linear-gradient(135deg, #667eea, #764ba2) !important;
        color: white !important;
        border-color: transparent !important;
        transform: translateY(-2px);
    }

    .book-card-modern:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
    }

    .book-card-modern a:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
    }

    .load-more-btn:hover {
        background: #667eea !important;
        color: white !important;
        transform: translateY(-2px);
    }

    /* Smooth Transitions */
    .book-card-modern,
    .category-tag-wide,
    .load-more-btn,
    .search-form-wide button {
        transition: all 0.3s ease;
    }

    /* Scrollbar Styling */
    .categories-scroll-wide::-webkit-scrollbar {
        height: 6px;
    }

    .categories-scroll-wide::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 3px;
    }

    .categories-scroll-wide::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 3px;
    }

    .categories-scroll-wide::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }

    /* Responsive Design */
    @media (max-width: 1024px) {
        .hero-content {
            grid-template-columns: 1fr;
            gap: 2rem;
        }
        
        .books-grid-wide {
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        }
        
        .search-filter-section > div {
            grid-template-columns: 1fr;
            gap: 1.5rem;
        }
    }

    @media (max-width: 768px) {
        .hero-section h1 {
            font-size: 2.5rem;
        }
        
        .books-grid-wide {
            grid-template-columns: 1fr;
        }
        
        .categories-scroll-wide {
            gap: 0.5rem;
        }
        
        .category-tag-wide {
            padding: 0.6rem 1.2rem !important;
            font-size: 0.9rem;
        }
    }

    @media (max-width: 480px) {
        .hero-section {
            padding: 2rem 1rem;
        }
        
        .main-content-wide {
            padding: 1.5rem 1rem;
        }
        
        .search-filter-section {
            padding: 1.5rem;
        }
        
        .search-form-wide button {
            padding: 1rem 1.5rem !important;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Category scroll behavior
        const categoriesScroll = document.querySelector('.categories-scroll-wide');
        if (categoriesScroll) {
            categoriesScroll.addEventListener('wheel', (e) => {
                if (e.deltaY !== 0) {
                    e.preventDefault();
                    categoriesScroll.scrollLeft += e.deltaY;
                }
            });
        }

        // Load more functionality
        const loadMoreBtn = document.querySelector('.load-more-btn');
        if (loadMoreBtn) {
            loadMoreBtn.addEventListener('click', function() {
                const originalText = this.innerHTML;
                this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading...';
                this.disabled = true;

                setTimeout(() => {
                    this.innerHTML = originalText;
                    this.disabled = false;
                    // In real implementation, load more books here
                }, 1500);
            });
        }

        // Search input focus effect
        const searchInput = document.querySelector('input[name="search"]');
        if (searchInput) {
            const searchContainer = searchInput.parentElement;
            
            searchInput.addEventListener('focus', function() {
                searchContainer.style.borderColor = '#667eea';
                searchContainer.style.boxShadow = '0 0 0 3px rgba(102, 126, 234, 0.1)';
            });
            
            searchInput.addEventListener('blur', function() {
                searchContainer.style.borderColor = '#e5e7eb';
                searchContainer.style.boxShadow = 'none';
            });
        }
    });
</script>

<?php include '../includes/footer.php'; ?>