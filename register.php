<?php
session_start();
require 'db_connect.php'; 
require 'mail_config.php';

$sys_msg = null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']); 
    $password = md5($_POST['password']);
    $email = trim($_POST['email']); 
    $name = trim($_POST['name']);
    $phone = trim($_POST['phone']); 
    $address = trim($_POST['address']);
    $dob = $_POST['dob']; 
    $gender = (int)$_POST['gender'];

    // Kiểm tra trùng lặp User hoặc Email
    $check_stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $check_stmt->bind_param("ss", $username, $email); 
    $check_stmt->execute();
    
    if ($check_stmt->get_result()->num_rows > 0) {
        $sys_msg = ['type' => 'error', 'title' => 'Trùng lặp', 'msg' => 'Tên đăng nhập hoặc Email đã tồn tại!'];
    } else {
        $otp = sprintf("%06d", mt_rand(1, 999999)); 
        $expiry = date("Y-m-d H:i:s", strtotime('+5 minutes'));
        
        $stmt = $conn->prepare("INSERT INTO users (username, password, email, name, phone, address, dob, gender, role, otp_code, otp_expiry) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1, ?, ?)");
        
        // ĐÃ SỬA LỖI: Sửa "sssssssiiss" thành "sssssssiss" (10 định dạng tương ứng 10 biến)
        $stmt->bind_param("sssssssiss", $username, $password, $email, $name, $phone, $address, $dob, $gender, $otp, $expiry);
        
        if ($stmt->execute()) {
            $new_user_id = $conn->insert_id;
            
            if (sendOTP($email, $otp)) {
                // Lưu tạm session để sang trang verify_otp xác nhận
                $_SESSION['temp_user_id'] = $new_user_id;
                $_SESSION['temp_role'] = 1;
                $_SESSION['sys_msg'] = ['type' => 'success', 'title' => 'Đã gửi mã!', 'msg' => 'Vui lòng kiểm tra email để lấy mã OTP.'];
                header("Location: verify_otp.php"); 
                exit();
            } else {
                $sys_msg = ['type' => 'error', 'title' => 'Lỗi Email', 'msg' => 'Tài khoản đã tạo nhưng không thể gửi mã OTP!'];
            }
        } else {
            $sys_msg = ['type' => 'error', 'title' => 'Thất bại', 'msg' => 'Không thể tạo tài khoản do lỗi hệ thống!'];
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Đăng ký - MF SHOP</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="style.css">
</head>
<body style="display: flex; justify-content: center; align-items: center; min-height: 100vh; padding: 20px;">

<div class="table-wrapper" style="width: 450px; margin: auto;">
    <h2 style="text-align: center; color: var(--success-color); margin-top:0;"><i class="fa fa-user-plus"></i> Đăng Ký Tài Khoản</h2>
    
    <form method="POST" action="">
        <div class="form-group"><label>Tên đăng nhập:</label><input type="text" name="username" required></div>
        <div class="form-group"><label>Mật khẩu:</label><input type="password" name="password" required></div>
        <div class="form-group"><label>Email (Nhận mã OTP):</label><input type="email" name="email" required></div>
        <div class="form-group"><label>Họ và tên:</label><input type="text" name="name" required></div>
        <div class="form-group"><label>Số điện thoại:</label><input type="text" name="phone" required></div>
        <div class="form-group"><label>Địa chỉ:</label><textarea name="address" rows="2" required></textarea></div>
        
        <div class="variant-flex" style="display:flex; gap:10px;">
            <div class="form-group" style="flex:1;"><label>Ngày sinh:</label><input type="date" name="dob" required></div>
            <div class="form-group" style="flex:1;"><label>Giới tính:</label>
                <select name="gender">
                    <option value="0">Nam</option>
                    <option value="1">Nữ</option>
                    <option value="2">Khác</option>
                </select>
            </div>
        </div>
        
        <button type="submit" class="btn btn-success" style="width: 100%; padding:12px; font-size:16px;">Đăng ký</button>
    </form>
    
    <div style="text-align: center; margin-top: 15px;">
        <a href="login.php" style="color: var(--secondary-color); font-weight:bold;">Đã có tài khoản? Đăng nhập</a>
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
    document.getElementById('modalTitle').innerText = title; 
    document.getElementById('modalMessage').innerText = msg;
    document.getElementById('modalIcon').className = type === 'success' ? 'fa fa-check-circle modal-icon icon-success' : 'fa fa-exclamation-circle modal-icon icon-error';
}
</script>
<?php if ($sys_msg) echo "<script>showSysModal('{$sys_msg['type']}', '{$sys_msg['title']}', '{$sys_msg['msg']}');</script>"; ?>
</body>
</html>