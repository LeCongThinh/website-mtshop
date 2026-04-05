<!-- Cart content -->
<section class="py-5">
    <div class="container">
        <h1 class="mb-4">Giỏ hàng của bạn</h1>
        <div class="row">
            <div class="col-lg-8">
                <!-- Cart items -->
                <div class="card">
                    <div class="card-body">
                        <?php
                        if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
                            echo '<div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Sản phẩm</th>
                                            <th>Giá</th>
                                            <th>Số lượng</th>
                                            <th>Tổng</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>';
                            
                            $total = 0;
                            foreach ($_SESSION['cart'] as $product_id => $quantity) {
                                // Get product details
                                $stmt = mysqli_prepare($con, "SELECT * FROM products WHERE id = ?");
                                mysqli_stmt_bind_param($stmt, "i", $product_id);
                                mysqli_stmt_execute($stmt);
                                $product = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
                                
                                if ($product) {
                                    $subtotal = $product['price'] * $quantity;
                                    $total += $subtotal;
                                    echo '<tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="assets/images/products/' . $product['image'] . '" alt="' . $product['name'] . '" class="me-3" style="width: 60px; height: 60px; object-fit: cover;">
                                                <div>
                                                    <h6 class="mb-0">' . $product['name'] . '</h6>
                                                </div>
                                            </div>
                                        </td>
                                        <td>' . number_format($product['price']) . ' VND</td>
                                        <td>
                                            <input type="number" class="form-control" value="' . $quantity . '" min="1" style="width: 80px;">
                                        </td>
                                        <td>' . number_format($subtotal) . ' VND</td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-danger">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </td>
                                    </tr>';
                                }
                            }
                            
                            echo '</tbody>
                                </table>
                            </div>';
                        } else {
                            echo '<div class="text-center py-5">
                                <i class="bi bi-cart-x display-1 text-muted mb-3"></i>
                                <h3>Giỏ hàng trống</h3>
                                <p class="text-muted">Hãy thêm sản phẩm vào giỏ hàng của bạn</p>
                                <a href="index.php?page=all-products" class="btn btn-primary">Tiếp tục mua sắm</a>
                            </div>';
                        }
                        ?>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <!-- Cart summary -->
                <?php if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])): ?>
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Tóm tắt đơn hàng</h5>
                        <div class="d-flex justify-content-between mb-3">
                            <span>Tổng cộng:</span>
                            <strong><?php echo number_format($total ?? 0); ?> VND</strong>
                        </div>
                        <button class="btn btn-primary w-100">Thanh toán</button>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>