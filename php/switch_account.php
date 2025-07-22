<?php
// php/ajax_switch_account.php

require_once 'db_connect.php';
session_start();
header('Content-Type: application/json');

$response = ['status' => 'error', 'message' => 'An unknown error occurred.'];

// Security check: User must be logged in to switch
if (!isset($_SESSION['user_id'])) {
    $response['message'] = 'Authentication failed. Please log in again.';
    echo json_encode($response);
    exit();
}

// Get password from the form
$password = $_POST['switch_password'] ?? '';

if (empty($password)) {
    $response['message'] = 'Password is required.';
    echo json_encode($response);
    exit();
}

// --- Verify Password ---
$user_id = $_SESSION['user_id'];
$query = "SELECT password_hash FROM users WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($user = $result->fetch_assoc()) {
    // Check if the provided password matches the stored hash
    if (password_verify($password, $user['password_hash'])) {
        // --- SUCCESS ---
        // The user is authenticated. We can now "switch" their role in the session.
        // We set 'is_seller' to 0 to grant access to regular user pages.
        $_SESSION['is_seller'] = 0; 
        
        $response['status'] = 'success';
        $response['message'] = 'Switching to user account...';
    } else {
        // --- FAILURE ---
        $response['message'] = 'Incorrect password. Please try again.';
    }
} else {
    $response['message'] = 'User not found.';
}

$stmt->close();
$conn->close();
echo json_encode($response);
?>