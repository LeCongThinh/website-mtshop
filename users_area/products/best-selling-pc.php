<div class="bg-white p-4 rounded shadow-sm mb-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>PC bán chạy</h4>
        <a href="index.php?page=all-best-selling-pc" class="text-primary fst-italic text-decoration-none">Xem tất cả →</a>
    </div>

    <div class="product-scroll d-flex overflow-auto pb-2" style="gap: 15px;">
        <?php
        // Lấy dữ liệu từ biến $data đã truyền từ route home
        $bestSellingPCs = $data['best_selling_pcs'] ?? [];
        ?>

        <?php if (!empty($bestSellingPCs)): ?>
            <?php foreach ($bestSellingPCs as $pc): ?>
                <div class="product-item" style="min-width: 230px; max-width: 230px;">
                    <div class="card product-card border h-100 d-flex flex-column shadow-sm transition hover-up">
                        <a href="index.php?page=product-detail&slug=<?php echo $pc['slug']; ?>"
                            class="text-decoration-none text-black">
                            <div class="position-relative product-img-container overflow-hidden bg-white">
                                <img class="card-img-top p-2 product-img-hover"
                                    src="<?php echo $pc['thumbnail'] ? 'admin/admin_images/' . $pc['thumbnail'] : 'admin/admin_images/post_thumbnails/undefined.png'; ?>"
                                    style="height:180px; object-fit:contain;"
                                    alt="<?php echo htmlspecialchars($pc['name']); ?>">

                                <?php if ($pc['sale_price'] > 0): ?>
                                    <span class="position-absolute top-0 start-0 badge bg-danger m-2">Giảm giá</span>
                                <?php endif; ?>
                            </div>

                            <div class="card-body p-3 text-start d-flex flex-column flex-grow-1">
                                <h6 class="fw-bold mb-2 text-dark line-clamp-2" style="min-height: 38px;">
                                    <?php
                                    $name = strip_tags($pc['name']);
                                    echo (mb_strlen($name) > 37) ? mb_substr($name, 0, 37) . '...' : $name;
                                    ?>
                                </h6>

                                <div class="mt-auto pt-2">
                                    <?php if ($pc['sale_price'] > 0): ?>
                                        <div class="text-danger fw-bold fs-5">
                                            <?php echo number_format($pc['sale_price'], 0, ',', '.'); ?> đ
                                        </div>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="text-muted small text-decoration-line-through">
                                                <?php echo number_format($pc['price'], 0, ',', '.'); ?> đ
                                            </div>
                                            <div class="badge border border-danger text-danger fw-bold" style="font-size: 0.7rem;">
                                                -<?php echo round((($pc['price'] - $pc['sale_price']) / $pc['price']) * 100); ?>%
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <div class="text-danger fw-bold fs-5">
                                            <?php echo number_format($pc['price'], 0, ',', '.'); ?> đ
                                        </div>
                                        <div class="small opacity-0">&nbsp;</div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </a>

                        <div class="card-footer p-2 border-0 bg-transparent text-center mt-auto">
                            <button class="btn btn-outline-primary btn-sm w-100 btn-add-cart"
                                data-id="<?php echo $pc['id']; ?>">
                                <i class="bi bi-cart-plus me-1"></i> Thêm vào giỏ hàng
                            </button>

                            <div class="d-flex align-items-center mt-2 mb-1 justify-content-between">
                                <span class="text-warning">
                                    <span class="fw-semibold small">4.8</span>
                                    <i class="bi bi-star-fill"></i>
                                    <span class="text-muted small">(120 đánh giá)</span>
                                </span>

                                <span class="text-muted small">
                                    <i class="bi bi-fire text-danger me-1"></i>Đã bán
                                    <?php echo number_format($pc['total_sold'] ?? 0); ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="p-3 text-muted">Đang cập nhật sản phẩm bán chạy...</p>
        <?php endif; ?>
    </div>
</div>