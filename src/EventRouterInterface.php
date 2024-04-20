<?php

declare(strict_types=1);

namespace Hschulz\Router;

use Hschulz\Config\Configurable;
use Hschulz\Http\Request\Request;
use Hschulz\Router\RoutingEvent;
use Hschulz\Router\Route\RouteInterface;

/**
 * Interface for an event router.
 */
interface EventRouterInterface extends Configurable
{
    /**
     * Event listener for the get route event.
     *
     * @param RoutingEvent $event The event to handle
     * @return RoutingEvent The modified event
     */
    public function onGetRoute(RoutingEvent $event): RoutingEvent;

    /**
     * Returns the route for the provided request.
     *
     * @param Request $request The request to get the route for
     * @return RouteInterface | null The route for the request or null
     */
    public function getRoute(Request $request): ?RouteInterface;

    /**
     * Adds a route to the router.
     *
     * @param RouteInterface $route The route to add
     * @return void
     */
    public function addRoute(RouteInterface $route): void;

    /**
     * Deletes a route from the router.
     *
     * @param RouteInterface $route The route to delete
     * @return bool True if the route was deleted, false otherwise
     */
    public function deleteRoute(RouteInterface $route): bool;
}
