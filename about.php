<?php include 'includes/header.php'; ?>

<div class="card p-4">
    <div style="text-align: center; margin-bottom: 3rem;">
        <h1 style="color: #1f2937; font-size: 2.5rem; margin-bottom: 1rem;">About BookReview System</h1>
        <p style="color: #6b7280; font-size: 1.125rem; max-width: 600px; margin: 0 auto;">
            A comprehensive book review platform where readers can discover, share, and discuss their favorite books.
        </p>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem; margin-bottom: 3rem;">
        <div class="card p-4" style="text-align: center;">
            <div style="background: #4f46e5; color: white; width: 80px; height: 80px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;">
                <i class="fas fa-book-open" style="font-size: 2rem;"></i>
            </div>
            <h3 style="color: #1f2937; margin-bottom: 1rem;">Discover Books</h3>
            <p style="color: #6b7280;">Explore thousands of books across various genres and categories.</p>
        </div>
        
        <div class="card p-4" style="text-align: center;">
            <div style="background: #10b981; color: white; width: 80px; height: 80px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;">
                <i class="fas fa-star" style="font-size: 2rem;"></i>
            </div>
            <h3 style="color: #1f2937; margin-bottom: 1rem;">Share Reviews</h3>
            <p style="color: #6b7280;">Write and share your thoughts with our community of readers.</p>
        </div>
        
        <div class="card p-4" style="text-align: center;">
            <div style="background: #f59e0b; color: white; width: 80px; height: 80px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;">
                <i class="fas fa-users" style="font-size: 2rem;"></i>
            </div>
            <h3 style="color: #1f2937; margin-bottom: 1rem;">Join Community</h3>
            <p style="color: #6b7280;">Connect with fellow book lovers and discover new perspectives.</p>
        </div>
    </div>

    <div class="card p-4" style="background: #f8fafc;">
        <h2 style="color: #1f2937; margin-bottom: 1.5rem;">Key Features</h2>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem;">
            <div style="display: flex; align-items: center; gap: 1rem; padding: 1rem; background: white; border-radius: 8px;">
                <i class="fas fa-search" style="color: #4f46e5;"></i>
                <span>Browse and search books</span>
            </div>
            <div style="display: flex; align-items: center; gap: 1rem; padding: 1rem; background: white; border-radius: 8px;">
                <i class="fas fa-pen-fancy" style="color: #4f46e5;"></i>
                <span>Read and write reviews</span>
            </div>
            <div style="display: flex; align-items: center; gap: 1rem; padding: 1rem; background: white; border-radius: 8px;">
                <i class="fas fa-user-plus" style="color: #4f46e5;"></i>
                <span>User registration and profiles</span>
            </div>
            <div style="display: flex; align-items: center; gap: 1rem; padding: 1rem; background: white; border-radius: 8px;">
                <i class="fas fa-crown" style="color: #4f46e5;"></i>
                <span>Admin management system</span>
            </div>
            <div style="display: flex; align-items: center; gap: 1rem; padding: 1rem; background: white; border-radius: 8px;">
                <i class="fas fa-star-half-alt" style="color: #4f46e5;"></i>
                <span>Rating system (1-5 stars)</span>
            </div>
            <div style="display: flex; align-items: center; gap: 1rem; padding: 1rem; background: white; border-radius: 8px;">
                <i class="fas fa-mobile-alt" style="color: #4f46e5;"></i>
                <span>Mobile-responsive design</span>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>