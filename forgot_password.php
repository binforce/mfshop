<?php
session_start();
require 'db_connect.php';

// Tự động thêm cột lưu OTP vào bảng users nếu chưa có
try { $conn->query("ALTER TABLE users ADD COLUMN reset_otp VARCHAR(10) NULL"); } catch(Exception $e){}
try { $conn->query("ALTER TABLE users ADD COLUMN otp_expiry DATETIME NULL"); } catch(Exception $e){}

$step = isset($_POST['step']) ? (int)$_POST['step'] : 1;
$msg = ''; $msg_type = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = isset($_POST['email']) ? $conn->real_escape_string($_POST['email']) : '';

    // XỬ LÝ KHI BẤM NÚT "GỬI LẠI OTP"
    if (isset($_POST['resend_otp'])) {
        $otp = sprintf("%06d", mt_rand(1, 999999));
        $expiry = date('Y-m-d H:i:s', strtotime('+15 minutes'));
        $conn->query("UPDATE users SET reset_otp='$otp', otp_expiry='$expiry' WHERE email='$email'");

        // THỬ GỬI EMAIL
        $subject = "Mã OTP Khôi phục mật khẩu - MF SHOP";
        $message = "Mã xác thực OTP của bạn là: $otp . Mã có hiệu lực trong vòng 15 phút.";
        $headers = "From: noreply@mfshop.com";
        @mail($email, $subject, $message, $headers); // Gửi mail ngầm

        $msg = "Mã OTP mới đã được gửi lại đến Email của bạn!";
        $msg_type = "success";
        $step = 2; // Vẫn giữ ở bước 2
    }
    // BƯỚC 1: NHẬP EMAIL & GỬI OTP LẦN ĐẦU
    elseif ($step == 1) {
        $check = $conn->query("SELECT * FROM users WHERE email = '$email'");
        if ($check && $check->num_rows > 0) {
            $otp = sprintf("%06d", mt_rand(1, 999999)); // Tạo mã 6 số ngẫu nhiên
            $expiry = date('Y-m-d H:i:s', strtotime('+15 minutes')); // Hết hạn sau 15p
            $conn->query("UPDATE users SET reset_otp='$otp', otp_expiry='$expiry' WHERE email='$email'");

            // THỬ GỬI EMAIL
            $subject = "Mã OTP Khôi phục mật khẩu - MF SHOP";
            $message = "Mã xác thực OTP của bạn là: $otp . Mã có hiệu lực trong vòng 15 phút.";
            $headers = "From: noreply@mfshop.com";
            @mail($email, $subject, $message, $headers); // Gửi mail ngầm

            $msg = "Mã OTP đã được gửi đến Email của bạn!";
            $msg_type = "success";
            $step = 2; // Chuyển sang bước 2
        } else {
            $msg = "Email này chưa được đăng ký trong hệ thống!"; $msg_type = "error";
        }
    }
    // BƯỚC 2: KIỂM TRA OTP
    elseif ($step == 2) {
        $otp_input = $conn->real_escape_string($_POST['otp']);
        $check = $conn->query("SELECT * FROM users WHERE email = '$email' AND reset_otp = '$otp_input' AND otp_expiry >= NOW()");
        
        if ($check && $check->num_rows > 0) {
            $msg = "Xác thực OTP thành công. Mời bạn đặt mật khẩu mới.";
            $msg_type = "success";
            $step = 3; // Chuyển sang bước 3
        } else {
            $msg = "Mã OTP không hợp lệ hoặc đã hết hạn!"; $msg_type = "error";
            $step = 2; // Giữ lại bước 2
        }
    }
    // BƯỚC 3: ĐẶT LẠI MẬT KHẨU
    elseif ($step == 3) {
        $new_pass = $_POST['new_password'];
        $confirm_pass = $_POST['confirm_password'];
        if ($new_pass === $confirm_pass) {
            // ĐÃ SỬA: Đồng bộ hóa dùng md5() để giống với login.php và register.php
            $hashed_pass = md5($new_pass);
            $conn->query("UPDATE users SET password='$hashed_pass', reset_otp=NULL, otp_expiry=NULL WHERE email='$email'");
            
            $_SESSION['sys_msg'] = ['type' => 'success', 'title' => 'Thành công', 'msg' => 'Đặt lại mật khẩu thành công. Mời đăng nhập!'];
            header("Location: login.php"); exit();
        } else {
            $msg = "Mật khẩu xác nhận không khớp!"; $msg_type = "error";
            $step = 3;
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Khôi phục mật khẩu - MF SHOP</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
body { font-family: Arial; background: #f4f6f9; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
.auth-box { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); width: 100%; max-width: 400px; text-align: center; }
.form-group { margin-bottom: 15px; text-align: left; }
.form-group label { display: block; font-weight: bold; margin-bottom: 5px; color: #2c3e50; }
.form-group input { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; outline: none; }
.form-group input:focus { border-color: #3498db; }
.btn-submit { width: 100%; padding: 12px; background: #3498db; color: white; border: none; border-radius: 4px; font-size: 16px; cursor: pointer; font-weight: bold; }
.btn-resend { width: 100%; padding: 12px; background: #f4f6f9; color: #2c3e50; border: 1px solid #ccc; border-radius: 4px; font-size: 16px; cursor: pointer; font-weight: bold; margin-top: 10px; transition: 0.3s; }
.btn-resend:hover { background: #e2e6ea; }
.alert { padding: 10px; border-radius: 4px; margin-bottom: 15px; font-size: 14px; }
.alert-error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
.alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
</style>
</head>
<body>
<div class="auth-box">
<h2 style="color: #ff4757; margin-top:0;"><i class="fa fa-unlock-alt"></i> Khôi phục mật khẩu</h2>

<?php if($msg != ''): ?>
<div class="alert alert-<?= $msg_type ?>"><?= $msg ?></div>
<?php endif; ?>

<form method="POST">
<!-- Luôn giữ lại email cho các bước sau -->
<input type="hidden" name="email" value="<?= htmlspecialchars($email ?? '') ?>">

<?php if($step == 1): ?>
    <input type="hidden" name="step" value="1">
    <div class="form-group">
        <label>Nhập Email đã đăng ký:</label>
        <input type="email" name="email" required placeholder="admin@gmail.com hoặc user@gmail.com">
    </div>
    <button type="submit" class="btn-submit">Lấy mã xác nhận (OTP)</button>

<?php elseif($step == 2): ?>
    <input type="hidden" name="step" value="2">
    <div class="form-group">
        <label>Nhập mã OTP (6 số):</label>
        <input type="text" name="otp" required placeholder="Ví dụ: 123456" maxlength="6" style="text-align: center; font-size: 20px; letter-spacing: 5px; font-weight: bold;">
    </div>
    <button type="submit" class="btn-submit">Xác thực mã</button>
    
    <!-- NÚT GỬI LẠI OTP (Dùng formnovalidate để không bị chặn bởi trường nhập OTP required) -->
    <button type="submit" name="resend_otp" formnovalidate class="btn-resend">
        <i class="fa fa-sync-alt"></i> Gửi lại OTP
    </button>

<?php elseif($step == 3): ?>
    <input type="hidden" name="step" value="3">
    <div class="form-group">
        <label>Mật khẩu mới:</label>
        <input type="password" name="new_password" required placeholder="Nhập mật khẩu mới">
    </div>
    <div class="form-group">
        <label>Xác nhận mật khẩu mới:</label>
        <input type="password" name="confirm_password" required placeholder="Nhập lại mật khẩu mới">
    </div>
    <button type="submit" class="btn-submit">Lưu mật khẩu mới</button>
<?php endif; ?>

</form>

<p style="margin-top: 20px;"><a href="login.php" style="color: #3498db; text-decoration: none;"><i class="fa fa-arrow-left"></i> Quay lại Đăng nhập</a></p>
</div>
</body>
</html>