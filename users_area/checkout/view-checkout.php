<section class="py-4" style="background-color:#e9ecef; min-height: 80vh;">
    <div class="container py-2">
        <form id="orderForm" action="index.php?page=process-checkout" method="POST">

            <div class="row g-4">

                <?php
                $alerts = ['error' => 'danger', 'success' => 'success', 'info' => 'info'];
                foreach ($alerts as $key => $type):
                    if (isset($_SESSION[$key])):
                        $message = $_SESSION[$key];
                        unset($_SESSION[$key]); // Xóa session sau khi lấy để không hiển thị lại
                        ?>
                        <script>
                            document.addEventListener("DOMContentLoaded", function () {
                                const message = <?php echo json_encode($message); ?>;
                                if (typeof showAlert === "function") {
                                    showAlert("mainAlert", message, "<?= $type ?>");
                                } else {
                                    console.error("Hệ thống bị gián đoạn");
                                }
                            });
                        </script>
                        <?php
                    endif;
                endforeach;
                ?>

                <div class="col-lg-7">
                    <div class="card checkout-card shadow-sm">
                        <div class="card-header bg-white py-3 border-0 mt-2">
                            <h5 class="mb-0 fw-bold">
                                <span class="step-badge">1</span>Thông tin vận chuyển
                            </h5>
                        </div>
                        <div class="card-body px-4 pb-4">
                            <div class="mb-2">
                                <label class="form-label fw-semibold small">Họ và tên người nhận</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i
                                            class="bi bi-person text-muted"></i></span>
                                    <input type="text" name="name" class="form-control border-start-0"
                                        value="<?= htmlspecialchars($user['name'] ?? '') ?>"
                                        placeholder="VD: Nguyễn Văn A">
                                </div>
                            </div>

                            <div class="row mb-2">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small">Số điện thoại</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i
                                                class="bi bi-telephone text-muted"></i></span>
                                        <input type="text" name="phone" class="form-control border-start-0"
                                            value="<?= htmlspecialchars($user['phone'] ?? '') ?>" required
                                            placeholder="09xx xxx xxx">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small">Email người dùng</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i
                                                class="bi bi-envelope text-muted"></i></span>
                                        <input type="email" class="form-control border-start-0 bg-light"
                                            value="<?= htmlspecialchars($user['email'] ?? '') ?>" readonly disabled>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-2">
                                <label class="form-label fw-semibold small">Địa chỉ nhận hàng</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i
                                            class="bi bi-geo-alt text-muted"></i></span>
                                    <textarea name="address" rows="3" class="form-control border-start-0"
                                        placeholder="Số nhà, tên đường, Phường/Xã, Quận/Huyện..."><?= htmlspecialchars($user['address'] ?? '') ?></textarea>
                                </div>
                            </div>

                            <div class="mb-0">
                                <label class="form-label fw-semibold small">Ghi chú giao hàng</label>
                                <textarea name="note" rows="2" class="form-control"
                                    placeholder="VD: Giao giờ hành chính, gọi trước khi đến..."></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="card checkout-card shadow-sm mt-3 border-0" style="border-radius: 12px;">
                        <div class="card-header bg-white py-3 border-0 mt-2">
                            <h5 class="mb-0 fw-bold">
                                <span class="step-badge">2</span>Phương thức thanh toán
                            </h5>
                        </div>
                        <div class="card-body px-4 pb-4">
                            <div class="mb-3">
                                <input class="form-check-input d-none payment-check" type="radio" name="payment_method"
                                    id="method_cod" value="cod" checked>
                                <label
                                    class="form-check-label d-flex align-items-center p-3 border rounded-3 payment-label payment-option w-100"
                                    for="method_cod" style="cursor:pointer;">
                                    <div class="flex-shrink-0">
                                        <i class="bi bi-cash-stack fs-4 text-secondary"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <div class="fw-bold">Thanh toán khi nhận hàng (COD)</div>
                                        <div class="text-muted small">Thanh toán bằng tiền mặt khi nhận hàng</div>
                                    </div>
                                </label>
                            </div>

                            <div class="mb-0">
                                <input class="form-check-input d-none payment-check" type="radio" name="payment_method"
                                    id="method_vnpay" value="vnpay">
                                <label
                                    class="form-check-label d-flex align-items-center p-3 border rounded-3 payment-label payment-option w-100"
                                    for="method_vnpay" style="cursor:pointer;">
                                    <div class="flex-shrink-0">
                                        <img src="assets/images/logo/vnpay-payment.png" class="payment-logo" alt="VNPAY"
                                            style="width:40px;">
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <div class="fw-bold">VNPAY</div>
                                        <div class="text-muted small">Thanh toán qua ứng dụng ngân hàng hoặc thẻ
                                            ATM/Quốc tế
                                        </div>
                                    </div>
                                </label>
                            </div>
                            <div class="mb-3">
    <input class="form-check-input d-none payment-check" type="radio" name="payment_method"
        id="method_qr" value="qr">
    <label
        class="form-check-label d-flex align-items-center p-3 border rounded-3 payment-label payment-option w-100"
        for="method_qr" style="cursor:pointer;">
        <div class="flex-shrink-0">
            <i class="bi bi-qr-code-scan fs-4 text-primary"></i>
        </div>
        <div class="flex-grow-1 ms-3">
            <div class="fw-bold">Chuyển khoản qua mã QR</div>
            <div class="text-muted small">Tự động xác nhận đơn hàng qua App ngân hàng</div>
        </div>
    </label>
</div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="sticky-summary">
                        <div class="card checkout-card shadow-sm">
                            <div class="card-header bg-white py-3 border-0">
                                <h5 class="mb-0 fw-bold">Giỏ hàng của bạn</h5>
                            </div>
                            <div class="card-body p-0">
                                <div class="list-group list-group-flush" style="max-height: 380px; overflow-y: auto;">
                                    <?php foreach ($cartItems as $item):
                                        $currentPrice = ($item['sale_price'] > 0) ? $item['sale_price'] : $item['price'];
                                        $subtotal = $currentPrice * $item['quantity'];
                                        // Kiểm tra xem có đang giảm giá không 
                                        $hasSale = ($item['sale_price'] > 0 && $item['sale_price'] < $item['price']);
                                        ?>
                                        <div class="list-group-item py-3 px-4 border-0">
                                            <div class="d-flex align-items-center">
                                                <div class="position-relative">
                                                    <img src="admin/admin_images/<?= $item['thumbnail'] ?>"
                                                        class="product-img border" alt=""
                                                        style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px;">
                                                    <span
                                                        class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                                        <?= $item['quantity'] ?>
                                                    </span>
                                                </div>
                                                <div class="ms-3 flex-grow-1">
                                                    <h6 class="mb-1 small fw-bold text-dark"><?= $item['name'] ?></h6>
                                                    <div class="d-flex align-items-center">
                                                        <span
                                                            class="text-primary fw-bold small"><?= number_format($currentPrice) ?>đ</span>
                                                        <?php if ($hasSale): ?>
                                                            <small class="text-muted text-decoration-line-through ms-2"
                                                                style="font-size: 0.7rem;">
                                                                <?= number_format($item['price'], 0, ',', '.') ?>đ
                                                            </small>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                                <div class="text-end ms-2">
                                                    <span class="fw-bold small"><?= number_format($subtotal) ?>đ</span>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <div class="card-footer bg-light border-0 p-4">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted small">Tạm tính</span>
                                    <span class="fw-semibold"><?= number_format($totalOrder) ?>đ</span>
                                </div>
                                <div class="d-flex justify-content-between mb-3">
                                    <span class="text-muted small">Phí vận chuyển</span>
                                    <span class="text-success small fw-bold">Miễn phí</span>
                                </div>
                                <hr class="my-3">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <span class="fw-bold">Tổng thanh toán</span>
                                    <span class="h4 mb-0 fw-bold text-danger"><?= number_format($totalOrder) ?>đ</span>
                                </div>
                                <button type="submit" form="orderForm"
                                    class="btn btn-primary w-100 fw-bold text-uppercase shadow-sm py-3"
                                    style="border-radius: 10px;">
                                    Thanh toán đơn hàng <i class="bi bi-arrow-right ms-2"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>

<link rel="stylesheet" href="assets/css/user/form-checkout.css">
<script>
    // Chỉ dùng để log kiểm tra hoặc xử lý giao diện nếu cần
    document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
        radio.addEventListener('change', function () {
            console.log("Phương thức thanh toán đã chọn: " + this.value);
        });
    });
</script>