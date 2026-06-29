<?php
// ==========================================
// TÍNH TOÁN DỮ LIỆU SẢN PHẨM & TRANG CHỦ
// ==========================================
$limit = 9;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $limit;
$cat_id = isset($_GET['cat_id']) ? (int)$_GET['cat_id'] : 0;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
// ĐIỀU KIỆN HIỂN THỊ SLIDER
$show_slider = ($cat_id == 0 && $search == '');

// Mặc định luôn giấu sản phẩm đã bị xóa (is_hidden = 0)
$where_clauses = ["is_hidden = 0"];
if ($cat_id > 0) $where_clauses[] = "category_id = $cat_id";
if ($search !== '') {
$escaped_search = $conn->real_escape_string($search);
$where_clauses[] = "(name LIKE '%$escaped_search%' OR description LIKE '%$escaped_search%')";
}
$where_clause = "WHERE " . implode(" AND ", $where_clauses);
$total_results_query = $conn->query("SELECT COUNT(*) AS total FROM products $where_clause");
$total_results = $total_results_query ? $total_results_query->fetch_assoc()['total'] : 0;
$total_pages = ceil($total_results / $limit);
$products = $conn->query("SELECT * FROM products $where_clause ORDER BY id DESC LIMIT $limit OFFSET $offset");
$categories = $conn->query("SELECT * FROM categories");

// LẤY SẢN PHẨM BÁN CHẠY
$best_sellers = null;
$cat_best_sellers = null;
if ($show_slider) {
try {
$best_query = "SELECT p.id, p.name, p.price, p.sale_price, p.image, SUM(od.quantity) as total_sold FROM products p JOIN order_details od ON p.id = od.product_id WHERE p.is_hidden = 0 GROUP BY p.id, p.name, p.price, p.sale_price, p.image ORDER BY total_sold DESC LIMIT 10";
$best_sellers = $conn->query($best_query);
if (!$best_sellers || $best_sellers->num_rows == 0) {
$best_sellers = $conn->query("SELECT id, name, price, sale_price, image FROM products WHERE is_hidden = 0 ORDER BY id DESC LIMIT 10");
}
} catch (Exception $e) {
$best_sellers = $conn->query("SELECT id, name, price, sale_price, image FROM products WHERE is_hidden = 0 ORDER BY id DESC LIMIT 10");
}
} elseif ($cat_id > 0) {
// Top bán chạy của danh mục hiện tại
$cat_best_query = "SELECT p.id, p.name, p.price, p.sale_price, p.image, SUM(od.quantity) as total_sold FROM products p JOIN order_details od ON p.id = od.product_id WHERE p.category_id = $cat_id AND p.is_hidden = 0 GROUP BY p.id ORDER BY total_sold DESC LIMIT 3";
$cat_best_sellers = $conn->query($cat_best_query);
}
$unread_noti = 0; $user_avatar = 'default.png';
if (isset($_SESSION['user_id'])) {
$uid = $_SESSION['user_id'];
$unread_noti = $conn->query("SELECT COUNT(*) as c FROM notifications WHERE user_id=$uid AND is_read=0")->fetch_assoc()['c'] ?? 0;
$user_avatar = $conn->query("SELECT avatar FROM users WHERE id=$uid")->fetch_assoc()['avatar'] ?? 'default.png';
}
?>