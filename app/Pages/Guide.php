<?php
namespace App\Pages;

class Guide {
    public static function getFile($id, $filename) {
        return public_path('guide.pdf');
    }
}
