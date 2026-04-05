<?php
function getBestSellingProducts($con, $slug = 'pc', $limit = 10)
{
    // 1. Xử lý phân trang
    $page = isset($_GET['p']) ? (int) $_GET['p'] : 1;
    if ($page < 1)
        $page = 1;
    $offset = ($page - 1) * $limit;

    // 2. Lấy ID của danh mục cha và tất cả danh mục con của nó
    // Sử dụng Prepared Statement để bảo mật hơn nếu cần, ở đây giữ nguyên logic của bạn
    $cat_query = "SELECT id FROM categories WHERE slug = '$slug' 
                  OR parent_id IN (SELECT id FROM categories WHERE slug = '$slug')";
    $cat_result = mysqli_query($con, $cat_query);

    $category_ids = [];
    while ($row = mysqli_fetch_assoc($cat_result)) {
        $category_ids[] = $row['id'];
    }

    if (empty($category_ids))
        return null;
    $ids_string = implode(',', $category_ids);

    // 3. Đếm tổng số sản phẩm để phân trang
    $count_sql = "SELECT COUNT(*) as total FROM products 
                  WHERE status = 'active' AND category_id IN ($ids_string)";
    $count_res = mysqli_query($con, $count_sql);
    $total_rows = mysqli_fetch_assoc($count_res)['total'];
    $total_pages = ceil($total_rows / $limit);

    // 4. Truy vấn lấy sản phẩm + Tính total_sold
    $product_sql = "
        SELECT p.*, 
               IFNULL(SUM(od.quantity), 0) as total_sold
        FROM products p
        LEFT JOIN order_details od ON p.id = od.product_id
        LEFT JOIN orders o ON od.order_id = o.id AND o.payment_status = 'paid'
        WHERE p.status = 'active' 
        AND p.category_id IN ($ids_string)
        GROUP BY p.id
        ORDER BY total_sold DESC, p.id DESC
        LIMIT $limit OFFSET $offset
    ";

    $result = mysqli_query($con, $product_sql);
    $products = mysqli_fetch_all($result, MYSQLI_ASSOC);

    return [
        'products' => $products,
        'total_rows' => $total_rows,
        'total_pages' => $total_pages,
        'current_page' => $page,
        'category_slug' => $slug
    ];
}
?>