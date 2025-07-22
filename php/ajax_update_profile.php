<?php
session_start();
require_once 'db_connect.php';
header('Content-Type: application/json');
$response = ['status' => 'error', 'message' => 'An unknown error occurred.'];

if (!isset($_SESSION['user_id'])) {
    $response['message'] = 'Authentication failed.';
    echo json_encode($response);
    exit();
}
$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    if (empty($username)) {
        $response['message'] = 'Username cannot be empty.';
        echo json_encode($response);
        exit();
    }
    $stmt_check = $conn->prepare("SELECT user_id FROM users WHERE username = ? AND user_id != ?");
    $stmt_check->bind_param("si", $username, $user_id);
    $stmt_check->execute();
    if ($stmt_check->get_result()->num_rows > 0) {
        $response['message'] = 'This username is already taken.';
        echo json_encode($response);
        exit();
    }
    $stmt_check->close();

    $stmt_old_pic = $conn->prepare("SELECT profile_picture FROM users WHERE user_id = ?");
    $stmt_old_pic->bind_param("i", $user_id);
    $stmt_old_pic->execute();
    $old_picture_path = $stmt_old_pic->get_result()->fetch_assoc()['profile_picture'];
    $stmt_old_pic->close();

    $new_picture_path = $old_picture_path;
    $params = [$username];
    $types = "s";
    
    if (isset($_FILES["profile_picture"]) && $_FILES["profile_picture"]["error"] == UPLOAD_ERR_OK) {
        $target_dir = "../uploads/profiles/";
        if (!is_dir($target_dir)) { mkdir($target_dir, 0755, true); }
        $file_type = strtolower(pathinfo($_FILES["profile_picture"]["name"], PATHINFO_EXTENSION));
        $target_file = $target_dir . "user_" . $user_id . "_" . time() . "." . $file_type;
        if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file)) {
            $new_picture_path = 'uploads/profiles/' . basename($target_file);
            if (!empty($old_picture_path) && strpos($old_picture_path, 'default-profile.png') === false && file_exists('../' . $old_picture_path)) {
                unlink('../' . $old_picture_path);
            }
        }
    }
    
    $query = "UPDATE users SET username = ?";
    if ($new_picture_path !== $old_picture_path) {
        $query .= ", profile_picture = ?";
        $params[] = $new_picture_path;
        $types .= "s";
    }
    $query .= " WHERE user_id = ?";
    $params[] = $user_id;
    $types .= "i";
    $stmt_update = $conn->prepare($query);
    $stmt_update->bind_param($types, ...$params);
    
    if ($stmt_update->execute()) {
        $response['status'] = 'success';
        $response['message'] = 'Profile updated successfully!';
    } else {
        $response['message'] = 'Database update failed.';
    }
    $stmt_update->close();
}
$conn->close();
echo json_encode($response);
?>