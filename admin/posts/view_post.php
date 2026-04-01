<?php
// 1. Cấu hình phân trang
$limit = 10;
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
if ($page < 1)
    $page = 1;
$start = ($page - 1) * $limit;

// 2. Lấy tổng số bài viết để tính số trang
$total_query = "SELECT COUNT(*) AS total FROM `posts`";
$total_res = mysqli_query($con, $total_query);
$total_row = mysqli_fetch_assoc($total_res);
$total_posts = $total_row['total'];
$total_pages = ceil($total_posts / $limit);

// 1. Truy vấn lấy dữ liệu (Sửa u.fullname thành u.name)
$get_posts = "SELECT p.*, u.name AS author_name 
              FROM `posts` p 
              LEFT JOIN `users` u ON p.user_id = u.id 
              ORDER BY p.created_at DESC
              LIMIT $limit OFFSET $start";

$res_posts = mysqli_query($con, $get_posts);

?>

<div class="container-fluid mt-4">
    <?php if (isset($_GET['status'])): ?>
        <?php
        $success_msg = match ($_GET['status']) {
            'success' => 'Bài viết đã được lưu vào hệ thống thành công.',
            'updated' => 'Bài viết đã được cập nhật thành công.',
            'deleted' => 'Đã chuyển bài viết vào trạng thái ngừng hoạt động.',
            'restored' => 'Khôi phục bài viết thành công!',
            default => null
        };
        ?>
        <?php if ($success_msg): ?>
            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-3 shadow-sm"
                role="alert">
                <i class="fas fa-check-circle"></i>
                <div><strong>Thành công!</strong> <?php echo $success_msg; ?></div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <?php
        $error_msg = match ($_GET['error']) {
            'title_exists' => 'Tiêu đề này đã tồn tại trong hệ thống, vui lòng đặt tên khác.',
            'invalid_image' => 'Định dạng ảnh không hợp lệ (Chỉ nhận JPG, PNG, WEBP).',
            'upload_failed' => 'Lỗi hệ thống: Không thể lưu file ảnh vào thư mục.',
            'no_image' => 'Vui lòng chọn ảnh đại diện cho bài viết.',
            'db_error' => 'Lỗi cơ sở dữ liệu, vui lòng thử lại sau.',
            'not_found' => 'Không tìm thấy dữ liệu yêu cầu.',
            default => 'Đã xảy ra lỗi không xác định.'
        };
        ?>
        <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 mb-3 shadow-sm"
            role="alert">
            <i class="fas fa-exclamation-circle"></i>
            <div><strong>Lỗi!</strong> <?php echo $error_msg; ?></div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <div class="categ-header mb-4">
        <div class="sub-title d-flex align-items-center gap-2">
            <span class="shape bg-primary"
                style="width: 5px; height: 25px; display: inline-block; border-radius: 10px;"></span>
            <h3 class="mb-0">Danh sách bài viết</h3>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-3" style="width: 80px;">Ảnh</th>
                            <th>Tiêu đề bài viết</th> <!-- Chỉ giữ tiêu đề -->
                            <th style="width: 350px;">Nội dung</th>
                            <th>Tác giả</th>
                            <th>Ngày đăng</th>
                            <th>Trạng thái</th>
                            <th class="text-center">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (mysqli_num_rows($res_posts) > 0) {
                            while ($row = mysqli_fetch_assoc($res_posts)) {
                                $post_id = $row['id'];
                                $thumbnail = $row['thumbnail'];
                                $full_title = $row['title'];
                                $author = $row['author_name'] ?? 'Ẩn danh';
                                $date = date('d/m/Y', strtotime($row['created_at']));
                                $status = $row['status'];
                                $deleted_at = $row['deleted_at'] ?? null; // Kiểm tra thêm trường xóa mềm
                        
                                // Logic xác định bài viết có đang Active hay không
                                $is_active = ($status == 'active' && $deleted_at == null);

                                $display_title = mb_strimwidth($full_title, 0, 50, "...");
                                $short_content = mb_strimwidth(strip_tags($row['content']), 0, 120, "...");
                                ?>
                                <tr>
                                    <td class="ps-3">
                                        <?php
                                        $thumb_src = $thumbnail;
                                        if (strpos($thumb_src, 'post_thumbnails/') === 0) {
                                            $thumb_src = 'admin_images/' . $thumb_src;
                                        } else {
                                            $thumb_src = 'admin_images/post_thumbnails/' . $thumb_src;
                                        }
                                        ?>
                                        <img src="<?php echo $thumb_src; ?>" alt="thumb" class="rounded border"
                                            style="width: 60px; height: 45px; object-fit: cover;">
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark"><?php echo $display_title; ?></div>
                                    </td>
                                    <td>
                                        <small class="text-muted"><?php echo $short_content; ?></small>
                                    </td>
                                    <td>
                                        <span class="text-secondary small">
                                            <i class="fas fa-user-edit me-1"></i><?php echo $author; ?>
                                        </span>
                                    </td>
                                    <td><span class="small text-muted"><?php echo $date; ?></span></td>
                                    <td>
                                        <?php if ($is_active): ?>
                                            <span class="badge bg-success" style="font-size: 0.7rem;">Hoạt động</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger" style="font-size: 0.7rem;">Không hoạt động</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-2">
                                            <?php if ($is_active): ?>
                                                <a href="index.php?edit_post=<?php echo $post_id; ?>"
                                                    class='btn btn-sm btn-outline-primary' title="Sửa">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="#" class='btn btn-sm btn-outline-danger' data-bs-toggle="modal"
                                                    data-bs-target="#delPost<?php echo $post_id; ?>" title="Xóa">
                                                    <i class="fas fa-trash-alt"></i>
                                                </a>

                                                <div class="modal fade" id="delPost<?php echo $post_id; ?>" tabindex="-1"
                                                    aria-hidden="true">
                                                    <div class="modal-dialog modal-sm modal-dialog-centered">
                                                        <div class="modal-content">
                                                            <div class="modal-body p-4 text-center">
                                                                <p class="mb-4 text-dark">Xóa bài viết này?</p>
                                                                <div class="d-flex justify-content-center gap-2">
                                                                    <button type="button" class="btn btn-light btn-sm border"
                                                                        data-bs-dismiss="modal">Hủy</button>
                                                                    <a href="../functions/admin/posts/delete_post.php?id=<?php echo $post_id; ?>"
                                                                        class="btn btn-danger btn-sm px-3">Xóa</a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                            <?php else: ?>
                                                <a href="../functions/admin/posts/restore_post.php?id=<?php echo $post_id; ?>"
                                                    class="btn btn-outline-success btn-sm px-2 py-1" style="font-size: 0.7rem;"
                                                    title="Khôi phục">
                                                    <i class="fas fa-undo-alt me-1"></i> Khôi phục
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php
                            }
                        } else {
                            echo "<tr><td colspan='7' class='text-center py-4 text-muted'>Trống.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php if ($total_pages > 1): ?>
            <div class="card-footer bg-white border-top-0 py-3">
                <nav aria-label="Page navigation">
                    <ul class="pagination pagination-sm justify-content-center mb-0">

                        <!-- Nút Previous -->
                        <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                            <a class="page-link" href="index.php?view_post&page=<?php echo $page - 1; ?>">Trước</a>
                        </li>

                        <!-- Các số trang -->
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?php echo ($page == $i) ? 'active' : ''; ?>">
                                <a class="page-link" href="index.php?view_post&page=<?php echo $i; ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>

                        <!-- Nút Next -->
                        <li class="page-item <?php echo ($page >= $total_pages) ? 'disabled' : ''; ?>">
                            <a class="page-link" href="index.php?view_post&page=<?php echo $page + 1; ?>">Sau</a>
                        </li>

                    </ul>
                </nav>
            </div>
        <?php endif; ?>
    </div>
</div>