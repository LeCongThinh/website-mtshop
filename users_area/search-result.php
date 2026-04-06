<section class="py-4" style="background-color:#e9ecef; min-height: 80vh;">
    <div class="container">

        <?php if (!empty($keyword)): ?>
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <div class="border-start border-4 border-primary ps-3">
                        <h3 class="mb-1">Kết quả tìm kiếm cho: <span
                                class="text-primary">"<?php echo htmlspecialchars($keyword); ?>"</span></h3>
                        <p class="text-muted mb-0">Tìm thấy <?php echo count($products); ?> sản phẩm trên trang này</p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-5 g-3">
            <?php if (!empty($products)): ?>
                <?php foreach ($products as $product):
                    $p_id = $product['id'];
                    $p_name = $product['name'];
                    $p_slug = $product['slug'];
                    $p_price = $product['price'];
                    $p_sale = $product['sale_price'];
                    $p_image = $product['thumbnail'];

                    // Rút ngắn tên sản phẩm (tương đương Str::limit)
                    $display_name = (mb_strlen($p_name) > 37) ? mb_substr($p_name, 0, 37) . '...' : $p_name;
                    ?>
                    <div class="col">
                        <div class="card product-card border-0 shadow-sm h-100 d-flex flex-column transition hover-up">
                            <a href="index.php?page=product-detail&slug=<?php echo $p_slug; ?>"
                                class="text-decoration-none text-black">
                                <div class="position-relative product-img-container overflow-hidden bg-white">
                                    <img class="card-img-top p-2 product-img-hover"
                                        src="<?php echo !empty($p_image) ? 'admin/admin_images/' . $p_image : 'assets/images/avatar/undefined.png'; ?>"
                                        style="height:180px; object-fit:contain;"
                                        alt="<?php echo htmlspecialchars($p_name); ?>">

                                    <?php if ($p_sale > 0): ?>
                                        <span class="position-absolute top-0 start-0 badge bg-danger m-2">Giảm giá</span>
                                    <?php endif; ?>
                                </div>

                                <div class="card-body p-3 text-start d-flex flex-column flex-grow-1">
                                    <h6 class="fw-bold mb-2 text-dark line-clamp-2" style="min-height: 38px;">
                                        <?php echo htmlspecialchars($display_name); ?>
                                    </h6>

                                    <div class="mt-auto pt-2">
                                        <?php if ($p_sale > 0): ?>
                                            <div class="text-danger fw-bold fs-5">
                                                <?php echo number_format($p_sale, 0, ',', '.'); ?> đ
                                            </div>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="text-muted small text-decoration-line-through">
                                                    <?php echo number_format($p_price, 0, ',', '.'); ?> đ
                                                </div>
                                                <div class="badge border border-danger text-danger fw-bold"
                                                    style="font-size: 0.7rem;">
                                                    -<?php echo round((($p_price - $p_sale) / $p_price) * 100); ?>%
                                                </div>
                                            </div>
                                        <?php else: ?>
                                            <div class="text-danger fw-bold fs-5">
                                                <?php echo number_format($p_price, 0, ',', '.'); ?> đ
                                            </div>
                                            <div class="small opacity-0">&nbsp;</div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </a>

                            <div class="card-footer p-2 border-0 bg-transparent text-center mt-auto">
                                <button class="btn btn-outline-primary btn-sm w-100 btn-add-cart"
                                    data-id="<?php echo $p_id; ?>">
                                    <i class="bi bi-cart-plus me-1"></i> Thêm vào giỏ hàng
                                </button>
                                <div class="d-flex align-items-center mt-2 mb-1 justify-content-start">
                                    <span class="text-warning me-2">
                                        <span class="fw-semibold small">4.8</span>
                                        <i class="bi bi-star-fill"></i>
                                    </span>
                                    <span class="text-muted small">(120 đánh giá)</span>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>

            <?php else: ?>
                <div class="col-12 w-100">
                    <div class="card border-0 shadow-sm rounded-4 text-center py-5">
                        <div class="card-body">
                            <div class="mb-4">
                                <i class="bi bi-search-heart text-muted" style="font-size: 5rem; opacity: 0.3;"></i>
                            </div>

                            <?php if (empty($keyword)): ?>
                                <h4 class="fw-bold">Bạn chưa nhập từ khóa</h4>
                                <p class="text-muted">Vui lòng nhập tên sản phẩm cần tìm vào ô tìm kiếm phía trên.</p>
                            <?php else: ?>
                                <h4 class="fw-bold">Không tìm thấy kết quả</h4>
                                <p class="text-muted">Rất tiếc, chúng tôi không tìm thấy sản phẩm nào phù hợp với từ khóa
                                    <strong>"<?php echo htmlspecialchars($keyword); ?>"</strong>.
                                </p>
                            <?php endif; ?>

                            <div class="mt-4">
                                <a href="index.php" class="btn btn-primary px-4 rounded-pill">
                                    <i class="bi bi-house-door me-2"></i>Quay lại trang chủ
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <?php if ($total_pages > 1): ?>
            <nav aria-label="Page navigation" class="d-flex justify-content-center mt-5 mb-4">
                <div class="bg-white shadow-sm p-1 p-md-2 rounded-pill d-inline-flex">
                    <ul class="pagination mb-0 border-0 d-flex flex-nowrap align-items-center">

                        <li class="page-item <?php echo ($current_page <= 1) ? 'disabled' : ''; ?>">
                            <a class="page-link border-0 rounded-circle mx-1 d-flex align-items-center justify-content-center custom-page-link"
                                href="<?php echo ($current_page <= 1) ? '#' : 'index.php?page=search-result&keyword=' . urlencode($keyword) . '&p=' . ($current_page - 1); ?>"
                                aria-label="Previous">
                                <i class="bi bi-chevron-left"></i>
                            </a>
                        </li>

                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?php echo ($i == $current_page) ? 'active' : ''; ?>">
                                <a class="page-link border-0 rounded-circle mx-1 d-flex align-items-center justify-content-center custom-page-link"
                                    href="index.php?page=search-result&keyword=<?php echo urlencode($keyword); ?>&p=<?php echo $i; ?>">
                                    <?php echo $i; ?>
                                </a>
                            </li>
                        <?php endfor; ?>

                        <li class="page-item <?php echo ($current_page >= $total_pages) ? 'disabled' : ''; ?>">
                            <a class="page-link border-0 rounded-circle mx-1 d-flex align-items-center justify-content-center custom-page-link"
                                href="<?php echo ($current_page >= $total_pages) ? '#' : 'index.php?page=search-result&keyword=' . urlencode($keyword) . '&p=' . ($current_page + 1); ?>"
                                aria-label="Next">
                                <i class="bi bi-chevron-right"></i>
                            </a>
                        </li>

                    </ul>
                </div>
            </nav>
        <?php endif; ?>
    </div>
</section>