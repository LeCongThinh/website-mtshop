<?php
// Kết nối database
include __DIR__ . '/../../../includes/connect.php';

// Kiểm tra nếu biến $con chưa tồn tại thì báo lỗi để dễ debug
if (!$con) {
    die("Kết nối database thất bại trong controller.");
}

if (isset($_GET['action']) && $_GET['action'] == 'get_revenue_data') {
    if (ob_get_length()) ob_clean();

    $type = $_GET['type'] ?? 'week';
    $month = (int) ($_GET['month'] ?? date('m'));
    $year = (int) ($_GET['year'] ?? date('Y'));

    $labels = [];
    $data = [];

    if ($type == 'year') {
        for ($m = 1; $m <= 12; $m++) {
            $labels[] = "Tháng $m";
            $sql = "SELECT SUM(total_amount) as total FROM `orders` 
                    WHERE MONTH(created_at) = $m AND YEAR(created_at) = $year 
                    AND (status = 'delivered' OR payment_status = 'paid')";
            $res = mysqli_fetch_assoc(mysqli_query($con, $sql));
            $data[] = (float) ($res['total'] ?? 0);
        }
    } elseif ($type == 'month') {
        $labels = ['Tuần 1', 'Tuần 2', 'Tuần 3', 'Tuần 4'];
        $weeks = [
            ['s' => 1, 'e' => 7], ['s' => 8, 'e' => 14],
            ['s' => 15, 'e' => 21], ['s' => 22, 'e' => 31]
        ];
        foreach ($weeks as $w) {
            $sql = "SELECT SUM(total_amount) as total FROM `orders` 
                    WHERE DAY(created_at) BETWEEN {$w['s']} AND {$w['e']} 
                    AND MONTH(created_at) = $month AND YEAR(created_at) = $year
                    AND (status = 'delivered' OR payment_status = 'paid')";
            $res = mysqli_fetch_assoc(mysqli_query($con, $sql));
            $data[] = (float) ($res['total'] ?? 0);
        }
    } else { 
    // ==================== THỐNG KÊ THEO TUẦN ====================
    $labels = [];
    $data   = [];

    if (isset($_GET['start_date']) && !empty($_GET['start_date'])) {
        // ←←← ĐÃ SỬA: Click từ biểu đồ tháng → dùng đúng ngày bắt đầu (1,8,15,22)
        $start_date = $_GET['start_date'];
    } else {
        // Xem tuần bình thường (không click từ tháng) → vẫn lấy từ Thứ 2
        $dt = new DateTime();
        $dt->setISODate((int) date('Y'), (int) date('W'));
        $start_date = $dt->format('Y-m-d');
    }

    for ($i = 0; $i < 7; $i++) {
        $curr = date('Y-m-d', strtotime("$start_date +$i days"));
        
        $labels[] = date('d/m', strtotime($curr));   // vd: 08/04

        $sql = "SELECT SUM(total_amount) as total FROM `orders` 
                WHERE DATE(created_at) = '$curr' 
                AND (status = 'delivered' OR payment_status = 'paid')";

        $query = mysqli_query($con, $sql);
        $res = mysqli_fetch_assoc($query);
        $data[] = (float) ($res['total'] ?? 0);
    }
}

    header('Content-Type: application/json');
    echo json_encode(['labels' => $labels, 'data' => $data], JSON_NUMERIC_CHECK);
    exit;
}
// 1. TỔNG DOANH THU & % TĂNG TRƯỞNG
$month_now = date('m');
$year_now = date('Y');
$rev_now_query = "SELECT SUM(total_amount) as total FROM `orders` 
                  WHERE MONTH(created_at) = '$month_now' AND YEAR(created_at) = '$year_now' 
                  AND (payment_status = 'paid' OR status = 'delivered')";
$rev_now = mysqli_fetch_assoc(mysqli_query($con, $rev_now_query))['total'] ?? 0;

$month_last = date('m', strtotime("-1 month"));
$year_last = date('Y', strtotime("-1 month"));
$rev_last_query = "SELECT SUM(total_amount) as total FROM `orders` 
                   WHERE MONTH(created_at) = '$month_last' AND YEAR(created_at) = '$year_last' 
                   AND (payment_status = 'paid' OR status = 'delivered')";
$rev_last = mysqli_fetch_assoc(mysqli_query($con, $rev_last_query))['total'] ?? 0;

$rev_percent = ($rev_last > 0) ? round((($rev_now - $rev_last) / $rev_last) * 100, 1) : 0;

// 2. TỔNG ĐƠN HÀNG & % HOÀN TẤT
$total_orders_query = "SELECT COUNT(*) as total FROM `orders`";
$total_orders = mysqli_fetch_assoc(mysqli_query($con, $total_orders_query))['total'];

$completed_orders_query = "SELECT COUNT(*) as total FROM `orders` WHERE status = 'delivered'";
$completed_orders = mysqli_fetch_assoc(mysqli_query($con, $completed_orders_query))['total'];
$complete_rate = ($total_orders > 0) ? round(($completed_orders / $total_orders) * 100, 1) : 0;

// 3. KHÁCH HÀNG MỚI (7 ngày qua)
$new_cust_query = "SELECT COUNT(*) as total FROM `users` WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
$new_customers = mysqli_fetch_assoc(mysqli_query($con, $new_cust_query))['total'] ?? 0;
// 2. Số lượng khách hàng mới của 7 ngày TRƯỚC ĐÓ (để so sánh)
$old_cust_query = "SELECT COUNT(*) as total FROM `users` 
                   WHERE created_at >= DATE_SUB(NOW(), INTERVAL 14 DAY) 
                   AND created_at < DATE_SUB(NOW(), INTERVAL 7 DAY)";
$old_customers = mysqli_fetch_assoc(mysqli_query($con, $old_cust_query))['total'] ?? 0;
$cust_percent = 0;
if ($old_customers > 0) {
    $cust_percent = round((($new_customers - $old_customers) / $old_customers) * 100, 1);
} else {
    $cust_percent = ($new_customers > 0) ? 100 : 0;
}

// 4. ĐƠN HÀNG CHỜ DUYỆT
$pending_query = "SELECT COUNT(*) as total FROM `orders` WHERE status = 'pending'";
$pending_orders = mysqli_fetch_assoc(mysqli_query($con, $pending_query))['total'] ?? 0;

// 5. LẤY 5 ĐƠN HÀNG MỚI NHẤT
$latest_orders_query = "SELECT o.*, u.name as customer_name 
                        FROM `orders` o 
                        LEFT JOIN `users` u ON o.user_id = u.id 
                        ORDER BY o.created_at DESC 
                        LIMIT 5";
$run_latest_orders = mysqli_query($con, $latest_orders_query);

// 6. LẤY 5 SẢN PHẨM BÁN CHẠY NHẤT
$top_products_query = "SELECT 
                        od.product_id, 
                        od.product_name, 
                        od.price, 
                        od.product_thumbnail,
                        SUM(od.quantity) as total_sold,
                        SUM(od.subtotal) as total_revenue
                    FROM `order_details` od
                    INNER JOIN `orders` o ON od.order_id = o.id 
                    WHERE o.payment_status = 'paid'
                    GROUP BY od.product_id
                    ORDER BY total_sold DESC
                    LIMIT 5";

$run_top_products = mysqli_query($con, $top_products_query);

// 7. TỶ LỆ THEO TRẠNG THÁI ĐƠN HÀNG
// Lấy tổng số đơn hàng để tính %
$sql_total = "SELECT COUNT(*) as total FROM `orders`";
$total_all = mysqli_fetch_assoc(mysqli_query($con, $sql_total))['total'] ?? 0;

// 2. Lấy số lượng từng trạng thái
// Lưu ý: Bạn hãy thay đổi giá trị 'pending', 'confirmed', ... cho khớp với database của bạn
$sql_status = "SELECT 
    COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending_count,
    COUNT(CASE WHEN status = 'confirmed' THEN 1 END) as confirmed_count,
    COUNT(CASE WHEN status = 'shipping' THEN 1 END) as shipping_count,
    COUNT(CASE WHEN status = 'delivered' THEN 1 END) as delivered_count,
    COUNT(CASE WHEN status = 'cancelled' THEN 1 END) as cancelled_count
FROM `orders`";

$counts = mysqli_fetch_assoc(mysqli_query($con, $sql_status));

// 3. Hàm tính % 
function get_percentage($count, $total)
{
    return ($total > 0) ? round(($count / $total) * 100) : 0;
}

$p_pending = get_percentage($counts['pending_count'], $total_all);
$p_confirmed = get_percentage($counts['confirmed_count'], $total_all);
$p_shipping = get_percentage($counts['shipping_count'], $total_all);
$p_delivered = get_percentage($counts['delivered_count'], $total_all);
$p_cancelled = get_percentage($counts['cancelled_count'], $total_all);


?>