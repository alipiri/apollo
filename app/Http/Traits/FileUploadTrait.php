<?php

namespace App\Http\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Storage;

trait FileUploadTrait
{
    public function uploadFile(UploadedFile $file, $folder, $preFilename = NULL, $baseFolder = 'public', $allowedFileExtension = ['jpeg', 'jpg', 'png', 'svg', 'xlsx', 'xls']): bool|string
    {
        $fileExtension = $file->getClientOriginalExtension();
        $validate      = in_array($fileExtension, $allowedFileExtension, true);
        if ($validate) {

            $filename = $this->generateFileName($preFilename, $fileExtension);

            if ($this->moveToServer($baseFolder, $folder, $filename, $file)) {
                return $filename;
            }
        }
        return false;
    }

    public function generateFileName($preFilename, $fileExtension): string
    {
        if ($preFilename !== NULL) {
            $filename = $preFilename . "_" . time() . '_' . Str::upper(Str::random(5)) . '.' . $fileExtension;
        } else {
            $filename = time() . '_' . Str::upper(Str::random(5)) . '.' . $fileExtension;
        }

        return $filename;
    }

    public function moveToServer($baseFolder, $folder, $filename, $file): bool
    {
        if ($baseFolder === 'storage') {
            Storage::disk('public')->putFileAs($folder, $file, $filename);
        } else {
            $file->move(public_path() . '/' . $folder, $filename);
        }
        return true;
    }
}
