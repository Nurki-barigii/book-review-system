<?php
require_once '../config/db.php';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if($user && password_verify($password, $user['password'])) {
        session_start();
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['email'] = $user['email'];
        
        header("Location: ../index.php");
        exit();
    } else {
        $error = "Invalid email or password";
    }
}
?>

<?php include '../includes/header.php'; ?>

<div class="main-container">
    <div class="auth-container">
        <div class="auth-card">
            <!-- Login Form -->
            <div class="auth-form-section">
                <div class="auth-header">
                    <div class="auth-icon">
                        <i class="fas fa-sign-in-alt"></i>
                    </div>
                    <h1 class="auth-title">Welcome Back</h1>
                    <p class="auth-subtitle">Sign in to your BookReview account</p>
                </div>

                <?php if(isset($error)): ?>
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-circle"></i>
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <form method="POST" class="auth-form">
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-envelope"></i>
                            Email Address
                        </label>
                        <div class="input-container">
                            <input type="email" name="email" class="form-input" required 
                                   placeholder="Enter your email address" 
                                   value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                            <div class="input-icon">
                                <i class="fas fa-envelope"></i>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-lock"></i>
                            Password
                        </label>
                        <div class="input-container">
                            <input type="password" name="password" class="form-input" required 
                                   placeholder="Enter your password" id="password">
                            <div class="input-icon">
                                <i class="fas fa-lock"></i>
                            </div>
                            <button type="button" class="password-toggle" id="passwordToggle">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="form-options">
                        <label class="checkbox-label">
                            <input type="checkbox" name="remember">
                            <span class="checkmark"></span>
                            Remember me
                        </label>
                        <a href="#" class="forgot-password">Forgot password?</a>
                    </div>

                    <button type="submit" class="btn btn-primary btn-auth">
                        <i class="fas fa-sign-in-alt"></i>
                        Sign In
                    </button>
                </form>

                <div class="auth-footer">
                    <p>Don't have an account? 
                        <a href="register.php" class="auth-link">Create one here</a>
                    </p>
                </div>
            </div>

            <!-- Promo Section -->
            <div class="auth-promo-section">
                <div class="promo-content">
                    <h2>Join Our Community of Book Lovers</h2>
                    <p>Discover new books, share your thoughts, and connect with fellow readers from around the world.</p>
                    
                    <div class="promo-features">
                        <div class="feature-item">
                            <i class="fas fa-book"></i>
                            <div>
                                <h4>10,000+ Books</h4>
                                <span>Explore our vast collection</span>
                            </div>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-users"></i>
                            <div>
                                <h4>50,000+ Readers</h4>
                                <span>Join our growing community</span>
                            </div>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-star"></i>
                            <div>
                                <h4>100,000+ Reviews</h4>
                                <span>Share your insights</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Auth Pages Specific Styles */
    .auth-container {
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 80vh;
        padding: 2rem 0;
    }
    
    .auth-card {
        display: grid;
        grid-template-columns: 1fr 1fr;
        max-width: 1000px;
        width: 100%;
        background: white;
        border-radius: 24px;
        overflow: hidden;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
    }
    
    .auth-form-section {
        padding: 3rem;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    
    .auth-promo-section {
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
        color: white;
        padding: 3rem;
        display: flex;
        align-items: center;
    }
    
    .auth-header {
        text-align: center;
        margin-bottom: 2rem;
    }
    
    .auth-icon {
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, #4f46e5, #7c3aed);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem;
        color: white;
        font-size: 2rem;
    }
    
    .auth-title {
        color: #1f2937;
        font-size: 2rem;
        font-weight: 800;
        margin-bottom: 0.5rem;
    }
    
    .auth-subtitle {
        color: #6b7280;
        margin: 0;
        font-size: 1.1rem;
    }
    
    .auth-form {
        margin-top: 2rem;
    }
    
    /* Enhanced Form Styles */
    .form-group {
        margin-bottom: 1.5rem;
    }
    
    .form-label {
        display: block;
        color: #374151;
        font-weight: 600;
        margin-bottom: 0.5rem;
        font-size: 0.95rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .form-label i {
        color: #4f46e5;
        width: 16px;
    }
    
    .input-container {
        position: relative;
        display: flex;
        align-items: center;
    }
    
    .form-input {
        width: 100%;
        padding: 1rem 1rem 1rem 3rem;
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        font-size: 1rem;
        transition: all 0.3s ease;
        background: #fafafa;
        color: #374151;
        font-family: 'Inter', sans-serif;
    }
    
    .form-input:focus {
        outline: none;
        border-color: #4f46e5;
        background: white;
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        transform: translateY(-1px);
    }
    
    .form-input::placeholder {
        color: #9ca3af;
    }
    
    .input-icon {
        position: absolute;
        left: 1rem;
        color: #6b7280;
        font-size: 1.1rem;
        transition: color 0.3s ease;
        pointer-events: none;
    }
    
    .form-input:focus + .input-icon {
        color: #4f46e5;
    }
    
    .password-toggle {
        position: absolute;
        right: 1rem;
        background: none;
        border: none;
        color: #6b7280;
        cursor: pointer;
        padding: 0.25rem;
        border-radius: 4px;
        transition: all 0.3s ease;
    }
    
    .password-toggle:hover {
        color: #4f46e5;
        background: #f3f4f6;
    }
    
    .form-options {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
    }
    
    .checkbox-label {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        color: #6b7280;
        cursor: pointer;
        font-size: 0.95rem;
        transition: color 0.3s ease;
    }
    
    .checkbox-label:hover {
        color: #374151;
    }
    
    .checkbox-label input[type="checkbox"] {
        display: none;
    }
    
    .checkmark {
        width: 20px;
        height: 20px;
        border: 2px solid #d1d5db;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        background: white;
    }
    
    .checkbox-label input[type="checkbox"]:checked + .checkmark {
        background: #4f46e5;
        border-color: #4f46e5;
    }
    
    .checkbox-label input[type="checkbox"]:checked + .checkmark::after {
        content: '✓';
        color: white;
        font-size: 0.8rem;
        font-weight: bold;
    }
    
    .forgot-password {
        color: #4f46e5;
        text-decoration: none;
        font-size: 0.95rem;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    
    .forgot-password:hover {
        text-decoration: underline;
        color: #3730a3;
    }
    
    .btn-auth {
        width: 100%;
        justify-content: center;
        padding: 1rem 2rem;
        font-size: 1.1rem;
        margin-bottom: 1.5rem;
        border-radius: 12px;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .auth-footer {
        text-align: center;
        color: #6b7280;
    }
    
    .auth-link {
        color: #4f46e5;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .auth-link:hover {
        text-decoration: underline;
        color: #3730a3;
    }
    
    /* Promo Section */
    .promo-content h2 {
        font-size: 2rem;
        margin-bottom: 1rem;
        font-weight: 700;
    }
    
    .promo-content p {
        opacity: 0.9;
        margin-bottom: 2rem;
        font-size: 1.1rem;
        line-height: 1.6;
    }
    
    .promo-features {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }
    
    .feature-item {
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    
    .feature-item i {
        font-size: 2rem;
        opacity: 0.9;
        width: 50px;
        text-align: center;
    }
    
    .feature-item h4 {
        margin: 0 0 0.25rem 0;
        font-size: 1.1rem;
    }
    
    .feature-item span {
        opacity: 0.8;
        font-size: 0.9rem;
    }
    
    /* Alert Styles */
    .alert {
        padding: 1rem 1.5rem;
        border-radius: 12px;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-weight: 500;
        animation: slideIn 0.3s ease;
    }
    
    .alert-error {
        background: #fef2f2;
        color: #dc2626;
        border: 1px solid #fecaca;
    }
    
    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    /* Responsive Design */
    @media (max-width: 768px) {
        .auth-card {
            grid-template-columns: 1fr;
            max-width: 500px;
        }
        
        .auth-promo-section {
            display: none;
        }
        
        .auth-form-section {
            padding: 2rem;
        }
        
        .form-options {
            flex-direction: column;
            gap: 1rem;
            align-items: flex-start;
        }
    }
    
    @media (max-width: 480px) {
        .auth-container {
            padding: 1rem;
        }
        
        .auth-form-section {
            padding: 1.5rem;
        }
        
        .auth-title {
            font-size: 1.75rem;
        }
        
        .form-input {
            padding: 0.875rem 0.875rem 0.875rem 2.5rem;
        }
        
        .input-icon {
            left: 0.875rem;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Password toggle functionality
        const passwordToggle = document.getElementById('passwordToggle');
        const passwordInput = document.getElementById('password');
        
        if (passwordToggle && passwordInput) {
            passwordToggle.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                
                // Toggle eye icon
                const icon = this.querySelector('i');
                if (type === 'password') {
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                } else {
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                }
            });
        }
        
        // Add focus effects to form inputs
        const formInputs = document.querySelectorAll('.form-input');
        formInputs.forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.classList.add('focused');
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.classList.remove('focused');
            });
        });
    });
</script>

<?php include '../includes/footer.php'; ?>