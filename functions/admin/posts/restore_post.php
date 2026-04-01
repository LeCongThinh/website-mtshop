<?php
// 1. Kết nối cơ sở dữ liệu
include __DIR__ . '/../../../includes/connect.php';

if (isset($_GET['id'])) {
    // 2. Lấy và làm sạch ID từ URL
    $post_id = mysqli_real_escape_string($con, $_GET['id']);

    // 3. Thực hiện câu lệnh Cập nhật để khôi phục
    // Set status thành 'active' và deleted_at về NULL
    $restore_query = "UPDATE `posts` SET 
                      `status` = 'active', 
                      `deleted_at` = NULL 
                      WHERE `id` = '$post_id'";

    $run_query = mysqli_query($con, $restore_query);

    if ($run_query) {
        // Kiểm tra xem có bản ghi nào thực sự được cập nhật không
        if (mysqli_affected_rows($con) > 0) {
            // Trả về view_post kèm thông báo thành công
            header("Location: ../../../admin/index.php?view_post&status=restored");
        } else {
            // Trường hợp ID không tồn tại hoặc đã active sẵn rồi
            header("Location: ../../../admin/index.php?view_post&error=not_found");
        }
    } else {
        // Lỗi truy vấn SQL
        header("Location: ../../../admin/index.php?view_post&error=db_error");
    }
    exit();
} else {
    // Nếu truy cập file mà không có ID bài viết
    header("Location: ../../../admin/index.php?view_post");
    exit();
}