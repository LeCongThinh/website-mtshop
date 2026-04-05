<div class="bg-white p-4 rounded shadow-sm mb-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Tin công nghệ</h4>
        <a href="index.php?page=all-news" class="text-primary fst-italic text-decoration-none">Xem tất cả →</a>
    </div>
    <div class="product-scroll d-flex">
        <?php foreach ($posts as $post): ?>
            <div class="product-item">
                <div class="card product-card border">
                    <a href="index.php?page=news-detail&slug=<?= htmlspecialchars($post['slug']) ?>">
                        <div class="position-relative">
                            <img class="card-img-top p-2"
                                src="<?= $post['thumbnail'] ? 'admin/admin_images/' . htmlspecialchars($post['thumbnail']) : 'admin/admin_images/post_thumbnails/undefined.png' ?>"
                                style="height:180px; object-fit:cover;" alt="">
                        </div>
                        <div class="card-body p-3 text-start">
                            <h6 class="mb-2">
                                <a href="index.php?page=news-detail&slug=<?= htmlspecialchars($post['slug']) ?>" 
                                   class="mb-2 text-decoration-none text-black">
                                    <?= htmlspecialchars($post['title']) ?>
                                </a>
                            </h6>
                        </div>
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>