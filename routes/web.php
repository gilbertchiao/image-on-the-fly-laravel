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
    $width = ((int) request()->route('width')) ?: 200;
    $height = ((int) request()->route('height')) ?: 200;
    $renew = request()->query('renew');

    $filepath = sprintf('images/%s/%s/%s', $width, $height, $filename);

    // 是否已有指定尺寸的圖檔
    if (!$renew) {
        if (Storage::disk('public')->exists($filepath)) {
            Log::debug(sprintf('%s 已存在，直接使用', $filepath));
            return response()->file(Storage::disk('public')->path($filepath));
        }
    }

    // 檢查原圖是否存在
    if (!Storage::disk('images')->exists($filename)) {
        abort(404);
    }

    // 產生指定尺寸圖檔
    $image = Image::make(Storage::disk('images')->get($filename));
    $image->resize($width, $height, function ($constraint) {
        $constraint->aspectRatio();
        $constraint->upsize();
    });

    // 儲存圖檔以便再次使用
    Log::debug(sprintf('儲存圖檔以便再次使用 %s', $filepath));
    Storage::disk('public')->put($filepath, $image->encode('jpg'));

    return $image->response('jpg');
});

Route::any('*', function () {
    abort(404);
});
