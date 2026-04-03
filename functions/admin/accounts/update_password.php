<?php
ob_start();
session_start();
include __DIR__ . '/../../../includes/connect.php';

if (isset($_POST['change_pwd_btn'])) {
    $admin_id = $_SESSION['admin_id'];
    $current_pwd = $_POST['current_password'];
    $new_pwd = $_POST['new_password'];
    $confirm_pwd = $_POST['confirm_password'];

    // 1. Xử lý URL quay lại sạch sẽ
    $referer = $_SERVER['HTTP_REFERER'] ?? '../../../admin/index.php';
    $url_parts = parse_url($referer);
    $path = $url_parts['path'] ?? '../../../admin/index.php';
    $query = [];
    if (isset($url_parts['query'])) {
        parse_str($url_parts['query'], $query);
    }

    // Xóa các thông báo cũ để tránh URL bị dài vô tận
    unset($query['status'], $query['error'], $query['msg']);

    // Hàm build lại URL
    function build_url($path, $query)
    {
        return $path . ($query ? '?' . http_build_query($query) : '');
    }

    // 2. Kiểm tra khớp mật khẩu mới
    if ($new_pwd !== $confirm_pwd) {
        $query['error'] = 'password_not_match';
        header("Location: " . build_url($path, $query));
        exit();
    }

    // 3. Lấy mật khẩu cũ từ DB (Dùng tham số an toàn)
    $admin_id = mysqli_real_escape_string($con, $admin_id);
    $sql = "SELECT password FROM `users` WHERE id = '$admin_id'";
    $result = mysqli_query($con, $sql);
    $row = mysqli_fetch_assoc($result);

    if ($row) {
        // 4. Kiểm tra mật khẩu hiện tại
        if (password_verify($current_pwd, $row['password'])) {
            $hash_new_pwd = password_hash($new_pwd, PASSWORD_DEFAULT);
            $update = mysqli_query($con, "UPDATE `users` SET password = '$hash_new_pwd' WHERE id = '$admin_id'");

            if ($update) {
                $query['status'] = 'password_updated';
            } else {
                $query['error'] = 'db_error';
            }
        } else {
            $query['error'] = 'wrong_current_password';
        }
    } else {
        $query['error'] = 'user_not_found';
    }

    // 5. Trả về URL cuối cùng (Sử dụng hàm build_url cho tất cả trường hợp)
    header("Location: " . build_url($path, $query));
    exit();
}