<?php
require 'db_connect.php'; 
require 'mail_config.php';

$order_success = false; $qr_order_id = null;

// Lấy giỏ hàng từ Database nếu đã đăng nhập
$cart_items = [];
$total = 0;
if (isset($_SESSION['user_id'])) {
    $uid = (int)$_SESSION['user_id'];
    $res = $conn->query("SELECT c.id as cart_id, c.quantity as qty, c.color, c.size, p.id as product_id, p.name, p.image, p.price, p.sale_price FROM carts c JOIN products p ON c.product_id = p.id WHERE c.user_id = $uid");
    while ($row = $res->fetch_assoc()) {
        $real_price = ($row['sale_price'] > 0) ? $row['sale_price'] : $row['price'];
        $row['real_price'] = $real_price;
        $total += ($real_price * $row['qty']);
        $cart_items[] = $row;
    }
}

// Gọi file xử lý logic giỏ hàng & thanh toán
require 'cart_actions.php';
?>
<!DOCTYPE html>
<html>
<head>
<title>Giỏ Hàng - MF SHOP</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="style.css?v=<?= time() ?>">
<style>
/* Tùy chỉnh thanh cuộn cho Modal Checkout */
#checkoutModal .modal-content::-webkit-scrollbar { width: 6px; }
#checkoutModal .modal-content::-webkit-scrollbar-thumb { background: #b2bec3; border-radius: 4px; }
</style>
</head>
<body>
<div class="toolbar">
<div class="logo"><a href="index.php" style="display: flex; align-items: center; gap: 8px; font-size: 26px; font-weight: 900; color: var(--primary-color); text-decoration: none;"><i class="fa fa-shopping-bag"></i> MF SHOP</a></div>
<div style="font-weight: bold;"><a href="index.php" style="margin-right: 20px; color: var(--text-main);"><i class="fa fa-home"></i> Về Trang Chủ</a><a href="orders.php" style="color: var(--secondary-color);"><i class="fa fa-box"></i> Đơn Hàng Của Tôi</a></div>
</div>
<div class="main-container" style="min-height: 60vh;">
<div class="table-wrapper" style="width: 100%; max-width: 1000px; margin: auto;">
<?php if ($order_success): ?>
<div style="text-align: center; padding: 30px;">
<?php if (isset($_POST['payment_method']) && $_POST['payment_method'] == 'QR'): ?>
<i class="fa fa-qrcode" style="font-size: 60px; color: var(--secondary-color);"></i>
<h2 style="color: var(--secondary-color);">VUI LÒNG THANH TOÁN ĐỂ HOÀN TẤT!</h2>
<p>Đơn hàng <b>#<?= $qr_order_id ?></b> của bạn đã được ghi nhận.</p>
<div style="border: 2px dashed var(--secondary-color); padding: 20px; margin: 20px auto; display: flex; flex-direction: column; align-items: center; border-radius: 8px; width: fit-content;">
  <h3>Quét Mã QR Chuyển Khoản</h3>
  
  <div style="display: flex; justify-content: center; width: 200px;">
    <img src="uploads/qrcode/qrcode_checkout.png" style="width: 180px; display: block;">
  </div>

  <p>Nội dung chuyển khoản:</p>
  <b style="color:var(--primary-color); font-size: 20px;">MFSHOP <?= $qr_order_id ?></b>
</div>
<?php else: ?>
<i class="fa fa-check-circle" style="font-size: 60px; color: var(--success-color);"></i><h2>ĐẶT HÀNG THÀNH CÔNG!</h2><p>Hóa đơn điện tử đã được gửi tới email của bạn.</p>
<?php endif; ?>
<br><br><a href="orders.php" class="btn btn-secondary">Theo dõi đơn hàng</a><a href="index.php" class="btn" style="background:#ecf0f1; color:#333;">Về trang chủ</a>
</div>
<?php else: ?>
<h2 style="margin-top:0;"><a href="index.php" style="color:var(--text-main);"><i class="fa fa-arrow-left"></i></a> Giỏ Hàng Của Bạn</h2>
<form action="cart.php" method="POST">
<table>
<tr><th style="width: 40px; text-align: center;"><input type="checkbox" onclick="toggleSelectAllCart(this)"></th><th>Hình</th><th>Sản phẩm</th><th>Phân loại</th><th>Số lượng</th><th>Thành tiền</th><th>Xóa</th></tr>
<?php foreach ($cart_items as $item): ?>
<tr>
<td style="text-align: center;"><input type="checkbox" name="selected_cart_items[]" value="<?= $item['cart_id'] ?>" class="cart_checkbox"></td>
<td><img src="<?= $item['image'] ?>" width="60" style="border-radius:4px; object-fit:cover;" onerror="this.src='https://via.placeholder.com/60';"></td>
<td><b><?= htmlspecialchars($item['name']) ?></b></td><td><?= htmlspecialchars($item['color']) ?> - <?= htmlspecialchars($item['size']) ?></td><td><?= $item['qty'] ?></td>
<td style="color:var(--primary-color); font-weight:bold;"><?= number_format($item['real_price'] * $item['qty']) ?>đ</td>
<td><a href="cart.php?action=remove&key=<?= $item['cart_id'] ?>" style="color: var(--primary-color); font-size:18px;"><i class="fa fa-trash"></i></a></td>
</tr>
<?php endforeach; ?>
</table>
<?php if(count($cart_items) > 0): ?>
<div style="margin-top: 15px; display: flex; justify-content: space-between; align-items: center;">
<button type="submit" name="bulk_remove_cart" class="btn" style="background: white; border: 1px solid var(--primary-color); color: var(--primary-color);" onclick="return confirm('Bỏ các sản phẩm đã chọn?');"><i class="fa fa-trash"></i> Xóa mục đã chọn</button>
<h3 style="color: var(--primary-color); margin: 0;">Tạm tính: <?= number_format($total) ?> VNĐ</h3>
</div>
<?php endif; ?>
</form>
<?php if(count($cart_items) > 0): ?>
<div style="text-align: right; margin-top: 25px; border-top: 1px solid var(--border-color); padding-top: 20px;">
<button onclick="openCheckoutModal()" class="btn btn-primary" style="padding:15px 30px; font-size:16px;">Tiến Hành Thanh Toán</button>
</div>
<?php else: ?>
<p style="text-align:center; padding: 50px; color:var(--text-muted);">Giỏ hàng đang trống.</p>
<?php endif; ?>
<?php endif; ?>
</div>
</div>
<?php
$u_name = ''; $u_phone = ''; $u_addr = '';
if(isset($_SESSION['user_id'])) {
$user_info = $conn->query("SELECT name, phone, address FROM users WHERE id={$_SESSION['user_id']}")->fetch_assoc();
if($user_info) { $u_name = $user_info['name']; $u_phone = $user_info['phone']; $u_addr = $user_info['address']; }
}
$cart_subtotal = isset($total) ? $total : 0;
?>
<div id="checkoutModal" class="modal">
<div class="modal-content" style="width: 500px; text-align: left; max-height: 90vh; overflow-y: auto;">
<span class="close-btn" onclick="document.getElementById('checkoutModal').style.display='none'">&times;</span>
<h2 style="margin-top:0; border-bottom:2px solid var(--border-color); padding-bottom:10px;"><i class="fa fa-money-check-alt"></i> Thanh toán</h2>
<div style="background:#f8f9fa; padding:15px; border-radius:8px; margin-bottom:15px; border:1px dashed #ccc;">
<label style="font-weight:bold; font-size:13px; margin-bottom:5px; display:block;"><i class="fa fa-ticket-alt"></i> Nhập Mã Giảm Giá / Freeship:</label>
<div style="display:flex; gap:10px;">
<input type="text" id="promoCodeInput" placeholder="Nhập mã khuyến mãi..." onkeypress="if(event.key === 'Enter') { event.preventDefault(); applyPromo(<?= $cart_subtotal ?>); return false; }" style="flex:1; padding:8px 12px; border:1px solid #ddd; border-radius:4px; text-transform:uppercase;">
<button type="button" onclick="applyPromo(<?= $cart_subtotal ?>)" class="btn btn-secondary" style="padding:8px 15px;">Áp dụng</button>
</div>
<p id="promoMessage" style="font-size:13px; font-weight:bold; margin-top:8px; margin-bottom:0;"></p>
<hr style="border:0; border-top:1px dashed #ddd; margin:15px 0;">
<div style="display:flex; justify-content:space-between; margin-bottom:5px;"><span>Tạm tính:</span> <b><?= number_format($cart_subtotal) ?>đ</b></div>
<div id="discountRow" style="display:none; justify-content:space-between; margin-bottom:5px; color:var(--primary-color);"><span>Giảm giá:</span> <b>-<span id="discountAmt">0</span>đ</b></div>
<div style="display:flex; justify-content:space-between; margin-bottom:5px;"><span>Thuế VAT (10%):</span> <b><span id="vatAmt"><?= number_format($cart_subtotal * 0.1) ?></span>đ</b></div>
<div style="display:flex; justify-content:space-between; font-size:18px; margin-top:10px;"><b>Tổng tiền:</b> <b style="color:var(--primary-color);"><span id="finalAmt"><?= number_format($cart_subtotal * 1.1) ?></span>đ</b></div>
</div>
<form action="cart.php" method="POST">
<input type="hidden" name="process_checkout" value="1">
<div class="form-group"><label>Họ và Tên:</label><input type="text" name="receiver_name" value="<?= htmlspecialchars($u_name) ?>" required></div>
<div class="form-group"><label>Số điện thoại:</label><input type="text" name="receiver_phone" value="<?= htmlspecialchars($u_phone) ?>" required></div>
<div class="form-group"><label>Địa chỉ nhận hàng:</label><textarea name="receiver_address" rows="3" required><?= htmlspecialchars($u_addr) ?></textarea></div>
<div class="form-group"><label>Ghi chú:</label><textarea name="note" rows="2" placeholder="Ví dụ: Giao giờ hành chính..."></textarea></div>
<div class="form-group" style="background:var(--bg-light); padding:15px; border-radius:var(--border-radius);">
<label style="color:var(--primary-color);">Hình thức thanh toán:</label>
<div style="margin-top: 10px;">
<label style="font-weight:normal; display:block; margin-bottom:10px;"><input type="radio" name="payment_method" value="COD" checked> Thanh toán khi nhận hàng (COD)</label>
<label style="font-weight:normal; display:block;"><input type="radio" name="payment_method" value="QR"> Chuyển khoản (Quét QR)</label>
</div>
</div>
<button type="submit" class="btn btn-primary" style="width: 100%; margin-top:10px; padding:15px;">Xác Nhận Đặt Hàng</button>
</form>
</div>
</div>
<script>
function toggleSelectAllCart(source) { let checkboxes = document.getElementsByClassName('cart_checkbox'); for(let i=0; i<checkboxes.length; i++) { checkboxes[i].checked = source.checked; } }
function openCheckoutModal() {
<?php if(!isset($_SESSION['user_id'])): ?> alert("Vui lòng đăng nhập!"); window.location.href = 'login.php'; <?php else: ?> document.getElementById('checkoutModal').style.display = 'flex'; <?php endif; ?>
}
function applyPromo(cartTotal) {
let code = document.getElementById('promoCodeInput').value;
let msgBox = document.getElementById('promoMessage');
if(code.trim() === '') { msgBox.style.color = '#e74c3c'; msgBox.innerHTML = '<i class="fa fa-exclamation-circle"></i> Vui lòng nhập mã!'; return; }
msgBox.style.color = '#f39c12';
msgBox.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Đang xử lý...';
let formData = new URLSearchParams(); formData.append('code', code); formData.append('total', cartTotal);
fetch('api_apply_promo.php', { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body: formData.toString() })
.then(async response => {
let text = await response.text();
try { return JSON.parse(text); }
catch(e) { throw new Error("Máy chủ lỗi nội bộ, vui lòng thử lại sau."); }
})
.then(data => {
if(data.success) {
msgBox.style.color = '#27ae60'; msgBox.innerHTML = '<i class="fa fa-check"></i> ' + data.message;
document.getElementById('discountRow').style.display = 'flex';
document.getElementById('discountAmt').innerText = new Intl.NumberFormat('vi-VN').format(data.discount);
document.getElementById('vatAmt').innerText = new Intl.NumberFormat('vi-VN').format(data.vat);
document.getElementById('finalAmt').innerText = new Intl.NumberFormat('vi-VN').format(data.new_total);
} else {
msgBox.style.color = '#e74c3c'; msgBox.innerHTML = '<i class="fa fa-times"></i> ' + data.message;
document.getElementById('discountRow').style.display = 'none';
document.getElementById('vatAmt').innerText = new Intl.NumberFormat('vi-VN').format(cartTotal * 0.1);
document.getElementById('finalAmt').innerText = new Intl.NumberFormat('vi-VN').format(cartTotal * 1.1);
}
})
.catch(error => {
msgBox.style.color = '#e74c3c'; msgBox.innerHTML = '<i class="fa fa-exclamation-triangle"></i> Lỗi hệ thống: ' + error.message;
});
}
</script>
</body>
</html>