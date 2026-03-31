<?php
// Kết nối database
include __DIR__ . '/../../../includes/connect.php';

if (isset($_GET['id'])) {
    $del_id = intval($_GET['id']);

    // Thực hiện Soft Delete: Cập nhật status thành inactive và deleted_at
    $query = "UPDATE `categories` 
              SET `status` = 'inactive', 
                  `deleted_at` = NOW(),
                  `updated_at` = NOW() 
              WHERE `id` = $del_id";

    $result = mysqli_query($con, $query);

    if ($result) {
        // Thành công: Điều hướng về trang view kèm mã thành công
        header("Location: ../../../admin/index.php?view_category&msg=delete_success");
        exit();
    } else {
        // Thất bại: Điều hướng về kèm mã lỗi
        header("Location: ../../../admin/index.php?view_category&msg=delete_error");
        exit();
    }
} else {
    // Nếu truy cập file trực tiếp mà không có ID
    header("Location: ../../../admin/index.php?view_category");
    exit();
}
?>