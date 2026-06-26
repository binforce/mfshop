<?php
require 'Exception.php';
require 'PHPMailer.php';
require 'SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Hàm gửi mã OTP
function sendOTP($email, $otp) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com'; 
        $mail->SMTPAuth   = true;
        // THAY EMAIL VÀ MẬT KHẨU ỨNG DỤNG CỦA BẠN VÀO 2 DÒNG DƯỚI
        $mail->Username   = 'daoanhtuan18012006@gmail.com'; 
        $mail->Password   = 'jlkc dtdt mqqc zkhg'; 
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; 
        $mail->Port       = 465;

        $mail->setFrom('daoanhtuan18012006@gmail.com', 'MF SHOP');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = 'Ma xac thuc OTP - MF SHOP';
        $mail->Body    = "Mã xác thực OTP của bạn là: <b style='font-size:20px; color:#3498db;'>$otp</b>. Mã có hiệu lực trong 5 phút.";

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

// Hàm gửi Hóa đơn (Bill) cho khách hàng
function sendBillEmail($customer_email, $customer_name, $order_id, $items, $total, $vat) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com'; 
        $mail->SMTPAuth   = true;
        // THAY EMAIL VÀ MẬT KHẨU ỨNG DỤNG CỦA BẠN VÀO 2 DÒNG DƯỚI
        $mail->Username   = 'daoanhtuan18012006@gmail.com'; 
        $mail->Password   = 'jlkc dtdt mqqc zkhg'; 
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; 
        $mail->Port       = 465;

        $mail->setFrom('daoanhtuan18012006@gmail.com', 'MF SHOP');
        $mail->addAddress($customer_email);
        $mail->isHTML(true);
        $mail->Subject = "Hoa don mua hang #" . $order_id . " - MF SHOP";

        $shop_name = "MF SHOP";
        $owner_name = "Quản Lý Shop";
        $phone_contact = "1900 1xxx";
        $support_email = "support@mfshop.com";
        $delivery_date = date('d/m/Y', strtotime('+3 days'));

        $items_html = "";
        foreach ($items as $item) {
            $items_html .= "
            <tr>
                <td style='border: 1px solid #ddd; padding: 8px;'><img src='{$item['image']}' width='50' alt='SP'></td>
                <td style='border: 1px solid #ddd; padding: 8px;'>{$item['name']}<br><small>Biến thể: {$item['variant']}</small></td>
                <td style='border: 1px solid #ddd; padding: 8px;'>{$item['quantity']}</td>
                <td style='border: 1px solid #ddd; padding: 8px;'>" . number_format($item['price']) . "đ</td>
            </tr>";
        }

        $mail->Body = "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: auto; border: 1px solid #eee; padding: 20px;'>
            <h2 style='color: #e74c3c; text-align: center;'>CẢM ƠN BẠN ĐÃ ĐẶT HÀNG!</h2>
            <p>Chào <b>{$customer_name}</b>, đơn hàng <b>#{$order_id}</b> của bạn đã được ghi nhận.</p>
            <table style='width: 100%; border-collapse: collapse; margin-bottom: 20px;'>
                <tr style='background: #f4f4f4;'>
                    <th style='border: 1px solid #ddd; padding: 8px;'>Hình</th>
                    <th style='border: 1px solid #ddd; padding: 8px;'>Sản phẩm</th>
                    <th style='border: 1px solid #ddd; padding: 8px;'>SL</th>
                    <th style='border: 1px solid #ddd; padding: 8px;'>Giá</th>
                </tr>
                {$items_html}
            </table>
            <p><b>Thuế VAT (10%):</b> " . number_format($vat) . "đ</p>
            <h3 style='color: #e74c3c;'>Tổng tiền thanh toán: " . number_format($total) . "đ</h3>
            <div style='background: #f9f9f9; padding: 15px; margin-top: 20px;'>
                <p><b>Dự kiến giao hàng:</b> {$delivery_date}</p>
                <hr>
                <p><b>Thông tin liên hệ Shop:</b></p>
                <p>Chủ cửa hàng: {$owner_name} | {$shop_name}</p>
                <p>Hotline: {$phone_contact} | Hỗ trợ: {$support_email}</p>
            </div>
        </div>";

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}
?>