<?php

namespace App\Services;

use App\Models\LinkPreview;
use App\Models\User;
use App\Support\OpenGraphMetadataParser;
use App\Support\UrlSsrfGuard;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Smart Links (task #4) — turns a bare pasted URL into a title/image/
 * domain snapshot, backed by a global cache (link_previews) so the same
 * URL pasted repeatedly anywhere in the app doesn't re-fetch every time.
 *
 * Every fetch is guarded by UrlSsrfGuard first — this is the one place in
 * the app that makes an outbound HTTP request to an arbitrary,
 * user-submitted address, which is exactly the shape of a classic SSRF
 * vulnerability if left unchecked.
 *
 * $user is accepted now (unused by Tier 1's plain HTTP + Open Graph path)
 * so the controller/caller shape doesn't need to change again once the
 * Google Docs/Sheets/Slides Drive-API path (Tier 2, same task) is added —
 * that path needs the pasting user's own OAuth token, this one doesn't.
 *
 * Returns null — never throws — for anything that isn't confidently
 * resolvable (blocked by the SSRF guard, times out, 404s, has no usable
 * title): the caller's job on null is to leave the pasted text as a plain
 * hyperlink, never to show a broken or empty chip.
 */
class LinkPreviewService
{
    private const CACHE_TTL_DAYS = 7;

    private const FETCH_TIMEOUT_SECONDS = 5;

    // Enough for a real page's <head> (where og:title/og:image/<title>
    // always live) without downloading an entire large page body just to
    // read four lines of metadata.
    private const MAX_BYTES = 200_000;

    public function resolve(string $url, ?User $user = null): ?LinkPreviewResult
    {
        $url = trim($url);
        $hash = hash('sha256', self::normalize($url));

        $cached = LinkPreview::where('url_hash', $hash)->first();
        if ($cached && $cached->fetched_at->gt(now()->subDays(self::CACHE_TTL_DAYS))) {
            return $this->fromCache($cached);
        }

        $result = $this->fetchFresh($url);

        LinkPreview::updateOrCreate(
            ['url_hash' => $hash],
            [
                'url' => $url,
                'title' => $result?->title,
                'image_url' => $result?->image,
                'domain' => $result?->domain ?? self::domainOf($url) ?? '',
                'fetched_at' => now(),
            ]
        );

        return $result;
    }

    private function fromCache(LinkPreview $cached): ?LinkPreviewResult
    {
        // A cached row with no title means "we tried and got nothing" —
        // still worth remembering (so a known-dead link isn't re-fetched
        // on every single paste within the 7-day window), but there's
        // nothing to render from it.
        if ($cached->title === null) {
            return null;
        }

        return new LinkPreviewResult(
            url: $cached->url,
            title: $cached->title,
            image: $cached->image_url,
            domain: $cached->domain ?? '',
        );
    }

    private function fetchFresh(string $url): ?LinkPreviewResult
    {
        $blockReason = UrlSsrfGuard::reasonToBlock($url);
        if ($blockReason !== null) {
            Log::info('Smart Links: refused to fetch a URL', ['url' => $url, 'reason' => $blockReason]);

            return null;
        }

        try {
            $response = Http::timeout(self::FETCH_TIMEOUT_SECONDS)
                ->withOptions(['allow_redirects' => ['max' => 3]])
                ->withUserAgent('Solava-LinkPreview/1.0 (+internal task management tool)')
                ->get($url);
        } catch (\Throwable $e) {
            Log::info('Smart Links: fetch failed', ['url' => $url, 'error' => $e->getMessage()]);

            return null;
        }

        if (! $response->successful()) {
            return null;
        }

        // The Guzzle response body is already fully buffered in memory by
        // the time ->body() is called (Http::get() doesn't stream), so
        // the byte cap is applied here rather than during the transfer —
        // still bounds how much of a huge page gets handed to the DOM
        // parser, just not the network transfer itself.
        $html = substr($response->body(), 0, self::MAX_BYTES);
        $meta = OpenGraphMetadataParser::parse($html);

        if (empty($meta['title'])) {
            return null;
        }

        return new LinkPreviewResult(
            url: $url,
            title: $meta['title'],
            image: $meta['image'] ?? null,
            domain: self::domainOf($url) ?? '',
        );
    }

    /**
     * Fragment (#...) stripped before hashing — it never changes what the
     * server sends back, so "https://x/y#section-2" and "https://x/y"
     * would otherwise fetch (and cache) as two unrelated entries for the
     * exact same content.
     */
    private static function normalize(string $url): string
    {
        return (string) preg_replace('/#.*$/', '', $url);
    }

    private static function domainOf(string $url): ?string
    {
        $host = parse_url($url, PHP_URL_HOST);

        return $host !== false ? $host : null;
    }
}
