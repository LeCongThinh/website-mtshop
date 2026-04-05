<section class="py-4" style="background-color:#e9ecef;">
    <div class="container">
        <!-- Breadcrumb -->
        <ul class="breadcrumb ms-5">
            <li class="breadcrumb-item">
                <a href="index.php" class="text-decoration-none fw-semibold">
                    <i class="bi bi-house-door-fill me-1"></i>Trang chủ
                </a>
            </li>
            <li class="breadcrumb-item">Tất cả bài viết</li>
        </ul>

        <div class="card border-0 shadow-sm mb-3 rounded-4 overflow-hidden">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="border-start border-4 border-primary ps-3 py-1">
                        <h4 class="fw-bolder mb-1 text-dark text-uppercase">
                            Tin tức công nghệ mới nhất
                        </h4>
                        <p class="text-muted mb-0 small fw-medium">
                            Cập nhật những thông tin và thủ thuật mới nhất hàng ngày
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <?php if (!empty($posts)): ?>
                <?php foreach ($posts as $item): ?>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="card h-100 border-0 shadow-sm">
                            <a href="index.php?page=news-detail&slug=<?php echo $item['slug']; ?>">
                                <img src="admin/admin_images/<?= htmlspecialchars($item['thumbnail']) ?>"
                                    class="card-img-top img-fluid" alt="<?= htmlspecialchars($item['title']) ?>"
                                    style="height: 220px; object-fit: cover;">
                            </a>

                            <div class="card-body p-4">
                                <div class="d-flex align-items-center mb-2 text-muted small">
                                    <span class="me-3">
                                        <i class="bi bi-calendar3 me-1"></i>
                                        <?= date('d/m/Y', strtotime($item['created_at'])) ?>
                                    </span>
                                    <span>
                                        <i class="bi bi-person me-1"></i>
                                        <?= htmlspecialchars($item['user_name'] ?? 'Admin') ?>
                                    </span>
                                </div>

                                <h5 class="card-title mb-3">
                                    <a href="index.php?page=news-detail&slug=<?= htmlspecialchars($item['slug']) ?>"
                                        class="text-decoration-none text-dark fw-bold">
                                        <?= htmlspecialchars($item['title']) ?>
                                    </a>
                                </h5>

                                <p class="card-text text-muted small">
                                    <?php
                                    // 1. Loại bỏ thẻ HTML
                                    $content = strip_tags($item['content']);
                                    // 2. Chuyển các thực thể như &acirc; thành chữ â
                                    $content = html_entity_decode($content, ENT_QUOTES, 'UTF-8');
                                    // 3. Cắt chuỗi an toàn với mb_strimwidth (nhớ có 'UTF-8' ở cuối)
                                    echo htmlspecialchars(mb_strimwidth($content, 0, 120, '...', 'UTF-8'));
                                    ?>
                                </p>
                            </div>

                            <div class="card-footer bg-transparent border-0 p-4 pt-0">
                                <a href="index.php?page=news-detail&slug=<?= htmlspecialchars($item['slug']) ?>"
                                    class="btn btn-outline-primary btn-sm rounded-pill px-3">
                                    Đọc tiếp <i class="bi bi-arrow-right ms-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <p class="text-muted">Chưa có bài viết nào được đăng.</p>
                </div>
            <?php endif; ?>

            <!-- Phân trang -->
            <?php if ($totalPages > 1): ?>
                <nav aria-label="Page navigation" class="d-flex justify-content-center mt-5">
                    <ul class="pagination">
                        <?php if ($currentPage > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="index.php?page=all-news&p=<?= $currentPage - 1 ?>">«</a>
                            </li>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item <?= $i === $currentPage ? 'active' : '' ?>">
                                <a class="page-link" href="index.php?page=all-news&p=<?= $i ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>

                        <?php if ($currentPage < $totalPages): ?>
                            <li class="page-item">
                                <a class="page-link" href="index.php?page=all-news&p=<?= $currentPage + 1 ?>">»</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            <?php endif; ?>
        </div>
    </div>
</section>