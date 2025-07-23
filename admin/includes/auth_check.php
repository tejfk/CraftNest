<?php
session_start();

// Check if an admin is logged in. If not, redirect to the login page.
if (!isset($_SESSION['admin_id'])) {
    // We need to account for AJAX requests vs. direct page loads
    // If it's an AJAX request, we can't redirect, so we send an error.
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        http_response_code(401); // Unauthorized
        echo json_encode(['error' => 'Session expired. Please log in again.']);
    } else {
        header('Location: ../admin_login.php'); // Redirect for normal page loads
    }
    exit();
}

// FIXED: Use an absolute path from the current file's directory to reliably find db_connect.php
require_once __DIR__ . '/../../php/db_connect.php'; 
?>