<?php
// controllers/SellerController.php

require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/../models/SellerProfile.php';
require_once __DIR__ . '/../models/Inventory.php';
require_once __DIR__ . '/../core/Session.php';

class SellerController {
    private $productModel;
    private $orderModel;
    private $sellerProfileModel;
    private $inventoryModel;
    
    public function __construct() {
        // Check seller authentication for all routes except login
        if (!$this->isLoginRoute() && (!Session::isLoggedIn() || Session::getUserType() !== 'seller')) {
            Session::setFlash('error', 'Please login as a seller to access this area');
            redirect('/login?type=seller');
            exit;
        }
        
        $this->productModel = new Product();
        $this->orderModel = new Order();
        $this->sellerProfileModel = new SellerProfile();
        $this->inventoryModel = new Inventory();
    }
    
    private function isLoginRoute() {
        return in_array($_SERVER['REQUEST_URI'], ['/login', '/auth/login', '/register', '/auth/register']);
    }
    
    /**
     * Get seller profile
     */
    public function getProfile() {
        if (!$this->sellerProfileModel) {
            $this->sellerProfileModel = new SellerProfile();
        }
        $profile = $this->sellerProfileModel->getByUserId(Session::getUserId());
        return $profile ?: [
            'business_name' => '',
            'business_email' => '',
            'business_phone' => '',
            'business_address' => '',
            'business_description' => ''
        ];
    }
    
    /**
     * Show seller dashboard
     */
    public function dashboard() {
        if (!Session::isLoggedIn() || Session::getUserType() !== 'seller') {
            Session::setFlash('error', 'Please login as a seller to access the dashboard');
            redirect('/login');
            exit;
        }
        
        require_once __DIR__ . '/../views/seller/dashboard.php';
    }

    /**
     * Update seller profile
     */
    public function updateProfile($data) {
        $profile = $this->getProfile();
        
        if (!$profile) {
            return false;
        }
        
        return $this->sellerProfileModel->update($profile['seller_id'], $data);
    }
    
    /**
     * Get dashboard stats
     */
    public function getDashboardStats() {
        $sellerId = $this->getSellerId();
        
        return [
            'total_products' => $this->productModel->count(['seller_id' => $sellerId, 'is_active' => 1]),
            'low_stock_count' => count($this->productModel->getLowStockProducts($sellerId)),
            'revenue_stats' => $this->orderModel->getRevenueStats($sellerId),
            'order_stats' => $this->orderModel->getOrderStatsByStatus($sellerId)
        ];
    }
    
    /**
     * Get seller's products
     */
    public function getProducts() {
        return $this->productModel->getProductsWithInventory($this->getSellerId());
    }
    
    /**
     * Get seller's orders
     */
    public function getOrders() {
        return $this->orderModel->getOrdersBySeller($this->getSellerId());
    }
    
    /**
     * Get low stock products
     */
    public function getLowStockProducts() {
        return $this->productModel->getLowStockProducts($this->getSellerId());
    }
    
    /**
     * Get seller ID
     */
    /**
     * Store a new product
     */
    public function updateProduct($params) {
        error_log("Update Product - START");
        error_log("Raw params: " . print_r($params, true));
        
        // If $params is a string (the ID directly), convert it to array format
        if (!is_array($params)) {
            $params = ['id' => $params];
        }
        
        $productId = $params['id'] ?? null;
        if (!$productId) {
            Session::setFlash('error', 'Product ID is required');
            redirect('/seller/products');
            exit;
        }

        // Verify product exists and belongs to seller
        $existingProduct = $this->productModel->findWithInventory($productId);
        if (!$existingProduct || $existingProduct['seller_id'] != $this->getSellerId()) {
            Session::setFlash('error', 'Product not found');
            redirect('/seller/products');
            exit;
        }

        // Collect form data
        $data = [
            'product_name' => $_POST['product_name'] ?? '',
            'sku' => $_POST['sku'] ?? '',
            'product_description' => $_POST['product_description'] ?? '',
            'current_price' => (float)($_POST['price'] ?? 0),
            'base_cost' => (float)($_POST['cost'] ?? 0),
            'is_active' => isset($_POST['is_active']) ? (int)$_POST['is_active'] : 1,
            'seller_id' => $this->getSellerId(),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        // Handle image upload if provided
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../public/assets/images/products/';
            $imageUrl = $this->handleImageUpload($_FILES['image'], $uploadDir);
            if ($imageUrl) {
                $data['image_url'] = $imageUrl;
            }
        }

        try {
            $this->productModel->beginTransaction();

            // Update the product
            $success = $this->productModel->update($productId, $data);
            
            // Update inventory if stock quantity is provided
            if ($success && isset($_POST['min_stock_quantity'])) {
                $this->inventoryModel->update(['product_id' => $productId], [
                    'low_stock_threshold' => (int)$_POST['min_stock_quantity']
                ]);
            }

            $this->productModel->commit();
            Session::setFlash('success', 'Product updated successfully');
            redirect('/seller/products');

        } catch (Exception $e) {
            $this->productModel->rollback();
            error_log("Error updating product: " . $e->getMessage());
            Session::setFlash('error', 'Failed to update product. Please try again.');
            redirect('/seller/product/edit/' . $productId);
        }
    }

    public function storeProduct() {
        // Debug logging
        error_log(sprintf("[%s] Starting storeProduct method. Request Method: %s", 
            date('Y-m-d H:i:s'),
            $_SERVER['REQUEST_METHOD']
        ));

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            error_log("Invalid request method: " . $_SERVER['REQUEST_METHOD']);
            redirect('/seller/products');
            exit;
        }

        $data = Validator::sanitize($_POST);
        error_log("Sanitized POST data: " . print_r($data, true));
        
        $validator = new Validator($data);
        $rules = [
            'product_name' => 'required|min:3|max:255',
            'sku' => 'required|min:3|max:50',
            'product_description' => 'required|min:10',
            'price' => 'required|numeric|min:0',
            'cost' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'min_stock_quantity' => 'required|integer|min:0',
            'is_active' => 'required|in:0,1'
        ];

        if (!$validator->validate($rules)) {
            $error = $validator->getFirstError();
            error_log("Validation failed: " . $error);
            Session::setFlash('error', $error);
            Session::setFlash('old', $data);
            redirect('/seller/product/create');
            exit;
        }

        // Check if SKU is unique
        if (!$this->productModel->isSkuUnique($data['sku'])) {
            error_log("Duplicate SKU detected: " . $data['sku']);
            Session::setFlash('error', 'This SKU is already in use. Please choose a unique SKU.');
            Session::setFlash('old', $data);
            redirect('/seller/product/create');
            exit;
        }

        error_log("Validation passed successfully");

        // Handle image upload if present
        $imageUrl = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../public/assets/images/products/';
            $imageUrl = $this->handleImageUpload($_FILES['image'], $uploadDir);
            error_log("Image upload attempted. Result: " . var_export($imageUrl, true) . ", uploadDir: " . $uploadDir);
            if (!$imageUrl) {
                error_log("Image upload failed for file: " . print_r($_FILES['image'], true));
                Session::setFlash('error', 'Failed to upload image');
                Session::setFlash('old', $data);
                redirect('/seller/product/create');
                exit;
            }
        }

        // Add seller_id and image_url to data (map form keys to DB column names)
        $data['seller_id'] = $this->getSellerId();
        if ($imageUrl) {
            $data['image_url'] = $imageUrl;
        }
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['price_updated_at'] = date('Y-m-d H:i:s');

        // The products table uses different column names than the form input names.
            // Map the incoming $data keys to the actual DB columns expected by Product->create().
        $insertData = [
            'product_name' => $data['product_name'] ?? null,
            'sku' => $data['sku'] ?? null,
            'product_description' => $data['product_description'] ?? '',
            'current_price' => $data['price'] ?? 0,
            'base_cost' => $data['cost'] ?? 0,
            'category' => 'general', // Required field, default to 'general' for now
            'cost_currency' => 'NGN', // Default from schema
            'price_currency' => 'NGN', // Default from schema
            'is_active' => isset($data['is_active']) ? (int)$data['is_active'] : 1,
            'seller_id' => $data['seller_id'],
            'image_url' => '/assets/images/products/' . ($data['image_url'] ?? null),
            'last_price_update' => $data['price_updated_at'] ?? date('Y-m-d H:i:s'),
            'created_at' => $data['created_at'],
            'updated_at' => date('Y-m-d H:i:s')
        ];

        try {
            $this->productModel->beginTransaction();

            // Create the product
            try {
                error_log("Insert data for product create: " . print_r($insertData, true));
                $productId = $this->productModel->create($insertData);
                error_log("Product create returned: " . var_export($productId, true));

                // Create inventory record for the product
                if ($productId) {
                    $inventoryData = [
                        'product_id' => $productId,
                        'quantity_available' => $data['stock_quantity'] ?? 0,
                        'quantity_reserved' => 0,
                        'reorder_point' => 10, // Default from schema
                        'low_stock_threshold' => $data['min_stock_quantity'] ?? 20, // Use form input or schema default
                        'high_stock_threshold' => 100, // Default from schema
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ];
                    
                    error_log("Creating inventory record: " . print_r($inventoryData, true));
                    $inventoryModel = new Inventory();  // No namespace needed
                    $this->productModel->shareConnection($inventoryModel);
                    
                    $inventoryId = $inventoryModel->create($inventoryData);
                    error_log("Inventory create returned: " . var_export($inventoryId, true));
                    
                    if (!$inventoryId) {
                        throw new Exception('Failed to create inventory record');
                    }
                }
            } catch (PDOException $pdoEx) {
                error_log("PDOException during product/inventory create: " . $pdoEx->getMessage());
                $this->productModel->rollback();
                Session::setFlash('error', 'Database error while creating product');
                Session::setFlash('old', $data);
                redirect('/seller/product/create');
                exit;
            }

            if (!$productId) {
                throw new Exception('Failed to create product');
            }

            $this->productModel->commit();
            
            Session::setFlash('success', 'Product created successfully');
            redirect('/seller/products');
            exit;
            
        } catch (Exception $e) {
            $this->productModel->rollback();
            Session::setFlash('error', 'Failed to create product: ' . $e->getMessage());
            Session::setFlash('old', $data);
            redirect('/seller/product/create');
            exit;
        }
    }

    /**
     * Handle image upload
     */
    private function handleImageUpload($file, $uploadDir) {
        error_log('Starting image upload...');
        error_log('File info: ' . print_r($file, true));
        error_log('Upload directory: ' . $uploadDir);
        
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        
        if (!in_array($file['type'], $allowedTypes)) {
            error_log('Invalid file type: ' . $file['type']);
            return false;
        }

        if (!file_exists($uploadDir)) {
            error_log('Creating upload directory: ' . $uploadDir);
            if (!mkdir($uploadDir, 0777, true)) {
                error_log('Failed to create upload directory');
                return false;
            }
        }

        if (!is_writable($uploadDir)) {
            error_log('Upload directory is not writable: ' . $uploadDir);
            chmod($uploadDir, 0777);
            if (!is_writable($uploadDir)) {
                error_log('Failed to make directory writable');
                return false;
            }
        }

        $fileName = uniqid() . '_' . basename($file['name']);
        $targetPath = $uploadDir . $fileName;
        error_log('Target path for upload: ' . $targetPath);

        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            error_log('File uploaded successfully to: ' . $targetPath);
            return $fileName;
        }

        error_log('Failed to move uploaded file. PHP error: ' . error_get_last()['message']);
        return false;
    }

    /**
     * Get seller ID
     */
    private function getSellerId() {
        $profile = $this->getProfile();
        return $profile['seller_id'] ?? null;
    }

    /**
     * Show products page
     */
    public function products() {
        $sellerId = $this->getSellerId();
        if (!$sellerId) {
            Session::setFlash('error', 'Seller profile not found');
            redirect('/seller/dashboard');
            exit;
        }
        
        // Get products with inventory
        $products = $this->productModel->getProductsWithInventory($sellerId);
        require_once __DIR__ . '/../views/seller/products.php';
    }

    /**
     * Show create product form
     */
    public function createProductForm() {
        $pageTitle = APP_NAME . ' - Add New Product';
        require_once __DIR__ . '/../views/seller/product_form.php';
    }

    /**
     * Show edit product form
     */
    public function editProductForm($params) {
        error_log("Edit Product Form - START");
        error_log("Request URI: " . $_SERVER['REQUEST_URI']);
        error_log("Raw params: " . print_r($params, true));
        
        // If $params is a string (the ID directly), convert it to array format
        if (!is_array($params)) {
            $params = ['id' => $params];
        }
        error_log("Processed params: " . print_r($params, true));
        
        $productId = $params['id'] ?? null;
        error_log("Product ID extracted: " . var_export($productId, true));
        
        if (!$productId) {
            error_log("Edit Product Form - No product ID found in params");
            Session::setFlash('error', 'Product ID is required');
            redirect('/seller/products');
            exit;
        }

        $product = $this->productModel->findWithInventory($productId);
        if (!$product || $product['seller_id'] != $this->getSellerId()) {
            Session::setFlash('error', 'Product not found');
            redirect('/seller/products');
            exit;
        }

        $pageTitle = APP_NAME . ' - Edit Product';
        $isEdit = true; // Flag to indicate this is an edit form
        require_once __DIR__ . '/../views/seller/product_form.php';
    }

    /**
     * Show orders page
     */
    public function orders() {
        $orders = $this->getOrders();
        require_once __DIR__ . '/../views/seller/orders.php';
    }

    /**
     * Show analytics page
     */
        public function analytics() {
            $stats = $this->getDashboardStats();
            // Ensure keys exist to avoid PHP warnings
            if (!isset($stats['revenue_stats']['today'])) {
                $stats['revenue_stats']['today'] = 0;
            }
            if (!isset($stats['revenue_stats']['month'])) {
                $stats['revenue_stats']['month'] = 0;
            }
            require_once __DIR__ . '/../views/seller/analytics.php';
        }
    /**
     * AJAX: Update inventory stock
     */
    public function updateInventory() {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'error' => 'Invalid request method']);
            exit;
        }
        $input = json_decode(file_get_contents('php://input'), true);
        $productId = $input['product_id'] ?? null;
        $stock = $input['stock_quantity'] ?? null;
        if (!$productId || !is_numeric($stock)) {
            echo json_encode(['success' => false, 'error' => 'Missing or invalid parameters']);
            exit;
        }
        $inventory = $this->inventoryModel->getByProductId($productId);
        if (!$inventory) {
            echo json_encode(['success' => false, 'error' => 'Inventory record not found']);
            exit;
        }
        $result = $this->inventoryModel->updateStock($productId, (int)$stock);
        echo json_encode(['success' => (bool)$result]);
        exit;
    }

    /**
     * AJAX: Update product price
     */
    public function updatePrice() {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'error' => 'Invalid request method']);
            exit;
        }
        $input = json_decode(file_get_contents('php://input'), true);
        $productId = $input['product_id'] ?? null;
        $price = $input['price'] ?? null;
        if (!$productId || !is_numeric($price)) {
            echo json_encode(['success' => false, 'error' => 'Missing or invalid parameters']);
            exit;
        }
        $result = $this->productModel->updatePrice($productId, (float)$price, 'Manual update');
        echo json_encode(['success' => (bool)$result]);
        exit;
    }

    /**
     * Show inventory page
     */
    public function inventory() {
        $products = $this->getProducts();
        $lowStock = $this->getLowStockProducts();
        require_once __DIR__ . '/../views/seller/inventory.php';
    }

    /**
     * Show pricing page
     */
    public function pricing() {
        $products = $this->getProducts();
        require_once __DIR__ . '/../views/seller/pricing.php';
    }

    /**
     * Show settings page
     */
    public function settings() {
        $profile = $this->getProfile();
        require_once __DIR__ . '/../views/seller/settings.php';
    }

    /**
     * Update seller settings/profile
     */
    public function updateSettings() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/seller/settings');
            exit;
        }

        $data = [
            'business_name' => $_POST['business_name'] ?? '',
            'business_email' => $_POST['business_email'] ?? '',
            'business_phone' => $_POST['business_phone'] ?? '',
            'business_address' => $_POST['business_address'] ?? '',
            'business_description' => $_POST['business_description'] ?? '',
            'updated_at' => date('Y-m-d H:i:s')
        ];

        // Validate data
        $validator = new Validator($data);
        $rules = [
            'business_name' => 'required|min:2|max:100',
            'business_email' => 'required|email',
            'business_phone' => 'phone',
            'business_address' => 'min:5|max:255',
            'business_description' => 'min:10|max:1000'
        ];

        if (!$validator->validate($rules)) {
            Session::setFlash('errors', $validator->getErrors());
            Session::setFlash('old', $_POST);
            redirect('/seller/settings');
            exit;
        }

        try {
            if ($this->updateProfile($data)) {
                Session::setFlash('success', 'Settings updated successfully');
            } else {
                Session::setFlash('error', 'Failed to update settings');
                Session::setFlash('old', $_POST);
            }
        } catch (Exception $e) {
            error_log("Error updating seller settings: " . $e->getMessage());
            Session::setFlash('error', 'An error occurred while updating settings');
            Session::setFlash('old', $_POST);
        }

        redirect('/seller/settings');
    }
}
?>
