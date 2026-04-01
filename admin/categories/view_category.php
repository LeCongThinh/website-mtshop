<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh sách danh mục</title>
</head>

<body>
    <div class="container">
        <?php
        if (isset($_GET['msg'])) {
            if ($_GET['msg'] == 'delete_success') {
                echo '
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i> 
                <strong>Thành công!</strong> Xóa danh mục thành công.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>';
            } elseif ($_GET['msg'] == 'delete_error') {
                echo '
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i> 
                <strong>Lỗi!</strong> Đã xảy ra sự cố khi xử lý dữ liệu.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>';
            }
            if ($_GET['msg'] == 'restore_success') {
                echo '
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i> 
            <strong>Thành công!</strong> Danh mục đã được khôi phục trạng thái hoạt động.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>';
            } elseif ($_GET['msg'] == 'restore_error') {
                echo '
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i> 
            <strong>Lỗi!</strong> Không thể khôi phục danh mục này. Vui lòng thử lại.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>';
            }
        }
        ?>
        <div class="categ-header mb-4">
            <div class="sub-title d-flex align-items-center gap-2">
                <span class="shape bg-primary"
                    style="width: 5px; height: 25px; display: inline-block; border-radius: 10px;"></span>
                <h3 class="mb-0">Danh sách danh mục</h3>
            </div>
        </div>
        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Tên danh mục</th>
                                <th>Slug</th>
                                <th>Thuộc danh mục</th>
                                <th class="text-center">Trạng thái</th>
                                <th class="text-center">Hoạt động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // 1. Cấu hình phân trang
                            $limit = 10; // Số bản ghi trên mỗi trang
                            $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
                            if ($page < 1)
                                $page = 1;
                            $offset = ($page - 1) * $limit;

                            // 2. Lấy tổng số bản ghi để tính tổng số trang
                            $total_query = "SELECT COUNT(*) as total FROM `categories`";
                            $total_result = mysqli_query($con, $total_query);
                            $total_row = mysqli_fetch_assoc($total_result);
                            $total_records = $total_row['total'];
                            $total_pages = ceil($total_records / $limit);

                            // 3. Truy vấn lấy dữ liệu theo trang (thêm LIMIT và OFFSET)
                            $get_category_query = "SELECT c1.*, c2.name as parent_name 
                       FROM `categories` c1 
                       LEFT JOIN `categories` c2 ON c1.parent_id = c2.id 
                       ORDER BY c1.id DESC 
                       LIMIT $limit OFFSET $offset";

                            $get_category_result = mysqli_query($con, $get_category_query);

                            while ($row = mysqli_fetch_array($get_category_result)) {
                                $category_id = $row['id'];
                                $category_name = $row['name'];
                                $category_slug = $row['slug'];
                                $parent_name = $row['parent_name'];
                                $status = $row['status'];
                                $deleted_at = $row['deleted_at'];

                                // 1. Logic cột Trạng thái
                                // Nếu có deleted_at HOẶC status là inactive thì coi như "Không hoạt động"
                                if ($deleted_at != NULL || $status == 'inactive' || $status == 0) {
                                    $status_html = "<span class='badge bg-danger'>Không hoạt động</span>";
                                    $is_active = false;
                                } else {
                                    $status_html = "<span class='badge bg-success'>Hoạt động</span>";
                                    $is_active = true;
                                }

                                $display_parent = !empty($parent_name) ? $parent_name : "<i class='text-muted'>Danh mục gốc</i>";

                                echo "
                        <tr>
                            <td class='text-start'>$category_name</td>
                            <td class='text-start'><code class='text-secondary'>$category_slug</code></td>
                            <td class='text-start'>$display_parent</td>
                            <td class='text-center'>$status_html</td>
                            <td class='text-center'>
                                <div class='d-flex gap-2 justify-content-center'>";

                                if ($is_active) {
                                    // Nếu đang hoạt động: Hiện nút Sửa và Xóa
                                    echo "
                                        <a href='index.php?edit_category=$category_id' class='btn btn-sm btn-outline-primary' title='Sửa'>
                                            <i class='fas fa-edit'></i>
                                        </a>
                                        <a href='#' class='btn btn-sm btn-outline-danger' data-bs-toggle='modal' data-bs-target='#delModal_$category_id' title='Xóa'>
                                            <i class='fas fa-trash-alt'></i>
                                        </a>";
                                } else {
                                    echo "
                                                <a href='../functions/admin/categories/restore_category.php?id=$category_id' 
                                                class='btn btn-outline-success btn-sm px-3' 
                                                style='font-size: 0.75rem;'>
                                                <i class='fas fa-undo-alt me-1'></i> Khôi phục
                                                </a>";
                                }
                                echo "  </div>
                                <!-- Modal xóa (Chỉ khởi tạo nếu danh mục đang active) -->
                                " . ($is_active ? "
                                <div class='modal fade' id='delModal_$category_id' tabindex='-1' aria-hidden='true'>
                                    <div class='modal-dialog modal-dialog-centered text-dark'>
                                        <div class='modal-content'>
                                            <div class='modal-body text-center p-4'>
                                                <h4 class='text-danger mb-3'>Xác nhận?</h4>
                                                <p>Danh mục <strong>$category_name</strong> sẽ bị ngừng hoạt động.</p>
                                                <div class='d-flex justify-content-center gap-2 mt-4'>
                                                    <button type='button' class='btn btn-light border' data-bs-dismiss='modal'>Hủy</button>
                                                    <a href='../functions/admin/categories/delete_category.php?id=$category_id' class='btn btn-danger px-4'>Đồng ý</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>" : "") . "
                            </td>
                        </tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                    <nav aria-label="Page navigation" class="mt-3">
                        <ul class="pagination justify-content-center">
                            <!-- Nút Trở lại -->
                            <li class="page-item <?php if ($page <= 1)
                                echo 'disabled'; ?>">
                                <a class="page-link"
                                    href="index.php?view_category&page=<?php echo $page - 1; ?>">Trước</a>
                            </li>

                            <!-- Các số trang -->
                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                <li class="page-item <?php if ($page == $i)
                                    echo 'active'; ?>">
                                    <a class="page-link"
                                        href="index.php?view_category&page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>

                            <!-- Nút Tiếp theo -->
                            <li class="page-item <?php if ($page >= $total_pages)
                                echo 'disabled'; ?>">
                                <a class="page-link"
                                    href="index.php?view_category&page=<?php echo $page + 1; ?>">Sau</a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>

</body>

</html>