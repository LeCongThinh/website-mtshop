<?php
header('Content-Type: application/json; charset=utf-8');
require_once 'gemini_config.php';
require_once __DIR__ . '/../../includes/connect.php';

// Nhận dữ liệu từ AJAX
$input = json_decode(file_get_contents('php://input'), true);
$userMessage = $input['message'] ?? '';

if (empty($userMessage)) {
    echo json_encode(['error' => 'Tin nhắn không được để trống'], JSON_UNESCAPED_UNICODE);
    exit;
}

$productData = "Dưới đây là danh sách sản phẩm thực tế tại cửa hàng MTShop:\n";

// Truy vấn lấy 15 sản phẩm mới nhất/nổi bật để AI có thông tin
$userMessage = trim($userMessage);
$lowerMessage = mb_strtolower($userMessage, 'UTF-8');
$searchKeyword = mysqli_real_escape_string($con, $userMessage);
// Danh sách các câu chào
$helloKeywords = ['xin chào', 'hello', 'hi', 'xin chao', 'cin chaof', 'chào', 'helo', 'halo'];
$isPureGreeting = in_array($lowerMessage, $helloKeywords);
// Kiểm tra xem tin nhắn người dùng có nằm trong danh sách chào hỏi không
$hasGreetingWord = preg_match('/(chào|hello|hi|xin chao|cin chaof)/i', $lowerMessage);

if ($isPureGreeting || (strlen($userMessage) < 10 && $hasGreetingWord)) {
        // Nếu là lời chào, không cần lấy quá nhiều sản phẩm, chỉ lấy 3 sp tiêu biểu thôi
        $sql = "SELECT p.*, c.name as category_name, b.name as brand_name 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id
                LEFT JOIN brands b ON p.brand_id = b.id
                ORDER BY p.id DESC LIMIT 3";
        
        $introText = "Khách hàng vừa gửi lời chào. Hãy chào lại họ thật niềm nở, giới thiệu bạn là trợ lý ảo của MTShop và liệt kê nhẹ nhàng một vài sản phẩm mới nhất bên dưới.";
    
} else {
    $sql = "SELECT p.*, c.name as category_name, b.name as brand_name 
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id
            LEFT JOIN brands b ON p.brand_id = b.id
            WHERE p.name LIKE '%$searchKeyword%' 
            OR c.name LIKE '%$searchKeyword%' 
            OR b.name LIKE '%$searchKeyword%'
            LIMIT 5";
    $introText = "Dựa vào danh sách sản phẩm bên dưới để trả lời và tư vấn cho khách hàng.";
}

$result = mysqli_query($con, $sql);
$productData = "";

if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $price = number_format($row['price'], 0, ',', '.');
        $sale_price = ($row['sale_price'] > 0) ? number_format($row['sale_price'], 0, ',', '.') : $price;
        $brand = $row['brand_name'] ?? 'Khác';
        $category = $row['category_name'] ?? 'Laptop';
        
        // Cung cấp đầy đủ "nhãn" để AI không đoán mò
        $productData .= "- Tên: {$row['name']} | Hãng: {$brand} | Loại: {$category} | Giá gốc: {$price}đ | Giá KM: {$sale_price}đ | Mô tả: {$row['description']}\n";
    }
} else {
    // Fallback cũng cần lấy Hãng và Loại để AI gợi ý chuẩn
    $sql_fallback = "SELECT p.*, c.name as category_name, b.name as brand_name 
                     FROM products p 
                     LEFT JOIN categories c ON p.category_id = c.id
                     LEFT JOIN brands b ON p.brand_id = b.id
                     ORDER BY RAND() LIMIT 10";
    $result_fb = mysqli_query($con, $sql_fallback);
    while ($row_fb = mysqli_fetch_assoc($result_fb)) {
        $productData .= "- Tên: {$row_fb['name']} | Hãng: " . ($row_fb['brand_name'] ?? 'Khác') . " | Loại: " . ($row_fb['category_name'] ?? 'Laptop') . " | Giá gốc: " . number_format($row_fb['price'], 0, ',', '.') . "đ\n";
    }
}

// Cấu hình promtt cho AI, bao gồm cả thông tin sản phẩm để AI có thể tư vấn chính xác hơn
$fullPrompt = "BẠN LÀ CHUYÊN VIÊN TƯ VẤN CỦA MTSHOP.
            Nhiệm vụ: {$introText}
            DANH SÁCH SẢN PHẨM THỰC TẾ:
            " . $productData . "
            YÊU CẦU ĐỊNH DẠNG (CỰC KỲ QUAN TRỌNG):
            1. Liệt kê theo số thứ tự.
            2. Định dạng mẫu mỗi sản phẩm (KHÔNG ĐƯỢC CÓ DÒNG TRỐNG GIỮA CÁC DÒNG):
            [Số thứ tự]. \"Tên sản phẩm\"
            Giá bán: [Giá]
            Giá khuyến mãi: [Giá KM]
            3. Sau mỗi sản phẩm mới cách ra một dòng để phân biệt với sản phẩm tiếp theo.
            4. KHÔNG tự ý thêm các dòng trống giữa Tên và Giá.
            5. Nếu là lời chào, hãy chào lại trước rồi mới liệt kê sản phẩm
            Khách hàng hỏi: " . $userMessage;

// Cấu trúc dữ liệu gửi đến Gemini API
$url = "https://generativelanguage.googleapis.com/v1beta/models/" . GEMINI_MODEL . ":generateContent?key=" . GEMINI_API_KEY;
// $url = "https://generativelanguage.googleapis.com/v1/models/gemini-pro:generateContent?key=" . GEMINI_API_KEY;

$data = [
    "contents" => [
        [
            "role" => "user",
            "parts" => [
                ["text" => $fullPrompt]
            ]
        ]
    ],
    "generationConfig" => [
        "temperature" => 0.4,
        "maxOutputTokens" => 2048, // Tăng hạn mức phản hồi để AI viết được dài hơn
        "topP" => 0.95,
        
    ]
];


// Sử dụng cURL để gọi API
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Tắt kiểm tra SSL nếu chạy trên localhost

$response = curl_exec($ch);
$error = curl_error($ch);
curl_close($ch);

if ($error) {
    echo json_encode(['error' => 'Lỗi kết nối: ' . $error], JSON_UNESCAPED_UNICODE);
} else {
    $result = json_decode($response, true);
    file_put_contents('log_api.txt', $response);
    // Trích xuất nội dung phản hồi từ cấu trúc JSON của Google
    $aiReply = $result['candidates'][0]['content']['parts'][0]['text'] ?? 'Xin lỗi, tôi đang bận một chút.';
    echo json_encode(['reply' => $aiReply], JSON_UNESCAPED_UNICODE);
}