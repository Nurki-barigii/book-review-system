<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BookReview - Discover Your Next Favorite Book</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* Footer Styles - Redesigned */
        .footer {
            background: linear-gradient(135deg, #1f2937 0%, #111827 100%);
            color: white;
            margin-top: 4rem;
            padding: 4rem 0 1.5rem;
            position: relative;
            overflow: hidden;
        }

        .footer::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, #4f46e5, transparent);
        }

        .footer-container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 2rem;
            position: relative;
            z-index: 1;
        }

        .footer-content {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1.5fr;
            gap: 3rem;
            margin-bottom: 3rem;
        }

        .footer-section h3 {
            color: #fbbf24;
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            position: relative;
        }

        .footer-section h3::after {
            content: '';
            position: absolute;
            bottom: -8px;
            left: 0;
            width: 40px;
            height: 3px;
            background: #4f46e5;
            border-radius: 2px;
        }

        .footer-brand {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 1.5rem;
        }

        .footer-brand-icon {
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            width: 44px;
            height: 44px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
        }

        .footer-brand-icon i {
            color: white;
            font-size: 1.5rem;
        }

        .footer-brand-text {
            font-size: 1.75rem;
            font-weight: 800;
            background: linear-gradient(135deg, #fbbf24, #f59e0b);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .footer-description {
            color: #d1d5db;
            line-height: 1.7;
            margin-bottom: 2rem;
            font-size: 1rem;
        }

        .social-links {
            display: flex;
            gap: 1rem;
        }

        .social-link {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 44px;
            height: 44px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            color: #d1d5db;
            font-size: 1.25rem;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .social-link:hover {
            background: #4f46e5;
            color: white;
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(79, 70, 229, 0.4);
        }

        .footer-links {
            list-style: none;
        }

        .footer-links li {
            margin-bottom: 0.75rem;
        }

        .footer-links a {
            color: #d1d5db;
            text-decoration: none;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .footer-links a:hover {
            color: #fbbf24;
            transform: translateX(5px);
        }

        .footer-links a i {
            font-size: 0.75rem;
            color: #4f46e5;
            transition: all 0.3s ease;
        }

        .footer-links a:hover i {
            color: #fbbf24;
        }

        .contact-info {
            color: #d1d5db;
        }

        .contact-item {
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            margin-bottom: 1rem;
            line-height: 1.5;
        }

        .contact-item i {
            color: #4f46e5;
            margin-top: 0.25rem;
            flex-shrink: 0;
        }

        .newsletter-form {
            margin-top: 1.5rem;
        }

        .newsletter-text {
            color: #d1d5db;
            margin-bottom: 1rem;
            font-size: 0.95rem;
        }

        .newsletter-input-group {
            display: flex;
            gap: 0.5rem;
        }

        .newsletter-input {
            flex: 1;
            padding: 0.75rem 1rem;
            border: 1px solid #374151;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.05);
            color: white;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }

        .newsletter-input:focus {
            outline: none;
            border-color: #4f46e5;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }

        .newsletter-btn {
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            color: white;
            border: none;
            border-radius: 8px;
            padding: 0.75rem 1.25rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            white-space: nowrap;
        }

        .newsletter-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(79, 70, 229, 0.4);
        }

        .footer-bottom {
            border-top: 1px solid #374151;
            padding-top: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .copyright {
            color: #9ca3af;
            font-size: 0.95rem;
        }

        .footer-bottom-links {
            display: flex;
            gap: 2rem;
        }

        .footer-bottom-link {
            color: #9ca3af;
            text-decoration: none;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            position: relative;
        }

        .footer-bottom-link:hover {
            color: #fbbf24;
        }

        .footer-bottom-link::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 1px;
            background: #fbbf24;
            transition: width 0.3s ease;
        }

        .footer-bottom-link:hover::after {
            width: 100%;
        }

        .back-to-top {
            position: absolute;
            right: 2rem;
            top: -25px;
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            color: white;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            font-size: 1.25rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            transition: all 0.3s ease;
            z-index: 2;
        }

        .back-to-top:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 25px rgba(79, 70, 229, 0.5);
        }

        /* Responsive Design */
        @media (max-width: 1024px) {
            .footer-content {
                grid-template-columns: 1fr 1fr;
                gap: 2.5rem;
            }
        }

        @media (max-width: 768px) {
            .footer {
                padding: 3rem 0 1.5rem;
            }
            
            .footer-content {
                grid-template-columns: 1fr;
                gap: 2rem;
            }
            
            .footer-bottom {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }
            
            .footer-bottom-links {
                justify-content: center;
            }
            
            .back-to-top {
                right: 1rem;
                top: -20px;
                width: 40px;
                height: 40px;
                font-size: 1rem;
            }
        }

        @media (max-width: 480px) {
            .footer-container {
                padding: 0 1rem;
            }
            
            .newsletter-input-group {
                flex-direction: column;
            }
            
            .footer-bottom-links {
                flex-direction: column;
                gap: 0.75rem;
            }
            
            .social-links {
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <!-- Your existing content here -->

    <footer class="footer">
        <a href="#" class="back-to-top" id="backToTop">
            <i class="fas fa-chevron-up"></i>
        </a>
        
        <div class="footer-container">
            <div class="footer-content">
                <div class="footer-section">
                    <div class="footer-brand">
                        <div class="footer-brand-icon">
                            <i class="fas fa-book-open"></i>
                        </div>
                        <span class="footer-brand-text">BookReview</span>
                    </div>
                    <p class="footer-description">
                        Discover, read, and review your favorite books. Join our community of book lovers 
                        and explore thousands of reviews from readers worldwide.
                    </p>
                    <div class="social-links">
                        <a href="#" class="social-link" aria-label="Facebook">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="social-link" aria-label="Twitter">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="social-link" aria-label="Instagram">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="social-link" aria-label="Goodreads">
                            <i class="fab fa-goodreads"></i>
                        </a>
                    </div>
                </div>
                
                <div class="footer-section">
                    <h3>Quick Links</h3>
                    <ul class="footer-links">
                        <li><a href="/book_review/index.php"><i class="fas fa-chevron-right"></i> Home</a></li>
                        <li><a href="/book_review/books/list.php"><i class="fas fa-chevron-right"></i> All Books</a></li>
                        <li><a href="/book_review/books/search.php"><i class="fas fa-chevron-right"></i> Search Books</a></li>
                        <li><a href="/book_review/about.php"><i class="fas fa-chevron-right"></i> About Us</a></li>
                        <li><a href="/book_review/contact.php"><i class="fas fa-chevron-right"></i> Contact</a></li>
                    </ul>
                </div>
                
                <div class="footer-section">
                    <h3>Categories</h3>
                    <ul class="footer-links">
                        <li><a href="#"><i class="fas fa-chevron-right"></i> Fiction</a></li>
                        <li><a href="#"><i class="fas fa-chevron-right"></i> Non-Fiction</a></li>
                        <li><a href="#"><i class="fas fa-chevron-right"></i> Science Fiction</a></li>
                        <li><a href="#"><i class="fas fa-chevron-right"></i> Mystery</a></li>
                        <li><a href="#"><i class="fas fa-chevron-right"></i> Romance</a></li>
                    </ul>
                </div>
                
                <div class="footer-section">
                    <h3>Contact Info</h3>
                    <div class="contact-info">
                        <div class="contact-item">
                            <i class="fas fa-envelope"></i>
                            <span>Nurkiali97@gmail.com</span>
                        </div>
                        <div class="contact-item">
                            <i class="fas fa-phone"></i>
                            <span>+254797987272</span>
                        </div>
                        <div class="contact-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <span>Runda,Thika</span>
                        </div>
                    </div>
                    
                    <div class="newsletter-form">
                        <p class="newsletter-text">Subscribe to our newsletter for updates</p>
                        <div class="newsletter-input-group">
                            <input type="email" class="newsletter-input" placeholder="Your email address" aria-label="Email for newsletter">
                            <button type="submit" class="newsletter-btn">Subscribe</button>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p class="copyright">&copy; 2024 BookReview System. All rights reserved.</p>
                <div class="footer-bottom-links">
                    <a href="#" class="footer-bottom-link">Privacy Policy</a>
                    <a href="#" class="footer-bottom-link">Terms of Service</a>
                    <a href="#" class="footer-bottom-link">Cookie Policy</a>
                </div>
            </div>
        </div>
    </footer>

    <script>
        // Back to top functionality
        document.addEventListener('DOMContentLoaded', function() {
            const backToTop = document.getElementById('backToTop');
            
            if (backToTop) {
                // Show/hide back to top button based on scroll position
                window.addEventListener('scroll', function() {
                    if (window.pageYOffset > 300) {
                        backToTop.style.opacity = '1';
                        backToTop.style.visibility = 'visible';
                    } else {
                        backToTop.style.opacity = '0';
                        backToTop.style.visibility = 'hidden';
                    }
                });
                
                // Smooth scroll to top
                backToTop.addEventListener('click', function(e) {
                    e.preventDefault();
                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });
                });
            }
            
            // Newsletter form submission
            const newsletterForm = document.querySelector('.newsletter-input-group');
            if (newsletterForm) {
                newsletterForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const emailInput = this.querySelector('.newsletter-input');
                    const email = emailInput.value.trim();
                    
                    if (email && isValidEmail(email)) {
                        // Here you would typically send the email to your server
                        alert('Thank you for subscribing to our newsletter!');
                        emailInput.value = '';
                    } else {
                        alert('Please enter a valid email address.');
                    }
                });
            }
            
            function isValidEmail(email) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return emailRegex.test(email);
            }
        });
    </script>
</body>
</html>