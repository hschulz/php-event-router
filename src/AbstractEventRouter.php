<?php

declare(strict_types=1);

namespace Hschulz\Router;

use Hschulz\Config\ConfigurationManager;
use Hschulz\Dispatcher\EventDispatcher;
use Hschulz\Dispatcher\EventDispatcherInterface;
use Hschulz\Http\Request\Request;
use Hschulz\Router\EventRouterInterface;
use Hschulz\Router\Route\Factory;
use Hschulz\Router\Route\RouteInterface;
use Hschulz\Router\RoutingEvent;
use InvalidArgumentException;

use function array_merge;
use function array_search;
use function count;

/**
 * Abstract event router class.
 */
abstract class AbstractEventRouter implements EventRouterInterface
{
    /**
     * The routes.
     *
     * @var array
     */
    protected array $routes = [];

    /**
     * The event dispatcher.
     *
     * @var EventDispatcherInterface
     */
    protected EventDispatcherInterface $dispatcher;

    /**
     * The configuration handler.
     *
     * @var ConfigurationManager | null
     */
    protected ?ConfigurationManager $config = null;

    /**
     * Constructor
     *
     * @param EventDispatcherInterface | null $dispatcher An event dispatcher
     */
    public function __construct(?EventDispatcherInterface $dispatcher = null)
    {
        $this->routes = [];
        $this->config = null;
        $this->setDispatcher($dispatcher ?? new EventDispatcher());
    }

    /**
     * Returns the event dispatcher.
     *
     * @return EventDispatcherInterface
     */
    public function getDispatcher(): EventDispatcherInterface
    {
        return $this->dispatcher;
    }

    /**
     * Sets the event dispatcher.
     * Also sets the event listener for the get route event.
     *
     * @param EventDispatcherInterface $dispatcher The event dispatcher
     * @return void
     */
    public function setDispatcher(EventDispatcherInterface $dispatcher): void
    {
        $this->dispatcher = $dispatcher;

        $this->dispatcher->getListenerProvider()->addListener(
            RoutingEvent::EVENT_GET_ROUTE,
            [$this, 'onGetRoute']
        );
    }

    /**
     * Returns the configuration handler.
     *
     * @return ConfigurationManager
     */
    public function getConfigurationHandler(): ConfigurationManager
    {
        return $this->config;
    }

    /**
     * Sets the configuration handler.
     *
     * @param ConfigurationManager $config The configuration handler
     * @return void
     */
    public function setConfiguationHandler(ConfigurationManager $config): void
    {
        $this->config = $config;

        $routes = $config['Router']['routes'] ?? [];

        if (!empty($routes)) {

            // @toto decouple
            $routeFactory = new Factory($this->config);

            for ($i = 0, $c = count($routes); $i < $c; $i++) {
                $routeFactory->makeRoute($routes[$i]);
            }

            $this->routes = array_merge(
                $this->routes,
                $routeFactory->getRoutes()
            );
        }
    }

    /**
     * Event listener for the get route event.
     *
     * @param RoutingEvent $event The routing event
     * @return RoutingEvent The modified routing event
     */
    public function onGetRoute(RoutingEvent $event): RoutingEvent
    {
        $route = $this->getRoute($event->getRequest());

        if ($route === null) {

            $routeErrorEvent = new RoutingEvent();
            $routeErrorEvent->setName(RoutingEvent::EVENT_ROUTE_ERROR);
            $routeErrorEvent->setRequest($event->getRequest());

            $this->dispatcher->dispatch($routeErrorEvent);

            return $event;
        }

        $routeFoundEvent = new RoutingEvent();
        $routeFoundEvent->setName(RoutingEvent::EVENT_ROUTE_FOUND);
        $routeFoundEvent->setRequest($event->getRequest());
        $routeFoundEvent->setRoute($route);
        
        $this->dispatcher->dispatch($routeFoundEvent);

        $event->setRoute($route);

        return $event;
    }

    /**
     * Returns the route for the provided request.
     *
     * @param Request $request The request
     * @return RouteInterface | null The route or null if no route was found
     */
    public function getRoute(Request $request): ?RouteInterface
    {
        foreach ($this->routes as $route) {
            /** @var $route RouteInterface */

            if ($route->matches($request)) {

                return $route;
            }
        }

        return null;
    }

    /**
     * Adds multiple routes.
     *
     * @throws InvalidArgumentException If a route is not an instance of RouteInterface
     * @param array $routes The routes to add
     * @return void
     */
    public function addRoutes(array $routes): void
    {
        for ($i = 0, $c = count($routes); $i < $c; $i++) {

            if (!$routes[$i] instanceof RouteInterface) {
                throw new InvalidArgumentException();
            }

            $this->addRoute($routes[$i]);
        }
    }

    /**
     * Adds a route.
     *
     * @param RouteInterface $route The route to add
     * @return void
     */
    public function addRoute(RouteInterface $route): void
    {
        $this->routes[] = $route;
    }

    /**
     * Deletes a route.
     *
     * @param RouteInterface $route The route to delete
     * @return bool True if the route was deleted, false otherwise
     */
    public function deleteRoute(RouteInterface $route): bool
    {
        $index = array_search($route, $this->routes, true);

        if ($index !== false) {
            unset($this->routes[$index]);
        }

        return $index !== false;
    }

    /**
     * Returns all routes.
     *
     * @return array The routes
     */
    public function getRoutes(): array
    {
        return $this->routes;
    }
}
