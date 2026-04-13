<?php
// 1. Kết nối database (giữ nguyên của bạn)
include __DIR__ . '/../../../includes/connect.php';

$alert = '';

// 2. LẤY DỮ LIỆU ĐỂ HIỂN THỊ (QUAN TRỌNG)
// Logic này giúp file này tự lo liệu việc lấy dữ liệu cho biến $order
if (isset($_GET['edit_order'])) {
    $edit_id = mysqli_real_escape_string($con, $_GET['edit_order']);

    $get_order = "SELECT * FROM `orders` WHERE id = '$edit_id'";
    $run_order = mysqli_query($con, $get_order);
    $order = mysqli_fetch_assoc($run_order);

    if (!$order) {
        echo "<script>alert('Đơn hàng không tồn tại!'); window.location.href='index.php?view_order';</script>";
        exit();
    }
}

// 3. XỬ LÝ CẬP NHẬT KHI SUBMIT FORM
if (isset($_POST['update_order_btn'])) {
    $order_id = mysqli_real_escape_string($con, $_POST['order_id']);
    $status = mysqli_real_escape_string($con, $_POST['status']);
    $payment_status = mysqli_real_escape_string($con, $_POST['payment_status']);

    $update_query = "UPDATE `orders` 
                     SET `status` = '$status', 
                         `payment_status` = '$payment_status', 
                         `updated_at` = NOW() 
                     WHERE `id` = '$order_id'";

    if (mysqli_query($con, $update_query)) {
        $alert = '
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 shadow-sm mb-4" role="alert">
            <i class="fas fa-check-circle"></i>
            <div><strong>Thành công!</strong> Trạng thái đơn hàng đã được cập nhật.</div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>';
        $order['status'] = $status;
        $order['payment_status'] = $payment_status;
        $order['updated_at'] = date('Y-m-d H:i:s');
        header("Location: index.php?view_order&status=updated");
        exit();
    } else {
        $alert = '
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i> Lỗi hệ thống: Không thể cập nhật dữ liệu.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>';
    }
}
?>