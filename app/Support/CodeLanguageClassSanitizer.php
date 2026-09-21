<?php

namespace App\Support;

use Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig;
use Symfony\Component\HtmlSanitizer\Visitor\AttributeSanitizer\AttributeSanitizerInterface;

/**
 * The editor only ever puts a class on <code>, and only "language-xyz" (the
 * syntax-highlighting hint). Allowing the attribute through unfiltered would
 * let a crafted body attach arbitrary Tailwind classes ("fixed inset-0 z-50
 * bg-white ...") and overlay the page, so anything that isn't that exact
 * shape is dropped.
 */
class CodeLanguageClassSanitizer implements AttributeSanitizerInterface
{
    public function getSupportedElements(): ?array
    {
        return ['code'];
    }

    public function getSupportedAttributes(): ?array
    {
        return ['class'];
    }

    public function sanitizeAttribute(string $element, string $attribute, string $value, HtmlSanitizerConfig $config): ?string
    {
        return preg_match('/\Alanguage-[A-Za-z0-9_+#-]{1,30}\z/', $value) ? $value : null;
    }
}
