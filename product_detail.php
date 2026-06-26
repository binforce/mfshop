<?php
require 'db_connect.php';
// ĐÃ XÓA session_start() Ở ĐÂY VÌ TRONG db_connect.php ĐÃ CÓ SẴN RỒI!

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$product = $conn->query("SELECT p.*, c.name as cat_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.id = $id")->fetch_assoc();

if (!$product) {
    die("<h2 style='text-align:center; margin-top:50px; font-family:sans-serif;'>Sản phẩm không tồn tại!</h2>");
}

// Lấy danh sách biến thể (màu, size)
$variants_data = [];
$colors = [];
$sizes = [];
$v_query = $conn->query("SELECT * FROM product_variants WHERE product_id = $id");
while($v = $v_query->fetch_assoc()) {
    $variants_data[] = $v;
    if(!in_array($v['color'], $colors)) $colors[] = $v['color'];
    if(!in_array($v['size'], $sizes)) $sizes[] = $v['size'];
}

$variants_json = htmlspecialchars(json_encode($variants_data), ENT_QUOTES, 'UTF-8');

// Lấy 4 sản phẩm gợi ý
$related = $conn->query("SELECT * FROM products WHERE category_id = {$product['category_id']} AND id != $id ORDER BY RAND() LIMIT 4");

$unread_noti = 0; $user_avatar = 'default.png';
if (isset($_SESSION['user_id'])) {
    $uid = $_SESSION['user_id'];
    $unread_noti = $conn->query("SELECT COUNT(*) as c FROM notifications WHERE user_id=$uid AND is_read=0")->fetch_assoc()['c'] ?? 0;
    $user_avatar = $conn->query("SELECT avatar FROM users WHERE id=$uid")->fetch_assoc()['avatar'] ?? 'default.png';
}
?>
<!DOCTYPE html>
<html>
<head>
    <title><?= htmlspecialchars($product['name']) ?> - MF SHOP</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css?v=<?= time() ?>">
</head>
<body>
    <div class="toolbar">
        <div class="logo">
            <a href="index.php" style="display: flex; align-items: center; gap: 8px; font-size: 26px; font-weight: 900; color: var(--primary-color); text-decoration: none;">
                <i class="fa fa-shopping-bag"></i> MF SHOP
            </a>
        </div>
        <div class="action-group">
            <a href="cart.php" class="action-icon"><i class="fa fa-shopping-cart"></i></a>
            <a href="index.php" class="btn btn-secondary"><i class="fa fa-arrow-left"></i> Trở về kho</a>
        </div>
    </div>

    <div class="detail-container" style="max-width: 1100px;">
        <div class="detail-gallery">
            <img src="<?= !empty($product['image']) ? $product['image'] : 'https://via.placeholder.com/600x600?text=No+Image' ?>" class="detail-main-img" alt="<?= htmlspecialchars($product['name']) ?>">
        </div>
        
        <div class="detail-info">
            <div style="background: var(--primary-color); color: white; display: inline-block; padding: 4px 10px; border-radius: 4px; font-size: 12px; font-weight: bold; margin-bottom: 10px;">
                <?= htmlspecialchars($product['cat_name'] ?? 'Chưa phân loại') ?>
            </div>
            <h1 class="detail-title"><?= htmlspecialchars($product['name']) ?></h1>
            <div class="detail-price"><?= number_format($product['price']) ?> đ</div>
            
            <p style="color: var(--text-muted); line-height: 1.6; margin-bottom: 25px; padding-bottom: 20px; border-bottom: 1px solid var(--border-color);">
                <?= nl2br(htmlspecialchars($product['description'] ?? 'Chưa có mô tả cho sản phẩm này.')) ?>
            </p>

            <form action="cart.php?action=add" method="POST" style="background: #f8f9fa; padding: 20px; border-radius: 12px; border: 1px solid var(--border-color);">
                <input type="hidden" name="product_id" value="<?= $id ?>">
                
                <div class="variant-flex" style="margin-bottom: 15px;">
                    <div class="form-group" style="flex:1;">
                        <label>Màu sắc:</label>
                        <select name="color" id="detail_color" required onchange="checkDetailStock()">
                            <?php if(empty($colors)): ?><option value="Mặc định">Mặc định</option><?php endif; ?>
                            <?php foreach($colors as $c): ?><option value="<?= $c ?>"><?= $c ?></option><?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group" style="flex:1;">
                        <label>Kích thước:</label>
                        <select name="size" id="detail_size" required onchange="checkDetailStock()">
                            <?php if(empty($sizes)): ?><option value="Mặc định">Mặc định</option><?php endif; ?>
                            <?php foreach($sizes as $s): ?><option value="<?= $s ?>"><?= $s ?></option><?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label>Số lượng muốn mua: <span id="detail_stock_info" style="color:var(--primary-color); font-weight:normal;"></span></label>
                    <input type="number" name="quantity" id="detail_qty" value="1" min="1" required oninput="checkDetailStock()">
                </div>

                <button type="submit" id="detail_submit_btn" class="btn btn-primary" style="width: 100%; padding: 15px; font-size: 16px; margin-top: 10px;">
                    <i class="fa fa-cart-plus"></i> THÊM VÀO GIỎ HÀNG
                </button>
            </form>
        </div>
    </div>

    <?php if($related && $related->num_rows > 0): ?>
    <div class="main-container" style="display: block; margin-top: 40px; max-width: 1100px;">
        <h3 style="border-left: 5px solid var(--primary-color); padding-left: 10px; color: var(--text-main); margin-bottom: 20px;">
            Sản Phẩm Tương Tự Bạn Có Thể Thích
        </h3>
        
        <div class="product-grid" style="grid-template-columns: repeat(4, 1fr);">
            <?php while($rel = $related->fetch_assoc()): ?>
                <div class="product-item">
                    <a href="product_detail.php?id=<?= $rel['id'] ?>" style="display:block;">
                        <img src="<?= !empty($rel['image']) ? $rel['image'] : 'https://via.placeholder.com/200x200' ?>" onerror="this.onerror=null; this.src='https://via.placeholder.com/200x200?text=No+Image';">
                        <h4><?= htmlspecialchars($rel['name']) ?></h4>
                        <div class="product-price" style="margin:0; font-size: 16px;"><?= number_format($rel['price']) ?> đ</div>
                    </a>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
    <?php endif; ?>

    <footer style="background: #ffffff; padding: 25px 20px; text-align: center; border-top: 1px solid var(--border-color); margin-top: 40px; color: var(--text-muted); font-size: 14px; font-weight: 500;">
        &copy; <?= date("Y") ?> Bản quyền thuộc về <b>P.S.B.T.S</b>. Hệ thống cửa hàng thời trang MF SHOP.
    </footer>

    <script>
        const variantsData = <?= json_encode($variants_data) ?>;
        
        function checkDetailStock() {
            let color = document.getElementById('detail_color').value;
            let size = document.getElementById('detail_size').value;
            let qtyInput = document.getElementById('detail_qty');
            let btnSubmit = document.getElementById('detail_submit_btn');
            let infoSpan = document.getElementById('detail_stock_info');
            
            let maxStock = 9999;
            if (variantsData.length > 0) {
                let variant = variantsData.find(v => v.color === color && v.size === size);
                maxStock = variant ? parseInt(variant.stock) : 0;
            }

            let inputVal = parseInt(qtyInput.value) || 0;
            if (variantsData.length > 0) { infoSpan.innerText = maxStock > 0 ? `(Kho còn: ${maxStock})` : '(Hết hàng)'; }

            if (maxStock === 0 || inputVal > maxStock) {
                qtyInput.style.borderColor = 'red'; btnSubmit.disabled = true;
                btnSubmit.innerHTML = maxStock === 0 ? '<i class="fa fa-times"></i> Hết hàng tạm thời' : '<i class="fa fa-exclamation"></i> Vượt quá số lượng kho';
                btnSubmit.style.background = '#ccc';
            } else {
                qtyInput.style.borderColor = '#ccc'; btnSubmit.disabled = false;
                btnSubmit.innerHTML = '<i class="fa fa-cart-plus"></i> THÊM VÀO GIỎ HÀNG';
                btnSubmit.style.background = 'var(--primary-color)';
            }
        }
        checkDetailStock();
    </script>
</body>
</html>