<?php
require 'db_connect.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 0) { exit(); }

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['order_id'])) {
    $order_id = (int)$_POST['order_id'];
    $new_status = $_POST['status'];

    // Cập nhật trạng thái đơn vào Database
    $conn->query("UPDATE orders SET status='$new_status' WHERE id=$order_id");

    // Lấy ID khách hàng để bắn thông báo nội bộ
    $order = $conn->query("SELECT user_id FROM orders WHERE id=$order_id")->fetch_assoc();
    $uid = $order['user_id'];
    
    $title = "Cập nhật đơn hàng #$order_id";
    $msg = "Đơn hàng của bạn đã được chuyển sang trạng thái: **$new_status**.";
    
    // NẾU ADMIN ĐỔI THÀNH "ĐANG CHUẨN BỊ HÀNG" NGHĨA LÀ ĐÃ XÁC NHẬN TIỀN VÀO
    if ($new_status == 'Đang chuẩn bị hàng') {
        $msg = "Thanh toán cho đơn hàng #$order_id đã được xác nhận. Đặt hàng thành công! Chúng tôi đang chuẩn bị hàng cho bạn.";
        $title = "Xác nhận thanh toán thành công";
    } elseif ($new_status == 'Đang giao hàng') {
        $msg = "Đơn hàng #$order_id của bạn đã được giao cho đơn vị vận chuyển.";
        $title = "Đơn hàng đang giao";
    }
    
    $conn->query("INSERT INTO notifications (user_id, title, message) VALUES ($uid, '$title', '$msg')");

    $_SESSION['sys_msg'] = ['type' => 'success', 'title' => 'Cập nhật', 'msg' => 'Đã cập nhật trạng thái đơn hàng!'];
    header("Location: admin.php?action=orders");
    exit();
}
?>