<?php
// Hàm xử lý lấy chi tiết sản phẩm
function getProductDetail($con, $slug)
{
    // 1. Truy vấn sản phẩm chính
    $sql = "SELECT p.*, 
                   c.name as category_name, c.slug as category_slug, 
                   parent.name as category_parent_name, parent.slug as category_parent_slug,
                   b.name as brand_name 
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id 
            LEFT JOIN categories parent ON c.parent_id = parent.id 
            LEFT JOIN brands b ON p.brand_id = b.id 
            WHERE p.slug = ? AND p.status = 'active' LIMIT 1";

    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "s", $slug);
    mysqli_stmt_execute($stmt);
    $product = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

    if (!$product)
        return null;

    $p_id = $product['id'];

    // 2. Lấy hình ảnh phụ
    $stmt_img = mysqli_prepare($con, "SELECT * FROM product_images WHERE product_id = ?");
    mysqli_stmt_bind_param($stmt_img, "i", $p_id);
    mysqli_stmt_execute($stmt_img);
    $product['images'] = mysqli_fetch_all(mysqli_stmt_get_result($stmt_img), MYSQLI_ASSOC);

    // 3. Lấy thông số kỹ thuật
    $stmt_spec = mysqli_prepare($con, "SELECT * FROM product_specs WHERE product_id = ?");
    mysqli_stmt_bind_param($stmt_spec, "i", $p_id);
    mysqli_stmt_execute($stmt_spec);
    $product['specs'] = mysqli_fetch_all(mysqli_stmt_get_result($stmt_spec), MYSQLI_ASSOC);

    return $product;
}

// Load data sản phẩm theo danh mục (có hỗ trợ lọc theo thương hiệu)
function getCategoryData($con, $category_slug, $brand_slug = null)
{
    // 1. Lấy thông tin danh mục hiện tại
    $cat_query = mysqli_query($con, "SELECT * FROM categories WHERE slug = '$category_slug' LIMIT 1");
    $category = mysqli_fetch_assoc($cat_query);
    if (!$category)
        return null;

    $category_id = $category['id'];

    // 2. Lấy danh mục con
    $children_query = mysqli_query($con, "SELECT * FROM categories WHERE parent_id = $category_id AND status = 'active'");
    $children = mysqli_fetch_all($children_query, MYSQLI_ASSOC);

    // 3. Gom tất cả ID danh mục (cha + con) để lọc sản phẩm
    $category_ids = [$category_id];
    foreach ($children as $child) {
        $category_ids[] = $child['id'];
    }
    $ids_string = implode(',', $category_ids);

    // 4. Lấy danh sách thương hiệu có trong danh mục này (để hiển thị bộ lọc)
    $brands_query = mysqli_query($con, "SELECT DISTINCT b.* FROM brands b 
                                        JOIN products p ON b.id = p.brand_id 
                                        WHERE p.category_id IN ($ids_string)");
    $brands = mysqli_fetch_all($brands_query, MYSQLI_ASSOC);

    // 5. Xử lý lọc theo Thương hiệu (nếu có)
    $brand_filter = "";
    $current_brand = null;
    if ($brand_slug) {
        $b_query = mysqli_query($con, "SELECT * FROM brands WHERE slug = '$brand_slug' LIMIT 1");
        $current_brand = mysqli_fetch_assoc($b_query);
        if ($current_brand) {
            $brand_filter = " AND p.brand_id = " . $current_brand['id'];
        }
    }
    $limit = 10;
    $page = isset($_GET['p']) ? (int) $_GET['p'] : 1;
    $offset = ($page - 1) * $limit;

    // 7. Truy vấn sản phẩm
    $count_sql = "SELECT COUNT(*) as total FROM products p WHERE p.category_id IN ($ids_string) AND p.status = 'active' $brand_filter";
    $total_rows = mysqli_fetch_assoc(mysqli_query($con, $count_sql))['total'];
    $total_pages = ceil($total_rows / $limit);

    $product_sql = "SELECT p.* FROM products p 
                    WHERE p.category_id IN ($ids_string) AND p.status = 'active' $brand_filter 
                    ORDER BY p.created_at DESC LIMIT $limit OFFSET $offset";
    $products = mysqli_fetch_all(mysqli_query($con, $product_sql), MYSQLI_ASSOC);

    return [
        'category' => $category,
        'children' => $children,
        'brands' => $brands,
        'current_brand' => $current_brand,
        'products' => $products,
        'total_rows' => $total_rows,
        'total_pages' => $total_pages,
        'current_page' => $page
    ];
}

// Load data sản phẩm theo danh mục con (có hỗ trợ lọc theo thương hiệu)
function getSubcategoryData($con, $slug)
{
    // 1. Lấy thông tin danh mục con và danh mục cha của nó
    $sql = "SELECT child.*, parent.name as parent_name, parent.slug as parent_slug 
            FROM categories child 
            LEFT JOIN categories parent ON child.parent_id = parent.id 
            WHERE child.slug = ? LIMIT 1";

    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "s", $slug);
    mysqli_stmt_execute($stmt);
    $subcategory = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

    if (!$subcategory)
        return null;

    // 2. Cấu hình phân trang
    $limit = 10;
    $page = isset($_GET['p']) ? (int) $_GET['p'] : 1;
    $offset = ($page - 1) * $limit;

    // 3. Đếm tổng sản phẩm của danh mục con này
    $sub_id = $subcategory['id'];
    $count_query = mysqli_query($con, "SELECT COUNT(*) as total FROM products WHERE category_id = $sub_id AND status = 'active'");
    $total_rows = mysqli_fetch_assoc($count_query)['total'];
    $total_pages = ceil($total_rows / $limit);

    // 4. Lấy danh sách sản phẩm
    $product_sql = "SELECT * FROM products 
                    WHERE category_id = ? AND status = 'active' 
                    ORDER BY created_at DESC LIMIT ? OFFSET ?";
    $stmt_p = mysqli_prepare($con, $product_sql);
    mysqli_stmt_bind_param($stmt_p, "iii", $sub_id, $limit, $offset);
    mysqli_stmt_execute($stmt_p);
    $products = mysqli_fetch_all(mysqli_stmt_get_result($stmt_p), MYSQLI_ASSOC);

    return [
        'subcategory' => $subcategory,
        'products' => $products,
        'total_rows' => $total_rows,
        'total_pages' => $total_pages,
        'current_page' => $page
    ];
}
?>