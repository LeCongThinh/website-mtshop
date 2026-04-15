<div class="container py-5">
    <h2 class="fw-bold mb-4"><i class="bi bi-bag-check-fill me-2 text-primary"></i>Đơn hàng của tôi</h2>

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
                        <th>Thanh toán</th>
                        <th>Trạng thái</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $item): ?>
                        <tr>
                            <td class="fw-bold text-primary">#<?= $item['order_code'] ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($item['created_at'])) ?></td>
                            <td class="fw-bold text-danger"><?= number_format($item['total_amount'], 0, ',', '.') ?>đ</td>
                            <td>
                                <?php if ($item['payment_status'] === 'paid'): ?>
                                    <span class="badge bg-success-subtle text-success border border-success">Đã thanh toán</span>
                                <?php else: ?>
                                    <span class="badge bg-warning-subtle text-warning border border-warning">Chờ thanh toán</span>
                                <?php endif; ?>
                            </td>
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
                          <td class="text-center">
    <a href="index.php?page=order-detail&code=<?= $item['order_code'] ?>" 
       class="btn btn-sm btn-outline-primary rounded-pill px-3" 
       title="Xem chi tiết đơn hàng">
        <i class="bi bi-eye-fill me-1"></i> Xem
    </a>
</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>