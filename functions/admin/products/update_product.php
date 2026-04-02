<?php
session_start();
include __DIR__ . '/../../../includes/connect.php';

// --- CÁC HÀM TRỢ GIÚP (Giữ nguyên logic từ file Create) ---
function generateRandomFileName($extension)
{
    $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
    for ($i = 0; $i < 40; $i++) {
        $randomString .= $chars[rand(0, strlen($chars) - 1)];
    }
    return $randomString . '.' . $extension;
}

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

if (isset($_POST['update_product'])) {
    // 1. Nhận dữ liệu cơ bản
    $product_id = (int) $_POST['product_id'];
    $name = mysqli_real_escape_string($con, $_POST['name']);
    $category_id = (int) $_POST['category_id'];
    $brand_id = (int) $_POST['brand_id'];
    $price = (float) $_POST['price'];
    $sale_price = !empty($_POST['sale_price']) ? (float) $_POST['sale_price'] : 0;
    $stock = (int) $_POST['stock'];
    $description = mysqli_real_escape_string($con, $_POST['description']);
    $slug = create_slug($name);

    // 2. Thiết lập đường dẫn (Giống hệt file Create)
    $projectRoot = realpath(__DIR__ . '/../../..');
    $base_path = $projectRoot . '/admin/admin_images/products/';

    if (!is_dir($base_path)) {
        mkdir($base_path, 0777, true);
    }
    if (!is_dir($base_path . 'gallery')) {
        mkdir($base_path . 'gallery', 0777, true);
    }

    // --- BẮT ĐẦU TRANSACTION ---
    mysqli_begin_transaction($con);

    try {
        // 3. Cập nhật bảng products (Thông tin văn bản)
        $update_sql = "UPDATE `products` SET 
                        `category_id` = '$category_id', 
                        `brand_id` = '$brand_id', 
                        `name` = '$name', 
                        `slug` = '$slug', 
                        `price` = '$price', 
                        `sale_price` = '$sale_price', 
                        `description` = '$description', 
                        `stock` = '$stock', 
                        `updated_at` = NOW() 
                      WHERE `id` = '$product_id'";

        if (!mysqli_query($con, $update_sql))
            throw new Exception("Lỗi cập nhật thông tin sản phẩm.");

        // 4. Xử lý Ảnh đại diện (Thumbnail) nếu có upload mới
        if (!empty($_FILES['thumbnail']['name'])) {
            $thumb_file = $_FILES['thumbnail'];
            $thumb_ext = pathinfo($thumb_file['name'], PATHINFO_EXTENSION);
            $thumb_new_name = generateRandomFileName($thumb_ext);

            $thumb_db_value = "products/" . $thumb_new_name;
            $thumb_upload_path = $base_path . $thumb_new_name;

            if (move_uploaded_file($thumb_file['tmp_name'], $thumb_upload_path)) {
                $sql_thumb_update = "UPDATE `products` SET `thumbnail` = '$thumb_db_value' WHERE `id` = '$product_id'";
                mysqli_query($con, $sql_thumb_update);
            } else {
                throw new Exception("Lỗi upload ảnh đại diện mới.");
            }
        }

        // 5. Xử lý Album ảnh
        // 5.1 Xóa các ảnh cũ (nếu người dùng nhấn X trên giao diện)
        if (!empty($_POST['remove_old_images'])) {
            foreach ($_POST['remove_old_images'] as $img_id) {
                if (!empty($img_id)) {
                    // Xóa trong Database
                    $del_img_sql = "DELETE FROM `product_images` WHERE `id` = '$img_id' AND `product_id` = '$product_id'";
                    mysqli_query($con, $del_img_sql);
                    // Lưu ý: Có thể thêm unlink file vật lý tại đây nếu cần tiết kiệm dung lượng
                }
            }
        }

        // 5.2 Thêm ảnh mới vào Album (Cộng dồn)
        if (!empty($_FILES['images']['name'][0])) {
            foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
                if (!empty($tmp_name)) {
                    $img_ext = pathinfo($_FILES['images']['name'][$key], PATHINFO_EXTENSION);
                    $img_new_name = generateRandomFileName($img_ext);

                    $img_db_value = "products/gallery/" . $img_new_name;
                    $img_upload_path = $base_path . "gallery/" . $img_new_name;

                    if (move_uploaded_file($tmp_name, $img_upload_path)) {
                        $sql_img = "INSERT INTO `product_images` (`product_id`, `image`, `created_at`) 
                                    VALUES ('$product_id', '$img_db_value', NOW())";
                        mysqli_query($con, $sql_img);
                    }
                }
            }
        }

        // 6. Xử lý Thông số kỹ thuật (Delete & Re-insert để đảm bảo tính đồng bộ)
        mysqli_query($con, "DELETE FROM `product_specs` WHERE `product_id` = '$product_id'");
        if (isset($_POST['spec_key'])) {
            foreach ($_POST['spec_key'] as $index => $key_name) {
                $val_name = $_POST['spec_value'][$index];
                if (!empty($key_name) && !empty($val_name)) {
                    $key_name = mysqli_real_escape_string($con, $key_name);
                    $val_name = mysqli_real_escape_string($con, $val_name);
                    $sql_spec = "INSERT INTO `product_specs` (`product_id`, `spec_key`, `spec_value`, `created_at`) 
                                 VALUES ('$product_id', '$key_name', '$val_name', NOW())";
                    mysqli_query($con, $sql_spec);
                }
            }
        }

        // Hoàn tất thành công
        mysqli_commit($con);
        header("Location: ../../../admin/index.php?view_product&status=success&msg=updated");

        exit();

    } catch (Exception $e) {
        mysqli_rollback($con);
        // Có thể ghi log lỗi $e->getMessage() nếu cần
        header("Location: ../../../admin/index.php?view_product&status=error");
        exit();
    }
}