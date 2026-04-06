<?php
require_once __DIR__ . '/../../includes/connect.php';

function liveSearch($con, $keyword)
{
    // Làm sạch dữ liệu đầu vào
    $keyword = mysqli_real_escape_string($con, trim($keyword));

    if (empty($keyword)) {
        return json_encode([]);
    }

    // Câu lệnh SQL lấy 10 sản phẩm đang active
    $sql = "SELECT id, name, slug, thumbnail, price 
            FROM `products` 
            WHERE `status` = 'active' 
            AND `deleted_at` IS NULL
            AND `name` LIKE '%$keyword%' 
            LIMIT 10";

    $result = mysqli_query($con, $sql);
    $products = [];

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $products[] = $row;
        }
    }

    header('Content-Type: application/json');
    return json_encode($products);
}

// Hàm Tìm kiếm đầy đủ có phân trang
function searchProducts($con, $keyword, $currentPage = 1)
{
    $keyword = mysqli_real_escape_string($con, trim($keyword));
    $limit = 10; // Số sản phẩm mỗi trang
    $offset = ($currentPage - 1) * $limit;

    // Nếu từ khóa trống
    if (empty($keyword)) {
        return [
            'products' => [],
            'keyword' => '',
            'total_pages' => 0,
            'message' => 'Vui lòng nhập từ khóa để tìm kiếm sản phẩm.'
        ];
    }

    // A. Tính tổng số sản phẩm tìm được để phân trang
    $count_sql = "SELECT COUNT(*) as total FROM `products` 
                  WHERE `status` = 'active' 
                  AND `deleted_at` IS NULL 
                  AND `name` LIKE '%$keyword%'";
    $count_res = mysqli_query($con, $count_sql);
    $total_rows = mysqli_fetch_assoc($count_res)['total'];
    $total_pages = ceil($total_rows / $limit);

    // B. Lấy danh sách sản phẩm theo offset (Sản phẩm mới nhất lên đầu)
    $sql = "SELECT * FROM `products` 
            WHERE `status` = 'active' 
            AND `deleted_at` IS NULL 
            AND `name` LIKE '%$keyword%' 
            ORDER BY id DESC 
            LIMIT $limit OFFSET $offset";

    $result = mysqli_query($con, $sql);
    $products = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $products[] = $row;
        }
    }

    return [
        'products' => $products,
        'keyword' => $keyword,
        'total_pages' => $total_pages,
        'current_page' => $currentPage,
        'message' => count($products) > 0 ? "" : "Không tìm thấy sản phẩm nào phù hợp."
    ];
}

// --- LOGIC ĐIỀU HƯỚNG GỌI HÀM ---
// Nếu có param 'action=live', gọi hàm liveSearch
if (isset($_GET['action']) && $_GET['action'] == 'live') {
    $kw = $_GET['keyword'] ?? '';
    echo liveSearch($con, $kw);
    exit();
}

?>