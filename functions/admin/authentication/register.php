<?php
function handleAdminRegistration($con)
{
    if (isset($_POST['admin_register'])) {
        $name = mysqli_real_escape_string($con, $_POST['username']);
        $email = mysqli_real_escape_string($con, $_POST['email']);
        $phone = mysqli_real_escape_string($con, $_POST['admin_contact']);
        $password = $_POST['password'];
        $conf_password = $_POST['conf_password'];

        // Kiểm tra mật khẩu
        if ($password !== $conf_password) {
            echo "<script>Swal.fire({icon: 'error', title: 'Lỗi', text: 'Mật khẩu không khớp!'});</script>";
            return;
        }

        // Kiểm tra email tồn tại
        $check_query = "SELECT * FROM `users` WHERE email='$email'";
        $check_result = mysqli_query($con, $check_query);
        if (mysqli_num_rows($check_result) > 0) {
            echo "<script>Swal.fire({icon: 'warning', title: 'Lỗi', text: 'Email đã tồn tại!'});</script>";
            return;
        }

        // Hash và Data chuẩn bị
        $hash_password = password_hash($password, PASSWORD_DEFAULT);
        $role = 'staff';
        $status = 'active';
        $avatar = 'avatars/blank_user.png';
        $now = date('Y-m-d H:i:s');

        $insert_query = "INSERT INTO `users` (avatar, name, email, password, phone, address, role, status, created_at, updated_at) 
                         VALUES ('$avatar', '$name', '$email', '$hash_password', '$phone', '', '$role', '$status', '$now', '$now')";

        $insert_result = mysqli_query($con, $insert_query);

        if ($insert_result) {
            echo "
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 2000,
                        timerProgressBar: true
                    });
                    Toast.fire({
                        icon: 'success',
                        title: 'Đăng ký Staff thành công!'
                    }).then(() => {
                        window.location.href = 'admin_login.php';
                    });
                });
            </script>";
        } else {
            echo "Lỗi: " . mysqli_error($con);
        }
    }
}