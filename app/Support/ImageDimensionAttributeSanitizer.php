<?php

namespace App\Support;

use Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig;
use Symfony\Component\HtmlSanitizer\Visitor\AttributeSanitizer\AttributeSanitizerInterface;

/**
 * The editor's resize handles only ever write a bare positive integer pixel
 * count into width/height (task #4, image resize) — never a unit suffix, a
 * percentage, or CSS. The client already keeps a resize within 100-800px
 * wide / 60px+ tall, but that's a UI convenience, not something the server
 * can trust from a direct request; this is the actual backstop, with a
 * generous ceiling (well above the client's own cap) that exists only to
 * block an absurd value, not to duplicate the client's exact UX bounds.
 */
class ImageDimensionAttributeSanitizer implements AttributeSanitizerInterface
{
    private const MAX_PIXELS = 4000;

    public function getSupportedElements(): ?array
    {
        return ['img'];
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
