<?php

declare(strict_types=1);

namespace Hschulz\Router\Route;

use Hschulz\Http\Request\Request;
use Hschulz\Router\Route\AbstractRoute;

class StaticRoute extends AbstractRoute
{
    public function matches(Request $request): bool {

        $isRouteMatch = $this->path === $request->getRequestURI();

        return parent::matches($request) && $isRouteMatch;
    }

    protected function parseParam() {}
}
