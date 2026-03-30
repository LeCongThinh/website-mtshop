<?php
    // Gọi file xử lý logic
    include('../functions/admin/accounts/create_account.php');
?>

<div class="container mt-4">
    <div class="card mx-auto shadow-sm border-0 rounded-3" style="max-width: 700px;">
        <div class="card-header bg-white py-3 border-0">
            <h4 class="text-center mb-0 fw-bold text-dark">Thêm Mới Tài Khoản</h4>
        </div>
        <div class="card-body p-4 bg-light">
            <!-- Form giữ nguyên, action để trống để gửi dữ liệu về chính trang này -->
            <form action="" method="post" enctype="multipart/form-data">
                <div class="mb-3">
                    <label class="form-label small fw-bold">Họ và tên</label>
                    <input type="text" name="user_name" class="form-control" placeholder="Nhập họ tên đầy đủ" required>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold">Email</label>
                        <input type="email" name="user_email" class="form-control" placeholder="Nhập email..." required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold">Số điện thoại</label>
                        <input type="text" name="user_phone" class="form-control" placeholder="Nhập số điện thoại..." required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold">Mật khẩu</label>
                        <input type="password" name="user_password" class="form-control" placeholder="Nhập mật khẩu" required>
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

                <div class="mb-3">
                    <label class="form-label small fw-bold">Ảnh đại diện (Avatar)</label>
                    <input type="file" name="user_avatar" class="form-control" accept="image/*" required>
                </div>

                <div class="mb-4">
                    <label class="form-label small fw-bold">Địa chỉ</label>
                    <textarea name="user_address" class="form-control" rows="2" placeholder="Số nhà, tên đường..."></textarea>
                </div>

                <div class="d-grid">
                    <input type="submit" name="insert_account" class="btn btn-primary py-2 fw-bold" value="Xác nhận thêm tài khoản">
                </div>
            </form>
        </div>
    </div>
</div>