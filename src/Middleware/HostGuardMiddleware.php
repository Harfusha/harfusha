<?php
declare(strict_types=1);

namespace App\Middleware;

use Cake\Core\Configure;
use Cake\Http\Exception\BadRequestException;
use Cake\Http\Response;
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
        $rawPort = parse_url($configured, PHP_URL_PORT);
        $expectedPort = is_int($rawPort) ? $rawPort : null;
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
        $incomingHost = $uri->getHost();

        // 301 Redirect www. to non-www canonical host (SEO01, NET02)
        $isWww = is_string($expectedHost)
            && $expectedHost !== ''
            && strcasecmp($incomingHost, 'www.' . $expectedHost) === 0;
        if ($isWww) {
            $targetUri = $uri->withHost($expectedHost);
            if ($scheme === 'https') {
                $targetUri = $targetUri->withScheme('https')->withPort($expectedPort);
            }

            return (new Response())
                ->withStatus(301)
                ->withHeader('Location', (string)$targetUri);
        }

        // 301 Redirect HTTP to HTTPS if application is configured for HTTPS (NET02)
        $isProtoHttp = $request->hasHeader('X-Forwarded-Proto')
            && $request->getHeaderLine('X-Forwarded-Proto') === 'http';
        $isHttp = $uri->getScheme() === 'http' || $isProtoHttp;
        if (
            $scheme === 'https'
            && $isHttp
            && is_string($expectedHost)
            && strcasecmp($incomingHost, $expectedHost) === 0
        ) {
            $targetUri = $uri->withScheme('https')->withPort($expectedPort);

            return (new Response())
                ->withStatus(301)
                ->withHeader('Location', (string)$targetUri);
        }

        if (
            !is_string($expectedHost) || $expectedHost === '' ||
            strcasecmp($incomingHost, $expectedHost) !== 0 ||
            $expectedPort !== $uri->getPort()
        ) {
            throw new BadRequestException('Unexpected host');
        }

        return $handler->handle($request);
    }
}
