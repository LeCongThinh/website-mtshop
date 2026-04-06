<?php
require_once(__DIR__ . "/../includes/connect.php");
require_once(__DIR__ . "/../functions/user/handle-product/product-controller.php");
require_once(__DIR__ . "/../functions/user/home-controller.php");
require_once(__DIR__ . "/../functions/user/search-controller.php");

// Load categories cho header
$result = mysqli_query($con, "SELECT * FROM categories WHERE parent_id IS NULL AND status = 'active'");
$categories = mysqli_fetch_all($result, MYSQLI_ASSOC);

foreach ($categories as &$category) {
    $stmt = mysqli_prepare($con, "SELECT * FROM categories WHERE parent_id = ? AND status = 'active'");
    mysqli_stmt_bind_param($stmt, "i", $category['id']);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $category['children'] = mysqli_fetch_all($res, MYSQLI_ASSOC);
}
unset($category);

$page = $_GET['page'] ?? 'home';

if ($page === 'home') {
    // Danh sách sản phẩm mới nhất
    $product_res = mysqli_query($con, "SELECT * FROM products WHERE status = 'active' ORDER BY id DESC LIMIT 10");
    $products = mysqli_fetch_all($product_res, MYSQLI_ASSOC);

    // Danh sách bài viết mới nhất
    $result = mysqli_query($con, "SELECT * FROM posts WHERE status = 'active' ORDER BY created_at DESC LIMIT 10");
    $posts = mysqli_fetch_all($result, MYSQLI_ASSOC);

    // Danh sách PC bán chạy nhất
    $pcData = getBestSellingProducts($con, 'pc', 10);
    // Danh sách Laptop bán chạy nhất
    $laptopData = getBestSellingProducts($con, 'laptop', 10);
    // Danh sách Màn hình bán chạy
    $monitorData = getBestSellingProducts($con, 'man-hinh', 10);

    $web_title = 'MTShop - Chuyên cung cấp các dòng máy tính, laptop';
    $content_file = 'users_area/home-page.php';

    $data = [
        'new_products' => $products,
        'posts' => $posts,
        'best_selling_pcs' => $pcData['products'] ?? [],
        'best_selling_laptops' => $laptopData['products'] ?? [],
        'best_selling_monitors' => $monitorData['products'] ?? []
    ];

} 
// Giỏ hàng
elseif ($page === 'cart') {
    $web_title = 'Giỏ hàng - MTShop';
    $content_file = 'users_area/cart.php';

} 
// Chi tiết sản phẩm
elseif ($page === 'product-detail') {
    $slug = $_GET['slug'] ?? '';
    $product = getProductDetail($con, $slug);
    if ($product) {
        $web_title = $product['name'] . ' - MTShop.com';
        $product_images = $product['images'];
        $specs = $product['specs'];

        $content_file = 'users_area/products/product-detail.php';
    } else {
        header("Location: index.php?page=home");
        exit();
    }

} 
// Load sản phẩm theo danh mục chính
elseif ($page === 'category') {
    $slug = $_GET['slug'] ?? '';
    $brand_slug = $_GET['brand'] ?? null;

    // Gọi hàm xử lý từ Controller đã viết ở bước trước
    $data = getCategoryData($con, $slug, $brand_slug);

    if ($data) {
        $web_title = $data['category']['name'] . ' - MTShop.com';
        $content_file = 'users_area/products/product-by-category.php';
    } else {
        header("Location: index.php?page=404");
        exit();
    }
} 
// Load sản phẩm theo danh mục con
elseif ($page === 'subcategory') {
    $slug = $_GET['slug'] ?? '';

    $data = getSubcategoryData($con, $slug);

    if ($data) {
        $web_title = $data['subcategory']['name'] . ' - MTShop.com';
        $content_file = 'users_area/products/product-by-subcategory.php';
    } else {
        header("Location: index.php?page=404");
        exit();
    }
} 
// Danh sách sản phẩm mới nhất
elseif ($page === 'new-products') {
    $web_title = 'Sản phẩm mới nhất - MTShop.com';

    // Cấu hình phân trang (nếu bạn muốn trang này cũng có phân trang)
    $limit = 20;
    $currentPage = isset($_GET['p']) ? (int) $_GET['p'] : 1;
    $offset = ($currentPage - 1) * $limit;

    // 1. Đếm tổng số sản phẩm mới (Có thể thêm điều kiện thời gian nếu muốn)
    $countQuery = mysqli_query($con, "SELECT COUNT(*) as total FROM products WHERE status = 'active'");
    $totalRows = mysqli_fetch_assoc($countQuery)['total'];
    $totalPages = ceil($totalRows / $limit);

    // 2. Truy vấn danh sách sản phẩm mới nhất
    $sql = "SELECT * FROM products 
            WHERE status = 'active' 
            ORDER BY created_at DESC, id DESC 
            LIMIT ? OFFSET ?";

    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $limit, $offset);
    mysqli_stmt_execute($stmt);
    $products = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);

    // 3. Đường dẫn file hiển thị (Bạn có thể dùng chung file với all-product.php nếu giao diện giống nhau)
    $content_file = 'users_area/products/new-product.php';
} 
// Xem tất cả sản phẩm
elseif ($page === 'all-products') {
    $web_title = 'Tất cả sản phẩm mới - MTShop.com';

    // Cấu hình phân trang
    $limit = 10; // Số sản phẩm trên mỗi trang
    $currentPage = isset($_GET['p']) ? (int) $_GET['p'] : 1;
    $offset = ($currentPage - 1) * $limit;

    // Lấy tổng số sản phẩm để tính số trang
    $countQuery = mysqli_query($con, "SELECT COUNT(*) as total FROM products WHERE status = 'active'");
    $totalRows = mysqli_fetch_assoc($countQuery)['total'];
    $totalPages = ceil($totalRows / $limit);

    // Truy vấn danh sách sản phẩm
    $sql = "SELECT * FROM products WHERE status = 'active' ORDER BY created_at DESC LIMIT ? OFFSET ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $limit, $offset);
    mysqli_stmt_execute($stmt);
    $products = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);

    $content_file = 'users_area/products/all-product.php';
}
// Danh sách tất cả bài viết
elseif ($page === 'all-news') {
    $web_title = 'Tất cả bài viết - MTShop';
    $limit = 9; // số bài mỗi trang
    $currentPage = isset($_GET['p']) ? (int) $_GET['p'] : 1;
    $offset = ($currentPage - 1) * $limit;
    // Đếm tổng bài viết
    $countResult = mysqli_query($con, "SELECT COUNT(*) as total FROM posts WHERE status = 'active'");
    $totalRows = mysqli_fetch_assoc($countResult)['total'];
    $totalPages = ceil($totalRows / $limit);
    // Query bài viết kèm tên tác giả
    $stmt = mysqli_prepare($con, "
        SELECT p.*, u.name as user_name
        FROM posts p
        LEFT JOIN users u ON p.user_id = u.id
        WHERE p.status = 'active'
        ORDER BY p.created_at DESC
        LIMIT ? OFFSET ?
    ");
    mysqli_stmt_bind_param($stmt, "ii", $limit, $offset);
    mysqli_stmt_execute($stmt);
    $posts = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);
    $content_file = 'users_area/news/all-news.php';

} 
// Chi tiết bài viết
elseif ($page === 'news-detail') {
    // Lấy slug từ URL
    $slug = isset($_GET['slug']) ? mysqli_real_escape_string($con, $_GET['slug']) : '';

    // Truy vấn dựa trên cột slug
    $stmt = mysqli_prepare($con, "
        SELECT p.*, u.name as user_name 
        FROM posts p
        LEFT JOIN users u ON p.user_id = u.id
        WHERE p.slug = ? AND p.status = 'active'
        LIMIT 1
    ");

    // "s" đại diện cho kiểu dữ liệu string
    mysqli_stmt_bind_param($stmt, "s", $slug);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $post = mysqli_fetch_assoc($result);

    if ($post) {
        $web_title = $post['title'] . ' - MTShop.com';
        $timestamp = strtotime($post['created_at']);
        $days = ["Chủ Nhật", "Thứ Hai", "Thứ Ba", "Thứ Tư", "Thứ Năm", "Thứ Sáu", "Thứ Bảy"];
        $dayName = $days[date('w', $timestamp)];
        $formattedDate = date('d/m/Y', $timestamp);

        $content_file = 'users_area/news/news-detail.php';
    }
} 
// Danh sách PC bán chạy nhất
elseif ($page === 'all-best-selling-pc') {
    // Gọi hàm controller
    $data = showAllBestSellingPCs($con);

    // Thiết lập các biến cần thiết cho view
    $web_title = $data['web_title'];
    $content_file = 'users_area/products/all-best-selling-pc.php';
    // (Đảm bảo đường dẫn này đúng với file view bạn đã gửi ở trên)
}
// Danh sách Laptop bán chạy nhất
elseif ($page === 'all-best-selling-laptop') {
    // Gọi hàm xử lý logic từ controller
    $data = showAllBestSellingLaptops($con);

    // Thiết lập các biến cần thiết cho view
    $web_title = $data['web_title'];
    $content_file = 'users_area/products/all-best-selling-laptop.php';
} 
// Danh sách màn hình bán chạy nhất
elseif ($page === 'all-best-selling-monitor') {
    $data = showAllBestSellingMonitors($con);

    $web_title = $data['web_title'];
    $content_file = 'users_area/products/all-best-selling-monitor.php';
} 
// Tìm kiếm sản phẩm
elseif ($page === 'search-result') {
    $keyword = $_GET['keyword'] ?? '';
    $current_page = (int) ($_GET['p'] ?? 1);

    // Gọi hàm và lấy dữ liệu
    $search_data = searchProducts($con, $keyword, $current_page);

    $products = $search_data['products'];
    $total_pages = $search_data['total_pages'];
    $message = $search_data['message'];

    $web_title = "Kết quả tìm kiếm: " . htmlspecialchars($keyword).' - MTShop.com';
    $content_file = 'users_area/search-result.php';
} else {
    $web_title = 'Trang không tồn tại - MTShop';
    $content_file = 'users_area/404.php';
}
?>