<?php
// Vẫn cần truy vấn lấy danh mục cha để đổ vào Select Box
$get_parents_query = "SELECT id, name FROM `categories` WHERE parent_id IS NULL AND deleted_at IS NULL ORDER BY name ASC";
$get_parents_result = mysqli_query($con, $get_parents_query);
include __DIR__ . '/../../functions/admin/categories/create_category.php';

?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">

            <!-- thông báo thành công hoặc thất bại -->
            <?php if (!empty($alert))
                echo $alert; ?>

            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 text-secondary">Thêm danh mục mới</h5>
                </div>
                <div class="card-body p-4">
                    <form action="" method="POST">

                        <div class="mb-4">
                            <label for="category_name" class="form-label fw-bold">Tên danh mục</label>

                            <input type="text" name="name" id="category_name"
                                class="form-control <?php echo !empty($name_error) ? 'is-invalid' : ''; ?>"
                                placeholder="Ví dụ: Điện thoại, Laptop..." value="<?php echo $_POST['name'] ?? ''; ?>"
                                required>

                            <?php if (!empty($name_error)): ?>
                                <div class="invalid-feedback fw-bold">
                                    <?php echo $name_error; ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- 2. Thuộc danh mục -->
                        <div class="mb-4">
                            <label for="parent_id" class="form-label fw-bold">Thuộc danh mục </label>
                            <select name="parent_id" id="parent_id" class="form-select">
                                <option value="">-- Nếu là danh mục chính thì không cần chọn --</option>
                                <?php
                                if (mysqli_num_rows($get_parents_result) > 0) {
                                    while ($parent = mysqli_fetch_assoc($get_parents_result)) {
                                        // Giữ lại lựa chọn cũ nếu form bị reload do lỗi
                                        $selected = (isset($_POST['parent_id']) && $_POST['parent_id'] == $parent['id']) ? 'selected' : '';
                                        echo "<option value='{$parent['id']}' $selected>{$parent['name']}</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>

                        <hr class="my-4">

                        <div class="d-flex gap-2">
                            <button type="submit" name="insert_category_btn" class="btn btn-primary px-4 shadow-sm">
                                <i class="fas fa-plus-circle me-2"></i> Thêm mới danh mục
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const nameInput = document.getElementById('category_name');
    nameInput.addEventListener('keyup', function () {
        let title = nameInput.value;
        let slug = title.toLowerCase()
            .normalize("NFD").replace(/[\u0300-\u036f]/g, "")
            .replace(/[^\w ]+/g, '')
            .replace(/ +/g, '-');

        const slugInput = document.getElementById('category_slug');
        if (slugInput) slugInput.value = slug;
    });
</script>