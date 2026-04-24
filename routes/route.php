<?php
require_once(__DIR__ . "/../includes/connect.php");
require_once(__DIR__ . "/../functions/user/handle-product/product-controller.php");
require_once(__DIR__ . "/../functions/user/home-controller.php");
require_once(__DIR__ . "/../functions/user/authentication/account_profile.php");
require_once(__DIR__ . "/../functions/user/search-controller.php");
require_once(__DIR__ . "/../functions/user/checkout/checkout-controller.php");
require_once(__DIR__ . "/../functions/user/orders/order-controller.php");
require_once(__DIR__ . "/../functions/user/posts/post-controller.php");

// Khởi tạo order controller
$orderCtrl = new OrderController();
// Khởi tạo post controller
$postCtrl = new PostController();

// Hàm hiển thị Trạng thái Đơn hàng (Việt hóa đồng bộ Admin)
function getOrderStatusBadge($status)
{
    return match ($status) {
        'pending' => ['class' => 'bg-warning-subtle text-warning border border-warning', 'text' => 'Chờ duyệt đơn'],
        'confirmed' => ['class' => 'bg-primary-subtle text-primary border border-primary', 'text' => 'Đã xác nhận đơn hàng'],
        'shipping' => ['class' => 'bg-info-subtle text-info border border-info', 'text' => 'Đang giao hàng'],
        'delivered' => ['class' => 'bg-success-subtle text-success border border-success', 'text' => 'Giao thành công'],
        'cancelled' => ['class' => 'bg-danger-subtle text-danger border border-danger', 'text' => 'Đã hủy đơn'],
        default => ['class' => 'bg-secondary-subtle text-secondary border border-secondary', 'text' => 'Không rõ']
    };
}

// Hàm hiển thị Trạng thái Thanh toán (Việt hóa đồng bộ Admin)
function getPaymentStatusBadge($status)
{
    return match ($status) {
        'pending' => ['class' => 'bg-secondary-subtle text-secondary border border-secondary', 'text' => 'Chưa thanh toán'],
        'paid' => ['class' => 'bg-success-subtle text-success border border-success', 'text' => 'Đã thanh toán'],
        'failed' => ['class' => 'bg-danger-subtle text-danger border border-danger', 'text' => 'Thanh toán thất bại'],
        'refunded' => ['class' => 'bg-warning-subtle text-warning border border-warning', 'text' => 'Đã hoàn tiền'],
        default => ['class' => 'bg-secondary', 'text' => 'Chưa rõ']
    };
}

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
    $postData = $postCtrl->getAllPosts(9); 
    $posts = $postData['posts'];
    $totalPages = $postData['totalPages'];
    $currentPage = $postData['currentPage'];
    $content_file = 'users_area/news/all-news.php';
}
// Chi tiết bài viết
elseif ($page === 'news-detail') {
    $slug = $_GET['slug'] ?? '';
    $post = $postCtrl->getPostDetailBySlug($slug);
    if ($post) {
        $web_title = $post['title'] . ' - MTShop.com';
        $content_file = 'users_area/news/news-detail.php';
    } else {
        header("Location: index.php?page=all-news");
        exit();
    }
}
// Danh sách PC bán chạy nhất
elseif ($page === 'all-best-selling-pc') {
    // Gọi hàm controller
    $data = showAllBestSellingPCs($con);
    $web_title = $data['web_title'];
    $content_file = 'users_area/products/all-best-selling-pc.php';
}
// Danh sách Laptop bán chạy nhất
elseif ($page === 'all-best-selling-laptop') {
    // Gọi hàm xử lý logic từ controller
    $data = showAllBestSellingLaptops($con);
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

// Xử lý đơn hàng
elseif ($page === 'process-checkout') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $user_id = $_SESSION['user_id'];
        $checkoutData = getCheckoutData($con, $user_id);
        $cartItems = $checkoutData['cartItems'];
        $totalAmount = $checkoutData['totalOrder'];

        if (empty($cartItems)) {
            $_SESSION['error'] = "Giỏ hàng trống!";
            header("Location: index.php?page=checkout");
            exit();
        }

        // Gọi hàm lưu đơn hàng (Hàm này bạn đã có trong checkout-controller.php)
        $result = createOrderRecord($con, $user_id, $_POST, $cartItems, $totalAmount);

        if ($result['status'] === 'success') {
            $method = $result['payment_method'];

            if ($method === 'qr') {
                // TÍCH HỢP MỚI: Chuyển hướng sang trang hiển thị mã QR SePay
                // Chúng ta truyền order_code sang để trang QR truy vấn dữ liệu
                header("Location: index.php?page=qr-payment&code=" . $result['order_code']);
                exit();
            } elseif ($method === 'vnpay') {
                $vnpayUrl = generateVNPAYUrl($result['order_code'], $result['total_amount']);
                header("Location: " . $vnpayUrl);
                exit();
            } else {
                // Mặc định là COD
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
            // header("Location: index.php?page=checkout-success&code=" . $orderCode);
            // header("Location: " . URL . "index.php?page=checkout-success&code=" . $orderCode);
            header("Location: index.php?page=checkout-success&code=" . $orderCode);
            exit();
        } else {
            echo "Lỗi cập nhật database: " . mysqli_error($con);
        }
    } else {
        // Thanh toán thất bại hoặc khách hủy
        $_SESSION['error'] = "Thanh toán không thành công. Mã lỗi: " . $vnp_ResponseCode;
        // header("Location: index.php?page=checkout");
        header("Location: " . URL . "index.php?page=checkout");
        exit();
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
}

// Danh sách đơn hàng của tôi
elseif ($page === 'my-orders') {
    $data = $orderCtrl->index();
    // Hàm này tự động tạo các biến $orders, $web_title, $content_file từ mảng
    extract($data); 
}

// Xem chi tiết đơn hàng
elseif ($page === 'order-detail') {
    $code = $_GET['code'] ?? '';
    $data = $orderCtrl->show($code);
    extract($data);
}

// Hiển thị trang hồ sơ người dùng
elseif ($page === 'profile') {
    if (!isset($_SESSION['user_id'])) {
        header("Location: index.php?page=home");
        exit();
    }
    $user = getUserProfile($con, $_SESSION['user_id']);
    $web_title = 'Hồ sơ của tôi - MTShop';
    $content_file = 'users_area/authentication/profile.php';
}

// Cập nhật thông tin hồ sơ người dùng
elseif ($page === 'update-profile') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
        // Gọi hàm xử lý cập nhật
        $status = updateUserProfile($con, $_SESSION['user_id'], $_POST, $_FILES);
    }
    exit();
}

// API xử lý Webhook từ SePay
elseif ($page === 'sepay-webhook') {
    http_response_code(200);
    header('Content-Type: application/json');

    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    if ($data && isset($data['content'])) {
        $content = $data['content']; // Chuỗi dài chứa THANHTOANORD20260414E85841

        // 1. Dùng Regex lấy chính xác chuỗi bắt đầu từ ORD đến hết mã (chỉ lấy chữ và số)
        if (preg_match('/ORD([A-Z0-9]+)/', $content, $matches)) {
            $code_clean = "ORD" . $matches[1]; // Kết quả: ORD20260414E85841

            // 2. SQL thông minh: 
            // Xóa dấu '-' trong cột order_code trước khi so sánh
            $sql = "UPDATE orders SET 
                    payment_status = 'paid', 
                    payment_method = 'qr_code', 
                    status = 'confirmed',
                    updated_at = NOW() 
                    WHERE REPLACE(order_code, '-', '') = ? 
                    AND payment_status = 'pending'";

            $stmt = mysqli_prepare($con, $sql);
            mysqli_stmt_bind_param($stmt, "s", $code_clean);
            mysqli_stmt_execute($stmt);

            if (mysqli_stmt_affected_rows($stmt) > 0) {
                echo json_encode(['success' => true, 'msg' => 'Khớp hoàn toàn sau khi xóa gạch ngang']);
            } else {
                // Phương án dự phòng: Nếu vẫn không khớp, tìm kiếm tương đối (LIKE)
                $sql_like = "UPDATE orders SET payment_status = 'paid', status = 'confirmed' 
                             WHERE REPLACE(order_code, '-', '') LIKE ? 
                             AND payment_status = 'pending' LIMIT 1";
                $stmt_like = mysqli_prepare($con, $sql_like);
                $search = "%" . $matches[1] . "%";
                mysqli_stmt_bind_param($stmt_like, "s", $search);
                mysqli_stmt_execute($stmt_like);

                echo json_encode(['success' => true, 'msg' => 'Khớp tương đối']);
            }
        }
    }
    exit();
}
// API kiểm tra trạng thái đơn hàng (Dùng cho AJAX)
elseif ($page === 'check-order-status') {
    header('Content-Type: application/json');
    $order_code = $_GET['code'] ?? '';

    $sql = "SELECT payment_status FROM orders WHERE order_code = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "s", $order_code);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $order = mysqli_fetch_assoc($result);

    // Trả về true nếu status đã chuyển sang 'paid'
    echo json_encode([
        'paid' => ($order && strtolower($order['payment_status']) === 'paid'),
        'debug_status' => $order['payment_status'] ?? 'unknown'
    ]);
    exit();
}
// Trang hiển thị mã QR thanh toán SePay
elseif ($page === 'qr-payment') {
    $order_code = $_GET['code'] ?? '';
    $user_id = $_SESSION['user_id'] ?? 0;

    // Tận dụng hàm cũ để lấy thông tin đơn hàng
    $order = getOrderSuccessDetails($con, $order_code, $user_id);

    if (!$order) {
        header("Location: index.php?page=404");
        exit();
    }

    $web_title = 'Thanh toán đơn hàng qua QR #' . $order_code;
    $content_file = 'users_area/checkout/qr-payment.php'; // Đường dẫn file giao diện sẽ tạo ở Bước 3
}
//xóa đơn quay lại giỏ hàng
elseif ($page === 'cancel-qr-order') {
    $order_code = $_GET['code'] ?? '';

    if (!empty($order_code)) {
        // 1. Lấy thông tin đơn hàng và chi tiết đơn để hoàn kho
        $orderQuery = mysqli_query($con, "SELECT id FROM orders WHERE order_code = '$order_code' AND payment_status = 'pending'");
        $order = mysqli_fetch_assoc($orderQuery);

        if ($order) {
            $order_id = $order['id'];

            // 2. Hoàn trả số lượng vào kho sản phẩm
            $detailsQuery = mysqli_query($con, "SELECT product_id, quantity FROM order_details WHERE order_id = $order_id");
            while ($item = mysqli_fetch_assoc($detailsQuery)) {
                $p_id = $item['product_id'];
                $qty = $item['quantity'];
                mysqli_query($con, "UPDATE products SET stock = stock + $qty WHERE id = $p_id");
            }

            // 3. Xóa đơn hàng và chi tiết đơn hàng
            mysqli_query($con, "DELETE FROM order_details WHERE order_id = $order_id");
            mysqli_query($con, "DELETE FROM orders WHERE id = $order_id");
        }
    }

    // Sau khi hoàn kho và xóa đơn "tạm", quay lại trang thanh toán
    header("Location: index.php?page=checkout");
    exit();
} else {
    $web_title = 'Trang không tồn tại - MTShop';
    $content_file = 'users_area/404.php';
}
