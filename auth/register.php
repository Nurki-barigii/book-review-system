<?php
require_once '../config/db.php';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    // Check if email already exists
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    
    if($stmt->rowCount() > 0) {
        $error = "Email already registered";
    } else {
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'user')");
        if($stmt->execute([$name, $email, $password])) {
            header("Location: login.php");
            exit();
        } else {
            $error = "Registration failed";
        }
    }
}
?>

<?php include '../includes/header.php'; ?>

<div class="main-container">
    <div class="auth-container">
        <div class="auth-card">
            <!-- Promo Section -->
            <div class="auth-promo-section">
                <div class="promo-background">
                    <div class="floating-book book-1">
                        <i class="fas fa-book"></i>
                    </div>
                    <div class="floating-book book-2">
                        <i class="fas fa-book-open"></i>
                    </div>
                    <div class="floating-book book-3">
                        <i class="fas fa-star"></i>
                    </div>
                </div>
                <div class="promo-content">
                    <div class="promo-header">
                        <div class="promo-icon">
                            <i class="fas fa-book-reader"></i>
                        </div>
                        <h2>Start Your Reading Journey</h2>
                    </div>
                    <p>Create an account to unlock all features and join our vibrant community of readers.</p>
                    
                    <div class="promo-features">
                        <div class="feature-item">
                            <div class="feature-icon">
                                <i class="fas fa-pen-fancy"></i>
                            </div>
                            <div class="feature-text">
                                <h4>Write Reviews</h4>
                                <span>Share your thoughts on books</span>
                            </div>
                        </div>
                        <div class="feature-item">
                            <div class="feature-icon">
                                <i class="fas fa-heart"></i>
                            </div>
                            <div class="feature-text">
                                <h4>Save Favorites</h4>
                                <span>Build your personal library</span>
                            </div>
                        </div>
                        <div class="feature-item">
                            <div class="feature-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="feature-text">
                                <h4>Join Discussions</h4>
                                <span>Connect with other readers</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="promo-stats">
                        <div class="stat">
                            <strong>10K+</strong>
                            <span>Books</span>
                        </div>
                        <div class="stat">
                            <strong>50K+</strong>
                            <span>Readers</span>
                        </div>
                        <div class="stat">
                            <strong>100K+</strong>
                            <span>Reviews</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Register Form -->
            <div class="auth-form-section">
                <div class="form-header">
                    <div class="auth-icon">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <h1 class="auth-title">Join BookReview</h1>
                    <p class="auth-subtitle">Create your account in seconds</p>
                </div>

                <?php if(isset($error)): ?>
                    <div class="alert alert-error">
                        <div class="alert-icon">
                            <i class="fas fa-exclamation-circle"></i>
                        </div>
                        <div class="alert-content">
                            <strong>Registration Error</strong>
                            <p><?php echo $error; ?></p>
                        </div>
                    </div>
                <?php endif; ?>

                <form method="POST" class="auth-form" id="registerForm">
                    <div class="form-group">
                        <label class="form-label">
                            Full Name
                        </label>
                        <div class="input-container">
                            <input type="text" name="name" class="form-input" required 
                                   placeholder="Enter your full name" 
                                   value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>"
                                   data-validation="name">
                            <div class="input-icon">
                                <i class="fas fa-user"></i>
                            </div>
                            <div class="input-actions">
                                <div class="validation-icon valid">
                                    <i class="fas fa-check"></i>
                                </div>
                                <div class="validation-icon invalid">
                                    <i class="fas fa-times"></i>
                                </div>
                            </div>
                        </div>
                        <div class="input-hint">Your full name as you'd like it to appear</div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            Email Address
                        </label>
                        <div class="input-container">
                            <input type="email" name="email" class="form-input" required 
                                   placeholder="Enter your email address" 
                                   value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                                   data-validation="email">
                            <div class="input-icon">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div class="input-actions">
                                <div class="validation-icon valid">
                                    <i class="fas fa-check"></i>
                                </div>
                                <div class="validation-icon invalid">
                                    <i class="fas fa-times"></i>
                                </div>
                            </div>
                        </div>
                        <div class="input-hint">We'll never share your email with anyone</div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            Password
                        </label>
                        <div class="input-container">
                            <input type="password" name="password" class="form-input" required 
                                   placeholder="Create a secure password" 
                                   id="password"
                                   data-validation="password"
                                   minlength="8">
                            <div class="input-icon">
                                <i class="fas fa-lock"></i>
                            </div>
                            <div class="input-actions">
                                <button type="button" class="password-toggle" id="passwordToggle" title="Toggle password visibility">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <div class="validation-icon valid">
                                    <i class="fas fa-check"></i>
                                </div>
                                <div class="validation-icon invalid">
                                    <i class="fas fa-times"></i>
                                </div>
                            </div>
                        </div>
                        <div class="password-strength">
                            <div class="strength-bar">
                                <div class="strength-fill" id="strengthFill"></div>
                            </div>
                            <div class="strength-text" id="strengthText">Password strength</div>
                        </div>
                    </div>

                    <div class="form-group terms-group">
                        <label class="checkbox-label full-width">
                            <input type="checkbox" name="terms" required id="termsCheckbox">
                            <span class="checkmark"></span>
                            <span class="checkbox-text">
                                I agree to the <a href="#" class="terms-link">Terms of Service</a> and <a href="#" class="terms-link">Privacy Policy</a>
                            </span>
                        </label>
                    </div>

                    <button type="submit" class="btn btn-primary btn-auth" id="submitBtn">
                        <span class="btn-text">
                            <i class="fas fa-user-plus"></i>
                            Create Account
                        </span>
                        <div class="btn-loading">
                            <div class="loading-spinner"></div>
                            Creating your account...
                        </div>
                    </button>
                </form>

                <div class="auth-footer">
                    <div class="divider">
                        <span>Already have an account?</span>
                    </div>
                    <a href="login.php" class="btn btn-secondary btn-outline">
                        <i class="fas fa-sign-in-alt"></i>
                        Sign In Instead
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Enhanced Auth Container */
    .auth-container {
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 100vh;
        padding: 2rem 0;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    
    .auth-card {
        display: grid;
        grid-template-columns: 1fr 1fr;
        max-width: 1200px;
        width: 95%;
        background: white;
        border-radius: 24px;
        overflow: hidden;
        box-shadow: 0 25px 80px rgba(0, 0, 0, 0.15);
        min-height: 700px;
    }
    
    /* Enhanced Promo Section */
    .auth-promo-section {
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
        color: white;
        padding: 3rem;
        display: flex;
        align-items: center;
        position: relative;
        overflow: hidden;
    }
    
    .promo-background {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        opacity: 0.1;
    }
    
    .floating-book {
        position: absolute;
        font-size: 2rem;
        opacity: 0.6;
        animation: float 6s ease-in-out infinite;
    }
    
    .book-1 {
        top: 20%;
        left: 20%;
        animation-delay: 0s;
    }
    
    .book-2 {
        top: 60%;
        right: 25%;
        animation-delay: 2s;
    }
    
    .book-3 {
        bottom: 20%;
        left: 30%;
        animation-delay: 4s;
    }
    
    @keyframes float {
        0%, 100% { transform: translateY(0px) rotate(0deg); }
        50% { transform: translateY(-20px) rotate(5deg); }
    }
    
    .promo-content {
        position: relative;
        z-index: 2;
        width: 100%;
    }
    
    .promo-header {
        text-align: center;
        margin-bottom: 2rem;
    }
    
    .promo-icon {
        width: 80px;
        height: 80px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem;
        color: white;
        font-size: 2.5rem;
        backdrop-filter: blur(10px);
        border: 2px solid rgba(255, 255, 255, 0.3);
    }
    
    .promo-content h2 {
        font-size: 2.5rem;
        margin-bottom: 1rem;
        font-weight: 800;
        background: linear-gradient(135deg, #fff, #fbbf24);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    
    .promo-content p {
        opacity: 0.9;
        margin-bottom: 3rem;
        font-size: 1.2rem;
        line-height: 1.6;
        text-align: center;
    }
    
    .promo-features {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
        margin-bottom: 3rem;
    }
    
    .feature-item {
        display: flex;
        align-items: center;
        gap: 1.5rem;
        padding: 1.5rem;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 16px;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        transition: all 0.3s ease;
    }
    
    .feature-item:hover {
        transform: translateX(10px);
        background: rgba(255, 255, 255, 0.15);
    }
    
    .feature-icon {
        width: 60px;
        height: 60px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        flex-shrink: 0;
    }
    
    .feature-text h4 {
        margin: 0 0 0.5rem 0;
        font-size: 1.2rem;
        font-weight: 600;
    }
    
    .feature-text span {
        opacity: 0.8;
        font-size: 0.95rem;
    }
    
    .promo-stats {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1rem;
        text-align: center;
    }
    
    .stat {
        padding: 1rem;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 12px;
        backdrop-filter: blur(10px);
    }
    
    .stat strong {
        display: block;
        font-size: 1.5rem;
        font-weight: 800;
        margin-bottom: 0.25rem;
    }
    
    .stat span {
        font-size: 0.9rem;
        opacity: 0.8;
    }
    
    /* Enhanced Form Section */
    .auth-form-section {
        padding: 3rem;
        display: flex;
        flex-direction: column;
        justify-content: center;
        background: white;
    }
    
    .form-header {
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
        box-shadow: 0 10px 30px rgba(79, 70, 229, 0.3);
    }
    
    .auth-title {
        color: #1f2937;
        font-size: 2.2rem;
        font-weight: 800;
        margin-bottom: 0.5rem;
        background: linear-gradient(135deg, #1f2937, #4f46e5);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
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
        margin-bottom: 2rem;
        position: relative;
    }
    
    .form-label {
        display: block;
        color: #374151;
        font-weight: 600;
        margin-bottom: 0.75rem;
        font-size: 1rem;
        transition: color 0.3s ease;
    }
    
    .input-container {
        position: relative;
        display: flex;
        align-items: center;
        transition: all 0.3s ease;
    }
    
    .form-input {
        width: 100%;
        padding: 1.25rem 1rem 1.25rem 3.5rem;
        border: 2px solid #f1f5f9;
        border-radius: 16px;
        font-size: 1rem;
        transition: all 0.3s ease;
        background: #fafafa;
        color: #374151;
        font-family: 'Inter', sans-serif;
        font-weight: 500;
    }
    
    .form-input:focus {
        outline: none;
        border-color: #4f46e5;
        background: white;
        box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
        transform: translateY(-2px);
    }
    
    .form-input::placeholder {
        color: #9ca3af;
        font-weight: 400;
    }
    
    .input-icon {
        position: absolute;
        left: 1.25rem;
        color: #6b7280;
        font-size: 1.2rem;
        transition: all 0.3s ease;
        pointer-events: none;
        z-index: 2;
    }
    
    .form-input:focus + .input-icon {
        color: #4f46e5;
        transform: scale(1.1);
    }
    
    .input-actions {
        position: absolute;
        right: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .validation-icon {
        width: 20px;
        height: 20px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.7rem;
        opacity: 0;
        transform: scale(0);
        transition: all 0.3s ease;
    }
    
    .validation-icon.valid {
        background: #10b981;
        color: white;
    }
    
    .validation-icon.invalid {
        background: #ef4444;
        color: white;
    }
    
    .form-input:valid:not(:placeholder-shown) + .input-icon + .input-actions .validation-icon.valid,
    .form-input:invalid:not(:placeholder-shown):not(:focus) + .input-icon + .input-actions .validation-icon.invalid {
        opacity: 1;
        transform: scale(1);
    }
    
    .password-toggle {
        background: none;
        border: none;
        color: #6b7280;
        cursor: pointer;
        padding: 0.5rem;
        border-radius: 8px;
        transition: all 0.3s ease;
        font-size: 1rem;
    }
    
    .password-toggle:hover {
        color: #4f46e5;
        background: #f3f4f6;
    }
    
    .input-hint {
        color: #6b7280;
        font-size: 0.875rem;
        margin-top: 0.5rem;
        padding-left: 0.5rem;
    }
    
    /* Password Strength */
    .password-strength {
        margin-top: 1rem;
    }
    
    .strength-bar {
        height: 6px;
        background: #f1f5f9;
        border-radius: 3px;
        overflow: hidden;
        margin-bottom: 0.5rem;
    }
    
    .strength-fill {
        height: 100%;
        width: 0%;
        background: #ef4444;
        border-radius: 3px;
        transition: all 0.3s ease;
    }
    
    .strength-text {
        color: #6b7280;
        font-size: 0.875rem;
        font-weight: 500;
    }
    
    /* Terms Checkbox */
    .terms-group {
        margin: 2rem 0;
    }
    
    .checkbox-label {
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        color: #374151;
        cursor: pointer;
        font-size: 0.95rem;
        transition: color 0.3s ease;
        line-height: 1.5;
    }
    
    .checkbox-label:hover {
        color: #1f2937;
    }
    
    .checkbox-label input[type="checkbox"] {
        display: none;
    }
    
    .checkmark {
        width: 22px;
        height: 22px;
        border: 2px solid #d1d5db;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        background: white;
        flex-shrink: 0;
        margin-top: 0.1rem;
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
    
    .checkbox-text {
        flex: 1;
    }
    
    .terms-link {
        color: #4f46e5;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .terms-link:hover {
        text-decoration: underline;
        color: #3730a3;
    }
    
    /* Enhanced Button */
    .btn-auth {
        width: 100%;
        justify-content: center;
        padding: 1.25rem 2rem;
        font-size: 1.1rem;
        border-radius: 16px;
        font-weight: 600;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    
    .btn-auth::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
        transition: left 0.5s;
    }
    
    .btn-auth:hover::before {
        left: 100%;
    }
    
    .btn-text, .btn-loading {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        transition: opacity 0.3s ease;
    }
    
    .btn-loading {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        opacity: 0;
    }
    
    .btn-auth.loading .btn-text {
        opacity: 0;
    }
    
    .btn-auth.loading .btn-loading {
        opacity: 1;
    }
    
    .loading-spinner {
        width: 20px;
        height: 20px;
        border: 2px solid transparent;
        border-top: 2px solid white;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }
    
    /* Alert Styles */
    .alert {
        padding: 1.5rem;
        border-radius: 16px;
        margin-bottom: 2rem;
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        animation: slideIn 0.3s ease;
        border: 1px solid;
    }
    
    .alert-error {
        background: #fef2f2;
        color: #dc2626;
        border-color: #fecaca;
    }
    
    .alert-icon {
        font-size: 1.5rem;
        flex-shrink: 0;
        margin-top: 0.1rem;
    }
    
    .alert-content strong {
        display: block;
        margin-bottom: 0.25rem;
        font-weight: 600;
    }
    
    .alert-content p {
        margin: 0;
        opacity: 0.9;
    }
    
    /* Auth Footer */
    .auth-footer {
        margin-top: 2rem;
        text-align: center;
    }
    
    .divider {
        position: relative;
        margin: 2rem 0;
        color: #6b7280;
        font-size: 0.9rem;
    }
    
    .divider::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 0;
        right: 0;
        height: 1px;
        background: #e5e7eb;
        z-index: 1;
    }
    
    .divider span {
        background: white;
        padding: 0 1rem;
        position: relative;
        z-index: 2;
    }
    
    .btn-outline {
        background: transparent;
        border: 2px solid #e5e7eb;
        color: #374151;
        width: 100%;
        justify-content: center;
    }
    
    .btn-outline:hover {
        background: #f8fafc;
        border-color: #d1d5db;
    }
    
    /* Animations */
    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    /* Responsive Design */
    @media (max-width: 1024px) {
        .auth-card {
            grid-template-columns: 1fr 1fr;
        }
        
        .auth-promo-section,
        .auth-form-section {
            padding: 2rem;
        }
    }
    
    @media (max-width: 768px) {
        .auth-container {
            padding: 1rem;
            background: white;
        }
        
        .auth-card {
            grid-template-columns: 1fr;
            max-width: 500px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        }
        
        .auth-promo-section {
            display: none;
        }
        
        .auth-form-section {
            padding: 2rem 1.5rem;
        }
        
        .promo-stats {
            grid-template-columns: repeat(3, 1fr);
        }
    }
    
    @media (max-width: 480px) {
        .auth-form-section {
            padding: 1.5rem 1rem;
        }
        
        .auth-title {
            font-size: 1.75rem;
        }
        
        .form-input {
            padding: 1rem 1rem 1rem 3rem;
        }
        
        .input-icon {
            left: 1rem;
        }
        
        .feature-item {
            padding: 1rem;
            gap: 1rem;
        }
        
        .feature-icon {
            width: 50px;
            height: 50px;
            font-size: 1.25rem;
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
        
        // Password strength indicator
        const passwordField = document.getElementById('password');
        const strengthFill = document.getElementById('strengthFill');
        const strengthText = document.getElementById('strengthText');
        
        if (passwordField && strengthFill && strengthText) {
            passwordField.addEventListener('input', function() {
                const password = this.value;
                let strength = 0;
                let text = 'Password strength';
                let color = '#ef4444';
                
                if (password.length >= 8) strength += 25;
                if (password.length >= 12) strength += 25;
                if (/[A-Z]/.test(password)) strength += 25;
                if (/[0-9]/.test(password)) strength += 15;
                if (/[^A-Za-z0-9]/.test(password)) strength += 10;
                
                strength = Math.min(strength, 100);
                strengthFill.style.width = strength + '%';
                
                if (strength < 40) {
                    text = 'Weak';
                    color = '#ef4444';
                } else if (strength < 70) {
                    text = 'Fair';
                    color = '#f59e0b';
                } else if (strength < 90) {
                    text = 'Good';
                    color = '#10b981';
                } else {
                    text = 'Strong';
                    color = '#059669';
                }
                
                strengthFill.style.background = color;
                strengthText.textContent = text;
                strengthText.style.color = color;
            });
        }
        
        // Form validation and enhanced interactions
        const formInputs = document.querySelectorAll('.form-input');
        const authForm = document.getElementById('registerForm');
        const submitBtn = document.getElementById('submitBtn');
        
        formInputs.forEach(input => {
            const container = input.parentElement;
            
            input.addEventListener('focus', function() {
                container.classList.add('focused');
                this.parentElement.style.transform = 'translateY(-2px)';
            });
            
            input.addEventListener('blur', function() {
                container.classList.remove('focused');
                this.parentElement.style.transform = 'translateY(0)';
            });
            
            input.addEventListener('input', function() {
                // Real-time validation feedback
                if (this.validity.valid && this.value.trim() !== '') {
                    this.classList.add('valid');
                    this.classList.remove('invalid');
                } else if (this.value.trim() !== '') {
                    this.classList.add('invalid');
                    this.classList.remove('valid');
                }
            });
        });
        
        // Form submission loading state
        if (authForm && submitBtn) {
            authForm.addEventListener('submit', function(e) {
                const termsCheckbox = document.getElementById('termsCheckbox');
                
                if (!termsCheckbox.checked) {
                    e.preventDefault();
                    termsCheckbox.parentElement.style.animation = 'shake 0.5s ease-in-out';
                    setTimeout(() => {
                        termsCheckbox.parentElement.style.animation = '';
                    }, 500);
                    return;
                }
                
                submitBtn.classList.add('loading');
                submitBtn.disabled = true;
                
                // Allow form to submit normally
            });
        }
        
        // Add shake animation for invalid fields
        const style = document.createElement('style');
        style.textContent = `
            @keyframes shake {
                0%, 100% { transform: translateX(0); }
                25% { transform: translateX(-5px); }
                75% { transform: translateX(5px); }
            }
        `;
        document.head.appendChild(style);
    });
</script>

<?php include '../includes/footer.php'; ?>