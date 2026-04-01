<?php
// 1. Kết nối cơ sở dữ liệu
include __DIR__ . '/../../../includes/connect.php';

if (isset($_GET['id'])) {
    // 2. Lấy và làm sạch ID bài viết
    $post_id = mysqli_real_escape_string($con, $_GET['id']);

    $delete_query = "UPDATE `posts` SET 
                    `status` = 'inactive', 
                    `deleted_at` = NOW() 
                    WHERE `id` = '$post_id'";

    $run_query = mysqli_query($con, $delete_query);

    if ($run_query) {
        // Kiểm tra xem có dòng nào được cập nhật không (trường hợp ID không tồn tại)
        if (mysqli_affected_rows($con) > 0) {
            header("Location: ../../../admin/index.php?view_post&status=deleted");
        } else {
            header("Location: ../../../admin/index.php?view_post&error=not_found");
        }
    } else {
        // Lỗi truy vấn database
        header("Location: ../../../admin/index.php?view_post&error=db_error");
    }
    exit();
} else {
    // Nếu không có ID trên URL, quay lại trang danh sách
    header("Location: ../../../admin/index.php?view_post");
    exit();
}