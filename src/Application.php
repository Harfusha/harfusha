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
        $csrf = new CsrfProtectionMiddleware(['httponly' => true, 'samesite' => 'Strict', 'secure' => str_starts_with($url, 'https://')]);
        $csrf->skipCheckCallback(static fn(ServerRequest $request): bool => $request->getUri()->getPath() === '/cron/github');

        return $middlewareQueue
            ->add(new SecurityHeadersMiddleware())
            ->add(new ErrorHandlerMiddleware($config, $this))
            ->add(new HostGuardMiddleware())
            ->add(new AssetMiddleware(['cacheTime' => Configure::read('Asset.cacheTime')]))
            ->add(new RoutingMiddleware($this))
            ->add(new BodyParserMiddleware())
            ->add($csrf);
    }

    /**
     * Configure dependency injection bindings.
     */
    public function services(ContainerInterface $container): void
    {
    }
}
