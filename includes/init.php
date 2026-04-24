<?php
require_once(__DIR__ . "/connect.php");
require_once(__DIR__ . "/../functions/user/handle-product/product-controller.php");
require_once(__DIR__ . "/../functions/user/home-controller.php");
require_once(__DIR__ . "/../functions/user/authentication/account_profile.php");
require_once(__DIR__ . "/../functions/user/search-controller.php");
require_once(__DIR__ . "/../functions/user/checkout/checkout-controller.php");
require_once(__DIR__ . "/../functions/user/orders/order-controller.php");
require_once(__DIR__ . "/../functions/user/posts/post-controller.php");
require_once(__DIR__ . "/../functions/user/products/product-controller.php");
require_once(__DIR__ . "/helpers.php");

// Khởi tạo order controller
$orderCtrl = new OrderController();
// Khởi tạo post controller
$postCtrl = new PostController();
// Khởi tạo product controller
$productCtrl = new ProductController();

?>