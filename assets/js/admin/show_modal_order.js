function showOrderDetail(orderId) {
    // Reset dữ liệu cũ trước khi nạp mới
    document.getElementById('od-items-list').innerHTML = '<tr><td colspan="4" class="text-center py-3">Đang tải...</td></tr>';

    fetch(`../functions/admin/orders/show_order.php?id=${orderId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const order = data.order;
                const items = data.items;

                // 1. Điền thông tin đơn hàng
                document.getElementById('od-code').innerText = order.order_code;
                document.getElementById('od-receiver-name').innerText = order.receiver_name;
                document.getElementById('od-receiver-phone').innerText = order.receiver_phone;
                document.getElementById('od-receiver-address').innerText = order.receiver_address;
                // ĐỊnh dạng ngày tháng
                const dateObj = new Date(order.created_at);
                const formattedDate = String(dateObj.getDate()).padStart(2, '0') + '/' +
                    String(dateObj.getMonth() + 1).padStart(2, '0') + '/' +
                    dateObj.getFullYear();
                document.getElementById('od-date').innerText = formattedDate;

                // Xử lý ghi chú
                const noteBox = document.getElementById('od-note-container');
                if (order.note) {
                    noteBox.style.display = 'block';
                    document.getElementById('od-note').innerText = order.note;
                } else {
                    noteBox.style.display = 'none';
                }

                // 2. Trạng thái thanh toán
                  const paymentMethods = {
                    cod: 'Tiền mặt (COD)',
                    momo: 'MoMo',
                    vnpay: 'VNPAY',
                    qr_code: 'QR CODE'
                };

                const method = (order.payment_method || '').trim().toLowerCase();

                document.getElementById('od-payment-method').innerText =
                    paymentMethods[method] || 'Không xác định';

                const pMap = {
                    'pending': '<span class="text-secondary fw-bold">Chưa thanh toán</span>',
                    'paid': '<span class="text-success fw-bold">Đã thanh toán</span>',
                    'failed': '<span class="text-danger fw-bold">Thất bại</span>',
                    'refunded': '<span class="text-warning fw-bold">Đã hoàn tiền</span>'
                };
                document.getElementById('od-payment-status').innerHTML = pMap[order.payment_status] || order.payment_status;

                // 3. Render danh sách sản phẩm (Bảng order_details)
                let html = '';
                items.forEach(item => {
                    html += `
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="admin_images/${item.product_thumbnail}" 
                                         class="rounded border me-2" style="width:40px; height:40px; object-fit:cover;">
                                    <span class="fw-bold">${item.product_name}</span>
                                </div>
                            </td>
                            <td class="text-center">${item.quantity}</td>
                            <td class="text-end">${Number(item.price).toLocaleString('vi-VN')}đ</td>
                            <td class="text-end fw-bold">${Number(item.subtotal).toLocaleString('vi-VN')}đ</td>
                        </tr>
                    `;
                });
                document.getElementById('od-items-list').innerHTML = html;
                document.getElementById('od-total-amount').innerText = Number(order.total_amount).toLocaleString('vi-VN') + 'đ';
            }
        })
        .catch(err => alert("Lỗi tải dữ liệu!"));
}