<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\AlbumController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Http\Request;

// Route mặc định sẽ hiển thị trang đăng nhập/đăng ký
Route::get('/', function () {
    return view('media.giaodien');
})->name('welcome');

// Các route cần xác thực
Route::middleware(['auth'])->group(function () {
    // Sử dụng resource cho media và albums
    Route::resource('media', MediaController::class)->except(['create', 'edit', 'show']);
    Route::resource('albums', AlbumController::class);

    // Các route tùy chỉnh cho media
    Route::get('/media/{id}/info', [MediaController::class, 'getInfo'])->name('media.getInfo');
    Route::post('/media/batch-delete', [MediaController::class, 'batchDelete'])->name('media.batchDelete');
    Route::post('/media/batch-favorite', [MediaController::class, 'batchFavorite'])->name('media.batchFavorite');
    Route::post('/media/{id}/toggle-favorite', [MediaController::class, 'toggleFavorite'])->name('media.toggleFavorite');
    
    // Kiểm tra title
    Route::get('/check-title', function (Request $request) {
        $title = $request->query('title');
        $exists = App\Models\Media::where('title', $title)
            ->where('user_id', auth()->id())
            ->exists();
        return response()->json(['exists' => $exists]);
    })->name('media.checkTitle');
});

// Route cho đăng nhập và đăng ký
Route::post('/login', [LoginController::class, 'login'])->name('login');
Route::post('/register', [RegisterController::class, 'register'])->name('register');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
