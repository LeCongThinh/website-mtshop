<?php
// 1. Gọi file xử lý logic để lấy dữ liệu và xử lý UPDATE
include('../functions/admin/accounts/update_account.php');

// Biến $row_user và các thông tin đã được file logic chuẩn bị dựa trên $_GET['edit_user']
?>
<!-- Hiển thị thông báo Bootstrap -->
<?php if (isset($status_update) && $status_update == "success"): ?>
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" id="auto-close-alert">
        <i class="fas fa-check-circle me-2"></i> <strong>Thành công!</strong> Đã cập nhật thông tin tài khoản.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <script>
        // Tự động chuyển hướng sau 1.5 giây nếu muốn
        setTimeout(function () {
            window.location.href = 'index.php?list_accounts';
        }, 1500);
    </script>
<?php elseif (isset($status_update) && $status_update == "error"): ?>
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i> <strong>Lỗi!</strong> Không thể cập nhật dữ liệu.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-lg border-0 rounded-4 overflow-hidden">

                <!-- Header -->
                <div class="card-header bg-secondary py-3 px-4 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-white fw-bold">
                        <i class="fas fa-user-edit me-2"></i>Chỉnh sửa hồ sơ người dùng
                    </h5>
                </div>

                <div class="card-body p-0">
                    <form action="" method="post" enctype="multipart/form-data">
                        <div class="row g-0">
                            <div class="col-md-4 bg-light border-end p-4 text-center">
                                <div class="mb-4">
                                    <label class="form-label d-block small fw-bold text-uppercase text-muted mb-3">Ảnh
                                        đại diện</label>
                                    <div class="position-relative d-inline-block">
                                        <img src="admin_images/<?= !empty($old_avatar) ? $old_avatar : 'avatars/blank_user.png' ?>"
                                            class="rounded-circle border border-4 border-white shadow"
                                            style="width: 180px; height: 180px; object-fit: cover;" id="preview-avatar">
                                    </div>
                                    <div class="mt-3 px-3">
                                        <input type="file" name="user_avatar"
                                            class="form-control form-control-sm shadow-none" accept="image/*"
                                            onchange="previewImage(event)">
                                        <div class="form-text mt-2 small italic text-muted">Định dạng: JPG, PNG. Tối đa
                                            2MB.</div>
                                    </div>
                                </div>

                                <hr class="my-4 mx-3">

                                <div class="px-3 text-start">
                                    <div class="mb-3">
                                        <label class="form-label small fw-bold text-muted">Chức vụ hệ thống</label>
                                        <select name="user_role" class="form-select border-0 shadow-sm rounded-3">
                                            <option value="customer" <?= ($user_role == 'customer') ? 'selected' : '' ?>>
                                                Khách hàng</option>
                                            <option value="staff" <?= ($user_role == 'staff') ? 'selected' : '' ?>>Nhân
                                                viên</option>
                                            <option value="admin" <?= ($user_role == 'admin') ? 'selected' : '' ?>>Quản trị
                                                viên</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-8 p-4 p-lg-5">
                                <h6 class="fw-bold text-dark border-bottom pb-2 mb-4">Thông tin cá nhân</h6>

                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label small fw-bold">Họ và tên</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-white border-end-0"><i
                                                    class="far fa-user text-muted"></i></span>
                                            <input type="text" name="user_name"
                                                class="form-control border-start-0 ps-0 shadow-none"
                                                value="<?= htmlspecialchars($user_name) ?>" required>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label small fw-bold text-muted">Địa chỉ Email</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0 shadow-none opacity-75">
                                                <i class="far fa-envelope text-muted"></i>
                                            </span>
                                            <input type="email" name="user_email"
                                                class="form-control border-start-0 ps-0 shadow-none bg-light"
                                                value="<?= htmlspecialchars($user_email) ?>" readonly>
                                        </div>

                                    </div>

                                    <div class="col-12">
                                        <label class="form-label small fw-bold">Số điện thoại</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-white border-end-0"><i
                                                    class="fas fa-phone-alt text-muted"></i></span>
                                            <input type="text" name="user_phone"
                                                class="form-control border-start-0 ps-0 shadow-none"
                                                value="<?= htmlspecialchars($user_phone) ?>" required>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label small fw-bold">Địa chỉ liên hệ</label>
                                        <textarea name="user_address" class="form-control shadow-none" rows="3"
                                            placeholder="Số nhà, tên đường, quận/huyện..."><?= htmlspecialchars($user_address) ?></textarea>
                                    </div>
                                </div>

                                <<div class="mt-5 d-flex gap-3 justify-content-end">
                                    <a href="index.php?list_accounts"
                                        class="btn btn-outline-danger px-4 fw-bold shadow-sm">
                                        <i class="fas fa-times me-2"></i>Hủy bỏ
                                    </a>
                                    <button type="submit" name="update_account"
                                        class="btn btn-outline-primary px-5 fw-bold shadow-sm">
                                        <i class="fas fa-save me-2"></i>Lưu thay đổi
                                    </button>
                            </div>
                        </div>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>
</div>

<!-- Script xem trước ảnh khi chọn file -->
<script>
    function previewImage(event) {
        var reader = new FileReader();
        reader.onload = function () {
            var output = document.getElementById('preview-avatar');
            output.src = reader.result;
        };
        reader.readAsDataURL(event.target.files[0]);
    }
</script>