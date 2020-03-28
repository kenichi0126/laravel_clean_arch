<?php

namespace Switchm\Php\Illuminate\Http\Middleware;

use Closure;
use Illuminate\Routing\Router;

class PresenterOutput
{
    private $data;

    private $router;

    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    public function set($data): void
    {
        $this->data = $data;
    }

    public function handle($request, Closure $next)
    {
        $response = $next($request);

        if ($this->data === null) {
            return $response;
        }

        return $this->router->prepareResponse($this->router->getCurrentRequest(), $this->data);
    }
}
