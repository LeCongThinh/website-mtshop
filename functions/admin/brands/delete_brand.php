<?php
// Kết nối database
include __DIR__ . '/../../../includes/connect.php';

if (isset($_GET['id'])) {
    // Làm sạch ID để tránh SQL Injection
    $brand_id = mysqli_real_escape_string($con, $_GET['id']);

    // Logic: Cập nhật status thành 'inactive' và ghi lại thời gian xóa vào 'deleted_at'
    $query = "UPDATE `brands` 
              SET `status` = 'inactive', 
                  `deleted_at` = NOW() 
              WHERE `id` = '$brand_id'";

    $result = mysqli_query($con, $query);

    if ($result) {
        // Chuyển hướng về trang danh sách với thông báo thành công
        header("Location: ../../../admin/index.php?view_brand&msg=delete_success");
        exit();
    } else {
        // Chuyển hướng về với thông báo lỗi
        header("Location: ../../../admin/index.php?view_brand&msg=delete_error");
        exit();
    }
} else {
    // Nếu truy cập trực tiếp file mà không có ID
    header("Location: ../../../admin/index.php?view_brand");
    exit();
}
?>