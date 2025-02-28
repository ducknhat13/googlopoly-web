@extends('layouts.app')

@section('content')
<div class="media-container">
    <!-- Action Buttons -->
    <div class="action-buttons" id="actionButtons">
        <button class="action-button favorite-button" onclick="toggleFavoriteChecked()">
            <i class="fa-solid fa-star"></i>
        </button>
        <button class="action-button delete-button" onclick="deleteChecked()">
            <i class="fa-solid fa-trash"></i>
        </button>
    </div>

    <div class="div-bottom">
        <div class="left-side">
            @include('media.sidebar')
        </div>
        <div class="right-side">
            @forelse($media as $item)
                <div class="media-item {{ $item->is_favorite ? 'is-favorite' : '' }}" data-id="{{ $item->id }}">
                    <div class="media-checkbox" onclick="toggleMediaCheck(event, '{{ $item->id }}')">
                        <i class="fa-solid fa-check"></i>
                    </div>
                    <div class="media-image" 
                         data-url="{{ asset($item->file_path) }}"
                         data-type="{{ $item->type }}"
                         data-id="{{ $item->id }}"
                         onclick="openPopupFromData(this)">
                        @php
                            $itemType = $item->type;
                        @endphp
                        @if($itemType == 'video')
                            <video src="{{ asset($item->file_path) }}" class="media-content" onerror="handleMediaError(this, 'Video')"></video>
                            <div class="video-overlay">
                                <i class="fa-solid fa-play"></i>
                            </div>
                        @else
                            <img src="{{ asset($item->file_path) }}" alt="{{ $item->title }}" class="media-content" loading="lazy" onerror="handleMediaError(this, 'Ảnh')">                            
                        @endif
                        <div class="media-info">
                            <h3>{{ $item->title }}</h3>
                            @if($item->description)
                                <p>{{ $item->description }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="no-media-wrapper">
                    <div class="no-media">
                        <i class="fas fa-images"></i>
                        <p>Chưa có media nào. Hãy tải lên media đầu tiên của bạn!</p>
                    </div>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Popup -->
    <div id="popup" class="media-popup" style="display: none;">
        <div class="popup-content">
            <div class="popup-media"></div>
        </div>
        <div class="popup-actions">
            <button class="action-button delete-button" onclick="deleteMedia()">
                <i class="fa-solid fa-trash"></i>
            </button>
            <button class="action-button favorite-button" onclick="toggleFavorite()">
                <i class="fa-solid fa-star"></i>
            </button>
            <button class="action-button close-button" onclick="closePopup()">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
// Khởi tạo Set để lưu trữ các item đã được chọn
window.checkedItems = new Set();

// Đảm bảo action buttons được ẩn đi mặc định khi trang được tải
document.addEventListener('DOMContentLoaded', function() {
    // Đảm bảo action buttons bị ẩn mặc định
    const actionButtons = document.getElementById('actionButtons');
    if (actionButtons) {
        actionButtons.classList.remove('visible');
        console.log('Action buttons hidden on page load');
    }
});

function toggleMediaCheck(event, id) {
    event.stopPropagation();
    const item = document.querySelector(`.media-item[data-id="${id}"]`);
    
    if (window.checkedItems.has(id)) {
        window.checkedItems.delete(id);
        item.classList.remove('checked');
    } else {
        window.checkedItems.add(id);
        item.classList.add('checked');
    }
    
    // Hiển thị hoặc ẩn action buttons dựa trên số lượng item được chọn
    const actionButtons = document.getElementById('actionButtons');
    if (window.checkedItems.size > 0) {
        actionButtons.classList.add('visible');
        console.log('Action buttons should be visible now, checked items:', window.checkedItems.size);
    } else {
        actionButtons.classList.remove('visible');
        console.log('Action buttons should be hidden now, checked items:', window.checkedItems.size);
    }
}

function updateFavoritesSidebar(items) {
    const favoriteItems = document.querySelector('.favorite-items');
    
    // Cập nhật trạng thái yêu thích cho các mục
    items.forEach(item => {
        const mediaItem = document.querySelector(`.media-item[data-id="${item.id}"]`);
        if (mediaItem) {
            if (item.is_favorite) {
                mediaItem.classList.add('is-favorite');
                // Thêm hiệu ứng nhấp nháy cho mục vừa được thêm vào yêu thích
                mediaItem.classList.add('favorite-added');
                setTimeout(() => {
                    mediaItem.classList.remove('favorite-added');
                }, 1000);
            } else {
                mediaItem.classList.remove('is-favorite');
            }
        }
    });
    
    // Hiển thị thông báo thành công
    showNotification('Đã cập nhật trạng thái yêu thích');
}

function deleteChecked() {
    if (window.checkedItems.size === 0) return;
    
    if (!confirm('Bạn có chắc chắn muốn xóa các mục đã chọn?')) return;

    fetch('/media/batch-delete', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            ids: Array.from(window.checkedItems)
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Xóa các mục đã chọn khỏi DOM
            window.checkedItems.forEach(id => {
                const item = document.querySelector(`.media-item[data-id="${id}"]`);
                if (item) {
                    // Thêm hiệu ứng fade-out trước khi xóa
                    item.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                    item.style.opacity = '0';
                    item.style.transform = 'scale(0.8)';
                    
                    // Xóa phần tử sau khi hiệu ứng hoàn thành
                    setTimeout(() => {
                        item.remove();
                    }, 300);
                }
            });
            
            // Xóa các mục đã chọn và ẩn action buttons
            window.checkedItems.clear();
            document.getElementById('actionButtons').classList.remove('visible');
            
            // Hiển thị thông báo thành công
            showNotification(`Đã xóa ${data.count} mục thành công`);
        } else {
            // Hiển thị thông báo lỗi
            showNotification(data.message || 'Có lỗi xảy ra khi xóa các mục', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Có lỗi xảy ra khi xóa các mục', 'error');
    });
}

function toggleFavoriteChecked() {
    if (window.checkedItems.size === 0) return;
    
    fetch('/media/batch-favorite', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            ids: Array.from(window.checkedItems)
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateFavoritesSidebar(data.items);
            
            // Clear checked items and hide action buttons
            window.checkedItems.forEach(id => {
                const item = document.querySelector(`.media-item[data-id="${id}"]`);
                if (item) {
                    item.classList.remove('checked');
                }
            });
            window.checkedItems.clear();
            document.getElementById('actionButtons').classList.remove('visible');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Có lỗi xảy ra khi cập nhật trạng thái yêu thích', 'error');
    });
}

function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = 'notification-message ' + type;
    notification.textContent = message;
    notification.style.position = 'fixed';
    notification.style.bottom = '20px';
    notification.style.left = '50%';
    notification.style.transform = 'translateX(-50%)';
    notification.style.backgroundColor = type === 'success' ? '#4CAF50' : '#F44336';
    notification.style.color = 'white';
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

let currentZoom = 1;
const MAX_ZOOM = 3;
const MIN_ZOOM = 0.5;
let isDragging = false;
let startX, startY, scrollLeft, scrollTop;

function openPopupFromData(element) {
    const url = element.getAttribute('data-url');
    const type = element.getAttribute('data-type');
    const id = element.getAttribute('data-id');
    openPopup(url, type, id);
}

function openPopup(url, type, id) {
    const popup = document.getElementById('popup');
    const content = popup.querySelector('.popup-media');
    window.currentMediaId = id;
    currentZoom = 1;

    content.innerHTML = '';
    if (type === 'video') {
        const video = document.createElement('video');
        video.src = url;
        video.controls = true;
        content.appendChild(video);
    } else {
        const img = document.createElement('img');
        img.src = url;
        content.appendChild(img);
        
        // Add drag functionality for zoomed images
        img.addEventListener('mousedown', startDragging);
        img.addEventListener('mousemove', drag);
        img.addEventListener('mouseup', stopDragging);
        img.addEventListener('mouseleave', stopDragging);
    }

    popup.style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function startDragging(e) {
    if (currentZoom <= 1) return;
    
    const content = document.querySelector('.popup-media');
    isDragging = true;
    startX = e.pageX - content.offsetLeft;
    startY = e.pageY - content.offsetTop;
    scrollLeft = content.scrollLeft;
    scrollTop = content.scrollTop;
}

function drag(e) {
    if (!isDragging) return;
    
    e.preventDefault();
    const content = document.querySelector('.popup-media');
    const x = e.pageX - content.offsetLeft;
    const y = e.pageY - content.offsetTop;
    const walkX = (x - startX);
    const walkY = (y - startY);
    content.scrollLeft = scrollLeft - walkX;
    content.scrollTop = scrollTop - walkY;
}

function stopDragging() {
    isDragging = false;
}

function toggleFavorite() {
    if (!window.currentMediaId) return;
    
    fetch(`/media/${window.currentMediaId}/toggle-favorite`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const item = document.querySelector(`.media-item[data-id="${window.currentMediaId}"]`);
            if (item) {
                if (data.is_favorite) {
                    item.classList.add('is-favorite');
                } else {
                    item.classList.remove('is-favorite');
                }
            }
            // Reload the page to update sidebar
            window.location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Có lỗi xảy ra khi cập nhật trạng thái yêu thích');
    });
}

function deleteMedia() {
    if (!window.currentMediaId) return;
    
    if (!confirm('Bạn có chắc chắn muốn xóa media này?')) return;

    fetch(`/media/${window.currentMediaId}/delete`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const item = document.querySelector(`.media-item[data-id="${window.currentMediaId}"]`);
            if (item) {
                item.remove();
            }
            closePopup();
            alert(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Có lỗi xảy ra khi xóa media');
    });
}

function closePopup() {
    const popup = document.getElementById('popup');
    const content = popup.querySelector('.popup-media');
    const img = content.querySelector('img');
    
    if (img) {
        img.style.transform = '';
        currentZoom = 1;
    }
    
    popup.style.display = 'none';
    document.body.style.overflow = '';
    window.currentMediaId = null;
}

// Close popup when clicking outside
document.addEventListener('click', function(event) {
    const popup = document.getElementById('popup');
    if (event.target === popup) {
        closePopup();
    }
});

// Close popup with Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape' && document.getElementById('popup').classList.contains('active')) {
        closePopup();
    }
});

function handleMediaError(element, type) {
    element.style.display = 'none';
    element.parentElement.innerHTML += `<div class="error-message">${type} không khả dụng</div>`;
}
</script>
@endpush

@push('styles')
<style>
.media-container {
    padding: 20px;
    min-height: calc(100vh - 100px);
}

.div-bottom {
    display: flex;
    gap: 20px;
    margin-top: 20px;
}

.left-side {
    width: 250px;
    flex-shrink: 0;
}

.right-side {
    flex: 1;
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 20px;
    justify-items: center;
}

.no-media-wrapper {
    grid-column: 1 / -1;
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 400px;
    width: 100%;
}

.no-media {
    text-align: center;
    padding: 40px;
    color: #666;
    background: #f8f9fa;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    max-width: 400px;
    width: 100%;
}

.no-media i {
    font-size: 64px;
    margin-bottom: 20px;
    color: #ddd;
}

.no-media p {
    font-size: 16px;
    margin-bottom: 20px;
    line-height: 1.5;
}

.no-media .btn {
    padding: 10px 20px;
    font-size: 16px;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.no-media .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.no-media .btn i {
    font-size: 16px;
    margin-right: 8px;
    color: white;
    margin-bottom: 0;
}

/* Upload Modal */
.modal-content {
    border-radius: 12px;
}

.modal-header {
    border-bottom: 1px solid #eee;
    padding: 1rem 1.5rem;
}

.modal-title {
    font-size: 1.25rem;
    font-weight: 600;
}


.modal-footer {
    border-top: 1px solid #eee;
    padding: 1rem 1.5rem;
}

.form-label {
    font-weight: 500;
    margin-bottom: 0.5rem;
}

.form-text {
    color: #666;
    margin-top: 0.5rem;
}

.btn-close:focus {
    box-shadow: none;
}

.popup-actions {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    margin-top: 10px;
}

.popup-actions .action-button {
    padding: 10px 15px;
    border: none;
    border-radius: 8px;
    background-color: #f8f9fa;
    cursor: pointer;
}

.popup-actions .action-button:hover {
    background-color: #f0f0f0;
}

.popup-actions .action-button i {
    font-size: 18px;
    margin-right: 5px;
}

/* Đảm bảo action buttons bị ẩn mặc định */
.action-buttons {
    display: none !important;
}

/* Chỉ hiển thị khi có class visible */
.action-buttons.visible {
    display: flex !important;
}
</style>
@endpush
