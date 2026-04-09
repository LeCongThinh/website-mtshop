<section class="py-4" style="background-color:#e9ecef; min-height: 80vh;">
    <div class="container">
        <ul class="breadcrumb ms-2 mb-3">
            <li class="breadcrumb-item">
                <a href="index.php" class="text-decoration-none fw-semibold">
                    <i class="bi bi-house-door-fill me-1"></i>Trang chủ
                </a>
            </li>
            <li class="breadcrumb-item active">Giỏ hàng</li>
        </ul>

        <div class="card border-0 shadow-sm rounded-4 mb-3">
            <div class="card-body p-4">
                <div class="border-start border-4 border-primary ps-3">
                    <h4 class="fw-bolder mb-1 text-uppercase">Giỏ hàng của bạn</h4>
                    <p class="text-muted mb-0 small">Kiểm tra lại sản phẩm trước khi đặt hàng</p>
                </div>
            </div>
        </div>

        <?php
        // TẠO BIẾN TRUNG GIAN ĐỂ LẤY DỮ LIỆU HIỂN THỊ
        $display_cart = [];

        if (isset($_SESSION['user_id'])) {
            // Nếu đã đăng nhập: Lấy sản phẩm từ bảng 'carts' trong DB
            $u_id = (int) $_SESSION['user_id'];
            $db_cart_query = mysqli_query($con, "SELECT product_id, quantity FROM `carts` WHERE user_id = $u_id");
            while ($row = mysqli_fetch_assoc($db_cart_query)) {
                $display_cart[$row['product_id']] = $row['quantity'];
            }
        } else {
            // Nếu chưa đăng nhập: Lấy từ Session thông thường
            $display_cart = $_SESSION['cart'] ?? [];
        }
        ?>

        <?php if (empty($display_cart)): ?>
            <div class="card border-0 shadow-sm rounded-4 text-center py-5">
                <div class="card-body">
                    <i class="bi bi-cart-x" style="font-size: 4rem; color: #dee2e6;"></i>
                    <h5 class="mt-3 text-muted">Giỏ hàng của bạn đang trống</h5>
                    <p class="text-muted small">Hãy thêm sản phẩm vào giỏ hàng để tiếp tục mua sắm</p>
                    <a href="index.php" class="btn btn-primary mt-2">
                        <i class="bi bi-arrow-left me-1"></i> Tiếp tục mua sắm
                    </a>
                </div>
            </div>

        <?php else: ?>
            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm rounded-2 overflow-hidden">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table align-middle mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="ps-4 py-3 border-0" style="width: 45%">Sản phẩm</th>
                                            <th class="text-center py-3 border-0">Đơn giá</th>
                                            <th class="text-center py-3 border-0">Số lượng</th>
                                            <th class="text-center py-3 border-0 text-end">Thành tiền</th>
                                            <th class="text-center py-3 border-0"></th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        <?php
                                        $total = 0;
                                        foreach ($display_cart as $product_id => $quantity):
                                            // Truy vấn lấy thông tin sản phẩm từ DB
                                            $stmt = mysqli_prepare($con, "SELECT * FROM products WHERE id = ?");
                                            mysqli_stmt_bind_param($stmt, "i", $product_id);
                                            mysqli_stmt_execute($stmt);
                                            $product = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

                                            if ($product):
                                                $hasSale = ($product['sale_price'] > 0 && $product['sale_price'] < $product['price']);
                                                $currentPrice = $hasSale ? $product['sale_price'] : $product['price'];
                                                $subtotal = $currentPrice * $quantity;
                                                $total += $subtotal;

                                                ?>
                                                <tr class="cart-row border-top" data-id="<?= $product_id ?>">
                                                    <td class="ps-4 py-3">
                                                        <div class="d-flex align-items-center gap-3">
                                                            <img src="admin/admin_images/<?= $product['thumbnail'] ?>"
                                                                style="width:70px; height:70px; object-fit:cover;"
                                                                class="rounded-3 border p-1 bg-white shadow-sm"
                                                                alt="<?= $product['name'] ?>">
                                                            <div>
                                                                <h6 class="mb-1">
                                                                    <a href="index.php?page=product-detail&id=<?= $product_id ?>"
                                                                        class="text-decoration-none text-dark fw-semibold line-clamp-2"
                                                                        style="font-size: 0.95rem;">
                                                                        <?= $product['name'] ?>
                                                                    </a>
                                                                </h6>
                                                            </div>
                                                        </div>
                                                    </td>

                                                    <td class="text-center">
                                                        <div class="d-flex flex-column align-items-center">
                                                            <?php if ($hasSale): ?>
                                                                <span class="text-danger fw-bold">
                                                                    <?= number_format($product['sale_price'], 0, ',', '.') ?>đ
                                                                </span>
                                                                <small class="text-muted text-decoration-line-through"
                                                                    style="font-size: 0.8rem;">
                                                                    <?= number_format($product['price'], 0, ',', '.') ?>đ
                                                                </small>
                                                                
                                                            <?php else: ?>
                                                                <span class="text-danger fw-semibold">
                                                                    <?= number_format($product['price'], 0, ',', '.') ?>đ
                                                                </span>
                                                            <?php endif; ?>
                                                        </div>
                                                    </td>

                                                    <td class="text-center">
                                                        <div class="d-flex align-items-center justify-content-center gap-1">
                                                            <form method="POST" style="display:inline-block;">
                                                                <input type="hidden" name="cart_action" value="update_qty">
                                                                <input type="hidden" name="product_id" value="<?= $product_id ?>">
                                                                <input type="hidden" name="qty" value="<?= $quantity - 1 ?>">

                                                                <button type="submit" class="btn btn-outline-secondary btn-sm"
                                                                    style="width:30px; height:30px; padding:0; border-radius: 4px;"
                                                                    <?= ($quantity <= 1) ? 'disabled' : '' ?>>
                                                                    <i class="bi bi-dash"></i>
                                                                </button>
                                                            </form>

                                                            <span class="form-control form-control-sm text-center fw-bold"
                                                                style="width:40px; background:transparent; border:none; pointer-events:none; line-height: 30px;">
                                                                <?= $quantity ?>
                                                            </span>

                                                            <form method="POST" style="display:inline-block;">
                                                                <input type="hidden" name="cart_action" value="update_qty">
                                                                <input type="hidden" name="product_id" value="<?= $product_id ?>">
                                                                <input type="hidden" name="qty" value="<?= $quantity + 1 ?>">
                                                                <button type="submit" class="btn btn-outline-secondary btn-sm"
                                                                    style="width:30px; height:30px; padding:0; border-radius: 4px;">
                                                                    <i class="bi bi-plus"></i>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </td>

                                                    <td class="text-end pe-3">
                                                        <span class="fw-bold text-danger subtotal">
                                                            <?= number_format($subtotal, 0, ',', '.') ?>đ
                                                        </span>
                                                    </td>

                                                    <td class="text-center pe-3">
                                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                                            data-bs-toggle="modal" data-bs-target="#confirmDeleteModal"
                                                            data-product-id="<?= $product_id ?>"
                                                            data-product-name="<?= addslashes($product['name']) ?>">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            <?php endif; endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3">
                        <a href="index.php" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i> Tiếp tục mua sắm
                        </a>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm rounded-2 sticky-top" style="top: 70px;">
                        <div class="card-body p-4">
                            <h5 class="fw-bold mb-4 border-bottom pb-2">Tạm tính đơn hàng</h5>

                            <div class="d-flex justify-content-between mb-3">
                                <span class="text-muted">Tạm tính</span>
                                <span id="summary-subtotal" class="fw-semibold">
                                    <?= number_format($total, 0, ',', '.') ?>đ
                                </span>
                            </div>
                            <div class="d-flex justify-content-between mb-3">
                                <span class="text-muted">Giao hàng</span>
                                <span class="text-success fw-semibold small">MIỄN PHÍ</span>
                            </div>
                            <hr class="text-muted opacity-25">
                            <div class="d-flex justify-content-between mb-4">
                                <span class="fw-bold fs-6">Tổng cộng</span>
                                <span id="summary-total" class="fw-bold fs-4 text-danger">
                                    <?= number_format($total, 0, ',', '.') ?>đ
                                </span>
                            </div>

                            <?php if (isset($_SESSION['user_id'])): ?>
                                <a href="index.php?page=checkout"
                                    class="btn btn-primary w-100 py-2 fw-bold text-uppercase shadow-sm"
                                    style="border-radius: 10px;">
                                    <i class="bi bi-bag-check me-2"></i> Đặt hàng
                                </a>
                            <?php else: ?>
                                <a href="users_area/authentication/user_login.php?redirect=cart"
                                    class="btn btn-dark w-100 py-2 fw-bold text-uppercase shadow-sm"
                                    style="border-radius: 10px;">
                                    <i class="bi bi-box-arrow-in-right me-2"></i> Đăng nhập để đặt hàng
                                </a>
                            <?php endif; ?>

                            <div class="text-center mt-3">
                                <p class="text-muted" style="font-size: 0.75rem;">
                                    <i class="bi bi-shield-check me-1"></i>Thanh toán an toàn & bảo mật
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>
<!-- ==================== MODAL XÁC NHẬN XÓA ==================== -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 6px;">
            <div class="text-end p-2">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                    style="font-size: 0.8rem;"></button>
            </div>

            <div class="modal-body px-5 pb-5 pt-2 text-center">
                <div class="mb-4 d-inline-flex align-items-center justify-content-center bg-danger bg-opacity-10 rounded-circle"
                    style="width: 80px; height: 80px;">
                    <i class="bi bi-trash text-danger" style="font-size: 2.2rem;"></i>
                </div>

                <h4 class="fw-bold text-dark mb-3">Xóa sản phẩm này?</h4>
                <p class="text-muted mb-4 px-lg-4">
                    Hành động này sẽ loại bỏ <strong id="modalProductName" class="text-primary"></strong> khỏi giỏ hàng
                    của bạn. Bạn có chắc chắn không?
                </p>

                <div class="d-flex justify-content-center gap-3">
                    <button type="button" class="btn btn-light px-4 py-2 fw-semibold border" data-bs-dismiss="modal"
                        style="border-radius: 4px; min-width: 120px;">
                        Hủy
                    </button>

                    <form id="deleteForm" method="POST" class="d-inline">
                        <input type="hidden" name="cart_action" value="remove">
                        <input type="hidden" name="product_id" id="modalProductId" value="">
                        <button type="submit" class="btn btn-danger px-4 py-2 fw-semibold shadow-sm"
                            style="border-radius: 4px; min-width: 120px;">
                            Xác nhận xóa
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript xử lý modal -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const deleteModal = document.getElementById('confirmDeleteModal');

        deleteModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget; // Nút được click

            const productId = button.getAttribute('data-product-id');
            const productName = button.getAttribute('data-product-name');

            // Điền thông tin vào modal
            document.getElementById('modalProductName').textContent = productName;
            document.getElementById('modalProductId').value = productId;
        });
    });
</script>