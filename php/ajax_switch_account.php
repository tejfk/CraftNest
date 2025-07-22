<?php
session_start();
require_once 'db_connect.php';
header('Content-Type: application/json');
$response = ['status' => 'error', 'message' => 'An unknown error occurred.'];

if (!isset($_SESSION['user_id'])) {
    $response['message'] = 'Authentication failed. Please log in again.';
    echo json_encode($response);
    exit();
}

if (!empty($_POST['password'])) {
    $password = $_POST['password'];
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT password_hash FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($user = $result->fetch_assoc()) {
        if (password_verify($password, $user['password_hash'])) {
            $_SESSION['is_seller'] = 0;
            $response['status'] = 'success';
            $response['message'] = 'Switching to buyer account...';
        } else {
            $response['message'] = 'Incorrect password. Please try again.';
        }
    } else {
        $response['message'] = 'User not found.';
    }
    $stmt->close();
} else {
    $response['message'] = 'Password is required.';
}
$conn->close();
echo json_encode($response);
?>