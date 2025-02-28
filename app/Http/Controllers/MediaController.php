<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Media;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MediaController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        try {
            $query = Media::where('user_id', Auth::id());

            // Filter by type (image/video)
            if ($request->has('type')) {
                $type = $request->type;
                if (!in_array($type, ['image', 'video'])) {
                    return back()->with('error', 'Loại media không hợp lệ');
                }
                $query->where('type', $type);
            }

            // Filter favorites
            if ($request->has('favorite')) {
                $query->where('is_favorite', true);
            }

            $media = $query->latest()->paginate(20); // Add pagination

            // Get favorite media for sidebar
            $favoriteMedia = Media::where('user_id', Auth::id())
                ->where('is_favorite', true)
                ->latest()
                ->get();

            return view('media.index', compact('media', 'favoriteMedia'));
        } catch (\Exception $e) {
            Log::error('Error in media index: ' . $e->getMessage());
            return back()->with('error', 'Có lỗi xảy ra khi tải danh sách media');
        }
    }

    public function store(Request $request)
    {
        try {
            Log::info('Starting media upload process');
            
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'file' => [
                    'required',
                    'file',
                    'max:20480',
                ]
            ]);

            Log::info('Validation passed');
            
            if (!$request->hasFile('file')) {
                Log::error('No file uploaded');
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy file upload'
                ], 422);
            }

            $file = $request->file('file');
            
            if (!$file->isValid()) {
                Log::error('Invalid file upload: ' . $file->getErrorMessage());
                return response()->json([
                    'success' => false,
                    'message' => 'File upload không hợp lệ: ' . $file->getErrorMessage()
                ], 422);
            }
            
            Log::info('File is valid: ' . $file->getClientOriginalName());
            
            $extension = strtolower($file->getClientOriginalExtension());
            Log::info('File extension: ' . $extension);
            
            // Xác định loại file dựa trên extension thay vì MIME type
            $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
            $videoExtensions = ['mp4', 'mov', 'avi', 'wmv', 'flv', 'mkv', 'webm'];
            
            if (in_array($extension, $imageExtensions)) {
                $type = 'image';
                Log::info('File type: image');
            } elseif (in_array($extension, $videoExtensions)) {
                $type = 'video';
                Log::info('File type: video');
            } else {
                Log::error('Unsupported file extension: ' . $extension);
                return response()->json([
                    'success' => false,
                    'message' => 'Định dạng file không được hỗ trợ. Chỉ chấp nhận các file ảnh (jpg, jpeg, png, gif) và video (mp4, mov, avi).'
                ], 422);
            }
            
            // Xác định thư mục đích
            $destinationFolder = $type === 'image' ? 'images' : 'videos';
            $publicPath = public_path($destinationFolder);
            Log::info('Destination folder: ' . $publicPath);
            
            // Generate a secure filename
            $filename = time() . '_' . Str::random(10) . '.' . $extension;
            Log::info('Generated filename: ' . $filename);
            
            // Start transaction
            DB::beginTransaction();
            
            try {
                // Đảm bảo thư mục tồn tại và có quyền ghi
                if (!is_dir($publicPath)) {
                    Log::info('Creating directory: ' . $publicPath);
                    if (!mkdir($publicPath, 0777, true)) {
                        Log::error('Failed to create directory: ' . $publicPath . '. Error: ' . error_get_last()['message']);
                        throw new \Exception('Không thể tạo thư mục ' . $publicPath);
                    }
                    // Đảm bảo quyền ghi sau khi tạo
                    chmod($publicPath, 0777);
                    Log::info('Set permissions 0777 on directory: ' . $publicPath);
                } else {
                    // Kiểm tra quyền ghi
                    if (!is_writable($publicPath)) {
                        // Thử cấp quyền ghi
                        chmod($publicPath, 0777);
                        Log::info('Attempted to set permissions 0777 on existing directory: ' . $publicPath);
                        
                        // Kiểm tra lại sau khi cấp quyền
                        if (!is_writable($publicPath)) {
                            Log::error('Directory still not writable after chmod: ' . $publicPath);
                            throw new \Exception('Không có quyền ghi vào thư mục: ' . $publicPath);
                        }
                    }
                }
                
                // Lưu file vào thư mục public
                $fullPath = $publicPath . '/' . $filename;
                Log::info('Attempting to save file to: ' . $fullPath);
                
                // Thử sử dụng move() của Laravel
                if (!$file->move($publicPath, $filename)) {
                    Log::error('Failed to move file to: ' . $fullPath);
                    throw new \Exception('Không thể di chuyển file vào ' . $fullPath);
                }
                
                Log::info('File saved successfully: ' . $fullPath);
                
                // Đường dẫn tương đối để lưu vào database
                $relativePath = $destinationFolder . '/' . $filename;
                Log::info('Relative path for database: ' . $relativePath);

                $fileSize = filesize($fullPath);
                
                $media = new Media([
                    'title' => $request->title,
                    'description' => $request->description,
                    'file_path' => $relativePath,
                    'type' => $type,
                    'file_size' => $fileSize,
                    'mime_type' => $type . '/' . $extension, // Tạo mime type đơn giản
                    'user_id' => Auth::id()
                ]);

                $media->save();
                Log::info('Media record saved to database with ID: ' . $media->id);
                
                DB::commit();
                Log::info('Transaction committed');

                return response()->json([
                    'success' => true,
                    'message' => 'Media đã được tải lên thành công.',
                    'data' => $media
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Exception in upload process: ' . $e->getMessage());
                throw $e;
            }
        } catch (\Exception $e) {
            // Clean up the uploaded file if it exists
            if (isset($filename) && isset($publicPath) && file_exists($publicPath . '/' . $filename)) {
                unlink($publicPath . '/' . $filename);
                Log::info('Cleaned up file after error: ' . $publicPath . '/' . $filename);
            }
            
            Log::error('Error uploading media: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $media = Media::where('user_id', Auth::id())
                ->findOrFail($id);

            // Delete the file from public directory
            $filePath = public_path($media->file_path);
            if (file_exists($filePath)) {
                unlink($filePath);
            }

            // Delete the database record
            $media->delete();

            return response()->json([
                'success' => true,
                'message' => 'Media đã được xóa thành công'
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting media: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xóa media'
            ], 500);
        }
    }

    public function batchDelete(Request $request)
    {
        try {
            $request->validate([
                'ids' => 'required|array',
                'ids.*' => 'required|integer|exists:media,id'
            ]);

            $ids = $request->input('ids', []);
            $successCount = 0;
            $errors = [];
            
            foreach ($ids as $id) {
                try {
                    $media = Media::find($id);
                    
                    if (!$media) {
                        $errors[] = "Không tìm thấy media với ID: " . $id;
                        continue;
                    }

                    if ($media->user_id !== Auth::id()) {
                        $errors[] = "Không có quyền xóa media với ID: " . $id;
                        continue;
                    }

                    // Check if file exists before trying to delete
                    $filePath = public_path($media->file_path);
                    if (file_exists($filePath)) {
                        unlink($filePath);
                    }
                    
                    $media->delete();
                    $successCount++;
                } catch (\Exception $e) {
                    $errors[] = "Lỗi khi xóa media ID {$id}: " . $e->getMessage();
                }
            }

            $message = $successCount > 0 ? "{$successCount} media đã được xóa thành công. " : "";
            if (!empty($errors)) {
                $message .= "Các lỗi xảy ra: " . implode(", ", $errors);
            }

            return response()->json([
                'success' => $successCount > 0,
                'message' => $message
            ]);
        } catch (\Exception $e) {
            Log::error('Error in batch delete: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xóa media: ' . $e->getMessage()
            ], 500);
        }
    }

    public function batchFavorite(Request $request)
    {
        try {
            $request->validate([
                'ids' => 'required|array',
                'ids.*' => 'required|integer|exists:media,id'
            ]);

            $ids = $request->input('ids', []);
            $media = Media::whereIn('id', $ids)
                ->where('user_id', Auth::id())
                ->get();

            foreach ($media as $item) {
                $item->is_favorite = !$item->is_favorite;
                $item->save();
            }

            return response()->json([
                'success' => true,
                'message' => 'Đã cập nhật trạng thái yêu thích',
                'items' => $media
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi cập nhật trạng thái yêu thích'
            ], 500);
        }
    }

    public function toggleFavorite($id)
    {
        try {
            $media = Media::where('id', $id)
                ->where('user_id', Auth::id())
                ->firstOrFail();

            $media->is_favorite = !$media->is_favorite;
            $media->save();

            return response()->json([
                'success' => true,
                'message' => $media->is_favorite ? 'Đã thêm vào mục yêu thích' : 'Đã xóa khỏi mục yêu thích',
                'is_favorite' => $media->is_favorite
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi cập nhật trạng thái yêu thích'
            ], 500);
        }
    }
}
