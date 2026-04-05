// public/user-assets/js/cart-detail.js

document.addEventListener('DOMContentLoaded', function () {
    // Chỉ chạy khi đang ở trang giỏ hàng
    if (!document.querySelector('.cart-row')) return;

    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    // CẬP NHẬT TỔNG TIỀN TRÊN GIAO DIỆN
    function recalcTotal() {
        let total = 0;
        document.querySelectorAll('.cart-row').forEach(row => {
            const qty = parseInt(row.querySelector('.qty-input').value) || 0;
            const price = parseFloat(row.querySelector('.subtotal').dataset.price) || 0;
            const subtotal = qty * price;

            row.querySelector('.subtotal').textContent =
                subtotal.toLocaleString('vi-VN') + 'đ';
            total += subtotal;
        });

        const formatted = total.toLocaleString('vi-VN') + 'đ';
        const elSub = document.getElementById('summary-subtotal');
        const elTotal = document.getElementById('summary-total');
        if (elSub) elSub.textContent = formatted;
        if (elTotal) elTotal.textContent = formatted;

        // Cập nhật badge navbar
        let totalQty = 0;
        document.querySelectorAll('.qty-input').forEach(i => totalQty += parseInt(i.value) || 0);
        const badge = document.getElementById('cart-count');
        if (badge) badge.textContent = totalQty;
    }

    // NÚT TĂNG / GIẢM SỐ LƯỢNG
    document.querySelectorAll('.btn-qty').forEach(btn => {
        btn.addEventListener('click', function () {
            const productId = this.dataset.id;
            const action = this.dataset.action;
            const row = document.querySelector(`.cart-row[data-id="${productId}"]`);
            const input = row.querySelector('.qty-input');
            let qty = parseInt(input.value) || 1;

            if (action === 'plus') qty = Math.min(qty + 1, 99);
            if (action === 'minus') qty = Math.max(qty - 1, 1);

            input.value = qty;
            updateCart(productId, qty);
        });
    });

    // NHẬP TAY SỐ LƯỢNG
    document.querySelectorAll('.qty-input').forEach(input => {
        input.addEventListener('change', function () {
            let qty = parseInt(this.value) || 1;
            qty = Math.max(1, Math.min(qty, 99));
            this.value = qty;
            updateCart(this.dataset.id, qty);
        });
    });

    // GỌI API CẬP NHẬT SỐ LƯỢNG 
    function updateCart(productId, quantity) {
        fetch(window.CART.updateUrl + productId, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'X-HTTP-Method-Override': 'PATCH',
            },
            body: JSON.stringify({ quantity }),
        })
            .then(res => res.json())
            .then(data => {
                if (data.success) recalcTotal();
            })
            .catch(() => showToast('Cập nhật thất bại!', 'danger'));
    }

    // XÓA SẢN PHẨM
    document.querySelectorAll('.btn-remove').forEach(btn => {
        btn.addEventListener('click', function () {
            const productId = this.dataset.id;
            if (!confirm('Bạn có chắc muốn xóa sản phẩm này?')) return;

            fetch(window.CART.removeUrl + productId, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'X-HTTP-Method-Override': 'DELETE',
                },
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        const row = document.querySelector(`.cart-row[data-id="${productId}"]`);
                        row.style.transition = 'opacity 0.3s';
                        row.style.opacity = '0';
                        setTimeout(() => {
                            row.remove();
                            recalcTotal();
                            if (!document.querySelector('.cart-row')) location.reload();
                        }, 300);
                    }
                })
                .catch(() => showToast('Xóa thất bại!', 'danger'));
        });
    });
});