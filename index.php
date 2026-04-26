<?php
session_start();

// Giải pháp tối ưu cho ngrok: Kiểm tra thêm header X-Forwarded-Proto
$protocol = 'http://';
if ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
    $_SERVER['SERVER_PORT'] == 443 ||
    (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')
) {
    $protocol = 'https://';
}

$domainName = $_SERVER['HTTP_HOST'];
// define('URL', $protocol . $domainName . '/project-php/website-mtshop/');
$basePath = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');

define('URL', $protocol . $domainName . $basePath . '/');

header('Access-Control-Allow-Origin: *');

require_once("includes/connect.php");
require_once("routes/route.php");
include 'functions/user/cart/edit-cart.php';
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title><?php echo $web_title ?? 'MTShop - Chuyên cung cấp các dòng máy tính, laptop'; ?></title>

    <link rel="shortcut icon" type="image/x-icon"
        href="/project-php/website-mtshop/assets/images/logo/icon-laptopshop.png" />

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" rel="stylesheet" />

    <link rel="stylesheet" href="assets/css/user/styles.css" />

    <link rel="stylesheet" href="assets/css/user/app.css">

    <script>
        window.CART = {
            // Sử dụng đường dẫn đầy đủ từ hằng số URL đã define ở trên
            addUrl: "<?php echo URL; ?>functions/user/cart/cart-controller.php",
            updateUrl: "<?php echo URL; ?>functions/user/cart/update.php",
            removeUrl: "<?php echo URL; ?>functions/user/cart/remove.php",
            cartUrl: "<?php echo URL; ?>index.php?page=cart",
        };
    </script>
</head>

<body class="d-flex flex-column min-vh-100">

    <?php include("header.php"); ?>

    <main class="flex-fill">
        <div class="position-fixed top-0 end-0 p-3" style="z-index: 9999; margin-top: 10px;">
            <div id="mainAlert" class="alert d-none alert-dismissible fade show shadow-lg border-0 py-3" role="alert"
                style="min-width: 320px; max-width: 600px; border-radius: 12px; min-height: 60px; display: flex; align-items: center;">
                <div class="d-flex align-items-center w-100">
                    <div class="pe-4">
                        <span class="alert-text fw-bold" style="font-size: 0.95rem; line-height: 1.5;"></span>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"
                    style="position: absolute; top: 50%; right: 15px; transform: translateY(-50%);"></button>
            </div>
        </div>

        <div id="loginAlertApp"
            style="position: fixed; top: 50px; right: 5px; z-index: 10000; width: 100%; max-width: 480px; pointer-events: none;">
        </div>

        <?php
        // Lúc này biến $data từ route.php đã sẵn sàng để file con sử dụng
        if (isset($content_file) && file_exists($content_file)) {
            include($content_file);
        }
        ?>
        
        <?php
            // Zalo chat: nhắn tin trực tiếp với nhân viên cửa hàng
            include("users_area/chatbot/zalo-chat.php");

            // Chatbot AI: trợ lý ảo hỗ trợ mua hàng
            include("users_area/chatbot/ai-chatbot.php");
        ?>
        

    </main>

    <footer class="py-5 bg-dark">
        <div class="container">
            <p class="m-0 text-center text-white">Copyright &copy; MTShop 2026</p>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Chatbot nhắn tin -->
    <script src="assets/js/user/chatbot.js"></script>
    <!-- Thêm sản phẩm vào giỏ hàng -->
    <script src="assets/js/user/product-to-cart.js"></script>
    <script src="assets/js/user/alert.js"></script>
    <script src="assets/js/user/handle-product-detail.js"></script>

    <script>
        window.addEventListener('DOMContentLoaded', () => {
            // 1. Lấy các tham số từ URL
            const params = new URLSearchParams(window.location.search);
            if (params.has('login')) {
                const type = params.get('login');
                let message = "";
                let alertType = "success";
                if (type === 'success') {
                    const user = params.get('user') || 'bạn';
                    message = `Chào mừng ${user} đã quay trở lại!`;
                } else if (type === 'has_cart') {
                    const qty = params.get('qty') || 0;
                    message = `Chào mừng trở lại! Bạn có ${qty} sản phẩm trong giỏ hàng.`;
                    alertType = "info";
                }
                // 3. Gọi hàm showAlert từ file alert.js của bạn
                if (message !== "") {
                    showAlert('mainAlert', message, alertType, 4000);
                }
                // 4. Làm sạch URL (Xóa các tham số login để không hiện lại khi F5)
                const newUrl = window.location.pathname + (params.get('page') ? '?page=' + params.get('page') : '');
                window.history.replaceState({}, document.title, newUrl);
            }
            // Thông báo cập nhật tài khoản
            if (params.has('update')) {
                const updateType = params.get('update');
                let message = "";
                let alertType = "success";

                switch (updateType) {
                    case 'success':
                        message = "Cập nhật thông tin tài khoản thành công!";
                        alertType = "success";
                        break;
                    case 'error':
                        message = "Lỗi hệ thống: Không thể cập nhật dữ liệu!";
                        alertType = "danger";
                        break;
                    case 'upload_error':
                        message = "Lỗi: Không thể tải ảnh lên máy chủ!";
                        alertType = "warning";
                        break;
                }

                if (message !== "") {
                    showAlert('mainAlert', message, alertType, 4000);
                }

                // Dọn dẹp URL
                const page = params.get('page') || 'profile';
                const newUrl = window.location.pathname + '?page=' + page;
                window.history.replaceState({}, document.title, newUrl);
            }
        });
    </script>

</body>

</html>