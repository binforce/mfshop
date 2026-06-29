<?php
// ==========================================
// HÀNH ĐỘNG XỬ LÝ CRUD KHUYẾN MÃI & CHIẾN DỊCH
// ==========================================
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_promo'])) {
$code = strtoupper(trim($conn->real_escape_string($_POST['code']))); $amount = (int)$_POST['discount_amount']; $percent = (int)$_POST['discount_percent'];
$is_free = isset($_POST['is_freeship']) ? 1 : 0; $min_order = (int)$_POST['min_order']; $limit = (int)$_POST['usage_limit']; $start = $conn->real_escape_string($_POST['start_date']); $end = $conn->real_escape_string($_POST['end_date']);
try { $conn->query("INSERT INTO promotions (code, discount_amount, discount_percent, is_freeship, min_order, usage_limit, start_date, end_date) VALUES ('$code', $amount, $percent, $is_free, $min_order, $limit, '$start', '$end')"); $_SESSION['sys_msg'] = ['type'=>'success','title'=>'Thành công','msg'=>'Đã phát hành mã mới!']; } catch(Exception $e) { $_SESSION['sys_msg'] = ['type'=>'error','title'=>'Lỗi','msg'=>'Mã giảm giá bị trùng!']; }
header("Location: admin.php?action=promotions&tab=vouchers"); exit();
}
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_promo'])) {
$id = (int)$_POST['promo_id']; $code = strtoupper(trim($conn->real_escape_string($_POST['code']))); $amount = (int)$_POST['discount_amount']; $percent = (int)$_POST['discount_percent']; $is_free = isset($_POST['is_freeship']) ? 1 : 0; $min_order = (int)$_POST['min_order']; $limit = (int)$_POST['usage_limit']; $start = $conn->real_escape_string($_POST['start_date']); $end = $conn->real_escape_string($_POST['end_date']);
try { $conn->query("UPDATE promotions SET code='$code', discount_amount=$amount, discount_percent=$percent, is_freeship=$is_free, min_order=$min_order, usage_limit=$limit, start_date='$start', end_date='$end' WHERE id=$id"); $_SESSION['sys_msg'] = ['type'=>'success','title'=>'Thành công','msg'=>'Cập nhật thành công!']; } catch(Exception $e) { $_SESSION['sys_msg'] = ['type'=>'error','title'=>'Lỗi','msg'=>'Mã Code bị trùng lặp!']; }
header("Location: admin.php?action=promotions&tab=vouchers"); exit();
}
if (isset($_GET['delete_promo'])) { $id = (int)$_GET['delete_promo']; $conn->query("DELETE FROM promotions WHERE id=$id"); $_SESSION['sys_msg'] = ['type'=>'success','title'=>'Đã xóa','msg'=>'Xóa mã thành công!']; header("Location: admin.php?action=promotions&tab=vouchers"); exit(); }
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_campaign'])) {
$name = $conn->real_escape_string($_POST['campaign_name']); $percent = (int)$_POST['discount_percent']; $start = $conn->real_escape_string($_POST['start_date']); $end = $conn->real_escape_string($_POST['end_date']);
$conn->query("INSERT INTO campaigns (name, discount_percent, start_date, end_date) VALUES ('$name', $percent, '$start', '$end')"); $campaign_id = $conn->insert_id;
if (!empty($_POST['campaign_products'])) { foreach ($_POST['campaign_products'] as $p_id) { $conn->query("UPDATE products SET campaign_id = $campaign_id, sale_price = price - (price * $percent / 100) WHERE id = ".(int)$p_id); } }
$_SESSION['sys_msg'] = ['type'=>'success','title'=>'Thành công','msg'=>'Đã áp dụng chiến dịch khuyến mãi!']; header("Location: admin.php?action=promotions&tab=campaigns"); exit();
}
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_campaign'])) {
$c_id = (int)$_POST['campaign_id']; $name = $conn->real_escape_string($_POST['campaign_name']); $percent = (int)$_POST['discount_percent']; $start = $conn->real_escape_string($_POST['start_date']); $end = $conn->real_escape_string($_POST['end_date']);
$conn->query("UPDATE campaigns SET name='$name', discount_percent=$percent, start_date='$start', end_date='$end' WHERE id=$c_id"); $conn->query("UPDATE products SET campaign_id = NULL, sale_price = 0 WHERE campaign_id = $c_id");
if (!empty($_POST['campaign_products'])) { foreach ($_POST['campaign_products'] as $p_id) { $conn->query("UPDATE products SET campaign_id = $c_id, sale_price = price - (price * $percent / 100) WHERE id = ".(int)$p_id); } }
$_SESSION['sys_msg'] = ['type'=>'success','title'=>'Thành công','msg'=>'Cập nhật chương trình thành công!']; header("Location: admin.php?action=promotions&tab=campaigns"); exit();
}
if (isset($_GET['delete_campaign'])) {
$id = (int)$_GET['delete_campaign']; $conn->query("UPDATE products SET campaign_id = NULL, sale_price = 0 WHERE campaign_id = $id"); $conn->query("DELETE FROM campaigns WHERE id = $id");
$_SESSION['sys_msg'] = ['type'=>'success','title'=>'Đã xóa','msg'=>'Đã kết thúc chiến dịch khuyến mãi!']; header("Location: admin.php?action=promotions&tab=campaigns"); exit();
}
// XÓA ĐƠN, KHÁCH, DANH MỤC (VÀ CẬP NHẬT LẠI LOGIC XÓA SẢN PHẨM THÀNH ẨN SẢN PHẨM)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['bulk_delete_products'])) {
if (!empty($_POST['selected_products'])) {
$success_count = 0; $error_count = 0; foreach ($_POST['selected_products'] as $id) { try { $conn->query("UPDATE products SET is_hidden = 1 WHERE id=".(int)$id); $success_count++; } catch (Exception $e) { $error_count++; } }
}
$msg = "Đã ẩn thành công $success_count sản phẩm."; if ($error_count > 0) $msg .= " Bỏ qua $error_count sản phẩm do lỗi.";
$_SESSION['sys_msg'] = ['type' => ($error_count > 0 ? 'error' : 'success'), 'title' => 'Ẩn hàng loạt', 'msg' => $msg];
header("Location: admin.php?action=products"); exit();
}
if (isset($_GET['delete_product'])) { $id = (int)$_GET['delete_product']; try { $conn->query("UPDATE products SET is_hidden = 1 WHERE id=$id"); $_SESSION['sys_msg'] = ['type' => 'success', 'title' => 'Thành công', 'msg' => 'Đã ẩn sản phẩm khỏi hệ thống (vẫn giữ lịch sử bán)!']; } catch (Exception $e) { $_SESSION['sys_msg'] = ['type' => 'error', 'title' => 'Lỗi', 'msg' => 'Không thể ẩn.']; } header("Location: admin.php?action=products"); exit(); }
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['apply_bulk_action'])) {
if (!empty($_POST['selected_products']) && !empty($_POST['bulk_action'])) {
$action_type = $_POST['bulk_action']; $val = (int)$_POST['discount_value']; $ids = implode(',', array_map('intval', $_POST['selected_products']));
if ($action_type == 'discount_percent') { $conn->query("UPDATE products SET sale_price = price - (price * $val / 100) WHERE id IN ($ids)"); }
elseif ($action_type == 'discount_amount') { $conn->query("UPDATE products SET sale_price = GREATEST(price - $val, 0) WHERE id IN ($ids)"); }
elseif ($action_type == 'reset_discount') { $conn->query("UPDATE products SET sale_price = 0, campaign_id = NULL WHERE id IN ($ids)"); }
elseif ($action_type == 'delete') { foreach ($_POST['selected_products'] as $id) { try{$conn->query("UPDATE products SET is_hidden = 1 WHERE id=".(int)$id);}catch(Exception $e){} } }
$_SESSION['sys_msg'] = ['type' => 'success', 'title' => 'Thành công', 'msg' => 'Thực thi hành động hàng loạt thành công!'];
}
header("Location: admin.php?action=products"); exit();
}
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_category'])) { $c_id = (int)$_POST['cat_id']; $c_name = $conn->real_escape_string($_POST['cat_name']); $conn->query("UPDATE categories SET name='$c_name' WHERE id=$c_id"); $_SESSION['sys_msg'] = ['type' => 'success', 'title' => 'Thành công', 'msg' => 'Cập nhật danh mục thành công!']; header("Location: admin.php?action=categories"); exit(); }
if (isset($_GET['delete_category'])) { $id = (int)$_GET['delete_category']; $conn->query("UPDATE products SET category_id=NULL WHERE category_id=$id"); $conn->query("DELETE FROM categories WHERE id=$id"); $_SESSION['sys_msg'] = ['type' => 'success', 'title' => 'Thành công', 'msg' => 'Đã xóa danh mục!']; header("Location: admin.php?action=categories"); exit(); }
if (isset($_GET['delete_order'])) { $id = (int)$_GET['delete_order']; $conn->query("DELETE FROM order_details WHERE order_id=$id"); $conn->query("DELETE FROM orders WHERE id=$id"); $_SESSION['sys_msg'] = ['type' => 'success', 'title' => 'Thành công', 'msg' => 'Đã xóa đơn hàng vĩnh viễn!']; header("Location: admin.php?action=orders"); exit(); }
if (isset($_GET['delete_customer'])) { $id = (int)$_GET['delete_customer']; try { $conn->query("DELETE FROM notifications WHERE user_id=$id"); $conn->query("DELETE FROM users WHERE id=$id"); $_SESSION['sys_msg'] = ['type' => 'success', 'title' => 'Thành công', 'msg' => 'Đã xóa khách hàng!']; } catch (Exception $e) { $_SESSION['sys_msg'] = ['type' => 'error', 'title' => 'Không thể xóa', 'msg' => 'Khách hàng này đã có lịch sử mua.']; } header("Location: admin.php?action=customers"); exit(); }
?>