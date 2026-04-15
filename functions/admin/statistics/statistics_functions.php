<?php
// 1. Lấy dữ liệu doanh thu 12 tháng trong năm để vẽ biểu đồ line
function get_monthly_revenue($con, $year) {
    $monthly_data = array_fill(1, 12, 0); // Tạo mảng từ tháng 1-12 với giá trị 0
    $sql = "SELECT MONTH(created_at) as month, SUM(total_amount) as total 
            FROM orders 
            WHERE YEAR(created_at) = '$year' AND payment_status = 'paid'
            GROUP BY MONTH(created_at)";
    $result = mysqli_query($con, $sql);
    while($row = mysqli_fetch_assoc($result)) {
        $monthly_data[(int)$row['month']] = (float)$row['total'];
    }
    return array_values($monthly_data); // Trả về mảng chỉ chứa giá trị số
}

// 2. Lấy Top 5 sản phẩm bán chạy nhất
function get_top_selling_products($con, $limit = 5) {
    // Lưu ý: Kiểm tra xem bảng của bạn là 'order_items' hay 'order_details'
    // Kiểm tra tên cột là 'product_id' hay 'id_sanpham'
    $sql = "SELECT p.name, SUM(oi.quantity) as total_sold 
            FROM order_details oi 
            JOIN products p ON oi.product_id = p.id 
            GROUP BY oi.product_id 
            ORDER BY total_sold DESC LIMIT $limit";
            
    $result = mysqli_query($con, $sql);

    // Nếu câu lệnh SQL bị lỗi, đoạn này sẽ báo cho bạn biết lỗi gì
    if (!$result) {
        die("Lỗi SQL (Top sản phẩm): " . mysqli_error($con));
    }

    return $result;
}
// Lấy Top 5 khách hàng mua nhiều nhất
function get_top_customers($con, $limit = 5) {
    $sql = "SELECT u.name, SUM(o.total_amount) as total_spent 
            FROM orders o 
            JOIN users u ON o.user_id = u.id 
            WHERE o.payment_status = 'paid'
            GROUP BY o.user_id 
            ORDER BY total_spent DESC LIMIT $limit";
    return mysqli_query($con, $sql);
}
// Hàm lấy doanh thu có bộ lọc
function get_revenue_by_filter($con, $day = '', $month = '', $year = '') {
    $conditions = ["payment_status = 'paid'"]; // Chỉ tính đơn đã thanh toán

    if (!empty($day)) {
        $conditions[] = "DAY(created_at) = " . intval($day);
    }
    if (!empty($month)) {
        $conditions[] = "MONTH(created_at) = " . intval($month);
    }
    if (!empty($year)) {
        $conditions[] = "YEAR(created_at) = " . intval($year);
    }

    $where_sql = implode(" AND ", $conditions);
    $sql = "SELECT SUM(total_amount) as total FROM orders WHERE $where_sql";
    
    $result = mysqli_query($con, $sql);
    $row = mysqli_fetch_assoc($result);
    return $row['total'] ?? 0;
}
// Hàm lấy các chỉ số tổng quát
function get_total_stats($con) {
    // Tổng sản phẩm
    $products = mysqli_num_rows(mysqli_query($con, "SELECT id FROM products"));
    // Tổng đơn hàng
    $orders = mysqli_num_rows(mysqli_query($con, "SELECT id FROM orders"));
    // Tổng khách hàng
    $users = mysqli_num_rows(mysqli_query($con, "SELECT id FROM users WHERE role = 'user'"));
    // Tổng doanh thu (đơn đã thanh toán)
    $revenue_res = mysqli_query($con, "SELECT SUM(total_amount) as total FROM orders WHERE payment_status = 'paid'");
    $revenue_row = mysqli_fetch_assoc($revenue_res);
    $revenue = $revenue_row['total'] ?? 0;

    return [
        'products' => $products,
        'orders' => $orders,
        'users' => $users,
        'revenue' => $revenue
    ];
}

// Hàm lấy danh sách đơn hàng mới nhất
function get_latest_orders($con, $limit = 8) {
    $sql = "SELECT o.*, u.name as user_name 
            FROM orders o 
            JOIN users u ON o.user_id = u.id 
            ORDER BY o.created_at DESC LIMIT $limit";
    return mysqli_query($con, $sql);
}
?>