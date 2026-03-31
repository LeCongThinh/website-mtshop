<?php
include __DIR__ . '/../../../includes/connect.php';

$alert = '';
$name_error = '';

// Hàm chuyển đổi tiếng Việt có dấu thành không dấu
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
    
if (isset($_POST['insert_category_btn'])) {
    $name = mysqli_real_escape_string($con, trim($_POST['name']));
    $parent_id = $_POST['parent_id'];

    $slug = create_slug($name);

    $check_query = "SELECT * FROM `categories` WHERE `name` = '$name' AND `deleted_at` IS NULL LIMIT 1";
    $check_result = mysqli_query($con, $check_query);

    if (mysqli_num_rows($check_result) > 0) {
        $name_error = "Tên danh mục '$name' đã tồn tại trong hệ thống!";
    } else {
        $insert_parent = empty($parent_id) ? "NULL" : "'$parent_id'";

        $insert_query = "INSERT INTO `categories` (`name`, `slug`, `parent_id`, `status`, `created_at`) 
                         VALUES ('$name', '$slug', $insert_parent, 'active', NOW())";

        $insert_result = mysqli_query($con, $insert_query);

        if ($insert_result) {
            // Thông báo thành công
            $alert = '
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                <strong>Thành công!</strong> Danh mục đã được thêm vào hệ thống.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>';

            // Xóa giá trị cũ để reset form sau khi thêm thành công
            unset($_POST['name']);
            unset($_POST['parent_id']);
        } else {
            // Thông báo lỗi hệ thống
            $alert = '
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Lỗi!</strong> Không thể thêm dữ liệu. Vui lòng thử lại sau.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>';
        }
    }
}


?>