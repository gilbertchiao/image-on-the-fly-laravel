<?php

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return response()->noContent();
});

Route::get('/images/{width}/{height}/{image}', function () {
    $filename = request()->route('image');
    $width = ((int) request()->route('width')) ?: config('image-on-the-fly.default_width');
    $height = ((int) request()->route('height')) ?: config('image-on-the-fly.default_height');
    $renew = request()->query('renew');

    $source_image_disk = config('image-on-the-fly.source_disk');
    $source_image_folder = config('image-on-the-fly.source_folder');
    $cache_disk = config('image-on-the-fly.cache_disk');
    $cache_folder = config('image-on-the-fly.cache_folder');

    Log::debug($source_image_disk);
    Log::debug($source_image_folder);

    $source_file = $filename;
    if ($source_image_folder) {
        $source_file = $source_image_folder . '/' . $source_file;
    }

    $cache_file = sprintf('%s/%s/%s', $width, $height, $filename);
    if ($cache_folder) {
        $cache_file = $cache_folder . '/' . $cache_file;
    }

    // 是否已有指定尺寸的圖檔
    if (!config('image-on-the-fly.no_cache')) {
        if (!config('image-on-the-fly.allow_renew')) {
            if (Storage::disk($cache_disk)->exists($cache_file)) {
                Log::debug(sprintf('%s 已存在，直接使用', $cache_file));
                return response()->file(Storage::disk($cache_disk)->path($cache_file));
            }
        } else {
            if (!$renew) {
                if (Storage::disk($cache_disk)->exists($cache_file)) {
                    Log::debug(sprintf('%s 已存在，直接使用', $cache_file));
                    return response()->file(Storage::disk($cache_disk)->path($cache_file));
                }
            }
        }
    }

    // 檢查原圖是否存在
    if (!Storage::disk($source_image_disk)->exists($source_file)) {
        Log::debug(sprintf('原圖 %s 不存在', $source_file));
        abort(404);
    }

    // 產生指定尺寸圖檔
    $image = Image::make(Storage::disk($source_image_disk)->get($source_file));
    $image->resize($width, $height, function ($constraint) {
        $constraint->aspectRatio();
        $constraint->upsize();
    });

    // 儲存圖檔以便再次使用
    if (!config('image-on-the-fly.no_cache')) {
        Log::debug(sprintf('儲存圖檔以便再次使用 %s', $cache_file));
        Storage::disk($cache_disk)->put($cache_file, $image->encode('jpg'));
    }

    return $image->response('jpg');
});

Route::any('*', function () {
    abort(404);
});
