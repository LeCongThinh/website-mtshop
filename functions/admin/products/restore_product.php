<?php
session_start();
include __DIR__ . '/../../../includes/connect.php';

if (isset($_GET['id'])) {
    $product_id = (int)$_GET['id'];

    // Logic khôi phục: status thành 'active' và xóa mốc thời gian deleted_at (về NULL)
    $restore_query = "UPDATE `products` SET 
                        `status` = 'active', 
                        `deleted_at` = NULL 
                      WHERE `id` = '$product_id'";

    if (mysqli_query($con, $restore_query)) {
        // Điều hướng về trang danh sách với thông báo khôi phục thành công
        header("Location: ../../../admin/index.php?view_product&status=success&msg=restored");
        exit();
    } else {
        // Điều hướng kèm thông báo lỗi nếu truy vấn thất bại
        header("Location: ../../../admin/index.php?view_product&status=error");
        exit();
    }
} else {
    // Nếu không có ID, quay lại trang danh sách
    header("Location: ../../../admin/index.php?view_product");
    exit();
}