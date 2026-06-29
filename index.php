<?php
session_start();
require 'db_connect.php';

// Nạp file xử lý logic truy vấn dữ liệu trang chủ
require 'index_logic.php';
?>
<!DOCTYPE html>
<html>
<head>
<title>MF SHOP - Thời trang cao cấp chuyên nghiệp</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="style.css?v=<?= time() ?>">
<style>.grid-item-normal { flex: none !important; width: auto !important; max-width: 100% !important; }</style>
</head>
<body>
<div class="toolbar">
<div class="logo">
<a href="index.php" style="display: flex; align-items: center; gap: 8px; font-size: 26px; font-weight: 900; color: var(--primary-color); letter-spacing: 1px; text-decoration: none;">
<i class="fa fa-shopping-bag"></i> MF SHOP
</a>
</div>
<form action="index.php" method="GET" class="search-bar" style="position: relative;">
<?php if($cat_id > 0): ?><input type="hidden" name="cat_id" value="<?= $cat_id ?>"><?php endif; ?>
<!-- Thêm ID và tắt autocomplete -->
<input type="text" name="search" id="live_search_input" autocomplete="off" value="<?= htmlspecialchars($search) ?>" placeholder="Tìm kiếm sản phẩm, thương hiệu thời trang...">
<button type="submit"><i class="fa fa-search"></i></button>

<!-- Hộp Dropdown hiển thị kết quả Real-time -->
<div id="search_suggest" style="display:none; position:absolute; top:110%; left:0; width:100%; background:#fff; border:1px solid var(--border-color); border-radius:8px; box-shadow:var(--shadow-md); z-index:9999; max-height:400px; overflow-y:auto;"></div>
</form>
<div class="action-group">
<a href="notifications.php" class="action-icon" title="Thông báo"><i class="fa fa-bell"></i><?php if($unread_noti > 0): ?><span class="badge"><?= $unread_noti ?></span><?php endif; ?></a>
<a href="cart.php" class="action-icon" title="Giỏ hàng"><i class="fa fa-shopping-cart"></i></a>
<?php if (isset($_SESSION['user_id'])): ?>
<a href="orders.php" class="action-icon" title="Đơn hàng của tôi"><i class="fa fa-box"></i></a>
<?php endif; ?>
<?php if (isset($_SESSION['user_id'])): ?>
<div class="user-dropdown">
<img src="<?= $user_avatar ?>" class="avatar-btn" alt="User Avatar" onerror="this.src='https://via.placeholder.com/150';">
<div class="dropdown-content">
<?php if ($_SESSION['role'] == 0): ?><a href="admin.php"><i class="fa fa-tachometer-alt"></i> Quản trị hệ thống</a><?php endif; ?>
<a href="profile.php"><i class="fa fa-user-edit"></i> Tài khoản cá nhân</a>
<a href="orders.php"><i class="fa fa-clipboard-list"></i> Quản lý đơn hàng</a>
<a href="logout.php" style="color: var(--primary-color);"><i class="fa fa-sign-out-alt"></i> Đăng xuất</a>
</div>
</div>
<?php else: ?>
<div style="display: flex; gap: 10px;">
    <a href="login.php" class="btn btn-primary"><i class="fa fa-sign-in-alt"></i> Đăng nhập</a>
    <a href="register.php" class="btn btn-success"><i class="fa fa-user-plus"></i> Đăng ký</a>
</div>
<?php endif; ?>
</div>
</div>
<div class="main-container" style="min-height: 70vh;">
<div class="sidebar">
<h3><i class="fa fa-bars"></i> Danh Mục</h3>
<ul>
<li><a href="index.php" style="<?= ($cat_id==0 && $search=='') ? 'color:var(--primary-color); font-weight:bold;' : '' ?>">Tất cả sản phẩm <i class="fa fa-angle-right"></i></a></li>
<?php while($cat = $categories->fetch_assoc()): ?>
<li><a href="index.php?cat_id=<?= $cat['id'] ?><?= ($search !== '') ? '&search='.urlencode($search) : '' ?>" style="<?= ($cat_id==$cat['id']) ? 'color:var(--primary-color); font-weight:bold;' : '' ?>"><?= htmlspecialchars($cat['name']) ?> <i class="fa fa-angle-right"></i></a></li>
<?php endwhile; ?>
</ul>
</div>
<div class="content-area">
<?php
$variants_data = [];
$v_query = $conn->query("SELECT product_id, color, size, stock FROM product_variants");
while($v = $v_query->fetch_assoc()){ $variants_data[$v['product_id']][] = $v; }
?>
<!-- SLIDER SẢN PHẨM NỔI BẬT (CHỈ HIỆN Ở TRANG CHỦ) -->
<?php if($show_slider && $best_sellers && $best_sellers->num_rows > 0): ?>
<div class="featured-section">
<h2 style="margin-top:0; font-size:20px; color: #2c3e50; margin-bottom: 15px;"><i class="fa fa-fire" style="color:var(--primary-color);"></i> SẢN PHẨM BÁN CHẠY NHẤT</h2>
<div class="slider-container-box">
<button class="arrow-btn-ctrl arrow-left-pos" onclick="moveSliderLeft()"><i class="fa fa-chevron-left"></i></button>
<div class="slider-track-wrapper" id="featuredSlider">
<?php while($bpro = $best_sellers->fetch_assoc()):
$bid = $bpro['id']; $bimg = !empty($bpro['image']) ? $bpro['image'] : 'https://via.placeholder.com/230x220?text=MF+Shop';
$bjson = htmlspecialchars(json_encode(isset($variants_data[$bid]) ? $variants_data[$bid] : []), ENT_QUOTES, 'UTF-8');
$real_price_slider = (isset($bpro['sale_price']) && $bpro['sale_price'] > 0) ? $bpro['sale_price'] : $bpro['price'];
?>
<div class="product-item slider-item-special">
<!-- BỌC ẢNH VÀ NHÃN VÀO KHUNG RELATIVE CỐ ĐỊNH CHIỀU CAO -->
<div style="position: relative; min-height: 240px; background: #f8f9fa; border-radius: 8px; margin-bottom: 14px;">
    <span style="position:absolute; top:10px; left:10px; background:var(--primary-color); color:white; font-size:11px; padding:3px 8px; border-radius:20px; font-weight:bold; z-index:5;"><i class="fa fa-bolt"></i> HOT</span>
    <?php if(isset($bpro['sale_price']) && $bpro['sale_price'] > 0 && $bpro['sale_price'] < $bpro['price']): ?>
    <?php $percent_off = round((($bpro['price'] - $bpro['sale_price']) / $bpro['price']) * 100); ?>
    <span style="position:absolute; top:10px; right:10px; background:#e74c3c; color:white; font-size:12px; padding:3px 6px; border-radius:4px; font-weight:bold; z-index:5;">-<?= $percent_off ?>%</span>
    <?php endif; ?>
    <a href="product_detail.php?id=<?= $bid ?>">
        <img src="<?= $bimg ?>" alt="<?= htmlspecialchars($bpro['name']) ?>" onerror="this.onerror=null; this.src='https://via.placeholder.com/230x220?text=No+Image';" style="margin-bottom:0;">
    </a>
</div>
<a href="product_detail.php?id=<?= $bid ?>"><h4><?= htmlspecialchars($bpro['name']) ?></h4></a>
<div class="product-price" style="min-height: 45px; display: flex; flex-direction: column; align-items: center; justify-content: center; margin-bottom: 10px;">
<?php if(isset($bpro['sale_price']) && $bpro['sale_price'] > 0 && $bpro['sale_price'] < $bpro['price']): ?>
<span style="text-decoration: line-through; color: #aaa; font-size: 13px; font-weight: normal; display: block; margin-bottom: 2px;"><?= number_format($bpro['price']) ?> đ</span><span style="color: var(--primary-color); font-weight: 800; font-size: 18px; display: block;"><?= number_format($bpro['sale_price']) ?> đ</span>
<?php else: ?>
<span style="color: var(--primary-color); font-weight: 800; font-size: 18px; display: block; margin-top: 15px;"><?= number_format($bpro['price']) ?> đ</span>
<?php endif; ?>
</div>
<button class="btn-buy" onclick="openQuickCart(<?= $bid ?>, '<?= htmlspecialchars(addslashes($bpro['name'])) ?>', <?= $real_price_slider ?>, '<?= $bimg ?>', '<?= $bjson ?>')"><i class="fa fa-cart-plus"></i> Thêm vào giỏ</button>
</div>
<?php endwhile; ?>
</div>
<button class="arrow-btn-ctrl arrow-right-pos" onclick="moveSliderRight()"><i class="fa fa-chevron-right"></i></button>
</div>
</div>
<?php endif; ?>

<!-- BÁN CHẠY CỦA DANH MỤC (NẾU ĐANG XEM DANH MỤC) -->
<?php if($cat_id > 0 && $cat_best_sellers && $cat_best_sellers->num_rows > 0): ?>
<div style="background: #fffdf5; border: 1px dashed #f39c12; padding: 20px; border-radius: var(--border-radius); margin-bottom: 30px; box-shadow: var(--shadow-sm);">
<h2 style="margin-top:0; font-size:18px; color: #f39c12; margin-bottom: 15px; text-transform: uppercase;">
<i class="fa fa-crown"></i> Top Bán Chạy Trong Danh Mục Này
</h2>
<div class="product-grid">
<?php while($c_pro = $cat_best_sellers->fetch_assoc()):
$c_pid = $c_pro['id']; $c_img = !empty($c_pro['image']) ? $c_pro['image'] : 'https://via.placeholder.com/230x220?text=No+Image';
$c_json = htmlspecialchars(json_encode(isset($variants_data[$c_pid]) ? $variants_data[$c_pid] : []), ENT_QUOTES, 'UTF-8');
$c_real = (isset($c_pro['sale_price']) && $c_pro['sale_price'] > 0) ? $c_pro['sale_price'] : $c_pro['price'];
?>
<div class="product-item grid-item-normal">
<!-- BỌC ẢNH VÀ NHÃN VÀO KHUNG RELATIVE CỐ ĐỊNH CHIỀU CAO -->
<div style="position: relative; min-height: 240px; background: #f8f9fa; border-radius: 8px; margin-bottom: 14px;">
    <span style="position:absolute; top:10px; left:10px; background:#f39c12; color:#fff; font-size:11px; padding:3px 8px; border-radius:20px; font-weight:bold; z-index:5;"><i class="fa fa-star"></i> BÁN CHẠY</span>
    <?php if(isset($c_pro['sale_price']) && $c_pro['sale_price'] > 0 && $c_pro['sale_price'] < $c_pro['price']): ?>
    <?php $percent_off = round((($c_pro['price'] - $c_pro['sale_price']) / $c_pro['price']) * 100); ?>
    <span style="position:absolute; top:10px; right:10px; background:#e74c3c; color:white; font-size:12px; padding:3px 6px; border-radius:4px; font-weight:bold; z-index:5;">-<?= $percent_off ?>%</span>
    <?php endif; ?>
    <a href="product_detail.php?id=<?= $c_pid ?>">
        <img src="<?= $c_img ?>" alt="<?= htmlspecialchars($c_pro['name']) ?>" onerror="this.onerror=null; this.src='https://via.placeholder.com/230x220?text=No+Image';" style="margin-bottom:0;">
    </a>
</div>
<a href="product_detail.php?id=<?= $c_pid ?>"><h4><?= htmlspecialchars($c_pro['name']) ?></h4></a>
<div class="product-price" style="min-height: 45px; display: flex; flex-direction: column; align-items: center; justify-content: center; margin-bottom: 10px;">
<?php if(isset($c_pro['sale_price']) && $c_pro['sale_price'] > 0 && $c_pro['sale_price'] < $c_pro['price']): ?>
<span style="text-decoration: line-through; color: #aaa; font-size: 13px; font-weight: normal; display: block; margin-bottom: 2px;"><?= number_format($c_pro['price']) ?> đ</span><span style="color: var(--primary-color); font-weight: 800; font-size: 18px; display: block;"><?= number_format($c_pro['sale_price']) ?> đ</span>
<?php else: ?>
<span style="color: var(--primary-color); font-weight: 800; font-size: 18px; display: block; margin-top: 15px;"><?= number_format($c_pro['price']) ?> đ</span>
<?php endif; ?>
</div>
<button class="btn-buy" onclick="openQuickCart(<?= $c_pid ?>, '<?= htmlspecialchars(addslashes($c_pro['name'])) ?>', <?= $c_real ?>, '<?= $c_img ?>', '<?= $c_json ?>')"><i class="fa fa-cart-plus"></i> Thêm vào giỏ</button>
</div>
<?php endwhile; ?>
</div>
</div>
<?php endif; ?>

<div class="product-grid">
<?php if ($products && $products->num_rows > 0):
while($pro = $products->fetch_assoc()):
$pid = $pro['id']; $img = !empty($pro['image']) ? $pro['image'] : 'https://via.placeholder.com/230x220?text=MF+Shop';
$json_variants = htmlspecialchars(json_encode(isset($variants_data[$pid]) ? $variants_data[$pid] : []), ENT_QUOTES, 'UTF-8');
$real_price_grid = (isset($pro['sale_price']) && $pro['sale_price'] > 0) ? $pro['sale_price'] : $pro['price'];
?>
<div class="product-item grid-item-normal">
<!-- BỌC ẢNH VÀ NHÃN VÀO KHUNG RELATIVE CỐ ĐỊNH CHIỀU CAO -->
<div style="position: relative; min-height: 240px; background: #f8f9fa; border-radius: 8px; margin-bottom: 14px;">
    <?php if(isset($pro['sale_price']) && $pro['sale_price'] > 0 && $pro['sale_price'] < $pro['price']): ?>
    <?php $percent_off = round((($pro['price'] - $pro['sale_price']) / $pro['price']) * 100); ?>
    <span style="position:absolute; top:10px; right:10px; background:#e74c3c; color:white; font-size:12px; padding:3px 6px; border-radius:4px; font-weight:bold; z-index:5;">-<?= $percent_off ?>%</span>
    <?php endif; ?>
    <a href="product_detail.php?id=<?= $pid ?>">
        <img src="<?= $img ?>" alt="<?= htmlspecialchars($pro['name']) ?>" onerror="this.onerror=null; this.src='https://via.placeholder.com/230x220?text=No+Image';" style="margin-bottom:0;">
    </a>
</div>
<a href="product_detail.php?id=<?= $pid ?>"><h4><?= htmlspecialchars($pro['name']) ?></h4></a>
<div class="product-price" style="min-height: 45px; display: flex; flex-direction: column; align-items: center; justify-content: center; margin-bottom: 10px;">
<?php if(isset($pro['sale_price']) && $pro['sale_price'] > 0 && $pro['sale_price'] < $pro['price']): ?>
<span style="text-decoration: line-through; color: #aaa; font-size: 13px; font-weight: normal; display: block; margin-bottom: 2px;"><?= number_format($pro['price']) ?> đ</span><span style="color: var(--primary-color); font-weight: 800; font-size: 18px; display: block;"><?= number_format($pro['sale_price']) ?> đ</span>
<?php else: ?>
<span style="color: var(--primary-color); font-weight: 800; font-size: 18px; display: block; margin-top: 15px;"><?= number_format($pro['price']) ?> đ</span>
<?php endif; ?>
</div>
<button class="btn-buy" onclick="openQuickCart(<?= $pid ?>, '<?= htmlspecialchars(addslashes($pro['name'])) ?>', <?= $real_price_grid ?>, '<?= $img ?>', '<?= $json_variants ?>')"><i class="fa fa-cart-plus"></i> Thêm vào giỏ</button>
</div>
<?php endwhile; else: ?>
<p style='color: var(--text-muted); font-style:italic; padding: 20px; text-align:center; width: 100%; grid-column: 1 / -1;'>Không tìm thấy sản phẩm nào phù hợp.</p>
<?php endif; ?>
</div>
<!-- PHÂN TRANG -->
<?php if ($total_pages > 1): ?>
<div class="pagination">
<?php for($i = 1; $i <= $total_pages; $i++): ?>
<a href="index.php?page=<?= $i ?><?= ($cat_id > 0) ? '&cat_id='.$cat_id : '' ?><?= ($search !== '') ? '&search='.urlencode($search) : '' ?>" class="<?= ($i == $page) ? 'active' : '' ?>"><?= $i ?></a>
<?php endfor; ?>
</div>
<?php endif; ?>
</div>
</div>

<!-- MODAL QUICK CART -->
<div id="quickCartModal" class="modal">
<div class="modal-content" style="width: 400px;">
<span class="close-btn" onclick="document.getElementById('quickCartModal').style.display='none'">&times;</span>
<h3 id="qc_name" style="margin-top:0; color:var(--text-main); font-size:18px;"></h3>
<div style="display:flex; gap:20px; align-items:center; margin-bottom:20px;">
<img id="qc_img" style="width: 90px; height: 90px; object-fit:cover; border-radius:6px; border:1px solid var(--border-color);" alt="Thumbnail">
<div class="product-price" id="qc_price" style="margin:0; color:var(--primary-color); font-weight:bold; font-size:18px;"></div>
</div>
<form action="cart.php?action=add" method="POST">
<input type="hidden" name="product_id" id="qc_id"><input type="hidden" name="is_quick" value="1">
<div class="variant-flex">
<div class="form-group" style="flex:1;"><label>Màu sắc:</label><select name="color" id="qc_color" required onchange="checkQuickCartStock()"></select></div>
<div class="form-group" style="flex:1;"><label>Size:</label><select name="size" id="qc_size" required onchange="checkQuickCartStock()"></select></div>
</div>
<div class="form-group">
<label>Số lượng đặt mua: <span id="qc_stock_info" style="color:var(--primary-color); font-weight:normal; font-size:13px; margin-left:5px;"></span></label>
<input type="number" name="quantity" id="qc_qty" value="1" min="1" required oninput="checkQuickCartStock()">
</div>
<button type="submit" id="qc_submit_btn" class="btn btn-primary" style="width:100%; padding:12px; font-size:15px;"><i class="fa fa-check"></i> Xác nhận thêm</button>
</form>
</div>
</div>

<div id="systemModal" class="modal">
<div class="modal-content" style="width:320px; text-align:center;">
<span class="close-btn" onclick="document.getElementById('systemModal').style.display='none'">&times;</span>
<i id="modalIcon" class="fa modal-icon"></i><h3 id="modalTitle" style="margin: 10px 0 5px 0;"></h3><p id="modalMessage" style="color:var(--text-muted); font-size:14px; margin-bottom:20px;"></p><button onclick="document.getElementById('systemModal').style.display='none'" class="btn btn-secondary" style="width:100%;">Đóng lại</button>
</div>
</div>

<footer style="background: #ffffff; padding: 25px 20px; text-align: center; border-top: 1px solid var(--border-color); margin-top: 40px; color: var(--text-muted); font-size: 14px; font-weight: 500;">
&copy; <?= date("Y") ?> Bản quyền thuộc về <b>P.S.B.T.S</b>. Hệ thống cửa hàng thời trang MF SHOP.
</footer>

<script>
let currentProductVariants = [];
function openQuickCart(id, name, price, img, variants_json) {
document.getElementById('quickCartModal').style.display = 'flex';
document.getElementById('qc_id').value = id; document.getElementById('qc_name').innerText = name;
document.getElementById('qc_img').src = img; document.getElementById('qc_price').innerText = new Intl.NumberFormat('vi-VN').format(price) + ' đ'; document.getElementById('qc_qty').value = 1;
let cSel = document.getElementById('qc_color'); let sSel = document.getElementById('qc_size');
cSel.innerHTML = ''; sSel.innerHTML = '';
try {
currentProductVariants = JSON.parse(variants_json); let colors = new Set(); let sizes = new Set();
if (currentProductVariants.length > 0) { currentProductVariants.forEach(v => { colors.add(v.color); sizes.add(v.size); }); colors.forEach(c => cSel.appendChild(new Option(c, c))); sizes.forEach(s => sSel.appendChild(new Option(s, s))); }
else { cSel.appendChild(new Option('Mặc định', 'Mặc định')); sSel.appendChild(new Option('Mặc định', 'Mặc định')); }
checkQuickCartStock();
} catch (e) { console.error(e); }
}

function checkQuickCartStock() {
let color = document.getElementById('qc_color').value; let size = document.getElementById('qc_size').value; let qtyInput = document.getElementById('qc_qty'); let btnSubmit = document.getElementById('qc_submit_btn'); let infoSpan = document.getElementById('qc_stock_info'); let maxStock = 9999;
if (currentProductVariants.length > 0) { let variant = currentProductVariants.find(v => v.color === color && v.size === size); maxStock = variant ? parseInt(variant.stock) : 0; }
let inputVal = parseInt(qtyInput.value) || 0;
if(currentProductVariants.length > 0) infoSpan.innerText = maxStock > 0 ? `(Kho còn: ${maxStock})` : '(Hết hàng)';
if (maxStock === 0 || inputVal > maxStock) { qtyInput.style.borderColor = 'red'; qtyInput.style.color = 'red'; btnSubmit.disabled = true; btnSubmit.innerHTML = maxStock === 0 ? '<i class="fa fa-times"></i> Hết hàng tạm thời' : '<i class="fa fa-exclamation"></i> Lố kho'; btnSubmit.style.background = '#ccc'; }
else { qtyInput.style.borderColor = '#ccc'; qtyInput.style.color = 'inherit'; btnSubmit.disabled = false; btnSubmit.innerHTML = '<i class="fa fa-check"></i> Xác nhận thêm vào giỏ'; btnSubmit.style.background = 'var(--primary-color)'; }
}

function moveSliderRight() { const track = document.getElementById('featuredSlider'); if(!track) return; const step = (track.querySelector('.product-item') ? track.querySelector('.product-item').offsetWidth : 220) + 20; if (track.scrollLeft >= (track.scrollWidth - track.clientWidth - 15)) { track.scrollTo({ left: 0, behavior: 'smooth' }); } else { track.scrollBy({ left: step * 3, behavior: 'smooth' }); } }
function moveSliderLeft() { const track = document.getElementById('featuredSlider'); if(!track) return; const step = (track.querySelector('.product-item') ? track.querySelector('.product-item').offsetWidth : 220) + 20; if (track.scrollLeft <= 10) { track.scrollTo({ left: track.scrollWidth, behavior: 'smooth' }); } else { track.scrollBy({ left: -step * 3, behavior: 'smooth' }); } }
function showSysModal(type, title, message) { document.getElementById('systemModal').style.display = 'flex'; document.getElementById('modalTitle').innerText = title; document.getElementById('modalMessage').innerText = message; document.getElementById('modalIcon').className = type === 'success' ? 'fa fa-check-circle modal-icon icon-success' : 'fa fa-exclamation-circle modal-icon icon-error'; }
</script>

<?php if (isset($_SESSION['sys_msg'])): ?>
<script>showSysModal('<?= $_SESSION['sys_msg']['type'] ?>', '<?= $_SESSION['sys_msg']['title'] ?>', '<?= $_SESSION['sys_msg']['msg'] ?>');</script>
<?php unset($_SESSION['sys_msg']); endif; ?>

<script>
// ==========================================
// XỬ LÝ TÌM KIẾM REAL-TIME TRANG CHỦ
// ==========================================
const searchInput = document.getElementById('live_search_input');
const suggestBox = document.getElementById('search_suggest');
let searchTimeout;

searchInput.addEventListener('input', function() {
    clearTimeout(searchTimeout);
    let q = this.value.trim();
    
    if(q.length < 2) { 
        suggestBox.style.display = 'none'; 
        return; 
    }
    
    // Đợi 0.3s sau khi người dùng ngừng gõ mới gọi API để tránh lag
    searchTimeout = setTimeout(() => {
        fetch('api_search.php?q=' + encodeURIComponent(q))
        .then(res => res.json())
        .then(data => {
            suggestBox.innerHTML = '';
            if(data.length > 0) {
                data.forEach(p => {
                    let priceHtml = p.sale_price > 0 
                        ? `<span style="text-decoration:line-through; color:#aaa; font-size:12px;">${new Intl.NumberFormat('vi-VN').format(p.price)}đ</span> <b style="color:var(--primary-color);">${new Intl.NumberFormat('vi-VN').format(p.sale_price)}đ</b>` 
                        : `<b style="color:var(--primary-color);">${new Intl.NumberFormat('vi-VN').format(p.price)}đ</b>`;
                    let img = p.image ? p.image : 'https://via.placeholder.com/50';
                    
                    suggestBox.innerHTML += `
                    <a href="product_detail.php?id=${p.id}" style="display:flex; align-items:center; gap:12px; padding:10px 15px; border-bottom:1px solid #f1f2f6; text-decoration:none; transition:0.2s;" onmouseover="this.style.background='#f8f9fa'" onmouseout="this.style.background='none'">
                        <img src="${img}" style="width:45px; height:45px; object-fit:cover; border-radius:6px; border: 1px solid #eee;">
                        <div style="flex:1; overflow:hidden;">
                            <div style="font-size:14px; font-weight:600; color:var(--text-main); white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">${p.name}</div>
                            <div style="margin-top:4px;">${priceHtml}</div>
                        </div>
                    </a>`;
                });
                suggestBox.style.display = 'block';
            } else {
                suggestBox.innerHTML = '<div style="padding:15px; text-align:center; color:#888; font-size:14px;"><i class="fa fa-search"></i> Không tìm thấy sản phẩm nào phù hợp.</div>';
                suggestBox.style.display = 'block';
            }
        }).catch(err => console.log(err));
    }, 300);
});

// Ẩn hộp gợi ý khi click chuột ra chỗ khác
document.addEventListener('click', function(e) {
    if(!searchInput.contains(e.target) && !suggestBox.contains(e.target)) {
        suggestBox.style.display = 'none';
    }
});
</script>





</body>
</html>