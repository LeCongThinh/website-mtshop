<?php
require_once __DIR__ . '/../../../includes/connect.php';

// Hàm hiển thị thông tin tài khoản người dùng
function getUserProfile($con, $user_id)
{
    $sql = "select * from users where id=?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_assoc($result);
}

// Hàm cập nhật thông tin tài khoản người dùng
function updateUserProfile($con, $user_id, $data, $file)
{
    $name = mysqli_real_escape_string($con, $data['name']);
    $phone = mysqli_real_escape_string($con, $data['phone']);
    $address = mysqli_real_escape_string($con, $data['address']);

    $avatar_name = $file['avatar']['name'] ?? '';
    $avatar_tmp = $file['avatar']['tmp_name'] ?? '';

    if (!empty($avatar_name)) {
        // Xử lý có thay đổi ảnh
        $file_name = time() . "_" . $avatar_name;
        $upload_path = "admin/admin_images/avatars/" . $file_name;
        $db_save_path = "avatars/" . $file_name;

        if (move_uploaded_file($avatar_tmp, $upload_path)) {
            $sql = "UPDATE users SET name=?, phone=?, address=?, avatar=? WHERE id=?";
            $stmt = mysqli_prepare($con, $sql);
            mysqli_stmt_bind_param($stmt, "ssssi", $name, $phone, $address, $db_save_path, $user_id);
        } else {
            return false; // Upload thất bại
        }
    } else {
        // Xử lý không thay đổi ảnh
        $sql = "UPDATE users SET name=?, phone=?, address=? WHERE id=?";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "sssi", $name, $phone, $address, $user_id);
    }

    // Trong hàm xử lý cập nhật hoặc đoạn code cập nhật thành công
    if (mysqli_stmt_execute($stmt)) {
        // Trường hợp thành công
        $_SESSION['user_name'] = $name;
        header("Location: index.php?page=profile&update=success");
        exit();
    } else {
        header("Location: index.php?page=profile&update=error");
        exit();
    }

    return $result;
}
