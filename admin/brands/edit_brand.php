<?php
if (isset($_GET['edit_brand'])) {
    $edit_id = mysqli_real_escape_string($con, $_GET['edit_brand']);

    $get_brand = "SELECT * FROM `brands` WHERE id = '$edit_id'";
    $res_brand = mysqli_query($con, $get_brand);
    $row_brand = mysqli_fetch_assoc($res_brand);

    if ($row_brand) {
        $brand_name = $row_brand['name'];
    } else {
        echo "<script>window.open('index.php?view_brand','_self')</script>";
    }
}
?>

<div class="container">
    <?php
    if (isset($_GET['msg'])) {
        if ($_GET['msg'] == 'duplicate') {
            echo '
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i> 
                <strong>Lỗi!</strong> Loại sản phẩm đã tồn tại trong hệ thống.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>';
        } elseif ($_GET['msg'] == 'update_success') {
            echo '
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i> 
                <strong>Thành công!</strong> Cập nhật loại sản phẩm thành công.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>';
        } elseif ($_GET['msg'] == 'update_error') {
            echo '
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-times-circle me-2"></i> 
                <strong>Lỗi!</strong> Không thể cập nhật dữ liệu vào lúc này.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>';
        }
    }
    ?>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 text-secondary">Cập nhật loại sản phẩm</h5>
                </div>

                <div class="card-body p-4">
                    <form action="../functions/admin/brands/update_brand.php" method="POST">
                        <input type="hidden" name="brand_id" value="<?php echo $edit_id; ?>">

                        <div class="mb-3">
                            <label for="name" class="form-label fw-bold">Tên loại sản phẩm mới</label>

                            <?php
                            // Kiểm tra xem có lỗi 'name' từ URL không
                            $is_invalid = isset($_GET['error']) && $_GET['error'] == 'name';
                            // Lấy lại giá trị người dùng vừa nhập sai để họ sửa (nếu có)
                            $display_name = $is_invalid ? $_GET['old_val'] : $brand_name;
                            ?>

                            <input type="text" name="name" id="name"
                                class="form-control <?php echo $is_invalid ? 'is-invalid' : ''; ?>"
                                value="<?php echo htmlspecialchars($display_name); ?>"
                                placeholder="Nhập tên thương hiệu..." required>

                            <!-- Thông báo lỗi hiển thị dưới field -->
                            <?php if ($is_invalid): ?>
                                <div class="invalid-feedback">
                                    Loại sản phẩm đã tồn tại trong hệ thống
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" name="update_brand_btn" class="btn btn-primary px-4">
                                <i class="fas fa-save me-1"></i> Lưu thay đổi
                            </button>
                            <a href="index.php?view_brand" class="btn btn-light border px-4">Hủy bỏ</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>