<?php
require_once __DIR__ . '/../../config/db.php';

require_once '../../includes/auth_check.php';
if($_SESSION['role'] != 'admin') {
    header("Location: ../../index.php");
    exit();
}

if(isset($_GET['delete'])) {
    $user_id = $_GET['delete'];
    // Prevent admin from deleting themselves
    if($user_id != $_SESSION['user_id']) {
        $stmt = $pdo->prepare("DELETE FROM users WHERE user_id = ?");
        if($stmt->execute([$user_id])) {
            $success = "User deleted successfully!";
        } else {
            $error = "Failed to delete user.";
        }
    } else {
        $error = "You cannot delete your own account.";
    }
}

$stmt = $pdo->query("SELECT * FROM users ORDER BY created_at DESC");
$users = $stmt->fetchAll();

// Get user statistics
$stmt = $pdo->query("SELECT COUNT(*) as total_users FROM users");
$total_users = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) as admin_users FROM users WHERE role = 'admin'");
$admin_users = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) as regular_users FROM users WHERE role = 'user'");
$regular_users = $stmt->fetchColumn();

// Get recent users (last 7 days)
$stmt = $pdo->query("SELECT COUNT(*) as recent_users FROM users WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)");
$recent_users = $stmt->fetchColumn();
?>

<?php include '../../includes/header.php'; ?>

<!-- Hero Section -->
<div class="hero-section" style="
    background: linear-gradient(135deg, rgba(16, 185, 129, 0.9), rgba(5, 150, 105, 0.9)),
                url('https://images.unsplash.com/photo-1551434678-e076c223a692?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80');
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
                    User Management
                </h1>
                
                <p style="
                    font-size: 1.2rem;
                    color: rgba(255, 255, 255, 0.9);
                    margin-bottom: 2rem;
                    line-height: 1.6;
                ">
                    Manage user accounts, monitor activity, and maintain system security.
                </p>
                
                <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                    <a href="../admin/dashboard.php" class="btn btn-outline" style="
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
                        <i class="fas fa-arrow-left"></i>
                        Back to Dashboard
                    </a>
                    
                    <a href="../admin/manage_books.php" class="btn btn-primary" style="
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
                        Manage Books
                    </a>
                </div>
            </div>
            
            <div class="admin-stats" style="
                background: rgba(255, 255, 255, 0.15);
                backdrop-filter: blur(20px);
                border: 1px solid rgba(255, 255, 255, 0.3);
                padding: 2rem;
                border-radius: 16px;
                color: white;
                min-width: 280px;
            ">
                <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1.5rem;">
                    <div style="
                        background: rgba(255, 255, 255, 0.2);
                        width: 50px;
                        height: 50px;
                        border-radius: 10px;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        font-size: 1.5rem;
                    ">
                        <i class="fas fa-chart-pie"></i>
                    </div>
                    <div>
                        <div style="font-size: 0.9rem; opacity: 0.8;">User Analytics</div>
                        <div style="font-size: 1.1rem; font-weight: 700;">System Overview</div>
                    </div>
                </div>
                
                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span>Total Users</span>
                        <span style="font-weight: 700; font-size: 1.2rem;"><?php echo $total_users; ?></span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span>Admin Users</span>
                        <span style="font-weight: 700; font-size: 1.2rem; color: #fbbf24;"><?php echo $admin_users; ?></span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span>Regular Users</span>
                        <span style="font-weight: 700; font-size: 1.2rem;"><?php echo $regular_users; ?></span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span>New This Week</span>
                        <span style="font-weight: 700; font-size: 1.2rem; color: #34d399;"><?php echo $recent_users; ?></span>
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
        <?php if(isset($success)): ?>
            <div class="alert alert-success" style="
                background: #d1fae5;
                color: #065f46;
                padding: 1rem 1.5rem;
                border-radius: 10px;
                border: 1px solid #a7f3d0;
                margin-bottom: 2rem;
                display: flex;
                align-items: center;
                gap: 0.75rem;
                font-weight: 600;
            ">
                <i class="fas fa-check-circle" style="color: #10b981;"></i>
                <?php echo $success; ?>
            </div>
        <?php endif; ?>
        
        <?php if(isset($error)): ?>
            <div class="alert alert-error" style="
                background: #fee2e2;
                color: #991b1b;
                padding: 1rem 1.5rem;
                border-radius: 10px;
                border: 1px solid #fecaca;
                margin-bottom: 2rem;
                display: flex;
                align-items: center;
                gap: 0.75rem;
                font-weight: 600;
            ">
                <i class="fas fa-exclamation-circle" style="color: #ef4444;"></i>
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <!-- Users Table Card -->
        <div class="users-card" style="
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
                    background: linear-gradient(135deg, #10b981, #059669);
                    width: 60px;
                    height: 60px;
                    border-radius: 12px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    color: white;
                    font-size: 1.5rem;
                ">
                    <i class="fas fa-users"></i>
                </div>
                <div>
                    <h2 style="
                        color: #1f2937;
                        font-size: 1.5rem;
                        font-weight: 700;
                        margin: 0 0 0.25rem 0;
                    ">All Users</h2>
                    <p style="color: #6b7280; margin: 0;">Manage user accounts and permissions</p>
                </div>
                <div style="
                    background: #10b981;
                    color: white;
                    padding: 0.5rem 1.2rem;
                    border-radius: 20px;
                    font-weight: 700;
                    margin-left: auto;
                    font-size: 0.9rem;
                ">
                    <?php echo count($users); ?> Users
                </div>
            </div>
            
            <div style="overflow-x: auto; border-radius: 12px; border: 1px solid #e5e7eb;">
                <table style="width: 100%; border-collapse: collapse; min-width: 800px;">
                    <thead>
                        <tr style="background: linear-gradient(135deg, #f8fafc, #f1f5f9);">
                            <th style="padding: 1.25rem 1.5rem; text-align: left; font-weight: 700; color: #374151; border-bottom: 2px solid #e5e7eb; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.5px;">
                                <i class="fas fa-user"></i>
                                User Profile
                            </th>
                            <th style="padding: 1.25rem 1.5rem; text-align: left; font-weight: 700; color: #374151; border-bottom: 2px solid #e5e7eb; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.5px;">
                                <i class="fas fa-envelope"></i>
                                Email Address
                            </th>
                            <th style="padding: 1.25rem 1.5rem; text-align: left; font-weight: 700; color: #374151; border-bottom: 2px solid #e5e7eb; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.5px;">
                                <i class="fas fa-shield-alt"></i>
                                Account Role
                            </th>
                            <th style="padding: 1.25rem 1.5rem; text-align: left; font-weight: 700; color: #374151; border-bottom: 2px solid #e5e7eb; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.5px;">
                                <i class="fas fa-calendar-plus"></i>
                                Member Since
                            </th>
                            <th style="padding: 1.25rem 1.5rem; text-align: left; font-weight: 700; color: #374151; border-bottom: 2px solid #e5e7eb; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.5px;">
                                <i class="fas fa-cog"></i>
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($users as $user): ?>
                            <tr style="
                                border-bottom: 1px solid #f1f5f9;
                                transition: all 0.3s ease;
                                <?php if($user['user_id'] == $_SESSION['user_id']): ?>
                                    background: linear-gradient(135deg, #f0f9ff, #e0f2fe);
                                <?php endif; ?>
                            ">
                                <td style="padding: 1.5rem;">
                                    <div style="display: flex; align-items: center; gap: 1rem;">
                                        <div style="
                                            background: <?php echo $user['role'] == 'admin' ? 'linear-gradient(135deg, #dc2626, #ef4444)' : 'linear-gradient(135deg, #4f46e5, #7c3aed)'; ?>;
                                            color: white;
                                            width: 50px;
                                            height: 50px;
                                            border-radius: 12px;
                                            display: flex;
                                            align-items: center;
                                            justify-content: center;
                                            font-weight: 700;
                                            font-size: 1.1rem;
                                            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                                        ">
                                            <?php echo strtoupper(substr($user['name'], 0, 1)); ?>
                                        </div>
                                        <div>
                                            <div style="font-weight: 700; color: #1f2937; font-size: 1.1rem;"><?php echo htmlspecialchars($user['name']); ?></div>
                                            <?php if($user['user_id'] == $_SESSION['user_id']): ?>
                                                <div style="color: #0369a1; font-size: 0.8rem; font-weight: 600; display: flex; align-items: center; gap: 0.25rem;">
                                                    <i class="fas fa-circle" style="font-size: 0.5rem;"></i>
                                                    Current Session
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td style="padding: 1.5rem;">
                                    <div style="color: #6b7280; font-weight: 500;"><?php echo htmlspecialchars($user['email']); ?></div>
                                </td>
                                <td style="padding: 1.5rem;">
                                    <span style="
                                        background: <?php echo $user['role'] == 'admin' ? 'linear-gradient(135deg, #dc2626, #ef4444)' : 'linear-gradient(135deg, #4f46e5, #7c3aed)'; ?>;
                                        color: white;
                                        padding: 0.5rem 1rem;
                                        border-radius: 20px;
                                        font-size: 0.8rem;
                                        font-weight: 700;
                                        text-transform: uppercase;
                                        letter-spacing: 0.5px;
                                        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                                    ">
                                        <i class="fas <?php echo $user['role'] == 'admin' ? 'fa-crown' : 'fa-user'; ?>"></i>
                                        <?php echo ucfirst($user['role']); ?>
                                    </span>
                                </td>
                                <td style="padding: 1.5rem;">
                                    <div style="color: #6b7280; font-weight: 500;">
                                        <div style="font-size: 0.9rem; font-weight: 600; color: #374151;"><?php echo date('M j, Y', strtotime($user['created_at'])); ?></div>
                                        <div style="font-size: 0.8rem; color: #9ca3af;"><?php echo date('g:i A', strtotime($user['created_at'])); ?></div>
                                    </div>
                                </td>
                                <td style="padding: 1.5rem;">
                                    <?php if($user['user_id'] != $_SESSION['user_id']): ?>
                                        <a href="?delete=<?php echo $user['user_id']; ?>" 
                                           onclick="return confirm('Are you sure you want to delete \"<?php echo addslashes($user['name']); ?>\"?\\n\\nThis action will permanently remove the user account and all their reviews.\\nThis action cannot be undone!')"
                                           class="btn" style="
                                            background: linear-gradient(135deg, #ef4444, #dc2626);
                                            color: white;
                                            padding: 0.6rem 1.2rem;
                                            border-radius: 8px;
                                            text-decoration: none;
                                            font-weight: 600;
                                            font-size: 0.8rem;
                                            display: inline-flex;
                                            align-items: center;
                                            gap: 0.5rem;
                                            transition: all 0.3s ease;
                                            border: none;
                                            cursor: pointer;
                                        " title="Delete User">
                                            <i class="fas fa-trash"></i>
                                            Delete
                                        </a>
                                    <?php else: ?>
                                        <div style="
                                            background: #dbeafe;
                                            color: #1e40af;
                                            padding: 0.6rem 1rem;
                                            border-radius: 8px;
                                            font-weight: 600;
                                            font-size: 0.8rem;
                                            display: inline-flex;
                                            align-items: center;
                                            gap: 0.5rem;
                                        ">
                                            <i class="fas fa-user-shield"></i>
                                            Current User
                                        </div>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <?php if(count($users) === 0): ?>
                <div style="
                    text-align: center;
                    padding: 4rem 2rem;
                    color: #6b7280;
                ">
                    <div style="
                        width: 100px;
                        height: 100px;
                        background: #f1f5f9;
                        border-radius: 50%;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        margin: 0 auto 1.5rem;
                        color: #94a3b8;
                        font-size: 2.5rem;
                    ">
                        <i class="fas fa-users-slash"></i>
                    </div>
                    <h3 style="color: #475569; margin-bottom: 0.5rem; font-size: 1.5rem;">No Users Found</h3>
                    <p style="margin: 0; font-size: 1rem;">There are no user accounts in the system.</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Quick Stats -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-top: 2rem;">
            <div style="
                background: linear-gradient(135deg, #10b981, #059669);
                color: white;
                padding: 2rem;
                border-radius: 16px;
                text-align: center;
            ">
                <div style="font-size: 2.5rem; font-weight: 800; margin-bottom: 0.5rem;"><?php echo $total_users; ?></div>
                <div style="font-weight: 600;">Total Users</div>
            </div>
            
            <div style="
                background: linear-gradient(135deg, #f59e0b, #d97706);
                color: white;
                padding: 2rem;
                border-radius: 16px;
                text-align: center;
            ">
                <div style="font-size: 2.5rem; font-weight: 800; margin-bottom: 0.5rem;"><?php echo $admin_users; ?></div>
                <div style="font-weight: 600;">Admin Users</div>
            </div>
            
            <div style="
                background: linear-gradient(135deg, #4f46e5, #7c3aed);
                color: white;
                padding: 2rem;
                border-radius: 16px;
                text-align: center;
            ">
                <div style="font-size: 2.5rem; font-weight: 800; margin-bottom: 0.5rem;"><?php echo $regular_users; ?></div>
                <div style="font-weight: 600;">Regular Users</div>
            </div>
            
            <div style="
                background: linear-gradient(135deg, #ec4899, #db2777);
                color: white;
                padding: 2rem;
                border-radius: 16px;
                text-align: center;
            ">
                <div style="font-size: 2.5rem; font-weight: 800; margin-bottom: 0.5rem;"><?php echo $recent_users; ?></div>
                <div style="font-weight: 600;">New This Week</div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Hover Effects */
    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15);
    }

    .btn-primary:hover {
        background: rgba(255, 255, 255, 0.3) !important;
    }

    .btn-outline:hover {
        background: rgba(255, 255, 255, 0.15) !important;
    }

    .users-card tbody tr:hover {
        background: #f8fafc !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }

    .users-card tbody tr[style*="background: linear-gradient"]:hover {
        background: linear-gradient(135deg, #e0f2fe, #bae6fd) !important;
    }

    /* Table Responsive Design */
    @media (max-width: 1024px) {
        .hero-content {
            grid-template-columns: 1fr;
            gap: 2rem;
            text-align: center;
        }
        
        .admin-stats {
            max-width: 400px;
            margin: 0 auto;
        }
    }

    @media (max-width: 768px) {
        .hero-section h1 {
            font-size: 2.25rem;
        }
        
        .users-card {
            padding: 2rem !important;
        }
        
        .users-card > div:first-child {
            flex-direction: column;
            text-align: center;
            gap: 1rem;
        }
        
        .users-card > div:first-child > div:last-child {
            margin-left: 0 !important;
        }
        
        table th, table td {
            padding: 1rem !important;
        }
    }

    @media (max-width: 480px) {
        .hero-section {
            padding: 2rem 1rem;
        }
        
        .main-content-wide {
            padding: 1.5rem 1rem;
        }
        
        .users-card {
            padding: 1.5rem !important;
        }
        
        .users-card > div:last-child {
            border-radius: 8px !important;
        }
        
        table {
            min-width: 600px;
        }
    }

    /* Scrollbar Styling */
    .users-card > div::-webkit-scrollbar {
        height: 8px;
    }

    .users-card > div::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 10px;
    }

    .users-card > div::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 10px;
    }

    .users-card > div::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add hover effects to table rows
        const tableRows = document.querySelectorAll('.users-card tbody tr');
        tableRows.forEach(row => {
            row.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-1px)';
                this.style.boxShadow = '0 4px 12px rgba(0, 0, 0, 0.05)';
            });
            
            row.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
                this.style.boxShadow = 'none';
            });
        });

        // Enhanced delete confirmation
        const deleteButtons = document.querySelectorAll('a[onclick*="confirm"]');
        deleteButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                const confirmMessage = this.getAttribute('onclick').match(/'([^']+)'/)[1];
                if(!confirm(confirmMessage.replace(/\\n/g, '\n'))) {
                    e.preventDefault();
                }
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
    });
</script>

<?php include '../../includes/footer.php'; ?>