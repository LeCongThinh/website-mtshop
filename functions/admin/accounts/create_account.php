<?php
// Kết nối DB đã được include ở file index.php nên không cần include lại ở đây 
// trừ khi bạn gọi file này trực tiếp qua URL.

if (isset($_POST['insert_account'])) {
    // Làm sạch dữ liệu đầu vào
    $user_name = mysqli_real_escape_string($con, $_POST['user_name']);
    $user_email = mysqli_real_escape_string($con, $_POST['user_email']);
    $user_password = $_POST['user_password'];
    $hash_password = password_hash($user_password, PASSWORD_DEFAULT);
    $user_phone = mysqli_real_escape_string($con, $_POST['user_phone']);
    $user_address = mysqli_real_escape_string($con, $_POST['user_address']);
    $user_role = $_POST['user_role'];
    $user_status = 'active';

    // 1. Kiểm tra email trùng lặp
    $select_query = "SELECT * FROM `users` WHERE email='$user_email'";
    $result_select = mysqli_query($con, $select_query);

    if (mysqli_num_rows($result_select) > 0) {
        echo "<script>alert('Email này đã tồn tại!')</script>";
    } else {
        // 2. Xử lý File Upload
        if (isset($_FILES['user_avatar']) && $_FILES['user_avatar']['error'] == 0) {
            $user_avatar = $_FILES['user_avatar']['name'];
            $user_avatar_tmp = $_FILES['user_avatar']['tmp_name'];

            // Tạo tên file duy nhất
            $file_extension = pathinfo($user_avatar, PATHINFO_EXTENSION);
            $new_file_name = date('YmdHis') . '_' . uniqid() . '.' . $file_extension;

            // ĐƯỜNG DẪN VẬT LÝ (Tính từ file create-account.php lùi ra website-mtshop)
            // Cấu trúc: functions/admin/accounts/create-account.php
            // Cần lùi 3 cấp để ra thư mục gốc, rồi vào admin_images/avatars/
            $upload_directory = dirname(dirname(dirname(__DIR__))) . DIRECTORY_SEPARATOR . 'admin_images' . DIRECTORY_SEPARATOR . 'avatars' . DIRECTORY_SEPARATOR;

            if (!is_dir($upload_directory)) {
                mkdir($upload_directory, 0777, true);
            }

            $physical_path = $upload_directory . $new_file_name;

            if (move_uploaded_file($user_avatar_tmp, $physical_path)) {
                // ĐƯỜNG DẪN LƯU VÀO DATABASE
                $db_path = "avatars/" . $new_file_name;

                // 3. Insert dữ liệu
                $insert_query = "INSERT INTO `users` (avatar, name, email, password, phone, address, role, status, created_at) 
                                 VALUES ('$db_path', '$user_name', '$user_email', '$hash_password', '$user_phone', '$user_address', '$user_role', '$user_status', NOW())";

                $sql_execute = mysqli_query($con, $insert_query);

                if ($sql_execute) {
                    echo "<script>alert('Thêm tài khoản thành công!')</script>";
                    echo "<script>window.open('index.php?list_accounts','_self')</script>";
                } else {
                    echo "<script>alert('Lỗi truy vấn Database!')</script>";
                }
            } else {
                echo "<script>alert('Lỗi: Không thể ghi file vào hệ thống.')</script>";
            }
        } else {
            echo "<script>alert('Vui lòng chọn ảnh đại diện!')</script>";
        }
    }
}
?>