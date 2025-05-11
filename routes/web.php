<?php

use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Route;

Route::post('/clear-cache', function () {
    \Illuminate\Support\Facades\Cache::flush();
    Notification::make()
        ->title('Xoá cache thành công')
        ->body('Toàn bộ cache đã được xóa thành công')
        ->success()
        ->send();
    return redirect()->back();
})->name('admin.clear-cache');
