<?php
// functions/check_admin.php

// 1. Khởi tạo session nếu chưa có
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Kiểm tra đăng nhập cơ bản
if (!isset($_SESSION['admin_id'])) {
    header("Location: ./authentication/admin_login.php");
    exit();
}

// 3. Kiểm tra quyền hạn (Role)
$allowed_roles = ['admin', 'staff'];
if (!isset($_SESSION['admin_role']) || !in_array($_SESSION['admin_role'], $allowed_roles)) {
    // Nếu có session nhưng role không hợp lệ, xóa session và bắt đăng nhập lại
    session_destroy();
    echo "<script>alert('Bạn không có quyền truy cập trang này!');</script>";
    echo "<script>window.location.href='./authentication/admin_login.php';</script>";
    exit();
}

// 4. Lấy dữ liệu mới nhất từ DB (để cập nhật avatar/tên nếu admin có thay đổi)
// $con là biến kết nối từ file connect.php mà bạn sẽ include ở file index
$user_id = $_SESSION['admin_id'];
$get_user_data = "SELECT * FROM `users` WHERE id = '$user_id'";
$get_user_result = mysqli_query($con, $get_user_data);

if ($row = mysqli_fetch_array($get_user_result)) {
    $admin_name = $row['name'];
    $admin_image = $row['avatar'];
    $admin_role = $row['role'];
} else {
    session_destroy();
    header("Location: ./authentication/admin_login.php");
    exit();
}