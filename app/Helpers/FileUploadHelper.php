<?php

use Intervention\Image\Facades\Image;

if (!function_exists('uploadImageWithThumbnail')) {
    function uploadImageWithThumbnail($image, $location, $main_height, $thumbnail_height)
    {
        $main_location = $location . '/main';
        $thumbnail_location = $location . '/thumbnail';
        $large_image = resizingTheImage($image, $main_location, $main_height);
        $thumbnail_image = resizingTheImage($image, $thumbnail_location, $thumbnail_height);
        return [
            'large_image' => $large_image,
            'thumbnail_image' => $thumbnail_image
        ];
    }
}

if (!function_exists('resizingTheImage')) {
    function resizingTheImage($image, $location, $height)
    {
        // RENAMING THE ORIGINAL IMAGE
        $image_original_name = $image->getClientOriginalName();
        $image_original_extension = $image->getClientOriginalExtension();
        $new_image_unique_name = uniqueImageName($image_original_name, $image_original_extension);

        // SAVING THE IMAGE IN STORAGE
        $image->storeAs('public/uploads/' . $location, $new_image_unique_name);

        // CREATING THE THUMBNAIL
        $thumbnail = public_path('storage/uploads/' . $location . '/' . $new_image_unique_name);
        Image::make($thumbnail)->resize(null, $height, function ($constraint) {
            $constraint->aspectRatio();
        })->save($thumbnail);

        return $new_image_unique_name;
    }
}

if (!function_exists('uniqueImageName')) {
    function uniqueImageName($image_original_name, $image_original_extension)
    {
        if (strlen($image_original_name) > 10)
            $image_original_name = substr($image_original_name, 0, 10);

        return $image_original_name . '_' . time() . '_' . str_random(5) . '.' . $image_original_extension;
    }
}

if (!function_exists('uploadFileHelper')) {
    function uploadFileHelper($file)
    {
        // RENAMING THE ORIGINAL FILE
        $file_original_name = $file->getClientOriginalName();
        $file_original_extension = $file->getClientOriginalExtension();
        $new_file_unique_name = uniqueImageName($file_original_name, $file_original_extension);
        $file->storeAs('public/uploads/files/', $new_file_unique_name);
        return $new_file_unique_name;
    }
}
