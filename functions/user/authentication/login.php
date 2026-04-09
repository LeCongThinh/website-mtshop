<?php
function user_login_logic($con)
{
    if (isset($_POST['user_login'])) {
        // 1. Lấy và làm sạch dữ liệu đầu vào
        $user_email = mysqli_real_escape_string($con, $_POST['user_username']);
        $user_password = $_POST['user_password'];

        // 2. Truy vấn người dùng
        $select_query = "SELECT * FROM `users` WHERE email='$user_email' AND role='customer'";
        $select_result = mysqli_query($con, $select_query);

        if ($select_result && mysqli_num_rows($select_result) > 0) {
            $row_data = mysqli_fetch_assoc($select_result);

            if (password_verify($user_password, $row_data['password'])) {
                // 3. Khởi tạo Session
                $_SESSION['user_id'] = $row_data['id'];
                $_SESSION['user_name'] = $row_data['name'];
                $_SESSION['user_email'] = $row_data['email'];
                $_SESSION['user_role'] = $row_data['role'];

                $user_id = (int) $row_data['id'];

                // --- BẮT ĐẦU LOGIC MERGE GIỎ HÀNG ---
                if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
                    foreach ($_SESSION['cart'] as $product_id => $quantity) {
                        $product_id = (int) $product_id;
                        $quantity = (int) $quantity;

                        // Kiểm tra tồn tại bằng Prepared Statement để an toàn hơn
                        $stmt = mysqli_prepare($con, "SELECT id, quantity FROM `carts` WHERE user_id = ? AND product_id = ?");
                        mysqli_stmt_bind_param($stmt, "ii", $user_id, $product_id);
                        mysqli_stmt_execute($stmt);
                        $check_res = mysqli_stmt_get_result($stmt);

                        if ($cart_row = mysqli_fetch_assoc($check_res)) {
                            // Cập nhật cộng dồn
                            $new_qty = $cart_row['quantity'] + $quantity;
                            $update_id = $cart_row['id'];
                            mysqli_query($con, "UPDATE `carts` SET quantity = $new_qty, updated_at = NOW() WHERE id = $update_id");
                        } else {
                            // Thêm mới
                            mysqli_query($con, "INSERT INTO `carts` (user_id, product_id, quantity, created_at, updated_at) 
                                               VALUES ($user_id, $product_id, $quantity, NOW(), NOW())");
                        }
                    }
                    // Xóa giỏ hàng Session sau khi merge thành công
                    unset($_SESSION['cart']);
                }

                // 4. Kiểm tra số lượng sản phẩm trong DB để điều hướng
                $check_db_cart = mysqli_query($con, "SELECT COUNT(*) as total FROM `carts` WHERE user_id = $user_id");
                $cart_data = mysqli_fetch_assoc($check_db_cart);
                $cart_count = $cart_data['total'];

                // Trong file login, dòng điều hướng thành công:
                if ($cart_count == 0) {
                    $name = urlencode($row_data['name']);
                    // Thêm page=home vào đây
                    echo "<script>window.open('../../index.php?page=home&login=success&user=$name','_self')</script>";
                } else {
                    // Nếu có hàng thì về trang giỏ hàng (đã có sẵn trong code của bạn)
                    echo "<script>window.open('../../index.php?page=cart&login=has_cart&qty=$cart_count','_self')</script>";
                }
                exit; // Dừng thực thi sau khi redirect

            } else {
                echo "<script>alert('Mật khẩu không chính xác!')</script>";
            }
        } else {
            echo "<script>alert('Tài khoản không tồn tại!')</script>";
        }
    }
}
?>