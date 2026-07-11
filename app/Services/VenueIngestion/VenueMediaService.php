<?php

namespace App\Services\VenueIngestion;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Exception;

class VenueMediaService
{
    public function processAndUpload(?string $url): ?string
    {
        if (empty($url)) {
            return null;
        }

        try {
            $response = Http::withoutVerifying()->timeout(15)->get($url);
            if (!$response->successful()) {
                return null;
            }

            $content = $response->body();
            if (empty($content)) {
                return null;
            }

            if (str_contains(strtolower(substr($content, 0, 500)), '<svg') || str_ends_with(strtolower(parse_url($url, PHP_URL_PATH) ?? ''), '.svg')) {
                return null;
            }

            if (!function_exists('imagecreatefromstring')) {
                $filename = Str::uuid() . '.jpg';
                Storage::disk('r2')->put('banners/' . $filename, $content);
                return Storage::disk('r2')->url('banners/' . $filename);
            }

            $gdImage = @imagecreatefromstring($content);
            if (!$gdImage) {
                return null;
            }

            ob_start();
            $success = @imagejpeg($gdImage, null, 85);
            $jpegContent = ob_get_clean();
            imagedestroy($gdImage);

            if (!$success || empty($jpegContent)) {
                return null;
            }

            $filename = Str::uuid() . '.jpg';
            Storage::disk('r2')->put('banners/' . $filename, $jpegContent);

            return Storage::disk('r2')->url('banners/' . $filename);
        } catch (Exception) {
            return null;
        }
    }
}
