<?php
$total_cart_items = 0;
if (isset($_SESSION['user_id'])) {
    $u_id = $_SESSION['user_id'];
    // Thêm kiểm tra kết nối $con để tránh lỗi nếu include file bị sai thứ tự
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
    <div class="container px-4 px-lg-5">
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
                    <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button"
                        data-bs-toggle="dropdown" aria-expanded="false">Danh mục</a>
                    <ul class="dropdown-menu shadow border-0" aria-labelledby="navbarDropdown">
                        <?php if (!empty($categories)): ?>
                            <?php foreach ($categories as $category): ?>
                                <li class="dropdown-submenu position-relative">
                                    <a class="dropdown-item d-flex justify-content-between align-items-center"
                                        href="index.php?page=category&slug=<?php echo $category['slug']; ?>">
                                        <?php echo htmlspecialchars($category['name']); ?>
                                        <?php if (!empty($category['children'])): ?>
                                            <i class="bi bi-chevron-right small ms-2"></i>
                                            <?php
                                        endif; ?>
                                    </a>

                                    <?php if (!empty($category['children'])): ?>
                                        <ul class="dropdown-menu submenu">
                                            <?php foreach ($category['children'] as $child): ?>
                                                <li>
                                                    <a class="dropdown-item"
                                                        href="index.php?page=subcategory&slug=<?php echo $child['slug']; ?>">
                                                        <?php echo htmlspecialchars($child['name']); ?>
                                                    </a>
                                                </li>
                                                <?php
                                            endforeach; ?>
                                        </ul>
                                        <?php
                                    endif; ?>
                                </li>
                                <?php
                            endforeach; ?>
                            <?php
                        endif; ?>
                    </ul>
                </li>
            </ul>

            <form action="index.php" method="GET"
                class="d-flex flex-grow-1 justify-content-center my-3 my-lg-0 px-lg-5 position-relative">
                <input type="hidden" name="page" value="search-result">

                <div class="position-relative w-100" style="max-width:500px;">
                    <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                    <input class="form-control ps-5" type="search" id="search-input" name="keyword"
                        placeholder="Bạn cần tìm gì..." autocomplete="off"
                        value="<?php echo isset($_GET['keyword']) ? htmlspecialchars($_GET['keyword']) : ''; ?>">

                    <div id="search-results" class="position-absolute bg-white w-100 shadow-sm rounded-bottom d-none"
                        style="z-index: 1050; top: 100%; max-height: 400px; overflow-y: auto;">
                    </div>
                </div>
            </form>

            <div class="d-flex align-items-center gap-2">
                <a href="index.php?page=cart" class="btn cart-btn-mini">
                    <div class="cart-icon-wrapper">
                        <i class="bi bi-cart3"></i>
                        <span class="cart-badge" id="cart-count">
                            <?php echo $total_cart_items; ?>
                        </span>
                    </div>
                </a>

                <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="dropdown">
                        <a href="#" class="auth-btn-modern dropdown-toggle" data-bs-toggle="dropdown">
                            <div class="auth-icon-wrapper">
                                <i class="bi bi-person-check-fill"></i>
                            </div>
                            <span class="auth-text"><?php echo $_SESSION['user_name']; ?></span>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="profile.php">Hồ sơ cá nhân</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <a href="functions/user/authentication/logout.php" class="dropdown-item text-danger">Đăng
                                    xuất</a>
                            </li>
                        </ul>
                    </div>
                    <?php
                else: ?>
                    <a href="users_area/authentication/user_login.php" class="auth-btn-modern">
                        <div class="auth-icon-wrapper">
                            <i class="bi bi-person-circle"></i>
                        </div>
                        <span class="auth-text">Đăng nhập</span>
                    </a>
                    <?php
                endif; ?>
            </div>
        </div>
    </div>
</nav>
<style>
    #search-results {
        border: 1px solid #ddd;
        border-top: none;
        display: none;
    }

    .search-item {
        display: flex;
        align-items: center;
        padding: 10px;
        border-bottom: 1px solid #f1f1f1;
        text-decoration: none;
        color: #333;
        transition: background 0.2s;
    }

    .search-item:hover {
        background-color: #f8f9fa;
        color: #000;
    }

    .search-item img {
        width: 40px;
        height: 40px;
        object-fit: cover;
        margin-right: 15px;
        border-radius: 4px;
    }

    .search-item .item-info {
        flex-grow: 1;
    }

    .search-item .item-name {
        font-size: 14px;
        font-weight: 600;
        margin-bottom: 0;
    }

    .search-item .item-price {
        font-size: 13px;
        color: #dc3545;
    }
</style>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function () {
        const searchInput = $('#search-input');
        const searchResults = $('#search-results');

        searchInput.on('keyup', function () {
            let keyword = $(this).val().trim();

            if (keyword.length > 0) {
                $.ajax({
                    // Đường dẫn tới file controller đã viết ở bước trước
                    url: 'functions/user/search-controller.php',
                    type: 'GET',
                    data: {
                        action: 'live',
                        keyword: keyword
                    },
                    dataType: 'json',
                    success: function (data) {
                        searchResults.empty();

                        if (data.length > 0) {
                            searchResults.removeClass('d-none').show();

                            $.each(data, function (index, product) {
                                // Render từng item kết quả
                                let html = `
                                <a href="index.php?page=product-detail&slug=${product.slug}" class="search-item">
                                    <img src="admin/admin_images/${product.thumbnail}" alt="${product.name}">
                                    <div class="item-info">
                                        <p class="item-name">${product.name}</p>
                                        <span class="item-price">${new Intl.NumberFormat('vi-VN').format(product.price)}đ</span>
                                    </div>
                                </a>
                            `;
                                searchResults.append(html);
                            });
                        } else {
                            searchResults.append('<div class="p-3 text-muted small">Không tìm thấy sản phẩm phù hợp.</div>');
                            searchResults.removeClass('d-none').show();
                        }
                    }
                });
            } else {
                searchResults.hide().addClass('d-none');
            }
        });

        // Đóng kết quả khi click ra ngoài
        $(document).on('click', function (e) {
            if (!$(e.target).closest('#search-input, #search-results').length) {
                searchResults.hide().addClass('d-none');
            }
        });
    });

    function showToast(message) {
        // Tạo một thông báo đơn giản nếu chưa có giao diện Toast
        const toast = document.createElement('div');
        toast.style.cssText = `
        position: fixed; top: 20px; right: 20px; 
        background: #28a745; color: white; 
        padding: 10px 20px; border-radius: 5px; 
        z-index: 10000; box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    `;
        toast.textContent = message;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 3000);
    }
</script>