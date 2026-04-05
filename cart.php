<?php
session_start();
require_once("includes/connect.php");
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Giỏ hàng - MTShop</title>

    <link rel="shortcut icon" type="image/x-icon"
        href="/project-php/website-mtshop/assets/images/logo/icon-laptopshop.png" />

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" rel="stylesheet" />

    <link rel="stylesheet" href="assets/css/user/styles.css" />
    <link rel="stylesheet" href="assets/css/user/app.css">

    <script>
        window.CART = {
            addUrl: "functions/cart/add.php",
            updateUrl: "functions/cart/update.php",
            removeUrl: "functions/cart/remove.php",
            cartUrl: "cart.php",
        };
    </script>
</head>

<body>
    <?php include("header.php"); ?>

    <main>
        <div id="loginAlertApp"
            style="position: fixed; top: 50px; right: 5px; z-index: 10000; width: 100%; max-width: 480px; pointer-events: none;">
        </div>

        <!-- Cart content goes here -->
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
    </main>

    <footer class="py-5 bg-dark">
        <div class="container">
            <p class="m-0 text-center text-white">Copyright &copy; MTShop 2026</p>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/user/handle-cart-detail.js"></script>
</body>

</html>