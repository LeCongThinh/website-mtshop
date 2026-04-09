document.addEventListener('DOMContentLoaded', function () {
    console.log('MTShop: Shopping Cart System Ready');

    // Cấu hình ưu tiên lấy từ window.CART đã khai báo ở index.php
    const CONFIG = {
        addUrl: '/project-php/website-mtshop/functions/user/cart/cart-controller.php',
        cartUrl: 'index.php?page=cart',
        badgeSelector: '#cart-count'
    };

    // --- 1. Xử lý cho tất cả các nút Thêm/Mua ---
    const handleCartAction = async (button, isRedirect = false) => {
        const productId = button.dataset.id;
        const originalHTML = button.innerHTML;

        if (!productId) return;

        // Hiệu ứng Loading
        button.disabled = true;
        button.innerHTML = `<span class="spinner-border spinner-border-sm" role="status"></span> ${isRedirect ? 'Đang xử lý...' : ''}`;
        await new Promise(resolve => setTimeout(resolve, 300));

        try {
            const formData = new FormData();
            formData.append('product_id', productId);
            formData.append('quantity', 1);

            const response = await fetch(CONFIG.addUrl, {
                method: 'POST',
                body: formData
            });

            if (!response.ok) throw new Error('Network error');
            const data = await response.json();

            if (data.success) {
                // Cập nhật số lượng trên Header
                const badge = document.querySelector(CONFIG.badgeSelector);
                if (badge) {
                    badge.textContent = data.count;
                    badge.classList.add('bump-effect');
                    setTimeout(() => badge.classList.remove('bump-effect'), 300);
                }

                if (isRedirect) {
                    // Nếu là "Mua ngay" -> Chuyển trang
                    window.location.href = CONFIG.cartUrl;
                } else {
                    button.innerHTML = '<i class="bi bi-check-lg me-1"></i> Đã thêm';
                    button.classList.replace('btn-outline-primary', 'btn-success');
                }
            } else {
                showToast(data.message || 'Không thể thêm hàng', 'warning');
            }
        } catch (err) {
            console.error('Cart Error:', err);
            showToast('Có lỗi xảy ra, vui lòng thử lại!', 'danger');
        } finally {
            // Khôi phục nút nếu không chuyển trang
            if (!isRedirect) {
                setTimeout(() => {
                    button.disabled = false;
                    button.innerHTML = originalHTML;
                    button.classList.replace('btn-success', 'btn-outline-primary');
                }, 2000);
            } else {
                // Nếu lỗi Mua ngay thì mới mở lại nút
                button.disabled = false;
                button.innerHTML = originalHTML;
            }
        }
    };

    // Gán sự kiện cho nút "Thêm vào giỏ"
    document.querySelectorAll('.btn-add-cart').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            handleCartAction(btn, false);
        });
    });

    // Gán sự kiện cho nút "Mua ngay"
    document.querySelectorAll('.btn-buy-now').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            handleCartAction(btn, true);
        });
    });
});