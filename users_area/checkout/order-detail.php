<div class="container py-5">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php?page=my-orders">Đơn hàng của tôi</a></li>
            <li class="breadcrumb-item active">Chi tiết #<?= $order['order_code'] ?></li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold">Sản phẩm đã đặt</h5>
                </div>
                <div class="card-body">
                    <?php foreach ($order_items as $item): ?>
                        <div class="d-flex align-items-center mb-3 pb-3 border-bottom">
                            <img src="<?= URL ?>admin/admin_images/<?= $item['product_thumbnail'] ?>"
                                style="width:70px; height:70px; object-fit:cover;"
                                class="rounded-3 border p-1 bg-white shadow-sm"
                                alt="<?= htmlspecialchars($item['product_name']) ?>">

                            <div class="ms-3 flex-grow-1">
                                <h6 class="mb-1 fw-bold"><?= $item['product_name'] ?></h6>
                                <small class="text-muted">Số lượng: <?= $item['quantity'] ?></small>
                            </div>

                            <div class="text-end">
                                <span class="fw-bold text-danger"><?= number_format($item['price'] * $item['quantity'], 0, ',', '.') ?>đ</span>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <div class="text-end pt-2">
                        <h5>Tổng thanh toán: <span class="text-danger fw-bold"><?= number_format($order['total_amount'], 0, ',', '.') ?>đ</span></h5>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold">Địa chỉ nhận hàng</h5>
                </div>
                <div class="card-body">
                    <p class="mb-1"><strong>Người nhận:</strong> <?= htmlspecialchars($order['receiver_name']) ?></p>
                    <p class="mb-1"><strong>Điện thoại:</strong> <?= htmlspecialchars($order['receiver_phone']) ?></p>
                    <p class="mb-1"><strong>Địa chỉ:</strong> <?= htmlspecialchars($order['receiver_address']) ?></p>
                    <hr>
                    <p class="mb-2"><strong>Phương thức:</strong> <?= strtoupper($order['payment_method']) ?></p>

                    <p class="mb-2"><strong>Thanh toán:</strong>
                        <?php $p_ui = getPaymentStatusBadge($order['payment_status']); ?>
                        <span class="badge <?= $p_ui['class'] ?>"><?= $p_ui['text'] ?></span>
                    </p>

                    <p class="mb-0"><strong>Trạng thái:</strong>
                        <?php $s_ui = getOrderStatusBadge($order['status']); ?>
                        <span class="badge <?= $s_ui['class'] ?> px-3 py-2"><?= $s_ui['text'] ?></span>
                    </p>
                </div>
            </div>
            <div class="d-grid mt-3">
                <a href="index.php?page=my-orders" class="btn btn-outline-secondary">Quay lại danh sách</a>
            </div>
        </div>
    </div>
</div>