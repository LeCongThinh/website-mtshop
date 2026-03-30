<?php
function handleAdminLogin($con)
{
    if (isset($_POST['admin_login'])) {
        $email = mysqli_real_escape_string($con, $_POST['username']);
        $password = $_POST['password'];

        // Lấy thông tin user (phải active và thuộc nhóm nhân sự)
        $select_query = "SELECT * FROM `users` WHERE `email`='$email' AND `status`='active'";
        $select_result = mysqli_query($con, $select_query);
        $row_count = mysqli_num_rows($select_result);

        if ($row_count > 0) {
            $row_data = mysqli_fetch_assoc($select_result);

            // Kiểm tra mật khẩu và quyền hạn
            if (password_verify($password, $row_data['password'])) {

                $user_role = $row_data['role'];

                // Kiểm tra nếu không phải admin hoặc staff thì từ chối
                if ($user_role !== 'admin' && $user_role !== 'staff') {
                    echo "<script>
                        document.addEventListener('DOMContentLoaded', function() {
                            Swal.fire({ icon: 'error', title: 'Truy cập bị từ chối', text: 'Tài khoản này không có quyền vào trang quản trị!' });
                        });
                    </script>";
                    return;
                }

                // Lưu Session
                $_SESSION['admin_id'] = $row_data['id'];
                $_SESSION['admin_name'] = $row_data['name'];
                $_SESSION['admin_role'] = $user_role;
                $_SESSION['admin_avatar'] = $row_data['avatar'];

                // Chuyển hướng về admin/dashboard.php
                echo "
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const Toast = Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 1500,
                            timerProgressBar: true
                        });
                        Toast.fire({
                            icon: 'success',
                            title: 'Chào mừng " . $row_data['name'] . "!'
                        }).then(() => {
                            window.location.href = '../index.php'; 
                        });
                    });
                </script>";
            } else {
                echo "<script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Swal.fire({ icon: 'error', title: 'Lỗi', text: 'Mật khẩu không chính xác!' });
                    });
                </script>";
            }
        } else {
            echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({ icon: 'warning', title: 'Thất bại', text: 'Tài khoản không tồn tại hoặc đã bị khóa!' });
                });
            </script>";
        }
    }
}
?>