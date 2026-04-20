<?php
include __DIR__ . '/../../functions/admin/dashboard/dashboard-controller.php';

?>
<div class="container-fluid">
    <div class="categ-header mb-4">
        <div class="sub-title d-flex align-items-center gap-2">
            <span class="shape bg-primary"
                style="width: 5px; height: 25px; display: inline-block; border-radius: 10px;"></span>
            <h4 class="mb-0 fw-bold">Thống kê hệ thống</h4>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 position-relative overflow-hidden text-white"
                style="background: linear-gradient(45deg, #0d6efd, #0dcaf0);">
                <div class="card-body p-4">
                    <h6 class="text-white-50 small text-uppercase fw-bold">Tổng doanh thu</h6>
                    <h3 class="fw-bold mb-1"><?php echo number_format($rev_now, 0, ',', '.'); ?> đ</h3>
                    <div class="d-flex align-items-center">
                        <span class="badge bg-white bg-opacity-25 small fw-bold">
                            <i class="fas <?php echo $rev_percent >= 0 ? 'fa-arrow-up' : 'fa-arrow-down'; ?> me-1"></i>
                            <?php echo abs($rev_percent); ?>%
                        </span>
                        <span class="ms-2 small text-white-50">so với tháng trước</span>
                    </div>
                    <i class="fas fa-wallet position-absolute opacity-25"
                        style="font-size: 4rem; right: -10px; bottom: -10px;"></i>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 position-relative overflow-hidden text-white"
                style="background: linear-gradient(45deg, #198754, #20c997);">
                <div class="card-body p-4">
                    <h6 class="text-white-50 small text-uppercase fw-bold">Tổng đơn hàng</h6>
                    <h3 class="fw-bold mb-1"><?php echo $total_orders; ?> đơn</h3>
                    <div class="d-flex align-items-center">
                        <span class="badge bg-white bg-opacity-25 small fw-bold">
                            <i class="fas fa-arrow-up me-1"></i><?php echo $complete_rate; ?>%
                        </span>
                        <span class="ms-2 small text-white-50">đơn hoàn tất</span>
                    </div>
                    <i class="fas fa-shopping-cart position-absolute opacity-25"
                        style="font-size: 4rem; right: -10px; bottom: -10px;"></i>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 position-relative overflow-hidden text-white"
                style="background: linear-gradient(45deg, #f59e0b, #fbbf24);">
                <div class="card-body p-4">
                    <h6 class="text-white-50 small text-uppercase fw-bold">Khách hàng mới</h6>
                    <h3 class="fw-bold mb-1"><?php echo $new_customers; ?> khách</h3>
                    <div class="d-flex align-items-center">
                        <span
                            class="badge <?php echo $cust_percent >= 0 ? 'bg-success' : 'bg-danger'; ?> bg-opacity-25 small fw-bold text-white">
                            <i class="fas <?php echo $cust_percent >= 0 ? 'fa-arrow-up' : 'fa-arrow-down'; ?> me-1"></i>
                            <?php echo abs($cust_percent); ?>%
                        </span>
                        <span class="ms-2 small text-white-50">trong 7 ngày</span>
                    </div>
                    <i class="fas fa-users position-absolute opacity-25"
                        style="font-size: 4rem; right: -10px; bottom: -10px;"></i>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 position-relative overflow-hidden text-white"
                style="background: linear-gradient(45deg, #dc3545, #f87171);">
                <div class="card-body p-4">
                    <h6 class="text-white-50 small text-uppercase fw-bold">Đơn chờ duyệt</h6>
                    <h2 class="fw-bold mb-1"><?php echo $pending_orders; ?> đơn</h2>
                    <div class="d-flex align-items-center">
                        <span class="badge bg-white text-danger small fw-bold">Cần xử lý ngay</span>
                    </div>
                    <i class="fas fa-exclamation-circle position-absolute opacity-50"
                        style="font-size: 4rem; right: -10px; bottom: -10px;"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow rounded-4 mb-4">
                <div class="card-header bg-white border-0 p-4">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                        <h6 class="fw-bold mb-0">Báo cáo doanh thu</h6>

                        <div class="d-flex gap-2">
                            <select id="filterType" class="form-select form-select-sm w-auto bg-light border-0">
                                <option value="week">Thống kê theo ngày</option>
                                <option value="month">Thống kê theo tháng</option>
                                <option value="year">Thống kê theo năm</option>
                            </select>

                            <select id="selectMonth" class="form-select form-select-sm w-auto bg-light border-0 d-none">
                                <option value="1">Tháng 1</option>
                                <option value="2">Tháng 2</option>
                                <option value="3">Tháng 3</option>
                                <option value="4">Tháng 4</option>
                                <option value="5">Tháng 5</option>
                                <option value="6">Tháng 6</option>
                                <option value="7">Tháng 7</option>
                                <option value="8">Tháng 8</option>
                                <option value="9">Tháng 9</option>
                                <option value="10">Tháng 10</option>
                                <option value="11">Tháng 11</option>
                                <option value="12">Tháng 12</option>
                            </select>

                            <select id="selectYear" class="form-select form-select-sm w-auto bg-light border-0 d-none">
                            </select>
                        </div>
                    </div>
                </div>
                <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const canvas = document.getElementById('revenueChart');
                        if (!canvas) return;
                        const ctx = canvas.getContext('2d');
                        let revenueChart;

                        // 1. Khởi tạo Năm
                        const currentYear = new Date().getFullYear();
                        const selectYear = document.getElementById('selectYear');
                        if (selectYear) {
                            for (let y = currentYear; y >= 2020; y--) {
                                selectYear.add(new Option(`Năm ${y}`, y));
                            }
                        }

                        // 2. Hàm khởi tạo Chart
                        function initChart() {
                            revenueChart = new Chart(ctx, {
                                type: 'bar',
                                data: {
                                    labels: [],
                                    datasets: [{
                                        label: 'Doanh thu (VNĐ)',
                                        data: [],
                                        backgroundColor: '#4e73df',
                                        borderRadius: 5
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    onClick: (event, elements) => {
                                        if (elements.length > 0) {
                                            const index = elements[0].index;
                                            const currentType = document.getElementById('filterType').value;

                                            if (currentType === 'year') {
                                                document.getElementById('filterType').value = 'month';
                                                document.getElementById('selectMonth').value = index + 1;
                                                updateReport();
                                            } else if (currentType === 'month') {
                                                const month = document.getElementById('selectMonth').value;
                                                const year = document.getElementById('selectYear').value;

                                                document.getElementById('filterType').value = 'week';

                                                // Tính toán ngày đại diện của tuần đó (Ví dụ Tuần 2 là ngày 08)
                                                const startDay = (index * 7) + 1;
                                                const targetDate = `${year}-${month.toString().padStart(2, '0')}-${startDay.toString().padStart(2, '0')}`;

                                                // QUAN TRỌNG: Truyền ngày vào hàm update
                                                updateReport(targetDate);
                                            }
                                        }
                                    },
                                    scales: {
                                        y: {
                                            beginAtZero: true,
                                            ticks: {
                                                callback: function(value) {
                                                    return value.toLocaleString('vi-VN') + 'đ';
                                                }
                                            }
                                        }
                                    },
                                    onHover: (event, chartElement) => {
                                        event.native.target.style.cursor = chartElement[0] ? 'pointer' : 'default';
                                    }
                                }
                            });
                        }

                        // 3. Hàm cập nhật dữ liệu (SỬA LỖI NHẬN THAM SỐ)
                        function updateReport(specificDate = null) {
                            const type = document.getElementById('filterType').value;
                            const month = document.getElementById('selectMonth').value;
                            const year = document.getElementById('selectYear').value;

                            // Quản lý hiển thị select
                            document.getElementById('selectMonth').classList.toggle('d-none', type !== 'month');
                            document.getElementById('selectYear').classList.toggle('d-none', type === 'week');

                            // Xây dựng URL
                            let url = `index.php?action=get_revenue_data&type=${type}&month=${month}&year=${year}`;

                            // Nếu nhấn từ cột "Tuần" sang, ta đính kèm thêm ngày bắt đầu
                            if (specificDate) {
                                url += `&start_date=${specificDate}`;
                            }

                            fetch(url)
                                .then(response => response.json())
                                .then(res => {
                                    let finalLabels = res.labels;
                                    if (type === 'week') {
                                        finalLabels = res.labels; // chỉ giữ dd/mm
                                    } else {
                                        finalLabels = res.labels;
                                    }

                                    revenueChart.data.labels = finalLabels;
                                    // Đảm bảo data là số để trục Y không bị nhảy lỗi 0,1đ
                                    revenueChart.data.datasets[0].data = res.data;
                                    revenueChart.update();
                                })
                                .catch(err => console.error("Lỗi cập nhật biểu đồ:", err));
                        }

                        // Đăng ký sự kiện
                        document.getElementById('filterType').addEventListener('change', () => updateReport());
                        document.getElementById('selectMonth').addEventListener('change', () => updateReport());
                        document.getElementById('selectYear').addEventListener('change', () => updateReport());

                        initChart();
                        updateReport();
                    });
                </script>
                <div class="card-body p-4 pt-0">
                    <div style="height: 350px; position: relative;">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow rounded-4 mt-4">
                <div class="card-header bg-white border-0 p-4 pb-0">
                    <h6 class="fw-bold mb-0 text-dark">Top sản phẩm bán chạy</h6>
                </div>
                <div class="card-body p-4">
                    <div class="table-responsive">
                        <table class="table align-middle table-hover">
                            <thead class="table-light">
                                <tr class="small text-muted text-uppercase"
                                    style="font-size: 0.65rem; letter-spacing: 0.5px;">
                                    <th class="border-0 ps-3">Sản phẩm</th>
                                    <th class="border-0 text-center">Giá bán</th>
                                    <th class="border-0 text-center">SL</th>
                                    <th class="border-0 text-end pe-3">Doanh thu</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (mysqli_num_rows($run_top_products) > 0):
                                    while ($row = mysqli_fetch_assoc($run_top_products)):
                                ?>
                                        <tr>
                                            <td class="ps-3">
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-light rounded-2 overflow-hidden me-3 d-flex align-items-center justify-content-center border"
                                                        style="width: 45px; height: 45px;">
                                                        <img src="admin_images/<?php echo $row['product_thumbnail']; ?>"
                                                            class="img-fluid" alt=""
                                                            style="object-fit: cover; height: 100%; width: 100%;">
                                                    </div>
                                                    <div>
                                                        <span
                                                            class="fw-bold text-dark d-block small"><?php echo $row['product_name']; ?></span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center small text-dark">
                                                <?php echo number_format($row['price'], 0, ',', '.'); ?>đ
                                            </td>
                                            <td class="text-center">
                                                <span class="badge rounded-pill bg-primary bg-opacity-10 text-primary px-3">
                                                    <?php echo number_format($row['total_sold']); ?>
                                                </span>
                                            </td>
                                            <td class="text-end pe-3 fw-bold text-dark small">
                                                <?php echo number_format($row['total_revenue'], 0, ',', '.'); ?>đ
                                            </td>
                                        </tr>
                                <?php
                                    endwhile;
                                else:
                                    echo '<tr><td colspan="4" class="text-center py-5 text-muted small">Chưa có dữ liệu bán hàng</td></tr>';
                                endif;
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow rounded-4 mb-4">
                <div class="card-header bg-white border-0 p-4 pb-0">
                    <h6 class="fw-bold mb-0">Đơn hàng mới nhất</h6>
                </div>
                <div class="card-body p-4">
                    <?php
                    if (mysqli_num_rows($run_latest_orders) > 0):
                        while ($order = mysqli_fetch_assoc($run_latest_orders)):
                            // Xử lý màu sắc badge dựa trên trạng thái
                            $status_class = match ($order['status']) {
                                'pending' => ['bg' => 'bg-warning-subtle', 'text' => 'text-warning', 'label' => 'Chờ duyệt', 'icon' => 'fa-clock'],
                                'delivered' => ['bg' => 'bg-success-subtle', 'text' => 'text-success', 'label' => 'Hoàn tất', 'icon' => 'fa-check'],
                                'cancelled' => ['bg' => 'bg-danger bg-opacity-10', 'text' => 'text-danger', 'label' => 'Đã hủy', 'icon' => 'fa-circle-xmark'],
                                default => ['bg' => 'bg-primary-subtle', 'text' => 'text-primary', 'label' => $order['status'], 'icon' => 'fa-shopping-bag']
                            };
                    ?>
                            <div class="d-flex align-items-center mb-4 border-bottom pb-3">
                                <div class="flex-shrink-0 <?php echo $status_class['bg'] . ' ' . $status_class['text']; ?> rounded-circle d-flex align-items-center justify-content-center"
                                    style="width: 40px; height: 40px;">
                                    <i class="fas <?php echo $status_class['icon']; ?>"></i>
                                </div>

                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-0 small fw-bold">Mã đơn: #<?php echo $order['order_code'] ?? $order['id']; ?>
                                    </h6>
                                    <p class="mb-0 text-muted small">
                                        <?php echo htmlspecialchars($order['customer_name'] ?? 'Khách vãng lai'); ?> -
                                        <span
                                            class="text-lowercase"><?php echo date('d/m/Y', strtotime($order['created_at'])); ?></span>
                                    </p>
                                </div>

                                <div class="text-end">
                                    <span class="badge <?php echo $status_class['bg'] . ' ' . $status_class['text']; ?> small">
                                        <?php echo $status_class['label']; ?>
                                    </span>
                                </div>
                            </div>
                    <?php
                        endwhile;
                    else:
                        echo '<p class="text-center text-muted">Chưa có đơn hàng nào.</p>';
                    endif;
                    ?>

                    <a href="index.php?view_order" class="btn btn-outline-primary btn-sm w-100 rounded-3 py-2">Xem tất
                        cả đơn hàng</a>
                </div>
            </div>

            <div class="card border-0 shadow rounded-4 mt-4">
                <div class="card-header bg-white border-0 p-4 pb-0">
                    <h6 class="fw-bold mb-0">
                        <i class="fas fa-chart-bar me-2 text-primary"></i>Trạng thái đơn hàng
                    </h6>
                </div>
                <div class="card-body p-4">

                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="small fw-bold text-muted">Chờ xác nhận</span>
                            <span class="small fw-bold"><?php echo $counts['pending_count']; ?> đơn</span>
                        </div>
                        <div class="progress bg-warning bg-opacity-10" style="height: 8px; border-radius: 10px;">
                            <div class="progress-bar bg-warning" role="progressbar"
                                style="width: <?php echo $p_pending; ?>%" aria-valuenow="<?php echo $p_pending; ?>"
                                aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="small fw-bold text-muted">Đã xác nhận</span>
                            <span class="small fw-bold"><?php echo $counts['confirmed_count']; ?> đơn</span>
                        </div>
                        <div class="progress bg-info bg-opacity-10" style="height: 8px; border-radius: 10px;">
                            <div class="progress-bar bg-info" role="progressbar"
                                style="width: <?php echo $p_confirmed; ?>%" aria-valuenow="<?php echo $p_confirmed; ?>"
                                aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="small fw-bold text-muted">Đang giao</span>
                            <span class="small fw-bold"><?php echo $counts['shipping_count']; ?> đơn</span>
                        </div>
                        <div class="progress bg-primary bg-opacity-10" style="height: 8px; border-radius: 10px;">
                            <div class="progress-bar bg-primary" role="progressbar"
                                style="width: <?php echo $p_shipping; ?>%" aria-valuenow="<?php echo $p_shipping; ?>"
                                aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="small fw-bold text-muted">Giao thành công</span>
                            <span class="small fw-bold"><?php echo $counts['delivered_count']; ?> đơn</span>
                        </div>
                        <div class="progress bg-success bg-opacity-10" style="height: 8px; border-radius: 10px;">
                            <div class="progress-bar bg-success" role="progressbar"
                                style="width: <?php echo $p_delivered; ?>%" aria-valuenow="<?php echo $p_delivered; ?>"
                                aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>

                    <div class="mb-0">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="small fw-bold text-muted">Đã hủy</span>
                            <span class="small fw-bold"><?php echo $counts['cancelled_count']; ?> đơn</span>
                        </div>
                        <div class="progress bg-danger bg-opacity-10" style="height: 8px; border-radius: 10px;">
                            <div class="progress-bar bg-danger" role="progressbar"
                                style="width: <?php echo $p_cancelled; ?>%" aria-valuenow="<?php echo $p_cancelled; ?>"
                                aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Bo góc cho Badge của Bootstrap 5 */
    .badge {
        font-weight: 500;
        padding: 0.5em 0.8em;
        border-radius: 8px;
    }

    /* Các màu nhạt cho Badge (Soft Colors) */
    .bg-primary-subtle {
        background-color: #e7f1ff;
    }

    .bg-warning-subtle {
        background-color: #fff3cd;
    }

    .bg-success-subtle {
        background-color: #d1e7dd;
    }

    /* Đường viền nét đứt cho placeholder biểu đồ */
    .border-dashed {
        border: 2px dashed #dee2e6 !important;
    }
</style>