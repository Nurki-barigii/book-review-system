<?php include 'includes/header.php'; ?>

<!-- Floating Chatbot Button -->
<div id="chatbot-toggle" style="
    position: fixed;
    bottom: 2rem;
    right: 2rem;
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, #667eea, #764ba2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.5rem;
    cursor: pointer;
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
    z-index: 1000;
    transition: all 0.3s ease;
">
    <i class="fas fa-robot"></i>
</div>

<!-- Chatbot Modal -->
<div id="chatbot-modal" style="
    position: fixed;
    bottom: 2rem;
    right: 2rem;
    width: 380px;
    height: 500px;
    background: white;
    border-radius: 20px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
    z-index: 1001;
    display: none;
    flex-direction: column;
    overflow: hidden;
    border: 1px solid #e5e7eb;
">
    <!-- Chat Header -->
    <div style="
        background: linear-gradient(135deg, #667eea, #764ba2);
        color: white;
        padding: 1.5rem;
        display: flex;
        align-items: center;
        gap: 1rem;
    ">
        <div style="
            background: rgba(255, 255, 255, 0.2);
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        ">
            <i class="fas fa-robot"></i>
        </div>
        <div style="flex: 1;">
            <h3 style="margin: 0 0 0.25rem 0; font-size: 1.1rem; font-weight: 600;">Book Finder Assistant</h3>
            <p style="margin: 0; font-size: 0.8rem; opacity: 0.9;">Ask me about books and reviews!</p>
        </div>
        <button id="close-chat" style="
            background: none;
            border: none;
            color: white;
            font-size: 1.2rem;
            cursor: pointer;
            padding: 0.5rem;
        ">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <!-- Chat Messages -->
    <div id="chat-messages" style="
        flex: 1;
        overflow-y: auto;
        padding: 1.5rem;
        display: flex;
        flex-direction: column;
        gap: 1rem;
        background: #f8fafc;
    ">
        <div class="bot-message" style="
            background: white;
            padding: 1rem;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
            font-size: 0.9rem;
            max-width: 85%;
            align-self: flex-start;
        ">
            <div style="font-weight: 600; color: #667eea; margin-bottom: 0.5rem; font-size: 0.8rem;">
                <i class="fas fa-robot"></i> Book Assistant
            </div>
            Hello! I'm here to help you discover amazing books! 📚<br><br>
            You can ask me about:
            <ul style="margin: 0.5rem 0 0 1rem; padding: 0;">
                <li>Available books and genres</li>
                <li>Book recommendations</li>
                <li>Popular books and reviews</li>
                <li>How to get started</li>
            </ul>
            Try asking: "What books are available?" or "Recommend some fiction books"
        </div>
    </div>

    <!-- Chat Input -->
    <div style="
        padding: 1.5rem;
        border-top: 1px solid #e5e7eb;
        background: white;
    ">
        <div style="display: flex; gap: 0.75rem;">
            <input type="text" id="chat-input" placeholder="Ask about books..." style="
                flex: 1;
                padding: 0.75rem 1rem;
                border: 1px solid #d1d5db;
                border-radius: 8px;
                font-size: 0.9rem;
                background: white;
            ">
            <button id="send-message" style="
                background: linear-gradient(135deg, #667eea, #764ba2);
                color: white;
                border: none;
                padding: 0.75rem 1rem;
                border-radius: 8px;
                cursor: pointer;
                font-size: 0.9rem;
                display: flex;
                align-items: center;
                gap: 0.5rem;
                transition: all 0.3s ease;
            ">
                <i class="fas fa-paper-plane"></i>
            </button>
        </div>
        <div style="display: flex; gap: 0.5rem; margin-top: 0.75rem; flex-wrap: wrap;">
            <button class="quick-question" data-question="What books are available?" style="
                background: #f1f5f9;
                border: 1px solid #e2e8f0;
                padding: 0.5rem 0.75rem;
                border-radius: 20px;
                font-size: 0.8rem;
                cursor: pointer;
                transition: all 0.3s ease;
            ">Available Books</button>
            <button class="quick-question" data-question="Show me popular books" style="
                background: #f1f5f9;
                border: 1px solid #e2e8f0;
                padding: 0.5rem 0.75rem;
                border-radius: 20px;
                font-size: 0.8rem;
                cursor: pointer;
                transition: all 0.3s ease;
            ">Popular Books</button>
            <button class="quick-question" data-question="Recommend fiction books" style="
                background: #f1f5f9;
                border: 1px solid #e2e8f0;
                padding: 0.5rem 0.75rem;
                border-radius: 20px;
                font-size: 0.8rem;
                cursor: pointer;
                transition: all 0.3s ease;
            ">Fiction</button>
        </div>
    </div>
</div>

<!-- Hero Section - Full Width -->
<div class="hero-section-wide" style="
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.9), rgba(118, 75, 162, 0.9)),
                url('https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80');
    background-size: cover;
    background-position: center;
    min-height: 70vh;
    display: flex;
    align-items: center;
    padding: 4rem 2rem;
    position: relative;
    overflow: hidden;
">
    <div class="container-wide" style="
        max-width: 1400px;
        margin: 0 auto;
        width: 100%;
    ">
        <div class="hero-content-wide" style="
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 4rem;
            align-items: center;
        ">
            <div class="hero-text">
                <h1 style="
                    font-size: 4rem;
                    font-weight: 800;
                    color: white;
                    margin-bottom: 1.5rem;
                    line-height: 1.1;
                ">
                    Discover Your Next<br>
                    <span style="
                        background: linear-gradient(45deg, #fbbf24, #f59e0b);
                        -webkit-background-clip: text;
                        -webkit-text-fill-color: transparent;
                        background-clip: text;
                    ">Favorite Book</span>
                </h1>
                
                <p style="
                    font-size: 1.4rem;
                    color: rgba(255, 255, 255, 0.9);
                    margin-bottom: 2.5rem;
                    line-height: 1.6;
                    max-width: 600px;
                ">
                    Join our vibrant community of readers exploring thousands of books, 
                    sharing honest reviews, and discovering new literary adventures.
                </p>
                
                <div style="display: flex; gap: 1.5rem; align-items: center;">
                    <a href="books/list.php" class="cta-button primary" style="
                        background: linear-gradient(135deg, #fbbf24, #f59e0b);
                        color: #1f2937;
                        padding: 1.2rem 3rem;
                        border-radius: 12px;
                        text-decoration: none;
                        font-weight: 700;
                        font-size: 1.2rem;
                        display: inline-flex;
                        align-items: center;
                        gap: 1rem;
                        transition: all 0.3s ease;
                        box-shadow: 0 10px 30px rgba(251, 191, 36, 0.3);
                    ">
                        <i class="fas fa-book-open"></i>
                        Explore Books
                    </a>
                    
                    <a href="auth/register.php" class="cta-button secondary" style="
                        background: rgba(255, 255, 255, 0.15);
                        color: white;
                        padding: 1.2rem 2.5rem;
                        border-radius: 12px;
                        text-decoration: none;
                        font-weight: 600;
                        font-size: 1.2rem;
                        display: inline-flex;
                        align-items: center;
                        gap: 1rem;
                        transition: all 0.3s ease;
                        border: 2px solid rgba(255, 255, 255, 0.3);
                        backdrop-filter: blur(10px);
                    ">
                        <i class="fas fa-user-plus"></i>
                        Join Free
                    </a>
                </div>
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
                ">Community Stats</h3>
                
                <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-size: 1.1rem;">Books Available</span>
                        <span style="
                            font-size: 2rem;
                            font-weight: 700;
                            color: #fbbf24;
                        "><?php
                            $stmt = $pdo->query("SELECT COUNT(*) FROM books");
                            echo $stmt->fetchColumn();
                        ?></span>
                    </div>
                    
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-size: 1.1rem;">Reviews Posted</span>
                        <span style="
                            font-size: 2rem;
                            font-weight: 700;
                            color: #fbbf24;
                        "><?php
                            $stmt = $pdo->query("SELECT COUNT(*) FROM reviews WHERE status='approved'");
                            echo $stmt->fetchColumn();
                        ?></span>
                    </div>
                    
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-size: 1.1rem;">Active Readers</span>
                        <span style="
                            font-size: 2rem;
                            font-weight: 700;
                            color: #fbbf24;
                        "><?php
                            $stmt = $pdo->query("SELECT COUNT(*) FROM users");
                            echo $stmt->fetchColumn();
                        ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Featured Books - Wide Grid -->
<div class="featured-books-wide" style="
    padding: 5rem 2rem;
    background: #f8fafc;
">
    <div class="container-wide" style="
        max-width: 1400px;
        margin: 0 auto;
        width: 100%;
    ">
        <div class="section-header" style="
            display: flex;
            justify-content: space-between;
            align-items: end;
            margin-bottom: 3rem;
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 1rem;
        ">
            <div>
                <h2 style="
                    font-size: 2.5rem;
                    color: #1f2937;
                    margin-bottom: 0.5rem;
                    font-weight: 700;
                ">Featured Books</h2>
                <p style="
                    font-size: 1.2rem;
                    color: #6b7280;
                ">Recently added to our collection</p>
            </div>
            
            <a href="books/list.php" style="
                color: #667eea;
                text-decoration: none;
                font-weight: 600;
                font-size: 1.1rem;
                display: flex;
                align-items: center;
                gap: 0.5rem;
            ">
                View All Books
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>
        
        <div class="books-grid-wide" style="
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(380px, 1fr));
            gap: 2rem;
        ">
            <?php
            $stmt = $pdo->prepare("SELECT * FROM books ORDER BY created_at DESC LIMIT 6");
            $stmt->execute();
            $books = $stmt->fetchAll();
            
            foreach($books as $book): ?>
                <div class="book-card-wide" style="
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
                    </div>
                    
                    <div style="padding: 1.5rem; flex-grow: 1; display: flex; flex-direction: column;">
                        <div style="margin-bottom: 1rem;">
                            <p style="
                                color: #6b7280;
                                margin-bottom: 0.5rem;
                                font-weight: 600;
                                display: flex;
                                align-items: center;
                                gap: 0.5rem;
                            ">
                                <i class="fas fa-user-pen" style="color: #667eea;"></i>
                                <?php echo htmlspecialchars($book['author']); ?>
                            </p>
                            
                            <p style="
                                color: #374151;
                                font-size: 0.9rem;
                                line-height: 1.5;
                                margin-bottom: 1rem;
                                flex-grow: 1;
                            ">
                                <?php echo substr($book['description'], 0, 120); ?>...
                            </p>
                        </div>
                        
                        <div style="
                            display: flex;
                            justify-content: space-between;
                            align-items: center;
                            margin-top: auto;
                        ">
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
                            
                            <a href="books/view.php?id=<?php echo $book['book_id']; ?>" style="
                                background: linear-gradient(135deg, #667eea, #764ba2);
                                color: white;
                                padding: 0.6rem 1.2rem;
                                border-radius: 8px;
                                text-decoration: none;
                                font-weight: 600;
                                font-size: 0.9rem;
                                transition: all 0.3s ease;
                                display: flex;
                                align-items: center;
                                gap: 0.5rem;
                            ">
                                <i class="fas fa-eye"></i>
                                View
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Features Section - Side by Side -->
<div class="features-wide" style="
    padding: 5rem 2rem;
    background: white;
">
    <div class="container-wide" style="
        max-width: 1400px;
        margin: 0 auto;
        width: 100%;
    ">
        <div class="section-header" style="
            margin-bottom: 4rem;
        ">
            <h2 style="
                font-size: 2.5rem;
                color: #1f2937;
                margin-bottom: 1rem;
                font-weight: 700;
            ">Why Choose BookReview?</h2>
            <p style="
                font-size: 1.2rem;
                color: #6b7280;
                max-width: 600px;
            ">Everything you need for your reading journey in one place</p>
        </div>
        
        <div class="features-grid" style="
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 2.5rem;
        ">
            <div class="feature-item" style="
                display: flex;
                gap: 1.5rem;
                align-items: flex-start;
            ">
                <div style="
                    background: linear-gradient(135deg, #667eea, #764ba2);
                    width: 70px;
                    height: 70px;
                    border-radius: 16px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-size: 1.8rem;
                    color: white;
                    flex-shrink: 0;
                ">
                    <i class="fas fa-book"></i>
                </div>
                <div>
                    <h3 style="
                        color: #1f2937;
                        margin-bottom: 0.75rem;
                        font-size: 1.4rem;
                        font-weight: 600;
                    ">Extensive Library</h3>
                    <p style="
                        color: #6b7280;
                        line-height: 1.6;
                        margin: 0;
                    ">Access thousands of books across all genres with detailed descriptions, author information, and publication details.</p>
                </div>
            </div>
            
            <div class="feature-item" style="
                display: flex;
                gap: 1.5rem;
                align-items: flex-start;
            ">
                <div style="
                    background: linear-gradient(135deg, #f093fb, #f5576c);
                    width: 70px;
                    height: 70px;
                    border-radius: 16px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-size: 1.8rem;
                    color: white;
                    flex-shrink: 0;
                ">
                    <i class="fas fa-star"></i>
                </div>
                <div>
                    <h3 style="
                        color: #1f2937;
                        margin-bottom: 0.75rem;
                        font-size: 1.4rem;
                        font-weight: 600;
                    ">Honest Reviews</h3>
                    <p style="
                        color: #6b7280;
                        line-height: 1.6;
                        margin: 0;
                    ">Read genuine reviews from real readers and share your own insights to help others discover great books.</p>
                </div>
            </div>
            
            <div class="feature-item" style="
                display: flex;
                gap: 1.5rem;
                align-items: flex-start;
            ">
                <div style="
                    background: linear-gradient(135deg, #4facfe, #00f2fe);
                    width: 70px;
                    height: 70px;
                    border-radius: 16px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-size: 1.8rem;
                    color: white;
                    flex-shrink: 0;
                ">
                    <i class="fas fa-users"></i>
                </div>
                <div>
                    <h3 style="
                        color: #1f2937;
                        margin-bottom: 0.75rem;
                        font-size: 1.4rem;
                        font-weight: 600;
                    ">Vibrant Community</h3>
                    <p style="
                        color: #6b7280;
                        line-height: 1.6;
                        margin: 0;
                    ">Connect with fellow book lovers, join discussions, and get personalized recommendations based on your interests.</p>
                </div>
            </div>
            
            <div class="feature-item" style="
                display: flex;
                gap: 1.5rem;
                align-items: flex-start;
            ">
                <div style="
                    background: linear-gradient(135deg, #43e97b, #38f9d7);
                    width: 70px;
                    height: 70px;
                    border-radius: 16px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-size: 1.8rem;
                    color: white;
                    flex-shrink: 0;
                ">
                    <i class="fas fa-search"></i>
                </div>
                <div>
                    <h3 style="
                        color: #1f2937;
                        margin-bottom: 0.75rem;
                        font-size: 1.4rem;
                        font-weight: 600;
                    ">Smart Discovery</h3>
                    <p style="
                        color: #6b7280;
                        line-height: 1.6;
                        margin: 0;
                    ">Find your next favorite book with advanced search, filtering, and personalized recommendations tailored to your taste.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bottom CTA Section -->
<div class="bottom-cta" style="
    padding: 4rem 2rem;
    background: linear-gradient(135deg, #1f2937, #374151);
    color: white;
">
    <div class="container-wide" style="
        max-width: 1400px;
        margin: 0 auto;
        width: 100%;
    ">
        <div style="
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 4rem;
            align-items: center;
        ">
            <div>
                <h2 style="
                    font-size: 2.5rem;
                    margin-bottom: 1rem;
                    font-weight: 700;
                ">Ready to Start Reading?</h2>
                <p style="
                    font-size: 1.2rem;
                    color: rgba(255, 255, 255, 0.8);
                    margin-bottom: 2rem;
                    line-height: 1.6;
                ">
                    Join thousands of readers who have already discovered their next favorite book. 
                    Create your free account and start your literary journey today.
                </p>
                
                <div style="display: flex; gap: 1.5rem;">
                    <a href="auth/register.php" style="
                        background: linear-gradient(135deg, #fbbf24, #f59e0b);
                        color: #1f2937;
                        padding: 1rem 2.5rem;
                        border-radius: 10px;
                        text-decoration: none;
                        font-weight: 700;
                        font-size: 1.1rem;
                        display: inline-flex;
                        align-items: center;
                        gap: 0.75rem;
                        transition: all 0.3s ease;
                    ">
                        <i class="fas fa-rocket"></i>
                        Get Started Free
                    </a>
                    
                    <a href="about.php" style="
                        background: rgba(255, 255, 255, 0.1);
                        color: white;
                        padding: 1rem 2rem;
                        border-radius: 10px;
                        text-decoration: none;
                        font-weight: 600;
                        font-size: 1.1rem;
                        display: inline-flex;
                        align-items: center;
                        gap: 0.75rem;
                        transition: all 0.3s ease;
                        border: 1px solid rgba(255, 255, 255, 0.2);
                    ">
                        <i class="fas fa-info-circle"></i>
                        Learn More
                    </a>
                </div>
            </div>
            
            <div style="
                background: rgba(255, 255, 255, 0.05);
                border: 1px solid rgba(255, 255, 255, 0.1);
                border-radius: 16px;
                padding: 2rem;
                text-align: center;
            ">
                <div style="font-size: 3rem; margin-bottom: 1rem; color: #fbbf24;">
                    <i class="fas fa-award"></i>
                </div>
                <h3 style="margin-bottom: 0.5rem; font-size: 1.3rem;">Trusted by Readers</h3>
                <p style="color: rgba(255, 255, 255, 0.7); font-size: 0.9rem; margin: 0;">
                    Join our community of passionate readers and book enthusiasts
                </p>
            </div>
        </div>
    </div>
</div>

<style>
    /* Hover Effects */
    .cta-button.primary:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 35px rgba(251, 191, 36, 0.4);
    }
    
    .cta-button.secondary:hover {
        background: rgba(255, 255, 255, 0.25);
        transform: translateY(-3px);
    }
    
    .book-card-wide:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
    }
    
    .book-card-wide a:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
    }
    
    .feature-item:hover {
        transform: translateX(10px);
    }

    #chatbot-toggle:hover {
        transform: scale(1.1);
        box-shadow: 0 12px 30px rgba(102, 126, 234, 0.5);
    }

    .quick-question:hover {
        background: #667eea !important;
        color: white !important;
        border-color: #667eea !important;
    }

    #send-message:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
    }
    
    /* Smooth Transitions */
    .book-card-wide,
    .feature-item,
    .cta-button,
    #chatbot-toggle,
    .quick-question,
    #send-message {
        transition: all 0.3s ease;
    }

    /* Chatbot Styles */
    .user-message {
        background: linear-gradient(135deg, #667eea, #764ba2);
        color: white;
        padding: 1rem;
        border-radius: 12px;
        font-size: 0.9rem;
        max-width: 85%;
        align-self: flex-end;
    }

    .bot-message {
        background: white;
        padding: 1rem;
        border-radius: 12px;
        border: 1px solid #e5e7eb;
        font-size: 0.9rem;
        max-width: 85%;
        align-self: flex-start;
    }

    /* Scrollbar Styling */
    #chat-messages::-webkit-scrollbar {
        width: 4px;
    }

    #chat-messages::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 10px;
    }

    #chat-messages::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 10px;
    }
    
    /* Responsive Design */
    @media (max-width: 1024px) {
        .hero-content-wide {
            grid-template-columns: 1fr;
            gap: 2rem;
        }
        
        .books-grid-wide {
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        }
        
        .features-grid {
            grid-template-columns: 1fr;
        }

        #chatbot-modal {
            width: 350px;
            height: 450px;
        }
    }
    
    @media (max-width: 768px) {
        .hero-section-wide h1 {
            font-size: 2.8rem;
        }
        
        .section-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 1rem;
        }
        
        .bottom-cta > .container-wide > div {
            grid-template-columns: 1fr;
            gap: 2rem;
        }

        #chatbot-modal {
            width: 100%;
            height: 100%;
            bottom: 0;
            right: 0;
            border-radius: 0;
        }

        #chatbot-toggle {
            bottom: 1rem;
            right: 1rem;
            width: 50px;
            height: 50px;
            font-size: 1.2rem;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const chatbotToggle = document.getElementById('chatbot-toggle');
        const chatbotModal = document.getElementById('chatbot-modal');
        const closeChat = document.getElementById('close-chat');
        const chatMessages = document.getElementById('chat-messages');
        const chatInput = document.getElementById('chat-input');
        const sendButton = document.getElementById('send-message');
        const quickQuestions = document.querySelectorAll('.quick-question');

        // Get book data from PHP
        const totalBooks = <?php echo $pdo->query("SELECT COUNT(*) FROM books")->fetchColumn(); ?>;
        const totalReviews = <?php echo $pdo->query("SELECT COUNT(*) FROM reviews WHERE status='approved'")->fetchColumn(); ?>;
        const totalUsers = <?php echo $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn(); ?>;

        // Get featured books
        const featuredBooks = <?php 
            $stmt = $pdo->prepare("SELECT title, author, category FROM books ORDER BY created_at DESC LIMIT 6");
            $stmt->execute();
            echo json_encode($stmt->fetchAll());
        ?>;

        // Get popular books based on number of reviews (most reviewed)
        const popularBooks = <?php
            $stmt = $pdo->query("
                SELECT b.title, b.author, b.category, COUNT(r.review_id) as review_count 
                FROM books b 
                LEFT JOIN reviews r ON b.book_id = r.book_id 
                WHERE r.status = 'approved'
                GROUP BY b.book_id 
                ORDER BY review_count DESC 
                LIMIT 5
            ");
            echo json_encode($stmt->fetchAll());
        ?>;

        // Get highest rated books (for comparison)
        const highestRatedBooks = <?php
            $stmt = $pdo->query("
                SELECT b.title, b.author, b.category, AVG(r.rating) as avg_rating, COUNT(r.review_id) as review_count
                FROM books b 
                LEFT JOIN reviews r ON b.book_id = r.book_id 
                WHERE r.status = 'approved'
                GROUP BY b.book_id 
                HAVING COUNT(r.review_id) >= 2
                ORDER BY avg_rating DESC 
                LIMIT 5
            ");
            echo json_encode($stmt->fetchAll());
        ?>;

        // Get books by category
        const fictionBooks = <?php
            $stmt = $pdo->prepare("SELECT title, author FROM books WHERE category LIKE '%fiction%' OR category LIKE '%Fiction%' LIMIT 5");
            $stmt->execute();
            echo json_encode($stmt->fetchAll());
        ?>;

        // Get most reviewed books specifically
        const mostReviewedBooks = <?php
            $stmt = $pdo->query("
                SELECT b.title, b.author, b.category, COUNT(r.review_id) as review_count
                FROM books b 
                JOIN reviews r ON b.book_id = r.book_id 
                WHERE r.status = 'approved'
                GROUP BY b.book_id 
                ORDER BY review_count DESC 
                LIMIT 5
            ");
            echo json_encode($stmt->fetchAll());
        ?>;

        // Toggle chatbot modal
        chatbotToggle.addEventListener('click', function() {
            chatbotModal.style.display = 'flex';
        });

        closeChat.addEventListener('click', function() {
            chatbotModal.style.display = 'none';
        });

        // Function to add message to chat
        function addMessage(message, isUser = false) {
            const messageDiv = document.createElement('div');
            messageDiv.className = isUser ? 'user-message' : 'bot-message';
            
            if (isUser) {
                messageDiv.innerHTML = `
                    <div style="font-weight: 600; margin-bottom: 0.5rem; font-size: 0.8rem;">
                        <i class="fas fa-user"></i> You
                    </div>
                    ${message}
                `;
            } else {
                messageDiv.innerHTML = message.replace(/\n/g, '<br>');
            }
            
            chatMessages.appendChild(messageDiv);
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }

        // Function to process user input and generate response
        function processInput(input) {
            input = input.toLowerCase().trim();
            
            if (input.includes('hello') || input.includes('hi') || input.includes('hey')) {
                return `Hello! 👋 I'm your Book Finder Assistant. I can help you discover amazing books in our collection!\n\nWe currently have <strong>${totalBooks}</strong> books available, with <strong>${totalReviews}</strong> reviews from our community of <strong>${totalUsers}</strong> readers.\n\nWhat would you like to know about our books?`;
            
            } else if (input.includes('available') || input.includes('what books') || input.includes('show books')) {
                let response = `We have <strong>${totalBooks}</strong> books available! Here are some recently added books:\n\n`;
                featuredBooks.forEach((book, index) => {
                    response += `<strong>${index + 1}. ${book.title}</strong> by ${book.author} (${book.category})\n`;
                });
                response += `\nYou can <a href="books/list.php" style="color: #667eea; text-decoration: underline;">browse all books here</a> or ask me about specific genres!`;
                return response;
            
            } else if (input.includes('popular') || input.includes('most reviewed') || input.includes('most reviews')) {
                let response = `Here are our most popular books based on number of reviews:\n\n`;
                mostReviewedBooks.forEach((book, index) => {
                    response += `<strong>${index + 1}. ${book.title}</strong> by ${book.author}\n📚 ${book.review_count} reviews (${book.category})\n`;
                });
                response += `\nThese books have generated the most discussion in our community!`;
                return response;
            
            } else if (input.includes('highest rated') || input.includes('best rated') || input.includes('top rated')) {
                let response = `Here are our highest rated books (with at least 2 reviews):\n\n`;
                highestRatedBooks.forEach((book, index) => {
                    const rating = book.avg_rating ? book.avg_rating.toFixed(1) : 'No ratings yet';
                    response += `<strong>${index + 1}. ${book.title}</strong> by ${book.author}\n⭐ ${rating}/5 stars (${book.review_count} reviews)\n`;
                });
                return response;
            
            } else if (input.includes('fiction') || input.includes('novel') || input.includes('story')) {
                if (fictionBooks.length > 0) {
                    let response = `Great choice! Here are some fiction books you might enjoy:\n\n`;
                    fictionBooks.forEach((book, index) => {
                        response += `<strong>${index + 1}. ${book.title}</strong> by ${book.author}\n`;
                    });
                    response += `\nYou can explore more fiction books in our <a href="books/list.php?category=fiction" style="color: #667eea; text-decoration: underline;">fiction section</a>.`;
                    return response;
                } else {
                    return `We have many fiction books available! You can browse our complete fiction collection in the <a href="books/list.php?category=fiction" style="color: #667eea; text-decoration: underline;">fiction section</a>.`;
                }
            
            } else if (input.includes('genre') || input.includes('category')) {
                return `We have books across many genres! Some popular categories include:\n\n• Fiction & Literature\n• Science Fiction & Fantasy\n• Mystery & Thriller\n• Romance\n• Biography & Memoir\n• Science & Technology\n• History\n• Self-Help\n\nYou can browse books by category in our <a href="books/list.php" style="color: #667eea; text-decoration: underline;">books section</a>.`;
            
            } else if (input.includes('review') || input.includes('rating')) {
                return `Our community has posted <strong>${totalReviews}</strong> reviews across all books! 📚\n\nEach book shows user ratings and detailed reviews to help you choose your next read.\n\nTo read or write reviews, you'll need to <a href="auth/register.php" style="color: #667eea; text-decoration: underline;">create a free account</a>.`;
            
            } else if (input.includes('how to') || input.includes('get started') || input.includes('begin')) {
                return `Getting started is easy! 🚀\n\n1. <strong>Browse Books</strong> - Explore our collection\n2. <strong>Create Account</strong> - <a href="auth/register.php" style="color: #667eea; text-decoration: underline;">Sign up for free</a>\n3. <strong>Read Reviews</strong> - See what others think\n4. <strong>Write Reviews</strong> - Share your thoughts\n5. <strong>Discover More</strong> - Get personalized recommendations\n\nReady to begin your reading journey?`;
            
            } else if (input.includes('thank') || input.includes('thanks')) {
                return `You're welcome! 😊\n\nI'm always here to help you discover amazing books. Don't hesitate to ask if you need more book recommendations or information about our platform!\n\nHappy reading! 📖`;
            
            } else {
                return `I'm not sure I understand. I can help you with:\n\n• Discovering available books\n• Finding popular and most-reviewed books\n• Exploring highest-rated books\n• Exploring different genres (fiction, sci-fi, mystery, etc.)\n• Learning how to get started\n• Understanding our review system\n\nTry asking something like "What books are available?" or "Show me popular books"`;
            }
        }

        // Send message function
        function sendMessage() {
            const message = chatInput.value.trim();
            if (message) {
                addMessage(message, true);
                chatInput.value = '';
                
                // Simulate thinking delay
                setTimeout(() => {
                    const response = processInput(message);
                    addMessage(response);
                }, 1000);
            }
        }

        // Quick question buttons
        quickQuestions.forEach(button => {
            button.addEventListener('click', function() {
                const question = this.getAttribute('data-question');
                chatInput.value = question;
                sendMessage();
            });
        });

        // Event listeners
        sendButton.addEventListener('click', sendMessage);
        chatInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                sendMessage();
            }
        });

        // Auto-open chatbot after 3 seconds
        setTimeout(() => {
            if (!localStorage.getItem('chatbotShown')) {
                chatbotModal.style.display = 'flex';
                localStorage.setItem('chatbotShown', 'true');
            }
        }, 3000);
    });
</script>

<?php include 'includes/footer.php'; ?>