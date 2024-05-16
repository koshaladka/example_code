<?php

namespace App\Helpers;

final class ImageHelper
{

    public static function getImageUrl($absolutePath)
    {
        return asset('storage/' . $absolutePath);
        //    url('/storage/' . $absolutePath);
    }

}