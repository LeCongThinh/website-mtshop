<?php
// 1. Lấy dữ liệu cũ từ Database (Luôn thực hiện để làm giá trị mặc định)
if (isset($_GET['edit_user'])) {
    $edit_id = (int) $_GET['edit_user'];
    $get_user = "SELECT * FROM `users` WHERE id='$edit_id'";
    $run_user = mysqli_query($con, $get_user);
    $row_user = mysqli_fetch_array($run_user);

    if ($row_user) {
        // Gán các biến để file giao diện (View) có thể hiểu được
        $user_name = $row_user['name'];
        $user_email = $row_user['email']; // Biến này sẽ fix lỗi dòng 81 của bạn
        $user_phone = $row_user['phone'];
        $user_address = $row_user['address'];
        $user_role = $row_user['role'];
        $old_avatar = $row_user['avatar'];
    }
}

// 2. Xử lý khi nhấn nút Cập nhật
if (isset($_POST['update_account'])) {
    // Sử dụng toán tử ?? (null coalescing) để lấy giá trị cũ nếu $_POST bị trống
    $name = mysqli_real_escape_string($con, $_POST['user_name'] ?? $user_name_old);
    $email = mysqli_real_escape_string($con, $_POST['user_email'] ?? $user_email_old); // Fix lỗi Duplicate entry
    $phone = mysqli_real_escape_string($con, $_POST['user_phone'] ?? $user_phone_old);
    $address = mysqli_real_escape_string($con, $_POST['user_address'] ?? $user_address_old);
    $role = mysqli_real_escape_string($con, $_POST['user_role'] ?? $user_role_old);
    // Khởi tạo biến thông báo
    $status_update = "";
    // Xử lý Ảnh đại diện
    $avatar_db_path = $old_avatar;
    if (isset($_FILES['user_avatar']) && $_FILES['user_avatar']['error'] == 0) {
        $user_avatar = $_FILES['user_avatar']['name'];
        $user_avatar_tmp = $_FILES['user_avatar']['tmp_name'];

        $extension = pathinfo($user_avatar, PATHINFO_EXTENSION);
        $new_file_name = date('YmdHis') . '_' . uniqid() . '.' . $extension;

        // Đường dẫn thư mục upload
        $upload_dir = $_SERVER['DOCUMENT_ROOT'] . "/project-php/website-mtshop/admin/admin_images/avatars/";

        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        if (move_uploaded_file($user_avatar_tmp, $upload_dir . $new_file_name)) {
            $avatar_db_path = "avatars/" . $new_file_name;

            // Xóa ảnh cũ (tránh rác server)
            if (!empty($old_avatar) && $old_avatar != 'avatars/default.png' && strpos($old_avatar, 'default') === false) {
                $old_file_path = $_SERVER['DOCUMENT_ROOT'] . "/project-php/website-mtshop/admin/admin_images/" . $old_avatar;
                if (file_exists($old_file_path)) {
                    unlink($old_file_path);
                }
            }
        }
    }

    // Thực thi câu lệnh UPDATE
    $update_query = "UPDATE `users` SET 
        `name`='$name', 
        `email`='$email', 
        `phone`='$phone', 
        `address`='$address', 
        `role`='$role', 
        `avatar`='$avatar_db_path' 
        WHERE `id`='$edit_id'";

    if (mysqli_query($con, $update_query)) {
        $status_update = "success";
        $user_name = $name;
        $user_phone = $phone;
        $user_address = $address;
        $user_role = $role;
        $old_avatar = $avatar_db_path;
    } else {
        $status_update = "error";
    }
}
?>