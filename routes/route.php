<?php
require_once(__DIR__ . "/../includes/connect.php");
require_once(__DIR__ . "/../functions/user/handle-product/product-controller.php");
require_once(__DIR__ . "/../functions/user/home-controller.php");
require_once(__DIR__ . "/../functions/user/search-controller.php");
require_once(__DIR__ . "/../functions/user/checkout/checkout-controller.php");


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

if ($page === 'home' || (!isset($_GET['page']) && isset($_GET['login']))) {

    // 1. Danh sách sản phẩm mới nhất
    $product_res = mysqli_query($con, "SELECT * FROM products WHERE status = 'active' ORDER BY id DESC LIMIT 10");
    $products = mysqli_fetch_all($product_res, MYSQLI_ASSOC);

    // 2. Danh sách bài viết mới nhất
    $post_res = mysqli_query($con, "SELECT * FROM posts WHERE status = 'active' ORDER BY created_at DESC LIMIT 10");
    $posts = mysqli_fetch_all($post_res, MYSQLI_ASSOC);

    // 3. Danh sách bán chạy (Sử dụng hàm từ home-controller.php)
    $pcData = getBestSellingProducts($con, 'pc', 10);
    $laptopData = getBestSellingProducts($con, 'laptop', 10);
    $monitorData = getBestSellingProducts($con, 'man-hinh', 10);

    // 4. Thiết lập tiêu đề và file nội dung
    $web_title = 'MTShop - Chuyên cung cấp các dòng máy tính, laptop';
    $content_file = 'users_area/home-page.php';

    // 5. Đóng gói dữ liệu vào mảng $data
    $data = [
        'new_products' => $products ?? [],
        'posts' => $posts ?? [],
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

    $web_title = "Kết quả tìm kiếm: " . htmlspecialchars($keyword) . ' - MTShop.com';
    $content_file = 'users_area/search-result.php';
}
// View chi tiết đơn hàng
elseif ($page === 'checkout') {
    // 2. Gọi Controller để lấy dữ liệu
    $checkoutData = getCheckoutData($con, $_SESSION['user_id']);

    // 4. Giải nén dữ liệu để file view (content_file) sử dụng
    $user = $checkoutData['user'];
    $cartItems = $checkoutData['cartItems'];
    $totalOrder = $checkoutData['totalOrder'];

    $web_title = 'Thanh toán đơn hàng - MTShop.com';
    $content_file = 'users_area/checkout/view-checkout.php';
}
// Xử lý đơn hàng thanh toán bằng tiền mặt
elseif ($page === 'process-checkout') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $user_id = $_SESSION['user_id'];

        // Lấy dữ liệu giỏ hàng để tính toán lại giá tại Server
        $checkoutData = getCheckoutData($con, $user_id);
        $cartItems = $checkoutData['cartItems'];
        $totalAmount = $checkoutData['totalOrder'];

        if (empty($cartItems)) {
            $_SESSION['error'] = "Giỏ hàng trống!";
            header("Location: index.php?page=checkout");
            exit();
        }

        // Gọi hàm lưu đơn hàng đã nâng cấp ở trên
        $result = createOrderRecord($con, $user_id, $_POST, $cartItems, $totalAmount);

        if ($result['status'] === 'success') {
            // Kiểm tra khách chọn phương thức nào
            if ($result['payment_method'] === 'vnpay') {
                // Nếu là VNPAY: Tạo URL và chuyển hướng sang cổng thanh toán
                $vnpayUrl = generateVNPAYUrl($result['order_code'], $result['total_amount']);
                header("Location: " . $vnpayUrl);
                exit();
            } else {
                // Nếu là COD: Chuyển hướng về trang thông báo thành công
                header("Location: index.php?page=checkout-success&code=" . $result['order_code']);
                exit();
            }
        } else {
            $_SESSION['error'] = $result['message'];
            header("Location: index.php?page=checkout");
            exit();
        }
    }
}
// Xử lý kết quả trả về từ VNPAY
elseif ($page === 'vnpay_return') {
    $vnp_ResponseCode = $_GET['vnp_ResponseCode'] ?? '';
    $vnp_TransactionNo = $_GET['vnp_TransactionNo'] ?? '';
    $orderCode = $_GET['vnp_TxnRef'] ?? '';

    if ($vnp_ResponseCode === '00') {
        // Thanh toán thành công: Cập nhật DB
        $sql = "UPDATE orders SET 
                payment_status = 'paid',
                transaction_id = '$vnp_TransactionNo', 
                status = 'confirmed',
                updated_at = NOW() 
                WHERE order_code = '$orderCode'";

        if (mysqli_query($con, $sql)) {
            // Chuyển hướng sang trang thành công
            header("Location: index.php?page=checkout-success&code=" . $orderCode);
            exit();
        } else {
            echo "Lỗi cập nhật database: " . mysqli_error($con);
        }
    } else {
        // Thanh toán thất bại hoặc khách hủy
        $_SESSION['error'] = "Thanh toán không thành công. Mã lỗi: " . $vnp_ResponseCode;
        header("Location: index.php?page=checkout");
    }
    exit();
}
// Thanh toán đơn hàng thành công
elseif ($page === 'checkout-success') {
    // Lấy mã đơn hàng từ URL (?code=ORD-...)
    $order_code = $_GET['code'] ?? '';
    $user_id = $_SESSION['user_id'] ?? 0;

    // Gọi hàm từ controller
    $order = getOrderSuccessDetails($con, $order_code, $user_id);

    // Nếu mã code sai hoặc không thuộc về user này
    if (!$order) {
        header("Location: index.php?page=404");
        exit();
    }

    // Thiết lập cho layout
    $web_title = 'Thanh toán đơn hàng thành công #' . $order_code . ' - MTShop';
    $content_file = 'users_area/checkout/checkout-success.php';
} else {
    $web_title = 'Trang không tồn tại - MTShop';
    $content_file = 'users_area/404.php';
}
?>