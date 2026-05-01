<?php include 'includes/header.php'; ?>

<div class="card p-4">
    <div style="text-align: center; margin-bottom: 3rem;">
        <h1 style="color: #1f2937; font-size: 2.5rem; margin-bottom: 1rem;">Contact Us</h1>
        <p style="color: #6b7280; font-size: 1.125rem; max-width: 600px; margin: 0 auto;">
            Have questions or feedback? We'd love to hear from you. Get in touch with our team.
        </p>
    </div>

    <?php
    if($_SERVER['REQUEST_METHOD'] == 'POST') {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $message = $_POST['message'];
        
        // In a real system, you would send an email here
        $success = "Thank you for your message! We'll get back to you soon.";
    }
    ?>
    
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 3rem;">
        <div>
            <?php if(isset($success)): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <?php echo $success; ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-user"></i>
                        Full Name
                    </label>
                    <input type="text" name="name" class="form-input" required placeholder="Enter your full name">
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-envelope"></i>
                        Email Address
                    </label>
                    <input type="email" name="email" class="form-input" required placeholder="Enter your email">
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-comment"></i>
                        Message
                    </label>
                    <textarea name="message" rows="6" class="form-input" required placeholder="Tell us what's on your mind..."></textarea>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center;">
                    <i class="fas fa-paper-plane"></i>
                    Send Message
                </button>
            </form>
        </div>

        <div>
            <div class="card p-4" style="background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); color: white;">
                <h2 style="margin-bottom: 1.5rem;">Get in Touch</h2>
                
                <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                    <div style="display: flex; align-items: center; gap: 1rem;">
                        <div style="background: rgba(255,255,255,0.2); width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div>
                            <div style="font-weight: 600;">Email Us</div>
                            <div style="opacity: 0.9;">support@bookreview.com</div>
                        </div>
                    </div>
                    
                    <div style="display: flex; align-items: center; gap: 1rem;">
                        <div style="background: rgba(255,255,255,0.2); width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-phone"></i>
                        </div>
                        <div>
                            <div style="font-weight: 600;">Call Us</div>
                            <div style="opacity: 0.9;">+1 (555) 123-4567</div>
                        </div>
                    </div>
                    
                    <div style="display: flex; align-items: center; gap: 1rem;">
                        <div style="background: rgba(255,255,255,0.2); width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div>
                            <div style="font-weight: 600;">Visit Us</div>
                            <div style="opacity: 0.9;">123 Book Street, Library City</div>
                        </div>
                    </div>
                </div>

                <div style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid rgba(255,255,255,0.2);">
                    <h3 style="margin-bottom: 1rem;">Office Hours</h3>
                    <div style="display: flex; flex-direction: column; gap: 0.5rem; opacity: 0.9;">
                        <div style="display: flex; justify-content: space-between;">
                            <span>Monday - Friday</span>
                            <span>9:00 AM - 6:00 PM</span>
                        </div>
                        <div style="display: flex; justify-content: space-between;">
                            <span>Saturday</span>
                            <span>10:00 AM - 4:00 PM</span>
                        </div>
                        <div style="display: flex; justify-content: space-between;">
                            <span>Sunday</span>
                            <span>Closed</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>