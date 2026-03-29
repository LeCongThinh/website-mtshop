<?php
include(__DIR__ . '/../../includes/connect.php');
include(__DIR__ . '/../../functions/admin/authentication/register.php');
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Registration | Dashboard</title>
    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link rel="stylesheet" href="../../assets/css/register.css" />

</head>

<body>
    <div class="container">
        <div class="card register-card mx-auto">
            <div class="row g-0">
                <div class="col-md-5 register-info-side">
                    <i class="fas fa-user-plus fa-4x mb-4"></i>
                    <p class="opacity-75">Tạo tài khoản quản trị để bắt đầu quản lý đơn hàng, sản phẩm và khách hàng của
                        bạn một cách chuyên nghiệp.</p>
                </div>
                <div class="col-md-7 register-form-side">
                    <div class="mb-4">
                        <h3 class="fw-bold text-dark">Admin Registration</h3>
                        <p class="text-muted small">Tạo tài khoản để quản trị hệ thống</p>
                    </div>

                    <form action="" method="post" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label text-secondary small fw-bold">Tên đăng nhập</label>
                            <div class="input-group">
                                <span class="input-group-text bg-transparent"><i class="far fa-user"></i></span>
                                <input type="text" name="username" class="form-control border-start-0"
                                    placeholder="Tên đăng nhập" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-secondary small fw-bold">Email</label>
                            <div class="input-group">
                                <span class="input-group-text bg-transparent"><i class="far fa-envelope"></i></span>
                                <input type="email" name="email" class="form-control border-start-0"
                                    placeholder="admin@example.com" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-secondary small fw-bold">Số điện thoại</label>
                            <div class="input-group">
                                <span class="input-group-text bg-transparent"><i class="fas fa-phone-alt"></i></span>
                                <input type="tel" name="admin_contact" class="form-control border-start-0"
                                    placeholder="0987 xxx xxx" pattern="[0-9]{10,11}" required>
                            </div>
                        </div>

                        <!-- Passwords -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-secondary small fw-bold">Mật khẩu</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-transparent"><i class="fas fa-lock"></i></span>
                                    <input type="password" name="password" class="form-control border-start-0"
                                        placeholder="••••••••" required>
                                </div>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="form-label text-secondary small fw-bold">Xác nhận mật khẩu</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-transparent"><i
                                            class="fas fa-check-double"></i></span>
                                    <input type="password" name="conf_password" class="form-control border-start-0"
                                        placeholder="••••••••" required>
                                </div>
                            </div>
                        </div>

                        <!-- Nút Register chính -->
                        <button type="submit" name="admin_register" class="btn btn-register w-100 mb-3">
                            Đăng ký tài khoản <i class="fas fa-arrow-right ms-2"></i>
                        </button>

                        <!-- Nút Đăng nhập ngay (Đặt ở dưới) -->
                        <div class="text-center mt-3">
                            <p class="text-muted small">
                                Bạn đã có tài khoản quản trị?
                                <a href="admin_login.php"
                                    class="text-primary fw-bold text-decoration-none hover-underline">
                                    Đăng nhập ngay
                                </a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php
    handleAdminRegistration($con);
    ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>

</html>