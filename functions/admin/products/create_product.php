<?php
session_start();
include __DIR__ . '/../../../includes/connect.php';
function generateRandomFileName($extension)
{
    $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
    for ($i = 0; $i < 40; $i++) {
        $randomString .= $chars[rand(0, strlen($chars) - 1)];
    }
    return $randomString . '.' . $extension;
}

// Hàm chuyển đổi tiếng Việt sang Slug (Nên thêm vào)
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
        '/[^a-zA-Z0-9\-\_]/',
    );
    $replace = array('a', 'e', 'i', 'o', 'u', 'y', 'd', 'A', 'E', 'I', 'O', 'U', 'Y', 'D', '-', );
    $string = preg_replace($search, $replace, $string);
    $string = preg_replace('/(-)+/', '-', $string);
    return strtolower(trim($string, '-'));
}

if (isset($_POST['submit_product'])) {
    // 1. Nhận dữ liệu từ Form
    $name = mysqli_real_escape_string($con, $_POST['name']);
    $category_id = (int) $_POST['category_id'];
    $brand_id = (int) $_POST['brand_id'];
    $price = (float) $_POST['price'];
    $sale_price = !empty($_POST['sale_price']) ? (float) $_POST['sale_price'] : 0;
    $stock = (int) $_POST['stock'];
    $description = mysqli_real_escape_string($con, $_POST['description']);
    $slug = create_slug($name);

    // Đường dẫn gốc lưu trữ trên hệ thống
    $projectRoot = realpath(__DIR__ . '/../../..');

    // Ưu tiên thư mục admin/admin_images/products nếu tồn tại (hoặc có thể tạo được), sau đó fallback về admin_images/products
    $candidate_paths = [
        $projectRoot . '/admin/admin_images/products/',
        $projectRoot . '/admin_images/products/'
    ];

    $base_path = null;
    foreach ($candidate_paths as $path) {
        if (is_dir($path) || mkdir($path, 0777, true)) {
            $base_path = rtrim($path, '/') . '/';
            break;
        }
    }

    if (!$base_path) {
        throw new Exception('Không thể tạo được thư mục lưu ảnh sản phẩm.');
    }

    // Tạo thư mục gallery nếu chưa có
    if (!is_dir($base_path . 'gallery')) {
        mkdir($base_path . 'gallery', 0777, true);
    }

    // 2. Xử lý Ảnh đại diện (Thumbnail)
    $thumb_file = $_FILES['thumbnail'];
    $thumb_ext = pathinfo($thumb_file['name'], PATHINFO_EXTENSION);
    $thumb_new_name = generateRandomFileName($thumb_ext);

    // Đường dẫn lưu vào DB: products/tên_file.png
    $thumb_db_value = "products/" . $thumb_new_name;
    // Đường dẫn vật lý để upload: admin_images/products/tên_file.png
    $thumb_upload_path = $base_path . $thumb_new_name;

    // --- BẮT ĐẦU TRANSACTION ---
    mysqli_begin_transaction($con);

    try {
        // 3. Chèn bảng products
        $sql_product = "INSERT INTO `products` (`category_id`, `brand_id`, `name`, `slug`, `price`, `sale_price`, `thumbnail`, `description`, `stock`, `status`, `created_at`) 
                        VALUES ('$category_id', '$brand_id', '$name', '$slug', '$price', '$sale_price', '$thumb_db_value', '$description', '$stock', 'active', NOW())";

        if (!mysqli_query($con, $sql_product))
            throw new Exception("Lỗi thêm sản phẩm chính.");

        $product_id = mysqli_insert_id($con);

        // 4. Xử lý Album ảnh (Cộng dồn từ gallery-input)
        if (!empty($_FILES['images']['name'][0])) {
            foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
                $img_ext = pathinfo($_FILES['images']['name'][$key], PATHINFO_EXTENSION);
                $img_new_name = generateRandomFileName($img_ext);

                $img_db_value = "products/gallery/" . $img_new_name;
                $img_upload_path = $base_path . "gallery/" . $img_new_name;

                if (move_uploaded_file($tmp_name, $img_upload_path)) {
                    $sql_img = "INSERT INTO `product_images` (`product_id`, `image`, `created_at`) VALUES ('$product_id', '$img_db_value', NOW())";
                    mysqli_query($con, $sql_img);
                }
            }
        }

        // 5. Xử lý Thông số kỹ thuật
        if (isset($_POST['spec_key'])) {
            foreach ($_POST['spec_key'] as $index => $key_name) {
                $val_name = $_POST['spec_value'][$index];
                if (!empty($key_name) && !empty($val_name)) {
                    $key_name = mysqli_real_escape_string($con, $key_name);
                    $val_name = mysqli_real_escape_string($con, $val_name);
                    $sql_spec = "INSERT INTO `product_specs` (`product_id`, `spec_key`, `spec_value`, `created_at`) VALUES ('$product_id', '$key_name', '$val_name', NOW())";
                    mysqli_query($con, $sql_spec);
                }
            }
        }

        // 6. Cuối cùng mới upload ảnh đại diện và commit
        if (move_uploaded_file($thumb_file['tmp_name'], $thumb_upload_path)) {
            mysqli_commit($con);
            // Chuyển hướng kèm tham số success
            header("Location: ../../../admin/index.php?view_product&status=success");
            exit();
        } else {
            throw new Exception("Lỗi upload");
        }

    } catch (Exception $e) {
        mysqli_rollback($con);
        header("Location: ../../../admin/index.php?view_product&status=error");
        exit();
    }
}