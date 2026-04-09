<?php
// ==================== XỬ LÝ GIỎ HÀNG BẰNG PHP THUẦN ====================
// File này chỉ xử lý POST (thêm/sửa/xóa số lượng)

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../../includes/connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cart_action'])) {
    $action     = $_POST['cart_action'];
    $product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
    $user_id    = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;

    if ($product_id > 0) {
        
        // --- TRƯỜNG HỢP 1: ĐÃ ĐĂNG NHẬP (Xử lý với DATABASE) ---
        if ($user_id > 0) {
            if ($action === 'remove') {
                $delete_query = "DELETE FROM `carts` WHERE user_id = $user_id AND product_id = $product_id";
                mysqli_query($con, $delete_query);
            } 
            elseif ($action === 'update_qty') {
                $qty = isset($_POST['qty']) ? (int)$_POST['qty'] : 0;
                if ($qty > 0) {
                    // Cập nhật số lượng mới trong DB
                    $update_query = "UPDATE `carts` SET quantity = $qty WHERE user_id = $user_id AND product_id = $product_id";
                    mysqli_query($con, $update_query);
                } else {
                    // Nếu số lượng bằng 0 thì xóa luôn sản phẩm
                    $delete_query = "DELETE FROM `carts` WHERE user_id = $user_id AND product_id = $product_id";
                    mysqli_query($con, $delete_query);
                }
            }
        } 
        
        // --- TRƯỜNG HỢP 2: CHƯA ĐĂNG NHẬP (Xử lý với SESSION) ---
        else {
            if (!isset($_SESSION['cart'])) {
                $_SESSION['cart'] = [];
            }

            if ($action === 'remove') {
                unset($_SESSION['cart'][$product_id]);
            } 
            elseif ($action === 'update_qty') {
                $qty = isset($_POST['qty']) ? (int)$_POST['qty'] : 0;
                if ($qty > 0) {
                    $_SESSION['cart'][$product_id] = $qty;
                } else {
                    unset($_SESSION['cart'][$product_id]);
                }
            }
        }
    }

    // --- PHẦN ĐIỀU HƯỚNG (REDIRECT) ---
    // Kiểm tra xem user đang ở trang nào để quay lại đúng trang đó
    $redirect = "index.php?page=cart"; // Mặc định về giỏ hàng
    
    // Nếu trang web của bạn dùng cơ chế index.php?page=...
    if (isset($_GET['page'])) {
        $redirect = "index.php?page=" . $_GET['page'];
    } elseif (isset($_SERVER['HTTP_REFERER'])) {
        // Hoặc quay lại trang trước đó bằng header referer
        $redirect = $_SERVER['HTTP_REFERER'];
    }

    header("Location: " . $redirect);
    exit;
}
?>