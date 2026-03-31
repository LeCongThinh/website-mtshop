<?php
include __DIR__ . '/../../../includes/connect.php';

if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($con, $_GET['id']);
    
    // Câu lệnh SQL khôi phục
    $query = "UPDATE `brands` 
              SET `status` = 'active', 
                  `deleted_at` = NULL 
              WHERE `id` = '$id'";

    if (mysqli_query($con, $query)) {
        header("Location: ../../../admin/index.php?view_brand&msg=restore_success");
    } else {
        header("Location: ../../../admin/index.php?view_brand&msg=restore_error");
    }
    exit();
}