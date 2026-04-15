<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="card border-0 shadow-sm" style="border-radius: 20px;">
                <div class="card-body p-0">
                    <div class="row g-0">
                        <div class="col-md-4 bg-light p-5 text-center" style="border-radius: 20px 0 0 20px;">
                            <form action="index.php?page=update-profile" method="POST" enctype="multipart/form-data">
                                <div class="position-relative d-inline-block mb-4">
                                    <?php
                                    // Database đã có "avatars/ten_file.jpg", nên ta nối thêm phần đầu
                                    $img_path = !empty($user['avatar'])
                                        ? "admin/admin_images/" . $user['avatar']
                                        : "admin/admin_images/avatars/default-avatar.png";
                                    ?>
                                    <img src="<?= $img_path ?>" id="preview" class="rounded-circle object-fit-cover shadow"
                                        style="width: 160px; height: 160px; border: 5px solid #fff;">

                                    <label for="avatar-input" class="position-absolute bottom-0 end-0 bg-primary text-white rounded-circle d-flex align-items-center justify-content-center shadow-sm"
                                        style="width: 40px; height: 40px; cursor: pointer; border: 3px solid #fff;">
                                        <i class="bi bi-camera-fill"></i>
                                    </label>
                                    <input type="file" id="avatar-input" name="avatar" class="d-none" accept="image/*"
                                        onchange="document.getElementById('preview').src = window.URL.createObjectURL(this.files[0])">
                                </div>

                                <h5 class="fw-bold mb-1"><?= htmlspecialchars($user['name']) ?></h5>
                                <p class="text-muted small mb-4"><?= htmlspecialchars($user['email']) ?></p>

                                <div class="list-group list-group-flush text-start rounded-3">
                                    <a href="index.php?page=profile" class="list-group-item list-group-item-action active border-0">
                                        <i class="bi bi-person me-2"></i> Thông tin tài khoản
                                    </a>
                                    <a href="index.php?page=my-orders" class="list-group-item list-group-item-action border-0">
                                        <i class="bi bi-bag me-2"></i> Đơn hàng đã mua
                                    </a>
                                </div>
                        </div>

                        <div class="col-md-8 p-5">
                            <h4 class="fw-bold text-dark mb-4">Thiết lập hồ sơ</h4>

                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label small fw-bold text-uppercase text-muted">Họ và tên</label>
                                    <input type="text" name="name" class="form-control form-control-lg bg-light border-0"
                                        style="font-size: 1rem;" value="<?= htmlspecialchars($user['name']) ?>" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-uppercase text-muted">Số điện thoại</label>
                                    <input type="text" name="phone" class="form-control form-control-lg bg-light border-0"
                                        style="font-size: 1rem;" value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-uppercase text-muted">Email (Cố định)</label>
                                    <input type="text" class="form-control form-control-lg bg-light border-0"
                                        style="font-size: 1rem;" value="<?= htmlspecialchars($user['email']) ?>" readonly disabled>
                                </div>

                                <div class="col-12">
                                    <label class="form-label small fw-bold text-uppercase text-muted">Địa chỉ giao hàng mặc định</label>
                                    <textarea name="address" class="form-control form-control-lg bg-light border-0"
                                        style="font-size: 1rem;" rows="3"><?= htmlspecialchars($user['address'] ?? '') ?></textarea>
                                </div>

                                <div class="col-12 mt-4">
                                    <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold shadow-sm" style="border-radius: 12px;">
                                        Lưu thay đổi
                                    </button>
                                </div>
                            </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .form-control:focus {
        background-color: #fff !important;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.1);
        border: 1px solid #0d6efd !important;
    }

    .object-fit-cover {
        object-fit: cover;
    }
</style>