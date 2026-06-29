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
$cat_filter = isset($_GET['cat_filter']) ? (int)$_GET['cat_filter'] : 0;
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';
// NẠP FILE XỬ LÝ HÀNH ĐỘNG VÀ FILE TÍNH TOÁN THỐNG KÊ
require 'admin_actions.php';
require 'admin_stats.php';
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
.dashboard-cards { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px; align-items: stretch; margin-bottom: 35px; }
.dash-card { background: #fff; padding: 25px 20px; border-radius: 14px; box-shadow: 0 4px 15px rgba(0,0,0,0.02); display: flex; align-items: flex-start; justify-content: space-between; border: 1px solid var(--border-color); height: 100%; box-sizing: border-box; text-decoration: none; transition: all 0.3s ease;}

.dash-card:hover { transform: translateY(-4px); box-shadow: 0 10px 25px rgba(0,0,0,0.06); border-color: var(--secondary-color); }
.top-product-list { display: flex; flex-direction: column; gap: 12px; margin-top: 15px; width: 100%;}
.top-product-row { display: flex; align-items: center; gap: 10px; padding-bottom: 8px; border-bottom: 1px dashed #f1f2f6; }
.top-product-row:last-child { border-bottom: none; padding-bottom: 0; }
.top-product-img { object-fit: cover; border-radius: 6px; border: 1px solid var(--border-color); box-shadow: var(--shadow-sm); flex-shrink: 0;}

.top-product-name { flex: 1; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; color: var(--text-main); }
.top-product-row:nth-child(1) .top-product-img { width: 45px; height: 45px; border-color: #f1c40f; }
.top-product-row:nth-child(1) .top-product-name { font-size: 15px; font-weight: 800; }
.top-product-row:nth-child(2) .top-product-img { width: 38px; height: 38px; border-color: #bdc3c7; }
.top-product-row:nth-child(2) .top-product-name { font-size: 13px; font-weight: 600; }
.top-product-row:nth-child(3) .top-product-img { width: 30px; height: 30px; border-color: #cd6133; }

.top-product-row:nth-child(3) .top-product-name { font-size: 12px; font-weight: 500; }
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
<a href="admin.php?action=customers" class="<?= $action=='customers'?'active':'' ?>"><i class="fa fa-users"></i> Quản Lý Khách Hàng</a>

<a href="profile.php"><i class="fa fa-user-cog"></i> Tài khoản Admin</a>
<a href="index.php" style="margin-top:30px; color:#f1c40f;"><i class="fa fa-home"></i> Xem trang Web</a>
<!-- Nút Đăng xuất được thêm vào đây -->
<a href="logout.php" style="color: #e74c3c;"><i class="fa fa-sign-out-alt"></i> Đăng xuất</a>
</div>
<div class="admin-main">
<?php if($action == 'dashboard'): ?>
<h2>Tổng Overview Hệ Thống</h2>
<div class="dashboard-cards">
<div class="dash-card" style="align-items: center;"><div class="dash-card-info"><h4>Tổng Doanh Thu Lịch Sử</h4><h2><?= number_format($total_revenue_overall) ?>đ</h2></div><div class="dash-icon icon-green"><i class="fa fa-money-bill-wave"></i></div></div>

<a href="admin.php?action=customers" class="dash-card" style="align-items: center; text-decoration: none;">
<div class="dash-card-info"><h4>Tổng Khách Hàng</h4><h2><?= $total_users ?></h2></div>
<div class="dash-icon icon-blue"><i class="fa fa-users"></i></div>
</a>
<a href="admin.php?action=products&sort=best_seller" class="dash-card">
<div class="dash-card-info" style="width: 100%; margin-right: 10px; overflow: hidden;">

<h4 style="color: var(--text-main); font-size: 14px; font-weight: bold; margin: 0; text-transform: uppercase;">BÁN CHẠY NHẤT <i class="fa fa-external-link-alt" style="font-size: 10px; margin-left: 5px;"></i></h4>
<div class="top-product-list">
<?php $rank = 1; if($best_seller_query && $best_seller_query->num_rows > 0): while($bs = $best_seller_query->fetch_assoc()):
$img = !empty($bs['image']) ? $bs['image'] : 'https://via.placeholder.com/60';
$bg_color = ($rank == 1) ? '#f1c40f' : (($rank == 2) ? '#bdc3c7' : '#cd7f32');

?>
<div class="top-product-row">
<span style="background:<?= $bg_color ?>; color:#fff; font-size:10px; padding:2px 5px; border-radius:4px; font-weight:bold; white-space:nowrap;">Top <?= $rank++ ?></span>
<img src="<?= $img ?>" class="top-product-img" alt="Ảnh SP" onerror="this.src='https://via.placeholder.com/60';">
<span class="top-product-name" title="<?= htmlspecialchars($bs['name']) ?>"><?= htmlspecialchars($bs['name']) ?></span>
<div style="text-align: right; line-height: 1; flex-shrink: 0;">
<b style="font-size: 14px; color: var(--text-main);"><?= $bs['qty'] ?></b><br>

<span style="font-size: 10px; color: #7f8c8d;">cái</span>
</div>
</div>
<?php endwhile; else: echo "<p style='color:#888; font-size:13px; margin-top:10px;'>Chưa có dữ liệu</p>"; endif; ?>
</div>
</div>
<div class="dash-icon icon-orange"><i class="fa fa-fire"></i></div>
</a>
<a href="admin.php?action=products&sort=bad_seller" class="dash-card">
<div class="dash-card-info" style="width: 100%; margin-right: 10px; overflow: hidden;">
<h4 style="color: var(--text-main); font-size: 14px; font-weight: bold; margin: 0; text-transform: uppercase;">TỒN KHO / BÁN Ế <i class="fa fa-external-link-alt" style="font-size: 10px; margin-left: 5px;"></i></h4>

<div class="top-product-list">
<?php $rank = 1; if($worst_seller_query && $worst_seller_query->num_rows > 0): while($ws = $worst_seller_query->fetch_assoc()):
$img = !empty($ws['image']) ? $ws['image'] : 'https://via.placeholder.com/60';
$bg_color = ($rank == 1) ? '#e74c3c' : (($rank == 2) ? '#e67e22' : '#f39c12');
?>
<div class="top-product-row">
<span style="background:<?= $bg_color ?>; color:#fff; font-size:10px; padding:2px 5px; border-radius:4px; font-weight:bold; white-space:nowrap;">Top <?= $rank++ ?></span>

<img src="<?= $img ?>" class="top-product-img" alt="Ảnh SP" onerror="this.src='https://via.placeholder.com/60';">
<span class="top-product-name" title="<?= htmlspecialchars($ws['name']) ?>"><?= htmlspecialchars($ws['name']) ?></span>
<div style="text-align: right; line-height: 1; flex-shrink: 0;">
<b style="font-size: 14px; color: var(--text-main);"><?= $ws['qty'] ?></b><br>
<span style="font-size: 10px; color: #7f8c8d;">cái</span>
</div>
</div>
<?php endwhile; else: echo "<p style='color:#888; font-size:13px; margin-top:10px;'>Chưa có dữ liệu</p>"; endif; ?>

</div>
</div>
<div class="dash-icon icon-red"><i class="fa fa-snowflake"></i></div>
</a>
</div>
<div class="table-wrapper">
<div class="chart-header">
<h3><i class="fa fa-chart-bar" style="color: #3498db;"></i> Biểu Đồ Kế Toán Tự Động</h3>
<form method="GET" class="advanced-filter-form">
<input type="hidden" name="action" value="dashboard">
<div class="filter-input-group"><label><i class="fa fa-clock"></i> Chế độ lọc:</label><select name="filter" id="filterSelector" onchange="toggleFilterInputs()"><optgroup label="GIỜ CHUYÊN SÂU"><option value="hour" <?=$filter=='hour'?'selected':''?>>Theo 24 giờ qua</option><option value="spec_hour" <?=$filter=='spec_hour'?'selected':''?>>Chọn 1 giờ cụ thể</option><option value="range_hour" <?=$filter=='range_hour'?'selected':''?>>Khoảng giờ tùy chọn</option></optgroup><optgroup label="NGÀY CHUYÊN SÂU"><option value="day" <?=$filter=='day'?'selected':''?>>Theo 30 ngày qua</option><option value="spec_day" <?=$filter=='spec_day'?'selected':''?>>Chọn 1 ngày cụ thể</option><option value="range_day" <?=$filter=='range_day'?'selected':''?>>Khoảng ngày tùy chọn</option></optgroup><optgroup label="TUẦN CHUYÊN SÂU"><option value="week" <?=$filter=='week'?'selected':''?>>Theo 12 tuần qua</option><option value="spec_week" <?=$filter=='spec_week'?'selected':''?>>Chọn 1 tuần cụ thể</option><option value="range_week" <?=$filter=='range_week'?'selected':''?>>Khoảng tuần tùy chọn</option></optgroup><optgroup label="THÁNG CHUYÊN SÂU"><option value="month" <?=$filter=='month'?'selected':''?>>Theo 12 tháng qua</option><option value="spec_month" <?=$filter=='spec_month'?'selected':''?>>Chọn 1 tháng cụ thể</option><option value="range_month" <?=$filter=='range_month'?'selected':''?>>Khoảng tháng tùy chọn</option></optgroup><optgroup label="QUÝ CHUYÊN SÂU"><option value="quarter" <?=$filter=='quarter'?'selected':''?>>Theo 12 quý qua</option><option value="spec_quarter" <?=$filter=='spec_quarter'?'selected':''?>>Chọn 1 quý cụ thể</option><option value="range_quarter" <?=$filter=='range_quarter'?'selected':''?>>Khoảng quý tùy chọn</option></optgroup><optgroup label="NĂM CHUYÊN SÂU"><option value="year" <?=$filter=='year'?'selected':''?>>Theo 10 năm qua</option><option value="spec_year" <?=$filter=='spec_year'?'selected':''?>>Chọn 1 năm cụ thể</option><option value="range_year" <?=$filter=='range_year'?'selected':''?>>Khoảng năm tùy chọn</option></optgroup><optgroup label="THẾ KỶ CHUYÊN SÂU"><option value="century" <?=$filter=='century'?'selected':''?>>Theo thế kỷ</option><option value="spec_century" <?=$filter=='spec_century'?'selected':''?>>Chọn thế kỷ cụ thể</option><option value="range_century" <?=$filter=='range_century'?'selected':''?>>Khoảng thế kỷ tùy chọn</option></optgroup></select></div>

<div id="box_spec_hour" class="dynamic-input-box"><div class="filter-input-group"><label>Mốc giờ:</label><input type="datetime-local" name="spec_hour" value="<?=$spec_hour?>"></div></div><div id="box_range_hour" class="dynamic-input-box"><div class="filter-input-group"><label>Từ:</label><input type="datetime-local" name="from_hour" value="<?=$from_hour?>"></div><div class="filter-input-group"><label>Đến:</label><input type="datetime-local" name="to_hour" value="<?=$to_hour?>"></div></div><div id="box_spec_day" class="dynamic-input-box"><div class="filter-input-group"><label>Mốc ngày:</label><input type="date" name="spec_date" value="<?=$spec_date?>"></div></div><div id="box_range_day" class="dynamic-input-box"><div class="filter-input-group"><label>Từ:</label><input type="date" name="from_date" value="<?=$from_date?>"></div><div class="filter-input-group"><label>Đến:</label><input type="date" name="to_date" value="<?=$to_date?>"></div></div><div id="box_spec_week" class="dynamic-input-box"><div class="filter-input-group"><label>Mốc tuần:</label><input type="week" name="spec_week" value="<?=$spec_week?>"></div></div><div id="box_range_week" class="dynamic-input-box"><div class="filter-input-group"><label>Từ:</label><input type="week" name="from_week" value="<?=$from_week?>"></div><div class="filter-input-group"><label>Đến:</label><input type="week" name="to_week" value="<?=$to_week?>"></div></div><div id="box_spec_month" class="dynamic-input-box"><div class="filter-input-group"><label>Mốc tháng:</label><input type="month" name="spec_month" value="<?=$spec_month?>"></div></div><div id="box_range_month" class="dynamic-input-box"><div class="filter-input-group"><label>Từ:</label><input type="month" name="from_month" value="<?=$from_month?>"></div><div class="filter-input-group"><label>Đến:</label><input type="month" name="to_month" value="<?=$to_month?>"></div></div><div id="box_spec_quarter" class="dynamic-input-box"><div class="filter-input-group"><label>Năm:</label><input type="number" name="spec_q_year" value="<?=$spec_q_year?>" style="width:80px;"></div><div class="filter-input-group"><label>Quý:</label><select name="spec_quarter"><option value="1" <?=$spec_quarter==1?'selected':''?>>Q1</option><option value="2" <?=$spec_quarter==2?'selected':''?>>Q2</option><option value="3" <?=$spec_quarter==3?'selected':''?>>Q3</option><option value="4" <?=$spec_quarter==4?'selected':''?>>Q4</option></select></div></div><div id="box_range_quarter" class="dynamic-input-box"><div class="filter-input-group"><label>Từ:</label><input type="number" name="from_q_year" value="<?=$from_q_year?>" style="width:75px;"><select name="from_quarter"><option value="1" <?=$from_quarter==1?'selected':''?>>Q1</option><option value="2" <?=$from_quarter==2?'selected':''?>>Q2</option><option value="3" <?=$from_quarter==3?'selected':''?>>Q3</option><option value="4" <?=$from_quarter==4?'selected':''?>>Q4</option></select></div><div class="filter-input-group">

<label>Đến:</label><input type="number" name="to_q_year" value="<?=$to_q_year?>" style="width:75px;"><select name="to_quarter"><option value="1" <?=$to_quarter==1?'selected':''?>>Q1</option><option value="2" <?=$to_quarter==2?'selected':''?>>Q2</option><option value="3" <?=$to_quarter==3?'selected':''?>>Q3</option><option value="4" <?=$to_quarter==4?'selected':''?>>Q4</option></select></div></div><div id="box_spec_year" class="dynamic-input-box"><div class="filter-input-group"><label>Mốc năm:</label><input type="number" name="spec_year" value="<?=$spec_year?>" min="2000" max="2099"></div></div><div id="box_range_year" class="dynamic-input-box"><div class="filter-input-group"><label>Từ:</label><input type="number" name="from_year" value="<?=$from_year?>" min="2000" max="2099"></div><div class="filter-input-group"><label>Đến:</label><input type="number" name="to_year" value="<?=$to_year?>" min="2000" max="2099"></div></div><div id="box_spec_century" class="dynamic-input-box"><div class="filter-input-group"><label>Mốc TK:</label><input type="number" name="spec_century" value="<?=$spec_century?>"></div></div><div id="box_range_century" class="dynamic-input-box"><div class="filter-input-group"><label>Từ TK:</label><input type="number" name="from_century" value="<?=$from_century?>"></div><div class="filter-input-group"><label>Đến TK:</label><input type="number" name="to_century" value="<?=$to_century?>"></div></div>

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
<?php
$where_promo = "";
if ($search != '') {
$where_promo = "WHERE code LIKE '%$escaped_search%'";
}
$promos = $conn->query("SELECT * FROM promotions $where_promo ORDER BY id DESC");

?>
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
<h3 style="margin: 0;"><i class="fa fa-list"></i> Danh sách mã giảm giá</h3>
<form method="GET" action="admin.php" style="display: flex; gap: 10px;">
<input type="hidden" name="action" value="promotions">
<input type="hidden" name="tab" value="vouchers">
<input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Tìm mã code..." style="padding: 10px; width: 250px; border: 1px solid var(--border-color); border-radius: 4px;">

<button type="submit" class="btn btn-secondary"><i class="fa fa-search"></i> Tìm</button>
<?php if($search != '' && $tab == 'vouchers'): ?><a href="admin.php?action=promotions&tab=vouchers" class="btn" style="background:#ccc; color:#333;">Hủy</a><?php endif; ?>
</form>
</div>
<table>
<tr><th>Mã Code</th><th>Loại Voucher</th><th>Mức Giảm / Min Đơn</th><th>Đã dùng</th><th>Thời gian áp dụng</th><th>Thao tác</th></tr>
<?php if($promos->num_rows > 0): while($pr = $promos->fetch_assoc()): ?>

<tr>
<td><b style="color:var(--primary-color); background:#ffeaa7; padding:4px 8px; border-radius:4px;"><?= $pr['code'] ?></b></td>
<td><?= ($pr['is_freeship']==1) ? '<b style="color:var(--secondary-color);"><i class="fa fa-shipping-fast"></i> FREESHIP</b>' : 'Giảm tiền đơn' ?></td>
<td><?= ($pr['is_freeship']==1) ? 'Giảm 30k' : ($pr['discount_amount'] > 0 ? number_format($pr['discount_amount']).'đ' : $pr['discount_percent'].'%') ?><br><small>Min: <?= number_format($pr['min_order']) ?>đ</small></td>

<td><?= $pr['used_count'] ?> / <?= $pr['usage_limit'] ?></td>
<td><small>Từ: <?= date('d/m/Y H:i', strtotime($pr['start_date'])) ?><br>Đến: <b style="color:red;"><?= date('d/m/Y H:i', strtotime($pr['end_date'])) ?></b></small></td>
<td><a href="admin.php?action=promotions&tab=vouchers&edit_promo=<?= $pr['id'] ?>" style="color:var(--secondary-color); margin-right:10px;"><i class="fa fa-edit"></i> Sửa</a><a href="admin.php?action=promotions&delete_promo=<?= $pr['id'] ?>" onclick="return confirm('Xóa mã này?');" style="color:var(--primary-color);"><i class="fa fa-trash"></i> Xóa</a></td>

</tr>
<?php endwhile; else: ?>
<tr><td colspan="6" style="text-align:center; padding: 20px;">Không tìm thấy mã giảm giá.</td></tr>
<?php endif; ?>
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
<?php $all_p = $conn->query("SELECT id, name, price FROM products WHERE is_hidden=0 ORDER BY id DESC"); ?>
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
<?php $all_p = $conn->query("SELECT id, name, price FROM products WHERE is_hidden=0 ORDER BY id DESC"); ?>

<?php while($p_row = $all_p->fetch_assoc()): ?>
<label><input type="checkbox" name="campaign_products[]" value="<?= $p_row['id'] ?>"> <b>[ID: <?= $p_row['id'] ?>]</b> <?= htmlspecialchars($p_row['name']) ?> - <?= number_format($p_row['price']) ?>đ</label>
<?php endwhile; ?>
</div>
</div>
<button type="submit" name="add_campaign" class="btn btn-primary" style="margin-top: 15px;"><i class="fa fa-bolt"></i> Tạo Chiến Dịch</button>
</form>
<?php endif; ?>
<?php
$where_camp = "";
if ($search != '') {
$where_camp = "WHERE name LIKE '%$escaped_search%'";
}

$campaigns = $conn->query("SELECT * FROM campaigns $where_camp ORDER BY id DESC");
?>
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
<h3 style="margin: 0;"><i class="fa fa-list"></i> Các chương trình khuyến mãi hiện có</h3>
<form method="GET" action="admin.php" style="display: flex; gap: 10px;">
<input type="hidden" name="action" value="promotions">
<input type="hidden" name="tab" value="campaigns">
<input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Tìm tên chương trình..." style="padding: 10px; width: 250px; border: 1px solid var(--border-color); border-radius: 4px;">

<button type="submit" class="btn btn-secondary"><i class="fa fa-search"></i> Tìm</button>
<?php if($search != '' && $tab == 'campaigns'): ?><a href="admin.php?action=promotions&tab=campaigns" class="btn" style="background:#ccc; color:#333;">Hủy</a><?php endif; ?>
</form>
</div>
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
<tr><td colspan="5" style="text-align:center; padding: 20px; color: #888;">Không tìm thấy chiến dịch khuyến mãi nào.</td></tr>
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
<form method="GET" action="admin.php" style="margin-bottom: 15px; display: flex; gap: 10px;">
<input type="hidden" name="action" value="orders">
<input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Tìm mã ĐH, tên khách, SĐT..." style="padding: 10px; width: 300px; border: 1px solid var(--border-color); border-radius: 4px;">
<button type="submit" class="btn btn-secondary"><i class="fa fa-search"></i> Tìm kiếm</button>

<?php if($search != ''): ?><a href="admin.php?action=orders" class="btn" style="background:#ccc; color:#333;">Hủy tìm</a><?php endif; ?>
</form>
<?php
$where_orders = "";
if ($search != '') {
$where_orders = "WHERE o.id = '$escaped_search' OR u.name LIKE '%$escaped_search%' OR u.phone LIKE '%$escaped_search%'";
}
$ords = $conn->query("SELECT o.*, u.name as customer_name FROM orders o JOIN users u ON o.user_id = u.id $where_orders ORDER BY o.created_at DESC LIMIT 50");
?>
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
<tr><td colspan="6" style="text-align:center; padding: 20px;">Không tìm thấy đơn hàng.</td></tr>
<?php endif; ?>
</table>
</div>
<?php elseif($action == 'products'): ?>
<h2>Quản Lý Sản Phẩm & Xếp Hạng</h2>
<div class="table-wrapper">
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; flex-wrap: wrap; gap: 10px;">

<a href="add_product.php" class="btn btn-success"><i class="fa fa-plus"></i> Thêm Sản Phẩm</a>
<form method="GET" action="admin.php" style="display: flex; gap: 10px; flex-wrap: wrap; align-items: center;">
<input type="hidden" name="action" value="products">
<select name="cat_filter" onchange="this.form.submit()" style="padding: 10px; border: 1px solid var(--border-color); border-radius: 4px; outline: none; background: #fff; font-weight: bold; color: var(--secondary-color);">

<option value="0">-- Tất cả danh mục --</option>
<?php
$all_cats = $conn->query("SELECT * FROM categories ORDER BY name ASC");
while($c = $all_cats->fetch_assoc()):
?>
<option value="<?= $c['id'] ?>" <?= $cat_filter == $c['id'] ? 'selected' : '' ?>><?= htmlspecialchars($c['name']) ?></option>
<?php endwhile; ?>
</select>
<select name="sort" onchange="this.form.submit()" style="padding: 10px; border: 1px solid var(--border-color); border-radius: 4px; outline: none; background: #fffdf5; font-weight: bold;">

<option value="newest" <?= $sort=='newest'?'selected':'' ?>>Sắp xếp: Mới nhất</option>
<option value="best_seller" <?= $sort=='best_seller'?'selected':'' ?>>🔥 Sản phẩm Bán Chạy Nhất</option>
<option value="bad_seller" <?= $sort=='bad_seller'?'selected':'' ?>>❄️ Sản phẩm Tồn Kho / Bán Ế</option>
</select>
<input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Tìm tên sản phẩm, ID..." style="padding: 10px; width: 250px; border: 1px solid var(--border-color); border-radius: 4px;">

<button type="submit" class="btn btn-secondary"><i class="fa fa-search"></i> Tìm kiếm</button>
<?php if($search != '' || $cat_filter > 0 || $sort != 'newest'): ?>
<a href="admin.php?action=products" class="btn" style="background:#ccc; color:#333;"><i class="fa fa-times"></i> Xóa bộ lọc</a>
<?php endif; ?>
</form>
</div>
<form action="admin.php" method="POST">
<div class="bulk-action-bar">
<strong><i class="fa fa-check-square"></i> Hành động:</strong>
<select name="bulk_action" id="bulkActionSelector" required><option value="">-- Chọn thao tác --</option><option value="discount_percent">Giảm giá theo %</option><option value="discount_amount">Giảm tiền mặt trực tiếp</option><option value="reset_discount">Hủy tất cả giảm giá (Về giá gốc)</option><option value="delete">Xóa hàng loạt</option></select>

<input type="number" name="discount_value" id="discountValueInput" placeholder="Nhập số % hoặc tiền..." style="display:none; width: 150px;">
<button type="submit" name="apply_bulk_action" class="btn btn-primary"><i class="fa fa-bolt"></i> Áp dụng</button>
</div>
<?php
// ĐÃ SỬA: Lọc bỏ hiển thị các sản phẩm đã ấn (is_hidden = 0)
$where_arr = ["p.is_hidden = 0"];
if ($search != '') {
$where_arr[] = "(p.name LIKE '%$escaped_search%' OR p.id = '$escaped_search')";
}
if ($cat_filter > 0) {
$where_arr[] = "p.category_id = $cat_filter";
}
$where_products = !empty($where_arr) ? "WHERE " . implode(" AND ", $where_arr) : "";

$order_by = "p.id DESC";
if ($sort == 'best_seller') $order_by = "total_sold DESC, p.id DESC";
elseif ($sort == 'bad_seller') $order_by = "total_sold ASC, p.id DESC";
$prods = $conn->query("SELECT p.*, c.name as cat_name,
(SELECT COALESCE(SUM(stock), 0) FROM product_variants WHERE product_id = p.id) as total_stock,
(SELECT COALESCE(SUM(od.quantity), 0) FROM order_details od JOIN orders o ON od.order_id = o.id WHERE od.product_id = p.id AND o.status = 'Hoàn thành') as total_sold

FROM products p LEFT JOIN categories c ON p.category_id = c.id $where_products ORDER BY $order_by");
?>
<table>
<tr><th style="width: 40px;"><input type="checkbox" onclick="toggleSelectAll(this, 'product_checkbox')"></th><th>ID</th><th>Hình ảnh</th><th>Tên sản phẩm</th><th>Danh mục</th><th>Giá hiện tại</th><th>Đã bán</th><th>Tồn kho</th><th>Thao tác</th></tr>
<?php
$rank = 1;
if($prods->num_rows > 0): while($p = $prods->fetch_assoc()):
$img_src = !empty($p['image']) ? $p['image'] : 'https://via.placeholder.com/50';

$bg_rank = '';
if ($sort == 'best_seller' && $p['total_sold'] > 0) {
if ($rank == 1) $bg_rank = 'background: #f1c40f; color: white;';
elseif ($rank == 2) $bg_rank = 'background: #bdc3c7; color: white;';
elseif ($rank == 3) $bg_rank = 'background: #cd7f32; color: white;';
}
?>
<tr>
<td><input type="checkbox" name="selected_products[]" value="<?= $p['id'] ?>" class="product_checkbox"></td>
<td><span style="<?= $bg_rank ?> padding: 3px 6px; border-radius: 4px; font-weight: <?= $bg_rank!=''?'bold':'normal' ?>;"><?= ($bg_rank != '') ? 'Top '.$rank++ : $p['id'] ?></span></td>

<td><img src="<?= $img_src ?>" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px; border: 1px solid var(--border-color);" alt="Ảnh"></td>
<td><b style="<?= ($sort == 'bad_seller' && $p['total_sold'] == 0) ? 'color:#e74c3c;' : '' ?>"><?= htmlspecialchars($p['name']) ?></b></td>
<td><?= $p['cat_name'] ?? '<i style="color:#aaa;">Chưa có</i>' ?></td>
<td><?php if($p['sale_price'] > 0): ?><span class="price-original"><?= number_format($p['price']) ?>đ</span><span class="price-sale"><?= number_format($p['sale_price']) ?>đ</span><?php else: ?><b style="color:var(--primary-color);"><?= number_format($p['price']) ?>đ</b><?php endif; ?></td>

<td>
<?php if ($p['total_sold'] > 0): ?>
<b style="color: var(--success-color); font-size: 16px;"><i class="fa fa-arrow-trend-up"></i> <?= $p['total_sold'] ?></b>
<?php else: ?>
<span style="color: #e74c3c; font-size: 12px; font-weight:bold;"><i class="fa fa-snowflake"></i> 0 (Ế)</span>
<?php endif; ?>
</td>
<td><span style="background:#e1f5fe; color:#0984e3; padding:4px 10px; border-radius:4px; font-weight:bold;"><?= $p['total_stock'] ?></span></td>
<td style="white-space:nowrap;">

<a target="_blank" href="product_detail.php?id=<?= $p['id'] ?>" style="color:var(--success-color); margin-right:10px;"><i class="fa fa-eye"></i> Xem</a>
<a href="edit_product.php?id=<?= $p['id'] ?>" style="color:var(--secondary-color); margin-right:10px;"><i class="fa fa-edit"></i> Sửa</a>
<a href="admin.php?delete_product=<?= $p['id'] ?>" onclick="return confirm('Ẩn sản phẩm này khỏi hệ thống?');" style="color:var(--primary-color);"><i class="fa fa-trash"></i> Ẩn</a>
</td>
</tr>
<?php
if ($sort == 'best_seller' && $p['total_sold'] == 0) continue;

endwhile; else: ?>
<tr><td colspan="9" style="text-align:center; padding: 20px;">Không tìm thấy sản phẩm.</td></tr>
<?php endif; ?>
</table>
</form>
</div>
<script>document.getElementById('bulkActionSelector').addEventListener('change', function() { let v = document.getElementById('discountValueInput'); if (this.value==='discount_percent' || this.value==='discount_amount') { v.style.display='block'; v.required=true; } else { v.style.display='none'; v.required=false; } });</script>
<?php elseif($action == 'categories'): ?>

<h2>Quản Lý Danh Mục</h2>
<div class="table-wrapper">
<div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px;">
<?php if (isset($_GET['edit_cat'])): $edit_id = (int)$_GET['edit_cat']; $cat_edit = $conn->query("SELECT * FROM categories WHERE id=$edit_id")->fetch_assoc(); ?>
<form method="POST" action="admin.php?action=categories" style="display: flex; gap: 10px; background: #ebf5fb; padding: 15px; border-radius: 4px; flex: 1; margin-right: 20px;">

<input type="hidden" name="update_category" value="1"><input type="hidden" name="cat_id" value="<?= $edit_id ?>"><input type="text" name="cat_name" value="<?= htmlspecialchars($cat_edit['name']) ?>" required style="padding: 10px; width: 250px; border: 1px solid var(--secondary-color); border-radius: 4px;"><button type="submit" class="btn btn-secondary"><i class="fa fa-save"></i> Cập nhật</button><a href="admin.php?action=categories" class="btn" style="background:#ccc; color:#333;">Hủy</a>
</form>

<?php else: ?>
<form method="POST" action="?action=categories" style="display: flex; gap: 10px; flex: 1; margin-right: 20px;">
<input type="text" name="new_category" placeholder="Tên danh mục mới..." required style="padding: 10px; width: 250px; border: 1px solid var(--border-color); border-radius: 4px;"><button type="submit" class="btn btn-success"><i class="fa fa-plus"></i> Thêm</button>
</form>
<?php endif; ?>
<form method="GET" action="admin.php" style="display: flex; gap: 10px;">

<input type="hidden" name="action" value="categories">
<input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Tìm tên danh mục..." style="padding: 10px; width: 250px; border: 1px solid var(--border-color); border-radius: 4px;">
<button type="submit" class="btn btn-secondary"><i class="fa fa-search"></i> Tìm kiếm</button>
<?php if($search != ''): ?><a href="admin.php?action=categories" class="btn" style="background:#ccc; color:#333;">Hủy tìm</a><?php endif; ?>

</form>
</div>
<?php
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['new_category'])) { $c_name = $_POST['new_category']; $conn->query("INSERT INTO categories (name) VALUES ('$c_name')"); echo "<script>window.location.href='admin.php?action=categories';</script>"; }
$where_cats = "";
if ($search != '') {
$where_cats = "WHERE c.name LIKE '%$escaped_search%' OR c.id = '$escaped_search'";
}
$cats_query = "SELECT c.id, c.name,
(SELECT COUNT(id) FROM products WHERE category_id = c.id) as product_count,

(SELECT COALESCE(SUM(od.quantity), 0) FROM order_details od JOIN products p ON od.product_id = p.id JOIN orders o ON od.order_id = o.id WHERE p.category_id = c.id AND o.status = 'Hoàn thành') as total_sold
FROM categories c $where_cats ORDER BY c.id DESC";
$cats = $conn->query($cats_query);
?>
<table>
<tr><th>ID</th><th>Tên danh mục</th><th>Số SP</th><th>Đã bán</th><th style="min-width: 320px;">Sản phẩm HOT nhất</th><th>Thao tác</th></tr>
<?php if($cats->num_rows > 0): while($c = $cats->fetch_assoc()):

$cat_id = $c['id'];
$top_p_query = $conn->query("SELECT p.name, p.image, SUM(od.quantity) as sold FROM products p JOIN order_details od ON p.id = od.product_id JOIN orders o ON od.order_id = o.id WHERE p.category_id = $cat_id AND o.status = 'Hoàn thành' GROUP BY p.id ORDER BY sold DESC LIMIT 1");
$top_p = $top_p_query->fetch_assoc();
?>
<tr>
<td><?= $c['id'] ?></td>
<td><a href="admin.php?action=products&cat_filter=<?= $c['id'] ?>" style="font-size: 16px; font-weight: bold; color: var(--primary-color); text-decoration: underline;" title="Nhấn để xem các sản phẩm của danh mục này"><?= htmlspecialchars($c['name']) ?></a></td>

<td><b style="color:var(--secondary-color);"><?= $c['product_count'] ?></b> sp</td>
<td><b style="color:var(--success-color);"><?= $c['total_sold'] ?></b> cái</td>
<td>
<?php if($top_p): ?>
<div style="display:flex; align-items:center; justify-content:space-between; gap:10px; background:#fffdf5; padding:5px 10px; border-radius:6px; border:1px dashed #f39c12;">
<div style="display:flex; align-items:center; gap:10px;">
<img src="<?= !empty($top_p['image']) ? $top_p['image'] : 'https://via.placeholder.com/40' ?>" style="width:40px; height:40px; border-radius:4px; object-fit:cover;">

<div style="overflow:hidden;">
<span style="font-size:11px; font-weight:bold; color:#fff; background:#e74c3c; padding:2px 5px; border-radius:4px;"><i class="fa fa-fire"></i> HOT (Bán <?= $top_p['sold'] ?>)</span><br>
<span style="font-size:13px; font-weight:bold; color:var(--text-main); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; display:block; margin-top:3px; max-width:140px;" title="<?= htmlspecialchars($top_p['name']) ?>"><?= htmlspecialchars($top_p['name']) ?></span>

</div>
</div>
<a href="admin.php?action=products&cat_filter=<?= $cat_id ?>&sort=best_seller" class="btn btn-secondary" style="padding: 5px 10px; font-size: 11px; white-space: nowrap; height: fit-content;"><i class="fa fa-trophy"></i> Xem BXH</a>
</div>
<?php else: ?>
<span style="color:#999; font-style:italic; font-size:13px;">Chưa có dữ liệu</span>
<?php endif; ?>
</td>
<td style="white-space:nowrap;">
<a href="admin.php?action=products&cat_filter=<?= $c['id'] ?>" style="color: var(--success-color); margin-right: 12px;"><i class="fa fa-eye"></i> Xem SP</a>

<a href="admin.php?action=categories&edit_cat=<?= $c['id'] ?>" style="color: var(--secondary-color); margin-right: 12px;"><i class="fa fa-edit"></i> Sửa</a>
<a href="admin.php?delete_category=<?= $c['id'] ?>" onclick="return confirm('Xóa danh mục này?');" style="color: var(--primary-color);"><i class="fa fa-trash"></i> Xóa</a>
</td>
</tr>
<?php endwhile; else: ?>
<tr><td colspan="6" style="text-align:center; padding: 20px;">Không có kết quả.</td></tr>
<?php endif; ?>
</table>
</div>

<?php elseif($action == 'customers'): ?>
<h2>Quản Lý Khách Hàng</h2>
<div class="table-wrapper">
<form method="GET" action="admin.php" style="margin-bottom: 15px; display: flex; gap: 10px;">
<input type="hidden" name="action" value="customers">
<input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Tìm tên, email, username, SĐT..." style="padding: 10px; width: 300px; border: 1px solid var(--border-color); border-radius: 4px;">
<button type="submit" class="btn btn-secondary"><i class="fa fa-search"></i> Tìm kiếm</button>

<?php if($search != ''): ?><a href="admin.php?action=customers" class="btn" style="background:#ccc; color:#333;">Hủy tìm</a><?php endif; ?>
</form>
<?php
$where_users = "WHERE role = 1";
if ($search != '') {
$where_users .= " AND (name LIKE '%$escaped_search%' OR email LIKE '%$escaped_search%' OR phone LIKE '%$escaped_search%' OR username LIKE '%$escaped_search%')";
}
$users = $conn->query("SELECT * FROM users $where_users ORDER BY id DESC");
?>
<table>
<tr><th>ID</th><th>Họ Tên</th><th>Username</th><th>Email</th><th>Thao tác</th></tr>

<?php if($users->num_rows > 0): while($u = $users->fetch_assoc()): ?>
<tr>
<td><?= $u['id'] ?></td><td><?= htmlspecialchars($u['name']) ?></td><td><?= htmlspecialchars($u['username']) ?></td><td><?= htmlspecialchars($u['email']) ?></td>
<td><a href="admin.php?delete_customer=<?= $u['id'] ?>" onclick="return confirm('Xóa tài khoản này?');" style="color: var(--primary-color);"><i class="fa fa-trash"></i> Xóa</a></td>
</tr>
<?php endwhile; else: ?>
<tr><td colspan="5" style="text-align:center; padding: 20px;">Không tìm thấy khách hàng.</td></tr>

<?php endif; ?>
</table>
</div>
<?php elseif($action == 'order_detail'):
$oid = (int)$_GET['id'];
$order = $conn->query("SELECT o.*, u.name, u.phone, u.address, u.email FROM orders o JOIN users u ON o.user_id = u.id WHERE o.id=$oid")->fetch_assoc();
// SỬA LỖI: JOIN thêm bảng products để lấy đúng tên sản phẩm
$details = $conn->query("SELECT od.*, p.name AS product_name FROM order_details od LEFT JOIN products p ON od.product_id = p.id WHERE od.order_id=$oid");
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
<td><img src="<?= $d['image_url'] ?>" width="50" style="border-radius:4px; object-fit:cover;"></td>
<td><?= htmlspecialchars($d['product_name'] ?? 'Sản phẩm đã bị xóa') ?></td>
<td><?= htmlspecialchars($d['variant'] ?? 'Mặc định') ?></td>
<td><?= $d['quantity'] ?></td><td><?= number_format($d['price']) ?>đ</td><td style="font-weight:bold; color:var(--primary-color);"><?= number_format($d['price'] * $d['quantity']) ?>đ</td>

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
}

document.getElementById('modalIcon').className = type === 'success' ? 'fa fa-check-circle modal-icon icon-success' : 'fa fa-exclamation-circle modal-icon icon-error';
}
// BỘ LỌC TÌM KIẾM REAL-TIME
document.addEventListener('DOMContentLoaded', function() {
const searchInputs = document.querySelectorAll('input[name="search"]');
searchInputs.forEach(input => {
input.setAttribute('autocomplete', 'off');
input.addEventListener('keyup', function() {
let filterWord = this.value.toLowerCase();

let tableWrapper = this.closest('.table-wrapper');
if(tableWrapper) {
let table = tableWrapper.querySelector('table');
if(table) {
let rows = table.getElementsByTagName('tr');
for (let i = 1; i < rows.length; i++) {
let rowText = rows[i].textContent || rows[i].innerText;
if (rowText.toLowerCase().indexOf(filterWord) > -1) { rows[i].style.display = ""; }
else { rows[i].style.display = "none"; }
}

}
}
});
let form = input.closest('form');
if(form) { form.addEventListener('submit', function(e) { e.preventDefault(); }); }
});
});
</script>
<?php if (isset($_SESSION['sys_msg'])): ?>
<script>showSysModal('<?= $_SESSION['sys_msg']['type'] ?>', '<?= $_SESSION['sys_msg']['title'] ?>', '<?= $_SESSION['sys_msg']['msg'] ?>');</script>
<?php unset($_SESSION['sys_msg']); ?>
<?php endif; ?>
</body>
</html>