<?php
$projectRoot = dirname(__DIR__, 2);
require_once $projectRoot . '/config/db.php';
require_once $projectRoot . '/includes/auth_check.php';

if ($_SESSION['role'] != 'admin') {
    header('Location: ../../index.php');
    exit();
}

$upload_dir = $projectRoot . '/uploads/books/';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

/** Stored path in DB must be uploads/books/filename.pdf — blocks path traversal */
function manage_books_normalize_pdf_path(?string $path): ?string
{
    if ($path === null || $path === '') {
        return null;
    }
    $path = str_replace('\\', '/', $path);
    if (str_contains($path, '..') || !str_starts_with($path, 'uploads/books/')) {
        return null;
    }
    return $path;
}

function manage_books_is_pdf_upload(array $file): bool
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK || empty($file['tmp_name'])) {
        return false;
    }
    $ext = strtolower(pathinfo($file['name'] ?? '', PATHINFO_EXTENSION));
    if ($ext !== 'pdf') {
        return false;
    }
    if (function_exists('finfo_open')) {
        $f = finfo_open(FILEINFO_MIME_TYPE);
        if ($f) {
            $detected = finfo_file($f, $file['tmp_name']);
            finfo_close($f);
            return $detected === 'application/pdf';
        }
    }
    $mime = $file['type'] ?? '';
    return in_array($mime, ['application/pdf', 'application/x-pdf', 'application/octet-stream'], true);
}

// Handle Add Book
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_book'])) {
    $title = $_POST['title'];
    $author = $_POST['author'];
    $description = $_POST['description'];
    $year = $_POST['year'];
    $category = $_POST['category'];
    $price = $_POST['price'];
    $has_digital = isset($_POST['has_digital']) ? 1 : 0;
    $pdf_file = null;
    
    // Handle PDF upload
    if ($has_digital && isset($_FILES['pdf_file']) && manage_books_is_pdf_upload($_FILES['pdf_file'])) {
        $file_name = time() . '_' . preg_replace('/[^a-zA-Z0-9_\-]/', '', $title) . '.pdf';
        $file_path = $upload_dir . $file_name;
        if (move_uploaded_file($_FILES['pdf_file']['tmp_name'], $file_path)) {
            $pdf_file = 'uploads/books/' . $file_name;
        }
    }
    
    $stmt = $pdo->prepare("INSERT INTO books (title, author, description, year, category, price, has_digital, pdf_file) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    if($stmt->execute([$title, $author, $description, $year, $category, $price, $has_digital, $pdf_file])) {
        $success = "Book added successfully!";
    } else {
        $error = "Failed to add book.";
    }
}

// Handle Edit Book
if(isset($_GET['edit'])) {
    $edit_id = $_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM books WHERE book_id = ?");
    $stmt->execute([$edit_id]);
    $edit_book = $stmt->fetch();
}

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_book'])) {
    $book_id = $_POST['book_id'];
    $title = $_POST['title'];
    $author = $_POST['author'];
    $description = $_POST['description'];
    $year = $_POST['year'];
    $category = $_POST['category'];
    $price = $_POST['price'];
    $has_digital = isset($_POST['has_digital']) ? 1 : 0;
    $pdf_file = manage_books_normalize_pdf_path($_POST['existing_pdf'] ?? null);

    // Handle new PDF upload (optional)
    if (isset($_FILES['pdf_file']) && manage_books_is_pdf_upload($_FILES['pdf_file'])) {
        $old = manage_books_normalize_pdf_path($_POST['existing_pdf'] ?? null);
        if ($old && file_exists($projectRoot . '/' . $old)) {
            unlink($projectRoot . '/' . $old);
        }
        $file_name = time() . '_' . preg_replace('/[^a-zA-Z0-9_\-]/', '', $title) . '.pdf';
        $file_path = $upload_dir . $file_name;
        if (move_uploaded_file($_FILES['pdf_file']['tmp_name'], $file_path)) {
            $pdf_file = 'uploads/books/' . $file_name;
        }
    }
    
    $stmt = $pdo->prepare("UPDATE books SET title=?, author=?, description=?, year=?, category=?, price=?, has_digital=?, pdf_file=? WHERE book_id=?");
    if($stmt->execute([$title, $author, $description, $year, $category, $price, $has_digital, $pdf_file, $book_id])) {
        $success = "Book updated successfully!";
        header("Location: manage_books.php");
        exit();
    } else {
        $error = "Failed to update book.";
    }
}

// Handle Delete Book
if(isset($_GET['delete'])) {
    $book_id = $_GET['delete'];
    
    // Get PDF file before deleting
    $stmt = $pdo->prepare("SELECT pdf_file FROM books WHERE book_id = ?");
    $stmt->execute([$book_id]);
    $book = $stmt->fetch();
    
    $pdfPath = $book ? manage_books_normalize_pdf_path($book['pdf_file']) : null;
    if ($pdfPath && file_exists($projectRoot . '/' . $pdfPath)) {
        unlink($projectRoot . '/' . $pdfPath);
    }
    
    $stmt = $pdo->prepare("DELETE FROM books WHERE book_id = ?");
    if($stmt->execute([$book_id])) {
        $success = "Book deleted successfully!";
    } else {
        $error = "Failed to delete book.";
    }
}

$stmt = $pdo->query("SELECT * FROM books ORDER BY created_at DESC");
$books = $stmt->fetchAll();

// Get stats for the dashboard
$stmt = $pdo->query("SELECT COUNT(*) as total_books FROM books");
$total_books = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) as total_reviews FROM reviews");
$total_reviews = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) as pending_reviews FROM reviews WHERE status = 'pending'");
$pending_reviews = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) as digital_books FROM books WHERE has_digital = 1");
$digital_books = $stmt->fetchColumn();

// Get total value of library
$stmt = $pdo->query("SELECT SUM(price) as total_value FROM books");
$total_value = $stmt->fetchColumn();
$total_value = $total_value ?: 0;
?>

<?php include $projectRoot . '/includes/header.php'; ?>

<!-- Hero Section -->
<div class="hero-section" style="
    background: linear-gradient(135deg, rgba(79, 70, 229, 0.9), rgba(99, 102, 241, 0.9)),
                url('https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80');
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
                    Book Management
                </h1>
                
                <p style="
                    font-size: 1.2rem;
                    color: rgba(255, 255, 255, 0.9);
                    margin-bottom: 2rem;
                    line-height: 1.6;
                ">
                    Manage your book collection, add new titles with PDF files, and oversee the entire library system.
                </p>
                
                <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                    <a href="dashboard.php" class="btn btn-outline" style="
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
                    
                    <a href="manage_reviews.php" class="btn btn-primary" style="
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
                        <i class="fas fa-star"></i>
                        Manage Reviews
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
                        <i class="fas fa-chart-bar"></i>
                    </div>
                    <div>
                        <div style="font-size: 0.9rem; opacity: 0.8;">System Overview</div>
                        <div style="font-size: 1.1rem; font-weight: 700;">Library Stats</div>
                    </div>
                </div>
                
                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span>Total Books</span>
                        <span style="font-weight: 700; font-size: 1.2rem;"><?php echo $total_books; ?></span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span>Digital Books</span>
                        <span style="font-weight: 700; font-size: 1.2rem; color: #22c55e;"><?php echo $digital_books; ?></span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span>Total Reviews</span>
                        <span style="font-weight: 700; font-size: 1.2rem;"><?php echo $total_reviews; ?></span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span>Pending Reviews</span>
                        <span style="font-weight: 700; font-size: 1.2rem; color: #fbbf24;"><?php echo $pending_reviews; ?></span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid rgba(255, 255, 255, 0.2); padding-top: 1rem;">
                        <span>Library Value</span>
                        <span style="font-weight: 700; font-size: 1.2rem; color: #34d399;">$<?php echo number_format($total_value, 2); ?></span>
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
        
        <div class="management-grid" style="
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
        ">
            <!-- Add/Edit Book Form -->
            <div class="form-card" style="
                background: white;
                border-radius: 20px;
                padding: 2.5rem;
                box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
                border: 1px solid #e5e7eb;
                height: fit-content;
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
                        background: <?php echo isset($edit_book) ? 'linear-gradient(135deg, #f59e0b, #d97706)' : 'linear-gradient(135deg, #4f46e5, #7c3aed)'; ?>;
                        width: 60px;
                        height: 60px;
                        border-radius: 12px;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        color: white;
                        font-size: 1.5rem;
                    ">
                        <i class="fas <?php echo isset($edit_book) ? 'fa-edit' : 'fa-plus-circle'; ?>"></i>
                    </div>
                    <div>
                        <h2 style="
                            color: #1f2937;
                            font-size: 1.5rem;
                            font-weight: 700;
                            margin: 0 0 0.25rem 0;
                        "><?php echo isset($edit_book) ? 'Edit Book' : 'Add New Book'; ?></h2>
                        <p style="color: #6b7280; margin: 0;"><?php echo isset($edit_book) ? 'Update book details and PDF' : 'Add a new book to the library collection'; ?></p>
                    </div>
                    <?php if(isset($edit_book)): ?>
                        <a href="manage_books.php" style="
                            margin-left: auto;
                            color: #6b7280;
                            text-decoration: none;
                            display: flex;
                            align-items: center;
                            gap: 0.5rem;
                            padding: 0.5rem 1rem;
                            border-radius: 8px;
                            background: #f3f4f6;
                        ">
                            <i class="fas fa-times"></i> Cancel Edit
                        </a>
                    <?php endif; ?>
                </div>
                
                <form method="POST" enctype="multipart/form-data">
                    <?php if(isset($edit_book)): ?>
                        <input type="hidden" name="book_id" value="<?php echo $edit_book['book_id']; ?>">
                        <input type="hidden" name="existing_pdf" value="<?php echo htmlspecialchars((string) ($edit_book['pdf_file'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
                    <?php endif; ?>
                    
                    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                        <div class="form-group">
                            <label class="form-label" style="
                                display: block;
                                font-weight: 600;
                                color: #374151;
                                margin-bottom: 0.5rem;
                            ">Book Title</label>
                            <input type="text" name="title" class="form-input" required placeholder="Enter book title" 
                                   value="<?php echo isset($edit_book) ? htmlspecialchars($edit_book['title']) : ''; ?>"
                                   style="
                                width: 100%;
                                padding: 0.875rem 1rem;
                                border: 1px solid #d1d5db;
                                border-radius: 10px;
                                font-size: 1rem;
                                transition: all 0.3s ease;
                                background: #f8fafc;
                            ">
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label" style="
                                display: block;
                                font-weight: 600;
                                color: #374151;
                                margin-bottom: 0.5rem;
                            ">Author</label>
                            <input type="text" name="author" class="form-input" required placeholder="Enter author name"
                                   value="<?php echo isset($edit_book) ? htmlspecialchars($edit_book['author']) : ''; ?>"
                                   style="
                                width: 100%;
                                padding: 0.875rem 1rem;
                                border: 1px solid #d1d5db;
                                border-radius: 10px;
                                font-size: 1rem;
                                transition: all 0.3s ease;
                                background: #f8fafc;
                            ">
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label" style="
                                display: block;
                                font-weight: 600;
                                color: #374151;
                                margin-bottom: 0.5rem;
                            ">Description</label>
                            <textarea name="description" rows="4" class="form-input" required placeholder="Enter book description"
                                      style="
                                width: 100%;
                                padding: 0.875rem 1rem;
                                border: 1px solid #d1d5db;
                                border-radius: 10px;
                                font-size: 1rem;
                                transition: all 0.3s ease;
                                background: #f8fafc;
                                resize: vertical;
                                min-height: 120px;
                            "><?php echo isset($edit_book) ? htmlspecialchars($edit_book['description']) : ''; ?></textarea>
                        </div>
                        
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                            <div class="form-group">
                                <label class="form-label" style="
                                    display: block;
                                    font-weight: 600;
                                    color: #374151;
                                    margin-bottom: 0.5rem;
                                ">Publication Year</label>
                                <input type="number" name="year" class="form-input" min="1000" max="<?php echo (int) date('Y'); ?>" required placeholder="e.g., 2023"
                                       value="<?php echo isset($edit_book) ? htmlspecialchars($edit_book['year']) : ''; ?>"
                                       style="
                                    width: 100%;
                                    padding: 0.875rem 1rem;
                                    border: 1px solid #d1d5db;
                                    border-radius: 10px;
                                    font-size: 1rem;
                                    transition: all 0.3s ease;
                                    background: #f8fafc;
                                ">
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label" style="
                                    display: block;
                                    font-weight: 600;
                                    color: #374151;
                                    margin-bottom: 0.5rem;
                                ">Category</label>
                                <input type="text" name="category" class="form-input" required placeholder="e.g., Fiction, Science"
                                       value="<?php echo isset($edit_book) ? htmlspecialchars($edit_book['category']) : ''; ?>"
                                       style="
                                    width: 100%;
                                    padding: 0.875rem 1rem;
                                    border: 1px solid #d1d5db;
                                    border-radius: 10px;
                                    font-size: 1rem;
                                    transition: all 0.3s ease;
                                    background: #f8fafc;
                                ">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label" style="
                                display: block;
                                font-weight: 600;
                                color: #374151;
                                margin-bottom: 0.5rem;
                            ">Price ($)</label>
                            <div style="position: relative;">
                                <span style="
                                    position: absolute;
                                    left: 1rem;
                                    top: 50%;
                                    transform: translateY(-50%);
                                    color: #6b7280;
                                    font-weight: 600;
                                ">$</span>
                                <input type="number" name="price" class="form-input" step="0.01" min="0" max="1000" required placeholder="0.00"
                                       value="<?php echo isset($edit_book) ? htmlspecialchars($edit_book['price']) : ''; ?>"
                                       style="
                                    width: 100%;
                                    padding: 0.875rem 1rem 0.875rem 2.5rem;
                                    border: 1px solid #d1d5db;
                                    border-radius: 10px;
                                    font-size: 1rem;
                                    transition: all 0.3s ease;
                                    background: #f8fafc;
                                ">
                            </div>
                        </div>

                        <!-- Digital Book Option -->
                        <div class="form-group" style="
                            background: #f0fdf4;
                            padding: 1.5rem;
                            border-radius: 12px;
                            border: 2px solid #bbf7d0;
                        ">
                            <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
                                <input type="checkbox" name="has_digital" id="has_digital" value="1" 
                                       <?php echo (isset($edit_book) && $edit_book['has_digital']) ? 'checked' : ''; ?>
                                       style="width: 20px; height: 20px;">
                                <label for="has_digital" style="
                                    font-weight: 700;
                                    color: #166534;
                                    font-size: 1.1rem;
                                ">
                                    <i class="fas fa-tablet-alt" style="color: #22c55e;"></i>
                                    This book has a digital PDF version
                                </label>
                            </div>
                            
                            <div id="pdf_upload_section" style="display: <?php echo (isset($edit_book) && $edit_book['has_digital']) ? 'block' : 'none'; ?>;">
                                <label class="form-label" style="
                                    display: block;
                                    font-weight: 600;
                                    color: #374151;
                                    margin-bottom: 0.5rem;
                                ">Upload PDF File (Optional)</label>
                                <input type="file" name="pdf_file" accept=".pdf" style="
                                    width: 100%;
                                    padding: 0.875rem;
                                    border: 2px dashed #22c55e;
                                    border-radius: 10px;
                                    background: white;
                                ">
                                <?php if(isset($edit_book) && $edit_book['pdf_file']): ?>
                                    <div style="
                                        margin-top: 1rem;
                                        padding: 0.75rem;
                                        background: #f0fdf4;
                                        border-radius: 8px;
                                        display: flex;
                                        align-items: center;
                                        justify-content: space-between;
                                    ">
                                        <div>
                                            <i class="fas fa-file-pdf" style="color: #dc2626;"></i>
                                            Current PDF: <?php echo basename($edit_book['pdf_file']); ?>
                                        </div>
                                        <a href="<?php echo htmlspecialchars('../../' . $edit_book['pdf_file'], ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener" style="
                                            color: #22c55e;
                                            text-decoration: none;
                                            font-weight: 600;
                                        ">View</a>
                                    </div>
                                <?php endif; ?>
                                <small style="color: #6b7280; display: block; margin-top: 0.5rem;">
                                    <i class="fas fa-info-circle"></i>
                                    Leave empty to keep existing PDF (when editing) or to add without PDF (when adding)
                                </small>
                            </div>
                        </div>
                        
                        <button type="submit" name="<?php echo isset($edit_book) ? 'edit_book' : 'add_book'; ?>" class="btn btn-primary" style="
                            background: <?php echo isset($edit_book) ? 'linear-gradient(135deg, #f59e0b, #d97706)' : 'linear-gradient(135deg, #4f46e5, #7c3aed)'; ?>;
                            color: white;
                            padding: 1rem 2rem;
                            border-radius: 12px;
                            border: none;
                            font-weight: 700;
                            font-size: 1rem;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            gap: 0.75rem;
                            transition: all 0.3s ease;
                            width: 100%;
                            cursor: pointer;
                        ">
                            <i class="fas <?php echo isset($edit_book) ? 'fa-save' : 'fa-plus'; ?>"></i>
                            <?php echo isset($edit_book) ? 'Update Book' : 'Add Book to Library'; ?>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Books List -->
            <div class="books-list-card" style="
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
                        background: linear-gradient(135deg, #f59e0b, #d97706);
                        width: 60px;
                        height: 60px;
                        border-radius: 12px;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        color: white;
                        font-size: 1.5rem;
                    ">
                        <i class="fas fa-list"></i>
                    </div>
                    <div>
                        <h2 style="
                            color: #1f2937;
                            font-size: 1.5rem;
                            font-weight: 700;
                            margin: 0 0 0.25rem 0;
                        ">All Books</h2>
                        <p style="color: #6b7280; margin: 0;">Manage existing books in the library</p>
                    </div>
                    <div style="
                        background: #4f46e5;
                        color: white;
                        padding: 0.5rem 1rem;
                        border-radius: 20px;
                        font-weight: 700;
                        margin-left: auto;
                    ">
                        <?php echo count($books); ?> Books
                    </div>
                </div>
                
                <div style="max-height: 600px; overflow-y: auto; padding-right: 0.5rem;">
                    <div style="display: flex; flex-direction: column; gap: 1rem;">
                        <?php if(count($books) > 0): ?>
                            <?php foreach($books as $book): ?>
                                <div class="book-item" style="
                                    background: #f8fafc;
                                    padding: 1.5rem;
                                    border-radius: 12px;
                                    border: 1px solid #e5e7eb;
                                    transition: all 0.3s ease;
                                    <?php echo $book['has_digital'] ? 'border-left: 6px solid #22c55e;' : ''; ?>
                                ">
                                    <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                                        <div style="flex: 1;">
                                            <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                                                <h4 style="
                                                    color: #1f2937;
                                                    font-size: 1.1rem;
                                                    font-weight: 700;
                                                    margin: 0 0 0.5rem 0;
                                                "><?php echo htmlspecialchars($book['title']); ?></h4>
                                                <?php if($book['has_digital']): ?>
                                                    <span style="
                                                        background: #22c55e;
                                                        color: white;
                                                        padding: 0.2rem 0.6rem;
                                                        border-radius: 20px;
                                                        font-size: 0.7rem;
                                                        font-weight: 600;
                                                        display: inline-flex;
                                                        align-items: center;
                                                        gap: 0.2rem;
                                                    ">
                                                        <i class="fas fa-tablet-alt"></i> PDF
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                            <p style="
                                                color: #6b7280;
                                                margin: 0 0 0.75rem 0;
                                                display: flex;
                                                align-items: center;
                                                gap: 0.5rem;
                                            ">
                                                <i class="fas fa-user-pen" style="color: #6b7280;"></i>
                                                <?php echo htmlspecialchars($book['author']); ?>
                                            </p>
                                            <div style="display: flex; gap: 1rem; align-items: center; flex-wrap: wrap;">
                                                <span style="
                                                    background: linear-gradient(135deg, #4f46e5, #7c3aed);
                                                    color: white;
                                                    padding: 0.4rem 1rem;
                                                    border-radius: 20px;
                                                    font-size: 0.8rem;
                                                    font-weight: 600;
                                                ">
                                                    <?php echo htmlspecialchars($book['category']); ?>
                                                </span>
                                                <span style="
                                                    color: #6b7280;
                                                    font-size: 0.875rem;
                                                    font-weight: 600;
                                                ">
                                                    <i class="fas fa-calendar"></i>
                                                    <?php echo htmlspecialchars($book['year']); ?>
                                                </span>
                                                <span style="
                                                    background: linear-gradient(135deg, #10b981, #34d399);
                                                    color: white;
                                                    padding: 0.4rem 1rem;
                                                    border-radius: 20px;
                                                    font-size: 0.8rem;
                                                    font-weight: 600;
                                                ">
                                                    <i class="fas fa-dollar-sign"></i>
                                                    <?php echo number_format($book['price'], 2); ?>
                                                </span>
                                            </div>
                                        </div>
                                        <div style="display: flex; gap: 0.5rem;">
                                            <a href="?edit=<?php echo $book['book_id']; ?>" class="btn btn-secondary" style="
                                                background: #f1f5f9;
                                                color: #475569;
                                                padding: 0.6rem;
                                                border-radius: 8px;
                                                text-decoration: none;
                                                display: flex;
                                                align-items: center;
                                                justify-content: center;
                                                transition: all 0.3s ease;
                                                border: 1px solid #e2e8f0;
                                            " title="Edit Book">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="?delete=<?php echo $book['book_id']; ?>" 
                                               onclick="return confirm('Are you sure you want to delete \"<?php echo addslashes($book['title']); ?>\"? This action cannot be undone.')"
                                               class="btn" style="
                                                background: #fee2e2;
                                                color: #dc2626;
                                                padding: 0.6rem;
                                                border-radius: 8px;
                                                text-decoration: none;
                                                display: flex;
                                                align-items: center;
                                                justify-content: center;
                                                transition: all 0.3s ease;
                                                border: 1px solid #fecaca;
                                            " title="Delete Book">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </div>
                                    
                                    <?php if(!empty($book['description'])): ?>
                                        <div style="
                                            margin-top: 1rem;
                                            padding-top: 1rem;
                                            border-top: 1px solid #e5e7eb;
                                        ">
                                            <p style="
                                                color: #6b7280;
                                                font-size: 0.9rem;
                                                line-height: 1.5;
                                                margin: 0;
                                                display: -webkit-box;
                                                -webkit-line-clamp: 2;
                                                -webkit-box-orient: vertical;
                                                overflow: hidden;
                                            "><?php echo htmlspecialchars($book['description']); ?></p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div style="
                                text-align: center;
                                padding: 3rem 2rem;
                                color: #6b7280;
                            ">
                                <div style="
                                    width: 80px;
                                    height: 80px;
                                    background: #f1f5f9;
                                    border-radius: 50%;
                                    display: flex;
                                    align-items: center;
                                    justify-content: center;
                                    margin: 0 auto 1.5rem;
                                    color: #94a3b8;
                                    font-size: 2rem;
                                ">
                                    <i class="fas fa-book-open"></i>
                                </div>
                                <h3 style="color: #475569; margin-bottom: 0.5rem;">No Books Yet</h3>
                                <p style="margin: 0;">Start by adding the first book to your library collection.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle PDF upload section
        const hasDigitalCheckbox = document.getElementById('has_digital');
        const pdfUploadSection = document.getElementById('pdf_upload_section');
        
        if(hasDigitalCheckbox && pdfUploadSection) {
            hasDigitalCheckbox.addEventListener('change', function() {
                pdfUploadSection.style.display = this.checked ? 'block' : 'none';
            });
        }

        // Add hover effects to book items
        const bookItems = document.querySelectorAll('.book-item');
        bookItems.forEach(item => {
            item.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-3px)';
                this.style.boxShadow = '0 6px 15px rgba(0, 0, 0, 0.08)';
            });
            
            item.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
                this.style.boxShadow = 'none';
            });
        });

        // Add focus effects to form inputs
        const formInputs = document.querySelectorAll('.form-input');
        formInputs.forEach(input => {
            input.addEventListener('focus', function() {
                this.style.outline = 'none';
                this.style.borderColor = '#4f46e5';
                this.style.background = 'white';
                this.style.boxShadow = '0 0 0 3px rgba(79, 70, 229, 0.1)';
            });
            
            input.addEventListener('blur', function() {
                this.style.boxShadow = 'none';
                this.style.background = '#f8fafc';
            });
        });

        // Price input validation
        const priceInput = document.querySelector('input[name="price"]');
        if (priceInput) {
            priceInput.addEventListener('input', function() {
                if (this.value < 0) {
                    this.value = 0;
                }
                if (this.value > 1000) {
                    this.value = 1000;
                }
                this.value = parseFloat(this.value).toFixed(2);
            });
        }
    });
</script>

<style>
    /* Hover Effects */
    .form-input:focus {
        outline: none;
        border-color: #4f46e5 !important;
        background: white !important;
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
    }

    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, #4338ca, #6d28d9) !important;
    }

    .book-item:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.08);
        border-color: #d1d5db;
    }

    .btn-secondary:hover {
        background: #e2e8f0 !important;
    }

    .btn[style*="background: #fee2e2"]:hover {
        background: #fecaca !important;
    }

    /* Scrollbar Styling */
    .books-list-card > div::-webkit-scrollbar {
        width: 6px;
    }

    .books-list-card > div::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 10px;
    }

    .books-list-card > div::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 10px;
    }

    .books-list-card > div::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }

    /* Responsive Design */
    @media (max-width: 1024px) {
        .hero-content {
            grid-template-columns: 1fr;
            gap: 2rem;
            text-align: center;
        }
        
        .management-grid {
            grid-template-columns: 1fr;
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
        
        .form-card,
        .books-list-card {
            padding: 2rem !important;
        }
        
        .management-grid > div:first-child > div {
            flex-direction: column;
            text-align: center;
            gap: 1rem;
        }
        
        .management-grid > div:last-child > div {
            flex-direction: column;
            text-align: center;
            gap: 1rem;
        }
        
        .management-grid > div:last-child > div > div:last-child {
            margin-left: 0 !important;
        }
    }

    @media (max-width: 480px) {
        .hero-section {
            padding: 2rem 1rem;
        }
        
        .main-content-wide {
            padding: 1.5rem 1rem;
        }
        
        .form-card,
        .books-list-card {
            padding: 1.5rem !important;
        }
        
        .book-item > div {
            flex-direction: column;
            gap: 1rem;
        }
        
        .book-item > div > div:last-child {
            align-self: flex-end;
        }
    }
</style>

<?php include $projectRoot . '/includes/footer.php'; ?>