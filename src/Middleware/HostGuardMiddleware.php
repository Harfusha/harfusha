<?php
declare(strict_types=1);

namespace App\Middleware;

use Cake\Core\Configure;
use Cake\Http\Exception\BadRequestException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/** Reject unexpected hosts and ports; trust the configured canonical origin only. */
final class HostGuardMiddleware implements MiddlewareInterface
{
    /**
     * Process an HTTP request using this middleware.
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $configured = Configure::read('App.fullBaseUrl');
        if (!is_string($configured)) {
            throw new BadRequestException('Invalid configured origin');
        }
        $expectedHost = parse_url($configured, PHP_URL_HOST);
        $expectedPort = parse_url($configured, PHP_URL_PORT);
        $scheme = parse_url($configured, PHP_URL_SCHEME);
        // PSR-7 URI implementations represent default HTTP(S) ports as null.
        // Normalize explicit :80 / :443 in the configured canonical URL too.
        if (
            ($scheme === 'http' && $expectedPort === 80) ||
            ($scheme === 'https' && $expectedPort === 443)
        ) {
            $expectedPort = null;
        }
        $uri = $request->getUri();
        if (
            !is_string($expectedHost) || $expectedHost === '' ||
            strcasecmp($uri->getHost(), $expectedHost) !== 0 ||
            $expectedPort !== $uri->getPort()
        ) {
            throw new BadRequestException('Unexpected host');
        }

        return $handler->handle($request);
    }
}
