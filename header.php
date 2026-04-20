<?php
// 1. XỬ LÝ LOGIC ĐẦU TRANG
$total_cart_items = 0;
// Đường dẫn ảnh mặc định (tính từ gốc thư mục web của bạn)
$default_avatar = 'admin/admin_images/avatars/blank_user.png'; 
$user_avatar = $default_avatar; 

if (isset($_SESSION['user_id'])) {
    $u_id = $_SESSION['user_id'];
    
    $res_user = mysqli_query($con, "SELECT avatar FROM users WHERE id = $u_id");
    if ($res_user && $u_data = mysqli_fetch_assoc($res_user)) {
        // Kiểm tra: 1. Cột avatar không trống | 2. File tồn tại trong thư mục admin
        $file_path = "admin/admin_images/" . $u_data['avatar'];
        
        if (!empty($u_data['avatar']) && file_exists($file_path)) {
            $user_avatar = $file_path; // Gán full đường dẫn ảnh người dùng
        } else {
            $user_avatar = $default_avatar; // Trả về mặc định nếu không thỏa điều kiện
        }
    }

    // Lấy tổng số lượng sản phẩm trong giỏ hàng
    $res = mysqli_query($con, "SELECT SUM(quantity) as total FROM carts WHERE user_id = $u_id");
    if ($res) {
        $cart_data = mysqli_fetch_assoc($res);
        $total_cart_items = $cart_data['total'] ?? 0;
    }
} else {
    $total_cart_items = isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0;
}
?>

<nav class="navbar navbar-expand-lg navbar-light bg-light sticky-top">
    <div class="container px-4 px-lg-5 mt-1">
        <a class="navbar-brand d-flex align-items-center gap-2" href="index.php">
            <img src="assets/images/logo/icon-laptopshop.png" alt="" width="30" height="30">MTShop
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-4">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown">Danh mục</a>
                    <ul class="dropdown-menu shadow border-0">
                        <?php if (!empty($categories)): ?>
                            <?php foreach ($categories as $category): ?>
                                <li class="dropdown-submenu position-relative">
                                    <a class="dropdown-item d-flex justify-content-between align-items-center" href="index.php?page=category&slug=<?php echo $category['slug']; ?>">
                                        <?php echo htmlspecialchars($category['name']); ?>
                                        <?php if (!empty($category['children'])): ?><i class="bi bi-chevron-right small ms-2"></i><?php endif; ?>
                                    </a>
                                    <?php if (!empty($category['children'])): ?>
                                        <ul class="dropdown-menu submenu">
                                            <?php foreach ($category['children'] as $child): ?>
                                                <li><a class="dropdown-item" href="index.php?page=subcategory&slug=<?php echo $child['slug']; ?>"><?php echo htmlspecialchars($child['name']); ?></a></li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php endif; ?>
                                </li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                </li>
            </ul>

            <form action="index.php" method="GET" class="d-flex flex-grow-1 justify-content-center my-3 my-lg-0 px-lg-5 position-relative">
                <input type="hidden" name="page" value="search-result">
                <div class="position-relative w-100" style="max-width:500px;">
                    <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                    <input class="form-control ps-5" type="search" id="search-input" name="keyword" placeholder="Bạn cần tìm gì..." autocomplete="off" value="<?php echo isset($_GET['keyword']) ? htmlspecialchars($_GET['keyword']) : ''; ?>">
                    <div id="search-results" class="position-absolute bg-white w-100 shadow-sm rounded-bottom d-none" style="z-index: 1050; top: 100%; max-height: 400px; overflow-y: auto;"></div>
                </div>
            </form>

            <div class="d-flex align-items-center gap-3">
                <a href="index.php?page=cart" class="btn cart-btn-mini p-0 border-0">
                    <div class="cart-icon-wrapper">
                        <i class="bi bi-cart3 fs-4"></i>
                        <span class="cart-badge" id="cart-count"><?php echo $total_cart_items; ?></span>
                    </div>
                </a>

                <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="dropdown">
                        <a href="#" class="d-flex align-items-center gap-2 text-decoration-none" data-bs-toggle="dropdown">
                            <div class="auth-icon-wrapper" style="width: 35px; height: 35px; overflow: hidden; border-radius: 50%; border: 1px solid #ddd; background: #eee;">
    <img src="<?php echo $user_avatar; ?>" alt="Avatar" style="width: 100%; height: 100%; object-fit: cover;">
</div>
                            <span class="auth-text text-dark d-none d-sm-inline"><?php echo $_SESSION['user_name']; ?></span>
                        </a>
                        <ul class="dropdown-menu shadow border-0 dropdown-menu-end">
                            <li><a class="dropdown-item py-2" href="index.php?page=profile"><i class="bi bi-person me-2"></i> Hồ sơ cá nhân</a></li>
                            <li><a class="dropdown-item py-2" href="index.php?page=my-orders"><i class="bi bi-bag me-2"></i> Lịch sử mua hàng</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a href="functions/user/authentication/logout.php" class="dropdown-item text-danger py-2"><i class="bi bi-box-arrow-right me-2"></i> Đăng xuất</a></li>
                        </ul>
                    </div>
                <?php else: ?>
                    <a href="users_area/authentication/user_login.php" class="btn btn-outline-primary btn-sm rounded-3 px-3">Đăng nhập</a>  
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<style>
    #search-results { border: 1px solid #ddd; border-top: none; }
    .search-item { display: flex; align-items: center; padding: 10px; border-bottom: 1px solid #f1f1f1; text-decoration: none; color: #333; transition: background 0.2s; }
    .search-item:hover { background-color: #f8f9fa; color: #000; }
    .search-item img { width: 40px; height: 40px; object-fit: cover; margin-right: 15px; border-radius: 4px; }
    .item-name { font-size: 14px; font-weight: 600; margin-bottom: 0; }
    .item-price { font-size: 13px; color: #dc3545; }
</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        const searchInput = $('#search-input');
        const searchResults = $('#search-results');

        searchInput.on('keyup', function() {
            let keyword = $(this).val().trim();
            if (keyword.length > 0) {
                $.ajax({
                    url: 'functions/user/search-controller.php',
                    type: 'GET',
                    data: { action: 'live', keyword: keyword },
                    dataType: 'json',
                    success: function(data) {
                        searchResults.empty();
                        if (data.length > 0) {
                            searchResults.removeClass('d-none').show();
                            $.each(data, function(index, product) {
                                let html = `
                                    <a href="index.php?page=product-detail&slug=${product.slug}" class="search-item">
                                        <img src="admin/admin_images/${product.thumbnail}" alt="${product.name}">
                                        <div class="item-info">
                                            <p class="item-name">${product.name}</p>
                                            <span class="item-price">${new Intl.NumberFormat('vi-VN').format(product.price)}đ</span>
                                        </div>
                                    </a>`;
                                searchResults.append(html);
                            });
                        } else {
                            searchResults.append('<div class="p-3 text-muted small">Không thấy sản phẩm phù hợp.</div>');
                            searchResults.removeClass('d-none').show();
                        }
                    }
                });
            } else {
                searchResults.hide().addClass('d-none');
            }
        });

        $(document).on('click', function(e) {
            if (!$(e.target).closest('#search-input, #search-results').length) {
                searchResults.hide().addClass('d-none');
            }
        });
    });
</script>