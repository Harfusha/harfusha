<?php
declare(strict_types=1);

namespace App\Middleware;

use App\Security\TrustedProxy;
use Cake\Core\Configure;
use Cake\Http\Exception\BadRequestException;
use Cake\Http\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/** Reject unexpected hosts and ports; trust the configured canonical origin only. */
final readonly class HostGuardMiddleware implements MiddlewareInterface
{
    /**
     * Initialize middleware with explicit proxy trust boundary.
     */
    public function __construct(private ?TrustedProxy $trustedProxy = null)
    {
    }

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
        if (!is_string($expectedHost) || $expectedHost === '' || !in_array($scheme, ['http', 'https'], true)) {
            throw new BadRequestException('Invalid configured origin');
        }
        // PSR-7 URI implementations represent default HTTP(S) ports as null.
        // Normalize explicit :80 / :443 in the configured canonical URL too.
        if (
            ($scheme === 'http' && $expectedPort === 80) ||
            ($scheme === 'https' && $expectedPort === 443)
        ) {
            $expectedPort = null;
        }

        $uri = $request->getUri();
        $uriScheme = strtolower($uri->getScheme());
        $incomingHost = $uri->getHost();
        $incomingPort = $uri->getPort();
        if (
            ($uriScheme === 'http' && $incomingPort === 80) ||
            ($uriScheme === 'https' && $incomingPort === 443)
        ) {
            $incomingPort = null;
        }

        $isCanonicalHost = strcasecmp($incomingHost, $expectedHost) === 0;
        $isWwwHost = strcasecmp($incomingHost, 'www.' . $expectedHost) === 0;
        if ((!$isCanonicalHost && !$isWwwHost) || $expectedPort !== $incomingPort) {
            throw new BadRequestException('Unexpected host');
        }

        // Reconstruct target URI strictly from the configured canonical origin.
        $canonicalUri = $uri
            ->withScheme($scheme)
            ->withHost($expectedHost)
            ->withPort($expectedPort);

        if ($isWwwHost) {
            return (new Response())
                ->withStatus(301)
                ->withHeader('Location', (string)$canonicalUri);
        }

        $proxy = $this->trustedProxy ?? TrustedProxy::fromEnvironment();
        $effectiveScheme = $proxy->effectiveScheme($request);
        if ($scheme === 'https' && $effectiveScheme === 'http') {
            return (new Response())
                ->withStatus(301)
                ->withHeader('Location', (string)$canonicalUri);
        }

        return $handler->handle($request);
    }
}
