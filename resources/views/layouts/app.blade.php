<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fira+Code:wght@300..700&family=Quicksand:wght@300..700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/a799082c9e.js" crossorigin="anonymous"></script>

    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}"> <!-- Main CSS -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}"> <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/dark-mode.css') }}"> <!-- Dark Mode CSS -->
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    @stack('styles')
</head>
<body>
    <div id="uploadNotification" class="upload-notification">
        <i class="fas fa-check-circle success-icon"></i>
        <i class="fas fa-times-circle error-icon" style="display: none;"></i>
        <span class="notification-message">Tải lên thành công!</span>
    </div>
    <div id="app">
        <div class="div-top">
            <div class="logo">
                <span style="color: #4285F4;">G</span><span style="color: #EA4335;">o</span><span style="color: #FBBC05;">o</span><span style="color: #4285F4;">g</span><span style="color: #34A853;">l</span><span style="color: #FBBC05;">o</span>&nbsp;<span style="color: #000;" class="theme-text">poly</span>&nbsp;<span style="color: #000;" class="theme-text photos">Photos</span>
            </div>
            <div class="search-bar">
                <input type="text" placeholder="Tìm kiếm ảnh và album của bạn (Chức năng đang phát triển)">
            </div>
            <div class="icons">
                <i class="fa-solid fa-plus" onclick="showUploadModal()"></i>
                <div class="settings-dropdown">
                    <i class="fa-solid fa-gear" onclick="toggleSettingsDropdown()"></i>
                    <div class="settings-dropdown-content" id="settingsDropdown">
                        <div class="dropdown-item" onclick="toggleDarkMode()">
                            <i class="fa-solid fa-moon"></i>
                            <span>Giao diện tối</span>
                            <div class="theme-toggle">
                                <div class="toggle-slider" id="themeToggle"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="user-dropdown">
                    <i class="fa-solid fa-user" onclick="toggleUserDropdown()"></i>
                    <div class="user-dropdown-content" id="userDropdown">
                        <div class="user-info">
                            <div class="user-name">{{ Auth::user()->name }}</div>
                            <div class="user-email">{{ Auth::user()->email }}</div>
                        </div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <a href="#" class="dropdown-item logout" onclick="event.preventDefault(); this.closest('form').submit();">
                                <i class="fa-solid fa-sign-out-alt"></i> Đăng xuất
                            </a>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="upload-modal" id="uploadModal">
            <div class="upload-modal-content">
                <div class="upload-modal-header">
                    <h2>Tải lên ảnh hoặc video</h2>
                    <i class="fa-solid fa-xmark" onclick="closeUploadModal()"></i>
                </div>
                <div class="upload-modal-body">
                    <form action="{{ route('media.store') }}" method="POST" enctype="multipart/form-data" id="uploadForm" onsubmit="return handleUpload(event)">
                        @csrf
                        <div class="upload-message" id="uploadMessage"></div>
                        <div class="upload-area" id="uploadArea">
                            <input type="file" name="file" id="fileInput" accept="image/*,video/*" hidden>
                            <div class="upload-placeholder" id="uploadPlaceholder">
                                <i class="fa-solid fa-cloud-arrow-up"></i>
                                <p>Kéo thả file vào đây hoặc</p>
                                <button type="button" class="select-file-btn" onclick="document.getElementById('fileInput').click()">
                                    Chọn file
                                </button>
                                <p class="file-support">Hỗ trợ: JPG, PNG, GIF, MP4</p>
                            </div>
                            <div class="file-preview" id="filePreview">
                                <img id="imagePreview" src="" alt="Preview">
                                <video id="videoPreview" controls>
                                    <source src="" type="video/mp4">
                                </video>
                            </div>
                        </div>
                        <div class="upload-form">
                            <div class="form-group">
                                <label>Tiêu đề</label>
                                <input type="text" name="title" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Mô tả</label>
                                <textarea name="description" class="form-control"></textarea>
                            </div>
                        </div>
                        <div class="upload-actions">
                            <button type="button" class="btn-cancel" onclick="closeUploadModal()">Hủy</button>
                            <button type="submit" class="btn-upload">Tải lên</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <main>
            @yield('content')
        </main>
    </div>

    <div id="popup" class="popup">
        <div class="popup-content"></div>
        <div class="popup-actions">
            <button class="action-button favorite-button" onclick="toggleFavorite()" title="Add to favorites">
                <i class="fas fa-star"></i>
            </button>
            <button class="action-button delete-button" onclick="deleteMedia()" title="Delete">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    </div>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        function showUploadNotification(type, message) {
            const notification = document.getElementById('uploadNotification');
            const successIcon = notification.querySelector('.success-icon');
            const errorIcon = notification.querySelector('.error-icon');
            const messageSpan = notification.querySelector('.notification-message');

            // Reset classes
            notification.classList.remove('success', 'error');
            successIcon.style.display = 'none';
            errorIcon.style.display = 'none';

            // Set appropriate class and icon
            if (type === 'success') {
                notification.classList.add('success');
                successIcon.style.display = 'inline-block';
            } else {
                notification.classList.add('error');
                errorIcon.style.display = 'inline-block';
            }

            // Set message
            messageSpan.textContent = message;

            // Show notification
            notification.classList.add('show');
            setTimeout(() => {
                notification.classList.remove('show');
            }, 3000);
        }

        function showUploadModal() {
            const modal = document.getElementById('uploadModal');
            modal.classList.add('show');
        }

        function closeUploadModal() {
            const modal = document.getElementById('uploadModal');
            modal.classList.remove('show');
            resetUploadForm();
        }

        function resetUploadForm() {
            document.getElementById('uploadForm').reset();
            document.getElementById('uploadArea').classList.remove('has-file');
            document.getElementById('imagePreview').src = '';
            document.getElementById('videoPreview').src = '';
            document.getElementById('uploadMessage').className = 'upload-message';
            document.getElementById('uploadMessage').textContent = '';
        }

        // Xử lý preview file
        document.getElementById('fileInput').addEventListener('change', function(e) {
            handleFileSelect(e.target.files[0]);
        });

        // Xử lý drag and drop
        const uploadArea = document.getElementById('uploadArea');

        uploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadArea.classList.add('dragover');
        });

        uploadArea.addEventListener('dragleave', () => {
            uploadArea.classList.remove('dragover');
        });

        uploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadArea.classList.remove('dragover');
            const file = e.dataTransfer.files[0];
            if (file) {
                const fileInput = document.getElementById('fileInput');
                fileInput.files = e.dataTransfer.files;
                handleFileSelect(file);
            }
        });

        function handleFileSelect(file) {
            if (file) {
                const reader = new FileReader();
                const imagePreview = document.getElementById('imagePreview');
                const videoPreview = document.getElementById('videoPreview');
                const uploadArea = document.getElementById('uploadArea');

                reader.onload = function(e) {
                    if (file.type.startsWith('image/')) {
                        imagePreview.style.display = 'block';
                        videoPreview.style.display = 'none';
                        imagePreview.src = e.target.result;
                    } else if (file.type.startsWith('video/')) {
                        imagePreview.style.display = 'none';
                        videoPreview.style.display = 'block';
                        videoPreview.src = URL.createObjectURL(file);
                    }
                    uploadArea.classList.add('has-file');
                }
                reader.readAsDataURL(file);
            }
        }

        function toggleSettingsDropdown() {
            document.getElementById('settingsDropdown').classList.toggle('show');
        }
        
        function toggleUserDropdown() {
            document.getElementById('userDropdown').classList.toggle('show');
        }

        // Theme handling
        const htmlElement = document.documentElement;
        const themeToggle = document.getElementById('themeToggle');
        
        // Check saved theme preference
        const savedTheme = localStorage.getItem('theme');
        if (savedTheme) {
            htmlElement.setAttribute('data-theme', savedTheme);
            updateThemeToggle(savedTheme === 'dark');
        }
        
        function toggleDarkMode() {
            const currentTheme = htmlElement.getAttribute('data-theme');
            const newTheme = currentTheme === 'light' ? 'dark' : 'light';
            
            htmlElement.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            updateThemeToggle(newTheme === 'dark');
            
            // Hiển thị thông báo
            showNotification(newTheme === 'dark' ? 'Đã chuyển sang giao diện tối' : 'Đã chuyển sang giao diện sáng');
        }

        function updateThemeToggle(isDark) {
            const toggle = document.getElementById('themeToggle');
            if (isDark) {
                toggle.classList.add('active');
            } else {
                toggle.classList.remove('active');
            }
        }

        function showNotification(message, type = 'success') {
            const notification = document.createElement('div');
            notification.className = 'notification-message ' + type;
            notification.textContent = message;
            notification.style.position = 'fixed';
            notification.style.bottom = '20px';
            notification.style.left = '50%';
            notification.style.transform = 'translateX(-50%)';
            notification.style.backgroundColor = type === 'success' ? 'var(--notification-background)' : 'var(--error-color)';
            notification.style.color = 'var(--notification-text)';
            notification.style.padding = '10px 20px';
            notification.style.borderRadius = '5px';
            notification.style.zIndex = '9999';
            notification.style.opacity = '0';
            notification.style.transition = 'opacity 0.3s ease';
            
            document.body.appendChild(notification);
            
            // Hiển thị và ẩn thông báo sau 2 giây
            setTimeout(() => {
                notification.style.opacity = '1';
                setTimeout(() => {
                    notification.style.opacity = '0';
                    setTimeout(() => {
                        document.body.removeChild(notification);
                    }, 300);
                }, 2000);
            }, 100);
        }

        // Close dropdowns when clicking outside
        window.addEventListener('click', function(event) {
            // Close user dropdown when clicking outside
            if (!event.target.matches('.fa-user')) {
                var userDropdowns = document.getElementsByClassName("user-dropdown-content");
                for (var i = 0; i < userDropdowns.length; i++) {
                    var openDropdown = userDropdowns[i];
                    if (openDropdown.classList.contains('show')) {
                        openDropdown.classList.remove('show');
                    }
                }
            }
            
            // Close settings dropdown when clicking outside
            if (!event.target.matches('.fa-gear')) {
                var settingsDropdowns = document.getElementsByClassName("settings-dropdown-content");
                for (var i = 0; i < settingsDropdowns.length; i++) {
                    var openDropdown = settingsDropdowns[i];
                    if (openDropdown.classList.contains('show')) {
                        openDropdown.classList.remove('show');
                    }
                }
            }
        });

        // Thêm hàm kiểm tra tên trùng
        async function checkTitleExists(title) {
            try {
                const response = await fetch(`/check-title?title=${encodeURIComponent(title)}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin' // Gửi kèm cookie CSRF
                });

                if (!response.ok) {
                    if (response.status === 401) {
                        // Chưa đăng nhập
                        window.location.href = '/login';
                        return false;
                    }
                    throw new Error('Network response was not ok');
                }

                const data = await response.json();
                return data.exists;
            } catch (error) {
                console.error('Lỗi kiểm tra tên:', error);
                return false;
            }
        }

        // Thêm sự kiện kiểm tra tên khi nhập
        document.querySelector('input[name="title"]').addEventListener('input', async function(e) {
            const title = e.target.value.trim();
            if (title) {
                const exists = await checkTitleExists(title);
                if (exists) {
                    showMessage('Tên đã được đặt! Đổi tên khác', 'error');
                    e.target.classList.add('error');
                } else {
                    const messageDiv = document.getElementById('uploadMessage');
                    if (messageDiv.textContent === 'Tên đã được đặt! Đổi tên khác') {
                        messageDiv.className = 'upload-message';
                        messageDiv.textContent = '';
                    }
                    e.target.classList.remove('error');
                }
            }
        });

        // Cập nhật hàm handleUpload
        async function handleUpload(event) {
            event.preventDefault();
            
            try {
                const form = event.target;
                const formData = new FormData(form);
                
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin',
                    body: formData
                });

                const result = await response.json();

                if (response.ok) {
                    closeUploadModal();
                    showUploadNotification('success', 'Tải lên thành công!');
                    location.reload();
                } else {
                    showUploadNotification('error', result.message || 'Có lỗi xảy ra khi tải lên');
                }
            } catch (error) {
                showUploadNotification('error', 'Có lỗi xảy ra khi tải lên');
            }
            
            return false;
        }

        // Hàm hiển thị thông báo
        function showMessage(message, type) {
            const messageDiv = document.getElementById('uploadMessage');
            messageDiv.textContent = message;
            messageDiv.className = 'upload-message ' + type;
        }

        let currentMediaId = null;
        // Sử dụng window.checkedItems thay vì biến cục bộ checkedItems
        if (!window.checkedItems) {
            window.checkedItems = new Set();
        }

        let currentMediaIndex = 0;
        let mediaItems = [];

        function openPopup(src, type, mediaId) {
            const popup = document.getElementById('popup');
            const content = popup.querySelector('.popup-content');
            currentMediaId = mediaId;
            
            // Get all media items and find current index
            mediaItems = Array.from(document.querySelectorAll('.media-item'));
            currentMediaIndex = mediaItems.findIndex(item => item.getAttribute('data-id') === mediaId.toString());
            
            content.innerHTML = type === 'image' 
                ? `<img src="${src}" alt="Media">`
                : `<video controls><source src="${src}" type="video/mp4"></video>`;
            
            popup.classList.add('show');
        }

        function closePopup() {
            const popup = document.getElementById('popup');
            popup.classList.remove('show');
            currentMediaId = null;
            mediaItems = [];
            currentMediaIndex = 0;
        }

        function toggleCheck(event, mediaId) {
            if (event) {
                event.preventDefault();
                event.stopPropagation();
            }
            
            const mediaItem = document.querySelector(`.media-item[data-id="${mediaId}"]`);
            
            if (window.checkedItems.has(mediaId)) {
                window.checkedItems.delete(mediaId);
                mediaItem.classList.remove('checked');
            } else {
                window.checkedItems.add(mediaId);
                mediaItem.classList.add('checked');
            }
            
            updateActionButtons();
        }

        function updateActionButtons() {
            const actionButtons = document.getElementById('actionButtons');
            if (!actionButtons) return;
            
            if (window.checkedItems.size > 0) {
                actionButtons.classList.add('visible');
                console.log('Action buttons visible from app.blade.php, items:', window.checkedItems.size);
            } else {
                actionButtons.classList.remove('visible');
                console.log('Action buttons hidden from app.blade.php, items:', window.checkedItems.size);
            }
        }

        function deleteCheckedMedia() {
            if (!window.checkedItems.size || !confirm(`Bạn có chắc chắn muốn xóa ${window.checkedItems.size} mục đã chọn?`)) {
                return;
            }

            const deletePromises = Array.from(window.checkedItems).map(mediaId => 
                fetch(`/media/${mediaId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                    }
                })
                .then(response => response.json())
            );

            Promise.all(deletePromises)
                .then(results => {
                    window.checkedItems.forEach(mediaId => {
                        const mediaItem = document.querySelector(`.media-item[data-id="${mediaId}"]`);
                        if (mediaItem) {
                            mediaItem.remove();
                        }
                    });
                    
                    window.checkedItems.clear();
                    updateActionButtons();
                    showMessage(`Đã xóa ${results.length} mục thành công`, 'success');
                })
                .catch(error => {
                    console.error('Error deleting media:', error);
                    showMessage('Có lỗi xảy ra khi xóa media', 'error');
                });
        }

        function deleteMedia() {
            if (!currentMediaId || !confirm('Bạn có chắc chắn muốn xóa media này?')) {
                return;
            }
            
            fetch(`/media/${currentMediaId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const mediaItem = document.querySelector(`.media-item[data-id="${currentMediaId}"]`);
                    if (mediaItem) {
                        mediaItem.remove();
                    }
                    closePopup();
                    alert('Đã xóa thành công!');
                } else {
                    alert(data.message || 'Có lỗi xảy ra khi xóa media.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Có lỗi xảy ra khi xóa media. Vui lòng thử lại.');
            });
        }

        // Close dropdown when clicking outside
        window.addEventListener('click', function(event) {
            if (!event.target.matches('.fa-user')) {
                var dropdowns = document.getElementsByClassName("user-dropdown-content");
                for (var i = 0; i < dropdowns.length; i++) {
                    var openDropdown = dropdowns[i];
                    if (openDropdown.classList.contains('show')) {
                        openDropdown.classList.remove('show');
                    }
                }
            }
        });
    </script>
    @stack('scripts')
</body>
</html>