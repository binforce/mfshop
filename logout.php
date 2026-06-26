<?php
session_start();
session_unset(); // Xóa tất cả các biến trong session
session_destroy(); // Hủy toàn bộ session

// Đăng xuất xong thì tự động đẩy người dùng về lại trang chủ
header("Location: index.php");
exit();
?>