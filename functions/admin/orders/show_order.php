<?php
// Tắt hiển thị lỗi trực tiếp để tránh làm hỏng cấu trúc JSON
error_reporting(0); 
include __DIR__ . '/../../../includes/connect.php';

header('Content-Type: application/json');

if (isset($_GET['id'])) {
    $order_id = mysqli_real_escape_string($con, $_GET['id']);

    // 1. Lấy thông tin từ bảng orders
    $order_query = "SELECT * FROM `orders` WHERE id = '$order_id'";
    $order_res = mysqli_query($con, $order_query);
    
    if (!$order_res) {
        echo json_encode(['success' => false, 'message' => 'Lỗi truy vấn đơn hàng']);
        exit;
    }

    $order = mysqli_fetch_assoc($order_res);

    if ($order) {
        // 2. Lấy danh sách sản phẩm từ bảng order_details
        $details_query = "SELECT * FROM `order_details` WHERE order_id = '$order_id'";
        $details_res = mysqli_query($con, $details_query);

        $items = [];
        while ($row = mysqli_fetch_assoc($details_res)) {
            $items[] = $row;
        }

        echo json_encode([
            'success' => true,
            'order' => $order,
            'items' => $items
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Không tìm thấy đơn hàng.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Thiếu ID đơn hàng.']);
}
?>