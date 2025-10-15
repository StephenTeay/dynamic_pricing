<?php
$pageTitle = APP_NAME . ' - Settings';
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/seller_nav.php';
?>

<div class="container mt-4">
    <h1>Seller Settings</h1>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Business Profile</h5>
                </div>
                <div class="card-body">
                    <form action="<?php echo url('seller/settings/update'); ?>" method="POST">
                        <div class="mb-3">
                            <label for="business_name" class="form-label">Business Name</label>
                            <input type="text" class="form-control" id="business_name" name="business_name" 
                                   value="<?php echo htmlspecialchars($profile['business_name']); ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="business_email" class="form-label">Business Email</label>
                            <input type="email" class="form-control" id="business_email" name="business_email" 
                                   value="<?php echo htmlspecialchars($profile['business_email']); ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="business_phone" class="form-label">Business Phone</label>
                            <input type="tel" class="form-control" id="business_phone" name="business_phone" 
                                   value="<?php echo htmlspecialchars($profile['business_phone']); ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label for="business_address" class="form-label">Business Address</label>
                            <textarea class="form-control" id="business_address" name="business_address" rows="3"
                                    ><?php echo htmlspecialchars($profile['business_address']); ?></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="business_description" class="form-label">Business Description</label>
                            <textarea class="form-control" id="business_description" name="business_description" rows="3"
                                    ><?php echo htmlspecialchars($profile['business_description']); ?></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Account Settings</h5>
                </div>
                <div class="card-body">
                    <a href="<?php echo url('auth/change-password'); ?>" class="btn btn-secondary d-block mb-3">
                        Change Password
                    </a>
                    <a href="<?php echo url('auth/notifications'); ?>" class="btn btn-secondary d-block">
                        Notification Settings
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>