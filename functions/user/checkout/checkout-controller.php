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

// Xử lý đặt hàng COD (Tiền mặt) và VNPay (Thanh toán online)
function createOrderRecord($con, $user_id, $post_data, $cartItems, $totalAmount)
{
    mysqli_begin_transaction($con);
    try {
        // 1. Kiểm tra tồn kho (FOR UPDATE để khóa dòng, tránh Race Condition)
        foreach ($cartItems as $item) {
            $p_id = $item['product_id'];
            $checkStock = mysqli_query($con, "SELECT stock FROM products WHERE id = $p_id FOR UPDATE");
            $stockData = mysqli_fetch_assoc($checkStock);
            if ($stockData['stock'] < $item['quantity']) {
                throw new Exception("Sản phẩm '{$item['name']}' đã hết hàng hoặc không đủ số lượng!");
            }
        }

        $orderCode = generateOrderCode();
        $receiverName = mysqli_real_escape_string($con, $post_data['name']);
        $receiverPhone = mysqli_real_escape_string($con, $post_data['phone']);
        $receiverAddress = mysqli_real_escape_string($con, $post_data['address']);
        $note = mysqli_real_escape_string($con, $post_data['note']);

        // Lấy phương thức thực tế từ form (cod hoặc vnpay)
        $method = mysqli_real_escape_string($con, $post_data['payment_method']);

        // 2. Tạo đơn hàng chính
        $sqlOrder = "INSERT INTO orders (user_id, order_code, receiver_name, receiver_phone, receiver_address, note, payment_method, total_amount, payment_status, created_at, updated_at) 
                     VALUES ('$user_id', '$orderCode', '$receiverName', '$receiverPhone', '$receiverAddress', '$note', '$method', '$totalAmount', 'pending', NOW(), NOW())";

        if (!mysqli_query($con, $sqlOrder))
            throw new Exception("Lỗi tạo đơn hàng");
        $order_id = mysqli_insert_id($con);

        // 3. Tạo chi tiết đơn hàng & Trừ kho
        foreach ($cartItems as $item) {
            $p_id = $item['product_id'];
            $p_name = mysqli_real_escape_string($con, $item['name']);
            $p_thumb = mysqli_real_escape_string($con, $item['thumbnail']);
            $current_price = ($item['sale_price'] > 0) ? $item['sale_price'] : $item['price'];
            $qty = $item['quantity'];
            $subtotal = $current_price * $qty;

            $sqlDetail = "INSERT INTO order_details (order_id, product_id, product_name, product_thumbnail, original_price, price, quantity, subtotal, created_at, updated_at) 
                          VALUES ('$order_id', '$p_id', '$p_name', '$p_thumb', '{$item['price']}', '$current_price', '$qty', '$subtotal', NOW(), NOW())";

            if (!mysqli_query($con, $sqlDetail))
                throw new Exception("Lỗi chi tiết đơn hàng");

            // Luôn trừ kho để giữ hàng cho khách. Nếu VNPAY thất bại quá lâu, bạn sẽ có script hoàn kho sau.
            $sqlUpdateStock = "UPDATE products SET stock = stock - $qty WHERE id = $p_id";
            mysqli_query($con, $sqlUpdateStock);
        }

        // 4. Xóa giỏ hàng
        mysqli_query($con, "DELETE FROM carts WHERE user_id = $user_id");

        mysqli_commit($con);

        // Trả về thêm total_amount để gửi sang VNPAY
        return [
            'status' => 'success',
            'order_code' => $orderCode,
            'total_amount' => $totalAmount,
            'payment_method' => $method
        ];

    } catch (Exception $e) {
        mysqli_rollback($con);
        return ['status' => 'error', 'message' => $e->getMessage()];
    }
}

function generateVNPAYUrl($orderCode, $totalAmount)
{
    date_default_timezone_set('Asia/Ho_Chi_Minh');
    // Thông tin cấu hình (Thay bằng thông tin của bạn)
    $vnp_TmnCode = "MWZZ6ERJ"; // Mã Website tại VNPAY
    $vnp_HashSecret = "SIDBVLSZSH1UQZ4NPK1NQF1OJVTA2SJP"; // Chuỗi bí mật
    $vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
    $vnp_Returnurl = "http://localhost/project-php/website-mtshop/index.php?page=vnpay_return";

    $startTime = date("YmdHis");
    // Tạo thời gian hết hạn sau 15 phút
    $expire = date('YmdHis', strtotime('+15 minutes', strtotime($startTime)));
    $vnp_TxnRef = $orderCode;
    $vnp_OrderInfo = "Thanh toán đơn hàng #" . $orderCode;
    $vnp_OrderType = "billpayment";
    $vnp_Amount = $totalAmount * 100; // VNPAY tính đơn vị xu (đồng * 100)
    $vnp_Locale = 'vn';
    $vnp_IpAddr = $_SERVER['REMOTE_ADDR'];

    $inputData = array(
        "vnp_Version" => "2.1.0",
        "vnp_TmnCode" => $vnp_TmnCode,
        "vnp_Amount" => $vnp_Amount,
        "vnp_Command" => "pay",
        "vnp_CreateDate" => date('YmdHis'),
        "vnp_CurrCode" => "VND",
        "vnp_IpAddr" => $vnp_IpAddr,
        "vnp_Locale" => $vnp_Locale,
        "vnp_OrderInfo" => $vnp_OrderInfo,
        "vnp_OrderType" => $vnp_OrderType,
        "vnp_ReturnUrl" => $vnp_Returnurl,
        "vnp_TxnRef" => $vnp_TxnRef
    );

    ksort($inputData);
    $query = "";
    $i = 0;
    $hashdata = "";
    foreach ($inputData as $key => $value) {
        if ($i == 1) {
            $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
        } else {
            $hashdata .= urlencode($key) . "=" . urlencode($value);
            $i = 1;
        }
        $query .= urlencode($key) . "=" . urlencode($value) . '&';
    }

    $vnp_Url = $vnp_Url . "?" . $query;
    $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
    $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;

    return $vnp_Url;
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