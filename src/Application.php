<?php
declare(strict_types=1);

namespace App;

use App\Middleware\HostGuardMiddleware;
use App\Middleware\SecurityHeadersMiddleware;
use Cake\Core\Configure;
use Cake\Core\ContainerInterface;
use Cake\Error\Middleware\ErrorHandlerMiddleware;
use Cake\Http\BaseApplication;
use Cake\Http\Middleware\BodyParserMiddleware;
use Cake\Http\Middleware\CsrfProtectionMiddleware;
use Cake\Http\MiddlewareQueue;
use Cake\Http\ServerRequest;
use Cake\Routing\Middleware\AssetMiddleware;
use Cake\Routing\Middleware\RoutingMiddleware;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use RuntimeException;

final class Application extends BaseApplication
{
    /**
     * Register required CakePHP plugins during bootstrap.
     */
    public function bootstrap(): void
    {
        parent::bootstrap();
        $this->addPlugin('Migrations');
    }

    /**
     * Build the ordered HTTP middleware pipeline.
     */
    public function middleware(MiddlewareQueue $middlewareQueue): MiddlewareQueue
    {
        $configuredUrl = Configure::read('App.fullBaseUrl');
        $url = is_string($configuredUrl) ? $configuredUrl : '';
        $config = Configure::read('Error');
        if (!is_array($config)) {
            throw new RuntimeException('CakePHP error configuration must be an array');
        }
        $csrf = new CsrfProtectionMiddleware([
            'httponly' => true,
            'samesite' => 'Strict',
            'secure' => str_starts_with($url, 'https://'),
        ]);
        $csrf->skipCheckCallback(
            static fn(ServerRequest $request): bool => $request->getUri()->getPath() === '/cron/github',
        );
        $csrfWrapper = static function (
            ServerRequestInterface $request,
            RequestHandlerInterface $handler,
        ) use ($csrf): ResponseInterface {
            $path = $request->getUri()->getPath();
            if (
                $path === '/sitemap.xml' ||
                $path === '/healthz' ||
                $path === '/robots.txt' ||
                $path === '/manifest.webmanifest' ||
                str_starts_with($path, '/api/') ||
                str_ends_with($path, '/feed.xml')
            ) {
                return $handler->handle($request);
            }

            return $csrf->process($request, $handler);
        };

        return $middlewareQueue
            ->add(new SecurityHeadersMiddleware())
            ->add(new ErrorHandlerMiddleware($config, $this))
            ->add(new HostGuardMiddleware())
            ->add(new AssetMiddleware(['cacheTime' => Configure::read('Asset.cacheTime')]))
            ->add(new RoutingMiddleware($this))
            ->add(new BodyParserMiddleware())
            ->add($csrfWrapper);
    }

    /**
     * Configure dependency injection bindings.
     */
    public function services(ContainerInterface $container): void
    {
    }
}
