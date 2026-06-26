<?php
// generate_images_fast.php
set_time_limit(0); // Không giới hạn thời gian

$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'mfshop';

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) die("Lỗi DB: " . $conn->connect_error);

$uploadDir = __DIR__ . '/uploads/products/';
if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

// Xóa data cũ nếu muốn (bỏ comment)
// $conn->query("DELETE FROM product_images WHERE product_id BETWEEN 55 AND 154");

$productIds = range(55, 154);
$total = 0;

foreach ($productIds as $pid) {
    $num = rand(2, 4);
    for ($i = 1; $i <= $num; $i++) {
        $isPrimary = ($i == 1) ? 1 : 0;
        $filename = "product_{$pid}_{$i}.jpg";
        $filepath = $uploadDir . $filename;
        
        // Tạo ảnh 400x400 bằng GD
        $img = imagecreatetruecolor(400, 400);
        $r = (($pid * 13) % 200) + 55;
        $g = (($pid * 17) % 200) + 55;
        $b = (($pid * 23) % 200) + 55;
        $bg = imagecolorallocate($img, $r, $g, $b);
        imagefill($img, 0, 0, $bg);
        $white = imagecolorallocate($img, 255, 255, 255);
        imagerectangle($img, 5, 5, 394, 394, $white);
        
        $text = "SP-$pid";
        $tw = imagefontwidth(5) * strlen($text);
        $x = (400 - $tw) / 2;
        $y = 180;
        imagestring($img, 5, $x, $y, $text, $white);
        
        $sub = "Ảnh $i / $num";
        $sw = imagefontwidth(3) * strlen($sub);
        imagestring($img, 3, (400 - $sw)/2, $y + 25, $sub, $white);
        
        imagejpeg($img, $filepath, 80);
        imagedestroy($img);
        
        $url = "uploads/products/$filename";
        $check = $conn->query("SELECT id FROM product_images WHERE product_id = $pid AND image_url = '$url'");
        if ($check->num_rows == 0) {
            $conn->query("INSERT INTO product_images (product_id, image_url, is_primary) VALUES ($pid, '$url', $isPrimary)");
            $total++;
        }
    }
    echo "Đã xử lý SP $pid <br>";
    flush();
}

echo "✅ Hoàn thành! Đã tạo $total ảnh.";
$conn->close();
?>