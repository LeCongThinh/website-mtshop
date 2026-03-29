<?php
if (isset($_POST['insert_account'])) {
    // Làm sạch dữ liệu đầu vào
    $user_name     = mysqli_real_escape_string($con, $_POST['user_name']);
    $user_email    = mysqli_real_escape_string($con, $_POST['user_email']);
    $user_password = $_POST['user_password'];
    $hash_password = password_hash($user_password, PASSWORD_DEFAULT);
    $user_phone    = mysqli_real_escape_string($con, $_POST['user_phone']);
    $user_address  = mysqli_real_escape_string($con, $_POST['user_address']);
    $user_role     = $_POST['user_role'];
    $user_status   = 'active';

    // 1. Kiểm tra email trùng lặp trước khi xử lý file (để đỡ tốn tài nguyên)
    $select_query = "SELECT * FROM `users` WHERE email='$user_email'";
    $result_select = mysqli_query($con, $select_query);

    if (mysqli_num_rows($result_select) > 0) {
        echo "<script>alert('Email này đã tồn tại!')</script>";
    } else {
        // 2. Xử lý File Upload
        if (isset($_FILES['user_avatar']) && $_FILES['user_avatar']['error'] == 0) {
            $user_avatar = $_FILES['user_avatar']['name'];
            $user_avatar_tmp = $_FILES['user_avatar']['tmp_name'];

            // Tạo tên file duy nhất để tránh ghi đè
            $file_extension = pathinfo($user_avatar, PATHINFO_EXTENSION);
            $new_file_name = date('YmdHis') . '_' . uniqid() . '.' . $file_extension;

            // XÁC ĐỊNH ĐƯỜNG DẪN TUYỆT ĐỐI (Dùng cho lệnh move_uploaded_file)
            // dirname(__DIR__) sẽ đưa bạn ra thư mục 'admin' từ thư mục 'accounts'
            $admin_path = dirname(__DIR__); 
            $upload_directory = $admin_path . DIRECTORY_SEPARATOR . 'admin_images' . DIRECTORY_SEPARATOR . 'avatars' . DIRECTORY_SEPARATOR;

            // Kiểm tra và tạo thư mục nếu chưa có
            if (!is_dir($upload_directory)) {
                mkdir($upload_directory, 0777, true);
            }

            $physical_path = $upload_directory . $new_file_name;

            // 3. Thực hiện di chuyển file vào thư mục vật lý
            if (move_uploaded_file($user_avatar_tmp, $physical_path)) {
                
                // ĐƯỜNG DẪN LƯU VÀO DATABASE (Đúng yêu cầu của bạn)
                $db_path = "avatars/" . $new_file_name;

                // 4. Insert dữ liệu vào DB
                $insert_query = "INSERT INTO `users` (avatar, name, email, password, phone, address, role, status, created_at) 
                                 VALUES ('$db_path', '$user_name', '$user_email', '$hash_password', '$user_phone', '$user_address', '$user_role', '$user_status', NOW())";

                $sql_execute = mysqli_query($con, $insert_query);

                if ($sql_execute) {
                    echo "<script>alert('Thêm tài khoản thành công!')</script>";
                    echo "<script>window.open('index.php?list_accounts','_self')</script>";
                } else {
                    echo "<script>alert('Lỗi truy vấn Database!')</script>";
                }
            } else {
                echo "<script>alert('Lỗi: Không thể ghi file vào hệ thống. Hãy kiểm tra lại thư mục admin_images/avatars/')</script>";
            }
        } else {
            echo "<script>alert('Vui lòng chọn ảnh đại diện!')</script>";
        }
    }
}
?>

<div class="container mt-4">
    <div class="card mx-auto shadow-sm border-0 rounded-3" style="max-width: 700px;">
        <div class="card-header bg-white py-3 border-0">
            <h4 class="text-center mb-0 fw-bold text-dark">Thêm Mới Tài Khoản</h4>
        </div>
        <div class="card-body p-4 bg-light">
            <!-- QUAN TRỌNG: Phải có enctype="multipart/form-data" -->
            <form action="" method="post" enctype="multipart/form-data">

                <!-- Hàng 1: Họ tên -->
                <div class="mb-3">
                    <label class="form-label small fw-bold">Họ và tên</label>
                    <input type="text" name="user_name" class="form-control" placeholder="Nhập họ tên đầy đủ" required>
                </div>

                <!-- Hàng 2: Email & Số điện thoại (Chia đôi) -->
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold">Email</label>
                        <input type="email" name="user_email" class="form-control" placeholder="Nhập email..." required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold">Số điện thoại</label>
                        <input type="text" name="user_phone" class="form-control" placeholder="Nhập số điện thoại..."
                            required>
                    </div>
                </div>

                <!-- Hàng 3: Mật khẩu & Chức vụ (Chia đôi) -->
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold">Mật khẩu</label>
                        <input type="password" name="user_password" class="form-control" placeholder="Nhập mật khẩu"
                            required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold">Chức vụ</label>
                        <select name="user_role" class="form-select">
                            <option value="customer">Khách hàng</option>
                            <option value="staff">Nhân viên</option>
                            <option value="admin">Quản trị viên</option>
                        </select>
                    </div>
                </div>

                <!-- Hàng 4: Ảnh đại diện -->
                <div class="mb-3">
                    <label class="form-label small fw-bold">Ảnh đại diện (Avatar)</label>
                    <input type="file" name="user_avatar" class="form-control" accept="image/*" required>
                </div>

                <!-- Hàng 5: Địa chỉ -->
                <div class="mb-4">
                    <label class="form-label small fw-bold">Địa chỉ</label>
                    <textarea name="user_address" class="form-control" rows="2"
                        placeholder="Số nhà, tên đường..."></textarea>
                </div>

                <!-- Nút bấm -->
                <div class="d-grid">
                    <input type="submit" name="insert_account" class="btn btn-primary py-2 fw-bold"
                        value="Xác nhận thêm tài khoản">
                </div>
            </form>
        </div>
    </div>
</div>