<?php
declare(strict_types=1);

namespace App\Middleware;

use Cake\Core\Configure;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/** HTTP response policies applied to every Cake response, including failures. */
final class SecurityHeadersMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);
        $response = $response
            ->withHeader('X-Content-Type-Options', 'nosniff')
            ->withHeader('Referrer-Policy', 'strict-origin-when-cross-origin')
            ->withHeader('X-Frame-Options', 'DENY')
            ->withHeader('Permissions-Policy', 'camera=(), microphone=(), geolocation=(), payment=()')
            ->withHeader('Cross-Origin-Resource-Policy', 'same-origin')
            ->withHeader('Content-Security-Policy', implode('; ', [
                "default-src 'self'", "base-uri 'self'", "object-src 'none'", "frame-ancestors 'none'",
                "form-action 'self'", "script-src 'self'", "style-src 'self'",
                "img-src 'self' data: blob:", "font-src 'self'", "connect-src 'self'",
            ]));
        $origin = Configure::read('App.fullBaseUrl');
        if (is_string($origin) && str_starts_with($origin, 'https://')) {
            $response = $response->withHeader('Strict-Transport-Security', 'max-age=31536000');
        }
        $path = $request->getUri()->getPath();
        if (str_starts_with($path, '/admin') || str_starts_with($path, '/cron')) {
            $response = $response->withHeader('Cache-Control', 'no-store, private');
        }

        return $response;
    }
}
