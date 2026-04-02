<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh sách tài khoản - MTShop</title>
    <link rel="shortcut icon" type="image/x-icon" href="../../../assets/images/logo/icon-laptopshop.png" />

</head>

<body>
    <div class="container">
        <?php if (isset($_GET['status']) && $_GET['status'] == 'success'): ?>
            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center shadow-sm" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                <div>
                    <strong>Thành công!</strong> Tài khoản mới đã được thêm vào hệ thống.
                </div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['error'])): ?>
            <?php
            $error_msg = match ($_GET['error']) {
                'db_error' => 'Lỗi cơ sở dữ liệu, không thể lưu tài khoản.',
                'upload_failed' => 'Lỗi hệ thống: Không thể ghi file ảnh vào thư mục.',
                'no_image' => 'Vui lòng chọn ảnh đại diện cho tài khoản.',
                'email_exists' => 'Email này đã tồn tại, vui lòng dùng email khác.',
                default => 'Đã xảy ra lỗi không xác định.'
            };
            ?>
            <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center shadow-sm" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <div>
                    <strong>Lỗi!</strong> <?php echo $error_msg; ?>
                </div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        <div class="categ-header mb-4">
            <div class="sub-title d-flex align-items-center gap-2">
                <span class="shape bg-primary"
                    style="width: 5px; height: 25px; display: inline-block; border-radius: 10px;"></span>
                <h3 class="mb-0">Danh sách tài khoản</h3>
            </div>
        </div>

        <?php
        // --- LOGIC PHÂN TRANG ---
        $limit = 10; // Số lượng record mỗi trang
        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        if ($page < 1)
            $page = 1;
        $start = ($page - 1) * $limit;

        // Lấy ID admin hiện tại từ session (nếu có)
        $current_admin_id = isset($_SESSION['admin_id']) ? (int) $_SESSION['admin_id'] : 0;

        // Lấy tổng số dòng để tính số trang (loại bỏ tài khoản đang đăng nhập)
        $total_res = mysqli_query($con, "SELECT COUNT(*) as total FROM `users` WHERE id != $current_admin_id");
        $total_data = mysqli_fetch_assoc($total_res);
        $total_records = $total_data['total'];
        $total_pages = ceil($total_records / $limit);

        // Truy vấn dữ liệu: Mới nhất lên đầu (DESC) + Phân trang (LIMIT), loại bỏ tài khoản đang đăng nhập
        $get_user_query = "SELECT * FROM `users` WHERE id != $current_admin_id ORDER BY id DESC LIMIT $start, $limit";
        $get_user_result = mysqli_query($con, $get_user_query);

        ?>


        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th width="5%">STT</th>
                                <th>Tên tài khoản</th>
                                <th>Email</th>
                                <th>Số điện thoại</th>
                                <th>Chức vụ</th>
                                <th>Trạng thái</th>
                                <th width="15%" class="text-center">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (mysqli_num_rows($get_user_result) == 0) {
                                echo "<tr><td colspan='7' class='py-4 text-muted'>Chưa có tài khoản nào trong hệ thống.</td></tr>";
                            } else {
                                // STT cộng dồn theo trang: Trang 1 (1-10), Trang 2 (11-20)...
                                $id_number = $start + 1;
                                while ($row = mysqli_fetch_array($get_user_result)) {
                                    $user_id = $row['id'];
                                    $username = $row['name'];
                                    $user_email = $row['email'];
                                    $user_phone = $row['phone'];
                                    $user_role = $row['role'];
                                    $user_status = $row['status'];

                                    // Xử lý hiển thị Chức vụ
                                    switch ($user_role) {
                                        case 'admin':
                                            $role_display = "<span class='badge rounded-pill bg-danger'><i class='fas fa-shield-alt me-1'></i> Quản trị</span>";
                                            break;
                                        case 'staff':
                                            $role_display = "<span class='badge rounded-pill bg-primary'><i class='fas fa-user-tie me-1'></i> Nhân viên</span>";
                                            break;
                                        default:
                                            $role_display = "<span class='badge rounded-pill bg-secondary'><i class='fas fa-user me-1'></i> Khách hàng</span>";
                                            break;
                                    }
                                    // 2. Xử lý hiển thị Trạng thái (Phải nằm trong while)
                                    $status_display = ($user_status === 'active')
                                        ? "<span class='badge bg-success'>Hoạt động</span>"
                                        : "<span class='badge bg-danger'>Bị khóa</span>";

                                    // 3. Xử lý logic Nút bấm (Phải nằm trong while)
                                    if ($user_status === 'active') {
                                        $action_buttons = "
                                <a href='index.php?edit_user=$user_id' class='btn btn-sm btn-outline-primary' title='Sửa'>
                                    <i class='fas fa-edit'></i>
                                </a>
                                <button class='btn btn-sm btn-outline-danger' data-bs-toggle='modal' data-bs-target='#deleteModal_$user_id' title='Xóa'>
                                    <i class='fas fa-trash-alt'></i>
                                </button>";
                                    } else {
                                        $action_buttons = "
                                <a href='../functions/admin/accounts/restore_account.php?restore_user=$user_id'
                                class='btn btn-sm btn-outline-success px-3 d-flex align-items-center justify-content-center fw-bold shadow-sm' 
                                style='height: 35px; font-size: 13px;' title='Khôi phục tài khoản' onclick=\"return confirm('Bạn có chắc chắn muốn khôi phục tài khoản này không?')\">
                                    <i class='fas fa-undo-alt me-1'></i> Khôi phục
                                </a>";
                                    }
                                    // Xử lý hiển thị Trạng thái
                                    $status_display = ($user_status === 'active')
                                        ? "<span class='badge bg-success'>Hoạt động</span>"
                                        : "<span class='badge bg-danger'> Bị khóa</span>";

                                    echo "
                            <tr>
                                <td>$id_number</td>
                                <td class='text-start ps-4 fw-semibold'>$username</td>
                                <td>$user_email</td>
                                <td>$user_phone</td>
                                <td>$role_display</td>
                                <td>$status_display</td>
                                <td>
                                    <div class='d-flex justify-content-center gap-2'>
                                    $action_buttons
                                </div>
                                </td>
                            </tr>
                            <!-- Modal Xác Nhận Khóa Tài Khoản -->
                            <div class='modal fade' id='deleteModal_$user_id' tabindex='-1' aria-hidden='true'>
                                <div class='modal-dialog modal-dialog-centered'>
                                    <div class='modal-content'>
                                        <div class='modal-body text-center p-4'>
                                                <h4 class='text-danger mb-3'>Xác nhận?</h4>
                                                <p>Tài khoản <strong>$username</strong> sẽ bị khóa.</p>
                                                <div class='d-flex justify-content-center gap-2 mt-4'>
                                                    <button type='button' class='btn btn-light border' data-bs-dismiss='modal'>Hủy</button>
                                                    <a href='../functions/admin/accounts/delete_account.php?id=$user_id&action=deactivate' class='btn btn-danger px-4'>Đồng ý</a>
                                                </div>
                                            </div>
                                        
                                    </div>
                                </div>
                            </div>";
                                    $id_number++;
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

                <!-- --- HIỂN THỊ PHÂN TRANG --- -->
                <?php if ($total_pages > 1): ?>
                    <nav class="mt-4">
                        <ul class="pagination justify-content-center">
                            <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                                <a class="page-link" href="index.php?list_accounts&page=<?php echo $page - 1; ?>">Trước</a>
                            </li>

                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                <li class="page-item <?php echo ($page == $i) ? 'active' : ''; ?>">
                                    <a class="page-link"
                                        href="index.php?list_accounts&page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>

                            <li class="page-item <?php echo ($page >= $total_pages) ? 'disabled' : ''; ?>">
                                <a class="page-link" href="index.php?list_accounts&page=<?php echo $page + 1; ?>">Sau</a>
                            </li>
                        </ul>
                    </nav>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>

</html>