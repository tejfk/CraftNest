<?php
require_once '../includes/auth_check.php';
$users = [];
$query = "SELECT user_id, username, email, is_seller, status, created_at FROM users ORDER BY created_at DESC";
$result = $conn->query($query);
if ($result) { while ($row = $result->fetch_assoc()) { $users[] = $row; } }
?>
<h3 class="text-3xl font-bold mb-8">Account Management</h3>
<div class="bg-card-light dark:bg-card-dark p-6 rounded-xl shadow-md">
    <input type="text" id="table-search" class="w-full mb-4 p-2 border rounded-lg dark:bg-background-dark dark:border-gray-600" placeholder="Search by username or email...">
    <div class="overflow-x-auto">
        <table class="w-full text-left" id="users-table">
            <thead>
                <tr class="border-b dark:border-gray-700"><th class="p-4">Username</th><th class="p-4">Email</th><th class="p-4">Role</th><th class="p-4">Status</th><th class="p-4">Registered On</th><th class="p-4 text-center">Actions</th></tr>
            </thead>
            <tbody>
                <?php if (empty($users)): ?>
                    <tr><td colspan="6" class="p-4 text-center text-subtext-dark">No users found.</td></tr>
                <?php else: ?>
                    <?php foreach ($users as $user): ?>
                        <tr class="border-b dark:border-gray-700" data-user-id="<?php echo $user['user_id']; ?>">
                            <td class="p-4 font-medium"><?php echo htmlspecialchars($user['username']); ?></td>
                            <td class="p-4"><?php echo htmlspecialchars($user['email']); ?></td>
                            <td class="p-4"><?php echo $user['is_seller'] ? 'Seller' : 'Buyer'; ?></td>
                            <td class="p-4 status-cell">
                                <?php if ($user['status'] === 'active'): ?><span class="px-2 py-1 font-semibold leading-tight text-green-700 bg-green-100 rounded-full">Active</span><?php else: ?><span class="px-2 py-1 font-semibold leading-tight text-red-700 bg-red-100 rounded-full">Suspended</span><?php endif; ?>
                            </td>
                            <td class="p-4"><?php echo date("M d, Y", strtotime($user['created_at'])); ?></td>
                            <td class="p-4 text-center space-x-2 actions-cell">
                                <?php if ($user['status'] === 'active'): ?><button class="action-btn bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-1 px-3 rounded" data-action="suspend">Suspend</button><?php else: ?><button class="action-btn bg-blue-500 hover:bg-blue-600 text-white font-bold py-1 px-3 rounded" data-action="activate">Activate</button><?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>