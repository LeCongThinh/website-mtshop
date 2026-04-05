<?php
include __DIR__ . '/../../includes/connect.php';
include __DIR__ . '/../../functions/common_functions.php';
include __DIR__ . '/../../functions/user/authentication/login.php'; // Gọi file chứa hàm vào đây
@session_start();

// Gọi hàm thực thi logic ngay khi load trang
user_login_logic($con);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Tech Store</title>
    <link rel="stylesheet" href="../../assets/css/bootstrap.css" />
    <link rel="stylesheet" href="../../assets/css/main.css" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/user/login.css" />
</head>

<body>

    <div class="login-container">
        <div class="login-card">
            <div class="login-side-img">
                <h1 class="fw-bold mb-3">MTShop</h1>
                <p class="fs-5 opacity-75">Nâng tầm trải nghiệm công nghệ với những dòng máy tính đỉnh cao.</p>
                <div class="mt-4 border-start ps-3 border-white-50 small">
                    "Sáng tạo không giới hạn cùng hiệu năng vượt trội."
                    "Chơi game bao đã."
                </div>
            </div>

            <div class="login-form-area">
                <div class="mb-4">
                    <h2 class="fw-bold">Chào mừng trở lại!</h2>
                    <p style="color: var(--text-muted)">Đăng nhập để quản lý tài khoản của bạn</p>
                </div>

                <form action="" method="post" class="d-flex flex-column gap-3">
                    <div class="form-group">
                        <label class="form-label small fw-semibold"> Email</label>
                        <input type="text" name="user_username" placeholder="Nhập email của bạn!" required class="form-control">
                    </div>

                    <div class="form-group">
                        <label class="form-label small fw-semibold">Mật khẩu</label>
                        <input type="password" name="user_password" placeholder="••••••••" required class="form-control">
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="remember">
                            <label class="form-check-label small" for="remember">Ghi nhớ tôi</label>
                        </div>
                        <a href="#" class="small text-primary-tech text-decoration-none fw-medium">Quên mật khẩu?</a>
                    </div>

                    <button type="submit" name="user_login" class="btn btn-primary-tech">Đăng nhập ngay</button>

                    <p class="text-center mt-4 mb-0 small text-muted">
                        Bạn là người mới?
                        <a href="user_registration.php" class="text-primary-tech fw-bold text-decoration-none">Tạo tài khoản</a>
                    </p>
                </form>
            </div>
        </div>
    </div>

    <script src="../assets/js/bootstrap.bundle.js"></script>
</body>

</html>