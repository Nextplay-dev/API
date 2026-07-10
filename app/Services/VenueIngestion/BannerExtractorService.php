<?php

namespace App\Services\VenueIngestion;

use Symfony\Component\DomCrawler\Crawler;

class BannerExtractorService
{
    public function extract(Crawler $crawler, string $baseUrl): ?string
    {
        $imageUrl = null;

        try {
            $imageUrl = $crawler->filter('meta[property="og:image"]')->first()->attr('content');
        } catch (\InvalidArgumentException) {}

        if (!$imageUrl) {
            try {
                $imageUrl = $crawler->filter('meta[name="twitter:image"]')->first()->attr('content');
            } catch (\InvalidArgumentException) {}
        }

        if (!$imageUrl) {
            $crawler->filter('img')->each(function (Crawler $node) use (&$imageUrl) {
                if ($imageUrl) {
                    return;
                }
                $src = $node->attr('src');
                if (!$src) {
                    return;
                }

                $alt = strtolower($node->attr('alt') ?? '');
                $class = strtolower($node->attr('class') ?? '');
                $id = strtolower($node->attr('id') ?? '');

                $keywords = ['banner', 'hero', 'slide', 'background', 'logo', 'header'];
                foreach ($keywords as $kw) {
                    if (str_contains($src, $kw) || str_contains($alt, $kw) || str_contains($class, $kw) || str_contains($id, $kw)) {
                        $imageUrl = $src;
                        return;
                    }
                }
            });
        }

        if (!$imageUrl) {
            try {
                $imageUrl = $crawler->filter('img')->first()->attr('src');
            } catch (\InvalidArgumentException) {}
        }

        if ($imageUrl) {
            return $this->makeAbsoluteUrl($imageUrl, $baseUrl);
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
