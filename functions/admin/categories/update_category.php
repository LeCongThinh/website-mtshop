<?php
include __DIR__ . '/../../../includes/connect.php';

$alert = '';
$name_error = '';

// Hàm tạo slug (Copy lại từ file create)
function create_slug($string) {
    $search = array(
        '#(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)#', '#(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)#', '#(ì|í|ị|ỉ|ĩ)#',
        '#(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)#', '#(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)#', '#(ỳ|ý|ỵ|ỷ|ỹ)#', '#(đ)#',
        '#(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)#', '#(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)#', '#(Ì|Í|Ị|Ỉ|Ĩ)#',
        '#(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)#', '#(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)#', '#(Ỳ|Ý|Ỵ|Ỷ|Ỹ)#', '#(Đ)#',
        '/[^a-zA-Z0-9\s-]/'
    );
    $replace = array('a', 'e', 'i', 'o', 'u', 'y', 'd', 'a', 'e', 'i', 'o', 'u', 'y', 'd', '');
    $string = preg_replace($search, $replace, $string);
    $string = strtolower(trim($string));
    $string = preg_replace('/[\s-]+/', '-', $string);
    return $string;
}

if (isset($_POST['update_category_btn'])) {
    $cat_id = $_POST['category_id'];
    $name = mysqli_real_escape_string($con, trim($_POST['name']));
    $parent_id = $_POST['parent_id'];
    $slug = create_slug($name);

    // 1. Kiểm tra trùng tên (loại trừ chính nó)
    $name_lower = strtolower($name);
    $check_query = "SELECT id FROM `categories` 
                    WHERE LOWER(`name`) = '$name_lower' 
                    AND `id` != '$cat_id' 
                    AND `deleted_at` IS NULL LIMIT 1";
    $check_result = mysqli_query($con, $check_query);

    if (mysqli_num_rows($check_result) > 0) {
        $name_error = "Tên danh mục này đã tồn tại ở một bản ghi khác!";
    } else {
        // 2. Xử lý parent_id
        $update_parent = empty($parent_id) ? "NULL" : "'$parent_id'";

        // 3. Thực hiện Update
        $update_query = "UPDATE `categories` 
                         SET `name` = '$name', `slug` = '$slug', `parent_id` = $update_parent, `updated_at` = NOW() 
                         WHERE `id` = '$cat_id'";

        if (mysqli_query($con, $update_query)) {
            $alert = '
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i> Cập nhật danh mục thành công!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>';
        } else {
            $alert = '<div class="alert alert-danger alert-dismissible fade show">Lỗi hệ thống!</div>';
        }
    }
}
?>