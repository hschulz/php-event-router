<?php

declare(strict_types=1);

namespace Hschulz\Router\Route;

use Hschulz\Router\Route\AbstractRoute;
use Hschulz\Http\Request\Request;

/**
 * SegmentedRoute
 */
class SegmentedRoute extends AbstractRoute
{
    /**
     * Parses the path for parameters.
     * 
     * @return void
     */
    protected function parseParam(): void
    {
        /* if there is a segment notation */
        if (stripos($this->path, ':') !== false) {

            $parts = explode(':', $this->path);

            foreach ($parts as $part) {

                if ($part === '/') {
                    continue;
                }

                if (stripos($part, '/') !== false) {

                     $staticParts = array_filter(explode('/', $part));

                     if (!empty($staticParts)) {

                        foreach ($staticParts as $staticPart) {

                            $this->params[$staticPart] = null;
                        }
                    }

                } else {
                    $this->params[$part] = null;
                }
            }
        }
    }

    /**
     * Matches the request against the route.
     * 
     * @param Request $request The request to match
     * @return bool True if the request matches the route, false otherwise
     */
    public function matches(Request $request): bool
    {
        $isParentMatch = parent::matches($request);

        if (!$isParentMatch) {
            return false;
        }

        $this->parseParam();

        $uri = $request->getRequestURI();

        if (strlen($uri) > 1 && $uri[0] === '/') {
            $uri = substr($uri, 1);
        }

        $parts = explode('/', $uri);

        $partCount = count($parts);
        $paramCount = count($this->params);

        if ($partCount === $paramCount) {

            $this->params = array_combine(array_keys($this->params), $parts);

            return true;
        }

        return false;
    }
}
