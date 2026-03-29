<?php
include('../includes/connect.php');
include('../functions/common_functions.php');
session_start();

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
    <link rel="stylesheet" href="../assets/css/main.css" />
    <!-- Thêm Font Awesome để hiện Icon trong Sidebar -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <link rel="shortcut icon" type="image/x-icon" href="../assets/images/logo/icon-laptopshop.png" />

    <!-- Thêm Google Font Inter cho hiện đại -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
</head>

<style>
    :root {
        --primary-color: #4e73df;
        --secondary-color: #2e59d9;
        --sidebar-bg: #1a2035;
        --body-bg: #f8f9fc;
    }

    body {
        font-family: 'Inter', sans-serif;
        background-color: var(--body-bg);
    }

    /* Sidebar Styling */
    /* Sidebar tổng */
    .sidebar {
        width: 250px;
        background: #0f172a;
        min-height: 100vh;
        color: #cbd5e1;
    }

    /* Title section (Sản phẩm, Danh mục...) */
    .sidebar .text-uppercase {
        font-size: 11px !important;
        letter-spacing: 1px;
        font-weight: 600;
        margin-top: 18px;
        margin-bottom: 6px;
        color: #64748b !important;
    }

    /* Nav item */
    .sidebar .nav-item {
        margin: 2px 0;
    }

    /* Link chính */
    .sidebar .nav-link {
        color: #cbd5e1;
        padding: 10px 18px;
        font-size: 14px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        gap: 10px;
        transition: all 0.2s ease;
    }

    /* Icon */
    .sidebar .nav-link i {
        width: 18px;
        text-align: center;
        font-size: 14px;
    }

    /* Hover */
    .sidebar .nav-link:hover {
        background: rgba(255, 255, 255, 0.05);
        color: #fff;
    }

    /* Active */
    .sidebar .nav-link.active {
        background: #2563eb;
        color: #fff;
        font-weight: 500;
    }

    /* Khoảng cách toàn list */
    .sidebar .nav {
        padding: 0 10px;
    }

    /* Header MT SHOP */
    .sidebar h4 {
        font-size: 18px;
        letter-spacing: 1px;
    }

    .sidebar small {
        font-size: 11px;
        color: #94a3b8 !important;
    }

    /* Header & Dashboard Area */
    .admin-header {
        background: #fff;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        padding: 1rem 2rem;
    }

    .admin-profile-img {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
    }

    .main-content {
        width: 100%;
        padding: 30px;
    }

    .card-custom {
        border: none;
        border-radius: 12px;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
    }

    .section-title {
        font-weight: 700;
        color: #333;
        margin-bottom: 25px;
        border-left: 5px solid var(--primary-color);
        padding-left: 15px;
    }

    /* Màu mặc định của link */
    .sidebar .nav-link {
        color: rgba(255, 255, 255, 0.6);
        border-left: 3px solid transparent;
        /* Tạo viền ẩn bên trái */
        transition: all 0.3s ease;
    }

    /* Khi mục đó được Active */
    .sidebar .nav-link.active {
        background: rgba(255, 255, 255, 0.15);
        /* Nền sáng hơn một chút */
        color: #fff !important;
        font-weight: 600;
        border-left: 4px solid #4e73df;
        /* Vạch xanh bên trái chuẩn Admin */
    }

    /* Icon khi active cũng sáng lên */
    .sidebar .nav-link.active i {
        color: #4e73df;
    }

    /* Tiếng Việt Font mượt hơn */
    h2,
    h5 {
        font-weight: 600;
    }
</style>

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
                <a href="index.php?view_products" class="nav-link <?php echo isActive('view_products'); ?>">
                    <i class="fas fa-laptop"></i> Danh sách sản phẩm
                </a>
            </li>
            <li class="nav-item">
                <a href="insert_product.php" class="nav-link">
                    <i class="fas fa-plus-circle"></i> Thêm mới sản phẩm
                </a>
            </li>


            <div class="px-4 py-2 small text-uppercase text-muted" style="font-size: 0.7rem;">Quản lý danh mục</div>
            <li class="nav-item">
                <a href="index.php?view_categories" class="nav-link <?php echo isActive('view_categories'); ?>">
                    <i class="fas fa-th-list"></i> Danh sách danh mục & loại SP
                </a>
            </li>
            <li class="nav-item">
                <a href="index.php?view_brands" class="nav-link <?php echo isActive('view_brands'); ?>">
                    <i class="fas fa-copyright"></i> Thêm mới danh mục & loại SP
                </a>
            </li>

            <div class="px-4 py-2 small text-uppercase text-muted" style="font-size: 0.7rem;">Quản lý đơn hàng</div>
            <li class="nav-item">
                <a href="index.php?list_orders" class="nav-link <?php echo isActive('list_orders'); ?>">
                    <i class="fas fa-shopping-cart"></i> Danh sách đơn hàng
                </a>
            </li>
            <li class="nav-item">
                <a href="index.php?list_payments" class="nav-link <?php echo isActive('list_payments'); ?>">
                    <i class="fas fa-credit-card"></i> Thêm mới đơn hàng
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
                <a href="#" class="nav-link <?php echo isActive('list_users'); ?>">
                    <i class="fas fa-newspaper"></i> Danh sách bài viết
                </a>
            </li>
            <li class="nav-item" style="margin-bottom: 200px;">
                <a href="#" class="nav-link <?php echo isActive('list_payments'); ?>">
                    <i class="fas fa-pen-to-square"></i> Thêm mới bài viết
                </a>
            </li>

        </ul>
    </nav>

    <!-- Page Content -->
    <div class="flex-fill">
        <!-- Top Header -->
        <header class="admin-header d-flex justify-content-between align-items-center">
            <h5 class="m-0 text-muted">Hệ thống Quản trị Laptop</h5>
            <div class="d-flex align-items-center">
                <div class="text-end me-3">
                    <div class="fw-bold mb-0"><?php echo $admin_name; ?></div>
                    <small class="text-success">Trực tuyến</small>
                </div>
                <div class="dropdown">
                    <img src="./admin_images/<?php echo $admin_image; ?>" class="admin-profile-img dropdown-toggle"
                        data-bs-toggle="dropdown" style="cursor: pointer;">
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                        <li><a class="dropdown-content p-2 d-block text-decoration-none text-dark" href="#"><i
                                    class="fas fa-user-cog me-2"></i> Hồ sơ</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-content p-2 d-block text-decoration-none text-danger"
                                href="authentication/admin_logout.php"><i class="fas fa-sign-out-alt me-2"></i> Đăng
                                xuất</a></li>
                    </ul>
                </div>
            </div>
        </header>

        <!-- Main Workspace -->
        <main class="main-content">
            <div class="container-fluid">

                <!-- Area for Dynamic Content -->
                <div class="card card-custom p-4">
                    <?php
                    // Logic PHP giữ nguyên của bạn
                    if (isset($_GET['insert_category'])) {
                        include('./insert_categories.php');
                    }
                    if (isset($_GET['insert_brand'])) {
                        include('./insert_brands.php');
                    }
                    if (isset($_GET['view_products'])) {
                        include('./view_products.php');
                    }
                    if (isset($_GET['edit_product'])) {
                        include('./edit_product.php');
                    }
                    if (isset($_GET['delete_product'])) {
                        include('./delete_product.php');
                    }
                    if (isset($_GET['view_categories'])) {
                        include('./view_categories.php');
                    }
                    if (isset($_GET['view_brands'])) {
                        include('./view_brands.php');
                    }
                    if (isset($_GET['list_orders'])) {
                        include('./list_orders.php');
                    }
                    if (isset($_GET['list_payments'])) {
                        include('./list_payments.php');
                    }
                    if (isset($_GET['list_accounts'])) {
                        include('./accounts/list_accounts.php');
                    }
                    if (isset($_GET['create_account'])) {
                        include('./accounts/create_account.php');
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
<script src="../assets/js/bootstrap.bundle.js"></script>
</body>

</html>