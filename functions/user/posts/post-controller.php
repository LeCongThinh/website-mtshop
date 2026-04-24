<?php
class PostController
{
    private $con;
    public function __construct()
    {
        // Sử dụng đường dẫn tuyệt đối an toàn
        require_once __DIR__ . '/../../../includes/connect.php';
        global $con;
        $this->con = $con;
    }

    // Lấy danh sách bài viết có phân trang
    public function getAllPosts($limit = 9)
    {
        $currentPage = isset($_GET['p']) ? (int) $_GET['p'] : 1;
        if ($currentPage < 1) $currentPage = 1;
        $offset = ($currentPage - 1) * $limit;

        // Đếm tổng bài viết
        $countQuery = "SELECT COUNT(*) as total FROM posts WHERE status = 'active'";
        $countResult = mysqli_query($this->con, $countQuery);
        $totalRows = mysqli_fetch_assoc($countResult)['total'];
        $totalPages = ceil($totalRows / $limit);

        // Query bài viết
        $stmt = mysqli_prepare($this->con, "
            SELECT p.*, u.name as user_name
            FROM posts p
            LEFT JOIN users u ON p.user_id = u.id
            WHERE p.status = 'active'
            ORDER BY p.created_at DESC
            LIMIT ? OFFSET ?");

        mysqli_stmt_bind_param($stmt, "ii", $limit, $offset);
        mysqli_stmt_execute($stmt);
        $posts = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);

        return [
            'posts' => $posts,
            'totalPages' => $totalPages,
            'currentPage' => $currentPage
        ];
    }

    // Lấy chi tiết bài viết theo Slug
    public function getPostDetailBySlug($slug)
    {
        $slug = mysqli_real_escape_string($this->con, $slug);

        $stmt = mysqli_prepare($this->con, "
            SELECT p.*, u.name as user_name 
            FROM posts p
            LEFT JOIN users u ON p.user_id = u.id
            WHERE p.slug = ? AND p.status = 'active'
            LIMIT 1");

        mysqli_stmt_bind_param($stmt, "s", $slug);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $post = mysqli_fetch_assoc($result);

        if ($post) {
            $timestamp = strtotime($post['created_at']);
            $days = ["Chủ Nhật", "Thứ Hai", "Thứ Ba", "Thứ Tư", "Thứ Năm", "Thứ Sáu", "Thứ Bảy"];
            $post['day_name'] = $days[date('w', $timestamp)]; // Gán vào mảng $post
            $post['formatted_date'] = date('d/m/Y', $timestamp); // Gán vào mảng $post
        }

        return $post;
    }
}
