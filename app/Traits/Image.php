<?php

namespace App\Traits;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

trait Image
{

    public function deleteImage(String $imageFilePath): bool
    {
        if (!empty($imageFilePath) && Storage::exists($imageFilePath)) {
            Storage::delete($imageFilePath);
            return true;
        }

        return true;
    }

    public function uploadImage(String $imageFilePath, $imageFile): string
    {
        $deletedImage = $this->deleteImage($imageFilePath);

        if ($deletedImage) {
            $image = $imageFile;
            $filename = Str::random(10) . '.' . $image->getClientOriginalExtension();
            $fullImagePath = 'images/shops/' . $filename;

            $image->storeAs('images/shops/', $filename);
            return $fullImagePath;
        }
    }
}
