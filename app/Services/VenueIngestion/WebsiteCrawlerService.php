<?php

namespace App\Services\VenueIngestion;

use Illuminate\Support\Facades\Http;
use Symfony\Component\DomCrawler\Crawler;

class WebsiteCrawlerService
{
    public function crawl(string $url): ?array
    {
        try {
            $response = Http::withoutVerifying()->withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            ])->timeout(15)->get($url);

            if (!$response->successful()) {
                return null;
            }

            $html = $response->body();
            $crawler = new Crawler($html);

            $title = '';
            try {
                $title = self::cleanText($crawler->filter('title')->first()->text());
            } catch (\InvalidArgumentException) {}

            $description = '';
            try {
                $description = self::cleanText($crawler->filter('meta[name="description"]')->first()->attr('content') ?? '');
            } catch (\InvalidArgumentException) {}

            $headings = [];
            $crawler->filter('h1, h2')->each(function (Crawler $node) use (&$headings) {
                $text = trim($node->text());
                if (!empty($text)) {
                    $headings[] = self::cleanText($text);
                }
            });

            $bodyText = '';
            try {
                $bodyText = $crawler->filter('body')->first()->text();
                $bodyText = preg_replace('/\s+/', ' ', $bodyText);
                $bodyText = self::cleanText(mb_substr(trim($bodyText), 0, 1000));
            } catch (\InvalidArgumentException) {}

            return [
                'title' => $title,
                'description' => $description,
                'headings' => $headings,
                'body_text' => $bodyText,
                'crawler' => $crawler,
                'html' => $html,
            ];
        } catch (\Exception) {
            return null;
        }
    }

    private static function cleanText(string $text): string
    {
        $text = iconv('UTF-8', 'UTF-8//IGNORE//TRANSLIT', $text);
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        return $text;
    }
}
