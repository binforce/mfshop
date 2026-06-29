<?php
// ==========================================
// TÍNH TOÁN DỮ LIỆU CHUNG & BADGE THÔNG BÁO ADMIN
// ==========================================
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

    $best_seller_query = $conn->query("SELECT p.name, p.image, SUM(od.quantity) as qty FROM order_details od JOIN products p ON od.product_id = p.id JOIN orders o ON od.order_id = o.id WHERE o.status = 'Hoàn thành' GROUP BY p.id ORDER BY qty DESC LIMIT 3");
    $worst_seller_query = $conn->query("SELECT p.name, p.image, COALESCE(SUM(od.quantity), 0) as qty FROM products p LEFT JOIN order_details od ON p.id = od.product_id LEFT JOIN orders o ON od.order_id = o.id AND o.status = 'Hoàn thành' GROUP BY p.id ORDER BY qty ASC LIMIT 3");
}
?>