<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh sách danh mục</title>
</head>

<body>
    <div class="container">
        <div class="categ-header">
            <div class="sub-title">
                <span class="shape"></span>
                <h3>Tất cả danh mục</h3>
            </div>
        </div>
        <div class="table-data">
            <table class="table table-bordered table-hover table-striped text-center">
                <thead class="table-dark">
                    <tr>
                        <th>Tên danh mục</th>
                        <th>Slug</th>
                        <th>Thuộc danh mục</th>
                        <th>Trạng thái</th>
                        <th>Hoạt động</th>
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
                    $total_query = "SELECT COUNT(*) as total FROM `categories` WHERE `deleted_at` IS NULL";
                    $total_result = mysqli_query($con, $total_query);
                    $total_row = mysqli_fetch_assoc($total_result);
                    $total_records = $total_row['total'];
                    $total_pages = ceil($total_records / $limit);

                    // 3. Truy vấn lấy dữ liệu theo trang (thêm LIMIT và OFFSET)
                    $get_category_query = "SELECT c1.*, c2.name as parent_name 
                       FROM `categories` c1 
                       LEFT JOIN `categories` c2 ON c1.parent_id = c2.id 
                       WHERE c1.deleted_at IS NULL 
                       ORDER BY c1.id DESC 
                       LIMIT $limit OFFSET $offset";

                    $get_category_result = mysqli_query($con, $get_category_query);

                    // Hiển thị dữ liệu bảng
                    while ($row = mysqli_fetch_array($get_category_result)) {
                        $category_id = $row['id'];
                        $category_name = $row['name'];
                        $category_slug = $row['slug'];
                        $parent_name = $row['parent_name'];
                        $status = $row['status'];

                        $status_html = ($status == 'active' || $status == 1)
                            ? "<span class='badge bg-success'>Hoạt động</span>"
                            : "<span class='badge bg-secondary'>Không hoạt động</span>";

                        $display_parent = !empty($parent_name) ? $parent_name : "<i class='text-muted'>Danh mục gốc</i>";

                        echo "
                        <tr>
                            <td class='text-start'><strong>$category_name</strong></td>
                            <td class='text-start'><code class='text-secondary'>$category_slug</code></td>
                            <td class='text-start'>$display_parent</td>
                            <td class='text-center'>$status_html</td>
                            <td class='text-center'>
                                <div class='d-flex gap-2 justify-content-center'>
                                    <a href='index.php?edit_category=$category_id' class='text-primary'><i class='fas fa-edit'></i></a>
                                    <a href='#' class='text-danger' data-bs-toggle='modal' data-bs-target='#delModal_$category_id'><i class='fas fa-trash'></i></a>
                                </div>
                                <!-- Modal xóa (giữ nguyên như cũ) -->
                                <div class='modal fade' id='delModal_$category_id' tabindex='-1' aria-hidden='true'>
                                    <div class='modal-dialog modal-dialog-centered'>
                                        <div class='modal-content'>
                                            <div class='modal-body text-center p-4'>
                                                <h4 class='text-danger mb-3'>Xác nhận xóa?</h4>
                                                <p>Bạn có chắc muốn xóa danh mục: <strong>$category_name</strong>?</p>
                                                <div class='d-flex justify-content-center gap-2 mt-4'>
                                                    <button type='button' class='btn btn-light border' data-bs-dismiss='modal'>Hủy bỏ</button>
                                                    <a href='index.php?delete_category=$category_id' class='btn btn-danger px-4'>Đồng ý xóa</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
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
                        <a class="page-link" href="index.php?view_category&page=<?php echo $page - 1; ?>">Trước</a>
                    </li>

                    <!-- Các số trang -->
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="page-item <?php if ($page == $i)
                            echo 'active'; ?>">
                            <a class="page-link" href="index.php?view_category&page=<?php echo $i; ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>

                    <!-- Nút Tiếp theo -->
                    <li class="page-item <?php if ($page >= $total_pages)
                        echo 'disabled'; ?>">
                        <a class="page-link" href="index.php?view_category&page=<?php echo $page + 1; ?>">Sau</a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>

</body>

</html>