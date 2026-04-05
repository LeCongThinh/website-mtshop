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
                                        <?php endif; ?>
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
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php endif; ?>
                                </li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                </li>
            </ul>

            <form action="search.php" method="GET"
                class="d-flex flex-grow-1 justify-content-center my-3 my-lg-0 px-lg-5 position-relative">
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
                <a href="cart.php" class="btn cart-btn-mini">
                    <div class="cart-icon-wrapper">
                        <i class="bi bi-cart3"></i>
                        <span class="cart-badge" id="cart-count">
                            <?php echo isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0; ?>
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
                                <a href="logout.php" class="dropdown-item text-danger">Đăng xuất</a>
                            </li>
                        </ul>
                    </div>
                <?php else: ?>
                    <a href="users_area/authentication/user_login.php" class="auth-btn-modern">
                        <div class="auth-icon-wrapper">
                            <i class="bi bi-person-circle"></i>
                        </div>
                        <span class="auth-text">Đăng nhập</span>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>