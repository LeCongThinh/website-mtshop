<?php
session_start();

// 1. Xóa toàn bộ biến Session
$_SESSION = array();

// 2. Hủy phiên làm việc trên Server
session_destroy();

// 3. Chuyển hướng về trang login (cùng nằm trong thư mục authentication)
echo "<script>
    window.location.href = 'admin_login.php';
</script>";
exit();
?>