<?php
// 1. Kết nối DB sử dụng đường dẫn bạn đã cung cấp
include __DIR__ . '/../../../includes/connect.php';

if (isset($_POST['update_post_btn'])) {
    // 2. Lấy dữ liệu từ form
    $post_id = mysqli_real_escape_string($con, $_POST['post_id']);
    $title = mysqli_real_escape_string($con, $_POST['title']);
    $content = isset($_POST['content']) ? mysqli_real_escape_string($con, $_POST['content']) : '';

    $target_dir = __DIR__ . '/../../../admin/admin_images/post_thumbnails/';

    // Kiểm tra thư mục tồn tại, nếu không có thì tạo (đề phòng)
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    // 4. Kiểm tra trùng tiêu đề (trừ bài viết hiện tại)
    $check_title = "SELECT id FROM `posts` WHERE title = '$title' AND id != '$post_id'";
    $res_title = mysqli_query($con, $check_title);

    if (mysqli_num_rows($res_title) > 0) {
        header("Location: ../../../admin/index.php?edit_post=$post_id&error=title_exists");
        exit();
    }

    // 5. Xử lý ảnh bài viết
    $new_image = $_FILES['thumbnail']['name'];
    $old_image_query = "SELECT thumbnail FROM `posts` WHERE id = '$post_id'";
    $old_res = mysqli_query($con, $old_image_query);
    $old_data = mysqli_fetch_assoc($old_res);
    $old_image_name = $old_data['thumbnail'];

    if (!empty($new_image) && $_FILES['thumbnail']['error'] === UPLOAD_ERR_OK) {
        // Nếu người dùng chọn ảnh mới
        $extension = strtolower(pathinfo($new_image, PATHINFO_EXTENSION));
        $allowed = array("jpg", "jpeg", "png", "webp", "gif");

        if (in_array($extension, $allowed)) {
            // Tạo tên file mới với time() để tránh trùng
            $new_filename = time() . '_' . uniqid() . '.' . $extension;
            // Lưu trong DB theo yêu cầu: post_thumbnails/tên file
            $update_image_name = 'post_thumbnails/' . $new_filename;
            $target_file = $target_dir . $new_filename;

            if (move_uploaded_file($_FILES['thumbnail']['tmp_name'], $target_file)) {
                // Xóa ảnh cũ nếu tồn tại (dùng basename() đề phòng DB lưu cả đường dẫn)
                if (!empty($old_image_name)) {
                    $old_path = $target_dir . basename($old_image_name);
                    if (file_exists($old_path)) {
                        unlink($old_path);
                    }
                }
            } else {
                echo "<script>alert('Lỗi: Không thể lưu file vào thư mục!'); window.history.back();</script>";
                exit();
            }
        } else {
            echo "<script>alert('Định dạng ảnh không hợp lệ!'); window.history.back();</script>";
            exit();
        }
    } else {
        // Nếu không thay ảnh, giữ nguyên tên ảnh cũ
        $update_image_name = $old_image_name;
    }

    // 6. Thực hiện câu lệnh Update
    $update_query = "UPDATE `posts` SET 
                     title = '$title', 
                     content = '$content', 
                     thumbnail = '$update_image_name' 
                     WHERE id = '$post_id'";

    $run_update = mysqli_query($con, $update_query);

    if ($run_update) {
        header("Location: ../../../admin/index.php?view_post&status=updated");
        exit();
    } else {
        header("Location: ../../../admin/index.php?edit_post=$post_id&error=db_error");
        exit();
    }
} else {
    header("Location: ../../../admin/index.php");
    exit();
}
?>