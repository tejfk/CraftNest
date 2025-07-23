<?php
require_once '../includes/auth_check.php';
$pending_products = [];
$query = "SELECT p.product_id, p.name, u.username as seller_name, p.price, p.start_bid, p.is_for_auction, p.created_at FROM products p JOIN users u ON p.user_id = u.user_id WHERE p.status = 'pending' ORDER BY p.created_at DESC";
$result = $conn->query($query);
if ($result) { while ($row = $result->fetch_assoc()) { $pending_products[] = $row; } }
?>
<h3 class="text-3xl font-bold mb-8">Product Approval Queue</h3>
<div class="bg-card-light dark:bg-card-dark p-6 rounded-xl shadow-md">
    <div class="overflow-x-auto">
        <table class="w-full text-left" id="products-table">
            <thead>
                <tr class="border-b dark:border-gray-700"><th class="p-4">Product Name</th><th class="p-4">Seller</th><th class="p-4">Type</th><th class="p-4">Price</th><th class="p-4">Date Submitted</th><th class="p-4 text-center">Actions</th></tr>
            </thead>
            <tbody>
                <?php if (empty($pending_products)): ?>
                    <tr><td colspan="6" class="p-4 text-center text-subtext-dark">No pending products found.</td></tr>
                <?php else: ?>
                    <?php foreach ($pending_products as $product): ?>
                        <tr class="border-b dark:border-gray-700" data-product-id="<?php echo $product['product_id']; ?>">
                            <td class="p-4 font-medium"><?php echo htmlspecialchars($product['name']); ?></td>
                            <td class="p-4"><?php echo htmlspecialchars($product['seller_name']); ?></td>
                            <td class="p-4"><?php echo $product['is_for_auction'] ? 'Auction' : 'Market'; ?></td>
                            <td class="p-4">₱<?php echo number_format($product['is_for_auction'] ? $product['start_bid'] : $product['price']); ?></td>
                            <td class="p-4"><?php echo date("M d, Y", strtotime($product['created_at'])); ?></td>
                            <td class="p-4 text-center space-x-2">
                                <button class="action-btn bg-green-500 hover:bg-green-600 text-white font-bold py-1 px-3 rounded" data-action="approve">Approve</button>
                                <button class="action-btn bg-red-500 hover:bg-red-600 text-white font-bold py-1 px-3 rounded" data-action="reject">Reject</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>