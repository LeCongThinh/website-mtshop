// admin/assets/js/handle_album_images.js

document.addEventListener('DOMContentLoaded', function() {
    let selectedFiles = [];
    const galleryInput = document.getElementById('gallery-input');
    const previewContainer = document.getElementById('gallery-preview');

    if (!galleryInput || !previewContainer) return;

    galleryInput.onchange = function() {
        const newFiles = Array.from(this.files);
        selectedFiles = selectedFiles.concat(newFiles);
        renderPreview();
        updateInputFiles();
    };

    function renderPreview() {
        // Nếu không có ảnh nào thì ẩn container preview đi
        if (selectedFiles.length === 0) {
            previewContainer.classList.add('d-none');
            previewContainer.innerHTML = '';
            return;
        }

        previewContainer.classList.remove('d-none');
        previewContainer.innerHTML = ''; 

        selectedFiles.forEach((file, index) => {
            const col = document.createElement('div');
            col.className = 'col-4 position-relative mb-2';
            
            const imageUrl = URL.createObjectURL(file);
            
            col.innerHTML = `
                <div class="p-1 border rounded shadow-sm bg-white">
                    <img src="${imageUrl}" class="img-thumbnail" style="height: 100px; width: 100%; object-fit: cover; border: none;">
                    <button type="button" 
                        class="btn btn-danger btn-sm position-absolute" 
                        style="top: -5px; right: 5px; border-radius: 50%; width: 22px; height: 22px; padding: 0; display: flex; align-items: center; justify-content: center; z-index: 10;"
                        onclick="window.removeGalleryFile(${index})">
                        <i class="fas fa-times" style="font-size: 10px;"></i>
                    </button>
                </div>
            `;
            previewContainer.appendChild(col);
        });
    }

    window.removeGalleryFile = function(index) {
        selectedFiles.splice(index, 1);
        renderPreview();
        updateInputFiles();
    };

    function updateInputFiles() {
        const dataTransfer = new DataTransfer();
        selectedFiles.forEach(file => {
            dataTransfer.items.add(file);
        });
        galleryInput.files = dataTransfer.files;
    }
});