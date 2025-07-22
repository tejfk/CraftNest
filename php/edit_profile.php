<?php
// php/ajax_update_profile.php

session_start();
// MODIFIED: Use your database connection file name
require_once 'db_connect.php'; 

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'errors' => ['Invalid request.']]);
    exit;
}

$user_id = $_SESSION['user_id'];
$username = trim($_POST['username']);
$email = trim($_POST['email']);
$errors = [];

// --- Validation ---
if (empty($username)) { $errors[] = "Please enter a username."; }
if (empty($email)) { $errors[] = "Please enter an email."; } 
elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) { $errors[] = "Please enter a valid email format."; }

// --- File Upload Handling ---
$new_profile_picture_name = null;
if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
    $target_dir = "../uploads/";
    $image_file_type = strtolower(pathinfo(basename($_FILES["profile_picture"]["name"]), PATHINFO_EXTENSION));
    $unique_image_name = uniqid() . '.' . $image_file_type;
    $target_file = $target_dir . $unique_image_name;

    $check = getimagesize($_FILES["profile_picture"]["tmp_name"]);
    if ($check === false) { $errors[] = "File is not an image."; }
    if ($_FILES["profile_picture"]["size"] > 5000000) { $errors[] = "Sorry, your file is too large (Max 5MB)."; }
    if (!in_array($image_file_type, ["jpg", "png", "jpeg", "gif"])) { $errors[] = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";}
    
    if (empty($errors)) {
        if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file)) {
            $new_profile_picture_name = $unique_image_name;
        } else {
            $errors[] = "Sorry, there was an error uploading your file.";
        }
    }
}

if (!empty($errors)) {
    echo json_encode(['status' => 'error', 'errors' => $errors]);
    exit;
}

// --- Database Update ---
if ($new_profile_picture_name !== null) {
    $sql = "UPDATE users SET username = ?, email = ?, profile_picture = ? WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $username, $email, $new_profile_picture_name, $user_id);
} else {
    $sql = "UPDATE users SET username = ?, email = ? WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $username, $email, $user_id);
}

if ($stmt->execute()) {
    $final_picture_name = $new_profile_picture_name;
    if ($final_picture_name === null) {
        $pic_sql = "SELECT profile_picture FROM users WHERE user_id = ?";
        $pic_stmt = $conn->prepare($pic_sql);
        $pic_stmt->bind_param("i", $user_id);
        $pic_stmt->execute();
        $pic_stmt->bind_result($final_picture_name);
        $pic_stmt->fetch();
        $pic_stmt->close();
    }
    
    echo json_encode([
        'status' => 'success',
        'data' => [ 'username' => $username, 'email' => $email, 'profile_picture' => $final_picture_name ]
    ]);
} else {
    echo json_encode(['status' => 'error', 'errors' => ['Database update failed.']]);
}

$stmt->close();
$conn->close();
?>