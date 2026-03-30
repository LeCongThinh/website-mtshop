<?php
include(__DIR__ . '/../../includes/connect.php');
include(__DIR__ . '/../../functions/admin/authentication/login.php');

session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập tài khoản quản trị - MTSHop</title>
    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link rel="shortcut icon" type="image/x-icon" href="../../assets/images/logo/icon-laptopshop.png" />

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <link rel="stylesheet" href="../../assets/css/admin/login.css" />
</head>

<body>

    <div class="container p-3">
        <div class="card login-card mx-auto">
            <div class="row g-0">
                <div class="col-md-5 login-info-side">
                    <i class="fas fa-user-shield fa-4x mb-4"></i>
                    <h2 class="fw-bold mb-3">MTShop</h2>
                    <p class="opacity-75">Quản lý cửa hàng của bạn một cách dễ dàng và hiệu quả hơn</p>
                </div>

                <div class="col-md-7 login-form-side">
                    <div class="mb-4">
                        <h3 class="fw-bold text-dark">Đăng nhập trang quản trị</h3>
                        <p class="text-muted">Vui lòng nhập thông tin để tiếp tục</p>
                    </div>

                    <form action="" method="post">
                        <div class="mb-3">
                            <label for="username" class="form-label text-secondary small fw-bold">EMAIL ĐĂNG
                                NHẬP</label>
                            <div class="input-group">
                                <span class="input-group-text bg-transparent border-end-0">
                                    <i class="far fa-envelope"></i>
                                </span>
                                <input type="email" name="username" id="username" class="form-control border-start-0"
                                    placeholder="Nhập email đăng nhập..." required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="d-flex justify-content-between">
                                <label for="password" class="form-label text-secondary small fw-bold">Mật khẩu</label>
                            </div>
                            <div class="input-group">
                                <span class="input-group-text bg-transparent border-end-0"><i
                                        class="fas fa-lock"></i></span>
                                <input type="password" name="password" id="password" class="form-control border-start-0"
                                    placeholder="••••••••" required>
                            </div>
                        </div>

                        <button type="submit" name="admin_login" class="btn btn-login w-100 mb-4">
                            Đăng nhập <i class="fas fa-arrow-right ms-2"></i>
                        </button>

                        <p class="text-center mt-4 text-muted small">
                            Chưa có tài khoản? <a href="admin_resgistration.php"
                                class="text-primary fw-bold text-decoration-none">Đăng ký ngay</a>
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <?php
    handleAdminLogin($con);
    ?>
</body>

</html>

