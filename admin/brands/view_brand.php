<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh sách loại sản phẩm</title>
</head>

<body>
    <div class="container">
        <div class="container-fluid px-0">
            <?php if (isset($_GET['status']) || isset($_GET['msg'])): ?>
                <?php
                // Lấy giá trị từ cả status hoặc msg (ưu tiên status)
                $status_val = $_GET['status'] ?? $_GET['msg'] ?? null;

                $success_msg = match ($status_val) {
                    'success' => 'Đã thêm loại sản phẩm vào hệ thống.',
                    'updated' => 'Cập nhật loại sản phẩm thành công.',
                    'deleted',
                    'delete_success' => 'Đã xóa loại sản phẩm khỏi hệ thống.',
                    'restored',
                    'restore_success' => 'Khôi phục loại sản phẩm thành công.',
                    default => null
                };
                ?>

                <?php if ($success_msg): ?>
                    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center shadow-sm mb-3"
                        role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        <div>
                            <strong>Thành công!</strong> <?php echo $success_msg; ?>
                        </div>
                        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <?php
            // Kiểm tra nếu có error hoặc msg chứa chữ 'error'
            $has_error = isset($_GET['error']) || (isset($_GET['msg']) && strpos($_GET['msg'], 'error') !== false);

            if ($has_error):
                ?>
                <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center shadow-sm mb-3"
                    role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <div>
                        <strong>Lỗi!</strong> Thao tác thất bại, vui lòng kiểm tra lại.
                    </div>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
        </div>

        <div class="categ-header mb-4">
            <div class="sub-title d-flex align-items-center gap-2">
                <span class="shape bg-primary"
                    style="width: 5px; height: 25px; display: inline-block; border-radius: 10px;"></span>
                <h3 class="mb-0">Danh sách loại sản phẩm</h3>
            </div>
        </div>

        <div class="table-data">
            <?php
            $limit = 10;
            $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
            $page = ($page < 1) ? 1 : $page;
            $offset = ($page - 1) * $limit;

            // 2. Tính tổng (BỎ điều kiện WHERE deleted_at IS NULL để lấy cả bản ghi đã xóa)
            $total_query = "SELECT COUNT(*) as total FROM `brands`";
            $total_result = mysqli_query($con, $total_query);
            $total_row = mysqli_fetch_assoc($total_result);
            $total_records = $total_row['total'];
            $total_pages = ceil($total_records / $limit);

            // 3. Truy vấn (BỎ điều kiện lọc để hiển thị mọi trạng thái)
            $get_brand_query = "SELECT * FROM `brands` 
                            ORDER BY `id` DESC 
                            LIMIT $limit OFFSET $offset";
            $get_brand_result = mysqli_query($con, $get_brand_query);
            ?>

            <div class="card shadow-sm border-0">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="text-start">Tên thương hiệu</th>
                                    <th class="text-start">Slug</th>
                                    <th class="text-center">Trạng thái</th>
                                    <th class="text-center">Hoạt động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (mysqli_num_rows($get_brand_result) > 0) {
                                    while ($row = mysqli_fetch_array($get_brand_result)) {
                                        $brand_id = $row['id'];
                                        $brand_name = $row['name'];
                                        $brand_slug = $row['slug'];
                                        $status = $row['status'];
                                        $deleted_at = $row['deleted_at'];

                                        // Logic xác định dòng này có "Active" hay không
                                        $is_inactive = ($deleted_at !== NULL || $status == 'inactive' || $status == 0);

                                        if ($is_inactive) {
                                            $status_html = "<span class='badge bg-danger'>Không hoạt động</span>";
                                        } else {
                                            $status_html = "<span class='badge bg-success'>Hoạt động</span>";
                                        }
                                        ?>
                                        <tr>
                                            <td class="text-start"><strong><?php echo $brand_name; ?></strong></td>
                                            <td class="text-start"><code
                                                    class="text-secondary"><?php echo $brand_slug; ?></code></td>
                                            <td class="text-center"><?php echo $status_html; ?></td>
                                            <td class="text-center">
                                                <div class="d-flex gap-2 justify-content-center">
                                                    <?php if (!$is_inactive): ?>
                                                        <!-- Nút Sửa & Xóa khi đang Active -->
                                                        <a href="index.php?edit_brand=<?php echo $brand_id; ?>"
                                                            class='btn btn-sm btn-outline-primary' title="Sửa">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <a href="#" class='btn btn-sm btn-outline-danger' data-bs-toggle="modal"
                                                            data-bs-target="#delModal_brand_<?php echo $brand_id; ?>" title="Xóa">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </a>
                                                    <?php else: ?>
                                                        <!-- Nút Khôi phục khi đang Inactive -->
                                                        <a href="../functions/admin/brands/restore_brand.php?id=<?php echo $brand_id; ?>"
                                                            class="btn btn-outline-success btn-sm px-3" style="font-size: 0.75rem;">
                                                            <i class="fas fa-undo-alt me-1"></i> Khôi phục
                                                        </a>
                                                    <?php endif; ?>
                                                </div>

                                                <!-- Modal xác nhận xóa -->
                                                <?php if (!$is_inactive): ?>
                                                    <div class="modal fade" id="delModal_brand_<?php echo $brand_id; ?>"
                                                        tabindex="-1" aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-centered text-dark">
                                                            <div class="modal-content">
                                                                <div class="modal-body text-center p-4">
                                                                    <h4 class="text-danger mb-3">Xác nhận?</h4>
                                                                    <p>Thương hiệu <strong><?php echo $brand_name; ?></strong> sẽ
                                                                        chuyển sang
                                                                        ngừng hoạt động.</p>
                                                                    <div class="d-flex justify-content-center gap-2 mt-4">
                                                                        <button type="button" class="btn btn-light border"
                                                                            data-bs-dismiss="modal">Hủy</button>
                                                                        <a href="../functions/admin/brands/delete_brand.php?id=<?php echo $brand_id; ?>"
                                                                            class="btn btn-danger px-4 text-white">Xác nhận</a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php }
                                } else {
                                    echo "<tr><td colspan='4' class='text-center text-muted'>Không có dữ liệu.</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>

                        <!-- Phân trang -->
                        <?php if ($total_pages > 1): ?>
                            <nav class="mt-4">
                                <ul class="pagination justify-content-center">
                                    <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                                        <a class="page-link"
                                            href="index.php?view_brand&page=<?php echo $page - 1; ?>">Trước</a>
                                    </li>
                                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                        <li class="page-item <?php echo ($page == $i) ? 'active' : ''; ?>">
                                            <a class="page-link"
                                                href="index.php?view_brand&page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                        </li>
                                    <?php endfor; ?>
                                    <li class="page-item <?php echo ($page >= $total_pages) ? 'disabled' : ''; ?>">
                                        <a class="page-link"
                                            href="index.php?view_brand&page=<?php echo $page + 1; ?>">Sau</a>
                                    </li>
                                </ul>
                            </nav>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>