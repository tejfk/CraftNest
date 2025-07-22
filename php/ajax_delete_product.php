<?php
session_start();
require_once 'db_connect.php';
header('Content-Type: application/json');

$response = ['status' => 'error', 'message' => 'An unknown error occurred.'];

// Security: Check if user is a logged-in seller
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_seller']) || $_SESSION['is_seller'] != 1) {
    $response['message'] = 'Authentication failed.';
    echo json_encode($response);
    exit();
}

if (isset($_POST['product_id'])) {
    $product_id = (int)$_POST['product_id'];
    $user_id = $_SESSION['user_id'];

    // Security: First, fetch the product to verify ownership and get image URL
    $stmt = $conn->prepare("SELECT user_id, image_url FROM products WHERE product_id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($product = $result->fetch_assoc()) {
        // Double-check that this product belongs to the logged-in seller
        if ($product['user_id'] == $user_id) {
            
            // Delete the database record
            $delete_stmt = $conn->prepare("DELETE FROM products WHERE product_id = ?");
            $delete_stmt->bind_param("i", $product_id);
            
            if ($delete_stmt->execute()) {
                // If deletion is successful, also delete the image file from the server
                if (!empty($product['image_url']) && file_exists('../' . $product['image_url'])) {
                    unlink('../' . $product['image_url']);
                }
                $response['status'] = 'success';
                $response['message'] = 'Product removed successfully.';
            } else {
                $response['message'] = 'Failed to remove product from database.';
            }
            $delete_stmt->close();
            
        } else {
            // This is a security failure - user is trying to delete someone else's product
            $response['message'] = 'Authorization error. You do not own this product.';
        }
    } else {
        $response['message'] = 'Product not found.';
    }
    $stmt->close();
} else {
    $response['message'] = 'Product ID not provided.';
}

$conn->close();
echo json_encode($response);
?>