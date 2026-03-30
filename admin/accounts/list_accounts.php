<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh sách tài khoản - MTShop</title>
    <link rel="shortcut icon" type="image/x-icon" href="../../../assets/images/logo/icon-laptopshop.png" />

</head>

<body>
    <div class="container mt-4">
        <div class="categ-header mb-4">
            <div class="sub-title d-flex align-items-center gap-2">
                <span class="shape bg-primary" style="width: 5px; height: 25px; display: inline-block; border-radius: 10px;"></span>
                <h3 class="mb-0">Danh sách tài khoản</h3>
            </div>
        </div>

        <?php
        // --- LOGIC PHÂN TRANG ---
        $limit = 10; // Số lượng record mỗi trang
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        if ($page < 1) $page = 1;
        $start = ($page - 1) * $limit;

        // Lấy tổng số dòng để tính số trang
        $total_res = mysqli_query($con, "SELECT COUNT(*) as total FROM `users`");
        $total_data = mysqli_fetch_assoc($total_res);
        $total_records = $total_data['total'];
        $total_pages = ceil($total_records / $limit);

        // Truy vấn dữ liệu: Mới nhất lên đầu (DESC) + Phân trang (LIMIT)
        $get_user_query = "SELECT * FROM `users` ORDER BY id DESC LIMIT $start, $limit";
        $get_user_result = mysqli_query($con, $get_user_query);
        ?>

        <div class="table-data shadow-sm rounded border overflow-hidden">
            <table class="table table-hover align-middle text-center mb-0">
                <thead class="table-dark">
                    <tr>
                        <th width="5%">STT</th>
                        <th>Tên tài khoản</th>
                        <th>Email</th>
                        <th>Số điện thoại</th>
                        <th>Chức vụ</th>
                        <th>Trạng thái</th>
                        <th width="15%">Hành động</th>
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

                            // Xử lý hiển thị Trạng thái
                            $status_display = ($user_status === 'active') 
                                ? "<span class='badge bg-success-subtle text-success border border-success px-3'><i class='fas fa-circle me-1' style='font-size: 8px;'></i> Hoạt động</span>"
                                : "<span class='badge bg-danger-subtle text-danger border border-danger px-3'><i class='fas fa-ban me-1'></i> Bị khóa</span>";

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
                                        <a href='index.php?edit_user=$user_id' class='btn btn-sm btn-outline-primary' title='Sửa'><i class='fas fa-edit'></i></a>
                                        <button class='btn btn-sm btn-outline-danger' data-bs-toggle='modal' data-bs-target='#deleteModal_$user_id' title='Xóa'><i class='fas fa-trash-alt'></i></button>
                                    </div>
                                </td>
                            </tr>";
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
                    <a class="page-link" href="index.php?list_accounts&page=<?php echo $i; ?>"><?php echo $i; ?></a>
                </li>
                <?php endfor; ?>

                <li class="page-item <?php echo ($page >= $total_pages) ? 'disabled' : ''; ?>">
                    <a class="page-link" href="index.php?list_accounts&page=<?php echo $page + 1; ?>">Sau</a>
                </li>
            </ul>
        </nav>
        <?php endif; ?>
    </div>
</body>

</html>