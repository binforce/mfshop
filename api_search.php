<?php
require 'db_connect.php';
header('Content-Type: application/json');
$q = isset($_GET['q']) ? trim($_GET['q']) : '';
if ($q === '') {
echo json_encode([]);
exit;
}
$escaped_q = $conn->real_escape_string($q);
// Chỉ tìm kiếm các sản phẩm chưa bị ẩn
$query = "SELECT id, name, price, sale_price, image FROM products WHERE is_hidden = 0 AND (name LIKE '%$escaped_q%' OR description LIKE '%$escaped_q%') LIMIT 6";
$result = $conn->query($query);
$products = [];
if ($result && $result->num_rows > 0) {
while ($row = $result->fetch_assoc()) {
$products[] = $row;
}
}
echo json_encode($products);
?>