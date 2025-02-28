<div class="sidebar">
    <ul class="sidebar-menu">
        <li>
            <a href="{{ route('media.index') }}" class="{{ !request('type') && !request('favorite') ? 'active' : '' }}">
                <i class="fas fa-images"></i> Tất cả ảnh và video
            </a>
        </li>
        <li>
            <a href="{{ route('media.index', ['type' => 'image']) }}" class="{{ request('type') === 'image' ? 'active' : '' }}">
                <i class="fas fa-image"></i> Ảnh
            </a>
        </li>
        <li>
            <a href="{{ route('media.index', ['type' => 'video']) }}" class="{{ request('type') === 'video' ? 'active' : '' }}">
                <i class="fas fa-video"></i> Video
            </a>
        </li>
        <li>
            <a href="{{ route('media.index', ['favorite' => true]) }}" class="{{ request('favorite') ? 'active' : '' }}">
                <i class="fas fa-star"></i> Yêu thích
            </a>
        </li>
    </ul>
</div>
