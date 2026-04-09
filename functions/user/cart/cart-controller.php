<?php
session_start();
require_once __DIR__ . '/../../../includes/connect.php';

// Thiết lập phản hồi JSON cho AJAX
header('Content-Type: application/json');

// Debug: Log errors
error_log('Cart request received');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // PHP thuần nhận dữ liệu qua $_POST khi gửi bằng FormData hoặc URLSearchParams
    $product_id = isset($_POST['product_id']) ? (int) $_POST['product_id'] : 0;
    $quantity = isset($_POST['quantity']) ? (int) $_POST['quantity'] : 1;
    $user_id = $_SESSION['user_id'] ?? null;

    error_log("Product ID: $product_id, Quantity: $quantity, User ID: $user_id");

    if ($product_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Sản phẩm không hợp lệ']);
        exit;
    }

    if ($user_id) {
        // --- LUỒNG ĐÃ ĐĂNG NHẬP: LƯU VÀO DATABASE ---
        $check_cart = mysqli_query($con, "SELECT id, quantity FROM carts WHERE user_id = $user_id AND product_id = $product_id");

        if (!$check_cart) {
            echo json_encode(['success' => false, 'message' => 'Lỗi cơ sở dữ liệu: ' . mysqli_error($con)]);
            exit;
        }

        if (mysqli_num_rows($check_cart) > 0) {
            $row = mysqli_fetch_assoc($check_cart);
            $new_qty = $row['quantity'] + $quantity;
            $update_sql = "UPDATE carts SET quantity = $new_qty, updated_at = NOW() WHERE id = " . $row['id'];
            $update_result = mysqli_query($con, $update_sql);
            if (!$update_result) {
                error_log("Update error: " . mysqli_error($con));
            }
        } else {
            $insert_sql = "INSERT INTO carts (user_id, product_id, quantity, created_at, updated_at) 
                           VALUES ($user_id, $product_id, $quantity, NOW(), NOW())";
            $insert_result = mysqli_query($con, $insert_sql);
            if (!$insert_result) {
                error_log("Insert error: " . mysqli_error($con));
            }
        }
    } else {
        // --- LUỒNG CHƯA ĐĂNG NHẬP: LƯU VÀO SESSION ---
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        // Lưu theo dạng [id_san_pham => so_luong]
        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id] += $quantity;
        } else {
            $_SESSION['cart'][$product_id] = $quantity;
        }
    }

    // Tính toán tổng số lượng hiện có để gửi về cho Header cập nhật Badge
    $total_count = 0;
    if ($user_id) {
        $res = mysqli_query($con, "SELECT SUM(quantity) as total FROM carts WHERE user_id = $user_id");
        if ($res) {
            $row = mysqli_fetch_assoc($res);
            $total_count = $row['total'] ?? 0;
        }
    } else {
        $total_count = isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0;
    }

    error_log("Response: success=true, count=$total_count");
    echo json_encode([
        'success' => true,
        'count' => $total_count
    ]);
    exit;
}

?>