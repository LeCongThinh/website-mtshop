// --- 1. Xử lý Preview Ảnh đại diện ---
document.getElementById('thumb-input').addEventListener('change', function (e) {
    const reader = new FileReader();
    reader.onload = function (e) {
        document.getElementById('thumb-img').src = e.target.result;
    }
    if (this.files[0]) reader.readAsDataURL(this.files[0]);
});

// --- 2. Xử lý Preview Album (Cộng dồn) ---
const albumInput = document.getElementById('album-input');
const albumContainer = document.getElementById('album-preview-container');

albumInput.addEventListener('change', function () {
    const files = Array.from(this.files);

    files.forEach((file, index) => {
        const reader = new FileReader();
        reader.onload = function (e) {
            const div = document.createElement('div');
            div.className = 'col-4 preview-item new-img';
            div.innerHTML = `
                <img src="${e.target.result}" class="img-fluid rounded border img-thumbnail-custom shadow-sm">
                <span class="btn-remove-img" onclick="removeNewImage(this, ${index})">×</span>
            `;
            albumContainer.appendChild(div);
        }
        reader.readAsDataURL(file);
    });
});

// Xóa ảnh Cũ (đã có trong DB)
function removeOldImage(btn, imgId) {
    if (confirm('Bạn có chắc muốn xóa ảnh này khỏi album?')) {
        const parent = btn.closest('.preview-item');
        // Điền ID vào input ẩn để PHP xử lý xóa trong DB
        parent.querySelector('.remove-id-input').value = imgId;
        parent.style.display = 'none';
    }
}

// Xóa ảnh Mới (vừa chọn từ máy tính)
function removeNewImage(btn, index) {
    btn.closest('.preview-item').remove();
}