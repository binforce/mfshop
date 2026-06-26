<?php
require 'db_connect.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 0) { header("Location: login.php"); exit(); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name']; $price = $_POST['price']; $category_id = $_POST['category_id']; $gender = $_POST['gender']; $desc = $_POST['description'];
    
    $main_img_path = '';
    if (isset($_FILES['main_image']) && $_FILES['main_image']['error'] == 0) {
        $main_img_path = 'uploads/' . time() . '_' . $_FILES['main_image']['name'];
        if (!is_dir('uploads')) mkdir('uploads', 0777, true);
        move_uploaded_file($_FILES['main_image']['tmp_name'], $main_img_path);
    }
    
    $stmt = $conn->prepare("INSERT INTO products (category_id, name, price, description, gender, image) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isdsis", $category_id, $name, $price, $desc, $gender, $main_img_path); $stmt->execute();
    $product_id = $conn->insert_id;

    if (isset($_FILES['extra_images'])) {
        foreach ($_FILES['extra_images']['tmp_name'] as $key => $tmp_name) {
            if ($_FILES['extra_images']['error'][$key] == 0) {
                $extra_path = 'uploads/' . time() . '_' . $_FILES['extra_images']['name'][$key];
                move_uploaded_file($tmp_name, $extra_path);
                $conn->query("INSERT INTO product_images (product_id, image_url, is_primary) VALUES ($product_id, '$extra_path', 0)");
            }
        }
    }
    
    $colors = $_POST['colors']; $sizes = $_POST['sizes']; $stocks = $_POST['stocks'];
    for ($i = 0; $i < count($colors); $i++) {
        $c = $conn->real_escape_string($colors[$i]); $s = $conn->real_escape_string($sizes[$i]); $st = (int)$stocks[$i];
        if(!empty($c) && !empty($s)) { $conn->query("INSERT INTO product_variants (product_id, color, size, stock) VALUES ($product_id, '$c', '$s', $st)"); }
    }
    
    $_SESSION['sys_msg'] = ['type' => 'success', 'title' => 'Thành công', 'msg' => 'Đã thêm sản phẩm mới!'];
    header("Location: admin.php?action=products"); exit();
}
$categories = $conn->query("SELECT * FROM categories");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Thêm Sản Phẩm - Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="main-container">
        <div class="table-wrapper" style="width: 100%; max-width: 900px; margin: auto;">
            <h2 style="margin-top:0; border-bottom: 2px solid var(--border-color); padding-bottom:10px; color:var(--primary-color);">
                <i class="fa fa-plus-circle"></i> Thêm Sản Phẩm Mới
            </h2>
            <form method="POST" enctype="multipart/form-data">
                <div class="variant-flex">
                    <div class="form-group" style="flex:2;"><label>Tên Sản Phẩm:</label><input type="text" name="name" required></div>
                    <div class="form-group" style="flex:1;"><label>Giá Bán (VNĐ):</label><input type="number" name="price" required></div>
                </div>
                <div class="variant-flex">
                    <div class="form-group" style="flex:1;"><label>Danh Mục:</label>
                        <select name="category_id"><?php while($cat = $categories->fetch_assoc()): ?><option value="<?= $cat['id'] ?>"><?= $cat['name'] ?></option><?php endwhile; ?></select>
                    </div>
                    <div class="form-group" style="flex:1;"><label>Giới Tính:</label><select name="gender"><option value="0">Unisex</option><option value="1">Nam</option><option value="2">Nữ</option></select></div>
                </div>
                <div class="form-group"><label>Mô tả chi tiết:</label><textarea name="description" rows="4"></textarea></div>
                
                <div class="form-group" style="background: var(--bg-light); padding: 20px; border-radius: var(--border-radius);">
                    <label style="color:var(--primary-color);">Ảnh đại diện (Bắt buộc):</label>
                    <input type="file" name="main_image" accept="image/*" required style="margin-bottom: 15px;">
                    <label>Ảnh phụ trợ (Chọn nhiều ảnh cùng lúc - Tùy chọn):</label>
                    <input type="file" name="extra_images[]" accept="image/*" multiple>
                </div>

                <div class="form-group" style="background: var(--bg-light); padding: 20px; border-radius: var(--border-radius);">
                    <label style="color:var(--secondary-color);">Phân loại Kích thước & Màu sắc:</label>
                    <table id="variantTable">
                        <tr><th>Màu sắc</th><th>Kích thước (Size)</th><th>Số lượng tồn kho</th></tr>
                        <tr>
                            <td><input type="text" name="colors[]" required></td>
                            <td><input type="text" name="sizes[]" required></td>
                            <td><input type="number" name="stocks[]" required min="1"></td>
                        </tr>
                    </table>
                    <button type="button" class="btn btn-secondary" style="margin-top:10px;" onclick="addVariant()"><i class="fa fa-plus"></i> Thêm dòng</button>
                </div>
                
                <div style="text-align: right; margin-top: 20px;">
                    <a href="admin.php?action=products" class="btn" style="background:#ecf0f1; color:#333; margin-right: 10px;">Hủy bỏ</a>
                    <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> LƯU SẢN PHẨM</button>
                </div>
            </form>
        </div>
    </div>
    <script>
        function addVariant() {
            let table = document.getElementById('variantTable'); let row = table.insertRow();
            row.innerHTML = '<td><input type="text" name="colors[]" required></td><td><input type="text" name="sizes[]" required></td><td><input type="number" name="stocks[]" required min="1"></td>';
        }
    </script>
</body>
</html>