<?php
include('../includes/connect.php');
include('../functions/common_functions.php');
include('../functions/admin/statistics/statistics_functions.php');
ob_start();
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include('../functions/admin/authentication/check_admin.php');

if (!isset($_SESSION['admin_id'])) {
    header("Location: ./admin_login.php");
    exit();
}

$allowed_roles = ['admin', 'staff'];
if (!in_array($_SESSION['admin_role'], $allowed_roles)) {
    echo "<script>alert('Bạn không có quyền truy cập trang này!');</script>";
    echo "<script>window.location.href='./admin_login.php';</script>";
    exit();
}

$user_id = $_SESSION['admin_id'];
$get_user_data = "SELECT * FROM `users` WHERE id = '$user_id'";
$get_user_result = mysqli_query($con, $get_user_data);

if ($row = mysqli_fetch_array($get_user_result)) {
    $admin_name = $row['name'];
    $admin_image = $row['avatar'];
    $admin_role = $row['role'];
    $admin_phone = $row['phone'];
    $admin_email = $row['email'];
    $admin_join = $row['created_at'];
} else {
    // Đề phòng trường hợp tài khoản vừa bị xóa khỏi DB
    session_destroy();
    header("Location: ./admin_login.php");
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang chủ Admin - MTShop</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.css" />
    <link rel="stylesheet" href="../assets/css/admin/main_admin.css" />
    <!-- <link rel="stylesheet" href="../assets/css/main.css" /> -->
    <!-- Thêm Font Awesome để hiện Icon trong Sidebar -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <link rel="shortcut icon" type="image/x-icon" href="../assets/images/logo/icon-laptopshop.png" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Thêm Google Font Inter cho hiện đại -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
</head>

<div class="d-flex">
    <!-- Sidebar -->
    <nav class="sidebar shadow">
        <div class="p-4 text-center">
            <a href="index.php" class="text-decoration-none">
                <h2 class="fw-bold text-white tracking-widest">MTSHOP</h2>
            </a>
        </div>

        <ul class="nav flex-column">
            <!-- Hàm hỗ trợ kiểm tra active -->
            <?php
            function isActive($key)
            {
                if ($key == 'dashboard' && empty($_SERVER['QUERY_STRING']))
                    return 'active';
                // Kiểm tra active cho cả trang list và trang edit liên quan
                if ($key == 'list_accounts' && isset($_GET['edit_user']))
                    return 'active';
                return (isset($_GET[$key])) ? 'active' : '';
            }
            ?>

            <li class="nav-item">
                <a href="index.php" class="nav-link <?php echo isActive('dashboard'); ?>">
                    <i class="fas fa-tachometer-alt"></i> Thống kê đơn hàng
                </a>
            </li>

            <div class="px-4 py-2 small text-uppercase text-muted" style="font-size: 0.7rem;">Quản lý sản phẩm</div>
            <li class="nav-item">
                <a href="index.php?view_product" class="nav-link <?php echo isActive('view_product'); ?>">
                    <i class="fas fa-laptop"></i> Danh sách sản phẩm
                </a>
            </li>
            <li class="nav-item">
                <a href="index.php?create_product" class="nav-link <?php echo isActive('create_product'); ?>">
                    <i class="fas fa-plus-circle"></i> Thêm mới sản phẩm
                </a>
            </li>


            <div class="px-4 py-2 small text-uppercase text-muted" style="font-size: 0.7rem;">Quản lý danh mục</div>
            <li class="nav-item">
                <a href="index.php?view_category" class="nav-link <?php echo isActive('view_category'); ?>">
                    <i class="fas fa-th-list"></i> Danh sách danh mục
                </a>
            </li>
            <li class="nav-item">
                <a href="index.php?create_category" class="nav-link <?php echo isActive('create_category'); ?>">
                    <i class="fas fa-copyright"></i> Thêm mới danh mục
                </a>
            </li>
            <div class="px-4 py-2 small text-uppercase text-muted" style="font-size: 0.7rem;">Quản lý loại sản phẩm
            </div>
            <li class="nav-item">
                <a href="index.php?view_brand" class="nav-link <?php echo isActive('view_brand'); ?>">
                    <i class="fas fa-th-list"></i> Danh sách loại SP
                </a>
            </li>
            <li class="nav-item">
                <a href="index.php?create_brand" class="nav-link <?php echo isActive('create_brand'); ?>">
                    <i class="fas fa-copyright"></i> Thêm mới loại SP
                </a>
            </li>
            <div class="px-4 py-2 small text-uppercase text-muted" style="font-size: 0.7rem;">Quản lý đơn hàng</div>
            <li class="nav-item">
                <a href="index.php?view_order" class="nav-link <?php echo isActive('view_order'); ?>">
                    <i class="fas fa-shopping-cart"></i> Xử lý đơn hàng
                </a>
            </li>

            <div class="px-4 py-2 small text-uppercase text-muted" style="font-size: 0.7rem;">Quản lý tài khoản</div>
            <li class="nav-item">
                <a href="index.php?list_accounts" class="nav-link <?php echo isActive('list_accounts'); ?>">
                    <i class="fas fa-users"></i> Danh sách tài khoản
                </a>
            </li>
            <li class="nav-item">
                <a href="index.php?create_account" class="nav-link <?php echo isActive('create_account'); ?>">
                    <i class="fas fa-user-plus"></i> Thêm mới tài khoản
                </a>
            </li>

            <div class="px-4 py-2 small text-uppercase text-muted" style="font-size: 0.7rem;">Quản lý bài viết</div>
            <li class="nav-item">
                <a href="index.php?view_post" class="nav-link <?php echo isActive('view_post'); ?>">
                    <i class="fas fa-newspaper"></i> Danh sách bài viết
                </a>
            </li>
            <li class="nav-item" style="margin-bottom: 200px;">
                <a href="index.php?create_post" class="nav-link <?php echo isActive('create_post'); ?>">
                    <i class="fas fa-pen-to-square"></i> Thêm mới bài viết
                </a>
            </li>

        </ul>
    </nav>

    <!-- Page Content -->
    <div class="flex-fill">
        <!-- Top Header -->
        <header class="admin-header d-flex justify-content-between align-items-center">
            <h5 class="m-0 text-muted">Hệ thống Quản trị MTShop</h5>
            <div class="d-flex align-items-center">
                <div class="text-end me-3">
                    <div class="fw-bold mb-0"><?php echo $admin_name; ?></div>
                    <small class="text-success">Trực tuyến</small>
                </div>
                <div class="dropdown">
                    <img src="./admin_images/<?php echo $admin_image; ?>" class="admin-profile-img dropdown-toggle"
                        data-bs-toggle="dropdown" style="cursor: pointer;">
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                        <li>
                            <a class="dropdown-item p-2 d-flex align-items-center text-dark" href="#"
                                data-bs-toggle="modal" data-bs-target="#profileModal">
                                <i class="fas fa-user-cog me-2 text-center" style="width: 20px;"></i>
                                <span>Hồ sơ</span>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item p-2 d-flex align-items-center text-dark" href="javascript:void(0)"
                                data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                                <i class="fas fa-key me-2 text-center" style="width: 20px;"></i>
                                <span>Đổi mật khẩu</span>
                            </a>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <a class="dropdown-item p-2 d-flex align-items-center text-danger"
                                href="authentication/admin_logout.php">
                                <i class="fas fa-sign-out-alt me-2 width-20 text-center"></i>
                                <span>Đăng xuất</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </header>

        <!-- Main Workspace -->
        <main class="main-content">
            <div class="container-fluid">
                <?php if (isset($_GET['status']) || isset($_GET['error'])): ?>
                    <script>
                        document.addEventListener("DOMContentLoaded", function() {
                            <?php
                            $type = $_GET['status'] ?? $_GET['error'];

                            $swal = match ($type) {
                                'password_updated' => ['icon' => 'success', 'title' => 'Thành công!', 'text' => 'Đã đổi mật khẩu thành công!'],
                                'password_not_match' => ['icon' => 'error', 'title' => 'Lỗi!', 'text' => 'Mật khẩu mới và xác nhận không khớp.'],
                                'wrong_current_password' => ['icon' => 'error', 'title' => 'Thất bại!', 'text' => 'Mật khẩu hiện tại không chính xác.'],
                                'db_error' => ['icon' => 'warning', 'title' => 'Lỗi!', 'text' => 'Lỗi hệ thống, vui lòng thử lại.'],
                                default => null
                            };

                            if ($swal): ?>
                                Swal.fire({
                                    icon: '<?php echo $swal['icon']; ?>',
                                    title: '<?php echo $swal['title']; ?>',
                                    text: '<?php echo $swal['text']; ?>',
                                    confirmButtonColor: '#0d6efd',
                                    timer: 3000,
                                    timerProgressBar: true
                                }).then(() => {
                                    // Xóa tham số trên URL để khi F5 không hiện lại thông báo
                                    const url = new URL(window.location);
                                    url.searchParams.delete('status');
                                    url.searchParams.delete('error');
                                    window.history.replaceState({}, document.title, url);
                                });
                            <?php endif; ?>
                        });
                    </script>
                <?php endif; ?>

                <!-- Area for Dynamic Content -->
                <div class="card card-custom p-4">
                    <?php
                    //Thống kê sản phẩm
                    if (isset($_GET['dashboard']) || empty($_SERVER['QUERY_STRING'])) {
        include('./statistics/dashboard_view.php');
    }
                    // Quản lý sản phẩm
                    if (isset($_GET['view_product'])) {
                        include('./products/view_product.php');
                    }
                    if (isset($_GET['create_product'])) {
                        include('./products/create_product.php');
                    }
                    if (isset($_GET['edit_product'])) {
                        include('./products/edit_product.php');
                    }
                    if (isset($_GET['delete_product'])) {
                        include('./delete_product.php');
                    }
                    // Quản lý danh mục
                    if (isset($_GET['view_category'])) {
                        include('./categories/view_category.php');
                    }
                    if (isset($_GET['create_category'])) {
                        include('./categories/create_category.php');
                    }
                    if (isset($_GET['edit_category'])) {
                        include('./categories/edit_category.php');
                    }
                    // Quản lý loại sản phẩm
                    if (isset($_GET['view_brand'])) {
                        include('./brands/view_brand.php');
                    }
                    if (isset($_GET['create_brand'])) {
                        include('./brands/create_brand.php');
                    }
                    if (isset($_GET['edit_brand'])) {
                        include('./brands/edit_brand.php');
                    }
                    // Quản lý đơn hàng
                    if (isset($_GET['view_order'])) {
                        include('./orders/view_order.php');
                    }
                    if (isset($_GET['edit_order'])) {
                        include('./orders/edit_order.php');
                    }
                    // Quản lý tài khoản
                    if (isset($_GET['list_accounts'])) {
                        include('./accounts/list_accounts.php');
                    }
                    if (isset($_GET['create_account'])) {
                        include('./accounts/create_account.php');
                    }
                    if (isset($_GET['edit_user'])) {
                        include('./accounts/edit_account.php');
                    }
                    // Quản lý bài viết
                    if (isset($_GET['view_post'])) {
                        include('./posts/view_post.php');
                    }
                    if (isset($_GET['create_post'])) {
                        include('./posts/create_post.php');
                    }
                    if (isset($_GET['edit_post'])) {
                        include('./posts/edit_post.php');
                    }

                    // Mặc định nếu không chọn gì
                    if (empty($_SERVER['QUERY_STRING'])) {
                        echo "<div class='text-center py-5'>
                                    <img src='https://cdn-icons-png.flaticon.com/512/2038/2038032.png' style='width: 150px; opacity: 0.5;'>
                                    <h3 class='mt-3 text-muted'>Chào mừng trở lại, $admin_name!</h3>
                                    <p>Chọn một chức năng bên trái để bắt đầu quản lý.</p>
                                  </div>";
                    }
                    ?>
                </div>
            </div>
        </main>
    </div>
</div>
<?php include('./accounts/user_profile.php'); ?>
<?php include('./accounts/change_password.php'); ?>
<script src="../assets/js/bootstrap.bundle.js"></script>
<script src="../assets/js/admin/show_modal_order.js"></script>

</body>

</html>