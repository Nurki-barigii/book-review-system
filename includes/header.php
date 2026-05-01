<?php
if (session_status() === PHP_SESSION_NONE) 
    session_start();require_once __DIR__ . '/../config/db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BookReview - Discover Your Next Favorite Book</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* Reset and Base Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            line-height: 1.6;
            color: #333;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }

        /* Navigation Styles - Redesigned */
        .navbar {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(15px);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.08);
            position: sticky;
            top: 0;
            z-index: 1000;
            padding: 0.8rem 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.3);
        }

        .nav-container {
            max-width: 1280px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 2rem;
        }

        .nav-brand {
            display: flex;
            align-items: center;
        }

        .nav-brand a {
            display: flex;
            align-items: center;
            text-decoration: none;
            color: #4f46e5;
            font-size: 1.75rem;
            font-weight: 800;
            transition: all 0.3s ease;
        }

        .nav-brand a:hover {
            transform: scale(1.05);
        }

        .brand-icon {
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 0.75rem;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
        }

        .brand-icon i {
            color: white;
            font-size: 1.4rem;
        }

        .nav-menu {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
            color: #4b5563;
            font-weight: 500;
            padding: 0.6rem 1rem;
            border-radius: 10px;
            transition: all 0.3s ease;
            position: relative;
        }

        .nav-link:hover {
            color: #4f46e5;
            background: rgba(79, 70, 229, 0.08);
        }

        .nav-link.active {
            color: #4f46e5;
            background: rgba(79, 70, 229, 0.1);
        }

        .nav-link.active::after {
            content: '';
            position: absolute;
            bottom: -8px;
            left: 50%;
            transform: translateX(-50%);
            width: 6px;
            height: 6px;
            background: #4f46e5;
            border-radius: 50%;
        }

        .nav-dropdown {
            position: relative;
        }

        .dropdown-toggle {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
        }

        .dropdown-menu {
            position: absolute;
            top: 100%;
            right: 0;
            background: white;
            min-width: 220px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
            border-radius: 12px;
            padding: 0.5rem;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.3s ease;
            z-index: 1001;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .nav-dropdown:hover .dropdown-menu {
            opacity: 1;
            visibility: visible;
            transform: translateY(5px);
        }

        .dropdown-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1rem;
            text-decoration: none;
            color: #6b7280;
            border-radius: 8px;
            transition: all 0.2s ease;
        }

        .dropdown-link:hover {
            background: #f8fafc;
            color: #4f46e5;
            transform: translateX(5px);
        }

        .dropdown-divider {
            height: 1px;
            background: #e5e7eb;
            margin: 0.5rem 0;
        }

        .admin-badge {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: white;
            padding: 0.2rem 0.6rem;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
            margin-left: 0.5rem;
        }

        .nav-auth {
            display: flex;
            gap: 0.75rem;
        }

        .login-btn {
            color: #4f46e5;
            border: 2px solid #4f46e5;
            font-weight: 600;
        }

        .login-btn:hover {
            background: #4f46e5;
            color: white;
        }

        .register-btn {
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            color: white;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
        }

        .register-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(79, 70, 229, 0.4);
        }

        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            margin-right: 0.5rem;
        }

        .mobile-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 1.5rem;
            color: #4f46e5;
            cursor: pointer;
        }

        /* Responsive Design */
        @media (max-width: 1024px) {
            .nav-menu {
                gap: 1rem;
            }
            
            .nav-link {
                padding: 0.5rem 0.8rem;
                font-size: 0.9rem;
            }
        }

        @media (max-width: 768px) {
            .mobile-toggle {
                display: block;
            }
            
            .nav-menu {
                position: fixed;
                top: 70px;
                left: 0;
                width: 100%;
                background: white;
                flex-direction: column;
                padding: 1.5rem;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
                transform: translateY(-100%);
                opacity: 0;
                visibility: hidden;
                transition: all 0.3s ease;
                z-index: 999;
            }
            
            .nav-menu.active {
                transform: translateY(0);
                opacity: 1;
                visibility: visible;
            }
            
            .nav-auth {
                flex-direction: column;
                width: 100%;
            }
            
            .nav-auth .nav-link {
                width: 100%;
                justify-content: center;
            }
            
            .dropdown-menu {
                position: static;
                box-shadow: none;
                background: #f9fafb;
                margin-top: 0.5rem;
                transform: none;
                display: none;
            }
            
            .nav-dropdown:hover .dropdown-menu,
            .nav-dropdown.active .dropdown-menu {
                display: block;
                opacity: 1;
                visibility: visible;
                transform: none;
            }
        }

        @media (max-width: 480px) {
            .nav-container {
                padding: 0 1rem;
            }
            
            .nav-brand a {
                font-size: 1.5rem;
            }
            
            .brand-icon {
                width: 35px;
                height: 35px;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-brand">
                <a href="/book_review/index.php">
                    <div class="brand-icon">
                        <i class="fas fa-book-open"></i>
                    </div>
                    <span>BookReview</span>
                </a>
            </div>
            
            <button class="mobile-toggle" id="mobileToggle">
                <i class="fas fa-bars"></i>
            </button>
            
            <div class="nav-menu" id="navMenu">
                <a href="/book_review/index.php" class="nav-link active">
                    <i class="fas fa-home"></i>
                    <span>Home</span>
                </a>
                <a href="/book_review/books/list.php" class="nav-link">
                    <i class="fas fa-book"></i>
                    <span>Books</span>
                </a>
                          <a href="/book_review/dig.php" class="nav-link">
                    <i class="fas fa-book"></i>
                    <span>Download</span>
                </a>
                     <a href="/book_review/payments/report.php" class="nav-link">
                    <i class="fas fa-book"></i>
                    <span>Report</span>
                </a>
                <a href="/book_review/books/search.php" class="nav-link">
                    <i class="fas fa-search"></i>
                    <span>Search</span>
                </a>
                
                <?php if(isset($_SESSION['user_id'])): ?>
                    <?php if($_SESSION['role'] == 'admin'): ?>
                        <div class="nav-dropdown">
                            <a href="#" class="nav-link">
                                <i class="fas fa-crown"></i>
                                <span>Admin</span>
                                <span class="admin-badge">PRO</span>
                                <i class="fas fa-chevron-down"></i>
                            </a>
                            <div class="dropdown-menu">
                                <a href="/book_review/auth/admin/dashboard.php" class="dropdown-link">
                                    <i class="fas fa-tachometer-alt"></i>
                                    Dashboard
                                </a>
                                <a href="/book_review/auth/admin/manage_books.php" class="dropdown-link">
                                    <i class="fas fa-book"></i>
                                    Manage Books
                                </a>
                                <a href="/book_review/auth/admin/manage_users.php" class="dropdown-link">
                                    <i class="fas fa-users"></i>
                                    Manage Users
                                </a>
                                      <a href="/book_review/auth/admin/payments_report.php" class="dropdown-link">
                                    <i class="fas fa-users"></i>
                                    payments_report
                                </a>
                                <a href="/book_review/auth/admin/manage_reviews.php" class="dropdown-link">
                                    <i class="fas fa-star"></i>
                                    Manage Reviews
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <div class="nav-dropdown">
                        <a href="#" class="nav-link">
                            <div class="user-avatar">
                                <?php echo strtoupper(substr(htmlspecialchars($_SESSION['name']), 0, 1)); ?>
                            </div>
                            <span><?php echo htmlspecialchars($_SESSION['name']); ?></span>
                            <i class="fas fa-chevron-down"></i>
                        </a>
                        <div class="dropdown-menu">
                            <a href="/book_review/user/profile.php" class="dropdown-link">
                                <i class="fas fa-user-circle"></i>
                                Profile
                            </a>
                            <a href="/book_review/user/my_reviews.php" class="dropdown-link">
                                <i class="fas fa-star"></i>
                                My Reviews
                            </a>
                            <div class="dropdown-divider"></div>
                            <a href="/book_review/auth/logout.php" class="dropdown-link">
                                <i class="fas fa-sign-out-alt"></i>
                                Logout
                            </a>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="nav-auth">
                        <a href="/book_review/auth/login.php" class="nav-link login-btn">
                            <i class="fas fa-sign-in-alt"></i>
                            <span>Login</span>
                        </a>
                        <a href="/book_review/auth/register.php" class="nav-link register-btn">
                            <i class="fas fa-user-plus"></i>
                            <span>Register</span>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <script>
        // Mobile menu toggle functionality
        document.addEventListener('DOMContentLoaded', function() {
            const mobileToggle = document.getElementById('mobileToggle');
            const navMenu = document.getElementById('navMenu');
            
            if (mobileToggle && navMenu) {
                mobileToggle.addEventListener('click', function() {
                    navMenu.classList.toggle('active');
                    
                    // Change icon based on menu state
                    const icon = mobileToggle.querySelector('i');
                    if (navMenu.classList.contains('active')) {
                        icon.classList.remove('fa-bars');
                        icon.classList.add('fa-times');
                    } else {
                        icon.classList.remove('fa-times');
                        icon.classList.add('fa-bars');
                    }
                });
            }
            
            // Close mobile menu when clicking outside
            document.addEventListener('click', function(event) {
                if (!event.target.closest('.nav-container') && navMenu.classList.contains('active')) {
                    navMenu.classList.remove('active');
                    const icon = mobileToggle.querySelector('i');
                    icon.classList.remove('fa-times');
                    icon.classList.add('fa-bars');
                }
            });
            
            // Handle dropdowns on mobile
            const dropdownToggles = document.querySelectorAll('.nav-dropdown > .nav-link');
            dropdownToggles.forEach(toggle => {
                toggle.addEventListener('click', function(e) {
                    if (window.innerWidth <= 768) {
                        e.preventDefault();
                        const dropdown = this.parentElement;
                        dropdown.classList.toggle('active');
                    }
                });
            });
        });
    </script>