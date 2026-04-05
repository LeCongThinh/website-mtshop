<?php
// Giả sử $data được lấy từ hàm getCategoryData trong index.php
$category = $data['category'];
$children = $data['children'];
$brands = $data['brands'];
$brand = $data['current_brand'];
$products = $data['products'];
$totalPages = $data['total_pages'];
$currentPage = $data['current_page'];
$pageName = $_GET['page'];
$slug = $category['slug'] ?? '';
$brandParam = isset($brand) ? '&brand=' . $brand['slug'] : '';
?>

<section class="py-4" style="background-color:#e9ecef;">
    <div class="container">
        <ul class="breadcrumb ms-5">
            <li class="breadcrumb-item"><a href="index.php?page=home" class="text-decoration-none fw-semibold">
                    <i class="bi bi-house-door-fill me-1"></i>Trang chủ</a>
            </li>
            <li class="breadcrumb-item active"><?php echo htmlspecialchars($category['name']); ?></li>
        </ul>

        <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden bg-white">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 border-bottom pb-2 mb-3">
                    <div class="border-start border-4 border-primary ps-3">
                        <h4 class="fw-bolder mb-1 text-uppercase text-dark">
                            <?php echo htmlspecialchars($category['name']); ?>
                        </h4>
                        <p class="text-muted mb-0 small">
                            <i class="bi bi-tag-fill me-1"></i> Khám phá <?php echo $data['total_rows']; ?> sản phẩm
                            công nghệ mới nhất
                        </p>
                    </div>
                </div>

                <?php if (count($children) > 0): ?>
                    <div class="d-flex align-items-center gap-3 mb-3 flex-wrap">
                        <span class="text-secondary small fw-bold text-uppercase" style="letter-spacing: 0.5px;">Dòng
                            máy:</span>
                        <div class="d-flex gap-2 flex-wrap">
                            <?php foreach ($children as $child): ?>
                                <a href="index.php?page=subcategory&slug=<?php echo $child['slug']; ?>"
                                    class="btn btn-outline-primary btn-sm px-3 fw-semibold shadow-sm rounded-3">
                                    <?php echo htmlspecialchars($child['name']); ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (count($brands) > 0): ?>
                    <div class="d-flex align-items-center gap-3">
                        <span class="text-secondary small fw-bold text-uppercase"
                            style="min-width: 70px; font-size: 0.75rem;">Phân loại:</span>
                        <div class="scroll-container overflow-auto">
                            <div class="d-flex gap-2 flex-nowrap">
                                <a href="index.php?page=category&slug=<?php echo $category['slug']; ?>"
                                    class="btn btn-sm <?php echo !isset($brand) ? 'btn-primary' : 'btn-light border'; ?> rounded-pill px-3">
                                    Tất cả
                                </a>
                                <?php foreach ($brands as $item): ?>
                                    <a href="index.php?page=category&slug=<?php echo $category['slug']; ?>&brand=<?php echo $item['slug']; ?>"
                                        class="btn btn-sm <?php echo (isset($brand) && $brand['id'] == $item['id']) ? 'btn-primary' : 'btn-light border'; ?> rounded-pill px-3">
                                        <?php echo htmlspecialchars($item['name']); ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-5 g-3">
            <?php if (!empty($products)): ?>
                <?php foreach ($products as $product): ?>
                    <div class="col">
                        <div class="card product-card border-0 shadow-sm h-100 d-flex flex-column transition hover-up">
                            <a href="index.php?page=product-detail&slug=<?php echo $product['slug']; ?>"
                                class="text-decoration-none text-black">
                                <div class="position-relative text-center bg-white p-2">
                                    <img src="<?php echo $product['thumbnail'] ? 'admin/admin_images/' . $product['thumbnail'] : 'assets/images/avatar/undefined.png'; ?>"
                                        style="height:180px; object-fit:contain;" class="img-fluid" alt="">
                                    <?php if ($product['sale_price'] > 0): ?>
                                        <span class="position-absolute top-0 start-0 badge bg-danger m-2">Giảm giá</span>
                                    <?php endif; ?>
                                </div>

                                <div class="card-body p-3 text-start d-flex flex-column flex-grow-1">
                                    <h6 class="fw-bold mb-2 text-dark line-clamp-2" style="min-height: 38px;">
                                        <?php echo htmlspecialchars($product['name']); ?>
                                    </h6>
                                    <div class="mt-auto">
                                        <?php if ($product['sale_price'] > 0): ?>
                                            <div class="text-danger fw-bold fs-5">
                                                <?php echo number_format($product['sale_price'], 0, ',', '.'); ?> đ
                                            </div>
                                            <div class="d-flex align-items-center gap-2">
                                                <span
                                                    class="text-muted small text-decoration-line-through"><?php echo number_format($product['price'], 0, ',', '.'); ?>
                                                    đ</span>
                                                <span class="badge border border-danger text-danger" style="font-size: 0.7rem;">
                                                    -<?php echo round((($product['price'] - $product['sale_price']) / $product['price']) * 100); ?>%
                                                </span>
                                            </div>
                                        <?php else: ?>
                                            <div class="text-danger fw-bold fs-5">
                                                <?php echo number_format($product['price'], 0, ',', '.'); ?> đ
                                            </div>
                                            <div class="small">&nbsp;</div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </a>
                            <div class="card-footer p-2 border-0 bg-transparent">
                                <button class="btn btn-outline-primary btn-sm w-100 btn-add-cart"
                                    data-id="<?php echo $product['id']; ?>">
                                    <i class="bi bi-cart-plus"></i> Thêm vào giỏ
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <p class="text-muted">Hiện chưa có sản phẩm nào trong danh mục này.</p>
                </div>
            <?php endif; ?>
        </div>

        <?php if ($totalPages > 1): ?>
            <nav aria-label="Page navigation" class="d-flex justify-content-center mt-5 mb-4">
                <div class="bg-white shadow-sm p-1 p-md-2 rounded-pill d-inline-flex">
                    <ul class="pagination mb-0 border-0 d-flex flex-nowrap align-items-center">

                        <li class="page-item <?php echo ($currentPage <= 1) ? 'disabled' : ''; ?>">
                            <a class="page-link border-0 rounded-circle mx-1 d-flex align-items-center justify-content-center custom-page-link"
                                href="<?php echo ($currentPage <= 1) ? '#' : "index.php?page=$pageName&slug=$slug&p=" . ($currentPage - 1) . $brandParam; ?>"
                                aria-label="Previous">
                                <i class="bi bi-chevron-left"></i>
                            </a>
                        </li>

                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item <?php echo ($i == $currentPage) ? 'active' : ''; ?>">
                                <a class="page-link border-0 rounded-circle mx-1 d-flex align-items-center justify-content-center custom-page-link"
                                    href="index.php?page=<?php echo $pageName; ?>&slug=<?php echo $slug; ?>&p=<?php echo $i . $brandParam; ?>">
                                    <?php echo $i; ?>
                                </a>
                            </li>
                        <?php endfor; ?>

                        <li class="page-item <?php echo ($currentPage >= $totalPages) ? 'disabled' : ''; ?>">
                            <a class="page-link border-0 rounded-circle mx-1 d-flex align-items-center justify-content-center custom-page-link"
                                href="<?php echo ($currentPage >= $totalPages) ? '#' : "index.php?page=$pageName&slug=$slug&p=" . ($currentPage + 1) . $brandParam; ?>"
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