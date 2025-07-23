<?php
require_once '../includes/auth_check.php';
header('Content-Type: application/json');

$response = [
    'success' => false,
    'new_status_html' => '',
    'new_actions_html' => ''
];

if (isset($_POST['user_id']) && isset($_POST['action'])) {
    $user_id = (int)$_POST['user_id'];
    $action = $_POST['action']; // 'suspend' or 'activate'
    $new_status = ($action === 'suspend') ? 'suspended' : 'active';

    $stmt = $conn->prepare("UPDATE users SET status = ? WHERE user_id = ?");
    $stmt->bind_param("si", $new_status, $user_id);

    if ($stmt->execute() && $stmt->affected_rows > 0) {
        $response['success'] = true;

        // Generate the new HTML for the status cell
        if ($new_status === 'active') {
            $response['new_status_html'] = '<span class="px-2 py-1 font-semibold leading-tight text-green-700 bg-green-100 rounded-full">Active</span>';
        } else {
            $response['new_status_html'] = '<span class="px-2 py-1 font-semibold leading-tight text-red-700 bg-red-100 rounded-full">Suspended</span>';
        }

        // Generate the new HTML for the actions cell
        if ($new_status === 'active') {
            $response['new_actions_html'] = '<button class="action-btn bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-1 px-3 rounded" data-action="suspend">Suspend</button>';
        } else {
            $response['new_actions_html'] = '<button class="action-btn bg-blue-500 hover:bg-blue-600 text-white font-bold py-1 px-3 rounded" data-action="activate">Activate</button>';
        }
    }
}
echo json_encode($response);
?>