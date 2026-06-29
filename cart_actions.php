<?php
// ==========================================
// XỬ LÝ CÁC THAO TÁC TRONG GIỎ HÀNG (DATABASE) & ĐẶT HÀNG
// ==========================================
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once 'db_connect.php';

// Yêu cầu đăng nhập cho mọi thao tác giỏ hàng
if (!isset($_SESSION['user_id'])) {
    $_SESSION['sys_msg'] = ['type'=>'error', 'title'=>'Yêu cầu đăng nhập', 'msg'=>'Vui lòng đăng nhập để sử dụng giỏ hàng!'];
    header("Location: login.php"); exit();
}
$uid = (int)$_SESSION['user_id'];

// XÓA NHIỀU SẢN PHẨM CÙNG LÚC
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['bulk_remove_cart'])) {
    if (!empty($_POST['selected_cart_items'])) {
        $ids = implode(',', array_map('intval', $_POST['selected_cart_items']));
        $conn->query("DELETE FROM carts WHERE id IN ($ids) AND user_id = $uid");
    }
    header("Location: cart.php"); exit();
}

// THÊM SẢN PHẨM VÀO GIỎ HÀNG
if (isset($_GET['action']) && $_GET['action'] == 'add') {
    $id = (int)$_POST['product_id'];
    $qty = (int)$_POST['quantity'];
    $color = "Mặc định"; $size = "Mặc định";

    if(isset($_POST['variant_combined'])){ 
        $parts = explode('|', $_POST['variant_combined']); 
        $color = $conn->real_escape_string($parts); 
        $size = $conn->real_escape_string($parts[1]); 
    } else { 
        if(isset($_POST['color'])) $color = $conn->real_escape_string($_POST['color']); 
        if(isset($_POST['size'])) $size = $conn->real_escape_string($_POST['size']); 
    }

    // Kiểm tra xem sản phẩm có phân loại đó đã có trong giỏ hàng chưa
    $check = $conn->query("SELECT id, quantity FROM carts WHERE user_id=$uid AND product_id=$id AND color='$color' AND size='$size'");
    
    if ($check->num_rows > 0) {
        $cart_item = $check->fetch_assoc();
        $new_qty = $cart_item['quantity'] + $qty;
        $conn->query("UPDATE carts SET quantity = $new_qty WHERE id = {$cart_item['id']}");
    } else {
        $conn->query("INSERT INTO carts (user_id, product_id, color, size, quantity) VALUES ($uid, $id, '$color', '$size', $qty)");
    }

    if(isset($_POST['is_quick'])) { 
        $_SESSION['sys_msg'] = ['type' => 'success', 'title' => 'Thành công', 'msg' => 'Đã thêm sản phẩm vào giỏ hàng!'];
        header("Location: index.php"); exit(); 
    }
    header("Location: cart.php"); exit();
}

// XÓA 1 SẢN PHẨM
if (isset($_GET['action']) && $_GET['action'] == 'remove') { 
    $cart_id = (int)$_GET['key'];
    $conn->query("DELETE FROM carts WHERE id=$cart_id AND user_id=$uid");
    header("Location: cart.php"); exit(); 
}

// XỬ LÝ ĐẶT HÀNG (CHECKOUT)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['process_checkout'])) {
    $payment_method = $_POST['payment_method']; 
    $note = $conn->real_escape_string($_POST['note']);
    $status = ($payment_method == 'QR') ? 'Chờ xác nhận thanh toán' : 'Đang chuẩn bị hàng';

    // Lấy giỏ hàng từ CSDL để tính tiền
    $cart_items = $conn->query("SELECT c.quantity as qty, c.color, c.size, p.id, p.name, p.image, p.price, p.sale_price FROM carts c JOIN products p ON c.product_id = p.id WHERE c.user_id = $uid");
    if ($cart_items->num_rows == 0) { header("Location: cart.php"); exit(); }

    $total_amount = 0; 
    $items_for_mail = [];
    $cart_array = [];
    while ($item = $cart_items->fetch_assoc()) { 
        $real_price = ($item['sale_price'] > 0) ? $item['sale_price'] : $item['price'];
        $total_amount += ($real_price * $item['qty']); 
        $item['real_price'] = $real_price;
        $cart_array[] = $item;
    }

    $discount = 0;
    if (isset($_SESSION['applied_promo']) && isset($_SESSION['discount_value'])) { 
        $discount = $_SESSION['discount_value']; 
    }

    $total_after_discount = max(0, $total_amount - $discount);
    $vat = $total_after_discount * 0.10; 
    $final_total = $total_after_discount + $vat;

    $stmt = $conn->prepare("INSERT INTO orders (user_id, total_amount, vat_amount, status, note) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iddss", $uid, $final_total, $vat, $status, $note); 
    $stmt->execute();
    $order_id = $conn->insert_id;

    if ($discount > 0 && isset($_SESSION['applied_promo'])) {
        $promo_code = $conn->real_escape_string($_SESSION['applied_promo']);
        $conn->query("UPDATE promotions SET used_count = used_count + 1 WHERE code = '$promo_code'");
        unset($_SESSION['applied_promo']); unset($_SESSION['discount_value']);
    }

    foreach ($cart_array as $item) {
        $variant = $item['color'] . " - " . $item['size'];
        $conn->query("INSERT INTO order_details (order_id, product_id, variant, quantity, price, image_url) VALUES ($order_id, {$item['id']}, '$variant', {$item['qty']}, {$item['real_price']}, '{$item['image']}')");
        $items_for_mail[] = ['name' => $item['name'], 'variant' => $variant, 'quantity' => $item['qty'], 'price' => $item['real_price'], 'image' => $item['image']];
    }

    $user = $conn->query("SELECT name, email FROM users WHERE id=$uid")->fetch_assoc();

    if ($payment_method == 'QR') { 
        $conn->query("INSERT INTO notifications (user_id, title, message) VALUES ($uid, 'Chờ thanh toán', 'Đơn hàng #$order_id đã được ghi nhận. Vui lòng quét mã QR chuyển khoản để chúng tôi xác nhận đơn hàng.')"); 
    } else { 
        $conn->query("INSERT INTO notifications (user_id, title, message) VALUES ($uid, 'Đặt hàng thành công', 'Đơn hàng #$order_id của bạn đã được xác nhận và đang được chuẩn bị.')"); 
    }

    sendBillEmail($user['email'], $user['name'], $order_id, $items_for_mail, $final_total, $vat);

    // XÓA GIỎ HÀNG TRONG CSDL SAU KHI ĐẶT HÀNG THÀNH CÔNG
    $conn->query("DELETE FROM carts WHERE user_id=$uid");

    $order_success = true; 
    $qr_order_id = $order_id;
}
?>