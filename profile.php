<?php
require 'db_connect.php';
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }
$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_profile'])) {
    $name = trim($_POST['name']); $phone = trim($_POST['phone']); $address = trim($_POST['address']);
    $dob = $_POST['dob']; $gender = (int)$_POST['gender']; $is_2fa_enabled = (int)$_POST['is_2fa_enabled'];
    $avatar_name = $_POST['current_avatar']; 
    
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] == 0) {
        $filename = $_FILES["avatar"]["name"];
        $avatar_name = 'uploads/' . time() . "_" . basename($filename);
        if (!is_dir('uploads')) mkdir('uploads', 0777, true);
        move_uploaded_file($_FILES["avatar"]["tmp_name"], $avatar_name);
    }
    $update_stmt = $conn->prepare("UPDATE users SET name=?, phone=?, address=?, dob=?, gender=?, avatar=?, is_2fa_enabled=? WHERE id=?");
    $update_stmt->bind_param("ssssisii", $name, $phone, $address, $dob, $gender, $avatar_name, $is_2fa_enabled, $user_id);
    if ($update_stmt->execute()) { $_SESSION['sys_msg'] = ['type' => 'success', 'title' => 'Thành công', 'msg' => 'Cập nhật tài khoản thành công!']; } 
    else { $_SESSION['sys_msg'] = ['type' => 'error', 'title' => 'Lỗi', 'msg' => 'Cập nhật thất bại.']; }
    header("Location: profile.php"); exit();
}
$user = $conn->query("SELECT * FROM users WHERE id = $user_id")->fetch_assoc();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Quản lý tài khoản - MF SHOP</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="toolbar">
        <div class="logo">
            <a href="index.php" style="display: flex; align-items: center; gap: 8px; font-size: 26px; font-weight: 900; color: var(--primary-color); letter-spacing: 1px; text-decoration: none;">
                <i class="fa fa-shopping-bag"></i> MF SHOP
            </a>
        </div>
        <div style="font-weight: bold;">
            <?php if ($user['role'] == 0): ?>
                <a href="admin.php" style="color:var(--text-main);"><i class="fa fa-tachometer-alt"></i> Về trang Quản trị</a>
            <?php else: ?>
                <a href="index.php" style="margin-right:20px; color:var(--text-main);"><i class="fa fa-home"></i> Về Trang Chủ</a>
                <a href="cart.php" style="color:var(--primary-color);"><i class="fa fa-shopping-cart"></i> Giỏ Hàng</a>
            <?php endif; ?>
        </div>
    </div>

    <div class="main-container" style="min-height: 60vh;">
        <div class="table-wrapper" style="width: 100%; max-width: 600px; margin: auto;">
            <h2 style="text-align: center; margin-top:0; color:var(--primary-color);"><i class="fa fa-user-cog"></i> Thông Tin Tài Khoản</h2>
            <div style="text-align: center; margin-bottom: 25px;">
                <?php $avatar_src = (!empty($user['avatar']) && $user['avatar'] != 'default.png') ? $user['avatar'] : 'https://via.placeholder.com/150'; ?>
                <img src="<?= $avatar_src ?>" style="width: 120px; height: 120px; border-radius: 50%; object-fit: cover; border: 3px solid var(--secondary-color);">
            </div>
            <form method="POST" action="" enctype="multipart/form-data">
                <input type="hidden" name="current_avatar" value="<?= htmlspecialchars($user['avatar']) ?>">
                <div class="variant-flex">
                    <div class="form-group" style="flex:1;"><label>Tên đăng nhập:</label><input type="text" value="<?= htmlspecialchars($user['username']) ?>" disabled style="background:#e9ecef;"></div>
                    <div class="form-group" style="flex:1;"><label>Email:</label><input type="text" value="<?= htmlspecialchars($user['email']) ?>" disabled style="background:#e9ecef;"></div>
                </div>
                <div class="variant-flex">
                    <div class="form-group" style="flex:1;"><label>Họ và tên:</label><input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required></div>
                    <div class="form-group" style="flex:1;"><label>Số điện thoại:</label><input type="text" name="phone" value="<?= htmlspecialchars($user['phone']) ?>" required></div>
                </div>
                <div class="variant-flex">
                    <div class="form-group" style="flex:1;"><label>Ngày sinh:</label><input type="date" name="dob" value="<?= $user['dob'] ?>" required></div>
                    <div class="form-group" style="flex:1;"><label>Giới tính:</label>
                        <select name="gender">
                            <option value="0" <?= ($user['gender'] == 0) ? 'selected' : '' ?>>Nam</option><option value="1" <?= ($user['gender'] == 1) ? 'selected' : '' ?>>Nữ</option><option value="2" <?= ($user['gender'] == 2) ? 'selected' : '' ?>>Khác</option>
                        </select>
                    </div>
                </div>
                <div class="form-group"><label>Địa chỉ:</label><textarea name="address" rows="3" required><?= htmlspecialchars($user['address']) ?></textarea></div>
                <div class="form-group"><label>Thay đổi ảnh đại diện mới:</label><input type="file" name="avatar" accept="image/*"></div>
                <div class="form-group" style="background: var(--bg-light); padding: 15px; border-radius: var(--border-radius); border: 1px solid var(--border-color); margin-top: 20px;">
                    <label style="color: var(--secondary-color);"><i class="fa fa-shield-alt"></i> Cài đặt bảo mật (Xác thực OTP)</label>
                    <div style="margin-top: 10px;">
                        <label style="font-weight: normal; display:inline-block; margin-right:20px;"><input type="radio" name="is_2fa_enabled" value="1" <?= ($user['is_2fa_enabled'] == 1) ? 'checked' : '' ?>> <b>Bật</b> mã OTP (An toàn)</label>
                        <label style="font-weight: normal; display:inline-block;"><input type="radio" name="is_2fa_enabled" value="0" <?= ($user['is_2fa_enabled'] == 0) ? 'checked' : '' ?>> <b>Tắt</b> (Đăng nhập nhanh)</label>
                    </div>
                </div>
                <button type="submit" name="update_profile" class="btn btn-primary" style="width: 100%; padding:15px; font-size:16px;">Lưu Thay Đổi</button>
            </form>
        </div>
    </div>

    <div id="systemModal" class="modal">
        <div class="modal-content" style="width: 300px; text-align: center;">
            <span class="close-btn" onclick="document.getElementById('systemModal').style.display='none'">&times;</span>
            <i id="modalIcon" class="fa modal-icon"></i><h3 id="modalTitle"></h3><p id="modalMessage"></p>
            <button onclick="document.getElementById('systemModal').style.display='none'" class="btn btn-secondary">Đóng</button>
        </div>
    </div>
    
    <footer style="background: #ffffff; padding: 25px 20px; text-align: center; border-top: 1px solid var(--border-color); margin-top: 40px; color: var(--text-muted); font-size: 14px; font-weight: 500;">
        &copy; <?= date("Y") ?> Bản quyền thuộc về <b>P.S.B.T.S</b>. Hệ thống cửa hàng thời trang MF SHOP.
    </footer>

    <script>
        function showSysModal(type, title, msg) {
            document.getElementById('systemModal').style.display = 'flex';
            document.getElementById('modalTitle').innerText = title; document.getElementById('modalMessage').innerText = msg;
            document.getElementById('modalIcon').className = type === 'success' ? 'fa fa-check-circle modal-icon icon-success' : 'fa fa-exclamation-circle modal-icon icon-error';
        }
    </script>
    <?php if (isset($_SESSION['sys_msg'])): ?>
        <script>showSysModal('<?= $_SESSION['sys_msg']['type'] ?>', '<?= $_SESSION['sys_msg']['title'] ?>', '<?= $_SESSION['sys_msg']['msg'] ?>');</script>
        <?php unset($_SESSION['sys_msg']); endif; ?>
</body>
</html>