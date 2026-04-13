<?php
include __DIR__ . '/../../functions/admin/orders/update_order.php';
?>
<div class="container-fluid mt-4">
    <div class="categ-header mb-4">
        <div class="sub-title d-flex align-items-center gap-2">
            <span class="shape bg-primary"
                style="width: 5px; height: 25px; display: inline-block; border-radius: 10px;"></span>
            <h4>Cập nhật đơn hàng: #<?php echo $order['order_code']; ?></h4>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body p-4">
                    <div class="row g-4 mb-4">
                        <div class="col-md-7">
                            <h6 class="text-uppercase fw-bold mb-3" style="font-size: 0.85rem; color: #4b5563;">Thông
                                tin nhận hàng</h6>
                            <div class="bg-light rounded-4 p-3 border">
                                <div class="d-flex mb-2">
                                    <div class="text-muted small" style="width: 120px;">Người nhận:</div>
                                    <div class="fw-bold small text-dark">
                                        <?php echo htmlspecialchars($order['receiver_name']); ?>
                                    </div>
                                </div>
                                <div class="d-flex mb-2">
                                    <div class="text-muted small" style="width: 120px;">Số điện thoại:</div>
                                    <div class="fw-semibold small text-dark"><?php echo $order['receiver_phone']; ?>
                                    </div>
                                </div>
                                <div class="d-flex mb-0">
                                    <div class="text-muted small" style="width: 120px;">Địa chỉ:</div>
                                    <div class="flex-grow-1 text-muted small">
                                        <?php echo htmlspecialchars($order['receiver_address']); ?>
                                    </div>
                                </div>
                                <?php if (!empty($order['note'])): ?>
                                    <div class="mt-3 p-2 bg-white rounded border-start border-3 border-warning">
                                        <div class="text-muted small fst-italic">Ghi chú:</div>
                                        <div class="text-dark small fst-italic">
                                            <?php echo htmlspecialchars($order['note']); ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="col-md-5">
                            <h6 class="text-uppercase fw-bold mb-3" style="font-size: 0.85rem; color: #4b5563;">Giao
                                dịch</h6>
                            <div class="bg-light rounded-4 p-3 border h-100">
                                <div class="mb-3">
                                    <label class="d-block small text-muted mb-1">Thanh toán qua:</label>
                                    <span
                                        class="badge bg-white text-dark border px-2 py-1"><?php echo $order['payment_method'] ?? 'COD'; ?></span>
                                </div>
                                <div class="mb-3">
                                    <label class="d-block small text-muted mb-1">Ngày đặt:</label>
                                    <span
                                        class="small fw-bold text-dark"><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <h6 class="text-uppercase fw-bold mb-3" style="font-size: 0.85rem; color: #4b5563;">Chi tiết sản
                        phẩm</h6>
                    <div class="table-responsive border rounded-3">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr class="small text-uppercase">
                                    <th style="width: 80px;">Ảnh</th>
                                    <th>Sản phẩm</th>
                                    <th class="text-center">SL</th>
                                    <th class="text-end">Giá</th>
                                    <th class="text-end">Thành tiền</th>
                                </tr>
                            </thead>
                            <tbody class="small">
                                <?php
                                $order_id = $order['id'];
                                $get_items = "SELECT * FROM `order_details` WHERE order_id = '$order_id'";
                                $res_items = mysqli_query($con, $get_items);
                                if (mysqli_num_rows($res_items) > 0):
                                    while ($item = mysqli_fetch_assoc($res_items)):
                                        ?>
                                        <tr>
                                            <td style="width: 80px;">
                                                <div class="product-img-container border rounded bg-white d-flex align-items-center justify-content-center"
                                                    style="width: 60px; height: 60px; overflow: hidden;">
                                                    <?php
                                                    $image_path = !empty($item['product_thumbnail']) ? "admin_images/" . $item['product_thumbnail'] : "../uploads/products/no-image.png";
                                                    ?>
                                                    <img src="<?php echo $image_path; ?>" alt="Product Image"
                                                        style="max-width: 100%; max-height: 100%; object-fit: contain;">
                                                </div>
                                            </td>
                                            <td class="fw-bold"><?php echo htmlspecialchars($item['product_name']); ?></td>
                                            <td class="text-center">x<?php echo $item['quantity']; ?></td>
                                            <td class="text-end"><?php echo number_format($item['price'], 0, ',', '.'); ?>đ</td>
                                            <td class="text-end fw-bold text-primary">
                                                <?php echo number_format($item['price'] * $item['quantity'], 0, ',', '.'); ?>đ
                                            </td>
                                        </tr>
                                    <?php endwhile; else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">Không có thông tin sản phẩm chi
                                            tiết.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="4" class="text-end fw-bold">TỔNG CỘNG:</td>
                                    <td class="text-end text-danger fw-bold fs-5">
                                        <?php echo number_format($order['total_amount'], 0, ',', '.'); ?>đ
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm border-0 rounded-4 sticky-top" style="top: 20px;">
                <div class="card-header bg-white border-bottom p-3">
                    <h6 class="mb-0 fw-bold"><i class="fas fa-edit me-2 text-primary"></i>Xử lý đơn hàng</h6>
                </div>
                <div class="card-body p-4">
                    <form action="" method="POST">
                        <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">

                        <div class="mb-4">
                            <label class="form-label small fw-bold text-muted text-uppercase">Trạng thái đơn
                                hàng</label>
                            <select name="status" class="form-select form-select-lg border-2 shadow-none"
                                style="font-size: 0.95rem;">
                                <option value="pending" <?php echo ($order['status'] == 'pending') ? 'selected' : ''; ?>>
                                    Chờ duyệt đơn</option>
                                <option value="confirmed" <?php echo ($order['status'] == 'confirmed') ? 'selected' : ''; ?>>Đã xác nhận đơn hàng</option>
                                <option value="shipping" <?php echo ($order['status'] == 'shipping') ? 'selected' : ''; ?>>Đang giao hàng</option>
                                <option value="delivered" <?php echo ($order['status'] == 'delivered') ? 'selected' : ''; ?>>Giao thành công</option>
                                <option value="cancelled" <?php echo ($order['status'] == 'cancelled') ? 'selected' : ''; ?>>Đã hủy đơn</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label small fw-bold text-muted text-uppercase">Trạng thái thanh
                                toán</label>
                            <select name="payment_status" class="form-select form-select-lg border-2 shadow-none"
                                style="font-size: 0.95rem;">
                                <option value="pending" <?php echo ($order['payment_status'] == 'pending') ? 'selected' : ''; ?>>Chưa thanh toán</option>
                                <option value="paid" <?php echo ($order['payment_status'] == 'paid') ? 'selected' : ''; ?>>Đã thanh toán</option>
                                <option value="failed" <?php echo ($order['payment_status'] == 'failed') ? 'selected' : ''; ?>>Thanh toán thất bại</option>
                                <option value="refunded" <?php echo ($order['payment_status'] == 'refunded') ? 'selected' : ''; ?>>Đã hoàn tiền</option>
                            </select>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" name="update_order_btn" class="btn btn-primary shadow-sm py-2 px-3"
                                style="font-size: 0.9rem;">
                                <i class="fas fa-check-circle me-2"></i>Cập nhật đơn hàng
                            </button>

                            <a href="index.php?view_order" class="btn btn-light border py-2 px-3"
                                style="font-size: 0.9rem;">
                                Quay lại
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>