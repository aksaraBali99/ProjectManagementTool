<?php

namespace App\Support;

/**
 * Smart Links (task #4), Tier 1 — pulls a page's og:title out of its HTML
 * <head>, falling back to the plain <title> tag when a page has no Open
 * Graph markup at all (most pages don't bother with OG tags but every
 * real page has a <title>). Kept separate from LinkPreviewService so the
 * actual HTML-parsing logic can be tested against a raw string, with no
 * HTTP fetch involved.
 *
 * og:image is deliberately not parsed here: the rendered chip is a
 * compact inline one (icon + title + domain, matching file-chip's own
 * visual weight — see link-preview-extension.js), not a card with a
 * thumbnail, so there's nothing that would ever use an image URL.
 */
class OpenGraphMetadataParser
{
    public static function title(string $html): ?string
    {
        $document = new \DOMDocument;
        libxml_use_internal_errors(true);
        // A truncated/partial fetch (the caller may cap how many bytes it
        // reads) is still worth parsing whatever <head> made it through —
        // DOMDocument tolerates malformed/incomplete HTML rather than
        // throwing.
        $document->loadHTML($html, LIBXML_NOERROR | LIBXML_NOWARNING);
        libxml_clear_errors();

        $xpath = new \DOMXPath($document);

        return self::metaContent($xpath, 'og:title') ?? self::plainTitle($xpath);
    }

    private static function metaContent(\DOMXPath $xpath, string $property): ?string
    {
        // Some sites use name="og:title" instead of the spec's
        // property="og:title" — both are checked, matching what real
        // browsers/scrapers already tolerate.
        $nodes = $xpath->query("//meta[@property='{$property}' or @name='{$property}']/@content");
        $value = $nodes->length > 0 ? trim($nodes->item(0)->nodeValue) : '';

        return $value !== '' ? $value : null;
    }

    private static function plainTitle(\DOMXPath $xpath): ?string
    {
        $nodes = $xpath->query('//title');
        $value = $nodes->length > 0 ? trim($nodes->item(0)->textContent) : '';

        return $value !== '' ? $value : null;
    }
}
