<?php
// Nạp logic xử lý update
include __DIR__ . '/../../functions/admin/categories/update_category.php';

// Lấy dữ liệu cũ để hiển thị lên form
if (isset($_GET['edit_category'])) {
    $edit_id = $_GET['edit_category'];
    $get_cat = mysqli_query($con, "SELECT * FROM `categories` WHERE `id` = '$edit_id'");
    $cat_data = mysqli_fetch_assoc($get_cat);

    // Lấy danh sách danh mục cha (để đổ vào select box)
    $parents_res = mysqli_query($con, "SELECT id, name FROM `categories` WHERE `parent_id` IS NULL AND `id` != '$edit_id' AND `deleted_at` IS NULL");
}
?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <?php echo $alert; ?>
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 text-secondary">Cập nhật danh mục</h5>
                </div>
                <div class="card-body p-4">
                    <form action="" method="POST">
                        <!-- Input ẩn giữ ID -->
                        <input type="hidden" name="category_id" value="<?php echo $cat_data['id']; ?>">

                        <!-- 1. Tên danh mục -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">Tên danh mục</label>
                            <input type="text" name="name" id="category_name" 
                                class="form-control <?php echo $name_error ? 'is-invalid' : ''; ?>"
                                value="<?php echo $_POST['name'] ?? $cat_data['name']; ?>" required>
                            
                            <?php if ($name_error): ?>
                                <div class="invalid-feedback"><?php echo $name_error; ?></div>
                            <?php endif; ?>
                        </div>

                        <!-- 2. Danh mục cha -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">Thuộc danh mục</label>
                            <select name="parent_id" class="form-select">
                                <option value="">-- Là danh mục gốc --</option>
                                <?php 
                                while ($p = mysqli_fetch_assoc($parents_res)) {
                                    $selected = ($p['id'] == $cat_data['parent_id']) ? 'selected' : '';
                                    echo "<option value='{$p['id']}' $selected>{$p['name']}</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" name="update_category_btn" class="btn btn-primary px-4">Lưu thay đổi</button>
                            <a href="index.php?view_category" class="btn btn-light border px-4">Hủy</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Logic tạo slug tương tự bên file Create
    const nameInp = document.getElementById('category_name');
    nameInp.addEventListener('keyup', function() {
        let title = this.value;
        let slug = title.toLowerCase()
            .replace(/á|à|ả|ạ|ã|ă|ắ|ằ|ẳ|ẵ|ặ|â|ấ|ầ|ẩ|ẫ|ậ/gi, 'a')
            .replace(/é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ/gi, 'e')
            .replace(/i|í|ì|ỉ|ĩ|ị/gi, 'i')
            .replace(/ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ/gi, 'o')
            .replace(/ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự/gi, 'u')
            .replace(/ý|ỳ|ỷ|ỹ|ỵ/gi, 'y')
            .replace(/đ/gi, 'd')
            .replace(/[^\w ]+/g, '')
            .replace(/ +/g, '-');
        // Bạn có thể thêm 1 input hidden cho slug nếu cần dùng JS cập nhật trực tiếp
    });
</script>