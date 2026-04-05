<?php
// Dữ liệu được truyền từ index.php thông qua biến $data
$products    = $data['products'] ?? [];
$currentPage = $data['current_page'] ?? 1;
$totalPages  = $data['total_pages'] ?? 0;
?>

<section class="py-4" style="background-color:#e9ecef;">
    <div class="container">
        <ul class="breadcrumb ms-5">
            <li class="breadcrumb-item">
                <a href="index.php?page=home" class="text-decoration-none fw-semibold">
                    <i class="bi bi-house-door-fill me-1"></i>Trang chủ
                </a>
            </li>
            <li class="breadcrumb-item active">PC bán chạy nhất</li>
        </ul>

        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4">
                <div class="border-start border-4 border-primary ps-3">
                    <h4 class="fw-bolder mb-1 text-uppercase">Danh sách PC bán chạy</h4>
                    <p class="text-muted mb-0 small">Khám phá những thiết bị công nghệ mới nhất tại MTShop</p>
                </div>
            </div>
        </div>

        <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-5 g-3">
            <?php if (!empty($products)): ?>
                <?php foreach($products as $product): ?>
                    <div class="col">
                        <div class="card product-card border-0 shadow-sm h-100 d-flex flex-column transition hover-up">
                            <a href="index.php?page=product-detail&slug=<?php echo $product['slug']; ?>" 
                               class="text-decoration-none text-black">
                                
                                <div class="position-relative product-img-container overflow-hidden bg-white">
                                    <img class="card-img-top p-2 product-img-hover"
                                         src="<?php echo $product['thumbnail'] ? 'storage/'.$product['thumbnail'] : 'assets/images/avatar/undefined.png'; ?>"
                                         style="height:180px; object-fit:contain;" 
                                         alt="<?php echo htmlspecialchars($product['name']); ?>">

                                    <?php if($product['sale_price'] > 0): ?>
                                        <span class="position-absolute top-0 start-0 badge bg-danger m-2">Giảm giá</span>
                                    <?php endif; ?>
                                </div>

                                <div class="card-body p-3 text-start d-flex flex-column flex-grow-1">
                                    <h6 class="fw-bold mb-2 text-dark line-clamp-2" style="min-height: 38px;">
                                        <?php 
                                            $name = strip_tags($product['name']);
                                            echo (mb_strlen($name) > 37) ? mb_substr($name, 0, 37) . '...' : $name;
                                        ?>
                                    </h6>

                                    <div class="mt-auto pt-2">
                                        <?php if($product['sale_price'] > 0): ?>
                                            <div class="text-danger fw-bold fs-5">
                                                <?php echo number_format($product['sale_price'], 0, ',', '.'); ?> đ
                                            </div>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="text-muted small text-decoration-line-through">
                                                    <?php echo number_format($product['price'], 0, ',', '.'); ?> đ
                                                </div>
                                                <div class="badge border border-danger text-danger fw-bold" style="font-size: 0.7rem;">
                                                    -<?php echo round((($product['price'] - $product['sale_price']) / $product['price']) * 100); ?>%
                                                </div>
                                            </div>
                                        <?php else: ?>
                                            <div class="text-danger fw-bold fs-5">
                                                <?php echo number_format($product['price'], 0, ',', '.'); ?> đ
                                            </div>
                                            <div class="small opacity-0">&nbsp;</div>
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
                <div class="col-12 text-center py-5">
                    <img src="assets/images/no-product.png" width="150" class="mb-3 opacity-50">
                    <p class="text-muted">Hiện chưa có sản phẩm nào.</p>
                </div>
            <?php endif; ?>
        </div>

        <?php if ($totalPages > 1): ?>
            <nav aria-label="Page navigation" class="d-flex justify-content-center mt-5 mb-4">
                <div class="bg-white shadow-sm p-1 p-md-2 rounded-pill d-inline-flex">
                    <ul class="pagination mb-0 border-0 d-flex flex-nowrap align-items-center">
                        
                        <li class="page-item <?php echo ($currentPage <= 1) ? 'disabled' : ''; ?>">
                            <a class="page-link border-0 rounded-circle mx-1 d-flex align-items-center justify-content-center custom-page-link"
                                href="<?php echo ($currentPage <= 1) ? '#' : 'index.php?page=hot-pc&p=' . ($currentPage - 1); ?>">
                                <i class="bi bi-chevron-left"></i>
                            </a>
                        </li>

                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item <?php echo ($i == $currentPage) ? 'active' : ''; ?>">
                                <a class="page-link border-0 rounded-circle mx-1 d-flex align-items-center justify-content-center custom-page-link"
                                    href="index.php?page=hot-pc&p=<?php echo $i; ?>">
                                    <?php echo $i; ?>
                                </a>
                            </li>
                        <?php endfor; ?>

                        <li class="page-item <?php echo ($currentPage >= $totalPages) ? 'disabled' : ''; ?>">
                            <a class="page-link border-0 rounded-circle mx-1 d-flex align-items-center justify-content-center custom-page-link"
                                href="<?php echo ($currentPage >= $totalPages) ? '#' : 'index.php?page=hot-pc&p=' . ($currentPage + 1); ?>">
                                <i class="bi bi-chevron-right"></i>
                            </a>
                        </li>

                    </ul>
                </div>
            </nav>
        <?php endif; ?>
    </div>
</section>