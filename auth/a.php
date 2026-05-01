<?php
require_once __DIR__ . '/../config/db.php';



if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role']; // Get the role from the form
    
    // Check if email already exists
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    
    if($stmt->rowCount() > 0) {
        $error = "Email already registered";
    } else {
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
        if($stmt->execute([$name, $email, $password, $role])) {
            $success = "User registered successfully as " . ucfirst($role);
        } else {
            $error = "Registration failed";
        }
    }
}
?>

<?php include '/book_review/includes/header.php'; ?>

<div class="card p-4" style="max-width: 800px; margin: 2rem auto;">
    <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 2rem;">
        <div style="background: #dc2626; color: white; width: 60px; height: 60px; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
            <i class="fas fa-user-shield" style="font-size: 1.5rem;"></i>
        </div>
        <div>
            <h1 style="color: #1f2937; margin: 0;">Admin User Registration</h1>
            <p style="color: #6b7280; margin: 0;">Register new users with specific roles</p>
        </div>
    </div>

    <?php if(isset($success)): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            <?php echo $success; ?>
        </div>
    <?php endif; ?>

    <?php if(isset($error)): ?>
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i>
            <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
        <div>
            <form method="POST">
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-user"></i>
                        Full Name
                    </label>
                    <input type="text" name="name" class="form-input" required placeholder="Enter user's full name">
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-envelope"></i>
                        Email Address
                    </label>
                    <input type="email" name="email" class="form-input" required placeholder="Enter user's email">
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-lock"></i>
                        Password
                    </label>
                    <input type="password" name="password" class="form-input" required placeholder="Create a password">
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-user-tag"></i>
                        User Role
                    </label>
                    <select name="role" class="form-input" required>
                        <option value="user">Regular User</option>
                        <option value="admin">Administrator</option>
                        <option value="manager">Manager</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; margin-top: 1rem;">
                    <i class="fas fa-user-plus"></i>
                    Register User
                </button>
            </form>
        </div>

        <div>
            <div class="card p-4" style="background: #f8fafc; height: 100%;">
                <h3 style="color: #1f2937; margin-bottom: 1.5rem;">
                    <i class="fas fa-info-circle"></i>
                    User Roles Information
                </h3>
                
                <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                    <div>
                        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                            <div style="background: #dc2626; color: white; width: 20px; height: 20px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.75rem;">
                                A
                            </div>
                            <strong style="color: #1f2937;">Administrator</strong>
                        </div>
                        <p style="color: #6b7280; font-size: 0.875rem; margin: 0;">
                            Full system access including user management, book management, and review moderation.
                        </p>
                    </div>
                    
                    <div>
                        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                            <div style="background: #f59e0b; color: white; width: 20px; height: 20px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.75rem;">
                                M
                            </div>
                            <strong style="color: #1f2937;">Manager</strong>
                        </div>
                        <p style="color: #6b7280; font-size: 0.875rem; margin: 0;">
                            Can manage books and moderate reviews, but cannot manage user accounts.
                        </p>
                    </div>
                    
                    <div>
                        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                            <div style="background: #4f46e5; color: white; width: 20px; height: 20px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.75rem;">
                                U
                            </div>
                            <strong style="color: #1f2937;">Regular User</strong>
                        </div>
                        <p style="color: #6b7280; font-size: 0.875rem; margin: 0;">
                            Can browse books, write reviews, and manage their own profile and reviews.
                        </p>
                    </div>
                </div>

                <div style="margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid #e5e7eb;">
                    <h4 style="color: #1f2937; margin-bottom: 1rem;">Security Notice</h4>
                    <p style="color: #6b7280; font-size: 0.875rem; margin: 0;">
                        <i class="fas fa-shield-alt" style="color: #dc2626;"></i>
                        This page is restricted to administrators only. All user registrations are logged.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div style="margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid #e5e7eb;">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <a href="/book_review/auth/admin/dashboard.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i>
                Back to Dashboard
            </a>
            <a href="/book_review/auth/admin/manage_users.php" class="btn" style="background: #10b981; color: white;">
                <i class="fas fa-users"></i>
                Manage Users
            </a>
        </div>
    </div>
</div>

<?php include '/book_review/includes/footer.php'; ?>