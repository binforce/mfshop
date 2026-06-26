<?php
session_start();
require 'db_connect.php';

// Kiểm tra giỏ hàng
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit();
}

// Lấy thông tin user nếu đã đăng nhập
$uid = $_SESSION['user_id'] ?? 0;
$user_info = ['name'=>'', 'phone'=>'', 'address'=>''];
if ($uid > 0) {
    $u_res = $conn->query("SELECT name, phone, address FROM users WHERE id=$uid");
    if ($u_res && $u_res->num_rows > 0) {
        $user_info = $u_res->fetch_assoc();
    }
}

// Tính tiền
$subtotal = 0;
foreach ($_SESSION['cart'] as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}
$vat = $subtotal * 0.10;
$total = $subtotal + $vat;
?>
<!DOCTYPE html>
<html>
<head>
    <title>Thanh Toán - MF SHOP</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css?v=<?= time() ?>">
    <style>
        .checkout-container { display: flex; gap: 30px; max-width: 1000px; margin: 40px auto; align-items: flex-start; flex-wrap: wrap; }
        .checkout-form { flex: 2; min-width: 300px; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .checkout-summary { flex: 1.2; min-width: 300px; background: #f8f9fa; padding: 25px; border-radius: 8px; border: 1px solid #e1e8ed; position: sticky; top: 20px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; font-weight: bold; margin-bottom: 5px; color: #2c3e50; }
        .form-group input, .form-group textarea { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; font-family: inherit; outline: none; }
        .form-group input:focus, .form-group textarea:focus { border-color: var(--primary-color); }
        .item-list { border-bottom: 1px solid #ddd; padding-bottom: 15px; margin-bottom: 15px; max-height: 300px; overflow-y: auto; }
        .item-row { display: flex; gap: 10px; margin-bottom: 10px; align-items: center; }
        .item-row img { width: 60px; height: 60px; object-fit: cover; border-radius: 4px; border: 1px solid #eee; }
        .summary-line { display: flex; justify-content: space-between; margin-bottom: 10px; font-size: 15px; color: #555; }
        .summary-total { display: flex; justify-content: space-between; margin-top: 15px; padding-top: 15px; border-top: 2px dashed #ddd; font-size: 18px; font-weight: bold; color: var(--primary-color); }
        .promo-box { margin: 20px 0; padding: 15px; background: white; border-radius: 6px; border: 1px dashed var(--primary-color); }
        .promo-input-group { display: flex; gap: 10px; }
        .promo-input-group input { flex: 1; padding: 10px; border: 1px solid #ccc; border-radius: 4px; text-transform: uppercase; outline: none; }
        .promo-input-group button { background: var(--primary-color); color: white; border: none; padding: 0 15px; border-radius: 4px; cursor: pointer; font-weight: bold; white-space: nowrap; }
        .promo-input-group button:hover { opacity: 0.9; }
        .promo-msg { margin-top: 8px; font-size: 13px; font-weight: bold; }
    </style>
</head>
<body style="background: #f4f6f9;">
    <div class="toolbar">
        <div class="logo"><a href="index.php" style="display: flex; align-items: center; gap: 8px; font-size: 26px; font-weight: 900; color: var(--primary-color); text-decoration: none;"><i class="fa fa-shopping-bag"></i> MF SHOP</a></div>
        <div><a href="cart.php" style="color: var(--text-main); font-weight: bold; text-decoration: none;"><i class="fa fa-arrow-left"></i> Quay lại Giỏ hàng</a></div>
    </div>

    <div class="checkout-container">
        <!-- FORM NHẬP THÔNG TIN GIAO HÀNG (SẼ GỬI ĐI KHI BẤM XÁC NHẬN) -->
        <div class="checkout-form">
            <h2 style="margin-top:0; border-bottom: 2px solid var(--primary-color); padding-bottom: 10px;"><i class="fa fa-map-marker-alt"></i> Thông tin giao hàng</h2>
            <form action="process_checkout.php" method="POST" id="mainCheckoutForm">
                <div class="form-group">
                    <label>Họ và tên người nhận:</label>
                    <!-- THÔNG TIN BẮT BUỘC THÌ VẪN ĐỂ REQUIRED -->
                    <input type="text" name="fullname" required value="<?= htmlspecialchars($user_info['name'] ?? '') ?>" placeholder="Nhập họ tên đầy đủ">
                </div>
                <div class="form-group">
                    <label>Số điện thoại liên hệ:</label>
                    <input type="tel" name="phone" required value="<?= htmlspecialchars($user_info['phone'] ?? '') ?>" placeholder="Ví dụ: 0912345678">
                </div>
                <div class="form-group">
                    <label>Địa chỉ nhận hàng chi tiết:</label>
                    <textarea name="address" required rows="3" placeholder="Số nhà, tên đường, phường/xã, quận/huyện, tỉnh/thành phố..."><?= htmlspecialchars($user_info['address'] ?? '') ?></textarea>
                </div>
                <div class="form-group">
                    <label>Ghi chú đơn hàng (Không bắt buộc):</label>
                    <textarea name="note" rows="2" placeholder="Giao trong giờ hành chính, gọi trước khi giao..."></textarea>
                </div>
                
                <!-- Input ẩn này lưu mã giảm giá sau khi check AJAX thành công để gửi kèm lên DB -->
                <input type="hidden" name="applied_promo_code" id="hidden_promo_code" value="">
                
                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 15px; font-size: 18px; margin-top: 20px;"><i class="fa fa-check-circle"></i> XÁC NHẬN ĐẶT HÀNG</button>
            </form>
        </div>

        <div class="checkout-summary">
            <h3 style="margin-top:0;"><i class="fa fa-receipt"></i> Tóm tắt đơn hàng</h3>
            <div class="item-list">
                <?php foreach($_SESSION['cart'] as $item): ?>
                    <div class="item-row">
                        <img src="<?= $item['image'] ?>" onerror="this.src='https://via.placeholder.com/60';">
                        <div style="flex:1;">
                            <div style="font-weight:bold; color:#2c3e50; font-size:14px; line-height: 1.4;"><?= htmlspecialchars($item['name']) ?></div>
                            <div style="font-size:12px; color:#7f8c8d; margin-top: 3px;">Phân loại: <?= htmlspecialchars($item['color']) ?> - <?= htmlspecialchars($item['size']) ?> | SL: x<?= $item['quantity'] ?></div>
                        </div>
                        <div style="font-weight:bold; font-size:14px; white-space: nowrap;"><?= number_format($item['price'] * $item['quantity']) ?>đ</div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- KHU VỰC NHẬP MÃ GIẢM GIÁ (TÁCH BIỆT KHỎI SỰ RÀNG BUỘC) -->
            <div class="promo-box">
                <label style="font-weight:bold; color:#2c3e50; margin-bottom:8px; display:block;"><i class="fa fa-ticket-alt"></i> Nhập Mã Giảm Giá / Freeship:</label>
                <div class="promo-input-group">
                    <!-- KHÔNG CÓ THUỘC TÍNH "REQUIRED" NỮA -->
                    <input type="text" id="promo_code_input" placeholder="NHẬP MÃ KHUYẾN MÃI...">
                    <button type="button" onclick="applyPromo()">Áp dụng</button>
                </div>
                <div id="promo_msg" class="promo-msg"></div>
            </div>

            <div class="summary-line"><span>Tạm tính:</span> <span><?= number_format($subtotal) ?> đ</span></div>
            <div class="summary-line"><span>Thuế VAT (10%):</span> <span><?= number_format($vat) ?> đ</span></div>
            <div class="summary-line" id="discount_line" style="display:none; color: #27ae60; font-weight: bold;"><span>Giảm giá (<span id="discount_code_display"></span>):</span> <span id="discount_amount_display">- 0 đ</span></div>
            
            <div class="summary-total">
                <span>TỔNG CỘNG:</span>
                <span id="final_total_display"><?= number_format($total) ?> đ</span>
            </div>
        </div>
    </div>

    <script>
        let baseTotal = <?= $total ?>;
        
        function applyPromo() {
            let codeInput = document.getElementById('promo_code_input');
            let code = codeInput.value.trim().toUpperCase();
            let msgDiv = document.getElementById('promo_msg');
            
            // Xử lý mềm mỏng nếu khách bấm "Áp dụng" mà chưa nhập gì thay vì chặn đặt hàng
            if (code === '') {
                msgDiv.style.display = 'block';
                msgDiv.style.color = '#7f8c8d'; 
                msgDiv.innerHTML = '<i>Nếu bạn không có mã, cứ bấm nút <b>Xác nhận đặt hàng</b> bên trái nhé!</i>';
                
                // Trả về số tiền gốc nếu họ xóa mã đi
                document.getElementById('discount_line').style.display = 'none';
                document.getElementById('final_total_display').innerText = new Intl.NumberFormat('vi-VN').format(baseTotal) + ' đ';
                document.getElementById('hidden_promo_code').value = '';
                return;
            }

            // Gọi API kiểm tra mã giảm giá
            fetch('api_apply_promo.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'code=' + encodeURIComponent(code) + '&subtotal=<?= $subtotal ?>'
            })
            .then(response => response.json())
            .then(data => {
                msgDiv.style.display = 'block';
                if (data.status === 'success') {
                    msgDiv.style.color = '#27ae60';
                    msgDiv.innerHTML = '<i class="fa fa-check-circle"></i> ' + data.message;
                    
                    document.getElementById('discount_line').style.display = 'flex';
                    document.getElementById('discount_code_display').innerText = code;
                    document.getElementById('discount_amount_display').innerText = '- ' + new Intl.NumberFormat('vi-VN').format(data.discount_amount) + ' đ';
                    
                    let newTotal = baseTotal - data.discount_amount;
                    if(newTotal < 0) newTotal = 0;
                    document.getElementById('final_total_display').innerText = new Intl.NumberFormat('vi-VN').format(newTotal) + ' đ';
                    
                    // Gán mã đã xác thực vào input ẩn để gửi lên server khi bấm Xác nhận
                    document.getElementById('hidden_promo_code').value = code;
                } else {
                    msgDiv.style.color = '#e74c3c';
                    msgDiv.innerHTML = '<i class="fa fa-times-circle"></i> ' + data.message;
                    
                    // Reset nếu mã sai
                    document.getElementById('discount_line').style.display = 'none';
                    document.getElementById('final_total_display').innerText = new Intl.NumberFormat('vi-VN').format(baseTotal) + ' đ';
                    document.getElementById('hidden_promo_code').value = '';
                }
            })
            .catch(error => {
                msgDiv.style.display = 'block';
                msgDiv.style.color = '#e74c3c';
                msgDiv.innerHTML = '<i class="fa fa-wifi"></i> Lỗi kết nối. Vui lòng thử lại!';
            });
        }

        // Ngăn form tự gửi (submit) khi khách bấm nút Enter trong ô nhập mã
        document.getElementById('promo_code_input').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault(); 
                applyPromo(); 
            }
        });
    </script>
</body>
</html>