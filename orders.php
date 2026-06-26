<?php
session_start();
require 'db_connect.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    $_SESSION['sys_msg'] = ['type' => 'error', 'title' => 'Cảnh báo', 'msg' => 'Vui lòng đăng nhập để xem đơn hàng!'];
    header("Location: login.php");
    exit();
}

$uid = $_SESSION['user_id'];

// XỬ LÝ HÀNH ĐỘNG HỦY ĐƠN HÀNG
if (isset($_GET['action']) && $_GET['action'] == 'cancel' && isset($_GET['id'])) {
    $order_id = (int)$_GET['id'];
    
    // Kiểm tra xem đơn hàng có thuộc về user này không
    $check_query = $conn->query("SELECT status FROM orders WHERE id=$order_id AND user_id=$uid");
    if ($check_query && $check_query->num_rows > 0) {
        $order_status = $check_query->fetch_assoc()['status'];
        if (in_array($order_status, ['Chờ xác nhận thanh toán', 'Đang chuẩn bị hàng'])) {
            // Cập nhật trạng thái
            $conn->query("UPDATE orders SET status='Đã hủy' WHERE id=$order_id AND user_id=$uid");
            
            // BẮN THÔNG BÁO CHO TOÀN BỘ ADMIN (role = 0)
            $msg_admin = "Khách hàng vừa HỦY đơn hàng #$order_id. Vui lòng kiểm tra lại kho.";
            $conn->query("INSERT INTO notifications (user_id, title, message) SELECT id, 'Khách Hủy Đơn', '$msg_admin' FROM users WHERE role = 0");

            $_SESSION['sys_msg'] = ['type' => 'success', 'title' => 'Thành công', 'msg' => 'Đã hủy đơn hàng #'.$order_id.' thành công.'];
        } else {
            $_SESSION['sys_msg'] = ['type' => 'error', 'title' => 'Lỗi', 'msg' => 'Đơn hàng đang giao hoặc đã hoàn thành, không thể hủy!'];
        }
    }
    header("Location: orders.php"); exit();
}

// XỬ LÝ HÀNH ĐỘNG YÊU CẦU HOÀN TRẢ
if (isset($_GET['action']) && $_GET['action'] == 'return' && isset($_GET['id'])) {
    $order_id = (int)$_GET['id'];
    $check_query = $conn->query("SELECT status FROM orders WHERE id=$order_id AND user_id=$uid");
    if ($check_query && $check_query->num_rows > 0) {
        $order_status = $check_query->fetch_assoc()['status'];
        if ($order_status == 'Hoàn thành') {
            // Cập nhật trạng thái
            $conn->query("UPDATE orders SET status='Yêu cầu hoàn trả' WHERE id=$order_id AND user_id=$uid");
            
            // BẮN THÔNG BÁO CHO TOÀN BỘ ADMIN (role = 0)
            $msg_admin = "Đơn hàng #$order_id có yêu cầu HOÀN TRẢ HÀNG từ khách. Vui lòng xem xét giải quyết!";
            $conn->query("INSERT INTO notifications (user_id, title, message) SELECT id, 'Yêu Cầu Hoàn Trả', '$msg_admin' FROM users WHERE role = 0");

            $_SESSION['sys_msg'] = ['type' => 'success', 'title' => 'Đã gửi yêu cầu', 'msg' => 'Yêu cầu hoàn trả đơn #'.$order_id.' đã được gửi tới quản trị viên.'];
        }
    }
    header("Location: orders.php"); exit();
}

// Lấy danh sách đơn hàng của khách
$orders_query = $conn->query("SELECT * FROM orders WHERE user_id=$uid ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Đơn Hàng Của Tôi - MF SHOP</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css?v=<?= time() ?>">
    <style>
        .order-card { background: #fff; border: 1px solid #e1e8ed; border-radius: 8px; margin-bottom: 20px; overflow: hidden; box-shadow: 0 2px 5px rgba(0,0,0,0.02); }
        .order-header { background: #f8f9fa; padding: 15px 20px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e1e8ed; font-size: 15px; }
        .order-body { padding: 20px; }
        .order-footer { background: #fafafa; padding: 15px 20px; border-top: 1px dashed #e1e8ed; display: flex; justify-content: flex-end; align-items: center; gap: 15px; }
        
        .item-row { display: flex; align-items: center; gap: 15px; margin-bottom: 15px; padding-bottom: 15px; border-bottom: 1px solid #f1f1f1; }
        .item-row:last-child { margin-bottom: 0; padding-bottom: 0; border-bottom: none; }
        .item-img { width: 80px; height: 80px; object-fit: cover; border-radius: 4px; border: 1px solid #eee; }
        .item-info { flex: 1; }
        .item-price { font-weight: bold; color: var(--primary-color); }
        
        .status-badge { padding: 5px 12px; border-radius: 20px; font-size: 13px; font-weight: bold; color: white; display: inline-flex; align-items: center; gap: 5px; }
        .st-cho-xac-nhan { background: #f39c12; }
        .st-dang-chuan-bi { background: #3498db; }
        .st-dang-giao { background: #9b59b6; }
        .st-hoan-thanh { background: #2ecc71; }
        .st-da-huy { background: #e74c3c; }
        .st-yeu-cau-hoan { background: #e67e22; }
    </style>
</head>
<body style="background: #f4f6f9;">
    <div class="toolbar">
        <div class="logo"><a href="index.php" style="display: flex; align-items: center; gap: 8px; font-size: 26px; font-weight: 900; color: var(--primary-color); text-decoration: none;"><i class="fa fa-shopping-bag"></i> MF SHOP</a></div>
        <div style="font-weight: bold;"><a href="index.php" style="margin-right: 20px; color: var(--text-main);"><i class="fa fa-home"></i> Về Trang Chủ</a><a href="cart.php" style="color: var(--secondary-color);"><i class="fa fa-shopping-cart"></i> Giỏ Hàng</a></div>
    </div>

    <div class="main-container" style="max-width: 900px; margin: 40px auto; min-height: 60vh; display: block;">
        <h2 style="margin-top:0; border-bottom: 2px solid var(--primary-color); padding-bottom: 10px; display: block;"><i class="fa fa-clipboard-list"></i> Theo Dõi Đơn Hàng Của Tôi</h2>
        
        <?php if ($orders_query && $orders_query->num_rows > 0): ?>
            <?php while($order = $orders_query->fetch_assoc()): 
                $oid = $order['id']; $status = $order['status'];
                
                $badge_class = 'st-cho-xac-nhan'; $icon = 'fa-clock';
                if($status == 'Đang chuẩn bị hàng') { $badge_class = 'st-dang-chuan-bi'; $icon = 'fa-box-open'; }
                elseif($status == 'Đang giao hàng') { $badge_class = 'st-dang-giao'; $icon = 'fa-shipping-fast'; }
                elseif($status == 'Hoàn thành') { $badge_class = 'st-hoan-thanh'; $icon = 'fa-check-circle'; }
                elseif($status == 'Đã hủy') { $badge_class = 'st-da-huy'; $icon = 'fa-times-circle'; }
                elseif($status == 'Yêu cầu hoàn trả' || $status == 'Đã hoàn trả') { $badge_class = 'st-yeu-cau-hoan'; $icon = 'fa-undo'; }
            ?>
                <div class="order-card">
                    <div class="order-header">
                        <div><b>Mã ĐH: #<?= $oid ?></b> <span style="color: #7f8c8d; font-size: 13px; margin-left: 10px;"><i class="fa fa-calendar-alt"></i> Đặt lúc: <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></span></div>
                        <span class="status-badge <?= $badge_class ?>"><i class="fa <?= $icon ?>"></i> <?= $status ?></span>
                    </div>
                    
                    <div class="order-body">
                        <?php 
                        $items = $conn->query("SELECT p.name, od.variant, od.quantity, od.price, od.image_url FROM order_details od JOIN products p ON od.product_id = p.id WHERE od.order_id = $oid");
                        while($item = $items->fetch_assoc()): 
                        ?>
                            <div class="item-row">
                                <img src="<?= $item['image_url'] ?>" class="item-img" onerror="this.src='https://via.placeholder.com/80';">
                                <div class="item-info">
                                    <h4 style="margin: 0 0 5px 0; color: #2c3e50;"><?= htmlspecialchars($item['name']) ?></h4>
                                    <p style="margin: 0; color: #7f8c8d; font-size: 13px;">Phân loại: <?= explode(' - ', $item['variant'])[1] ?? 'Mặc định' ?> | Số lượng: x<?= $item['quantity'] ?></p>
                                </div>
                                <div class="item-price"><?= number_format($item['price']) ?> đ</div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                    
                    <div class="order-footer">
                        <div style="font-size: 16px;">Tổng thanh toán: <b style="color: var(--primary-color); font-size: 20px;"><?= number_format($order['total_amount'] + $order['vat_amount']) ?> đ</b></div>
                        
                        <?php if (in_array($status, ['Chờ xác nhận thanh toán', 'Đang chuẩn bị hàng'])): ?>
                            <a href="orders.php?action=cancel&id=<?= $oid ?>" onclick="return confirm('Bạn có chắc chắn muốn hủy đơn hàng này không?');" class="btn" style="background: #e74c3c; border-color: #e74c3c; padding: 8px 20px;"><i class="fa fa-times"></i> Hủy Đơn</a>
                        <?php elseif ($status == 'Hoàn thành'): ?>
                            <a href="orders.php?action=return&id=<?= $oid ?>" onclick="return confirm('Bạn muốn yêu cầu hoàn trả hàng cho đơn này? Bộ phận CSKH sẽ liên hệ với bạn.');" class="btn btn-secondary" style="background: #f39c12; border-color: #f39c12; padding: 8px 20px;"><i class="fa fa-undo"></i> Yêu cầu hoàn trả</a>
                        <?php elseif ($status == 'Đang giao hàng'): ?>
                            <button class="btn" style="background: #95a5a6; cursor: not-allowed;" disabled><i class="fa fa-truck"></i> Shipper đang giao</button>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div style="text-align: center; padding: 50px 20px; background: white; border-radius: 8px; border: 1px dashed #ccc;">
                <i class="fa fa-box-open" style="font-size: 60px; color: #bdc3c7; margin-bottom: 20px;"></i>
                <h3 style="color: #7f8c8d;">Bạn chưa có đơn hàng nào!</h3>
                <a href="index.php" class="btn btn-primary" style="margin-top: 15px;"><i class="fa fa-shopping-cart"></i> Mua sắm ngay</a>
            </div>
        <?php endif; ?>
    </div>

    <div id="systemModal" class="modal">
        <div class="modal-content" style="width:320px; text-align:center;">
            <span class="close-btn" onclick="document.getElementById('systemModal').style.display='none'">&times;</span>
            <i id="modalIcon" class="fa modal-icon"></i><h3 id="modalTitle" style="margin: 10px 0 5px 0;"></h3><p id="modalMessage" style="color:var(--text-muted); font-size:14px; margin-bottom:20px;"></p><button onclick="document.getElementById('systemModal').style.display='none'" class="btn btn-secondary" style="width:100%;">Đóng lại</button>
        </div>
    </div>
    <script>
        function showSysModal(type, title, message) {
            document.getElementById('systemModal').style.display = 'flex';
            document.getElementById('modalTitle').innerText = title; document.getElementById('modalMessage').innerText = message;
            document.getElementById('modalIcon').className = type === 'success' ? 'fa fa-check-circle modal-icon icon-success' : 'fa fa-exclamation-circle modal-icon icon-error';
        }
    </script>
    <?php if (isset($_SESSION['sys_msg'])): ?>
        <script>showSysModal('<?= $_SESSION['sys_msg']['type'] ?>', '<?= $_SESSION['sys_msg']['title'] ?>', '<?= $_SESSION['sys_msg']['msg'] ?>');</script>
    <?php unset($_SESSION['sys_msg']); endif; ?>
</body>
</html>