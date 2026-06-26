<?php
require 'db_connect.php';
require 'mail_config.php';

$sys_msg = null;
if (isset($_SESSION['sys_msg'])) { $sys_msg = $_SESSION['sys_msg']; unset($_SESSION['sys_msg']); }

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = md5($_POST['password']); 
    $stmt = $conn->prepare("SELECT id, email, role, is_2fa_enabled FROM users WHERE username=? AND password=?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if ($user['is_2fa_enabled'] == 1) {
            $otp = sprintf("%06d", mt_rand(1, 999999));
            $expiry = date("Y-m-d H:i:s", strtotime('+5 minutes'));
            $conn->query("UPDATE users SET otp_code='$otp', otp_expiry='$expiry' WHERE id={$user['id']}");
            if (sendOTP($user['email'], $otp)) {
                $_SESSION['temp_user_id'] = $user['id']; $_SESSION['temp_role'] = $user['role'];
                $_SESSION['sys_msg'] = ['type' => 'success', 'title' => 'Đã gửi mã!', 'msg' => 'Kiểm tra email để lấy mã OTP.'];
                header("Location: verify_otp.php"); exit();
            } else {
                $sys_msg = ['type' => 'error', 'title' => 'Lỗi Email', 'msg' => 'Không thể gửi OTP!'];
            }
        } else {
            $_SESSION['user_id'] = $user['id']; $_SESSION['role'] = $user['role'];
            $_SESSION['sys_msg'] = ['type' => 'success', 'title' => 'Thành công', 'msg' => 'Đăng nhập thành công.'];
            header("Location: " . ($user['role'] == 0 ? "admin.php" : "index.php")); exit();
        }
    } else {
        $sys_msg = ['type' => 'error', 'title' => 'Thất bại', 'msg' => 'Sai tài khoản hoặc mật khẩu!'];
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Đăng nhập - MF SHOP</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body style="display: flex; justify-content: center; align-items: center; min-height: 100vh;">
    <div class="table-wrapper" style="width: 350px; text-align: center; margin: auto;">
        <h2 style="margin-top: 0; color: var(--primary-color);"><i class="fa fa-user-circle"></i> Đăng Nhập</h2>
        <form method="POST" action="">
            <div class="form-group"><label>Tên đăng nhập:</label><input type="text" name="username" required></div>
            <div class="form-group"><label>Mật khẩu:</label><input type="password" name="password" required></div>
            <button type="submit" class="btn btn-primary" style="width: 100%;">Đăng nhập</button>
        </form>
        <div style="margin-top: 15px; font-size: 14px;">
            <a href="register.php" style="color: var(--secondary-color); font-weight:bold;">Chưa có tài khoản? Đăng ký</a> <br><br>
            <a href="forgot_password.php">Quên mật khẩu?</a> | <a href="index.php">Về trang chủ</a>
        </div>
    </div>

    <div id="systemModal" class="modal">
        <div class="modal-content" style="width: 300px; text-align: center;">
            <span class="close-btn" onclick="document.getElementById('systemModal').style.display='none'">&times;</span>
            <i id="modalIcon" class="fa modal-icon"></i><h3 id="modalTitle"></h3><p id="modalMessage"></p>
            <button onclick="document.getElementById('systemModal').style.display='none'" class="btn btn-secondary">Đóng</button>
        </div>
    </div>
    <script>
        function showSysModal(type, title, msg) {
            document.getElementById('systemModal').style.display = 'flex';
            document.getElementById('modalTitle').innerText = title; document.getElementById('modalMessage').innerText = msg;
            document.getElementById('modalIcon').className = type === 'success' ? 'fa fa-check-circle modal-icon icon-success' : 'fa fa-exclamation-circle modal-icon icon-error';
        }
    </script>
    <?php if ($sys_msg) echo "<script>showSysModal('{$sys_msg['type']}', '{$sys_msg['title']}', '{$sys_msg['msg']}');</script>"; ?>
</body>
</html>