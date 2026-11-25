<?php
declare(strict_types=1);

namespace App\Support;

use Cake\Http\Response;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Shared RFC 9110 conditional response handling (ETag / If-None-Match)
 * for JSON API, sitemap XML, and RSS feeds.
 */
final class HttpConditionalResponder
{
    /**
     * Initialize static utility boundary.
     */
    private function __construct()
    {
    }

    /**
     * Attach ETag, Vary, Cache-Control, and Content-Type, returning 304 when
     * If-None-Match matches (exact, weak W/, wildcard *, or mod_deflate -gzip).
     */
    public static function respond(
        ServerRequestInterface $request,
        Response $response,
        string $body,
        string $contentType,
        string $cacheControl,
    ): Response {
        $etag = '"' . hash('sha256', $body) . '"';
        $prepared = $response
            ->withHeader('Content-Type', $contentType)
            ->withHeader('ETag', $etag)
            ->withHeader('Cache-Control', $cacheControl)
            ->withHeader('Vary', 'Accept-Encoding');

        $ifNoneMatch = trim($request->getHeaderLine('If-None-Match'));
        if ($ifNoneMatch !== '' && self::matches($ifNoneMatch, trim($etag, '"'))) {
            return $prepared->withStatus(304)->withStringBody('');
        }

        return $prepared->withStringBody($body);
    }

    /**
     * Check whether an If-None-Match header matches the raw opaque tag.
     */
    public static function matches(string $headerValue, string $rawEtag): bool
    {
        foreach (explode(',', $headerValue) as $validator) {
            $candidate = trim($validator);
            if ($candidate === '') {
                continue;
            }
            if ($candidate === '*') {
                return true;
            }
            $normalized = str_starts_with($candidate, 'W/') ? substr($candidate, 2) : $candidate;
            if (!str_starts_with($normalized, '"') || !str_ends_with($normalized, '"') || strlen($normalized) < 2) {
                continue;
            }
            $unquoted = substr($normalized, 1, -1);
            if (str_ends_with($unquoted, '-gzip')) {
                $unquoted = substr($unquoted, 0, -5);
            }
            if (hash_equals($rawEtag, $unquoted)) {
                return true;
            }
        }

        return false;
    }
}
