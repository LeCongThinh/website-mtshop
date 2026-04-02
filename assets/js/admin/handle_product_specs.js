// Thêm vào file JS xử lý form của bạn
document.getElementById('add-spec').addEventListener('click', function() {
    const container = document.getElementById('specs-container');
    
    // Tạo dòng mới
    const newRow = document.createElement('div');
    newRow.className = 'row g-2 mb-2 spec-row animate__animated animate__fadeInUp'; // Thêm hiệu ứng nếu có animate.css
    newRow.innerHTML = `
        <div class="col-md-5">
            <input type="text" name="spec_key[]" class="form-control shadow-sm" placeholder="Tên thông số">
        </div>
        <div class="col-md-6">
            <input type="text" name="spec_value[]" class="form-control shadow-sm" placeholder="Giá trị">
        </div>
        <div class="col-md-1">
            <button type="button" class="btn btn-outline-danger w-100 remove-spec">
                <i class="fas fa-trash-alt"></i>
            </button>
        </div>
    `;
    
    container.appendChild(newRow);

    // Tự động cuộn xuống cuối khi thêm dòng mới
    container.scrollTo({
        top: container.scrollHeight,
        behavior: 'smooth'
    });
});

// Xử lý xóa dòng
document.addEventListener('click', function(e) {
    if (e.target.closest('.remove-spec')) {
        const rows = document.querySelectorAll('.spec-row');
        // Giữ lại ít nhất 1 dòng để giao diện không bị trống
        if (rows.length > 1) {
            e.target.closest('.spec-row').remove();
        } else {
            // Nếu chỉ còn 1 dòng thì chỉ xóa nội dung input chứ không xóa row
            const inputs = e.target.closest('.spec-row').querySelectorAll('input');
            inputs.forEach(input => input.value = '');
        }
    }
});