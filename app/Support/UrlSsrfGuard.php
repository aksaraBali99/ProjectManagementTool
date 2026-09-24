<?php

namespace App\Support;

/**
 * Smart Links (task #4) fetches an arbitrary user-submitted URL
 * server-side — a classic SSRF vector if a request can be aimed at this
 * app's own internal network (the R2/MinIO endpoint, another service on
 * the same host, a cloud metadata endpoint at 169.254.169.254, etc.)
 * rather than the public internet. This class is the one place that
 * decides whether a URL is safe to actually fetch.
 *
 * Two checks, both required:
 *   1. Scheme must be exactly http/https — no file://, gopher://, etc.
 *   2. The HOSTNAME'S RESOLVED IP (not just the literal string in the
 *      URL) must not fall in a private/loopback/link-local/reserved
 *      range. Checking the resolved IP, not the hostname text, is
 *      deliberate: "http://127.0.0.1/" is an obvious block, but so is
 *      "http://my-private-hostname/" if that hostname's DNS record
 *      happens to point at 127.0.0.1 or a 10.x address — a bare string
 *      check on the URL would miss that entirely.
 *
 * Known, deliberately accepted gap: this checks the resolved IP once,
 * before the fetch — it does not pin the actual HTTP request to that
 * exact IP. A DNS answer that changes between this check and the fetch a
 * moment later (DNS rebinding) could theoretically slip through. Flagged
 * here rather than silently presented as complete protection; closing it
 * fully means resolving the hostname once and handing the IP directly to
 * the HTTP client (with the original Host header preserved), which
 * LinkPreviewService does not currently do.
 */
class UrlSsrfGuard
{
    /**
     * @return string|null null if the URL is safe to fetch, otherwise a
     *                     human-readable reason it was blocked
     */
    public static function reasonToBlock(string $url): ?string
    {
        $parts = parse_url($url);

        if ($parts === false || empty($parts['host'])) {
            return 'Not a valid URL.';
        }

        $scheme = strtolower($parts['scheme'] ?? '');
        if (! in_array($scheme, ['http', 'https'], true)) {
            return 'Only http and https URLs are allowed.';
        }

        $host = $parts['host'];

        // A bare IP literal in the URL (e.g. "http://169.254.169.254/")
        // resolves to itself — check it directly rather than trying to
        // "resolve" an address that's already one.
        $ips = filter_var($host, FILTER_VALIDATE_IP) ? [$host] : self::resolve($host);

        if (empty($ips)) {
            return "Could not resolve \"{$host}\".";
        }

        foreach ($ips as $ip) {
            if (self::isPrivateOrReserved($ip)) {
                return "\"{$host}\" resolves to a private or internal address.";
            }
        }

        return null;
    }

    public static function isSafe(string $url): bool
    {
        return self::reasonToBlock($url) === null;
    }

    /**
     * @return array<int, string>
     */
    private static function resolve(string $host): array
    {
        $ips = [];

        // gethostbyname() only ever returns one (IPv4) answer, or the
        // hostname itself unchanged on failure — dns_get_record() is
        // asked for the full A/AAAA answer set on top of it, since a
        // hostname can legitimately have multiple records and every one
        // of them needs to clear the check, not just the first.
        $a = @gethostbyname($host);
        if ($a !== $host && filter_var($a, FILTER_VALIDATE_IP)) {
            $ips[] = $a;
        }

        foreach ((@dns_get_record($host, DNS_A + DNS_AAAA) ?: []) as $record) {
            if (! empty($record['ip'])) {
                $ips[] = $record['ip'];
            }
            if (! empty($record['ipv6'])) {
                $ips[] = $record['ipv6'];
            }
        }

        return array_values(array_unique($ips));
    }

    /**
     * FILTER_FLAG_NO_PRIV_RANGE covers the standard RFC1918/ULA private
     * ranges and loopback; FILTER_FLAG_NO_RES_RANGE additionally covers
     * reserved ranges PHP's private-range flag doesn't, including
     * 169.254.0.0/16 (link-local — where a cloud provider's instance
     * metadata endpoint typically lives) and 0.0.0.0/8.
     */
    private static function isPrivateOrReserved(string $ip): bool
    {
        return filter_var(
            $ip,
            FILTER_VALIDATE_IP,
            FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
        ) === false;
    }
}
