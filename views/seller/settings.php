<?php
$pageTitle = APP_NAME . ' - Settings';
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/seller_nav.php';
?>

<div class="container mt-4">
    <h1>Seller Settings</h1>

    <?php if ($message = Session::getFlash('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($message); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if ($message = Session::getFlash('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($message); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

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
                            <input type="text" class="form-control <?php echo Session::hasFlash('errors')['business_name'] ? 'is-invalid' : ''; ?>" 
                                   id="business_name" name="business_name" 
                                   value="<?php echo htmlspecialchars(Session::getFlash('old')['business_name'] ?? $profile['business_name'] ?? ''); ?>" required>
                            <?php if ($error = Session::getFlash('errors')['business_name'] ?? null): ?>
                                <div class="invalid-feedback"><?php echo htmlspecialchars($error); ?></div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="mb-3">
                            <label for="business_email" class="form-label">Business Email</label>
                            <input type="email" class="form-control <?php echo Session::hasFlash('errors')['business_email'] ? 'is-invalid' : ''; ?>" 
                                   id="business_email" name="business_email" 
                                   value="<?php echo htmlspecialchars(Session::getFlash('old')['business_email'] ?? $profile['business_email'] ?? ''); ?>" required>
                            <?php if ($error = Session::getFlash('errors')['business_email'] ?? null): ?>
                                <div class="invalid-feedback"><?php echo htmlspecialchars($error); ?></div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="mb-3">
                            <label for="business_phone" class="form-label">Business Phone</label>
                            <input type="tel" class="form-control <?php echo Session::hasFlash('errors')['business_phone'] ? 'is-invalid' : ''; ?>" 
                                   id="business_phone" name="business_phone" 
                                   value="<?php echo htmlspecialchars(Session::getFlash('old')['business_phone'] ?? $profile['business_phone'] ?? ''); ?>">
                            <?php if ($error = Session::getFlash('errors')['business_phone'] ?? null): ?>
                                <div class="invalid-feedback"><?php echo htmlspecialchars($error); ?></div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="mb-3">
                            <label for="business_address" class="form-label">Business Address</label>
                            <textarea class="form-control <?php echo Session::hasFlash('errors')['business_address'] ? 'is-invalid' : ''; ?>" 
                                    id="business_address" name="business_address" rows="3"
                                    ><?php echo htmlspecialchars(Session::getFlash('old')['business_address'] ?? $profile['business_address'] ?? ''); ?></textarea>
                            <?php if ($error = Session::getFlash('errors')['business_address'] ?? null): ?>
                                <div class="invalid-feedback"><?php echo htmlspecialchars($error); ?></div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="mb-3">
                            <label for="business_description" class="form-label">Business Description</label>
                            <textarea class="form-control <?php echo Session::hasFlash('errors')['business_description'] ? 'is-invalid' : ''; ?>" 
                                    id="business_description" name="business_description" rows="3"
                                    ><?php echo htmlspecialchars(Session::getFlash('old')['business_description'] ?? $profile['business_description'] ?? ''); ?></textarea>
                            <?php if ($error = Session::getFlash('errors')['business_description'] ?? null): ?>
                                <div class="invalid-feedback"><?php echo htmlspecialchars($error); ?></div>
                            <?php endif; ?>
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