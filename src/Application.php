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

final class Application extends BaseApplication
{
    public function bootstrap(): void
    {
        parent::bootstrap();
        $this->addPlugin('Migrations');
    }

    public function middleware(MiddlewareQueue $middlewareQueue): MiddlewareQueue
    {
        $csrf = new CsrfProtectionMiddleware(['httponly' => true, 'samesite' => 'Strict', 'secure' => str_starts_with((string)Configure::read('App.fullBaseUrl'), 'https://')]);
        $csrf->skipCheckCallback(static fn (ServerRequest $request): bool => $request->getUri()->getPath() === '/cron/github');
        return $middlewareQueue
            ->add(new SecurityHeadersMiddleware())
            ->add(new ErrorHandlerMiddleware(Configure::read('Error'), $this))
            ->add(new HostGuardMiddleware())
            ->add(new AssetMiddleware(['cacheTime' => Configure::read('Asset.cacheTime')]))
            ->add(new RoutingMiddleware($this))
            ->add(new BodyParserMiddleware())
            ->add($csrf);
    }

    public function services(ContainerInterface $container): void
    {
    }
}
