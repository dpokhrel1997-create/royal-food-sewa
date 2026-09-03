<?php
// General Functions

// Session management
function startSession() {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
}

// Check if user is logged in (for admin)
function isAdminLoggedIn() {
    startSession();
    return isset($_SESSION['admin_id']) && !empty($_SESSION['admin_id']);
}

// Redirect to login if not authenticated
function requireAdminLogin() {
    if (!isAdminLoggedIn()) {
        header('Location: login.php');
        exit();
    }
}

// Sanitize input
function sanitize($input) {
    $input = trim($input);
    $input = stripslashes($input);
    $input = htmlspecialchars($input);
    return $input;
}

// Validate email
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Validate phone number (basic validation)
function isValidPhone($phone) {
    return preg_match('/^[0-9]{7,15}$/', $phone);
}

// Format currency
function formatCurrency($amount) {
    return number_format($amount, 2, '.', ',');
}

// Format date
function formatDate($date, $format = 'Y-m-d H:i:s') {
    return date($format, strtotime($date));
}

// Generate random order number
function generateOrderNumber() {
    return 'ORD-' . date('YmdHis') . '-' . rand(1000, 9999);
}

// Get order status badge color
function getStatusBadgeColor($status) {
    $colors = [
        'pending' => 'warning',
        'confirmed' => 'info',
        'preparing' => 'primary',
        'out_for_delivery' => 'secondary',
        'delivered' => 'success',
        'cancelled' => 'danger'
    ];
    return $colors[$status] ?? 'secondary';
}

// Get order status label
function getStatusLabel($status) {
    $labels = [
        'pending' => 'Pending',
        'confirmed' => 'Confirmed',
        'preparing' => 'Preparing',
        'out_for_delivery' => 'Out for Delivery',
        'delivered' => 'Delivered',
        'cancelled' => 'Cancelled'
    ];
    return $labels[$status] ?? 'Unknown';
}

// Upload file
function uploadFile($file, $uploadDir = '../uploads/') {
    if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
        return false;
    }
    
    $fileName = basename($file['name']);
    $fileExt = pathinfo($fileName, PATHINFO_EXTENSION);
    $allowedExts = ['jpg', 'jpeg', 'png', 'gif'];
    
    if (!in_array(strtolower($fileExt), $allowedExts)) {
        return false;
    }
    
    $newFileName = time() . '_' . uniqid() . '.' . $fileExt;
    $uploadPath = $uploadDir . $newFileName;
    
    if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
        return $newFileName;
    }
    
    return false;
}

// Delete file
function deleteFile($fileName, $uploadDir = '../uploads/') {
    $filePath = $uploadDir . $fileName;
    if (file_exists($filePath)) {
        unlink($filePath);
        return true;
    }
    return false;
}

// Get website settings
function getSetting($key) {
    global $conn;
    $result = $conn->query("SELECT value FROM settings WHERE setting_key = '$key' LIMIT 1");
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['value'];
    }
    return null;
}

// Calculate total order amount
function calculateOrderTotal($orderId) {
    global $conn;
    
    $result = $conn->query("SELECT SUM(quantity * price) as subtotal FROM order_items WHERE order_id = $orderId");
    $row = $result->fetch_assoc();
    $subtotal = $row['subtotal'] ?? 0;
    
    // Get delivery charge
    $orderResult = $conn->query("SELECT delivery_charge FROM orders WHERE id = $orderId");
    $orderRow = $orderResult->fetch_assoc();
    $deliveryCharge = $orderRow['delivery_charge'] ?? 0;
    
    return $subtotal + $deliveryCharge;
}

// Convert status to URL parameter
function statusToParam($status) {
    return str_replace(' ', '_', strtolower($status));
}

// Set flash message
function setFlash($key, $message) {
    startSession();
    $_SESSION['flash'][$key] = $message;
}

// Get flash message
function getFlash($key) {
    startSession();
    if (isset($_SESSION['flash'][$key])) {
        $message = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);
        return $message;
    }
    return null;
}

// HTML helper - Display success alert
function displayAlert($type, $message) {
    $alertClass = [
        'success' => 'alert-success',
        'error' => 'alert-danger',
        'warning' => 'alert-warning',
        'info' => 'alert-info'
    ];
    
    $class = $alertClass[$type] ?? 'alert-info';
    
    echo '<div class="alert ' . $class . ' alert-dismissible fade show" role="alert">';
    echo $message;
    echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
    echo '</div>';
}
?>
