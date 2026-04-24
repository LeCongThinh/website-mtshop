<section class="py-4" style="background-color: #e9ecef; min-height: 80vh;">
    <div class="container">
        <h3 class="fw-bold mb-4 text-secondary"><i class="bi bi-bag-check-fill me-3"></i>Đơn hàng của tôi</h3>
        <?php if (empty($orders)): ?>
            <div class="text-center py-5 bg-white rounded shadow-sm">
                <img src="assets/images/empty-cart.png" alt="No orders" style="width: 150px;" class="mb-3">
                <p class="text-muted">Bạn chưa có đơn hàng nào.</p>
                <a href="index.php" class="btn btn-primary mt-3">Tiếp tục mua sắm</a>
            </div>
        <?php else: ?>
            <div class="table-responsive bg-white p-3 rounded shadow-sm">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Mã đơn hàng</th>
                            <th>Ngày đặt</th>
                            <th>Tổng tiền</th>
                            <th>Trạng thái thanh toán</th>
                            <th>Trạng thái đơn hàng</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $item): ?>
                            <tr>
                                <td class="fw-bold text-primary">#<?= $item['order_code'] ?></td>
                                <td><?= date('d/m/Y', strtotime($item['created_at'])) ?></td>
                                <td class="fw-bold text-danger"><?= number_format($item['total_amount'], 0, ',', '.') ?>đ</td>
                                <td>
                                    <?php $p_badge = getPaymentStatusBadge($item['payment_status']); ?>
                                    <span class="badge <?= $p_badge['class'] ?> fw-bold">
                                        <?= $p_badge['text'] ?>
                                    </span>
                                </td>

                                <td>
                                    <?php $o_badge = getOrderStatusBadge($item['status']); ?>
                                    <span class="badge <?= $o_badge['class'] ?>" style="font-size: 0.75rem; padding: 5px 10px;">
                                        <?= $o_badge['text'] ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="index.php?page=order-detail&code=<?= $item['order_code'] ?>"
                                        class="btn btn-sm btn-outline-primary rounded-3 px-3" title="Xem chi tiết đơn hàng">
                                        <i class="bi bi-eye-fill me-1"></i> Xem đơn hàng
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <?php if ($total_pages > 1): ?>
                    <nav aria-label="Order pagination" class="mt-4">
                        <ul class="pagination justify-content-center">
                            <li class="page-item <?= ($current_page <= 1) ? 'disabled' : '' ?>">
                                <a class="page-link shadow-sm" href="index.php?page=my-orders&p=<?= $current_page - 1 ?>" aria-label="Previous">
                                    <i class="bi bi-chevron-left"></i>
                                </a>
                            </li>

                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                <li class="page-item <?= ($i == $current_page) ? 'active' : '' ?>">
                                    <a class="page-link shadow-sm" href="index.php?page=my-orders&p=<?= $i ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>

                            <li class="page-item <?= ($current_page >= $total_pages) ? 'disabled' : '' ?>">
                                <a class="page-link shadow-sm" href="index.php?page=my-orders&p=<?= $current_page + 1 ?>" aria-label="Next">
                                    <i class="bi bi-chevron-right"></i>
                                </a>
                            </li>
                        </ul>
                    </nav>
                <?php endif; ?>

            </div>
        <?php endif; ?>
    </div>
</section>