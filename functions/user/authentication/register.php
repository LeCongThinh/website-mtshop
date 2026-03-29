<?php
if (isset($_POST['user_register'])) {
    $user_name = mysqli_real_escape_string($con, $_POST['user_name']);
    $user_email = mysqli_real_escape_string($con, $_POST['user_email']);
    $user_password = $_POST['user_password'];
    $conf_user_password = $_POST['conf_user_password'];
    
    // Set role mặc định và các giá trị ban đầu
    $user_role = 'customer';
    $user_status = 'active';

    // 1. Kiểm tra Email đã tồn tại chưa
    $check_email = "SELECT * FROM `users` WHERE email='$user_email'";
    $result_check = mysqli_query($con, $check_email);
    
    if (mysqli_num_rows($result_check) > 0) {
        echo "<script>alert('Email này đã được đăng ký. Vui lòng sử dụng email khác!')</script>";
    } 
    // 2. Kiểm tra mật khẩu khớp nhau
    else if ($user_password != $conf_user_password) {
        echo "<script>alert('Mật khẩu xác nhận không khớp!')</script>";
    } 
    else {
        // 3. MÃ HÓA MẬT KHẨU
        $hash_password = password_hash($user_password, PASSWORD_DEFAULT);

        // 4. THỰC HIỆN ĐĂNG KÝ
        // Lưu ý: Các cột như avatar, phone, address để trống (null) hoặc mặc định
        $insert_query = "INSERT INTO `users` (name, email, password, role, status, created_at) 
                         VALUES ('$user_name', '$user_email', '$hash_password', '$user_role', '$user_status', NOW())";
        
        $sql_execute = mysqli_query($con, $insert_query);

        if ($sql_execute) {
            echo "<script>alert('Đăng ký tài khoản thành công!')</script>";
            echo "<script>window.open('user_login.php','_self')</script>";
        } else {
            die(mysqli_error($con));
        }
    }
}
?>