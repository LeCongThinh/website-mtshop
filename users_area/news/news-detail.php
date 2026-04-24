<section class="py-4" style="background-color:#e9ecef;">
    <div class="container">
        <ul class="breadcrumb ms-5">
            <li class="breadcrumb-item">
                <a href="index.php" class="text-decoration-none fw-semibold">
                    <i class="bi bi-house-door-fill me-1"></i>Trang chủ
                </a>
            </li>
            <li class="breadcrumb-item">
                <a href="index.php?page=all-news" class="text-decoration-none fw-semibold">Tin công nghệ</a>
            </li>
            <li class="breadcrumb-item active"><?php echo htmlspecialchars($post['title']); ?></li>
        </ul>

        <div class="row justify-content-center">
            <div class="col-lg-10 col-md-10">
                <div class="card stretch stretch-full mb-3 shadow-sm">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12 mt-3 text-center">
                                <h4 class="fw-bold mb-2"><?php echo htmlspecialchars($post['title']); ?></h4>
                            </div>
                            
                            <div class="d-flex align-items-center text-muted small mb-4">
                                <span class="me-3">
                                    <i class="bi bi-calendar3 me-1"></i>
                                    <?php echo $post['day_name'] . ', ' . $post['formatted_date']; ?>
                                </span>
                                <span class="d-flex align-items-center">
                                    <i class="bi bi-person-fill me-1"></i>
                                    <span class="fw-semibold text-dark">
                                        <?php echo htmlspecialchars($post['user_name']); ?>
                                    </span>
                                </span>
                            </div>

                            <div class="post-content border-top pt-4">
                                <?php echo $post['content']; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>