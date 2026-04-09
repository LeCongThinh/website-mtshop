<?php
function getBestSellingProducts($con, $slug = 'pc', $limit = 10)
{
    $slug = trim(strtolower(mysqli_real_escape_string($con, $slug)));
    $page = isset($_GET['p']) ? max(1, (int)$_GET['p']) : 1;
    $offset = ($page - 1) * $limit;

    // 1. Lấy category_id (danh mục chính và con)
    $cat_query = "SELECT id FROM categories 
                  WHERE (slug = '$slug' OR parent_id IN (SELECT id FROM categories WHERE slug = '$slug'))
                  AND status = 'active'";
    $cat_result = mysqli_query($con, $cat_query);
    $category_ids = [];

    if ($cat_result && mysqli_num_rows($cat_result) > 0) {
        while ($row = mysqli_fetch_assoc($cat_result)) {
            $category_ids[] = $row['id'];
        }
    }

    if (empty($category_ids)) {
        return [
            'products' => [],
            'total_rows' => 0,
            'total_pages' => 0,
            'current_page' => $page,
            'category_slug' => $slug
        ];
    }

    $ids_string = implode(',', $category_ids);

    // 2. Đếm tổng
    $count_sql = "SELECT COUNT(*) as total FROM products 
                  WHERE status = 'active' AND category_id IN ($ids_string)";
    $count_res = mysqli_query($con, $count_sql);
    $total_rows = mysqli_fetch_assoc($count_res)['total'] ?? 0;

    // 3. Truy vấn bán chạy (cần ít nhất 1 sản phẩm trả ra)

    // a) Ưu tiên: sản phẩm đã bán (có đơn đã thanh toán)
    $best_selling_sql = "
        SELECT p.*, 
            (SELECT IFNULL(SUM(od.quantity), 0) 
             FROM order_details od 
             INNER JOIN orders o ON od.order_id = o.id 
             WHERE od.product_id = p.id AND o.payment_status = 'paid') as total_sold
        FROM products p
        WHERE p.status = 'active' 
          AND p.category_id IN ($ids_string)
        ORDER BY total_sold DESC, p.id DESC
        LIMIT $limit OFFSET $offset
    ";

    $result = mysqli_query($con, $best_selling_sql);

    // Nếu không có kết quả nào (chưa có đơn bán/chưa cập nhật đúng payment_status)
    if (!$result || mysqli_num_rows($result) == 0) {
        // b) Fallback: lấy sản phẩm mới trong danh mục này
        $fallback_sql = "
            SELECT *, 0 as total_sold
            FROM products 
            WHERE status = 'active' 
              AND category_id IN ($ids_string)
            ORDER BY created_at DESC, id DESC
            LIMIT $limit OFFSET $offset
        ";
        $result = mysqli_query($con, $fallback_sql);
    }

    $products = mysqli_fetch_all($result, MYSQLI_ASSOC);

    return [
        'products' => $products ?? [],
        'total_rows' => $total_rows,
        'total_pages' => ceil($total_rows / $limit),
        'current_page' => $page,
        'category_slug' => $slug
    ];
}
?>