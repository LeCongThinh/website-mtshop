<script src="https://cdn.ckeditor.com/4.22.1/standard/ckeditor.js"></script>
<?php
if (isset($_GET['edit_post'])) {
    $edit_id = mysqli_real_escape_string($con, $_GET['edit_post']);

    // Lấy dữ liệu bài viết cũ
    $get_post_query = "SELECT * FROM `posts` WHERE id = '$edit_id' LIMIT 1";
    $res_post = mysqli_query($con, $get_post_query);

    if (mysqli_num_rows($res_post) > 0) {
        $post_data = mysqli_fetch_assoc($res_post);
    } else {
        echo "<script>window.location.href='index.php?view_post';</script>";
        exit();
    }
    $folder_path = "admin_images/post_thumbnails/";
    $folder_url = "admin_images/post_thumbnails/";
    $default_image_url = $folder_url . "undefined.png";

    $stored_thumbnail = $post_data['thumbnail'];
    if (strpos($stored_thumbnail, 'post_thumbnails/') === 0) {
        $stored_thumbnail = basename($stored_thumbnail);
    }

    if (!empty($stored_thumbnail) && file_exists($folder_path . $stored_thumbnail)) {
        $display_image = $folder_url . $stored_thumbnail;
        $label_text = "Ảnh hiện tại";
    } else {
        $display_image = $default_image_url;
        $label_text = "Chưa có ảnh (Mặc định)";
    }
}
?>

<div class="container-fluid">
    
    <div class="categ-header mb-2">
        <div class="sub-title d-flex align-items-center gap-2">
            <span class="shape bg-primary"
                style="width: 5px; height: 25px; display: inline-block; border-radius: 10px;"></span>
            <h3 class="mb-0">Cập nhật bài viết</h3>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <form action="../functions/admin/posts/update_post.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="post_id" value="<?php echo $post_data['id']; ?>">
                <!-- 2. Ảnh bài viết -->
                <div class="mb-4">
                    <label class="form-label fw-bold d-block">Ảnh bài viết</label>
                    <div class="mb-3">
                        <div class="position-relative" style="width: 300px;">
                            <img id="main-preview" src="<?php echo $display_image; ?>" class="rounded border shadow-sm"
                                style="width: 300px; height: 200px; object-fit: cover; display: block;">
                            <span id="image-badge"
                                class="badge bg-dark position-absolute bottom-0 start-0 m-1 opacity-75">
                                <?php echo $label_text; ?>
                            </span>
                        </div>
                    </div>
                    <label for="thumbnail" class="form-label fw-bold">Ảnh bài viết</label>
                    <input type="file" name="thumbnail" id="thumbnail" class="form-control" accept="image/*"
                        onchange="previewImage(this);">
                </div>

                <!-- 1. Tiêu đề bài viết -->
                <div class="mb-4">
                    <label for="title" class="form-label fw-bold">Tiêu đề bài viết</label>
                    <input type="text" name="title" id="title"
                        class="form-control <?php echo (isset($_GET['error']) && $_GET['error'] == 'title_exists') ? 'is-invalid' : ''; ?>"
                        value="<?php echo htmlspecialchars($post_data['title']); ?>" required>

                    <?php if (isset($_GET['error']) && $_GET['error'] == 'title_exists'): ?>
                        <div class="invalid-feedback">Tiêu đề này đã tồn tại, vui lòng đặt tên khác.</div>
                    <?php endif; ?>
                </div>

                <!-- 3. Nội dung bài viết (CKEditor) -->
                <div class="mb-4">
                    <label for="content" class="form-label fw-bold">Nội dung chi tiết</label>
                    <textarea name="content" id="post_content" class="form-control">
                        <?php echo $post_data['content']; ?>
                    </textarea>
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" name="update_post_btn" class="btn btn-primary px-4">
                        <i class="fas fa-save me-1"></i> Lưu bài viết
                    </button>
                    <a href="index.php?view_post" class="btn btn-light border px-4 text-secondary">Hủy bỏ</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    CKEDITOR.replace('post_content', {
        height: 400,
        filebrowserUploadUrl: "../functions/admin/posts/upload_image_ck.php",
        filebrowserUploadMethod: 'form'
    });

    // Hàm xử lý hiển thị ảnh
    function previewImage(input) {
        const preview = document.getElementById('main-preview');
        const badge = document.getElementById('image-badge');

        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function (e) {
                preview.src = e.target.result;
                badge.innerText = "Ảnh mới đang chọn";
                badge.className = "badge bg-success position-absolute bottom-0 start-0 m-1 opacity-75";
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>