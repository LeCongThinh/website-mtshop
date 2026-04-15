// Khởi tạo biến toàn cục
const ctx = document.getElementById('revenueChart').getContext('2d');
let revenueChart;

// 1. Tự động đổ dữ liệu năm từ 2020 đến nay
const currentYear = new Date().getFullYear();
const selectYear = document.getElementById('selectYear');
if (selectYear) {
    for (let y = currentYear; y >= 2020; y--) {
        selectYear.add(new Option(`Năm ${y}`, y));
    }
}
// Hàm bổ sung: Tính toán các ngày trong một tuần cụ thể của một tháng/năm
function getDaysOfSpecificWeek(weekNumber, month, year) {
    const days = [];
    // Tìm ngày đầu tiên của tháng
    const firstDayOfMonth = new Date(year, month - 1, 1);
    // Tìm ngày Thứ 2 đầu tiên của tháng (hoặc ngày gần nhất trước đó nếu tuần 1 bắt đầu giữa chừng)
    const dayOfWeek = firstDayOfMonth.getDay();
    const diff = dayOfWeek === 0 ? 6 : dayOfWeek - 1;
    const firstMonday = new Date(firstDayOfMonth);
    firstMonday.setDate(firstDayOfMonth.getDate() - diff);

    // Di chuyển tới tuần được chọn (weekNumber - 1)
    const targetMonday = new Date(firstMonday);
    targetMonday.setDate(firstMonday.getDate() + (weekNumber - 1) * 7);

    for (let i = 0; i < 7; i++) {
        const nextDay = new Date(targetMonday);
        nextDay.setDate(targetMonday.getDate() + i);
        const d = String(nextDay.getDate()).padStart(2, '0');
        const m = String(nextDay.getMonth() + 1).padStart(2, '0');
        days.push(`${d}/${m}`);
    }
    return days;
}
// 2. Khởi tạo Chart
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
                        // Click vào tháng trong biểu đồ Năm
                        const selectedMonth = index + 1;
                        document.getElementById('filterType').value = 'month';
                        document.getElementById('selectMonth').value = selectedMonth;
                        updateReport();
                    }
                    else if (currentType === 'month') {
                        // Click vào tuần trong biểu đồ Tháng
                        // Thay vì dùng dữ liệu giả, ta chuyển hướng sang view Tuần và để PHP lo data
                        document.getElementById('filterType').value = 'week';
                        updateReport();
                    }
                }
            },
            scales: {
                y: { 
                    beginAtZero: true, 
                    ticks: { callback: v => v.toLocaleString('vi-VN') + 'đ' } 
                }
            },
            onHover: (event, chartElement) => {
                event.native.target.style.cursor = chartElement[0] ? 'pointer' : 'default';
            }
        }
    });
}
// Hàm xử lý "Khoan sâu" xuống tuần (giữ nguyên logic tính ngày của bạn)
function drillDownToWeek(weekNumber) {
    const month = document.getElementById('selectMonth').value;
    const year = document.getElementById('selectYear').value;

    document.getElementById('filterType').value = 'week';

    const weekDays = getDaysOfSpecificWeek(weekNumber, month, year);
    const dayNames = ['T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'CN'];
    const labels = dayNames.map((name, index) => `${name} (${weekDays[index]})`);

    // Reset giao diện bộ lọc
    document.getElementById('selectMonth').classList.add('d-none');
    document.getElementById('selectYear').classList.add('d-none');

    revenueChart.data.labels = labels;
    revenueChart.data.datasets[0].data = [15, 25, 20, 35, 50, 40, 60].map(x => x * 1000000);
    revenueChart.update();
}
// Hàm lấy các ngày của tuần hiện tại
function getDaysInCurrentWeek() {
    const days = [];
    const now = new Date();
    const currentDay = now.getDay();
    const diffToMonday = currentDay === 0 ? 6 : currentDay - 1;

    const monday = new Date(now);
    monday.setDate(now.getDate() - diffToMonday);

    for (let i = 0; i < 7; i++) {
        const nextDay = new Date(monday);
        nextDay.setDate(monday.getDate() + i);

        const d = String(nextDay.getDate()).padStart(2, '0');
        const m = String(nextDay.getMonth() + 1).padStart(2, '0');
        days.push(`${d}/${m}`);
    }
    return days;
}

// 3. Logic cập nhật dữ liệu (Đã sửa lỗi hiển thị)
function updateReport() {
    const type = document.getElementById('filterType').value;
    const month = document.getElementById('selectMonth').value;
    const year = document.getElementById('selectYear').value;
    const monthEl = document.getElementById('selectMonth');
    const yearEl = document.getElementById('selectYear');

    // Quản lý hiển thị Select
    monthEl.classList.toggle('d-none', type !== 'month');
    yearEl.classList.toggle('d-none', type === 'week');

    // Gọi AJAX
    fetch(`index.php?action=get_revenue_data&type=${type}&month=${month}&year=${year}`)
        .then(response => response.json())
        .then(res => {
            let finalLabels = res.labels;

            if (type === 'week') {
                const dayNames = ['T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'CN'];
                finalLabels = res.labels.map((dateStr, index) => `${dayNames[index]} (${dateStr})`);
            }

            revenueChart.data.labels = finalLabels;
            revenueChart.data.datasets[0].data = res.data;
            revenueChart.update();
        })
        .catch(err => console.error("Lỗi cập nhật biểu đồ:", err));
}

// Đăng ký sự kiện
document.getElementById('filterType').addEventListener('change', updateReport);
document.getElementById('selectMonth').addEventListener('change', updateReport);
document.getElementById('selectYear').addEventListener('change', updateReport);

// Khởi tạo
initChart();
updateReport();
