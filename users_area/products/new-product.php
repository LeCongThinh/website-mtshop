<div class="bg-white p-4 rounded shadow-sm mb-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Sản phẩm mới</h4>
        <a href="index.php?page=all-products" class="text-primary fst-italic text-decoration-none">Xem tất cả →</a>
    </div>
    
    <div class="product-scroll d-flex">
        <?php if (!empty($products)): ?>
            <?php foreach ($products as $product): ?>
                <div class="product-item">
                    <div class="card product-card border h-100 d-flex flex-column">
                        <a href="index.php?page=product-detail&slug=<?php echo $product['slug']; ?>" class="text-decoration-none text-black">
                            <div class="position-relative product-img-container overflow-hidden">
                                <?php 
                                    // Xử lý đường dẫn ảnh (thay asset bằng path thực tế)
                                    $image_path = !empty($product['thumbnail']) ? 'admin/admin_images/' . $product['thumbnail'] : 'admin/admin_images/post_thumbnails/undefined.png';
                                ?>
                                <img class="card-img-top p-2 product-img-hover"
                                     src="<?php echo $image_path; ?>"
                                     style="height:180px; object-fit:contain;" 
                                     alt="<?php echo htmlspecialchars($product['name']); ?>">

                                <?php if ($product['sale_price'] > 0): ?>
                                    <span class="position-absolute top-0 start-0 badge bg-danger m-2">Giảm giá</span>
                                <?php endif; ?>
                            </div>

                            <div class="card-body p-3 text-start d-flex flex-column flex-grow-1">
                                <h6 class="fw-bold mb-2">
                                    <?php 
                                        // Thay thế Str::limit của Laravel
                                        $name = strip_tags($product['name']);
                                        echo (mb_strlen($name) > 37) ? mb_substr($name, 0, 37) . '...' : $name;
                                    ?>
                                </h6>
                                
                                <div class="mt-auto mt-3">
                                    <?php if ($product['sale_price'] > 0): ?>
                                        <div class="text-danger fw-bold">
                                            <?php echo number_format($product['sale_price'], 0, ',', '.'); ?> đ
                                        </div>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="text-muted small text-decoration-line-through">
                                                <?php echo number_format($product['price'], 0, ',', '.'); ?> đ
                                            </div>
                                            <div class="badge border border-danger text-danger fw-bold">
                                                -<?php echo round((($product['price'] - $product['sale_price']) / $product['price']) * 100); ?>%
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <div class="text-danger fw-bold">
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
            <p class="text-muted p-3">Đang cập nhật sản phẩm...</p>
        <?php endif; ?>
    </div>
</div>