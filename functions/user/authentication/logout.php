<?php
session_start();

// 2. Xóa tất cả các biến session hiện có
$_SESSION = array();

// 3. Nếu muốn hủy hoàn toàn session (xóa cả cookie session trên trình duyệt)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 4. Hủy session trên server
session_destroy();

// 5. Chuyển hướng người dùng về trang chủ (hoặc trang login tùy bạn)
header("Location: ../../../index.php");
exit;
?>