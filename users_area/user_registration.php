<?php
include('../includes/connect.php');
include('../functions/common_functions.php');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký | Tech Store</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.css" />
    <link rel="stylesheet" href="../assets/css/main.css" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-bg: #f8f9fa;
        }
        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--secondary-bg);
        }
        .reg-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }
        .reg-card {
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.05);
            overflow: hidden;
            max-width: 1100px;
            width: 100%;
            display: flex;
        }
        .reg-side-img {
            background: linear-gradient(135deg, rgba(67, 97, 238, 0.9), rgba(67, 97, 238, 0.7)), 
                        url('https://images.unsplash.com/photo-1460925895917-afdab827c52f?q=80&w=800&auto=format&fit=crop');
            background-size: cover;
            background-position: center;
            width: 45%;
            padding: 60px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            color: white;
        }
        .reg-form-area {
            width: 55%;
            padding: 50px 60px;
        }
        .form-control {
            background-color: #f1f3f9;
            border: 2px solid transparent;
            border-radius: 12px;
            padding: 10px 16px;
            transition: 0.3s;
        }
        .form-control:focus {
            background-color: #fff;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 4px rgba(67, 97, 238, 0.1);
        }
        .btn-reg {
            background-color: var(--primary-color);
            border: none;
            border-radius: 12px;
            padding: 14px;
            font-weight: 600;
            color: white;
            transition: 0.3s;
        }
        .btn-reg:hover {
            background-color: #3046bc;
            transform: translateY(-2px);
        }
        @media (max-width: 992px) {
            .reg-side-img { display: none; }
            .reg-form-area { width: 100%; padding: 40px 20px; }
        }
    </style>
</head>
<body>

    <div class="reg-container">
        <div class="reg-card">
            <div class="reg-side-img">
                <h2 class="fw-bold mb-3">Tham gia MT Shop</h2>
                <p class="opacity-75">Tạo tài khoản để trải nghiệm những tính năng ưu đãi dành riêng cho thành viên.</p>
            </div>

            <div class="reg-form-area">
                <div class="mb-4">
                    <h2 class="fw-bold">Đăng ký tài khoản</h2>
                    <p class="text-muted small">Vui lòng điền thông tin bên dưới</p>
                </div>

                <form action="" method="post" class="d-flex flex-column gap-3">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-semibold">Họ và Tên</label>
                            <input type="text" name="user_name" placeholder="VD: Nguyễn Văn A" required class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-semibold">Email</label>
                            <input type="email" name="user_email" placeholder="email@example.com" required class="form-control">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Mật khẩu</label>
                        <input type="password" name="user_password" placeholder="Tối thiểu 6 ký tự" required class="form-control">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Xác nhận mật khẩu</label>
                        <input type="password" name="conf_user_password" placeholder="Nhập lại mật khẩu" required class="form-control">
                    </div>

                    <button type="submit" name="user_register" class="btn btn-reg mt-2">Tạo tài khoản</button>

                    <p class="text-center mt-4 small text-muted">
                        Đã có tài khoản? 
                        <a href="user_login.php" class="fw-bold text-decoration-none" style="color: var(--primary-color)">Đăng nhập ngay</a>
                    </p>
                </form>
            </div>
        </div>
    </div>

</body>
</html>

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