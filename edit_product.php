<?php
require 'db_connect.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 0) { header("Location: login.php"); exit(); }
if (!isset($_GET['id'])) { header("Location: admin.php?action=products"); exit(); }

$product_id = (int)$_GET['id'];

// XỬ LÝ LƯU CẬP NHẬT KHI SUBMIT FORM
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name']; $price = $_POST['price']; 
    $category_id = $_POST['category_id']; $gender = $_POST['gender']; 
    $desc = $_POST['description'];

    // Cập nhật thông tin cơ bản
    $stmt = $conn->prepare("UPDATE products SET category_id=?, name=?, price=?, description=?, gender=? WHERE id=?");
    $stmt->bind_param("isdsii", $category_id, $name, $price, $desc, $gender, $product_id); 
    $stmt->execute();

    // Xử lý đổi Ảnh chính
    if (isset($_FILES['main_image']) && $_FILES['main_image']['error'] == 0) {
        $main_img_path = 'uploads/' . time() . '_' . $_FILES['main_image']['name'];
        if (!is_dir('uploads')) mkdir('uploads', 0777, true);
        move_uploaded_file($_FILES['main_image']['tmp_name'], $main_img_path);
        $conn->query("UPDATE products SET image='$main_img_path' WHERE id=$product_id");
    }

    // Xử lý đổi Ảnh phụ (Xóa ảnh phụ cũ và up ảnh mới nếu có chọn)
    if (isset($_FILES['extra_images']) && $_FILES['extra_images']['error'][0] == 0) {
        $conn->query("DELETE FROM product_images WHERE product_id=$product_id");
        foreach ($_FILES['extra_images']['tmp_name'] as $key => $tmp_name) {
            if ($_FILES['extra_images']['error'][$key] == 0) {
                $extra_path = 'uploads/' . time() . '_' . $_FILES['extra_images']['name'][$key];
                move_uploaded_file($tmp_name, $extra_path);
                $conn->query("INSERT INTO product_images (product_id, image_url, is_primary) VALUES ($product_id, '$extra_path', 0)");
            }
        }
    }

    // Xử lý cập nhật biến thể (Xóa toàn bộ biến thể cũ và tạo lại theo form mới)
    if (isset($_POST['colors'])) {
        $conn->query("DELETE FROM product_variants WHERE product_id=$product_id");
        $colors = $_POST['colors']; $sizes = $_POST['sizes']; $stocks = $_POST['stocks'];
        for ($i = 0; $i < count($colors); $i++) {
            $c = $conn->real_escape_string($colors[$i]); $s = $conn->real_escape_string($sizes[$i]); $st = (int)$stocks[$i];
            if(!empty($c) && !empty($s)) { 
                $conn->query("INSERT INTO product_variants (product_id, color, size, stock) VALUES ($product_id, '$c', '$s', $st)"); 
            }
        }
    }

    $_SESSION['sys_msg'] = ['type' => 'success', 'title' => 'Thành công', 'msg' => 'Cập nhật sản phẩm thành công!'];
    header("Location: admin.php?action=products"); exit();
}

// LẤY DỮ LIỆU CŨ ĐỂ ĐỔ VÀO FORM
$product = $conn->query("SELECT * FROM products WHERE id=$product_id")->fetch_assoc();
$categories = $conn->query("SELECT * FROM categories");
$variants = $conn->query("SELECT * FROM product_variants WHERE product_id=$product_id");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Sửa Sản Phẩm - Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="main-container">
        <div class="table-wrapper" style="width: 100%; max-width: 900px; margin: auto;">
            <h2 style="margin-top:0; border-bottom: 2px solid var(--border-color); padding-bottom:10px; color:var(--secondary-color);">
                <i class="fa fa-edit"></i> Chỉnh Sửa Sản Phẩm #<?= $product_id ?>
            </h2>
            <form method="POST" enctype="multipart/form-data">
                <div class="variant-flex">
                    <div class="form-group" style="flex:2;">
                        <label>Tên Sản Phẩm:</label>
                        <input type="text" name="name" value="<?= htmlspecialchars($product['name']) ?>" required>
                    </div>
                    <div class="form-group" style="flex:1;">
                        <label>Giá Bán (VNĐ):</label>
                        <input type="number" name="price" value="<?= $product['price'] ?>" required>
                    </div>
                </div>
                
                <div class="variant-flex">
                    <div class="form-group" style="flex:1;"><label>Danh Mục:</label>
                        <select name="category_id">
                            <?php while($cat = $categories->fetch_assoc()): ?>
                                <option value="<?= $cat['id'] ?>" <?= $cat['id']==$product['category_id']?'selected':'' ?>><?= $cat['name'] ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group" style="flex:1;"><label>Giới Tính:</label>
                        <select name="gender">
                            <option value="0" <?= $product['gender']==0?'selected':'' ?>>Unisex</option>
                            <option value="1" <?= $product['gender']==1?'selected':'' ?>>Nam</option>
                            <option value="2" <?= $product['gender']==2?'selected':'' ?>>Nữ</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Mô tả chi tiết:</label>
                    <textarea name="description" rows="4"><?= htmlspecialchars($product['description']) ?></textarea>
                </div>
                
                <div class="form-group" style="background: var(--bg-light); padding: 20px; border-radius: var(--border-radius);">
                    <label style="color:var(--primary-color);">Đổi ảnh đại diện mới (Để trống nếu giữ nguyên):</label>
                    <?php if(!empty($product['image'])): ?>
                        <img src="<?= $product['image'] ?>" width="100" style="display:block; margin-bottom:10px; border-radius:4px;">
                    <?php endif; ?>
                    <input type="file" name="main_image" accept="image/*" style="margin-bottom: 15px;">
                    
                    <label>Đổi tất cả ảnh phụ trợ (Để trống nếu giữ nguyên):</label>
                    <input type="file" name="extra_images[]" accept="image/*" multiple>
                </div>

                <div class="form-group" style="background: var(--bg-light); padding: 20px; border-radius: var(--border-radius);">
                    <label style="color:var(--secondary-color);">Phân loại Kích thước & Màu sắc hiện tại:</label>
                    <table id="variantTable">
                        <tr><th>Màu sắc</th><th>Kích thước (Size)</th><th>Số lượng tồn kho</th><th>Thao tác</th></tr>
                        <?php if($variants->num_rows > 0): while($v = $variants->fetch_assoc()): ?>
                        <tr>
                            <td><input type="text" name="colors[]" value="<?= htmlspecialchars($v['color']) ?>" required></td>
                            <td><input type="text" name="sizes[]" value="<?= htmlspecialchars($v['size']) ?>" required></td>
                            <td><input type="number" name="stocks[]" value="<?= $v['stock'] ?>" required min="1"></td>
                            <td><button type="button" class="btn btn-primary" onclick="removeRow(this)"><i class="fa fa-trash"></i></button></td>
                        </tr>
                        <?php endwhile; else: ?>
                        <tr>
                            <td><input type="text" name="colors[]" required></td>
                            <td><input type="text" name="sizes[]" required></td>
                            <td><input type="number" name="stocks[]" required min="1"></td>
                            <td><button type="button" class="btn btn-primary" onclick="removeRow(this)"><i class="fa fa-trash"></i></button></td>
                        </tr>
                        <?php endif; ?>
                    </table>
                    <button type="button" class="btn btn-secondary" style="margin-top:10px;" onclick="addVariant()"><i class="fa fa-plus"></i> Thêm dòng</button>
                </div>
                
                <div style="text-align: right; margin-top: 20px;">
                    <a href="admin.php?action=products" class="btn" style="background:#ecf0f1; color:#333; margin-right: 10px;">Hủy bỏ</a>
                    <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> LƯU CẬP NHẬT</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function addVariant() {
            let table = document.getElementById('variantTable'); let row = table.insertRow();
            row.innerHTML = '<td><input type="text" name="colors[]" required></td><td><input type="text" name="sizes[]" required></td><td><input type="number" name="stocks[]" required min="1"></td><td><button type="button" class="btn btn-primary" onclick="removeRow(this)"><i class="fa fa-trash"></i></button></td>';
        }
        function removeRow(btn) {
            let row = btn.parentNode.parentNode;
            row.parentNode.removeChild(row);
        }
    </script>
</body>
</html>