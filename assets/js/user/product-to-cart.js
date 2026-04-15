document.addEventListener('DOMContentLoaded', function () {
    console.log('MTShop: Shopping Cart System Ready');

    // Cấu hình linh hoạt từ window.CART
    const CONFIG = {
        addUrl: window.CART.addUrl, 
        cartUrl: window.CART.cartUrl,
        badgeSelector: '#cart-count'
    }

    // --- 1. Xử lý cho tất cả các nút Thêm/Mua ---
    const handleCartAction = async (button, isRedirect = false) => {
        const productId = button.dataset.id;
        const originalHTML = button.innerHTML;

        if (!productId) return;

        // Hiệu ứng Loading
        button.disabled = true;
        button.innerHTML = `<span class="spinner-border spinner-border-sm" role="status"></span> ${isRedirect ? 'Đang xử lý...' : ''}`;
        
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
                    window.location.href = CONFIG.cartUrl;
                } else {
                    button.innerHTML = '<i class="bi bi-check-lg me-1"></i> Đã thêm';
                    button.classList.replace('btn-outline-primary', 'btn-success');
                    
                    // SỬA TẠI ĐÂY: Dùng showAlert thay cho showToast
                    if (typeof showAlert === 'function') {
                        showAlert('mainAlert', 'Đã thêm sản phẩm vào giỏ hàng!', 'success', 3000);
                    }
                }
            } else {
                // SỬA TẠI ĐÂY: Dùng showAlert
                if (typeof showAlert === 'function') {
                    showAlert('mainAlert', data.message || 'Không thể thêm hàng', 'warning', 3000);
                }
            }
        } catch (err) {
            console.error('Cart Error:', err);
            // SỬA TẠI ĐÂY: Dùng showAlert
            if (typeof showAlert === 'function') {
                showAlert('mainAlert', 'Có lỗi xảy ra, vui lòng thử lại!', 'danger', 3000);
            }
        } finally {
            if (!isRedirect) {
                setTimeout(() => {
                    button.disabled = false;
                    button.innerHTML = originalHTML;
                    button.classList.replace('btn-success', 'btn-outline-primary');
                }, 2000);
            } else {
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