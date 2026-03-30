<?php
// Kết nối database (điều chỉnh đường dẫn cho đúng với cấu trúc thư mục của bạn)
include('../../../includes/connect.php');

if (isset($_GET['restore_user'])) {
    $restore_id = (int) $_GET['restore_user'];

    // Thực hiện câu lệnh update
    $restore_query = "UPDATE `users` SET `status`='active' WHERE `id`='$restore_id'";
    $run_restore = mysqli_query($con, $restore_query);

    if ($run_restore) {
        header('Location: ../../../admin/index.php?list_accounts');
    } else {
        echo "<script>alert('Lỗi: Không thể khôi phục tài khoản!');</script>";
        header('Location: ../../../admin/index.php?list_accounts');

    }
}
?>