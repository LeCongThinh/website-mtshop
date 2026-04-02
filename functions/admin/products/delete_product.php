<?php
session_start();
include __DIR__ . '/../../../includes/connect.php';

if (isset($_GET['id'])) {
    $product_id = (int) $_GET['id'];

    // Câu lệnh SQL Soft Delete: Cập nhật status và deleted_at
    $soft_delete_query = "UPDATE `products` SET 
                            `status` = 'inactive', 
                            `deleted_at` = NOW() 
                          WHERE `id` = '$product_id'";

    if (mysqli_query($con, $soft_delete_query)) {
        // Chuyển hướng về trang danh sách với thông báo thành công
        header("Location: ../../../admin/index.php?view_product&status=success&msg=deleted");

        exit();
    } else {
        // Chuyển hướng kèm thông báo lỗi
        header("Location: ../../../admin/index.php?view_product&status=error");
        exit();
    }
} else {
    header("Location: ../../../admin/index.php?view_product");
    exit();
}