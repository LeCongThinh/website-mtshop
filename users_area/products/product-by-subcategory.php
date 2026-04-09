<?php
$sub = $data['subcategory'];
$products = $data['products'];
?>

<section class="py-4" style="background-color:#e9ecef;">
    <div class="container">
        <ul class="breadcrumb ms-5">
            <li class="breadcrumb-item">
                <a href="index.php?page=home" class="text-decoration-none fw-semibold">
                    <i class="bi bi-house-door-fill me-1"></i>Trang chủ
                </a>
            </li>
            <li class="breadcrumb-item">
                <a href="index.php?page=category&slug=<?php echo $sub['parent_slug']; ?>" class="text-decoration-none fw-semibold">
                    <?php echo htmlspecialchars($sub['parent_name']); ?>
                </a>
            </li>
            <li class="breadcrumb-item active"><?php echo htmlspecialchars($sub['name']); ?></li>
        </ul>

        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div class="border-start border-4 border-primary ps-3">
                    <h4 class="fw-bolder mb-0 text-uppercase"><?php echo htmlspecialchars($sub['name']); ?></h4>
                    <p class="text-muted mb-0 small">Khám phá <?php echo $data['total_rows']; ?> sản phẩm công nghệ mới nhất</p>
                </div>
            </div>
        </div>

        <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-5 g-3">
            <?php if (!empty($products)): ?>
                <?php foreach($products as $product): ?>
                    <div class="col">
                        <div class="card product-card border-0 shadow-sm h-100 d-flex flex-column transition hover-up">
                            <a href="index.php?page=product-detail&slug=<?php echo $product['slug']; ?>" class="text-decoration-none text-black">
                                <div class="position-relative text-center bg-white p-2">
                                    <img src="<?php echo $product['thumbnail'] ? 'admin/admin_images/'.$product['thumbnail'] : 'assets/images/avatar/undefined.png'; ?>" 
                                         style="height:180px; object-fit:contain;" class="img-fluid product-img-hover" alt="">
                                    
                                    <?php if($product['sale_price'] > 0): ?>
                                        <span class="position-absolute top-0 start-0 badge bg-danger m-2">Giảm giá</span>
                                    <?php endif; ?>
                                </div>

                                <div class="card-body p-3 text-start d-flex flex-column flex-grow-1">
                                    <h6 class="fw-bold mb-2 text-dark line-clamp-2" style="min-height: 38px;">
                                        <?php echo htmlspecialchars($product['name']); ?>
                                    </h6>

                                    <div class="mt-auto">
                                        <?php if($product['sale_price'] > 0): ?>
                                            <div class="text-danger fw-bold fs-5"><?php echo number_format($product['sale_price'], 0, ',', '.'); ?> đ</div>
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="text-muted small text-decoration-line-through"><?php echo number_format($product['price'], 0, ',', '.'); ?> đ</span>
                                                <span class="badge border border-danger text-danger fw-bold" style="font-size: 0.7rem;">
                                                    -<?php echo round((($product['price'] - $product['sale_price']) / $product['price']) * 100); ?>%
                                                </span>
                                            </div>
                                        <?php else: ?>
                                            <div class="text-danger fw-bold fs-5"><?php echo number_format($product['price'], 0, ',', '.'); ?> đ</div>
                                            <div class="small">&nbsp;</div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </a>

                            <div class="card-footer p-2 border-0 bg-transparent text-center mt-auto">
                                <button class="btn btn-outline-primary btn-sm w-100 btn-add-cart" data-id="<?php echo $product['id']; ?>">
                                    <i class="bi bi-cart-plus me-1"></i> Thêm vào giỏ hàng
                                </button>
                                <div class="d-flex align-items-center mt-2 mb-1 justify-content-start">
                                    <span class="text-warning me-2">
                                        <span class="fw-semibold small">4.8</span> <i class="bi bi-star-fill"></i>
                                    </span>
                                    <span class="text-muted small">(120 đánh giá)</span>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <img src="assets/images/no-product.png" width="150" class="mb-3 opacity-50">
                    <p class="text-muted">Hiện chưa có sản phẩm nào trong danh mục này.</p>
                </div>
            <?php endif; ?>
        </div>

        <?php if ($data['total_pages'] > 1): ?>
        <nav class="d-flex justify-content-center mt-5 mb-4">
            <ul class="pagination shadow-sm p-2 rounded-pill bg-white">
                <?php for($i = 1; $i <= $data['total_pages']; $i++): ?>
                    <li class="page-item <?php echo ($i == $data['current_page']) ? 'active' : ''; ?>">
                        <a class="page-link border-0 rounded-circle mx-1" 
                           href="index.php?page=subcategory&slug=<?php echo $sub['slug']; ?>&p=<?php echo $i; ?>">
                           <?php echo $i; ?>
                        </a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
        <?php endif; ?>
    </div>
</section>