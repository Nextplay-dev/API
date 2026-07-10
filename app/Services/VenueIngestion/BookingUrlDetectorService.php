<?php

namespace App\Services\VenueIngestion;

use Symfony\Component\DomCrawler\Crawler;

class BookingUrlDetectorService
{
    public function detect(Crawler $crawler, string $baseUrl): ?string
    {
        $detectedUrl = null;

        $crawler->filter('a')->each(function (Crawler $node) use (&$detectedUrl) {
            if ($detectedUrl) {
                return;
            }

            $href = $node->attr('href');
            if (!$href) {
                return;
            }

            $text = strtolower(trim($node->text()));
            $hrefLower = strtolower($href);

            $keywords = [
                'book', 'booking', 'reserve', 'reservation', 'réserver',
                'reserver', 'planning', 'creneau', 'créneau', 'tarif', 'booking-widget'
            ];

            foreach ($keywords as $kw) {
                if (str_contains($hrefLower, $kw) || str_contains($text, $kw)) {
                    if (!str_starts_with($hrefLower, 'mailto:') && !str_starts_with($hrefLower, 'tel:')) {
                        $detectedUrl = $href;
                        return;
                    }
                }
            }
        });

        if ($detectedUrl) {
            return $this->makeAbsoluteUrl($detectedUrl, $baseUrl);
        }

        return null;
    }

    protected function makeAbsoluteUrl(string $url, string $baseUrl): string
    {
        if (parse_url($url, PHP_URL_SCHEME) != '') {
            return $url;
        }

        $baseParts = parse_url($baseUrl);
        $scheme = $baseParts['scheme'] ?? 'http';
        $host = $baseParts['host'] ?? '';

        if (str_starts_with($url, '//')) {
            return $scheme . ':' . $url;
        }

        if (str_starts_with($url, '/')) {
            return $scheme . '://' . $host . $url;
        }

        $path = $baseParts['path'] ?? '/';
        if (!str_ends_with($path, '/')) {
            $path = dirname($path) . '/';
        }

        return $scheme . '://' . $host . rtrim($path, '/') . '/' . ltrim($url, '/');
    }
}
