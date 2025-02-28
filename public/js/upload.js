const uploadForm = document.getElementById('uploadForm');
const imageInput = document.getElementById('imageInput');
const errorMessage = document.getElementById('errorMessage');

const ALLOWED_TYPES = ['image/jpeg', 'image/png', 'image/gif'];
const MAX_SIZE = 5 * 1024 * 1024; // 5MB

function showError(message) {
    errorMessage.style.display = 'block';
    errorMessage.textContent = message;
}

function validateFile(file) {
    if (!file) {
        throw new Error('Vui lòng chọn file');
    }
    if (!ALLOWED_TYPES.includes(file.type)) {
        throw new Error('Chỉ chấp nhận file JPG, PNG hoặc GIF');
    }
    if (file.size > MAX_SIZE) {
        throw new Error('File không được lớn hơn 5MB');
    }
}

uploadForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    errorMessage.style.display = 'none';
    
    try {
        const file = imageInput.files[0];
        validateFile(file);

        const formData = new FormData();
        formData.append('image', file);

        const response = await fetch('/upload', {
            method: 'POST',
            body: formData
        });

        if (!response.ok) {
            throw new Error('Lỗi upload: ' + response.statusText);
        }

        alert('Upload thành công!');
        imageInput.value = '';
        
    } catch (error) {
        showError(error.message);
    }
});
