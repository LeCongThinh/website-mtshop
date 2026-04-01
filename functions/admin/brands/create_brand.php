<?php
include __DIR__ . '/../../../includes/connect.php';

$alert = '';
$brand_error = '';

// Hàm tạo slug (Nếu bạn đã có hàm này ở file chung thì không cần khai báo lại)
function create_brand_slug($string)
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

if (isset($_POST['add_brand_btn'])) {
    // 1. Làm sạch dữ liệu và tạo slug
    $brand_name = mysqli_real_escape_string($con, trim($_POST['brand_name']));
    $brand_slug = create_brand_slug($brand_name);

    if (empty($brand_name)) {
        $brand_error = "Vui lòng nhập tên hãng sản xuất!";
    } else {
        $brand_name_lower = strtolower($brand_name);
        $check_query = "SELECT id FROM `brands` 
                        WHERE LOWER(`name`) = '$brand_name_lower' 
                        AND `deleted_at` IS NULL LIMIT 1";

        $check_result = mysqli_query($con, $check_query);

        if (mysqli_num_rows($check_result) > 0) {
            $brand_error = "Loại sản phẩm đã tồn tại!";
        } else {
            $insert_query = "INSERT INTO `brands` (`name`, `slug`, `status`, `created_at`) 
                             VALUES ('$brand_name', '$brand_slug', 'active', NOW())";

            if (mysqli_query($con, $insert_query)) {
                header("Location: index.php?view_brand&status=success");
                exit();
            } else {
                header("Location: index.php?add_brand&error=db_error");
                exit();
            }
        }
    }
}
?>