<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class UploadMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $contentLength = $request->server('CONTENT_LENGTH');
        $maxSize = str_replace('M', '', ini_get('post_max_size')) * 1024 * 1024;
        
        if ($contentLength > $maxSize) {
            return response()->json([
                'success' => false,
                'message' => 'File quá lớn. Kích thước tối đa cho phép là ' . ini_get('post_max_size')
            ], 413);
        }

        return $next($request);
    }
} 