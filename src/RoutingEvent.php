<?php

declare(strict_types=1);

namespace Hschulz\Router;

use Hschulz\Dispatcher\AbstractEvent;
use Hschulz\Http\Response\Response;
use Hschulz\Http\Request\Request;
use Hschulz\Router\Route\RouteInterface;

/**
 * Routing event class.
 */
class RoutingEvent extends AbstractEvent
{
    /**
     * Event name for getting a route.
     * @var string
     */
    public const EVENT_GET_ROUTE = 'hschulz.router.route-get';

    /**
     * Event name for an error while routing.
     * @var string
     */
    public const EVENT_ROUTE_ERROR = 'hschulz.router.route-error';

    /**
     * Event name for a found route.
     * @var string
     */
    public const EVENT_ROUTE_FOUND = 'hschulz.router.route-found';

    /**
     * The request.
     *
     * @var Request|null
     */
    protected ?Request $request = null;

    /**
     * The response.
     *
     * @var Response|null
     */
    protected ?Response $response = null;

    /**
     * The route.
     *
     * @var RouteInterface|null
     */
    protected ?RouteInterface $route = null;

    /**
     * Constructor
     * 
     * @param string $name The event name
     * @param array $params The event parameters
     * @param Request|null $request The request
     * @param RouteInterface|null $route The route
     * @param Response|null $response The response
     */
    public function __construct(
        string $name = '',
        array $params = [],
        ?Request $request = null,
        ?RouteInterface $route = null,
        ?Response $response = null
    ) {
        parent::__construct($name, $params);
        
        $this->request  = $request;
        $this->response = $response;
        $this->route    = $route;
    }

    /**
     * Returns the request.
     *
     * @return Request | null The request
     */
    public function getRequest(): ?Request
    {
        return $this->request;
    }

    /**
     * Sets the request.
     *
     * @param Request $request The request
     * @return void
     */
    public function setRequest(Request $request): void
    {
        $this->request = $request;
    }

    /**
     * Returns the route.
     *
     * @return RouteInterface | null The route
     */
    public function getRoute(): ?RouteInterface
    {
        return $this->route;
    }

    /**
     * Sets the route.
     *
     * @param RouteInterface $route The route
     * @return void
     */
    public function setRoute(RouteInterface $route): void
    {
        $this->route = $route;
    }

    /**
     * Returns the response.
     *
     * @return Response | null The response
     */
    public function getResponse(): ?Response
    {
        return $this->response;
    }

    /**
     * Sets the response.
     *
     * @param Response $response The response
     * @return void
     */
    public function setResponse(Response $response): void
    {
        $this->response = $response;
    }
}
