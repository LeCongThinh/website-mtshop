<?php
$con = new mysqli('localhost', 'root', '', 'website_mtshop');
$con->set_charset("utf8");
if ($con->connect_error) {
    die("Kết nối thất bại   : " . $con->connect_error);
}
?>