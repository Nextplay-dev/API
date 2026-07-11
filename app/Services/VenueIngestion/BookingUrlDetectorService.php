<?php

namespace App\Services\VenueIngestion;

use Symfony\Component\DomCrawler\Crawler;

class BookingUrlDetectorService
{
    private const LINK_KEYWORDS = [
        'book', 'booking', 'reserve', 'reservation', 'réserver', 'reserver',
        'planning', 'creneau', 'créneau', 'tarif', 'booking-widget',
        'billetterie', 'commander', 'inscription', 'acheter', 'session',
        'rdv', 'rendez-vous', 'reservation', 'widget', 'checkout',
    ];

    private const URL_KEYWORDS = [
        'book', 'booking', 'reserve', 'reservation', 'reserver', 'réserver',
        'resa', 'creneau', 'créneau', 'planning', 'billetterie', 'ticket',
        'weezevent', 'helloasso', 'boost', 'zenbooking', 'resaweb', 'supabase',
        'calendly', 'doctolib', 'yplan', 'activcorner', 'deciplus', 'heitz',
        'sportigo', 'crossfit', 'resamatic', 'reservio', 'simplybook',
    ];

    public function detect(Crawler $crawler, string $baseUrl): ?string
    {
        $detectedUrl = $this->detectFromLinks($crawler, $baseUrl);

        if ($detectedUrl) {
            return $detectedUrl;
        }

        $detectedUrl = $this->detectFromIframes($crawler, $baseUrl);

        if ($detectedUrl) {
            return $detectedUrl;
        }

        return $this->detectFromForms($crawler, $baseUrl);
    }

    private function detectFromLinks(Crawler $crawler, string $baseUrl): ?string
    {
        $detectedUrl = null;

        $crawler->filter('a')->each(function (Crawler $node) use (&$detectedUrl, $baseUrl) {
            if ($detectedUrl) {
                return;
            }

            $href = $node->attr('href');
            if (!$href) {
                return;
            }

            if (str_starts_with(strtolower($href), 'mailto:') || str_starts_with(strtolower($href), 'tel:')) {
                return;
            }

            $text = strtolower(trim($node->text()));
            $hrefLower = strtolower($href);

            foreach (self::LINK_KEYWORDS as $keyword) {
                if (str_contains($hrefLower, $keyword) || str_contains($text, $keyword)) {
                    $detectedUrl = $this->makeAbsoluteUrl($href, $baseUrl);
                    return;
                }
            }

            foreach (self::URL_KEYWORDS as $keyword) {
                if (str_contains($hrefLower, $keyword)) {
                    $detectedUrl = $this->makeAbsoluteUrl($href, $baseUrl);
                    return;
                }
            }
        });

        return $detectedUrl;
    }

    private function detectFromIframes(Crawler $crawler, string $baseUrl): ?string
    {
        $detectedUrl = null;

        $crawler->filter('iframe[src]')->each(function (Crawler $node) use (&$detectedUrl, $baseUrl) {
            if ($detectedUrl) {
                return;
            }

            $src = strtolower($node->attr('src') ?? '');

            foreach (self::URL_KEYWORDS as $keyword) {
                if (str_contains($src, $keyword)) {
                    $detectedUrl = $this->makeAbsoluteUrl($node->attr('src'), $baseUrl);
                    return;
                }
            }
        });

        return $detectedUrl;
    }

    private function detectFromForms(Crawler $crawler, string $baseUrl): ?string
    {
        $detectedUrl = null;

        $crawler->filter('form[action]')->each(function (Crawler $node) use (&$detectedUrl, $baseUrl) {
            if ($detectedUrl) {
                return;
            }

            $action = strtolower($node->attr('action') ?? '');

            foreach (array_merge(self::LINK_KEYWORDS, self::URL_KEYWORDS) as $keyword) {
                if (str_contains($action, $keyword)) {
                    $detectedUrl = $this->makeAbsoluteUrl($node->attr('action'), $baseUrl);
                    return;
                }
            }
        });

        return $detectedUrl;
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
