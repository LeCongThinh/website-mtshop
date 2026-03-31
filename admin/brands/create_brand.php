<?php
// Nạp file xử lý logic
include __DIR__ . '/../../functions/admin/brands/create_brand.php';
?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">

            <!-- Hiển thị thông báo Alert -->
            <?php echo $alert; ?>

            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 text-secondary">Thêm loại sản phẩm mới</h5>
                </div>
                <div class="card-body p-4">
                    <form action="" method="POST">
                        <div class="mb-4">
                            <label for="brand_name" class="form-label fw-bold">Tên loại sản phẩm</label>
                            <input type="text" name="brand_name" id="brand_name"
                                class="form-control <?php echo !empty($brand_error) ? 'is-invalid' : ''; ?>"
                                placeholder="Ví dụ: Apple, Samsung, Dell..."
                                value="<?php echo $_POST['brand_name'] ?? ''; ?>" required>

                            <!-- Input ẩn để gửi slug lên Server -->
                            <input type="hidden" name="brand_slug" id="brand_slug">

                            <!-- Thông báo lỗi validate trùng tên -->
                            <?php if (!empty($brand_error)): ?>
                                <div class="invalid-feedback fw-bold">
                                    <?php echo $brand_error; ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" name="add_brand_btn" class="btn btn-primary shadow-sm">
                                <i class="fas fa-plus-circle me-2"></i> Thêm loại sản phẩm
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Script xử lý Slug tự động -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const nameInput = document.getElementById('brand_name');
        const slugInput = document.getElementById('brand_slug');

        nameInput.addEventListener('keyup', function () {
            let title = nameInput.value;

            // Chuyển về chữ thường
            let slug = title.toLowerCase();

            // Xử lý các ký tự tiếng Việt có dấu sang không dấu
            slug = slug.replace(/á|à|ả|ạ|ã|ă|ắ|ằ|ẳ|ẵ|ặ|â|ấ|ầ|ẩ|ẫ|ậ/gi, 'a');
            slug = slug.replace(/é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ/gi, 'e');
            slug = slug.replace(/i|í|ì|ỉ|ĩ|ị/gi, 'i');
            slug = slug.replace(/ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ/gi, 'o');
            slug = slug.replace(/ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự/gi, 'u');
            slug = slug.replace(/ý|ỳ|ỷ|ỹ|ỵ/gi, 'y');
            slug = slug.replace(/đ/gi, 'd');

            // Xóa các ký tự đặc biệt
            slug = slug.replace(/[^\w ]+/g, '');

            // Thay khoảng trắng bằng dấu gạch ngang
            slug = slug.replace(/ +/g, '-');

            // Cập nhật giá trị vào input slug
            slugInput.value = slug;
        });
    });
</script>