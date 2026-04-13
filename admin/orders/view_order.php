<?php
// 1. Cấu hình phân trang
$limit = 10;
$current_page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
if ($current_page < 1)
    $current_page = 1;
$start = ($current_page - 1) * $limit;

// 2. Lấy tổng số đơn hàng để tính số trang (Lấy cả đơn đã xóa nếu dùng soft delete)
$total_query = "SELECT COUNT(*) AS total FROM `orders`";
$total_res = mysqli_query($con, $total_query);
$total_row = mysqli_fetch_assoc($total_res);
$total_orders = $total_row['total'];
$total_pages = ceil($total_orders / $limit);

// 3. Truy vấn lấy danh sách đơn hàng
$get_orders = "SELECT * FROM `orders` 
               ORDER BY created_at DESC 
               LIMIT $limit OFFSET $start";
$res_orders = mysqli_query($con, $get_orders);

// Hàm hiển thị Trạng thái Đơn hàng
function getOrderStatusBadge($status)
{
    return match ($status) {
        'pending' => ['class' => 'bg-warning', 'text' => 'Chờ duyệt đơn'],
        'confirmed' => ['class' => 'bg-primary', 'text' => 'Đã xác nhận đơn hàng'],
        'shipping' => ['class' => 'bg-primary', 'text' => 'Đang giao hàng'],
        'delivered' => ['class' => 'bg-success', 'text' => 'Giao thành công'],
        'cancelled' => ['class' => 'bg-danger', 'text' => 'Đã hủy đơn'],
        default => ['class' => 'bg-secondary', 'text' => 'Không rõ']
    };
}
// Hàm hiển thị Trạng thái Thanh toán
function getPaymentStatusBadge($status)
{
    return match ($status) {
        'pending' => ['class' => 'bg-secondary', 'text' => 'Chưa thanh toán'],
        'paid' => ['class' => 'bg-success', 'text' => 'Đã thanh toán'],
        'failed' => ['class' => 'bg-danger', 'text' => 'Thanh toán thất bại'],
        'refunded' => ['class' => 'bg-warning', 'text' => 'Đã hoàn tiền'],
        default => ['class' => 'bg-secondary', 'text' => 'Chưa rõ']
    };
}
?>

<div class="container-fluid mt-4">
    <?php if (isset($_GET['status'])): ?>
        <?php
        $msg = match ($_GET['status']) {
            'updated' => 'Cập nhật trạng thái đơn hàng thành công.',
            'deleted' => 'Đã xóa đơn hàng khỏi danh sách.',
            'restored' => 'Khôi phục đơn hàng thành công.',
            default => null
        };
        ?>
        <?php if ($msg): ?>
            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-3 shadow-sm"
                role="alert">
                <i class="fas fa-check-circle"></i>
                <div><strong>Thành công!</strong> <?php echo $msg; ?></div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <div class="categ-header mb-4">
        <div class="sub-title d-flex align-items-center gap-2">
            <span class="shape bg-primary"
                style="width: 5px; height: 25px; display: inline-block; border-radius: 10px;"></span>
            <h3 class="mb-0">Quản lý đơn hàng</h3>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-3">Mã đơn</th>
                            <th>Tên khách hàng</th>
                            <th>SĐT</th>
                            <th>Tổng tiền</th>
                            <th>Ngày đặt</th>
                            <th>Thanh toán</th>
                            <th>Trạng thái</th>
                            <th class="text-center">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (mysqli_num_rows($res_orders) > 0) {
                            while ($row = mysqli_fetch_assoc($res_orders)) {
                                $order_id = $row['id'];
                                $order_code = $row['order_code'];
                                // Lấy nhãn từ hàm logic bên trên
                                $order_badge = getOrderStatusBadge($row['status']);
                                $payment_badge = getPaymentStatusBadge($row['payment_status']);
                                ?>
                                <tr>
                                    <td class="ps-3 fw-bold text-primary"><?php echo $order_code; ?></td>
                                    <td><?php echo htmlspecialchars($row['receiver_name']); ?></td>
                                    <td><?php echo $row['receiver_phone']; ?></td>
                                    <td class="fw-bold"><?php echo number_format($row['total_amount'], 0, ',', '.'); ?>đ</td>
                                    <td><small
                                            class="text-muted"><?php echo date('d/m/Y', strtotime($row['created_at'])); ?></small>
                                    </td>
                                    <td>
                                        <span class="badge <?php echo $payment_badge['class']; ?> fw-bold">
                                            <?php echo $payment_badge['text']; ?>
                                        </span>
                                    </td>

                                    <td>
                                        <span class="badge <?php echo $order_badge['class']; ?>"
                                            style="font-size: 0.75rem; padding: 5px 10px;">
                                            <?php echo $order_badge['text']; ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-2">
                                            <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                                data-bs-target="#orderDetailModal"
                                                onclick="showOrderDetail(<?php echo $order_id; ?>)" title="Xem đơn hàng">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <a href="index.php?edit_order=<?php echo $order_id; ?>"
                                                class='btn btn-sm btn-outline-primary' title="Sửa">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </div>

                                        <div class="modal fade" id="delOrder<?php echo $order_id; ?>" tabindex="-1"
                                            aria-hidden="true">
                                            <div class="modal-dialog modal-sm modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-body p-4 text-center">
                                                        <p class="mb-4 text-dark">Xác nhận xóa đơn hàng
                                                            <strong><?php echo $order_code; ?></strong>?
                                                        </p>
                                                        <div class="d-flex justify-content-center gap-2">
                                                            <button type="button" class="btn btn-light btn-sm border"
                                                                data-bs-dismiss="modal">Hủy</button>
                                                            <a href="../functions/admin/orders/delete_order.php?id=<?php echo $order_id; ?>"
                                                                class="btn btn-danger btn-sm px-3">Xóa</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <?php
                            }
                        } else {
                            echo "<tr><td colspan='8' class='text-center py-4 text-muted'>Chưa có đơn hàng nào.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <?php if ($total_pages > 1): ?>
            <div class="card-footer bg-white border-top-0 py-3">
                <nav>
                    <ul class="pagination pagination-sm justify-content-center mb-0">
                        <li class="page-item <?php echo ($current_page <= 1) ? 'disabled' : ''; ?>">
                            <a class="page-link" href="index.php?view_order&page=<?php echo $current_page - 1; ?>">Trước</a>
                        </li>

                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?php echo ($current_page == $i) ? 'active' : ''; ?>">
                                <a class="page-link" href="index.php?view_order&page=<?php echo $i; ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>

                        <li class="page-item <?php echo ($current_page >= $total_pages) ? 'disabled' : ''; ?>">
                            <a class="page-link" href="index.php?view_order&page=<?php echo $current_page + 1; ?>">Sau</a>
                        </li>
                    </ul>
                </nav>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php include 'show_order.php'; ?>