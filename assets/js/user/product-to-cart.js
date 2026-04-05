document.addEventListener('DOMContentLoaded', function () {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    document.querySelectorAll('.btn-add-cart').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const productId = this.dataset.id;
            const button = this;

            button.disabled = true;
            button.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Đang thêm...';

            // Dùng window.CART.addUrl thay cho {{ route() }}
            fetch(window.CART.addUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    product_id: productId,
                    quantity: 1,
                }),
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        const badge = document.getElementById('cart-count');
                        if (badge) badge.textContent = data.count;

                        button.classList.remove('btn-outline-primary');
                        button.classList.add('btn-success');
                        button.innerHTML = '<i class="bi bi-check-lg me-1"></i> Đã thêm!';

                        setTimeout(() => {
                            button.disabled = false;
                            button.classList.remove('btn-success');
                            button.classList.add('btn-outline-primary');
                            button.innerHTML = '<i class="bi bi-cart-plus me-1"></i> Thêm vào giỏ hàng';
                        }, 2000);

                    } else {
                        showToast('Có lỗi xảy ra, vui lòng thử lại!', 'danger');
                        button.disabled = false;
                        button.innerHTML = '<i class="bi bi-cart-plus me-1"></i> Thêm vào giỏ hàng';
                    }
                })
                .catch(() => {
                    showToast('Mất kết nối, vui lòng thử lại!', 'danger');
                    button.disabled = false;
                    button.innerHTML = '<i class="bi bi-cart-plus me-1"></i> Thêm vào giỏ hàng';
                });
        });
    });

    // Nút mua ngay: thêm sp vào giỏ hàng và chuyển đến trang chi tiết giỏ hàng 
    document.querySelectorAll('.btn-buy-now').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const productId = this.dataset.id;
            const button = this;

            button.disabled = true;
            button.innerHTML = `
            <span class="spinner-border spinner-border-sm me-1"></span>
            <span class="fs-5 d-block">Đang xử lý...</span>
        `;

            fetch(window.CART.addUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    product_id: productId,
                    quantity: 1,
                }),
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        // Cập nhật badge rồi chuyển sang trang giỏ hàng
                        const badge = document.getElementById('cart-count');
                        if (badge) badge.textContent = data.count;

                        window.location.href = window.CART.cartUrl;
                    } else {
                        showToast('Có lỗi xảy ra, vui lòng thử lại!', 'danger');
                        button.disabled = false;
                        button.innerHTML = `
                    <span class="fs-5 d-block">MUA NGAY</span>
                    <span class="d-block fw-normal opacity-75" style="font-size: 0.75rem;">
                        (Giao nhanh từ 2 giờ hoặc nhận tại cửa hàng)
                    </span>
                `;
                    }
                })
                .catch(() => {
                    showToast('Mất kết nối, vui lòng thử lại!', 'danger');
                    button.disabled = false;
                    button.innerHTML = `
                <span class="fs-5 d-block">MUA NGAY</span>
                <span class="d-block fw-normal opacity-75" style="font-size: 0.75rem;">
                    (Giao nhanh từ 2 giờ hoặc nhận tại cửa hàng)
                </span>
            `;
                });
        });
    });

});

function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `toast-notify toast-${type}`;
    toast.textContent = message;
    document.body.appendChild(toast);
    setTimeout(() => toast.classList.add('show'), 10);
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}