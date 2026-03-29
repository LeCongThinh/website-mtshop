<?php
include('../includes/connect.php');
include('../functions/common_functions.php');
include('../functions/user/authentication/register.php'); 
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
     <link rel="stylesheet" href="../assets/css/user/register.css" />
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

