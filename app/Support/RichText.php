<?php

namespace App\Support;

use Symfony\Component\HtmlSanitizer\HtmlSanitizer;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig;

/**
 * Single place that knows how task descriptions and comment bodies are
 * stored and shown now that they come from the TipTap editor.
 *
 * Two storage formats coexist in the database:
 *   - legacy plain text (everything saved before the editor swap, plus
 *     anything written by the Bulk Import feature), and
 *   - HTML produced by the editor (a run of block elements: <p>, <h1-3>,
 *     <ul>/<ol>, <pre>, plus the void <img> block node — see VOID_TAGS).
 *
 * The format is detected from the value itself rather than tracked in a
 * column, so the LONGTEXT migration stays a pure widening and no existing
 * row ever has to be rewritten. The editor's HTML always starts with a
 * block-level open tag (or a void block tag) and ends with a block-level
 * close tag (or a void block tag); ordinary prose that merely mentions a
 * tag ("use <b> here") doesn't, so it stays plain text. The one accepted
 * edge case: a legacy plain-text value that literally begins with <p> and
 * ends with </p> is treated as HTML — and is still run through the
 * sanitizer, so the worst outcome is it rendering as the paragraph its
 * author typed.
 *
 * Every HTML value is sanitized against an allowlist that matches exactly
 * what the editor can emit, both when saved (normalize) and again when
 * shown (toHtml) — so a crafted request body, a direct DB write, or a
 * future editor bug can't put script into a page.
 */
class RichText
{
    private const BLOCK_TAGS = 'p|h[1-6]|ul|ol|pre';

    /**
     * Block-level nodes the editor emits with no closing tag (an uploaded
     * image is TipTap's "block" Image node, not wrapped in a <p>) — isHtml()
     * and plainText() both need a second rule for these, since neither can
     * match on "<tag ...>...</tag>".
     */
    private const VOID_BLOCK_TAGS = 'img';

    private static ?HtmlSanitizer $sanitizer = null;

    /**
     * Checked as two independent anchored patterns (starts-with, ends-with)
     * rather than one "open ... middle ... close" pattern, specifically so a
     * document that's just a single void tag ("<img src=...>" alone, nothing
     * else) still counts: it both starts AND ends with that same tag, which
     * a single sequential open-then-close regex can't match — an open
     * alternative for a void tag necessarily consumes the tag's closing ">"
     * too (there's no separate closing tag to leave for a later "close"
     * match), so with only one tag present nothing is left over to satisfy
     * a second, distinct close-match.
     */
    public static function isHtml(?string $value): bool
    {
        if ($value === null) {
            return false;
        }

        $startsWithBlock = preg_match(
            '/\A\s*<(?:(?:'.self::BLOCK_TAGS.')[\s>]|(?:'.self::VOID_BLOCK_TAGS.')\b[^>]*>)/is',
            $value
        );

        $endsWithBlock = preg_match(
            '/(?:<\/(?:'.self::BLOCK_TAGS.')>|<(?:'.self::VOID_BLOCK_TAGS.')\b[^>]*>)\s*\z/is',
            $value
        );

        return $startsWithBlock === 1 && $endsWithBlock === 1;
    }

    /**
     * Wraps legacy plain text as editor-ready HTML: HTML-escaped (so a "<"
     * or "&" typed years ago shows as itself, never as markup), blank-line
     * separated blocks become paragraphs, and single newlines become <br>.
     */
    public static function fromPlainText(string $text): string
    {
        $text = trim(str_replace(["\r\n", "\r"], "\n", $text));

        if ($text === '') {
            return '';
        }

        return collect(preg_split('/\n{2,}/', $text))
            ->map(fn (string $paragraph) => '<p>'.str_replace("\n", '<br>', e($paragraph)).'</p>')
            ->implode('');
    }

    /**
     * The one method both display and the editor's initial content go
     * through: whatever is stored (legacy plain text, editor HTML, null)
     * comes out as safe, valid HTML the editor can load and a page can
     * print with {!! !!}.
     */
    public static function toHtml(?string $value): string
    {
        if ($value === null || trim($value) === '') {
            return '';
        }

        return self::isHtml($value) ? self::sanitize($value) : self::fromPlainText($value);
    }

    /**
     * Prepares an incoming description/body for storage. Plain text
     * submissions (imports, older clients, tests) are stored as given;
     * HTML is sanitized. A blank value — including the editor's empty
     * "<p></p>" — becomes null.
     */
    public static function normalize(?string $value): ?string
    {
        if ($value === null || trim($value) === '') {
            return null;
        }

        if (! self::isHtml($value)) {
            return $value;
        }

        $clean = self::sanitize($value);

        // An image-only description/comment has no visible text at all, but
        // it's still real content, not a blank editor — plainText() alone
        // would otherwise null it out.
        if (self::plainText($clean) === '' && ! str_contains($clean, '<img')) {
            return null;
        }

        return $clean;
    }

    /**
     * Visible text only — used for the comment length limit, audit-trail
     * labels, and anywhere else tags would just be noise. An embedded image
     * contributes nothing to the text (there's nothing to read), just a line
     * break so it doesn't glue neighbouring words together — an image-only
     * comment isn't blank because of that, see normalize()'s own check.
     */
    public static function plainText(?string $value): string
    {
        if ($value === null) {
            return '';
        }

        if (! self::isHtml($value)) {
            return trim($value);
        }

        $withBreaks = preg_replace(
            '/<\/(?:'.self::BLOCK_TAGS.'|li)>|<br\s*\/?>|<(?:'.self::VOID_BLOCK_TAGS.')\b[^>]*>/i',
            "\n",
            $value
        );

        return trim(html_entity_decode(strip_tags($withBreaks), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
    }

    public static function sanitize(string $html): string
    {
        return self::sanitizer()->sanitize($html);
    }

    private static function sanitizer(): HtmlSanitizer
    {
        return self::$sanitizer ??= new HtmlSanitizer(
            (new HtmlSanitizerConfig)
                ->allowElement('p')
                ->allowElement('br')
                ->allowElement('strong')
                ->allowElement('em')
                ->allowElement('u')
                ->allowElement('h1')
                ->allowElement('h2')
                ->allowElement('h3')
                ->allowElement('ul')
                ->allowElement('ol')
                ->allowElement('li')
                ->allowElement('pre')
                ->allowElement('code', ['class'])
                ->allowElement('a', ['href'])
                ->allowElement('img', ['src', 'alt'])
                ->dropElement('script')
                ->dropElement('style')
                ->allowLinkSchemes(['http', 'https', 'mailto'])
                ->allowRelativeLinks(false)
                ->forceAttribute('a', 'rel', 'noopener noreferrer nofollow')
                ->forceAttribute('a', 'target', '_blank')
                // 'src' is sanitized against these same rules for every
                // element (Symfony's UrlAttributeSanitizer applies them to
                // anything that isn't an <a>/<area> href) — img is the only
                // one we allow. The library's own default additionally
                // allows "data:" here; excluded, since a base64 image would
                // both defeat the point of uploading to storage and let a
                // crafted request stuff an arbitrarily large blob straight
                // into the database.
                ->allowMediaSchemes(['http', 'https'])
                // Unlike a link's href, a relative <img src> carries no real
                // risk (it's just a same-origin GET for image bytes, same as
                // any other <img> the browser already loads on this page —
                // no script executes, nothing crosses origins) — and
                // config('filestorage.disk') is allowed to be 'local' rather
                // than R2/MinIO (see FileStorageService's docblock), which
                // legitimately produces relative "/storage/..." URLs.
                // Disallowing it would break that supported configuration
                // for no real security gain.
                ->allowRelativeMedias(true)
                ->withAttributeSanitizer(new CodeLanguageClassSanitizer)
                // The library's default is 20,000 bytes, and beyond that it
                // returns an empty string rather than the input — a long
                // description would silently vanish on save.
                ->withMaxInputLength(5_000_000)
        );
    }
}
