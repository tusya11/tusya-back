<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait SaveFileTrait {
    public static function saveFile($file) {
        $file_data = explode(',', $file);
        $format = '.' . explode(';', explode('/', $file_data[0])[1])[0];
        $file = base64_decode($file_data[1]);
        $file_name = Str::uuid();
        Storage::disk('public')->put('/public/uploads/' . $file_name . $format, $file);
        $path = str_replace('storage/', 'storage/public/', Storage::url('public/uploads/' . $file_name . $format));

        return $path;
    }
}