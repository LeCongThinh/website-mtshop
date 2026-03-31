<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh sách loại sản phẩm</title>
</head>

<body>
    <div class="container">
        <div class="categ-header">
            <div class="sub-title">
                <span class="shape"></span>
                <h3>Danh sách loại sản phẩm</h3>
            </div>
        </div>
        <div class="table-data">
            <?php
            // 1. Cấu hình phân trang
            $limit = 10;
            $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
            $page = ($page < 1) ? 1 : $page;
            $offset = ($page - 1) * $limit;

            // 2. Tính tổng số bản ghi và tổng số trang
            $total_query = "SELECT COUNT(*) as total FROM `brands` WHERE `deleted_at` IS NULL";
            $total_result = mysqli_query($con, $total_query);
            $total_row = mysqli_fetch_assoc($total_result);
            $total_records = $total_row['total'];
            $total_pages = ceil($total_records / $limit);

            // 3. Truy vấn lấy dữ liệu theo trang
            $get_brand_query = "SELECT * FROM `brands` 
                    WHERE `deleted_at` IS NULL 
                    ORDER BY `id` DESC 
                    LIMIT $limit OFFSET $offset";
            $get_brand_result = mysqli_query($con, $get_brand_query);
            ?>
            <table class="table table-bordered table-hover table-striped align-middle">
                <thead class="table-dark">
                    <tr>
                        <th class="text-center">Tên thương hiệu</th>
                        <th class="text-center">Slug</th>
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

                            // Xử lý hiển thị trạng thái
                            if ($status == 'active' || $status == 1) {
                                $status_html = "<span class='badge bg-success'>Hoạt động</span>";
                            } else {
                                $status_html = "<span class='badge bg-secondary'>Không hoạt động</span>";
                            }

                            echo "
                            <tr>
                                <td class='text-start'><strong>$brand_name</strong></td>
                                <td class='text-start'><code class='text-secondary'>$brand_slug</code></td>
                                <td class='text-center'>$status_html</td>
                                <td class='text-center'>
                                    <div class='d-flex gap-2 justify-content-center'>
                                        <!-- Nút Sửa -->
                                        <a href='index.php?edit_brand=$brand_id' class='text-primary' title='Sửa'>
                                            <i class='fas fa-edit'></i>
                                        </a>

                                        <!-- Nút Xóa -->
                                        <a href='#' class='text-danger' data-bs-toggle='modal' data-bs-target='#delModal_brand_$brand_id' title='Xóa'>
                                            <i class='fas fa-trash'></i>
                                        </a>
                                    </div>

                                    <!-- Modal Xác nhận xóa -->
                                    <div class='modal fade' id='delModal_brand_$brand_id' tabindex='-1' aria-hidden='true'>
                                        <div class='modal-dialog modal-dialog-centered'>
                                            <div class='modal-content'>
                                                <div class='modal-body text-center p-4 text-dark'>
                                                    <h4 class='text-danger mb-3'>Xác nhận xóa?</h4>
                                                    <p>Bạn có chắc muốn xóa thương hiệu: <strong>$brand_name</strong>?</p>
                                                    <div class='d-flex justify-content-center gap-2 mt-4'>
                                                        <button type='button' class='btn btn-light border' data-bs-dismiss='modal'>Hủy bỏ</button>
                                                        <a href='index.php?delete_brand=$brand_id' class='btn btn-danger px-4 text-white'>Đồng ý xóa</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4' class='text-center'>Không có dữ liệu thương hiệu.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>

            <!-- 4. Thanh phân trang căn giữa -->
            <?php if ($total_pages > 1): ?>
                <nav aria-label="Page navigation" class="mt-4">
                    <ul class="pagination justify-content-center">
                        <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                            <a class="page-link" href="index.php?view_brand&page=<?php echo $page - 1; ?>">Trước</a>
                        </li>

                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?php echo ($page == $i) ? 'active' : ''; ?>">
                                <a class="page-link" href="index.php?view_brand&page=<?php echo $i; ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>

                        <li class="page-item <?php echo ($page >= $total_pages) ? 'disabled' : ''; ?>">
                            <a class="page-link" href="index.php?view_brand&page=<?php echo $page + 1; ?>">Sau</a>
                        </li>
                    </ul>
                </nav>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>