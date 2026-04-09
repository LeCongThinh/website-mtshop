<?php
// Hàm lấy thông tin giỏ hàng
function getCheckoutData($con, $user_id)
{
    // 1. Lấy thông tin người dùng
    $userQuery = "SELECT * FROM users WHERE id = $user_id LIMIT 1";
    $userResult = mysqli_query($con, $userQuery);
    $user = mysqli_fetch_assoc($userResult);

    // 2. Lấy giỏ hàng kèm thông tin sản phẩm
    $cartQuery = "
        SELECT c.*, p.name, p.price, p.sale_price, p.thumbnail 
        FROM carts c 
        JOIN products p ON c.product_id = p.id 
        WHERE c.user_id = $user_id
    ";
    $cartResult = mysqli_query($con, $cartQuery);

    $cartItems = [];
    $totalOrder = 0;

    while ($item = mysqli_fetch_assoc($cartResult)) {
        $currentPrice = ($item['sale_price'] > 0) ? $item['sale_price'] : $item['price'];
        $item['current_price'] = $currentPrice; // Lưu giá hiện tại vào mảng để dùng ở View
        $totalOrder += $currentPrice * $item['quantity'];
        $cartItems[] = $item;
    }

    return [
        'user' => $user,
        'cartItems' => $cartItems,
        'totalOrder' => $totalOrder
    ];
}

// Generate ra mã đơn hàng duy nhất (ví dụ: ORD-20240601-ABC123)
function generateOrderCode()
{
    return 'ORD-' . date('Ymd') . '-' . strtoupper(substr(md5(uniqid()), 0, 6));
}

// Xử lý đặt hàng COD (Tiền mặt)
function handleCheckoutCOD($con, $user_id, $post_data)
{
    // 1. Lấy giỏ hàng và kiểm tra tồn kho
    $cart_sql = "SELECT c.*, p.name, p.price, p.sale_price, p.thumbnail, p.stock 
                 FROM carts c 
                 JOIN products p ON c.product_id = p.id 
                 WHERE c.user_id = $user_id";
    $cart_res = mysqli_query($con, $cart_sql);
    $cartItems = mysqli_fetch_all($cart_res, MYSQLI_ASSOC);

    // 2. Kiểm tra tồn kho trước khi cho phép đặt
    foreach ($cartItems as $item) {
        if ($item['stock'] <= 0) {
            return ['status' => 'error', 'message' => "Sản phẩm '{$item['name']}' đã hết hàng!"];
        }
        if ($item['stock'] < $item['quantity']) {
            return ['status' => 'error', 'message' => "Sản phẩm '{$item['name']}' chỉ còn {$item['stock']} món!"];
        }
    }

    mysqli_begin_transaction($con);
    try {
        $totalAmount = 0;
        foreach ($cartItems as $item) {
            $price = ($item['sale_price'] > 0) ? $item['sale_price'] : $item['price'];
            $totalAmount += $price * $item['quantity'];
        }

        // B. Tạo đơn hàng chính (Orders)
        $orderCode = generateOrderCode();
        $receiverName = mysqli_real_escape_string($con, $post_data['name']);
        $receiverPhone = mysqli_real_escape_string($con, $post_data['phone']);
        $receiverAddress = mysqli_real_escape_string($con, $post_data['address']);
        $note = mysqli_real_escape_string($con, $post_data['note']);

        $sqlOrder = "INSERT INTO orders (user_id, order_code, receiver_name, receiver_phone, receiver_address, note, payment_method, total_amount, payment_status, created_at, updated_at) 
                     VALUES ('$user_id', '$orderCode', '$receiverName', '$receiverPhone', '$receiverAddress', '$note', 'cod', '$totalAmount', 'pending', NOW(), NOW())";

        if (!mysqli_query($con, $sqlOrder))
            throw new Exception("Lỗi tạo đơn hàng");
        $order_id = mysqli_insert_id($con);

        // C. Tạo chi tiết đơn hàng & Cập nhật kho
        foreach ($cartItems as $item) {
            $p_id = $item['product_id'];
            $p_name = mysqli_real_escape_string($con, $item['name']);
            $p_thumb = $item['thumbnail'];
            $originPrice = $item['price'];
            $salePrice = ($item['sale_price'] > 0) ? $item['sale_price'] : $item['price'];
            $qty = $item['quantity'];
            $subtotal = $salePrice * $qty;

            // Chèn vào order_details
            $sqlDetail = "INSERT INTO order_details (order_id, product_id, product_name, product_thumbnail, original_price, price, quantity, subtotal, created_at, updated_at) 
                          VALUES ('$order_id', '$p_id', '$p_name', '$p_thumb', '$originPrice', '$salePrice', '$qty', '$subtotal', NOW(), NOW())";
            if (!mysqli_query($con, $sqlDetail))
                throw new Exception("Lỗi tạo chi tiết đơn hàng");

            // Trừ tồn kho (Decrement stock)
            $sqlUpdateStock = "UPDATE products SET stock = stock - $qty WHERE id = $p_id";
            if (!mysqli_query($con, $sqlUpdateStock))
                throw new Exception("Lỗi cập nhật kho");
        }

        // D. Xóa giỏ hàng sau khi đặt thành công
        mysqli_query($con, "DELETE FROM carts WHERE user_id = $user_id");

        // Commit dữ liệu
        mysqli_commit($con);
        return ['status' => 'success', 'order_code' => $orderCode];

    } catch (Exception $e) {
        mysqli_rollback($con); // Quay lại trạng thái cũ nếu có lỗi
        return ['status' => 'error', 'message' => $e->getMessage()];
    }
}

// Đặt hàng thành công trả ra view đơn hãng đã đặt thành công
function getOrderSuccessDetails($con, $order_code, $user_id)
{
    // 1. Chống SQL Injection
    $order_code = mysqli_real_escape_string($con, $order_code);

    // 2. Truy vấn đơn hàng và kiểm tra quyền sở hữu (tránh xem trộm đơn hàng người khác)
    $sql = "SELECT * FROM orders WHERE order_code = ? AND user_id = ? LIMIT 1";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "si", $order_code, $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    return mysqli_fetch_assoc($result);
}

?>