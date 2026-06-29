<?php
require 'db_connect.php';
// Bổ sung file cấu hình mail để có thể gọi hàm sendOTP
require 'mail_config.php';

$sys_msg = null;
if (isset($_SESSION['sys_msg'])) { $sys_msg = $_SESSION['sys_msg']; unset($_SESSION['sys_msg']); }

if (!isset($_SESSION['temp_user_id'])) { header("Location: login.php"); exit(); }
$user_id = $_SESSION['temp_user_id'];

// 1. XỬ LÝ KHI BẤM NÚT "GỬI LẠI OTP"
if (isset($_POST['resend_otp'])) {
    $user = $conn->query("SELECT email FROM users WHERE id=$user_id")->fetch_assoc();
    $otp = sprintf("%06d", mt_rand(1, 999999));
    $expiry = date("Y-m-d H:i:s", strtotime('+5 minutes'));
    
    // Lưu mã mới vào DB và gửi mail
    $conn->query("UPDATE users SET otp_code='$otp', otp_expiry='$expiry' WHERE id=$user_id");
    
    if (sendOTP($user['email'], $otp)) {
        $sys_msg = ['type' => 'success', 'title' => 'Đã gửi lại', 'msg' => 'Mã OTP mới đã được gửi đến email của bạn!'];
    } else {
        $sys_msg = ['type' => 'error', 'title' => 'Lỗi Email', 'msg' => 'Không thể gửi email OTP lúc này!'];
    }
}

// 2. XỬ LÝ KHI BẤM NÚT "XÁC NHẬN"
if (isset($_POST['verify_otp'])) {
    $otp_input = trim($_POST['otp']);
    $current_time = date("Y-m-d H:i:s");
    $user = $conn->query("SELECT role, otp_code, otp_expiry FROM users WHERE id=$user_id")->fetch_assoc();

    if ($user && $user['otp_code'] == $otp_input) {
        if ($current_time <= $user['otp_expiry']) {
            // Xác thực thành công: Đăng nhập chính thức
            $_SESSION['user_id'] = $user_id;
            $_SESSION['role'] = $user['role'];
            
            // Xóa mã OTP và biến tạm
            $conn->query("UPDATE users SET otp_code=NULL, otp_expiry=NULL WHERE id=$user_id");
            unset($_SESSION['temp_user_id']);
            unset($_SESSION['temp_role']);
            
            $_SESSION['sys_msg'] = ['type' => 'success', 'title' => 'Thành công', 'msg' => 'Xác thực OTP thành công. Chào mừng bạn!'];
            header("Location: " . ($user['role'] == 0 ? "admin.php" : "index.php"));
            exit();
        } else {
            $sys_msg = ['type' => 'error', 'title' => 'Hết hạn', 'msg' => 'Mã OTP đã hết hạn! Vui lòng bấm gửi lại.'];
        }
    } else {
        $sys_msg = ['type' => 'error', 'title' => 'Lỗi', 'msg' => 'Mã OTP không chính xác!'];
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Xác nhận OTP - MF SHOP</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="style.css">
</head>
<body style="display: flex; justify-content: center; align-items: center; min-height: 100vh;">

<div class="table-wrapper" style="width: 350px; text-align: center; margin: auto;">
    <h2 style="color: var(--secondary-color); margin-top:0;"><i class="fa fa-envelope-open-text"></i> Nhập mã OTP</h2>
    <p style="color: var(--text-muted); font-size: 14px;">Mã 6 số đã được gửi tới Email của bạn.</p>
    
    <form method="POST" action="">
        <div class="form-group">
            <input type="text" name="otp" maxlength="6" placeholder="------" style="font-size: 24px; text-align: center; letter-spacing: 5px; font-weight: bold;">
        </div>
        
        <button type="submit" name="verify_otp" class="btn btn-secondary" style="width: 100%; margin-bottom: 10px;">Xác nhận</button>
        
        <!-- NÚT GỬI LẠI OTP ĐƯỢC ĐẶT Ở ĐÂY -->
        <button type="submit" name="resend_otp" class="btn" style="width: 100%; background: #f4f6f9; color: var(--text-main); border: 1px solid var(--border-color);">
            <i class="fa fa-sync-alt"></i> Gửi lại OTP
        </button>
    </form>
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
    document.getElementById('modalTitle').innerText = title; 
    document.getElementById('modalMessage').innerText = msg;
    document.getElementById('modalIcon').className = type === 'success' ? 'fa fa-check-circle modal-icon icon-success' : 'fa fa-exclamation-circle modal-icon icon-error';
}
</script>
<?php if ($sys_msg) echo "<script>showSysModal('{$sys_msg['type']}', '{$sys_msg['title']}', '{$sys_msg['msg']}');</script>"; ?>
</body>
</html>