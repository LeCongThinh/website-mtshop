<?php if (isset($_GET['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show mb-3 shadow-sm border-0 small" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>
        <?php
        echo match ($_GET['error']) {
            'not_match' => 'Mật khẩu mới và xác nhận không khớp.',
            'wrong_current' => 'Mật khẩu hiện tại không chính xác.',
            'db_error' => 'Lỗi hệ thống, vui lòng thử lại sau.',
            default => 'Đã xảy ra lỗi.'
        };
        ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (isset($_GET['status']) && $_GET['status'] == 'password_updated'): ?>
    <div class="alert alert-success alert-dismissible fade show mb-3 shadow-sm border-0 small" role="alert">
        <i class="fas fa-check-circle me-2"></i>
        Mật khẩu đã được cập nhật thành công!
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>
<div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 bg-light">
                <h5 class="modal-title fw-bold" id="changePasswordModalLabel">
                    <i class="fas fa-lock me-2 text-primary"></i>Đổi mật khẩu
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="../functions/admin/accounts/update_password.php" method="POST">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Mật khẩu hiện tại</label>
                        <input type="password" name="current_password" class="form-control form-control-sm" required
                            placeholder="Nhập mật khẩu đang dùng">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Mật khẩu mới</label>
                        <input type="password" name="new_password" class="form-control form-control-sm" required
                            placeholder="Tối thiểu 6 ký tự">
                    </div>

                    <div class="mb-0">
                        <label class="form-label small fw-bold text-muted">Xác nhận mật khẩu mới</label>
                        <input type="password" name="confirm_password" class="form-control form-control-sm" required
                            placeholder="Nhập lại mật khẩu mới">
                    </div>
                </div>

                <div class="modal-footer border-0 bg-light justify-content-center p-3">
                    <button type="button" class="btn btn-outline-secondary btn-sm" style="min-width: 100px;"
                        data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" name="change_pwd_btn" class="btn btn-primary btn-sm"
                        style="min-width: 120px;">
                        Cập nhật ngay
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>