<?php

namespace App\Support;

use Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig;
use Symfony\Component\HtmlSanitizer\Visitor\AttributeSanitizer\AttributeSanitizerInterface;

/**
 * The editor's resize handles only ever write a bare positive integer pixel
 * count into width/height (task #4, image resize; extended to video by the
 * video resize + lightbox follow-up) — never a unit suffix, a percentage,
 * or CSS. The client already keeps a resize within each element's own
 * min/max (100-800px wide for images, 160-800px for video — see
 * resources/js/rich-text-editor.js), but that's a UI convenience, not
 * something the server can trust from a direct request; this is the actual
 * backstop, with a generous ceiling (well above either client cap) that
 * exists only to block an absurd value, not to duplicate the client's
 * exact UX bounds.
 *
 * Named for "media", not "image", from the start of this class's second
 * element: confirmed generic (getSupportedAttributes()/sanitizeAttribute()
 * never referenced 'img' specifically — only getSupportedElements() did)
 * rather than assumed, the same way FileStorageService's
 * reconcilePendingFiles() and CleanupStalePendingMedia were each confirmed
 * category-agnostic before this codebase started calling them that.
 */
class MediaDimensionAttributeSanitizer implements AttributeSanitizerInterface
{
    private const MAX_PIXELS = 4000;

    public function getSupportedElements(): ?array
    {
        return ['img', 'video'];
    }

    public function getSupportedAttributes(): ?array
    {
        return ['width', 'height'];
    }

    public function sanitizeAttribute(string $element, string $attribute, string $value, HtmlSanitizerConfig $config): ?string
    {
        if (! preg_match('/\A[1-9][0-9]*\z/', $value)) {
            return null;
        }

        return (int) $value <= self::MAX_PIXELS ? $value : null;
    }
}
