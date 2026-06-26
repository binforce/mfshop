<?php
ob_start(); // BẬT BỘ ĐỆM ĐỂ XÓA MỌI CẢNH BÁO LỖI NGẦM (GIÚP JSON KHÔNG BỊ HỎNG)
session_start();
require 'db_connect.php';

if (!isset($_POST['code']) || !isset($_POST['total'])) {
    ob_end_clean(); header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ.']); exit();
}

$code = strtoupper(trim($conn->real_escape_string($_POST['code'])));
$total = (float)$_POST['total']; 

$result = $conn->query("SELECT * FROM promotions WHERE code = '$code'");

if ($result && $result->num_rows > 0) {
    $promo = $result->fetch_assoc();
    $now = time();
    $start_time = strtotime($promo['start_date'] ?? '2000-01-01');
    $end_time = strtotime($promo['end_date'] ?? '2030-12-31');
    
    // KIỂM TRA MỐC THỜI GIAN VÀ ĐIỀU KIỆN
    if ($now < $start_time) {
        ob_end_clean(); header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Mã giảm giá chưa đến thời gian áp dụng!']); exit();
    }
    if ($now > $end_time) {
        ob_end_clean(); header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Mã giảm giá đã hết hạn!']); exit();
    }
    if ($promo['used_count'] >= $promo['usage_limit']) {
        ob_end_clean(); header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Mã giảm giá đã hết lượt sử dụng!']); exit();
    }
    if ($total < $promo['min_order']) {
        ob_end_clean(); header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Đơn tối thiểu để áp dụng mã là ' . number_format($promo['min_order']) . 'đ']); exit();
    }

    $discount_value = 0; $msg = "";
    
    if (isset($promo['is_freeship']) && $promo['is_freeship'] == 1) {
        $discount_value = 30000;
        $msg = 'Áp dụng FREESHIP thành công! (Giảm 30.000đ vận chuyển)';
    } else {
        if ($promo['discount_amount'] > 0) { $discount_value = $promo['discount_amount']; } 
        elseif ($promo['discount_percent'] > 0) { $discount_value = $total * ($promo['discount_percent'] / 100); }
        $msg = 'Áp dụng mã thành công! Bạn được giảm ' . number_format($discount_value) . 'đ';
    }

    if ($discount_value > $total) { $discount_value = $total; } // Không giảm lố tiền

    $total_after_discount = $total - $discount_value;
    $vat = $total_after_discount * 0.10;
    $new_total = $total_after_discount + $vat;

    $_SESSION['applied_promo'] = $promo['code'];
    $_SESSION['discount_value'] = $discount_value;

    ob_end_clean(); header('Content-Type: application/json');
    echo json_encode([
        'success' => true, 
        'message' => $msg,
        'discount' => $discount_value,
        'vat' => $vat,
        'new_total' => $new_total
    ]);
} else {
    ob_end_clean(); header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Mã giảm giá không tồn tại hoặc nhập sai!']);
}
?>