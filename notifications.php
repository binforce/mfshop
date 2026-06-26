<?php
require 'db_connect.php';
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }
$uid = $_SESSION['user_id'];
$noti = $conn->query("SELECT * FROM notifications WHERE user_id=$uid ORDER BY created_at DESC");
$conn->query("UPDATE notifications SET is_read=1 WHERE user_id=$uid");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Thông báo - MF SHOP</title>
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
        <div><a href="index.php" class="btn btn-secondary"><i class="fa fa-arrow-left"></i> Về Trang Chủ</a></div>
    </div>

    <div class="main-container" style="min-height: 60vh;">
        <div class="table-wrapper" style="width: 100%; max-width: 700px; margin: auto;">
            <h2 style="margin-top:0; color:var(--text-main); border-bottom:2px solid var(--border-color); padding-bottom:10px;"><i class="fa fa-bell"></i> Hộp Thư Thông Báo</h2>
            <?php if($noti->num_rows > 0): while($n = $noti->fetch_assoc()): ?>
                <div style="border-bottom: 1px solid var(--border-color); padding: 15px; border-radius:4px; margin-bottom:10px; <?= $n['is_read']==0 ? 'background:#ebf5fb;' : 'background:#fff;' ?>">
                    <h4 style="margin: 0; color: var(--secondary-color);"><?= htmlspecialchars($n['title']) ?></h4>
                    <p style="margin: 8px 0; color: var(--text-main); line-height: 1.5;"><?= htmlspecialchars($n['message']) ?></p>
                    <small style="color: var(--text-muted);"><i class="fa fa-clock"></i> <?= $n['created_at'] ?></small>
                </div>
            <?php endwhile; else: ?>
                <div style="text-align:center; padding: 40px; color: var(--text-muted);">
                    <i class="fa fa-box-open" style="font-size:40px; margin-bottom:10px;"></i>
                    <p>Bạn không có thông báo nào.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <footer style="background: #ffffff; padding: 25px 20px; text-align: center; border-top: 1px solid var(--border-color); margin-top: 40px; color: var(--text-muted); font-size: 14px; font-weight: 500;">
        &copy; <?= date("Y") ?> Bản quyền thuộc về <b>P.S.B.T.S</b>. Hệ thống cửa hàng thời trang MF SHOP.
    </footer>
</body>
</html>