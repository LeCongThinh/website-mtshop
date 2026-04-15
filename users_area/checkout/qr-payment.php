<?php
// Cấu hình ngân hàng của bạn
$bank = "MBBank"; // Ví dụ: VCB, MBBank, VietinBank...
$stk = "0336503798"; // Số tài khoản của bạn
$amount = $order['total_amount'];
$description = "THANHTOAN " . $order['order_code']; // Quan trọng để SePay tự duyệt đơn

// Link tạo QR tự động của SePay
$qr_url = "https://qr.sepay.vn/img?bank=$bank&acc=$stk&template=compact&amount=$amount&des=" . urlencode($description);
?>

<div class="container py-5 text-center">
    <div class="card shadow-sm mx-auto" style="max-width: 450px; border-radius: 15px;">
        <div class="card-body p-4">
            <h4 class="fw-bold text-dark">Thanh toán chuyển khoản</h4>
            <p class="text-muted small">Sử dụng App Ngân hàng để quét mã VietQR</p>
            <hr>

            <img src="<?= $qr_url ?>" class="img-fluid mb-3 border rounded" alt="Mã QR Thanh toán">

            <div class="text-start bg-light p-3 rounded mb-3">
                <div class="d-flex justify-content-between mb-2">
                    <span class="small text-muted">Số tiền:</span>
                    <span class="fw-bold text-danger"><?= number_format($amount) ?>đ</span>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="small text-muted">Nội dung CK:</span>
                    <span class="fw-bold text-primary"><?= $description ?></span>
                </div>
            </div>

            <div class="alert alert-warning py-2 small">
                <i class="bi bi-info-circle me-1"></i>
                Vui lòng giữ đúng nội dung chuyển khoản để hệ thống xác nhận tự động.
            </div>
            <div class="mt-4">
                <a href="index.php?page=cancel-qr-order&code=<?= $order['order_code'] ?>"
                    onclick="return confirm('Hành động này sẽ hủy đơn hàng hiện tại để bạn chọn lại. Bạn chắc chắn chứ?')"
                    class="btn btn-outline-secondary w-100">
                    <i class="bi bi-arrow-left"></i> Quay lại chọn phương thức khác
                </a>
            </div>
            <!-- <a href="index.php?page=my-orders" class="btn btn-primary w-100 py-2 fw-bold">
                XÁC NHẬN ĐÃ CHUYỂN KHOẢN
            </a> -->
        </div>
    </div>
</div>
<script>
    // 1. Lấy mã đơn hàng từ URL (?page=qr-payment&code=ORD-123)
    const params = new URLSearchParams(window.location.search);
    const orderCode = params.get('code');

    if (orderCode) {
        console.log("Đang theo dõi trạng thái đơn hàng: " + orderCode);

        // 2. Thiết lập vòng lặp kiểm tra mỗi 3 giây
        const checkStatus = setInterval(async () => {
            try {
                // Gọi đến Router với page=check-order-status
                const response = await fetch(`index.php?page=check-order-status&code=${orderCode}`);
                const data = await response.json();

                if (data.paid === true) {
                    // 3. Nếu đã thanh toán thành công
                    clearInterval(checkStatus); // Dừng vòng lặp

                    // Thông báo và chuyển hướng
                    alert("Hệ thống đã nhận được thanh toán. Cảm ơn bạn!");
                    window.location.href = "index.php?page=checkout-success&code=" + orderCode;
                }
            } catch (error) {
                console.error("Lỗi khi kiểm tra trạng thái:", error);
            }
        }, 3000); // 3 giây kiểm tra 1 lần
    }
</script>