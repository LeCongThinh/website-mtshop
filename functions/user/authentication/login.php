<?php
// Hàm xử lý đăng nhập người dùng
function user_login_logic($con) {
    if (isset($_POST['user_login'])) {
        // 1. Lấy dữ liệu và chống SQL Injection
        $user_email = mysqli_real_escape_string($con, $_POST['user_username']); 
        $user_password = $_POST['user_password']; 

        // 2. Truy vấn kiểm tra email và role là 'customer'
        $select_query = "SELECT * FROM `users` WHERE email='$user_email' AND role='customer'";
        $select_result = mysqli_query($con, $select_query);
        
        if ($select_result && mysqli_num_rows($select_result) > 0) {
            $row_data = mysqli_fetch_assoc($select_result);

            // 3. Kiểm tra mật khẩu (Dùng password_verify vì trang Register đã hash)
            if (password_verify($user_password, $row_data['password'])) {
                
                // Khởi tạo Session
                $_SESSION['user_id']    = $row_data['id'];
                $_SESSION['username']   = $row_data['name'];
                $_SESSION['user_email'] = $row_data['email'];
                $_SESSION['user_role']  = $row_data['role'];

                // 4. Kiểm tra giỏ hàng để điều hướng
                $user_ip = getIPAddress();
                $select_cart_query = "SELECT * FROM `card_details` WHERE ip_address='$user_ip'";
                $select_cart_result = mysqli_query($con, $select_cart_query);
                $row_cart_count = mysqli_num_rows($select_cart_result);

                if ($row_cart_count == 0) {
                    echo "<script>alert('Chào mừng " . $row_data['name'] . " đã quay trở lại!')</script>";
                    echo "<script>window.open('profile.php','_self')</script>";
                } else {
                    echo "<script>alert('Bạn có sản phẩm chưa thanh toán trong giỏ hàng.')</script>";
                    echo "<script>window.open('payment.php','_self')</script>";
                }
            } else {
                echo "<script>alert('Mật khẩu không chính xác!')</script>";
            }
        } else {
            echo "<script>alert('Tài khoản không tồn tại hoặc không có quyền truy cập!')</script>";
        }
    }
}
?>