document.addEventListener("DOMContentLoaded", function () {
    const wrapper = document.getElementById('descWrapper');
    const btn = document.getElementById('btnToggleDesc');
    const gradient = document.getElementById('descGradient');

    // CHỈ CHẠY NẾU CÁC PHẦN TỬ NÀY TỒN TẠI (Trang chi tiết sản phẩm)
    if (wrapper && btn && gradient) {
        if (wrapper.scrollHeight <= 500) {
            btn.style.display = 'none';
            gradient.style.display = 'none';
        }

        btn.addEventListener('click', function () {
            const isExpanded = wrapper.classList.contains('expanded');
            if (isExpanded) {
                wrapper.classList.remove('expanded');
                wrapper.style.maxHeight = "400px";
                btn.innerHTML = 'Đọc tiếp bài viết <i class="bi bi-chevron-down ms-1"></i>';
                wrapper.scrollIntoView({ behavior: 'smooth' });
            } else {
                wrapper.classList.add('expanded');
                wrapper.style.maxHeight = wrapper.scrollHeight + "px";
                btn.innerHTML = 'Thu gọn bài viết <i class="bi bi-chevron-up ms-1"></i>';
            }
        });
    }
});

// Animation cho danh sách ảnh - Cũng cần kiểm tra mainImage
const mainImage = document.getElementById('mainImage');
const thumbs = document.querySelectorAll('.thumb-img');

if (mainImage && thumbs.length > 0) {
    let currentIndex = 0;

    window.changeImage = function(element, index) {
        currentIndex = index;
        mainImage.src = element.src;
        thumbs.forEach(img => img.classList.remove('border-danger'));
        element.classList.add('border-danger');
    }

    window.updateSlider = function() {
        const targetThumb = thumbs[currentIndex];
        mainImage.classList.add('changing');
        setTimeout(() => {
            mainImage.src = targetThumb.src;
            thumbs.forEach(img => img.classList.remove('border-danger'));
            targetThumb.classList.add('border-danger');
            targetThumb.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
            mainImage.classList.remove('changing');
        }, 300);
    }

    // Định nghĩa thêm next/prev nếu bạn dùng nút bấm
    window.nextImage = function() {
        currentIndex = (currentIndex + 1) % thumbs.length;
        updateSlider();
    }

    window.prevImage = function() {
        currentIndex = (currentIndex - 1 + thumbs.length) % thumbs.length;
        updateSlider();
    }
}