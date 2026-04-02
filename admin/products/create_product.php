<script src="https://cdn.ckeditor.com/4.22.1/standard/ckeditor.js"></script>

<div class="container mt-4 mb-5">
    <div class="categ-header mb-4">
        <div class="sub-title d-flex align-items-center gap-2">
            <span class="shape bg-primary"
                style="width: 5px; height: 25px; display: inline-block; border-radius: 10px;"></span>
            <h3 class="mb-0">Thêm sản phẩm mới</h3>
        </div>
    </div>

    <form action="../functions/admin/products/create_product.php" method="POST" enctype="multipart/form-data" class="row g-3">
        <div class="col-md-8">
            <div class="card shadow-sm p-4">
                <div class="mb-3">
                    <label class="form-label fw-bold">Tên sản phẩm</label>
                    <input type="text" name="name" class="form-control" placeholder="Nhập tên sản phẩm..." required>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Danh mục</label>
                        <select name="category_id" class="form-select" required>
                            <option value="">Chọn danh mục</option>
                            <?php
                            // Lấy ra tên danh mục có parent_id khác null và trường deleted_at là null để chỉ hiển thị các danh mục con
                            $get_cats = "SELECT * FROM `categories` 
                            WHERE `parent_id` IS NOT NULL 
                            AND `deleted_at` IS NULL 
                            ORDER BY `name` ASC";
                            $res_cats = mysqli_query($con, $get_cats);
                            if (mysqli_num_rows($res_cats) > 0) {
                                while ($row = mysqli_fetch_assoc($res_cats)) {
                                    echo "<option value='" . $row['id'] . "'>" . $row['name'] . "</option>";
                                }
                            } else {
                                echo "<option value='' disabled>Không có danh mục con nào</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Loại sản phẩm</label>
                        <select name="brand_id" class="form-select" required>
                            <option value="">Chọn loại sản phẩm</option>
                            <?php
                            $get_brands = "SELECT * FROM `brands` 
                            WHERE `status` = 'active' 
                            AND `deleted_at` IS NULL 
                            ORDER BY `name` ASC";
                            $res_brands = mysqli_query($con, $get_brands);
                            while ($row = mysqli_fetch_assoc($res_brands)) {
                                echo "<option value='" . $row['id'] . "'>" . $row['name'] . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold">Giá bán</label>
                        <input type="number" name="price" class="form-control" placeholder="0" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold">Giá khuyến mãi</label>
                        <input type="number" name="sale_price" class="form-control" placeholder="0">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold">Số lượng tồn kho</label>
                        <input type="number" name="stock" class="form-control" placeholder="0" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Mô tả sản phẩm</label>
                    <textarea name="description" id="editor1"></textarea>
                </div>
            </div>

            <div class="card shadow-sm p-4 mt-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-microchip me-2"></i>Thông số kỹ thuật</h5>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="add-spec">
                        <i class="fas fa-plus me-1"></i> Thêm dòng
                    </button>
                </div>

                

                <div id="specs-container" class="specs-scroll-area">
                    <div class="row g-2 mb-2 spec-row">
                        <div class="col-md-5">
                            <input type="text" name="spec_key[]" class="form-control shadow-sm"
                                placeholder="Tên thông số (VD: CPU)">
                        </div>
                        <div class="col-md-6">
                            <input type="text" name="spec_value[]" class="form-control shadow-sm"
                                placeholder="Giá trị (VD: Core i7)">
                        </div>
                        <div class="col-md-1">
                            <button type="button" class="btn btn-outline-danger w-100 remove-spec">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm p-4 h-100">
                <div class="mb-4">
                    <label class="form-label fw-bold">Ảnh đại diện sản phẩm</label>
                    <input type="file" name="thumbnail" id="thumb-input" class="form-control" accept="image/*" required>
                    <div id="thumb-preview" class="mt-2 text-center border rounded p-2 d-none">
                        <img src="" class="img-fluid" style="max-height: 200px;">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Album ảnh sản phẩm</label>
                    <input type="file" name="images[]" id="gallery-input" class="form-control" accept="image/*" multiple>
                    <div id="gallery-preview" class="mt-2 row g-2 d-none">
                    </div>
                </div>

                <hr>
                <div class="d-grid gap-2">
                    <button type="submit" name="submit_product" class="btn btn-primary btn-lg">Thêm sản phẩm</button>
                </div>
            </div>
        </div>
    </form>
</div>
<link rel="stylesheet" href="../assets/css/admin/handle_product.css">
<!-- Load album ảnh sản phẩm -->
<script src="../assets/js/admin/handle_album_images.js"></script>
<!-- Load xử lý thông số kỹ thuật -->
<script src="../assets/js/admin/handle_product_specs.js"></script>
<script>
    // 1. Khởi tạo CKEditor
    CKEDITOR.replace('editor1');

    // 2. Preview Ảnh đại diện (Single)
    document.getElementById('thumb-input').onchange = evt => {
        const [file] = evt.target.files;
        if (file) {
            const preview = document.getElementById('thumb-preview');
            preview.classList.remove('d-none');
            preview.querySelector('img').src = URL.createObjectURL(file);
        }
    }
</script>