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
 *     <ul>/<ol>, <pre>).
 *
 * The format is detected from the value itself rather than tracked in a
 * column, so the LONGTEXT migration stays a pure widening and no existing
 * row ever has to be rewritten. The editor's HTML always starts with a
 * block-level open tag and ends with a block-level close tag; ordinary
 * prose that merely mentions a tag ("use <b> here") doesn't, so it stays
 * plain text. The one accepted edge case: a legacy plain-text value that
 * literally begins with <p> and ends with </p> is treated as HTML — and is
 * still run through the sanitizer, so the worst outcome is it rendering
 * as the paragraph its author typed.
 *
 * Every HTML value is sanitized against an allowlist that matches exactly
 * what the editor can emit, both when saved (normalize) and again when
 * shown (toHtml) — so a crafted request body, a direct DB write, or a
 * future editor bug can't put script into a page.
 */
class RichText
{
    private const BLOCK_TAGS = 'p|h[1-6]|ul|ol|pre';

    private static ?HtmlSanitizer $sanitizer = null;

    public static function isHtml(?string $value): bool
    {
        if ($value === null) {
            return false;
        }

        return (bool) preg_match('/\A\s*<('.self::BLOCK_TAGS.')[\s>].*<\/('.self::BLOCK_TAGS.')>\s*\z/is', $value);
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

        return self::plainText($clean) === '' ? null : $clean;
    }

    /**
     * Visible text only — used for the comment length limit, audit-trail
     * labels, and anywhere else tags would just be noise.
     */
    public static function plainText(?string $value): string
    {
        if ($value === null) {
            return '';
        }

        if (! self::isHtml($value)) {
            return trim($value);
        }

        $withBreaks = preg_replace('/<\/(?:'.self::BLOCK_TAGS.'|li)>|<br\s*\/?>/i', "\n", $value);

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
                ->dropElement('script')
                ->dropElement('style')
                // Unknown elements are dropped WITH their contents by default.
                // <span> is what the editor's emoji node serializes to
                // (<span data-type="emoji">🚀</span>) — the browser unwraps it
                // before submitting, but if one ever arrives, keep the text.
                ->blockElement('span')
                ->allowLinkSchemes(['http', 'https', 'mailto'])
                ->allowRelativeLinks(false)
                ->forceAttribute('a', 'rel', 'noopener noreferrer nofollow')
                ->forceAttribute('a', 'target', '_blank')
                ->withAttributeSanitizer(new CodeLanguageClassSanitizer)
                // The library's default is 20,000 bytes, and beyond that it
                // returns an empty string rather than the input — a long
                // description would silently vanish on save.
                ->withMaxInputLength(5_000_000)
        );
    }
}
