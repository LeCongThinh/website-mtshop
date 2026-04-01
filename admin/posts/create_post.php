<!-- Nhúng CKEditor từ CDN -->
<script src="https://cdn.ckeditor.com/4.22.1/standard/ckeditor.js"></script>

<div class="container-fluid">
    <div class="categ-header mb-2">
        <div class="sub-title d-flex align-items-center gap-2">
            <span class="shape bg-primary" 
                  style="width: 5px; height: 25px; display: inline-block; border-radius: 10px;"></span>
            <h3 class="mb-0">Thêm bài viết mới</h3>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <!-- Action trỏ tới file xử lý logic create -->
            <form action="../functions/admin/posts/create_post.php" method="POST" enctype="multipart/form-data">
                
                <!-- 1. Ảnh bài viết & Preview -->
                <div class="mb-4">
                    <label class="form-label fw-bold d-block">Ảnh bài viết</label>
                    <div class="mb-3">
                        <div class="position-relative" style="width: 300px;">
                            <!-- Ảnh mặc định khi chưa chọn -->
                            <img id="main-preview" src="admin_images/post_thumbnails/undefined.png" 
                                 class="rounded border shadow-sm"
                                 style="width: 300px; height: 200px; object-fit: cover; display: block;">
                            
                            <span id="image-badge" class="badge bg-secondary position-absolute bottom-0 start-0 m-1 opacity-75">
                                Chưa chọn ảnh
                            </span>
                        </div>
                    </div>
                    <input type="file" name="thumbnail" id="thumbnail" class="form-control" 
                           accept="image/*" required onchange="previewImage(this);">
                </div>

                <!-- 2. Tiêu đề bài viết -->
                <div class="mb-4">
                    <label for="title" class="form-label fw-bold">Tiêu đề bài viết</label>
                    <input type="text" name="title" id="title" 
                           class="form-control <?php echo (isset($_GET['error']) && $_GET['error'] == 'title_exists') ? 'is-invalid' : ''; ?>" 
                           placeholder="Nhập tiêu đề bài viết..." required>
                    
                    <?php if (isset($_GET['error']) && $_GET['error'] == 'title_exists'): ?>
                        <div class="invalid-feedback">Tiêu đề này đã tồn tại, vui lòng đặt tên khác.</div>
                    <?php endif; ?>
                </div>

                <!-- 3. Nội dung bài viết (CKEditor) -->
                <div class="mb-4">
                    <label for="post_content" class="form-label fw-bold">Nội dung chi tiết</label>
                    <textarea name="content" id="post_content" class="form-control"></textarea>
                </div>

                <!-- Nút điều hướng -->
                <div class="d-flex gap-2">
                    <button type="submit" name="create_post_btn" class="btn btn-primary px-4">
                        <i class="fas fa-plus-circle me-1"></i> Thêm mới bài viết
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Cấu hình CKEditor
    CKEDITOR.replace('post_content', {
        height: 450,
        // Cấu hình upload ảnh trực tiếp trong CKEditor nếu bạn đã có file upload_image_ck.php
        filebrowserUploadUrl: "../functions/admin/posts/upload_image_ck.php", 
        filebrowserUploadMethod: 'form',
        placeholder: 'Bắt đầu viết nội dung bài viết tại đây...'
    });

    // Script Preview ảnh trước khi upload
    function previewImage(input) {
        const preview = document.getElementById('main-preview');
        const badge = document.getElementById('image-badge');

        if (input.files && input.files[0]) {
            const reader = new FileReader();

            reader.onload = function (e) {
                preview.src = e.target.result;
                badge.innerText = "Ảnh đã chọn";
                badge.className = "badge bg-success position-absolute bottom-0 start-0 m-1 opacity-75";
            }

            reader.readAsDataURL(input.files[0]);
        } else {
            // Nếu hủy chọn ảnh
            preview.src = "admin_images/post_thumbnails/undefined.png";
            badge.innerText = "Chưa chọn ảnh";
            badge.className = "badge bg-secondary position-absolute bottom-0 start-0 m-1 opacity-75";
        }
    }
</script>