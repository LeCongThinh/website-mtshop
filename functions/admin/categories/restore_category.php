<?php
// Kết nối database (điều chỉnh đường dẫn cho đúng với cấu trúc thư mục của bạn)
include __DIR__ . '/../../../includes/connect.php';

if (isset($_GET['id'])) {
    // Làm sạch dữ liệu đầu vào
    $restore_id = mysqli_real_escape_string($con, $_GET['id']);

    // Câu lệnh SQL: Cập nhật status thành active và xóa giá trị deleted_at
    $query = "UPDATE `categories` 
              SET `status` = 'active', 
                  `deleted_at` = NULL 
              WHERE `id` = '$restore_id'";

    $result = mysqli_query($con, $query);

    if ($result) {
        // Khôi phục thành công -> Chuyển hướng kèm mã msg
        header("Location: ../../../admin/index.php?view_category&msg=restore_success");
        exit();
    } else {
        // Lỗi truy vấn -> Chuyển hướng kèm mã lỗi
        header("Location: ../../../admin/index.php?view_category&msg=restore_error");
        exit();
    }
} else {
    // Nếu không có ID truyền vào, quay lại trang danh sách
    header("Location: ../../../admin/index.php?view_category");
    exit();
}
?>