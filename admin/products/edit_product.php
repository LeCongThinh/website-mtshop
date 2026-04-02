<script src="https://cdn.ckeditor.com/4.22.1/standard/ckeditor.js"></script>
<?php
if (isset($_GET['edit_product'])) {
    $edit_id = (int) $_GET['edit_product'];

    // 1. Lấy thông tin cơ bản
    $get_product = "SELECT * FROM `products` WHERE id = '$edit_id' AND deleted_at IS NULL";
    $run_edit = mysqli_query($con, $get_product);
    $row_product = mysqli_fetch_assoc($run_edit);

    if (!$row_product) {
        echo "<script>alert('Sản phẩm không tồn tại!'); window.location.href='index.php?view_product';</script>";
        exit();
    }

    // 2. Lấy danh sách ảnh album
    $get_album = "SELECT * FROM `product_images` WHERE product_id = '$edit_id'";
    $res_album = mysqli_query($con, $get_album);

    // 3. Lấy thông số kỹ thuật
    $get_specs = "SELECT * FROM `product_specs` WHERE product_id = '$edit_id'";
    $res_specs = mysqli_query($con, $get_specs);
}
?>

<div class="container mt-4 mb-5">
    <div class="categ-header mb-4">
        <div class="sub-title d-flex align-items-center gap-2">
            <span class="shape bg-primary"
                style="width: 5px; height: 25px; display: inline-block; border-radius: 10px;"></span>
            <h3 class="mb-0">Cập nhật thông tin sản phẩm</h3>
        </div>
    </div>

    <form action="../functions/admin/products/update_product.php" method="POST" enctype="multipart/form-data" class="row g-3">
        <input type="hidden" name="product_id" value="<?php echo $row_product['id']; ?>">

        <div class="col-md-8">
            <div class="card shadow-sm p-4 border-0">
                <div class="mb-3">
                    <label class="form-label fw-bold">Tên sản phẩm</label>
                    <input type="text" name="name" class="form-control" value="<?php echo $row_product['name']; ?>"
                        required>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Danh mục</label>
                        <select name="category_id" class="form-select" required>
                            <?php
                            $get_cats = "SELECT * FROM `categories` WHERE `parent_id` IS NOT NULL AND `deleted_at` IS NULL";
                            $res_cats = mysqli_query($con, $get_cats);
                            while ($cat = mysqli_fetch_assoc($res_cats)) {
                                $selected = ($cat['id'] == $row_product['category_id']) ? "selected" : "";
                                echo "<option value='" . $cat['id'] . "' $selected>" . $cat['name'] . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Thương hiệu</label>
                        <select name="brand_id" class="form-select" required>
                            <?php
                            $get_brands = "SELECT * FROM `brands` WHERE `status` = 'active' AND `deleted_at` IS NULL";
                            $res_brands = mysqli_query($con, $get_brands);
                            while ($brand = mysqli_fetch_assoc($res_brands)) {
                                $selected = ($brand['id'] == $row_product['brand_id']) ? "selected" : "";
                                echo "<option value='" . $brand['id'] . "' $selected>" . $brand['name'] . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold">Giá bán</label>
                        <input type="number" name="price" class="form-control"
                            value="<?php echo $row_product['price']; ?>" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold">Giá khuyến mãi</label>
                        <input type="number" name="sale_price" class="form-control"
                            value="<?php echo $row_product['sale_price']; ?>">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold">Số lượng tồn kho</label>
                        <input type="number" name="stock" class="form-control"
                            value="<?php echo $row_product['stock']; ?>" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Mô tả sản phẩm</label>
                    <textarea name="description" id="editor1"><?php echo $row_product['description']; ?></textarea>
                </div>
            </div>

            <div class="card shadow-sm p-4 mt-4 border-0">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-microchip me-2"></i>Thông số kỹ thuật</h5>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="add-spec">
                        <i class="fas fa-plus me-1"></i> Thêm dòng
                    </button>
                </div>

                <div id="specs-container" class="specs-scroll-area">
                    <?php while ($spec = mysqli_fetch_assoc($res_specs)): ?>
                        <div class="row g-2 mb-2 spec-row">
                            <div class="col-md-5">
                                <input type="text" name="spec_key[]" class="form-control shadow-sm"
                                    value="<?php echo $spec['spec_key']; ?>">
                            </div>
                            <div class="col-md-6">
                                <input type="text" name="spec_value[]" class="form-control shadow-sm"
                                    value="<?php echo $spec['spec_value']; ?>">
                            </div>
                            <div class="col-md-1 text-end">
                                <button type="button" class="btn btn-outline-danger w-100 remove-spec">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm p-4 h-100 border-0">
                <div class="mb-4 text-center">
                    <label class="form-label fw-bold d-block text-start">Ảnh đại diện sản phẩm</label>
                    <div id="thumb-preview-container" class="border rounded p-2 mb-2 bg-light">
                        <img src="admin_images/<?php echo $row_product['thumbnail']; ?>" id="thumb-img"
                            class="img-fluid" style="max-height: 150px;">
                    </div>
                    <input type="file" name="thumbnail" id="thumb-input" class="form-control" accept="image/*">
                    <small class="text-muted d-block mt-1">Chọn ảnh mới để thay đổi ảnh đại diện.</small>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">Album ảnh sản phẩm</label>

                    <div id="album-preview-container" class="row g-2 mb-2 border rounded album-scroll-area">
                        <?php
                        mysqli_data_seek($res_album, 0);
                        while ($img = mysqli_fetch_assoc($res_album)):
                            ?>
                            <div class="col-4 preview-item old-img">
                                <img src="admin_images/<?php echo $img['image']; ?>"
                                    class="img-fluid border img-thumbnail-custom shadow-sm">
                                <span class="btn-remove-img"
                                    onclick="removeOldImage(this, <?php echo $img['id']; ?>)">×</span>
                                <input type="hidden" name="remove_old_images[]" value="" class="remove-id-input">
                            </div>
                        <?php endwhile; ?>

                    </div>

                    <input type="file" name="images[]" id="album-input" class="form-control mt-2" accept="image/*"
                        multiple>
                    <small class="text-muted italic">Tự động cộng dồn và hiện thanh cuộn nếu quá 6 ảnh.</small>
                </div>

                <hr>
                <div class="d-grid gap-2">
                    <button type="submit" name="update_product" class="btn btn-primary btn-lg shadow-sm">
                        <i class="fas fa-save me-1"></i> Lưu sản phẩm
                    </button>
                    <a href="index.php?view_product" class="btn btn-outline-secondary">Hủy bỏ</a>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    if (typeof CKEDITOR !== 'undefined') {
        CKEDITOR.replace('editor1', {
            height: 300,
            filebrowserUploadUrl: "includes/upload_ckeditor.php",
            filebrowserUploadMethod: 'form'
        });
    }
</script>
<script src="../assets/js/admin/edit_album_images.js"></script>
<script src="../assets/js/admin/handle_product_specs.js"></script>
<link rel="stylesheet" href="../assets/css/admin/edit_album_products.css">