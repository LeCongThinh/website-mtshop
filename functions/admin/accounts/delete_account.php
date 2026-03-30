<?php
// 1. Kết nối DB: từ functions/admin/accounts/ lùi 3 cấp ra gốc và vào includes/
include("../../../includes/connect.php"); 

if (isset($_GET['id']) && $_GET['action'] == 'deactivate') {
    // Bảo mật ID bằng cách ép kiểu (casting) sang số nguyên
    $user_id = (int)$_GET['id'];

    // 2. Thực thi câu lệnh SQL
    $update_query = "UPDATE users SET status = 'inactive' WHERE id = '$user_id'";
    $run_update = mysqli_query($con, $update_query);

    if ($run_update) {
        $_SESSION['flash_message'] = 'Tài khoản đã được khóa thành công!';
    } else {
        $_SESSION['flash_error'] = 'Lỗi: Không thể cập nhật trạng thái.';
    }

    // 3. Điều hướng quay lại trang danh sách admin
    header('Location: ../../../admin/index.php?list_accounts');
    exit;
} else {
    header('Location: ../../../admin/index.php?list_accounts');
    exit;
}
?>