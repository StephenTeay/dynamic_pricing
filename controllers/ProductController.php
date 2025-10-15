<?php
// controllers/ProductController.php

require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Inventory.php';
require_once __DIR__ . '/../core/Validator.php';
require_once __DIR__ . '/../core/Session.php';
require_once __DIR__ . '/../utils/helpers.php';

class ProductController {
    private $productModel;
    private $inventoryModel;

    public function __construct() {
        $this->productModel = new Product();
        $this->inventoryModel = new Inventory();
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->showCreateForm();
        }

        $data = Validator::sanitize($_POST);

        $validator = new Validator($data);
        $rules = [
            'product_name' => 'required|min:3|max:255',
            'base_cost' => 'required|numeric',
            'current_price' => 'required|numeric',
            'category' => 'required',
            'quantity' => 'required|numeric'
        ];

        if ($validator->validate($rules)) {
            try {
                // Handle image upload
                $imageUrl = $this->handleImageUpload($_FILES['product_image'] ?? null);

                $productData = [
                    'seller_id' => $this->getSellerIdFromSession(),
                    'product_name' => $data['product_name'],
                    'product_description' => $data['product_description'] ?? '',
                    'category' => $data['category'],
                    'base_cost' => $data['base_cost'],
                    'cost_currency' => $data['cost_currency'] ?? DEFAULT_CURRENCY,
                    'current_price' => $data['current_price'],
                    'price_currency' => $data['price_currency'] ?? DEFAULT_CURRENCY,
                    'image_url' => $imageUrl,
                    'sku' => $data['sku'] ?? $this->generateSKU()
                ];

                $this->productModel->beginTransaction();

                $productId = $this->productModel->create($productData);

                // Create inventory record
                $this->inventoryModel->createInventoryForProduct($productId, $data['quantity']);

                $this->productModel->commit();

                Session::setFlash('success', 'Product created successfully!');
                redirect('/seller/products');
                exit;

            } catch (Exception $e) {
                $this->productModel->rollback();
                Session::setFlash('error', 'Failed to create product: ' . $e->getMessage());
            }
        } else {
            Session::setFlash('error', $validator->getFirstError());
        }

        $this->showCreateForm($data);
    }

    public function update($productId) {
        $product = $this->productModel->find($productId);

        if (!$product || $product['seller_id'] != $this->getSellerIdFromSession()) {
            Session::setFlash('error', 'Product not found or access denied.');
            redirect('/seller/products');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->showEditForm($product);
        }

        $data = Validator::sanitize($_POST);

        $validator = new Validator($data);
        $rules = [
            'product_name' => 'required|min:3|max:255',
            'base_cost' => 'required|numeric',
            'current_price' => 'required|numeric',
            'category' => 'required'
        ];

        if ($validator->validate($rules)) {
            try {
                $imageUrl = $product['image_url'];

                // Handle new image upload
                if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === 0) {
                    $imageUrl = $this->handleImageUpload($_FILES['product_image']);

                    // Delete old image
                    if ($product['image_url']) {
                        @unlink(UPLOAD_DIR . basename($product['image_url']));
                    }
                }

                $updateData = [
                    'product_name' => $data['product_name'],
                    'product_description' => $data['product_description'] ?? '',
                    'category' => $data['category'],
                    'base_cost' => $data['base_cost'],
                    'cost_currency' => $data['cost_currency'] ?? DEFAULT_CURRENCY,
                    'current_price' => $data['current_price'],
                    'price_currency' => $data['price_currency'] ?? DEFAULT_CURRENCY,
                    'image_url' => $imageUrl
                ];

                $this->productModel->update($productId, $updateData);

                Session::setFlash('success', 'Product updated successfully!');
                redirect('/seller/products');
                exit;

            } catch (Exception $e) {
                Session::setFlash('error', 'Failed to update product: ' . $e->getMessage());
            }
        } else {
            Session::setFlash('error', $validator->getFirstError());
        }

        return $this->showEditForm($product);
    }

    public function delete($productId) {
        $product = $this->productModel->find($productId);

        if (!$product || $product['seller_id'] != $this->getSellerIdFromSession()) {
            return $this->jsonResponse(['success' => false, 'message' => 'Product not found']);
        }

        // Soft delete - just mark as inactive
        $this->productModel->update($productId, ['is_active' => 0]);

        return $this->jsonResponse(['success' => true, 'message' => 'Product deleted successfully']);
    }

    public function getProducts() {
        $sellerId = $this->getSellerIdFromSession();
        $page = $_GET['page'] ?? 1;
        $limit = PRODUCTS_PER_PAGE;
        $offset = ($page - 1) * $limit;

        $products = $this->productModel->getProductsWithInventory($sellerId);

        return $products;
    }

    public function getProductDetail($productId) {
        return $this->productModel->getProductDetail($productId);
    }

    private function handleImageUpload($file) {
        if (!$file || $file['error'] !== 0) {
            return null;
        }

        // Validate file
        if (!in_array($file['type'], ALLOWED_IMAGE_TYPES)) {
            throw new Exception('Invalid image type');
        }

        if ($file['size'] > MAX_FILE_SIZE) {
            throw new Exception('File size exceeds limit');
        }

        // Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '_' . time() . '.' . $extension;
        $filepath = UPLOAD_DIR . $filename;

        // Create upload directory if not exists
        if (!is_dir(UPLOAD_DIR)) {
            mkdir(UPLOAD_DIR, 0755, true);
        }

        // Move uploaded file
        if (!move_uploaded_file($file['tmp_name'], $filepath)) {
            throw new Exception('Failed to upload image');
        }

        return ASSETS_URL . '/images/uploads/' . $filename;
    }

    private function generateSKU() {
        return 'SKU-' . strtoupper(substr(uniqid(), -8));
    }

    private function getSellerIdFromSession() {
        $userId = Session::getUserId();

        // Get seller_id from seller_profiles
        $query = "SELECT seller_id FROM seller_profiles WHERE user_id = :user_id";
        $stmt = $this->productModel->db->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        $result = $stmt->fetch();

        return $result ? $result['seller_id'] : null;
    }

    private function showCreateForm($data = []) {
        include __DIR__ . '/../views/seller/product_form.php';
    }

    private function showEditForm($product) {
        include __DIR__ . '/../views/seller/product_form.php';
    }

    private function jsonResponse($data) {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
<?php
// controllers/ProductController.php

require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Inventory.php';
require_once __DIR__ . '/../core/Validator.php';
require_once __DIR__ . '/../core/Session.php';

class ProductController {
    private $productModel;
    private $inventoryModel;
    
    public function __construct() {
        $this->productModel = new Product();
        $this->inventoryModel = new Inventory();
    }
    
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->showCreateForm();
        }
        
        $data = Validator::sanitize($_POST);
        
        $validator = new Validator($data);
        $rules = [
            'product_name' => 'required|min:3|max:255',
            'base_cost' => 'required|numeric',
            'current_price' => 'required|numeric',
            'category' => 'required',
            'quantity' => 'required|numeric'
        ];
        
        if ($validator->validate($rules)) {
            try {
                // Handle image upload
                $imageUrl = $this->handleImageUpload($_FILES['product_image'] ?? null);
                
                $productData = [
                    'seller_id' => $this->getSellerIdFromSession(),
                    'product_name' => $data['product_name'],
                    'product_description' => $data['product_description'] ?? '',
                    'category' => $data['category'],
                    'base_cost' => $data['base_cost'],
                    'cost_currency' => $data['cost_currency'] ?? DEFAULT_CURRENCY,
                    'current_price' => $data['current_price'],
                    'price_currency' => $data['price_currency'] ?? DEFAULT_CURRENCY,
                    'image_url' => $imageUrl,
                    'sku' => $data['sku'] ?? $this->generateSKU()
                ];
                
                $this->productModel->beginTransaction();
                
                $productId = $this->productModel->create($productData);
                
                // Create inventory record
                $this->inventoryModel->createInventoryForProduct($productId, $data['quantity']);
                
                $this->productModel->commit();
                
                Session::setFlash('success', 'Product created successfully!');
                redirect('/seller/products');
                exit;
                
            } catch (Exception $e) {
                $this->productModel->rollback();
                Session::setFlash('error', 'Failed to create product: ' . $e->getMessage());
            }
        } else {
            Session::setFlash('error', $validator->getFirstError());
        }
        
        $this->showCreateForm($data);
    }
    
    public function update($productId) {
        $product = $this->productModel->find($productId);
        
        if (!$product || $product['seller_id'] != $this->getSellerIdFromSession()) {
            Session::setFlash('error', 'Product not found or access denied.');
            redirect('/seller/products');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->showEditForm($product);
        }
        
        $data = Validator::sanitize($_POST);
        
        $validator = new Validator($data);
        $rules = [
            'product_name' => 'required|min:3|max:255',
            'base_cost' => 'required|numeric',
            'current_price' => 'required|numeric',
            'category' => 'required'
        ];
        
        if ($validator->validate($rules)) {
            try {
                $imageUrl = $product['image_url'];
                
                // Handle new image upload
                if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === 0) {
                    $imageUrl = $this->handleImageUpload($_FILES['product_image']);
                    
                    // Delete old image
                    if ($product['image_url']) {
                        @unlink(UPLOAD_DIR . basename($product['image_url']));
                    }
                }
                
                $updateData = [
                    'product_name' => $data['product_name'],
                    'product_description' => $data['product_description'] ?? '',
                    'category' => $data['category'],
                    'base_cost' => $data['base_cost'],
                    'cost_currency' => $data['cost_currency'] ?? DEFAULT_CURRENCY,
                    'current_price' => $data['current_price'],
                    'price_currency' => $data['price_currency'] ?? DEFAULT_CURRENCY,
                    'image_url' => $imageUrl
                ];
                
                $this->productModel->update($productId, $updateData);
                
                Session::setFlash('success', 'Product updated successfully!');
                redirect('/seller/products');
                exit;
                
            } catch (Exception $e) {
                Session::setFlash('error', 'Failed to update product: ' . $e->getMessage());
            }
        } else {
            Session::setFlash('error', $validator->getFirstError());
        }
        
        return $this->showEditForm($product);
    }
    
    public function delete($productId) {
        $product = $this->productModel->find($productId);
        
        if (!$product || $product['seller_id'] != $this->getSellerIdFromSession()) {
            return $this->jsonResponse(['success' => false, 'message' => 'Product not found']);
        }
        
        // Soft delete - just mark as inactive
        $this->productModel->update($productId, ['is_active' => 0]);
        
        return $this->jsonResponse(['success' => true, 'message' => 'Product deleted successfully']);
    }
    
    public function getProducts() {
        $sellerId = $this->getSellerIdFromSession();
        $page = $_GET['page'] ?? 1;
        $limit = PRODUCTS_PER_PAGE;
        $offset = ($page - 1) * $limit;
        
        $products = $this->productModel->getProductsWithInventory($sellerId);
        
        return $products;
    }
    
    public function getProductDetail($productId) {
        return $this->productModel->getProductDetail($productId);
    }
    
    private function handleImageUpload($file) {
        if (!$file || $file['error'] !== 0) {
            return null;
        }
        
        // Validate file
        if (!in_array($file['type'], ALLOWED_IMAGE_TYPES)) {
            throw new Exception('Invalid image type');
        }
        
        if ($file['size'] > MAX_FILE_SIZE) {
            throw new Exception('File size exceeds limit');
        }
        
        // Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '_' . time() . '.' . $extension;
        $filepath = UPLOAD_DIR . $filename;
        
        // Create upload directory if not exists
        if (!is_dir(UPLOAD_DIR)) {
            mkdir(UPLOAD_DIR, 0755, true);
        }
        
        // Move uploaded file
        if (!move_uploaded_file($file['tmp_name'], $filepath)) {
            throw new Exception('Failed to upload image');
        }
        
        return ASSETS_URL . '/images/uploads/' . $filename;
    }
    
    private function generateSKU() {
        return 'SKU-' . strtoupper(substr(uniqid(), -8));
    }
    
    private function getSellerIdFromSession() {
        $userId = Session::getUserId();
        
        // Get seller_id from seller_profiles
        $query = "SELECT seller_id FROM seller_profiles WHERE user_id = :user_id";
        $stmt = $this->productModel->db->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        $result = $stmt->fetch();
        
        return $result ? $result['seller_id'] : null;
    }
    
    private function showCreateForm($data = []) {
        include __DIR__ . '/../views/seller/product_form.php';
    }
    
    private function showEditForm($product) {
        include __DIR__ . '/../views/seller/product_form.php';
    }
    
    private function jsonResponse($data) {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->showCreateForm();
        }
        
        $data = Validator::sanitize($_POST);
        
        $validator = new Validator($data);
        $rules = [
            'product_name' => 'required|min:3|max:255',
            'base_cost' => 'required|numeric',
            'current_price' => 'required|numeric',
            'category' => 'required',
            'quantity' => 'required|numeric'
        ];
        
        if ($validator->validate($rules)) {
            try {
                // Handle image upload
                $imageUrl = $this->handleImageUpload($_FILES['product_image'] ?? null);
                
                $productData = [
                    'seller_id' => $this->getSellerIdFromSession(),
                    'product_name' => $data['product_name'],
                    'product_description' => $data['product_description'] ?? '',
                    'category' => $data['category'],
                    'base_cost' => $data['base_cost'],
                    'cost_currency' => $data['cost_currency'] ?? DEFAULT_CURRENCY,
                    'current_price' => $data['current_price'],
                    'price_currency' => $data['price_currency'] ?? DEFAULT_CURRENCY,
                    'image_url' => $imageUrl,
                    'sku' => $data['sku'] ?? $this->generateSKU()
                ];
                
                $this->productModel->beginTransaction();
                
                $productId = $this->productModel->create($productData);
                
                // Create inventory record
                $this->inventoryModel->createInventoryForProduct($productId, $data['quantity']);
                
                $this->productModel->commit();
                
                Session::setFlash('success', 'Product created successfully!');
                header('Location: /seller/products.php');
                exit;
                
            } catch (Exception $e) {
                $this->productModel->rollback();
                Session::setFlash('error', 'Failed to create product: ' . $e->getMessage());
            }
        } else {
            Session::setFlash('error', $validator->getFirstError());
        }
        
        $this->showEditForm($product);
    }
    
    public function delete($productId) {
        $product = $this->productModel->find($productId);
        
        if (!$product || $product['seller_id'] != $this->getSellerIdFromSession()) {
            return $this->jsonResponse(['success' => false, 'message' => 'Product not found']);
        }
        
        // Soft delete - just mark as inactive
        $this->productModel->update($productId, ['is_active' => 0]);
        
        return $this->jsonResponse(['success' => true, 'message' => 'Product deleted successfully']);
    }
    
    public function getProducts() {
        $sellerId = $this->getSellerIdFromSession();
        $page = $_GET['page'] ?? 1;
        $limit = PRODUCTS_PER_PAGE;
        $offset = ($page - 1) * $limit;
        
        $products = $this->productModel->getProductsWithInventory($sellerId);
        
        return $products;
    }
    
    public function getProductDetail($productId) {
        return $this->productModel->getProductDetail($productId);
    }
    
    private function handleImageUpload($file) {
        if (!$file || $file['error'] !== 0) {
            return null;
        }
        
        // Validate file
        if (!in_array($file['type'], ALLOWED_IMAGE_TYPES)) {
            throw new Exception('Invalid image type');
        }
        
        if ($file['size'] > MAX_FILE_SIZE) {
            throw new Exception('File size exceeds limit');
        }
        
        // Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '_' . time() . '.' . $extension;
        $filepath = UPLOAD_DIR . $filename;
        
        // Create upload directory if not exists
        if (!is_dir(UPLOAD_DIR)) {
            mkdir(UPLOAD_DIR, 0755, true);
        }
        
        // Move uploaded file
        if (!move_uploaded_file($file['tmp_name'], $filepath)) {
            throw new Exception('Failed to upload image');
        }
        
        return ASSETS_URL . '/images/uploads/' . $filename;
    }
    
    private function generateSKU() {
        return 'SKU-' . strtoupper(substr(uniqid(), -8));
    }
    
    private function getSellerIdFromSession() {
        $userId = Session::getUserId();
        
        // Get seller_id from seller_profiles
        $query = "SELECT seller_id FROM seller_profiles WHERE user_id = :user_id";
        $stmt = $this->productModel->db->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        $result = $stmt->fetch();
        
        return $result ? $result['seller_id'] : null;
    }
    
    private function showCreateForm($data = []) {
        include __DIR__ . '/../views/seller/product_form.php';
    }
    
    private function showEditForm($product) {
        include __DIR__ . '/../views/seller/product_form.php';
    }
    
    private function jsonResponse($data) {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}());
        }
        
        $this->showCreateForm($data);
    }
    
    public function update($productId) {
        $product = $this->productModel->find($productId);
        
        if (!$product || $product['seller_id'] != $this->getSellerIdFromSession()) {
            Session::setFlash('error', 'Product not found or access denied.');
            header('Location: /seller/products.php');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->showEditForm($product);
        }
        
        $data = Validator::sanitize($_POST);
        
        $validator = new Validator($data);
        $rules = [
            'product_name' => 'required|min:3|max:255',
            'base_cost' => 'required|numeric',
            'current_price' => 'required|numeric',
            'category' => 'required'
        ];
        
        if ($validator->validate($rules)) {
            try {
                $imageUrl = $product['image_url'];
                
                // Handle new image upload
                if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === 0) {
                    $imageUrl = $this->handleImageUpload($_FILES['product_image']);
                    
                    // Delete old image
                    if ($product['image_url']) {
                        @unlink(UPLOAD_DIR . basename($product['image_url']));
                    }
                }
                
                $updateData = [
                    'product_name' => $data['product_name'],
                    'product_description' => $data['product_description'] ?? '',
                    'category' => $data['category'],
                    'base_cost' => $data['base_cost'],
                    'cost_currency' => $data['cost_currency'] ?? DEFAULT_CURRENCY,
                    'current_price' => $data['current_price'],
                    'price_currency' => $data['price_currency'] ?? DEFAULT_CURRENCY,
                    'image_url' => $imageUrl
                ];
                
                $this->productModel->update($productId, $updateData);
                
                Session::setFlash('success', 'Product updated successfully!');
                header('Location: /seller/products.php');
                exit;
                
            } catch (Exception $e) {
                Session::setFlash('error', 'Failed to update product: ' . $e->getMessage());
            }
        } else {
            Session::setFlash('error', $validator->getFirstError