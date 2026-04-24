<?php

class OrderController
{
    private $con;
    public function __construct()
    {
        require_once __DIR__ . '/../../../includes/connect.php';
        // Gán biến kết nối từ file connect.php vào thuộc tính của class
        global $con;
        $this->con = $con;
    }

    // Danh sách đơn hàng
    public function index()
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?page=home");
            exit();
        }

        $user_id = $_SESSION['user_id'];

        $limit = 10; // Số lượng đơn hàng hiển thị trên mỗi trang
        $current_page = isset($_GET['p']) ? (int)$_GET['p'] : 1;
        if ($current_page < 1) $current_page = 1;
        $offset = ($current_page - 1) * $limit;

        // 1. Đếm tổng số đơn hàng để tính tổng số trang
        $sql_count = "SELECT COUNT(*) as total FROM orders WHERE user_id = ?";
        $stmt_count = mysqli_prepare($this->con, $sql_count);
        mysqli_stmt_bind_param($stmt_count, "i", $user_id);
        mysqli_stmt_execute($stmt_count);
        $total_orders = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_count))['total'];
        $total_pages = ceil($total_orders / $limit);

        // 2. Truy vấn đơn hàng có LIMIT và OFFSET
        $sql = "SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC LIMIT ? OFFSET ?";
        $stmt = mysqli_prepare($this->con, $sql);
        mysqli_stmt_bind_param($stmt, "iii", $user_id, $limit, $offset);
        mysqli_stmt_execute($stmt);
        $orders = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);

        return [
            'web_title' => 'Đơn hàng của tôi - MTShop',
            'content_file' => 'users_area/checkout/my-orders.php',
            'orders' => $orders,
            'total_pages' => $total_pages,
            'current_page' => $current_page
        ];
    }

    // Chi tiết đơn hàng
    public function show($order_code)
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php");
            exit();
        }

        $user_id = $_SESSION['user_id'];

        // Lấy thông tin đơn hàng
        $sql_order = "SELECT * FROM orders WHERE order_code = ? AND user_id = ?";
        $stmt = mysqli_prepare($this->con, $sql_order);
        mysqli_stmt_bind_param($stmt, "si", $order_code, $user_id);
        mysqli_stmt_execute($stmt);
        $order = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

        if (!$order) {
            header("Location: index.php?page=404");
            exit();
        }

        // Lấy chi tiết sản phẩm
        $sql_items = "SELECT * FROM order_details WHERE order_id = ?";
        $stmt_items = mysqli_prepare($this->con, $sql_items);
        mysqli_stmt_bind_param($stmt_items, "i", $order['id']);
        mysqli_stmt_execute($stmt_items);
        $order_items = mysqli_fetch_all(mysqli_stmt_get_result($stmt_items), MYSQLI_ASSOC);

        return [
            'web_title' => 'Chi tiết đơn hàng #' . $order_code,
            'content_file' => 'users_area/checkout/order-detail.php',
            'order' => $order,
            'order_items' => $order_items
        ];
    }
}
