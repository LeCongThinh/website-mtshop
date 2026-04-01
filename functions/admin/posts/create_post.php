<?php
session_start();
include __DIR__ . '/../../../includes/connect.php';

// Hàm tạo Slug
function create_slug($string) {
    $search = array(
        '#(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)#', '#(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)#', '#(ì|í|ị|ỉ|ĩ)#',
        '#(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)#', '#(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)#', '#(ỳ|ý|ỵ|ỷ|ỹ)#',
        '#(đ)#', '#(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)#', '#(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)#',
        '#(Ì|Í|Ị|Ỉ|Ĩ)#', '#(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)#', '#(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)#',
        '#(ỳ|ý|ỵ|ỷ|ỹ)#', '#(Đ)#'
    );
    $string = preg_replace($search, 'a', $string); // Thay thế tạm thời để đơn giản hóa logic slug
    $string = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $string)));
    return $string;
}

if (isset($_POST['create_post_btn'])) {
    $user_id = $_SESSION['admin_id'] ?? 1;
    $title   = mysqli_real_escape_string($con, $_POST['title']);
    $content = mysqli_real_escape_string($con, $_POST['content']);
    $slug    = create_slug($title);

    // --- BƯỚC 1: KIỂM TRA TIÊU ĐỀ DUY NHẤT ---
    $check_query = "SELECT title FROM `posts` WHERE title = '$title' LIMIT 1";
    $check_res = mysqli_query($con, $check_query);
    if (mysqli_num_rows($check_res) > 0) {
        header("Location: ../../../admin/index.php?create_post&error=title_exists");
        exit();
    }

    // --- BƯỚC 2: VALIDATE VÀ XỬ LÝ UPLOAD ẢNH ---
    if (!isset($_FILES['thumbnail']) || $_FILES['thumbnail']['error'] != 0) {
        // Nếu không có ảnh hoặc lỗi upload file từ phía client
        header("Location: ../../../admin/index.php?create_post&error=no_image");
        exit();
    }

    $file_name = $_FILES['thumbnail']['name'];
    $file_tmp  = $_FILES['thumbnail']['tmp_name'];
    $file_ext  = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    $allowed   = array("jpg", "jpeg", "png", "webp");

    // Kiểm tra định dạng
    if (!in_array($file_ext, $allowed)) {
        header("Location: ../../../admin/index.php?create_post&error=invalid_format");
        exit();
    }

    // Tạo tên file mới
    $new_filename = time() . '_' . uniqid() . '.' . $file_ext;
    $upload_path = __DIR__ . '/../../../admin/admin_images/post_thumbnails/' . $new_filename;
    
    // Đường dẫn lưu vào DB
    $db_thumbnail_path = 'post_thumbnails/' . $new_filename;

    // Thực hiện upload
    if (!move_uploaded_file($file_tmp, $upload_path)) {
        // Nếu upload vật lý thất bại (lỗi phân quyền thư mục...)
        header("Location: ../../../admin/index.php?create_post&error=upload_failed");
        exit();
    }

    // --- BƯỚC 3: LƯU VÀO DATABASE (Chỉ chạy khi upload ảnh thành công) ---
    $insert_query = "INSERT INTO `posts` 
                    (`user_id`, `thumbnail`, `title`, `slug`, `content`, `status`, `created_at`, `updated_at`) 
                    VALUES 
                    ('$user_id', '$db_thumbnail_path', '$title', '$slug', '$content', '1', NOW(), NOW())";

    if (mysqli_query($con, $insert_query)) {
        header("Location: ../../../admin/index.php?view_post&status=success");
    } else {
        // Nếu lỗi DB, xóa luôn cái ảnh vừa upload để tránh rác server
        if (file_exists($upload_path)) unlink($upload_path);
        header("Location: ../../../admin/index.php?view_post&status=error");
    }
    exit();
}