<?php
// 1. Lấy dữ liệu lọc từ URL
$f_day = $_GET['f_day'] ?? '';
$f_month = $_GET['f_month'] ?? '';
$f_year = $_GET['f_year'] ?? date('Y');

// 2. Gọi dữ liệu từ Functions (Đảm bảo các hàm này đã có trong statistics_functions.php)
$stats = get_total_stats($con);
$filtered_revenue = get_revenue_by_filter($con, $f_day, $f_month, $f_year);
$latest_orders = get_latest_orders($con);
$top_products = get_top_selling_products($con);
$top_customers = get_top_customers($con);
$chart_data = get_monthly_revenue($con, $f_year); 
?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="container-fluid p-0">
    
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <h6 class="fw-bold mb-3 text-primary"><i class="fas fa-filter me-2"></i>Bộ lọc báo cáo</h6>
            <form action="index.php" method="GET" class="row g-2 align-items-end">
                <input type="hidden" name="dashboard" value="">
                <div class="col-md-2">
                    <label class="small fw-bold">Ngày</label>
                    <select name="f_day" class="form-select form-select-sm">
                        <option value="">-- Tất cả --</option>
                        <?php for ($i = 1; $i <= 31; $i++) echo "<option value='$i' " . ($f_day == $i ? 'selected' : '') . ">$i</option>"; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="small fw-bold">Tháng</label>
                    <select name="f_month" class="form-select form-select-sm">
                        <option value="">-- Tất cả --</option>
                        <?php for ($i = 1; $i <= 12; $i++) echo "<option value='$i' " . ($f_month == $i ? 'selected' : '') . ">Tháng $i</option>"; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="small fw-bold">Năm</label>
                    <select name="f_year" class="form-select form-select-sm">
                        <?php $y = date('Y'); for ($i = $y; $i >= $y - 3; $i--) echo "<option value='$i' " . ($f_year == $i ? 'selected' : '') . ">Năm $i</option>"; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary btn-sm px-3">Áp dụng</button>
                    <a href="index.php?dashboard" class="btn btn-light btn-sm border px-3">Mặc định</a>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm p-3 text-white h-100" style="background: linear-gradient(45deg, #4e73df, #224abe);">
                <p class="small fw-bold mb-1 text-uppercase text-white-50">Doanh thu theo bộ lọc</p>
                <h2 class="fw-bold mb-0"><?= number_format($filtered_revenue, 0, ',', '.') ?>đ</h2>
                <hr class="my-2 opacity-25">
                <small><i class="fas fa-calendar-alt me-1"></i> <?= ($f_day ? "Ngày $f_day " : "") . ($f_month ? "Tháng $f_month " : "") . "Năm $f_year" ?></small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3 h-100 text-center" style="border-top: 4px solid #1cc88a;">
                <p class="text-muted small fw-bold mb-1 text-uppercase">Đơn hàng mới</p>
                <h3 class="fw-bold text-success mb-0"><?= mysqli_num_rows($latest_orders) ?></h3>
                <small class="text-muted">Đơn gần đây</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3 h-100 text-center" style="border-top: 4px solid #f6c23e;">
                <p class="text-muted small fw-bold mb-1 text-uppercase">Tổng doanh thu</p>
                <h4 class="fw-bold text-warning mb-0"><?= number_format($stats['revenue'], 0, ',', '.') ?>đ</h4>
                <small class="text-muted">Toàn hệ thống</small>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm p-4 h-100">
                <h6 class="fw-bold mb-4"><i class="fas fa-chart-line me-2 text-primary"></i>Biến động doanh thu <?= $f_year ?></h6>
                <canvas id="revenueChart" style="max-height: 320px;"></canvas>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3 fw-bold text-success border-0">
                    <i class="fas fa-crown me-2"></i>Top 5 khách hàng VIP
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <tbody>
                                <?php if ($top_customers && mysqli_num_rows($top_customers) > 0): 
                                    while($cust = mysqli_fetch_assoc($top_customers)): ?>
                                    <tr>
                                        <td class="ps-3">
                                            <div class="small fw-bold text-dark"><?= htmlspecialchars($cust['name']) ?></div>
                                            <div class="text-muted" style="font-size: 10px;">Khách hàng thân thiết</div>
                                        </td>
                                        <td class="text-end pe-3">
                                            <span class="small fw-bold text-primary"><?= number_format($cust['total_spent'], 0, ',', '.') ?>đ</span>
                                        </td>
                                    </tr>
                                <?php endwhile; else: ?>
                                    <tr><td colspan="2" class="text-center py-4 text-muted small">Chưa có dữ liệu</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3 fw-bold"><i class="fas fa-fire me-2 text-danger"></i>Top 5 sản phẩm bán chạy</div>
        <div class="card-body">
            <div class="row">
                <?php if ($top_products && mysqli_num_rows($top_products) > 0): 
                    mysqli_data_seek($top_products, 0);
                    while($p = mysqli_fetch_assoc($top_products)): ?>
                    <div class="col-md-4 mb-3">
                        <div class="p-3 border rounded shadow-sm">
                            <div class="d-flex justify-content-between small mb-2">
                                <span class="text-truncate fw-bold" style="max-width: 70%;"><?= htmlspecialchars($p['name']) ?></span>
                                <span class="badge bg-danger"><?= $p['total_sold'] ?> SP</span>
                            </div>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar bg-danger" style="width: 85%;"></div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; endif; ?>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h6 class="fw-bold mb-0">Đơn hàng mới nhất</h6>
            <a href="index.php?view_order" class="btn btn-sm btn-outline-primary">Xem tất cả</a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small text-uppercase">
                        <tr>
                            <th class="ps-3">Mã đơn</th>
                            <th>Khách hàng</th>
                            <th>Tổng tiền</th>
                            <th class="text-center">Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(mysqli_num_rows($latest_orders) > 0): 
                            mysqli_data_seek($latest_orders, 0);
                            while($order = mysqli_fetch_assoc($latest_orders)): ?>
                            <tr>
                                <td class="ps-3 fw-bold">#<?= $order['order_code'] ?></td>
                                <td><?= htmlspecialchars($order['user_name']) ?></td>
                                <td class="text-danger fw-bold"><?= number_format($order['total_amount'], 0, ',', '.') ?>đ</td>
                                <td class="text-center"><span class="badge rounded-pill bg-primary px-3">Mới</span></td>
                            </tr>
                        <?php endwhile; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    const ctx = document.getElementById('revenueChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['T1', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'T8', 'T9', 'T10', 'T11', 'T12'],
            datasets: [{
                label: 'Doanh thu',
                data: <?= json_encode($chart_data) ?>,
                borderColor: '#4e73df',
                backgroundColor: 'rgba(78, 115, 223, 0.1)',
                fill: true,
                tension: 0.4,
                pointRadius: 5,
                pointBackgroundColor: '#4e73df'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { 
                    beginAtZero: true, 
                    ticks: { callback: v => v.toLocaleString() + 'đ' } 
                }
            }
        }
    });
</script>