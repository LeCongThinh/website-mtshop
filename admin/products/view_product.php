<?php
// 1. Cấu hình phân trang
$limit = 10; // Số sản phẩm mỗi trang
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
if ($page < 1)
    $page = 1;
$start = ($page - 1) * $limit;

// 2. Lấy tổng số sản phẩm để tính tổng số trang
$total_query = "SELECT COUNT(*) as total FROM `products` WHERE deleted_at IS NULL";
$total_result = mysqli_query($con, $total_query);
$total_data = mysqli_fetch_assoc($total_result);
$total_records = $total_data['total'];
$total_pages = ceil($total_records / $limit);

// 3. Truy vấn dữ liệu sản phẩm theo trang hiện tại
$get_products = "SELECT p.*, c.name as category_name 
                 FROM `products` p
                 LEFT JOIN `categories` c ON p.category_id = c.id
                 ORDER BY p.id DESC 
                 LIMIT $start, $limit";
$res_products = mysqli_query($con, $get_products);
?>
<div class="container mt-4">
    <?php
    if (isset($_GET['status'])) {
        $status = $_GET['status'];
        $msg_type = $_GET['msg'] ?? '';
        if ($status == 'success') {
            // Xác định nội dung thông báo dựa trên hành động
            switch ($msg_type) {
                case 'added':
                    $message = "Sản phẩm mới đã được thêm vào hệ thống.";
                    break;
                case 'updated':
                    $message = "Thông tin sản phẩm đã được cập nhật thành công.";
                    break;
                case 'deleted':
                    $message = "Sản phẩm đã ngừng kinh doanh.";
                    break;
                case 'restored':
                    $message = "Sản phẩm đã được khôi phục.";
                    break;
                default:
                    $message = "Thao tác dữ liệu thành công!";
            }
            echo '
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="fas fa-check-circle me-2 fs-5"></i>
                <div><strong>Thành công!</strong> ' . $message . '</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>';

        } elseif ($status == 'error') {
            echo '
        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="fas fa-exclamation-triangle me-2 fs-5"></i>
                <div><strong>Thất bại!</strong> Có lỗi xảy ra trong quá trình xử lý dữ liệu. Vui lòng thử lại.</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>';
        }
    }
    ?>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="sub-title d-flex align-items-center gap-2">
            <span class="shape bg-primary"
                style="width: 5px; height: 25px; display: inline-block; border-radius: 10px;"></span>
            <h3 class="mb-0">Danh sách sản phẩm</h3>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
                <tr>
                    <th width="10%">Ảnh sản phẩm</th>
                    <th class="text-start" width="25%">Tên sản phẩm</th>
                    <th>Danh mục</th>
                    <th>Giá gốc</th>
                    <th>Giá khuyến mãi</th>
                    <th>SL Kho</th>
                    <th>Trạng thái</th>
                    <th width="12%">Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (mysqli_num_rows($res_products) > 0) {
                    while ($row = mysqli_fetch_assoc($res_products)) {
                        $p_id = $row['id'];
                        $p_name = $row['name'];
                        $p_category = $row['category_name'] ?? 'Chưa phân loại';
                        $p_price = $row['price'];
                        $p_sale = $row['sale_price'];
                        $p_stock = $row['stock'];
                        $p_status = $row['status'];
                        $p_image = $row['thumbnail'];

                        // Xác định trạng thái sản phẩm
                        // $is_active = ($status == 'active' && $deleted_at == null);
                
                        // Rút ngắn tên sản phẩm nếu quá dài
                        $display_name = (mb_strlen($p_name) > 35) ? mb_substr($p_name, 0, 35) . '...' : $p_name;

                        // Logic hiển thị Trạng thái (Ưu tiên kiểm tra kho hàng trước)
                        if ($p_stock <= 0) {
                            $status_html = "<span class='badge bg-warning'>Hết hàng</span>";
                        } elseif ($p_status == 'active') {
                            $status_html = "<span class='badge bg-success'>Đang kinh doanh</span>";
                        } else {
                            $status_html = "<span class='badge bg-danger'>Ngừng kinh doanh</span>";
                        }
                        ?>
                        <tr>
                            <td>
                                <img src="../admin/admin_images/<?php echo $p_image; ?>" alt="Product Image"
                                    style="width: 55px; height: 55px; object-fit: cover;" class="rounded border shadow-sm">
                            </td>
                            <td class="text-start">
                                <div class="fw-bold text-dark mb-0"><?php echo $display_name; ?></div>
                            </td>
                            <td>
                                <span class="badge bg-light text-primary border border-primary-subtle">
                                    <?php echo $p_category; ?>
                                </span>
                            </td>
                            <td class="fw-semibold">
                                <?php echo number_format($p_price, 0, ',', '.'); ?>đ
                            </td>
                            <td class="text-danger fw-bold">
                                <?php
                                echo ($p_sale > 0) ? number_format($p_sale, 0, ',', '.') . 'đ' : '<span class="text-muted fw-normal">--</span>';
                                ?>
                            </td>
                            <td>
                                <span class="fw-bold <?php echo ($p_stock <= 0) ? 'text-danger' : 'text-dark'; ?>">
                                    <?php echo $p_stock; ?>
                                </span>
                            </td>
                            <td><?php echo $status_html; ?></td>
                            <td>
                                <div class="d-flex justify-content-center gap-2">
                                    <?php if ($p_status == 'active'): ?>
                                        <a href="index.php?edit_product=<?php echo $p_id; ?>" class="btn btn-sm btn-outline-primary"
                                            title="Chỉnh sửa">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal"
                                            data-bs-target="#delProduct<?php echo $p_id; ?>" title="Ngừng kinh doanh">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>

                                        <div class="modal fade" id="delProduct<?php echo $p_id; ?>" tabindex="-1"
                                            aria-hidden="true">
                                            <div class="modal-dialog modal-sm modal-dialog-centered">
                                                <div class="modal-content border-0 shadow">
                                                    <div class="modal-body p-4 text-center">
                                                        <div class="text-danger mb-3">
                                                            <i class="fas fa-exclamation-circle fa-3x"></i>
                                                        </div>
                                                        <h6 class="mb-2">Ngừng kinh doanh?</h6>
                                                        <p class="small text-muted mb-4">Bạn muốn ngừng kinh doanh sản phẩm
                                                            <br><strong><?php echo $p_name; ?></strong>?
                                                        </p>
                                                        <div class="d-flex justify-content-center gap-2">
                                                            <button type="button" class="btn btn-light btn-sm border px-3"
                                                                data-bs-dismiss="modal">Hủy</button>
                                                            <a href="../functions/admin/products/delete_product.php?id=<?php echo $p_id; ?>"
                                                                class="btn btn-danger btn-sm px-3">Xác nhận</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    <?php else: ?>
                                        <a href="../functions/admin/products/restore_product.php?id=<?php echo $p_id; ?>"
                                            class="btn btn-sm btn-outline-success w-100 d-flex align-items-center justify-content-center gap-1"
                                            onclick="return confirm('Bạn muốn khôi phục kinh doanh cho sản phẩm này?')"
                                            title="Khôi phục kinh doanh">
                                            <i class="fas fa-undo-alt"></i> <span style="font-size: 12px;">Khôi phục</span>
                                        </a>

                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php
                    }
                } else {
                    echo "<tr><td colspan='9' class='py-4 text-muted'>Không tìm thấy sản phẩm nào.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
    <?php if ($total_pages > 1): ?>
        <nav aria-label="Page navigation" class="mt-4">
            <ul class="pagination justify-content-center">
                <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                    <a class="page-link shadow-sm" href="index.php?view_product&page=<?php echo $page - 1; ?>"
                        aria-label="Previous">
                        <span aria-hidden="true">&laquo; Trước</span>
                    </a>
                </li>
                <?php
                // Giới hạn hiển thị số trang nếu quá nhiều (Ví dụ: hiển thị 5 trang gần nhất)
                for ($i = 1; $i <= $total_pages; $i++):
                    if ($i == 1 || $i == $total_pages || ($i >= $page - 2 && $i <= $page + 2)):
                        ?>
                        <li class="page-item <?php echo ($page == $i) ? 'active' : ''; ?>">
                            <a class="page-link shadow-sm" href="index.php?view_product&page=<?php echo $i; ?>">
                                <?php echo $i; ?>
                            </a>
                        </li>
                        <?php
                    endif;
                endfor;
                ?>
                <li class="page-item <?php echo ($page >= $total_pages) ? 'disabled' : ''; ?>">
                    <a class="page-link shadow-sm" href="index.php?view_product&page=<?php echo $page + 1; ?>"
                        aria-label="Next">
                        <span aria-hidden="true">Sau &raquo;</span>
                    </a>
                </li>
            </ul>
        </nav>
        <div class="text-center text-muted small mt-2">
            Hiển thị trang <?php echo $page; ?> / <?php echo $total_pages; ?> (Tổng cộng <?php echo $total_records; ?> sản
            phẩm)
        </div>
    <?php endif; ?>
</div>