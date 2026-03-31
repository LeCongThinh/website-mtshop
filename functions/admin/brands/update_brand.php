<?php
include __DIR__ . '/../../../includes/connect.php';

// Hàm tạo Slug (giữ đồng nhất với hàm create_slug của bạn)
function create_slug($string)
{
    $search = array(
        '#(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)#',
        '#(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)#',
        '#(ì|í|ị|ỉ|ĩ)#',
        '#(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)#',
        '#(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)#',
        '#(ỳ|ý|ỵ|ỷ|ỹ)#',
        '#(đ)#',
        '#(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)#',
        '#(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)#',
        '#(Ì|Í|Ị|Ỉ|Ĩ)#',
        '#(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)#',
        '#(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)#',
        '#(Ỳ|Ý|Ỵ|Ỷ|Ỹ)#',
        '#(Đ)#',
        '/[^a-zA-Z0-9\s-]/'
    );
    $replace = array(
        'a',
        'e',
        'i',
        'o',
        'u',
        'y',
        'd',
        'a',
        'e',
        'i',
        'o',
        'u',
        'y',
        'd',
        ''
    );
    $string = preg_replace($search, $replace, $string);
    $string = strtolower(trim($string));
    $string = preg_replace('/[\s-]+/', '-', $string);
    return $string;
}

if (isset($_POST['update_brand_btn'])) {
    $brand_id = mysqli_real_escape_string($con, $_POST['brand_id']);
    $name = mysqli_real_escape_string($con, trim($_POST['name']));
    $slug = create_slug($name);

    $check_query = "SELECT id FROM `brands` 
                    WHERE (LOWER(`name`) = LOWER('$name') OR `slug` = '$slug') 
                    AND `id` != '$brand_id' 
                    LIMIT 1";

    $check_result = mysqli_query($con, $check_query);

    if (mysqli_num_rows($check_result) > 0) {
        // Nếu trùng, quay lại trang edit kèm mã lỗi 'duplicate'
        header("Location: ../../../admin/index.php?edit_brand=$brand_id&error=name&old_val=" . urlencode($name));
        exit();
    } else {
        $update_query = "UPDATE `brands` SET `name` = '$name', `slug` = '$slug', `updated_at` = NOW() WHERE `id` = '$brand_id'";
        $update_result = mysqli_query($con, $update_query);

        if ($update_result) {
            // Thành công: Quay lại trang edit kèm mã success (để hiển thị alert)
            header("Location: ../../../admin/index.php?edit_brand=$brand_id&msg=update_success");
            exit();
        } else {
            // Thất bại: Quay lại trang edit kèm mã error
            header("Location: ../../../admin/index.php?edit_brand=$brand_id&msg=update_error");
            exit();
        }
    }
} else {
    header("Location: ../../../admin/index.php?view_brand");
}
?>