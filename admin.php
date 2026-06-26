<?php
require 'db_connect.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 0) { header("Location: login.php"); exit(); }

// 1. TỰ ĐỘNG KHỞI TẠO BẢNG CHƯƠNG TRÌNH KHUYẾN MÃI
$conn->query("CREATE TABLE IF NOT EXISTS promotions ( id INT AUTO_INCREMENT PRIMARY KEY, code VARCHAR(50) NOT NULL UNIQUE, discount_amount INT DEFAULT 0, discount_percent INT DEFAULT 0, is_freeship TINYINT DEFAULT 0, min_order INT DEFAULT 0, usage_limit INT DEFAULT 100, used_count INT DEFAULT 0, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP )");
try { $conn->query("ALTER TABLE promotions ADD COLUMN is_freeship TINYINT DEFAULT 0"); } catch(Exception $e){} try { $conn->query("ALTER TABLE promotions ADD COLUMN start_date DATETIME DEFAULT CURRENT_TIMESTAMP"); } catch(Exception $e){} try { $conn->query("ALTER TABLE promotions ADD COLUMN end_date DATETIME DEFAULT '2030-12-31 23:59:59'"); } catch(Exception $e){}
$conn->query("CREATE TABLE IF NOT EXISTS campaigns ( id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(255) NOT NULL, discount_percent INT DEFAULT 0, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP )");
try { $conn->query("ALTER TABLE campaigns ADD COLUMN start_date DATETIME DEFAULT CURRENT_TIMESTAMP"); } catch(Exception $e){} try { $conn->query("ALTER TABLE campaigns ADD COLUMN end_date DATETIME DEFAULT '2030-12-31 23:59:59'"); } catch(Exception $e){} try { $conn->query("ALTER TABLE products ADD COLUMN campaign_id INT DEFAULT NULL"); } catch(Exception $e){} try { $conn->query("ALTER TABLE products ADD COLUMN sale_price INT DEFAULT 0"); } catch(Exception $e){}

$action = isset($_GET['action']) ? $_GET['action'] : 'dashboard';
$tab = isset($_GET['tab']) ? $_GET['tab'] : 'vouchers'; 
$search = isset($_GET['search']) ? trim($_GET['search']) : ''; 
$escaped_search = $conn->real_escape_string($search);

// ==========================================
// HÀNH ĐỘNG XỬ LÝ CRUD KHUYẾN MÃI & CHIẾN DỊCH
// ==========================================
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_promo'])) {
    $code = strtoupper(trim($conn->real_escape_string($_POST['code']))); $amount = (int)$_POST['discount_amount']; $percent = (int)$_POST['discount_percent'];
    $is_free = isset($_POST['is_freeship']) ? 1 : 0; $min_order = (int)$_POST['min_order']; $limit = (int)$_POST['usage_limit']; $start = $conn->real_escape_string($_POST['start_date']); $end = $conn->real_escape_string($_POST['end_date']);
    try { $conn->query("INSERT INTO promotions (code, discount_amount, discount_percent, is_freeship, min_order, usage_limit, start_date, end_date) VALUES ('$code', $amount, $percent, $is_free, $min_order, $limit, '$start', '$end')"); $_SESSION['sys_msg'] = ['type'=>'success','title'=>'Thành công','msg'=>'Đã phát hành mã mới!']; } catch(Exception $e) { $_SESSION['sys_msg'] = ['type'=>'error','title'=>'Lỗi','msg'=>'Mã giảm giá bị trùng!']; }
    header("Location: admin.php?action=promotions&tab=vouchers"); exit();
}
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_promo'])) {
    $id = (int)$_POST['promo_id']; $code = strtoupper(trim($conn->real_escape_string($_POST['code']))); $amount = (int)$_POST['discount_amount']; $percent = (int)$_POST['discount_percent']; $is_free = isset($_POST['is_freeship']) ? 1 : 0; $min_order = (int)$_POST['min_order']; $limit = (int)$_POST['usage_limit']; $start = $conn->real_escape_string($_POST['start_date']); $end = $conn->real_escape_string($_POST['end_date']);
    try { $conn->query("UPDATE promotions SET code='$code', discount_amount=$amount, discount_percent=$percent, is_freeship=$is_free, min_order=$min_order, usage_limit=$limit, start_date='$start', end_date='$end' WHERE id=$id"); $_SESSION['sys_msg'] = ['type'=>'success','title'=>'Thành công','msg'=>'Cập nhật thành công!']; } catch(Exception $e) { $_SESSION['sys_msg'] = ['type'=>'error','title'=>'Lỗi','msg'=>'Mã Code bị trùng lặp!']; }
    header("Location: admin.php?action=promotions&tab=vouchers"); exit();
}
if (isset($_GET['delete_promo'])) { $id = (int)$_GET['delete_promo']; $conn->query("DELETE FROM promotions WHERE id=$id"); $_SESSION['sys_msg'] = ['type'=>'success','title'=>'Đã xóa','msg'=>'Xóa mã thành công!']; header("Location: admin.php?action=promotions&tab=vouchers"); exit(); }
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_campaign'])) {
    $name = $conn->real_escape_string($_POST['campaign_name']); $percent = (int)$_POST['discount_percent']; $start = $conn->real_escape_string($_POST['start_date']); $end = $conn->real_escape_string($_POST['end_date']);
    $conn->query("INSERT INTO campaigns (name, discount_percent, start_date, end_date) VALUES ('$name', $percent, '$start', '$end')"); $campaign_id = $conn->insert_id;
    if (!empty($_POST['campaign_products'])) { foreach ($_POST['campaign_products'] as $p_id) { $conn->query("UPDATE products SET campaign_id = $campaign_id, sale_price = price - (price * $percent / 100) WHERE id = ".(int)$p_id); } }
    $_SESSION['sys_msg'] = ['type'=>'success','title'=>'Thành công','msg'=>'Đã áp dụng chiến dịch khuyến mãi!']; header("Location: admin.php?action=promotions&tab=campaigns"); exit();
}
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_campaign'])) {
    $c_id = (int)$_POST['campaign_id']; $name = $conn->real_escape_string($_POST['campaign_name']); $percent = (int)$_POST['discount_percent']; $start = $conn->real_escape_string($_POST['start_date']); $end = $conn->real_escape_string($_POST['end_date']);
    $conn->query("UPDATE campaigns SET name='$name', discount_percent=$percent, start_date='$start', end_date='$end' WHERE id=$c_id"); $conn->query("UPDATE products SET campaign_id = NULL, sale_price = 0 WHERE campaign_id = $c_id");
    if (!empty($_POST['campaign_products'])) { foreach ($_POST['campaign_products'] as $p_id) { $conn->query("UPDATE products SET campaign_id = $c_id, sale_price = price - (price * $percent / 100) WHERE id = ".(int)$p_id); } }
    $_SESSION['sys_msg'] = ['type'=>'success','title'=>'Thành công','msg'=>'Cập nhật chương trình thành công!']; header("Location: admin.php?action=promotions&tab=campaigns"); exit();
}
if (isset($_GET['delete_campaign'])) {
    $id = (int)$_GET['delete_campaign']; $conn->query("UPDATE products SET campaign_id = NULL, sale_price = 0 WHERE campaign_id = $id"); $conn->query("DELETE FROM campaigns WHERE id = $id");
    $_SESSION['sys_msg'] = ['type'=>'success','title'=>'Đã xóa','msg'=>'Đã kết thúc chiến dịch khuyến mãi!']; header("Location: admin.php?action=promotions&tab=campaigns"); exit();
}

// XÓA ĐƠN, KHÁCH, DANH MỤC
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['bulk_delete_products'])) {
    if (!empty($_POST['selected_products'])) {
        $success_count = 0; $error_count = 0; foreach ($_POST['selected_products'] as $id) { try { $conn->query("DELETE FROM products WHERE id=".(int)$id); $success_count++; } catch (Exception $e) { $error_count++; } }
        $msg = "Đã xóa thành công $success_count sản phẩm."; if ($error_count > 0) $msg .= " Bỏ qua $error_count sản phẩm do đã có đơn hàng.";
        $_SESSION['sys_msg'] = ['type' => ($error_count > 0 ? 'error' : 'success'), 'title' => 'Xóa hàng loạt', 'msg' => $msg];
    } else { $_SESSION['sys_msg'] = ['type' => 'error', 'title' => 'Lỗi', 'msg' => 'Vui lòng chọn sản phẩm!']; }
    header("Location: admin.php?action=products"); exit();
}
if (isset($_GET['delete_product'])) { $id = (int)$_GET['delete_product']; try { $conn->query("DELETE FROM products WHERE id=$id"); $_SESSION['sys_msg'] = ['type' => 'success', 'title' => 'Thành công', 'msg' => 'Đã xóa sản phẩm!']; } catch (Exception $e) { $_SESSION['sys_msg'] = ['type' => 'error', 'title' => 'Không thể xóa', 'msg' => 'Sản phẩm này đã nằm trong đơn hàng.']; } header("Location: admin.php?action=products"); exit(); }
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['apply_bulk_action'])) {
    if (!empty($_POST['selected_products']) && !empty($_POST['bulk_action'])) {
        $action_type = $_POST['bulk_action']; $val = (int)$_POST['discount_value']; $ids = implode(',', array_map('intval', $_POST['selected_products']));
        if ($action_type == 'discount_percent') { $conn->query("UPDATE products SET sale_price = price - (price * $val / 100) WHERE id IN ($ids)"); }
        elseif ($action_type == 'discount_amount') { $conn->query("UPDATE products SET sale_price = GREATEST(price - $val, 0) WHERE id IN ($ids)"); }
        elseif ($action_type == 'reset_discount') { $conn->query("UPDATE products SET sale_price = 0, campaign_id = NULL WHERE id IN ($ids)"); }
        elseif ($action_type == 'delete') { foreach ($_POST['selected_products'] as $id) { try{$conn->query("DELETE FROM products WHERE id=".(int)$id);}catch(Exception $e){} } }
        $_SESSION['sys_msg'] = ['type' => 'success', 'title' => 'Thành công', 'msg' => 'Thực thi hành động hàng loạt thành công!'];
    }
    header("Location: admin.php?action=products"); exit();
}
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_category'])) { $c_id = (int)$_POST['cat_id']; $c_name = $conn->real_escape_string($_POST['cat_name']); $conn->query("UPDATE categories SET name='$c_name' WHERE id=$c_id"); $_SESSION['sys_msg'] = ['type' => 'success', 'title' => 'Thành công', 'msg' => 'Cập nhật danh mục thành công!']; header("Location: admin.php?action=categories"); exit(); }
if (isset($_GET['delete_category'])) { $id = (int)$_GET['delete_category']; $conn->query("UPDATE products SET category_id=NULL WHERE category_id=$id"); $conn->query("DELETE FROM categories WHERE id=$id"); $_SESSION['sys_msg'] = ['type' => 'success', 'title' => 'Thành công', 'msg' => 'Đã xóa danh mục!']; header("Location: admin.php?action=categories"); exit(); }
if (isset($_GET['delete_order'])) { $id = (int)$_GET['delete_order']; $conn->query("DELETE FROM order_details WHERE order_id=$id"); $conn->query("DELETE FROM orders WHERE id=$id"); $_SESSION['sys_msg'] = ['type' => 'success', 'title' => 'Thành công', 'msg' => 'Đã xóa đơn hàng vĩnh viễn!']; header("Location: admin.php?action=orders"); exit(); }
if (isset($_GET['delete_customer'])) { $id = (int)$_GET['delete_customer']; try { $conn->query("DELETE FROM notifications WHERE user_id=$id"); $conn->query("DELETE FROM users WHERE id=$id"); $_SESSION['sys_msg'] = ['type' => 'success', 'title' => 'Thành công', 'msg' => 'Đã xóa khách hàng!']; } catch (Exception $e) { $_SESSION['sys_msg'] = ['type' => 'error', 'title' => 'Không thể xóa', 'msg' => 'Khách hàng này đã có lịch sử mua.']; } header("Location: admin.php?action=customers"); exit(); }

// ==========================================
// TÍNH TOÁN DỮ LIỆU CHUNG & BADGE THÔNG BÁO ADMIN
// ==========================================
// Bắt xem có bao nhiêu đơn hàng đang "Yêu cầu hoàn trả" để báo Notification đỏ
$admin_noti_count = $conn->query("SELECT COUNT(*) as c FROM orders WHERE status = 'Yêu cầu hoàn trả'")->fetch_assoc()['c'] ?? 0;

if ($action == 'dashboard') {
    $filter = isset($_GET['filter']) ? $_GET['filter'] : 'day'; $where_clauses = ["status = 'Hoàn thành'"];
    $bounds = $conn->query("SELECT MIN(created_at) as min_d, MAX(created_at) as max_d FROM orders WHERE status='Hoàn thành'")->fetch_assoc();
    $min_time = $bounds['min_d'] ? strtotime($bounds['min_d']) : time(); $max_time = $bounds['max_d'] ? strtotime($bounds['max_d']) : time();

    $spec_hour = isset($_GET['spec_hour']) ? $_GET['spec_hour'] : date('Y-m-d\TH:00'); $from_hour = isset($_GET['from_hour']) ? $_GET['from_hour'] : date('Y-m-d\T00:00', strtotime('-1 day')); $to_hour = isset($_GET['to_hour']) ? $_GET['to_hour'] : date('Y-m-d\T23:00');
    $spec_date = isset($_GET['spec_date']) ? $_GET['spec_date'] : date('Y-m-d'); $from_date = isset($_GET['from_date']) ? $_GET['from_date'] : date('Y-m-d', strtotime('-15 days')); $to_date = isset($_GET['to_date']) ? $_GET['to_date'] : date('Y-m-d');
    $spec_week = isset($_GET['spec_week']) ? $_GET['spec_week'] : date('Y-\WW'); $from_week = isset($_GET['from_week']) ? $_GET['from_week'] : date('Y-\W', strtotime('-10 weeks')).date('W', strtotime('-10 weeks')); $to_week = isset($_GET['to_week']) ? $_GET['to_week'] : date('Y-\WW');
    $spec_month = isset($_GET['spec_month']) ? $_GET['spec_month'] : date('Y-m'); $from_month = isset($_GET['from_month']) ? $_GET['from_month'] : date('Y-m', strtotime('-6 months')); $to_month = isset($_GET['to_month']) ? $_GET['to_month'] : date('Y-m');
    $spec_q_year = isset($_GET['spec_q_year']) ? (int)$_GET['spec_q_year'] : date('Y'); $spec_quarter = isset($_GET['spec_quarter']) ? (int)$_GET['spec_quarter'] : ceil(date('n')/3); $from_q_year = isset($_GET['from_q_year']) ? (int)$_GET['from_q_year'] : date('Y') - 1; $from_quarter = isset($_GET['from_quarter']) ? (int)$_GET['from_quarter'] : 1; $to_q_year = isset($_GET['to_q_year']) ? (int)$_GET['to_q_year'] : date('Y'); $to_quarter = isset($_GET['to_quarter']) ? (int)$_GET['to_quarter'] : ceil(date('n')/3);
    $spec_year = isset($_GET['spec_year']) ? (int)$_GET['spec_year'] : date('Y'); $from_year = isset($_GET['from_year']) ? (int)$_GET['from_year'] : date('Y') - 5; $to_year = isset($_GET['to_year']) ? (int)$_GET['to_year'] : date('Y');
    $spec_century = isset($_GET['spec_century']) ? (int)$_GET['spec_century'] : ceil(date('Y')/100); $from_century = isset($_GET['from_century']) ? (int)$_GET['from_century'] : ceil(date('Y')/100) - 2; $to_century = isset($_GET['to_century']) ? (int)$_GET['to_century'] : ceil(date('Y')/100);

    $slots = []; $select_expr = ""; $today_time = time();
    if ($filter == 'hour') { $base_date = date('Y-m-d', $max_time); for($h = 0; $h <= 23; $h++) { $slots[sprintf("%02d:00", $h)] = ['revenue'=>0, 'orders'=>0]; } $where_clauses[] = "DATE(created_at) = '$base_date'"; $select_expr = "DATE_FORMAT(created_at, '%H:00')"; }
    elseif ($filter == 'spec_hour') { $h_str = substr(str_replace('T', ' ', $spec_hour), 0, 13); $st = strtotime("$h_str:00:00"); for($t = $st; $t < $st + 3600; $t += 300) { $slots[date("H:i", $t) . 'p'] = ['revenue'=>0, 'orders'=>0]; } $where_clauses[] = "DATE_FORMAT(created_at, '%Y-%m-%d %H') = '$h_str'"; $select_expr = "CONCAT(DATE_FORMAT(created_at, '%H:'), LPAD(FLOOR(MINUTE(created_at)/5)*5, 2, '0'), 'p')"; }
    elseif ($filter == 'range_hour') { $fh = str_replace('T', ' ', $from_hour); $th = str_replace('T', ' ', $to_hour); for($t = strtotime("$fh:00:00"); $t <= strtotime("$th:59:59"); $t += 3600) { $slots[date("H:00 d/m", $t)] = ['revenue'=>0, 'orders'=>0]; } $where_clauses[] = "created_at BETWEEN '$fh:00:00' AND '$th:59:59'"; $select_expr = "DATE_FORMAT(created_at, '%H:00 %d/%m')"; }
    elseif ($filter == 'day') { $st = strtotime(date('Y-m-d 00:00:00', strtotime("-29 days"))); for($t = $st; $t <= $today_time; $t += 86400) { $slots[date("d/m/Y", $t)] = ['revenue'=>0, 'orders'=>0]; } $where_clauses[] = "created_at >= '" . date('Y-m-d 00:00:00', $st) . "'"; $select_expr = "DATE_FORMAT(created_at, '%d/%m/%Y')"; }
    elseif ($filter == 'spec_day') { for($t = strtotime("$spec_date 00:00:00"); $t <= strtotime("$spec_date 23:59:59"); $t += 3600) { $slots[date("H:00", $t)] = ['revenue'=>0, 'orders'=>0]; } $where_clauses[] = "DATE(created_at) = '$spec_date'"; $select_expr = "DATE_FORMAT(created_at, '%H:00')"; }
    elseif ($filter == 'range_day') { for($t = strtotime("$from_date 00:00:00"); $t <= strtotime("$to_date 23:59:59"); $t += 86400) { $slots[date("d/m/Y", $t)] = ['revenue'=>0, 'orders'=>0]; } $where_clauses[] = "DATE(created_at) BETWEEN '$from_date' AND '$to_date'"; $select_expr = "DATE_FORMAT(created_at, '%d/%m/%Y')"; }
    elseif ($filter == 'week') { $st = strtotime("monday this week", strtotime("-11 weeks")); for($t = $st; $t <= $today_time; $t = strtotime("+1 week", $t)) { $slots[date("Y", $t).'-T'.sprintf("%02d", date("W", $t))] = ['revenue'=>0, 'orders'=>0]; } $where_clauses[] = "created_at >= '" . date('Y-m-d 00:00:00', $st) . "'"; $select_expr = "CONCAT(YEAR(created_at), '-T', LPAD(WEEK(created_at, 1), 2, '0'))"; }
    elseif ($filter == 'spec_week') { $w_y = substr($spec_week, 0, 4); $w_n = substr($spec_week, 6, 2); $st = strtotime("{$w_y}W{$w_n}"); for($i=0; $i<7; $i++) { $slots[date("d/m", strtotime("+$i days", $st))] = ['revenue'=>0, 'orders'=>0]; } $where_clauses[] = "YEARWEEK(created_at, 3) = '{$w_y}{$w_n}'"; $select_expr = "DATE_FORMAT(created_at, '%d/%m')"; }
    elseif ($filter == 'range_week') { $fw_y = substr($from_week, 0, 4); $fw_n = substr($from_week, 6, 2); $tw_y = substr($to_week, 0, 4); $tw_n = substr($to_week, 6, 2); for($t = strtotime("{$fw_y}W{$fw_n}"); $t <= strtotime("{$tw_y}W{$tw_n}"); $t = strtotime("+1 week", $t)) { $slots[date("Y", $t).'-T'.sprintf("%02d", date("W", $t))] = ['revenue'=>0, 'orders'=>0]; } $where_clauses[] = "YEARWEEK(created_at, 3) BETWEEN '{$fw_y}{$fw_n}' AND '{$tw_y}{$tw_n}'"; $select_expr = "CONCAT(YEAR(created_at), '-T', LPAD(WEEK(created_at, 1), 2, '0'))"; }
    elseif ($filter == 'month') { $st = strtotime(date('Y-m-01', strtotime("-11 months"))); for($t = $st; $t <= $today_time; $t = strtotime("+1 month", $t)) { $slots[date("m/Y", $t)] = ['revenue'=>0, 'orders'=>0]; } $where_clauses[] = "created_at >= '" . date('Y-m-d 00:00:00', $st) . "'"; $select_expr = "DATE_FORMAT(created_at, '%m/%Y')"; }
    elseif ($filter == 'spec_month') { $days = date('t', strtotime("$spec_month-01")); for($d=1; $d<=$days; $d++) { $slots["Ngày " . sprintf("%02d", $d)] = ['revenue'=>0, 'orders'=>0]; } $where_clauses[] = "DATE_FORMAT(created_at, '%Y-%m') = '$spec_month'"; $select_expr = "DATE_FORMAT(created_at, 'Ngày %d')"; }
    elseif ($filter == 'range_month') { for($t = strtotime("$from_month-01"); $t <= strtotime(date('Y-m-t', strtotime("$to_month-01"))); $t = strtotime("+1 month", $t)) { $slots[date("m/Y", $t)] = ['revenue'=>0, 'orders'=>0]; } $where_clauses[] = "DATE_FORMAT(created_at, '%Y-%m') BETWEEN '$from_month' AND '$to_month'"; $select_expr = "DATE_FORMAT(created_at, '%m/%Y')"; }
    elseif ($filter == 'quarter') { $q_now = ceil(date('n')/3); $y_now = date('Y'); for($i = 11; $i >= 0; $i--) { $q = $q_now - $i; $y = $y_now; while($q <= 0) { $q += 4; $y--; } $slots["Q$q/$y"] = ['revenue'=>0, 'orders'=>0]; } $where_clauses[] = "created_at >= '" . ($y_now - 3) . "-01-01'"; $select_expr = "CONCAT('Q', QUARTER(created_at), '/', YEAR(created_at))"; }
    elseif ($filter == 'spec_quarter') { for($m=1; $m<=3; $m++) { $slots["Tháng " . sprintf("%02d", ($spec_quarter-1)*3+$m)] = ['revenue'=>0, 'orders'=>0]; } $where_clauses[] = "YEAR(created_at) = $spec_q_year AND QUARTER(created_at) = $spec_quarter"; $select_expr = "DATE_FORMAT(created_at, 'Tháng %m')"; }
    elseif ($filter == 'range_quarter') { for($y = $from_q_year; $y <= $to_q_year; $y++) { $start_q = ($y == $from_q_year) ? $from_quarter : 1; $end_q = ($y == $to_q_year) ? $to_quarter : 4; for($q = $start_q; $q <= $end_q; $q++) { $slots["Q$q/$y"] = ['revenue'=>0, 'orders'=>0]; } } $where_clauses[] = "CONCAT(YEAR(created_at), QUARTER(created_at)) BETWEEN '{$from_q_year}{$from_quarter}' AND '{$to_q_year}{$to_quarter}'"; $select_expr = "CONCAT('Q', QUARTER(created_at), '/', YEAR(created_at))"; }
    elseif ($filter == 'year') { for($y = date('Y') - 9; $y <= date('Y'); $y++) { $slots[(string)$y] = ['revenue'=>0, 'orders'=>0]; } $where_clauses[] = "YEAR(created_at) >= " . (date('Y') - 9); $select_expr = "DATE_FORMAT(created_at, '%Y')"; }
    elseif ($filter == 'spec_year') { for($m=1; $m<=12; $m++) { $slots["Tháng " . sprintf("%02d", $m)] = ['revenue'=>0, 'orders'=>0]; } $where_clauses[] = "YEAR(created_at) = $spec_year"; $select_expr = "DATE_FORMAT(created_at, 'Tháng %m')"; }
    elseif ($filter == 'range_year') { for($y = $from_year; $y <= $to_year; $y++) { $slots[(string)$y] = ['revenue'=>0, 'orders'=>0]; } $where_clauses[] = "YEAR(created_at) BETWEEN $from_year AND $to_year"; $select_expr = "DATE_FORMAT(created_at, '%Y')"; }
    elseif ($filter == 'century') { $c = ceil(date('Y')/100); $slots["TK " . ($c-1)] = ['revenue'=>0, 'orders'=>0]; $slots["TK " . $c] = ['revenue'=>0, 'orders'=>0]; $select_expr = "CONCAT('TK ', CEIL(YEAR(created_at) / 100))"; }
    elseif ($filter == 'spec_century') { $st_y = ($spec_century - 1) * 100 + 1; for($y = $st_y; $y < $st_y + 100; $y+=10) { $slots["Năm $y -> " . ($y+9)] = ['revenue'=>0, 'orders'=>0]; } $where_clauses[] = "CEIL(YEAR(created_at)/100) = $spec_century"; $select_expr = "CONCAT('Năm ', FLOOR(YEAR(created_at)/10)*10 + 1, ' -> ', FLOOR(YEAR(created_at)/10)*10 + 10)"; }
    elseif ($filter == 'range_century') { for($c = $from_century; $c <= $to_century; $c++) { $slots["TK " . $c] = ['revenue'=>0, 'orders'=>0]; } $where_clauses[] = "CEIL(YEAR(created_at)/100) BETWEEN $from_century AND $to_century"; $select_expr = "CONCAT('TK ', CEIL(YEAR(created_at) / 100))"; }

    $where_clause = "WHERE " . implode(" AND ", $where_clauses);
    $chart_query = "SELECT $select_expr AS date_val, SUM(total_amount) AS revenue, COUNT(id) AS order_count FROM orders $where_clause GROUP BY date_val";
    $chart_result = $conn->query($chart_query);
    if ($chart_result) { while($row = $chart_result->fetch_assoc()) { $dv = trim($row['date_val']); if (isset($slots[$dv])) { $slots[$dv]['revenue'] = (float)$row['revenue']; $slots[$dv]['orders'] = (int)$row['order_count']; } } }

    $dates = array_keys($slots); $revenues = array_column($slots, 'revenue'); $order_counts = array_column($slots, 'orders');
    $filtered_total_revenue = array_sum($revenues); $filtered_total_orders = array_sum($order_counts);
    $total_revenue_overall = $conn->query("SELECT SUM(total_amount) as total FROM orders WHERE status='Hoàn thành'")->fetch_assoc()['total'] ?? 0;
    $total_users = $conn->query("SELECT COUNT(*) as c FROM users WHERE role=1")->fetch_assoc()['c'] ?? 0;
    $best_seller_query = $conn->query("SELECT p.name, SUM(od.quantity) as qty FROM order_details od JOIN products p ON od.product_id = p.id JOIN orders o ON od.order_id = o.id WHERE o.status = 'Hoàn thành' GROUP BY p.id ORDER BY qty DESC LIMIT 3");
    $worst_seller_query = $conn->query("SELECT p.name, COALESCE(SUM(od.quantity), 0) as qty FROM products p LEFT JOIN order_details od ON p.id = od.product_id LEFT JOIN orders o ON od.order_id = o.id AND o.status = 'Hoàn thành' GROUP BY p.id ORDER BY qty ASC LIMIT 3");
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Trang Quản Trị - MF SHOP</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css?v=<?= time() ?>">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .sub-tab-box { display: flex; gap: 10px; margin-bottom: 20px; border-bottom: 2px solid #ddd; padding-bottom: 10px; }
        .sub-tab { padding: 8px 20px; background: #eee; border-radius: 4px; font-weight: bold; color:#555; }
        .sub-tab.active { background: var(--primary-color); color: white; }
        .product-scroll-list { max-height: 250px; overflow-y: auto; border: 1px solid #ddd; padding: 10px; border-radius: 6px; background: white; }
        .product-scroll-list label { display: block; margin-bottom: 8px; padding-bottom: 8px; border-bottom: 1px dashed #eee; font-weight: normal !important; font-size: 14px; cursor: pointer; }
        .bulk-action-bar { background: #f8f9fa; padding: 15px; border-radius: 6px; border: 1px solid #ddd; display: flex; gap: 10px; align-items: center; margin-bottom: 15px; flex-wrap: wrap; }
        .bulk-action-bar select, .bulk-action-bar input { padding: 8px 12px; border: 1px solid #ccc; border-radius: 4px; outline: none; }
        .price-original { text-decoration: line-through; color: #95a5a6; font-size: 13px; }
        .price-sale { color: #e74c3c; font-weight: bold; font-size: 16px; display: block; }
    </style>
</head>
<body>
    <div class="admin-sidebar">
        <h2><i class="fa fa-shopping-bag"></i> MF SHOP</h2>
        <a href="admin.php?action=dashboard" class="<?= $action=='dashboard'?'active':'' ?>"><i class="fa fa-chart-pie"></i> Bảng điều khiển</a>
        <a href="admin.php?action=products" class="<?= $action=='products'?'active':'' ?>"><i class="fa fa-tshirt"></i> Quản lý Sản phẩm</a>
        <a href="admin.php?action=promotions" class="<?= $action=='promotions'?'active':'' ?>"><i class="fa fa-ticket-alt"></i> Quản lý Khuyến mãi</a>
        <a href="admin.php?action=orders" class="<?= $action=='orders'?'active':'' ?>"><i class="fa fa-box"></i> Quản lý Đơn hàng <?= $admin_noti_count > 0 ? '<span style="background:red; color:white; border-radius:50%; padding:2px 6px; font-size:12px; margin-left:5px;">'.$admin_noti_count.'</span>' : '' ?></a>
        <a href="admin.php?action=categories" class="<?= $action=='categories'?'active':'' ?>"><i class="fa fa-tags"></i> Quản lý Danh mục</a>
        <a href="admin.php?action=customers" class="<?= $action=='customers'?'active':'' ?>"><i class="fa fa-users"></i> Khách hàng</a>
        <a href="profile.php"><i class="fa fa-user-cog"></i> Tài khoản Admin</a>
        <a href="index.php" style="margin-top:30px; color:#f1c40f;"><i class="fa fa-home"></i> Xem trang Web</a>
    </div>

    <div class="admin-main">
        <?php if($action == 'dashboard'): ?>
            <h2>Tổng Overview Hệ Thống</h2>
            <div class="dashboard-cards">
                <div class="dash-card"><div class="dash-card-info"><h4>Tổng Doanh Thu Lịch Sử</h4><h2><?= number_format($total_revenue_overall) ?>đ</h2></div><div class="dash-icon icon-green"><i class="fa fa-money-bill-wave"></i></div></div>
                <div class="dash-card"><div class="dash-card-info"><h4>Tổng Khách Hàng</h4><h2><?= $total_users ?></h2></div><div class="dash-icon icon-blue"><i class="fa fa-users"></i></div></div>
                <a href="admin.php?action=top_sellers" class="dash-card">
                    <div class="dash-card-info" style="width: 100%; margin-right: 15px;">
                        <h4>Bán Chạy Nhất <i class="fa fa-arrow-right" style="font-size:10px;"></i></h4>
                        <div style="margin-top: 15px; display:flex; flex-direction:column; gap:10px;">
                            <?php $rank = 1; if($best_seller_query && $best_seller_query->num_rows > 0): while($bs = $best_seller_query->fetch_assoc()): ?>
                                <?php $bg_color = ($rank == 1) ? '#f1c40f' : (($rank == 2) ? '#bdc3c7' : '#cd7f32'); ?>
                                <div style="display:flex; justify-content:space-between; border-bottom: 1px dashed #eee; padding-bottom: 6px; align-items:center;">
                                    <div style="display:flex; align-items:center; gap:8px;"><span style="background:<?= $bg_color ?>; color:#fff; font-size:11px; padding:2px 6px; border-radius:4px; font-weight:bold;">Top <?= $rank++ ?></span><span style="color:#f39c12; font-size:13px; font-weight:600; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width: 100px;"><?= htmlspecialchars($bs['name']) ?></span></div>
                                    <span style="font-weight:bold; font-size:13px; color:#2c3e50;"><?= $bs['qty'] ?> <small style="font-weight:normal; color:#888;">cái</small></span>
                                </div>
                            <?php endwhile; else: ?><p style="color:#888; font-size:13px; margin:0;">Chưa có dữ liệu</p><?php endif; ?>
                        </div>
                    </div><div class="dash-icon icon-orange"><i class="fa fa-fire"></i></div>
                </a>
                <a href="admin.php?action=bad_sellers" class="dash-card">
                    <div class="dash-card-info" style="width: 100%; margin-right: 15px;">
                        <h4>Tồn Kho / Bán Ế <i class="fa fa-arrow-right" style="font-size:10px;"></i></h4>
                        <div style="margin-top: 15px; display:flex; flex-direction:column; gap:10px;">
                            <?php $rank = 1; if($worst_seller_query && $worst_seller_query->num_rows > 0): while($ws = $worst_seller_query->fetch_assoc()): ?>
                                <?php $bg_color = ($rank == 1) ? '#e74c3c' : (($rank == 2) ? '#e67e22' : '#f39c12'); ?>
                                <div style="display:flex; justify-content:space-between; border-bottom: 1px dashed #eee; padding-bottom: 6px; align-items:center;">
                                    <div style="display:flex; align-items:center; gap:8px;"><span style="background:<?= $bg_color ?>; color:#fff; font-size:11px; padding:2px 6px; border-radius:4px; font-weight:bold;">Top <?= $rank++ ?></span><span style="color:#e74c3c; font-size:13px; font-weight:600; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width: 100px;"><?= htmlspecialchars($ws['name']) ?></span></div>
                                    <span style="font-weight:bold; font-size:13px; color:#2c3e50;"><?= $ws['qty'] ?> <small style="font-weight:normal; color:#888;">cái</small></span>
                                </div>
                            <?php endwhile; else: ?><p style="color:#888; font-size:13px; margin:0;">Chưa có dữ liệu</p><?php endif; ?>
                        </div>
                    </div><div class="dash-icon icon-red"><i class="fa fa-snowflake"></i></div>
                </a>
            </div>
            <div class="table-wrapper">
                <div class="chart-header">
                    <h3><i class="fa fa-chart-bar" style="color: #3498db;"></i> Biểu Đồ Kế Toán Tự Động</h3>
                    <form method="GET" class="advanced-filter-form">
                        <input type="hidden" name="action" value="dashboard">
                        <div class="filter-input-group"><label><i class="fa fa-clock"></i> Chế độ lọc:</label><select name="filter" id="filterSelector" onchange="toggleFilterInputs()"><optgroup label="GIỜ CHUYÊN SÂU"><option value="hour" <?=$filter=='hour'?'selected':''?>>Theo 24 giờ qua</option><option value="spec_hour" <?=$filter=='spec_hour'?'selected':''?>>Chọn 1 giờ cụ thể</option><option value="range_hour" <?=$filter=='range_hour'?'selected':''?>>Khoảng giờ tùy chọn</option></optgroup><optgroup label="NGÀY CHUYÊN SÂU"><option value="day" <?=$filter=='day'?'selected':''?>>Theo 30 ngày qua</option><option value="spec_day" <?=$filter=='spec_day'?'selected':''?>>Chọn 1 ngày cụ thể</option><option value="range_day" <?=$filter=='range_day'?'selected':''?>>Khoảng ngày tùy chọn</option></optgroup><optgroup label="TUẦN CHUYÊN SÂU"><option value="week" <?=$filter=='week'?'selected':''?>>Theo 12 tuần qua</option><option value="spec_week" <?=$filter=='spec_week'?'selected':''?>>Chọn 1 tuần cụ thể</option><option value="range_week" <?=$filter=='range_week'?'selected':''?>>Khoảng tuần tùy chọn</option></optgroup><optgroup label="THÁNG CHUYÊN SÂU"><option value="month" <?=$filter=='month'?'selected':''?>>Theo 12 tháng qua</option><option value="spec_month" <?=$filter=='spec_month'?'selected':''?>>Chọn 1 tháng cụ thể</option><option value="range_month" <?=$filter=='range_month'?'selected':''?>>Khoảng tháng tùy chọn</option></optgroup><optgroup label="QUÝ CHUYÊN SÂU"><option value="quarter" <?=$filter=='quarter'?'selected':''?>>Theo 12 quý qua</option><option value="spec_quarter" <?=$filter=='spec_quarter'?'selected':''?>>Chọn 1 quý cụ thể</option><option value="range_quarter" <?=$filter=='range_quarter'?'selected':''?>>Khoảng quý tùy chọn</option></optgroup><optgroup label="NĂM CHUYÊN SÂU"><option value="year" <?=$filter=='year'?'selected':''?>>Theo 10 năm qua</option><option value="spec_year" <?=$filter=='spec_year'?'selected':''?>>Chọn 1 năm cụ thể</option><option value="range_year" <?=$filter=='range_year'?'selected':''?>>Khoảng năm tùy chọn</option></optgroup><optgroup label="THẾ KỶ CHUYÊN SÂU"><option value="century" <?=$filter=='century'?'selected':''?>>Theo thế kỷ</option><option value="spec_century" <?=$filter=='spec_century'?'selected':''?>>Chọn thế kỷ cụ thể</option><option value="range_century" <?=$filter=='range_century'?'selected':''?>>Khoảng thế kỷ tùy chọn</option></optgroup></select></div>
                        <div id="box_spec_hour" class="dynamic-input-box"><div class="filter-input-group"><label>Mốc giờ:</label><input type="datetime-local" name="spec_hour" value="<?=$spec_hour?>"></div></div><div id="box_range_hour" class="dynamic-input-box"><div class="filter-input-group"><label>Từ:</label><input type="datetime-local" name="from_hour" value="<?=$from_hour?>"></div><div class="filter-input-group"><label>Đến:</label><input type="datetime-local" name="to_hour" value="<?=$to_hour?>"></div></div><div id="box_spec_day" class="dynamic-input-box"><div class="filter-input-group"><label>Mốc ngày:</label><input type="date" name="spec_date" value="<?=$spec_date?>"></div></div><div id="box_range_day" class="dynamic-input-box"><div class="filter-input-group"><label>Từ:</label><input type="date" name="from_date" value="<?=$from_date?>"></div><div class="filter-input-group"><label>Đến:</label><input type="date" name="to_date" value="<?=$to_date?>"></div></div><div id="box_spec_week" class="dynamic-input-box"><div class="filter-input-group"><label>Mốc tuần:</label><input type="week" name="spec_week" value="<?=$spec_week?>"></div></div><div id="box_range_week" class="dynamic-input-box"><div class="filter-input-group"><label>Từ:</label><input type="week" name="from_week" value="<?=$from_week?>"></div><div class="filter-input-group"><label>Đến:</label><input type="week" name="to_week" value="<?=$to_week?>"></div></div><div id="box_spec_month" class="dynamic-input-box"><div class="filter-input-group"><label>Mốc tháng:</label><input type="month" name="spec_month" value="<?=$spec_month?>"></div></div><div id="box_range_month" class="dynamic-input-box"><div class="filter-input-group"><label>Từ:</label><input type="month" name="from_month" value="<?=$from_month?>"></div><div class="filter-input-group"><label>Đến:</label><input type="month" name="to_month" value="<?=$to_month?>"></div></div><div id="box_spec_quarter" class="dynamic-input-box"><div class="filter-input-group"><label>Năm:</label><input type="number" name="spec_q_year" value="<?=$spec_q_year?>" style="width:80px;"></div><div class="filter-input-group"><label>Quý:</label><select name="spec_quarter"><option value="1" <?=$spec_quarter==1?'selected':''?>>Q1</option><option value="2" <?=$spec_quarter==2?'selected':''?>>Q2</option><option value="3" <?=$spec_quarter==3?'selected':''?>>Q3</option><option value="4" <?=$spec_quarter==4?'selected':''?>>Q4</option></select></div></div><div id="box_range_quarter" class="dynamic-input-box"><div class="filter-input-group"><label>Từ:</label><input type="number" name="from_q_year" value="<?=$from_q_year?>" style="width:75px;"><select name="from_quarter"><option value="1" <?=$from_quarter==1?'selected':''?>>Q1</option><option value="2" <?=$from_quarter==2?'selected':''?>>Q2</option><option value="3" <?=$from_quarter==3?'selected':''?>>Q3</option><option value="4" <?=$from_quarter==4?'selected':''?>>Q4</option></select></div><div class="filter-input-group"><label>Đến:</label><input type="number" name="to_q_year" value="<?=$to_q_year?>" style="width:75px;"><select name="to_quarter"><option value="1" <?=$to_quarter==1?'selected':''?>>Q1</option><option value="2" <?=$to_quarter==2?'selected':''?>>Q2</option><option value="3" <?=$to_quarter==3?'selected':''?>>Q3</option><option value="4" <?=$to_quarter==4?'selected':''?>>Q4</option></select></div></div><div id="box_spec_year" class="dynamic-input-box"><div class="filter-input-group"><label>Mốc năm:</label><input type="number" name="spec_year" value="<?=$spec_year?>" min="2000" max="2099"></div></div><div id="box_range_year" class="dynamic-input-box"><div class="filter-input-group"><label>Từ:</label><input type="number" name="from_year" value="<?=$from_year?>" min="2000" max="2099"></div><div class="filter-input-group"><label>Đến:</label><input type="number" name="to_year" value="<?=$to_year?>" min="2000" max="2099"></div></div><div id="box_spec_century" class="dynamic-input-box"><div class="filter-input-group"><label>Mốc TK:</label><input type="number" name="spec_century" value="<?=$spec_century?>"></div></div><div id="box_range_century" class="dynamic-input-box"><div class="filter-input-group"><label>Từ TK:</label><input type="number" name="from_century" value="<?=$from_century?>"></div><div class="filter-input-group"><label>Đến TK:</label><input type="number" name="to_century" value="<?=$to_century?>"></div></div>
                        <div class="filter-input-group"><button type="submit" class="btn btn-secondary" style="padding: 7px 15px;"><i class="fa fa-filter"></i> Lọc dữ liệu</button></div>
                    </form>
                </div>
                <?php if (count($dates) > 0): ?>
                    <div class="chart-scroll-area"><div id="chartCanvasWrapper" class="chart-canvas-wrapper"><canvas id="comboChart"></canvas></div></div>
                    <script>
                        const totalLabels = <?= count($dates) ?>; const wrapper = document.getElementById('chartCanvasWrapper');
                        if (totalLabels > 12) { wrapper.style.width = ((totalLabels / 12) * 100) + '%'; } else { wrapper.style.width = '100%'; }
                        const ctx = document.getElementById('comboChart').getContext('2d');
                        let gradient = ctx.createLinearGradient(0, 0, 0, 380); gradient.addColorStop(0, 'rgba(52, 152, 219, 0.9)'); gradient.addColorStop(1, 'rgba(52, 152, 219, 0.1)');
                        new Chart(ctx, { type: 'bar', data: { labels: <?= json_encode($dates) ?>, datasets: [{ type: 'line', label: 'Số lượng đơn hàng', data: <?= json_encode($order_counts) ?>, borderColor: '#e74c3c', backgroundColor: '#e74c3c', borderWidth: 3, tension: 0.4, yAxisID: 'y1' }, { type: 'bar', label: 'Doanh thu (VNĐ)', data: <?= json_encode($revenues) ?>, backgroundColor: gradient, borderColor: '#2980b9', borderWidth: 2, borderRadius: 6, yAxisID: 'y' }] }, options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: true, position: 'top' }, tooltip: { padding: 15 } }, scales: { y: { type: 'linear', position: 'left', beginAtZero: true }, y1: { type: 'linear', position: 'right', beginAtZero: true, grid: { drawOnChartArea: false }, ticks: { stepSize: 1 } } } } });
                        window.onload = function() { const scrollArea = document.querySelector('.chart-scroll-area'); if(scrollArea) scrollArea.scrollLeft = scrollArea.scrollWidth; }
                    </script>
                <?php else: ?><div style="text-align: center; padding: 50px;"><i class="fa fa-chart-pie" style="font-size: 50px; color: #ccc;"></i><h4>Không tìm thấy dữ liệu hóa đơn</h4></div><?php endif; ?>
            </div>
            <script>function toggleFilterInputs() { let f = document.getElementById('filterSelector').value; const b = ['box_spec_hour', 'box_range_hour', 'box_spec_day', 'box_range_day', 'box_spec_week', 'box_range_week', 'box_spec_month', 'box_range_month', 'box_spec_quarter', 'box_range_quarter', 'box_spec_year', 'box_range_year', 'box_spec_century', 'box_range_century']; b.forEach(id => {  let el = document.getElementById(id); if(el) el.classList.remove('active'); }); let t = document.getElementById('box_' + f); if(t) t.classList.add('active'); } toggleFilterInputs();</script>

        <?php elseif($action == 'promotions'): ?>
            <h2>Hệ Thống Quản Lý Sự Kiện Khuyến Mãi</h2>
            <div class="sub-tab-box">
                <a href="admin.php?action=promotions&tab=vouchers" class="sub-tab <?= $tab=='vouchers'?'active':'' ?>"><i class="fa fa-ticket-alt"></i> Mã Giảm Giá / Freeship</a>
                <a href="admin.php?action=promotions&tab=campaigns" class="sub-tab <?= $tab=='campaigns'?'active':'' ?>"><i class="fa fa-bullhorn"></i> Chương Trình Khuyến Mãi</a>
            </div>

            <?php if($tab == 'vouchers'): ?>
                <div class="table-wrapper">
                    <?php if(isset($_GET['edit_promo'])): 
                        $edit_id = (int)$_GET['edit_promo']; $promo_edit = $conn->query("SELECT * FROM promotions WHERE id=$edit_id")->fetch_assoc(); 
                        $st_time = $promo_edit['start_date'] ? date('Y-m-d\TH:i', strtotime($promo_edit['start_date'])) : '';
                        $en_time = $promo_edit['end_date'] ? date('Y-m-d\TH:i', strtotime($promo_edit['end_date'])) : '';
                    ?>
                        <form method="POST" action="admin.php?action=promotions&tab=vouchers" style="background: #ebf5fb; padding: 20px; border-radius: 8px; border: 1px solid var(--secondary-color); margin-bottom: 20px;">
                            <h4 style="margin-top:0; color:var(--secondary-color);"><i class="fa fa-edit"></i> Cập Nhật Voucher</h4>
                            <input type="hidden" name="update_promo" value="1"><input type="hidden" name="promo_id" value="<?= $edit_id ?>">
                            <div class="variant-flex">
                                <div class="form-group" style="flex:1;"><label>Mã Code:</label><input type="text" name="code" value="<?= $promo_edit['code'] ?>" required style="text-transform: uppercase;"></div>
                                <div class="form-group" style="flex:1;"><label>Giảm tiền mặt (đ):</label><input type="number" name="discount_amount" value="<?= $promo_edit['discount_amount'] ?>"></div>
                                <div class="form-group" style="flex:1;"><label>Hoặc Giảm (%):</label><input type="number" name="discount_percent" value="<?= $promo_edit['discount_percent'] ?>" max="100"></div>
                            </div>
                            <div class="variant-flex">
                                <div class="form-group" style="flex:1;"><label>Đơn tối thiểu (đ):</label><input type="number" name="min_order" value="<?= $promo_edit['min_order'] ?>"></div>
                                <div class="form-group" style="flex:1;"><label>Tổng lượt dùng:</label><input type="number" name="usage_limit" value="<?= $promo_edit['usage_limit'] ?>"></div>
                                <div class="form-group" style="flex:1; display:flex; align-items:flex-end;"><label style="font-weight: bold; color: var(--secondary-color); padding-bottom: 10px;"><input type="checkbox" name="is_freeship" value="1" <?= $promo_edit['is_freeship']==1?'checked':'' ?>> Ghi đè thành mã FREESHIP</label></div>
                            </div>
                            <div class="variant-flex">
                                <div class="form-group" style="flex:1;"><label>Bắt đầu từ:</label><input type="datetime-local" name="start_date" required value="<?= $st_time ?>"></div>
                                <div class="form-group" style="flex:1;"><label>Kết thúc vào:</label><input type="datetime-local" name="end_date" required value="<?= $en_time ?>"></div>
                            </div>
                            <button type="submit" class="btn btn-secondary"><i class="fa fa-save"></i> Cập Nhật</button>
                            <a href="admin.php?action=promotions&tab=vouchers" class="btn" style="background:#ccc;">Hủy</a>
                        </form>
                    <?php else: ?>
                        <form method="POST" action="admin.php?action=promotions&tab=vouchers" style="background: #f8f9fa; padding: 20px; border-radius: 8px; border: 1px solid #ddd; margin-bottom: 20px;">
                            <h4 style="margin-top:0; color:var(--success-color);"><i class="fa fa-plus-circle"></i> Phát Hành Voucher Mới</h4>
                            <div class="variant-flex">
                                <div class="form-group" style="flex:1;"><label>Mã Code:</label><input type="text" name="code" required style="text-transform: uppercase;"></div>
                                <div class="form-group" style="flex:1;"><label>Giảm tiền mặt (đ):</label><input type="number" name="discount_amount" value="0"></div>
                                <div class="form-group" style="flex:1;"><label>Hoặc Giảm (%):</label><input type="number" name="discount_percent" value="0" max="100"></div>
                            </div>
                            <div class="variant-flex">
                                <div class="form-group" style="flex:1;"><label>Đơn tối thiểu (đ):</label><input type="number" name="min_order" value="0"></div>
                                <div class="form-group" style="flex:1;"><label>Tổng lượt dùng:</label><input type="number" name="usage_limit" value="100"></div>
                                <div class="form-group" style="flex:1; display:flex; align-items:flex-end;"><label style="font-weight: bold; color: var(--secondary-color); padding-bottom: 10px;"><input type="checkbox" name="is_freeship" value="1"> Đây là mã FREESHIP</label></div>
                            </div>
                            <div class="variant-flex">
                                <div class="form-group" style="flex:1;"><label>Bắt đầu từ:</label><input type="datetime-local" name="start_date" required value="<?=date('Y-m-d\T00:00')?>"></div>
                                <div class="form-group" style="flex:1;"><label>Kết thúc vào:</label><input type="datetime-local" name="end_date" required value="<?=date('Y-m-d\T23:59', strtotime('+30 days'))?>"></div>
                            </div>
                            <button type="submit" name="add_promo" class="btn btn-success"><i class="fa fa-check"></i> Lưu Voucher</button>
                        </form>
                    <?php endif; ?>

                    <?php $promos = $conn->query("SELECT * FROM promotions ORDER BY id DESC"); ?>
                    <table>
                        <tr><th>Mã Code</th><th>Loại Voucher</th><th>Mức Giảm / Min Đơn</th><th>Đã dùng</th><th>Thời gian áp dụng</th><th>Thao tác</th></tr>
                        <?php while($pr = $promos->fetch_assoc()): ?>
                        <tr>
                            <td><b style="color:var(--primary-color); background:#ffeaa7; padding:4px 8px; border-radius:4px;"><?= $pr['code'] ?></b></td>
                            <td><?= ($pr['is_freeship']==1) ? '<b style="color:var(--secondary-color);"><i class="fa fa-shipping-fast"></i> FREESHIP</b>' : 'Giảm tiền đơn' ?></td>
                            <td><?= ($pr['is_freeship']==1) ? 'Giảm 30k' : ($pr['discount_amount'] > 0 ? number_format($pr['discount_amount']).'đ' : $pr['discount_percent'].'%') ?><br><small>Min: <?= number_format($pr['min_order']) ?>đ</small></td>
                            <td><?= $pr['used_count'] ?> / <?= $pr['usage_limit'] ?></td>
                            <td><small>Từ: <?= date('d/m/Y H:i', strtotime($pr['start_date'])) ?><br>Đến: <b style="color:red;"><?= date('d/m/Y H:i', strtotime($pr['end_date'])) ?></b></small></td>
                            <td><a href="admin.php?action=promotions&tab=vouchers&edit_promo=<?= $pr['id'] ?>" style="color:var(--secondary-color); margin-right:10px;"><i class="fa fa-edit"></i> Sửa</a><a href="admin.php?action=promotions&delete_promo=<?= $pr['id'] ?>" onclick="return confirm('Xóa mã này?');" style="color:var(--primary-color);"><i class="fa fa-trash"></i> Xóa</a></td>
                        </tr>
                        <?php endwhile; ?>
                    </table>
                </div>

            <?php elseif($tab == 'campaigns'): ?>
                <div class="table-wrapper">
                    <?php if(isset($_GET['edit_campaign'])): 
                        $edit_id = (int)$_GET['edit_campaign']; $camp_edit = $conn->query("SELECT * FROM campaigns WHERE id=$edit_id")->fetch_assoc(); 
                        $st_time = $camp_edit['start_date'] ? date('Y-m-d\TH:i', strtotime($camp_edit['start_date'])) : '';
                        $en_time = $camp_edit['end_date'] ? date('Y-m-d\TH:i', strtotime($camp_edit['end_date'])) : '';
                        $sel_p = []; $p_res = $conn->query("SELECT id FROM products WHERE campaign_id=$edit_id");
                        while($row = $p_res->fetch_assoc()){ $sel_p[] = $row['id']; }
                    ?>
                        <form method="POST" action="admin.php?action=promotions&tab=campaigns" style="background: #ebf5fb; padding: 20px; border-radius: 8px; border: 1px solid var(--secondary-color); margin-bottom: 20px;">
                            <h4 style="margin-top:0; color:var(--secondary-color);"><i class="fa fa-edit"></i> Cập Nhật Chiến Dịch</h4>
                            <input type="hidden" name="update_campaign" value="1"><input type="hidden" name="campaign_id" value="<?= $edit_id ?>">
                            <div class="variant-flex">
                                <div class="form-group" style="flex:2;"><label>Tên chương trình:</label><input type="text" name="campaign_name" required value="<?= htmlspecialchars($camp_edit['name']) ?>"></div>
                                <div class="form-group" style="flex:1;"><label>Giảm (%):</label><input type="number" name="discount_percent" min="1" max="99" required value="<?= $camp_edit['discount_percent'] ?>"></div>
                            </div>
                            <div class="variant-flex">
                                <div class="form-group" style="flex:1;"><label>Bắt đầu từ:</label><input type="datetime-local" name="start_date" required value="<?= $st_time ?>"></div>
                                <div class="form-group" style="flex:1;"><label>Kết thúc vào:</label><input type="datetime-local" name="end_date" required value="<?= $en_time ?>"></div>
                            </div>
                            <div class="form-group" style="margin-top: 15px;">
                                <label><strong><i class="fa fa-tshirt"></i> Tick chọn các sản phẩm tham gia:</strong></label>
                                <div style="margin-bottom: 8px;">
                                    <input type="text" onkeyup="filterPromoProducts(this)" placeholder="🔍 Nhập tên hoặc ID sản phẩm để tìm nhanh..." style="width: 100%; padding: 8px 12px; border: 1px solid #ccc; border-radius: 4px; outline: none;">
                                </div>
                                <div class="product-scroll-list">
                                    <?php $all_p = $conn->query("SELECT id, name, price FROM products ORDER BY id DESC"); ?>
                                    <?php while($p_row = $all_p->fetch_assoc()): ?>
                                        <label><input type="checkbox" name="campaign_products[]" value="<?= $p_row['id'] ?>" <?= in_array($p_row['id'], $sel_p)?'checked':'' ?>> <b>[ID: <?= $p_row['id'] ?>]</b> <?= htmlspecialchars($p_row['name']) ?> - <?= number_format($p_row['price']) ?>đ</label>
                                    <?php endwhile; ?>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-secondary" style="margin-top: 15px;"><i class="fa fa-save"></i> Cập nhật ngay</button>
                            <a href="admin.php?action=promotions&tab=campaigns" class="btn" style="background:#ccc;">Hủy</a>
                        </form>
                    <?php else: ?>
                        <form method="POST" action="admin.php?action=promotions&tab=campaigns" style="background: #f8f9fa; padding: 20px; border-radius: 8px; border: 1px solid #ddd; margin-bottom: 20px;">
                            <h4 style="margin-top:0; color:var(--primary-color);"><i class="fa fa-bullhorn"></i> Tạo Chiến Dịch Mới</h4>
                            <div class="variant-flex">
                                <div class="form-group" style="flex:2;"><label>Tên chương trình (VD: Flash Sale):</label><input type="text" name="campaign_name" required></div>
                                <div class="form-group" style="flex:1;"><label>Mức giảm (%):</label><input type="number" name="discount_percent" min="1" max="99" required></div>
                            </div>
                            <div class="variant-flex">
                                <div class="form-group" style="flex:1;"><label>Bắt đầu từ:</label><input type="datetime-local" name="start_date" required value="<?=date('Y-m-d\T00:00')?>"></div>
                                <div class="form-group" style="flex:1;"><label>Kết thúc vào:</label><input type="datetime-local" name="end_date" required value="<?=date('Y-m-d\T23:59', strtotime('+10 days'))?>"></div>
                            </div>
                            <div class="form-group" style="margin-top: 15px;">
                                <label><strong><i class="fa fa-tshirt"></i> Tick chọn các sản phẩm tham gia:</strong></label>
                                <div style="margin-bottom: 8px;">
                                    <input type="text" onkeyup="filterPromoProducts(this)" placeholder="🔍 Nhập tên hoặc ID sản phẩm để tìm nhanh..." style="width: 100%; padding: 8px 12px; border: 1px solid #ccc; border-radius: 4px; outline: none;">
                                </div>
                                <div class="product-scroll-list">
                                    <?php $all_p = $conn->query("SELECT id, name, price FROM products ORDER BY id DESC"); ?>
                                    <?php while($p_row = $all_p->fetch_assoc()): ?>
                                        <label><input type="checkbox" name="campaign_products[]" value="<?= $p_row['id'] ?>"> <b>[ID: <?= $p_row['id'] ?>]</b> <?= htmlspecialchars($p_row['name']) ?> - <?= number_format($p_row['price']) ?>đ</label>
                                    <?php endwhile; ?>
                                </div>
                            </div>
                            <button type="submit" name="add_campaign" class="btn btn-primary" style="margin-top: 15px;"><i class="fa fa-bolt"></i> Tạo Chiến Dịch</button>
                        </form>
                    <?php endif; ?>

                    <h3><i class="fa fa-list"></i> Các chương trình khuyến mãi hiện có</h3>
                    <?php $campaigns = $conn->query("SELECT * FROM campaigns ORDER BY id DESC"); ?>
                    <table>
                        <tr><th>ID</th><th>Tên chương trình</th><th>Mức giảm</th><th>Thời gian</th><th>Thao tác</th></tr>
                        <?php if($campaigns->num_rows > 0): while($cp = $campaigns->fetch_assoc()): ?>
                        <tr>
                            <td>#<?= $cp['id'] ?></td><td><b style="color:var(--secondary-color);"><?= htmlspecialchars($cp['name']) ?></b></td>
                            <td><span style="background:red; color:white; padding:3px 8px; border-radius:4px; font-weight:bold;">-<?= $cp['discount_percent'] ?>%</span></td>
                            <td><small>Từ: <?= date('d/m/Y H:i', strtotime($cp['start_date'])) ?><br>Đến: <b style="color:red;"><?= date('d/m/Y H:i', strtotime($cp['end_date'])) ?></b></small></td>
                            <td>
                                <a href="admin.php?action=promotions&tab=campaigns&edit_campaign=<?= $cp['id'] ?>" style="color:var(--secondary-color); margin-right:15px;"><i class="fa fa-edit"></i> Sửa</a>
                                <a href="admin.php?action=promotions&tab=campaigns&delete_campaign=<?= $cp['id'] ?>" onclick="return confirm('Bạn muốn xóa chiến dịch này?');" style="color:var(--primary-color);"><i class="fa fa-trash"></i> Xóa</a>
                            </td>
                        </tr>
                        <?php endwhile; else: ?>
                            <tr><td colspan="5" style="text-align:center; padding: 20px; color: #888;">Chưa có chiến dịch khuyến mãi nào được tạo.</td></tr>
                        <?php endif; ?>
                    </table>
                </div>

                <script>
                    function filterPromoProducts(input) {
                        let filter = input.value.toLowerCase(); let list = input.parentElement.nextElementSibling; let labels = list.getElementsByTagName('label');
                        for (let i = 0; i < labels.length; i++) {
                            let txtValue = labels[i].textContent || labels[i].innerText;
                            if (txtValue.toLowerCase().indexOf(filter) > -1) { labels[i].style.display = ""; } else { labels[i].style.display = "none"; }
                        }
                    }
                </script>
            <?php endif; ?>

        <?php elseif($action == 'orders'): ?>
            <h2>Quản Lý Đơn Hàng</h2>
            <div class="table-wrapper">
                <?php $ords = $conn->query("SELECT o.*, u.name as customer_name FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC LIMIT 50"); ?>
                <table>
                    <tr><th>Mã ĐH</th><th>Khách hàng</th><th>Tổng tiền</th><th>Ngày đặt</th><th>Trạng thái</th><th>Thao tác</th></tr>
                    <?php if($ords->num_rows > 0): while($o = $ords->fetch_assoc()): 
                        // Bôi màu nền đơn hàng bị hủy hoặc hoàn trả để Admin chú ý
                        $bg_color = '';
                        if($o['status'] == 'Yêu cầu hoàn trả') $bg_color = 'background: #fff3cd;';
                        if($o['status'] == 'Đã hủy') $bg_color = 'background: #f8d7da;';
                    ?>
                    <tr style="<?= $bg_color ?>">
                        <td>#<?= $o['id'] ?></td><td><?= htmlspecialchars($o['customer_name']) ?></td>
                        <td style="color: var(--primary-color); font-weight: bold;"><?= number_format($o['total_amount'] + $o['vat_amount']) ?>đ</td>
                        <td><?= $o['created_at'] ?></td>
                        <td>
                            <form action="admin_update_order.php" method="POST" style="display:flex; gap:5px;">
                                <input type="hidden" name="order_id" value="<?= $o['id'] ?>">
                                <select name="status" style="padding: 5px; border-radius: 3px; border: 1px solid var(--border-color);">
                                    <option <?= $o['status']=='Chờ xác nhận thanh toán'?'selected':'' ?>>Chờ xác nhận thanh toán</option>
                                    <option <?= $o['status']=='Đang chuẩn bị hàng'?'selected':'' ?>>Đang chuẩn bị hàng</option>
                                    <option <?= $o['status']=='Đang giao hàng'?'selected':'' ?>>Đang giao hàng</option>
                                    <option <?= $o['status']=='Hoàn thành'?'selected':'' ?>>Hoàn thành</option>
                                    <option <?= $o['status']=='Đã hủy'?'selected':'' ?>>Đã hủy</option>
                                    <option <?= $o['status']=='Yêu cầu hoàn trả'?'selected':'' ?>>Yêu cầu hoàn trả</option>
                                    <option <?= $o['status']=='Đã hoàn trả'?'selected':'' ?>>Đã hoàn trả</option>
                                </select>
                                <button type="submit" class="btn btn-success" style="padding:5px 10px; font-size:13px;">Lưu</button>
                            </form>
                        </td>
                        <td>
                            <a href="admin.php?action=order_detail&id=<?= $o['id'] ?>" style="color: var(--secondary-color); margin-right: 10px;"><i class="fa fa-eye"></i> Xem</a> 
                            <a href="admin.php?delete_order=<?= $o['id'] ?>" onclick="return confirm('CẢNH BÁO: Xóa vĩnh viễn đơn hàng này?');" style="color: var(--primary-color);"><i class="fa fa-trash"></i> Xóa</a>
                        </td>
                    </tr>
                    <?php endwhile; else: ?>
                        <tr><td colspan="6" style="text-align:center; padding: 20px;">Chưa có đơn hàng nào.</td></tr>
                    <?php endif; ?>
                </table>
            </div>

        <?php elseif($action == 'products'): ?>
            <h2>Quản Lý Sản Phẩm</h2>
            <div class="table-wrapper">
                <form action="admin.php" method="POST">
                    <div style="display: flex; gap: 10px; margin-bottom: 15px;"><a href="add_product.php" class="btn btn-success"><i class="fa fa-plus"></i> Thêm Sản Phẩm</a></div>
                    <div class="bulk-action-bar">
                        <strong><i class="fa fa-check-square"></i> Hành động:</strong>
                        <select name="bulk_action" id="bulkActionSelector" required><option value="">-- Chọn thao tác --</option><option value="discount_percent">Giảm giá theo %</option><option value="discount_amount">Giảm tiền mặt trực tiếp</option><option value="reset_discount">Hủy tất cả giảm giá (Về giá gốc)</option><option value="delete">Xóa hàng loạt</option></select>
                        <input type="number" name="discount_value" id="discountValueInput" placeholder="Nhập số % hoặc tiền..." style="display:none; width: 150px;">
                        <button type="submit" name="apply_bulk_action" class="btn btn-primary"><i class="fa fa-bolt"></i> Áp dụng</button>
                    </div>
                    <?php $prods = $conn->query("SELECT p.*, c.name as cat_name FROM products p LEFT JOIN categories c ON p.category_id = c.id ORDER BY p.id DESC"); ?>
                    <table>
                        <tr><th style="width: 40px;"><input type="checkbox" onclick="toggleSelectAll(this, 'product_checkbox')"></th><th>ID</th><th>Tên sản phẩm</th><th>Danh mục</th><th>Giá hiện tại</th><th>Thao tác</th></tr>
                        <?php while($p = $prods->fetch_assoc()): ?>
                        <tr>
                            <td><input type="checkbox" name="selected_products[]" value="<?= $p['id'] ?>" class="product_checkbox"></td>
                            <td><?= $p['id'] ?></td><td><?= htmlspecialchars($p['name']) ?></td><td><?= $p['cat_name'] ?></td>
                            <td><?php if($p['sale_price'] > 0): ?><span class="price-original"><?= number_format($p['price']) ?>đ</span><span class="price-sale"><?= number_format($p['sale_price']) ?>đ</span><?php else: ?><b style="color:var(--primary-color);"><?= number_format($p['price']) ?>đ</b><?php endif; ?></td>
                            <td><a href="edit_product.php?id=<?= $p['id'] ?>" style="color:var(--secondary-color); margin-right:10px;"><i class="fa fa-edit"></i> Sửa</a> <a href="admin.php?delete_product=<?= $p['id'] ?>" style="color:var(--primary-color);"><i class="fa fa-trash"></i> Xóa</a></td>
                        </tr>
                        <?php endwhile; ?>
                    </table>
                </form>
            </div>
            <script>document.getElementById('bulkActionSelector').addEventListener('change', function() { let v = document.getElementById('discountValueInput'); if (this.value==='discount_percent' || this.value==='discount_amount') { v.style.display='block'; v.required=true; } else { v.style.display='none'; v.required=false; } });</script>

        <?php elseif($action == 'categories'): ?>
            <h2>Quản Lý Danh Mục</h2>
            <div class="table-wrapper">
                <?php if (isset($_GET['edit_cat'])): $edit_id = (int)$_GET['edit_cat']; $cat_edit = $conn->query("SELECT * FROM categories WHERE id=$edit_id")->fetch_assoc(); ?>
                    <form method="POST" action="admin.php?action=categories" style="margin-bottom: 20px; display: flex; gap: 10px; background: #ebf5fb; padding: 15px; border-radius: 4px;">
                        <input type="hidden" name="update_category" value="1"><input type="hidden" name="cat_id" value="<?= $edit_id ?>"><input type="text" name="cat_name" value="<?= htmlspecialchars($cat_edit['name']) ?>" required style="padding: 10px; width: 300px; border: 1px solid var(--secondary-color); border-radius: 4px;"><button type="submit" class="btn btn-secondary"><i class="fa fa-save"></i> Cập nhật</button><a href="admin.php?action=categories" class="btn" style="background:#ccc; color:#333;">Hủy</a>
                    </form>
                <?php else: ?>
                    <form method="POST" action="?action=categories" style="margin-bottom: 20px; display: flex; gap: 10px;">
                        <input type="text" name="new_category" placeholder="Tên danh mục mới..." required style="padding: 10px; width: 300px; border: 1px solid var(--border-color); border-radius: 4px;"><button type="submit" class="btn btn-success"><i class="fa fa-plus"></i> Thêm</button>
                    </form>
                <?php endif; 
                if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['new_category'])) { $c_name = $_POST['new_category']; $conn->query("INSERT INTO categories (name) VALUES ('$c_name')"); echo "<script>window.location.href='admin.php?action=categories';</script>"; } 
                $cats = $conn->query("SELECT * FROM categories ORDER BY id DESC"); 
                ?>
                <table>
                    <tr><th>ID</th><th>Tên danh mục</th><th>Thao tác</th></tr>
                    <?php if($cats->num_rows > 0): while($c = $cats->fetch_assoc()): ?>
                    <tr>
                        <td><?= $c['id'] ?></td><td><?= htmlspecialchars($c['name']) ?></td>
                        <td><a href="admin.php?action=categories&edit_cat=<?= $c['id'] ?>" style="color: var(--secondary-color); margin-right: 15px;"><i class="fa fa-edit"></i> Sửa</a> <a href="admin.php?delete_category=<?= $c['id'] ?>" onclick="return confirm('Xóa danh mục này?');" style="color: var(--primary-color);"><i class="fa fa-trash"></i> Xóa</a></td>
                    </tr>
                    <?php endwhile; else: ?>
                        <tr><td colspan="3" style="text-align:center; padding: 20px;">Không có kết quả.</td></tr>
                    <?php endif; ?>
                </table>
            </div>

        <?php elseif($action == 'customers'): ?>
            <h2>Danh Sách Khách Hàng</h2>
            <div class="table-wrapper">
                <?php $users = $conn->query("SELECT * FROM users WHERE role = 1 ORDER BY id DESC"); ?>
                <table>
                    <tr><th>ID</th><th>Họ Tên</th><th>Username</th><th>Email</th><th>Thao tác</th></tr>
                    <?php if($users->num_rows > 0): while($u = $users->fetch_assoc()): ?>
                    <tr>
                        <td><?= $u['id'] ?></td><td><?= htmlspecialchars($u['name']) ?></td><td><?= htmlspecialchars($u['username']) ?></td><td><?= htmlspecialchars($u['email']) ?></td>
                        <td><a href="admin.php?delete_customer=<?= $u['id'] ?>" onclick="return confirm('Xóa tài khoản này?');" style="color: var(--primary-color);"><i class="fa fa-trash"></i> Xóa</a></td>
                    </tr>
                    <?php endwhile; else: ?>
                        <tr><td colspan="5" style="text-align:center; padding: 20px;">Không có khách hàng.</td></tr>
                    <?php endif; ?>
                </table>
            </div>
            
        <?php elseif($action == 'order_detail'): 
            $oid = (int)$_GET['id']; $order = $conn->query("SELECT o.*, u.name, u.phone, u.address, u.email FROM orders o JOIN users u ON o.user_id = u.id WHERE o.id=$oid")->fetch_assoc(); $details = $conn->query("SELECT * FROM order_details WHERE order_id=$oid"); 
        ?>
            <h2><a href="admin.php?action=orders" style="color:var(--text-main);"><i class="fa fa-arrow-left"></i></a> Chi Tiết Đơn Hàng #<?= $oid ?></h2>
            <div class="variant-flex">
                <div class="table-wrapper" style="flex:1;"><h3><i class="fa fa-user"></i> Thông tin khách mua</h3><p><b>Họ tên:</b> <?= htmlspecialchars($order['name']) ?></p><p><b>Email:</b> <?= htmlspecialchars($order['email']) ?></p><p><b>SĐT:</b> <?= htmlspecialchars($order['phone']) ?></p><p><b>Địa chỉ:</b> <?= htmlspecialchars($order['address']) ?></p></div>
                <div class="table-wrapper" style="flex:1;"><h3><i class="fa fa-info-circle"></i> Trạng thái đơn</h3><p><b>Ngày đặt:</b> <?= $order['created_at'] ?></p><p><b>Tình trạng:</b> <span style="background:var(--bg-light); padding:5px; border-radius:4px; font-weight:bold;"><?= $order['status'] ?></span></p><p><b>Ghi chú:</b> <i><?= empty($order['note']) ? 'Không có' : htmlspecialchars($order['note']) ?></i></p></div>
            </div>
            <div class="table-wrapper">
                <h3 style="margin-top:0;"><i class="fa fa-list"></i> Mặt hàng</h3>
                <table>
                    <tr><th>Hình</th><th>Tên sản phẩm</th><th>Phân loại</th><th>SL</th><th>Đơn giá</th><th>Thành tiền</th></tr>
                    <?php while($d = $details->fetch_assoc()): ?>
                    <tr>
                        <td><img src="<?= $d['image_url'] ?>" width="50" style="border-radius:4px; object-fit:cover;"></td><td><?= htmlspecialchars($d['variant']) ?></td>
                        <td><?= explode(' - ', $d['variant'])[1] ?? 'Mặc định' ?></td><td><?= $d['quantity'] ?></td><td><?= number_format($d['price']) ?>đ</td><td style="font-weight:bold; color:var(--primary-color);"><?= number_format($d['price'] * $d['quantity']) ?>đ</td>
                    </tr>
                    <?php endwhile; ?>
                </table>
                <h3>Thuế VAT (10%): <?= number_format($order['vat_amount']) ?>đ</h3><h2>Tổng thu: <?= number_format($order['total_amount'] + $order['vat_amount']) ?>đ</h2>
            </div>
        <?php endif; ?>
    </div>
    
    <div id="systemModal" class="modal">
        <div class="modal-content" style="width:300px; text-align:center;">
            <span class="close-btn" onclick="document.getElementById('systemModal').style.display='none'">&times;</span>
            <i id="modalIcon" class="fa modal-icon"></i><h3 id="modalTitle"></h3><p id="modalMessage"></p>
            <button onclick="document.getElementById('systemModal').style.display='none'" class="btn btn-secondary">Đóng</button>
        </div>
    </div>
    <script>
        function toggleSelectAll(source, className) { let checkboxes = document.getElementsByClassName(className); for(let i=0; i<checkboxes.length; i++) { checkboxes[i].checked = source.checked; } }
        function showSysModal(type, title, message) {
            document.getElementById('systemModal').style.display = 'flex';
            document.getElementById('modalTitle').innerText = title; document.getElementById('modalMessage').innerText = message;
            document.getElementById('modalIcon').className = type === 'success' ? 'fa fa-check-circle modal-icon icon-success' : 'fa fa-exclamation-circle modal-icon icon-error';
        }
    </script>
    <?php if (isset($_SESSION['sys_msg'])): ?>
        <script>showSysModal('<?= $_SESSION['sys_msg']['type'] ?>', '<?= $_SESSION['sys_msg']['title'] ?>', '<?= $_SESSION['sys_msg']['msg'] ?>');</script>
        <?php unset($_SESSION['sys_msg']); ?>
    <?php endif; ?>
</body>
</html>