<?php
class ProductController
{
    private $con;

    public function __construct()
    {
        require_once __DIR__ . '/../../../includes/connect.php';
        global $con;
        $this->con = $con;
    }

     // Lấy danh sách sản phẩm
    public function getProducts($limit = 12, $orderBy = "created_at DESC")
    {
        $currentPage = isset($_GET['p']) ? (int) $_GET['p'] : 1;
        if ($currentPage < 1) $currentPage = 1;
        $offset = ($currentPage - 1) * $limit;

        // 1. Đếm tổng số sản phẩm active
        $countQuery = "SELECT COUNT(*) as total FROM products WHERE status = 'active'";
        $countResult = mysqli_query($this->con, $countQuery);
        $totalRows = mysqli_fetch_assoc($countResult)['total'];
        $totalPages = ceil($totalRows / $limit);

        // 2. Truy vấn dữ liệu
        $sql = "SELECT * FROM products 
                WHERE status = 'active' 
                ORDER BY $orderBy 
                LIMIT ? OFFSET ?";

        $stmt = mysqli_prepare($this->con, $sql);
        mysqli_stmt_bind_param($stmt, "ii", $limit, $offset);
        mysqli_stmt_execute($stmt);
        $products = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);

        return [
            'products'    => $products,
            'totalPages'  => $totalPages,
            'currentPage' => $currentPage,
            'totalRows'   => $totalRows
        ];
    }
}