<?php

namespace Hschulz\Router\Tests\Integration;

use Hschulz\Config\JSONConfigurationManager;
use Hschulz\Dispatcher\EventDispatcher;
use Hschulz\Http\Request\Request;
use Hschulz\Router\EventRouter;
use Hschulz\Router\Route\StaticRoute;
use Hschulz\Router\RoutingEvent;
use InvalidArgumentException;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;

final class EventRouterTest extends TestCase
{
    /**
     *
     * @var EventDispatcher|null
     */
    protected ?EventDispatcher $dispatcher = null;

    /**
     *
     * @var JSONConfigurationManager|null
     */
    protected ?JSONConfigurationManager $config = null;

    /**
     *
     * @var string
     */
    protected string $file = '';

    protected function setUp(): void
    {
        $this->dispatcher = new EventDispatcher();

        vfsStream::setup('integration');

        $this->file = vfsStream::url('integration/config.json');

        file_put_contents($this->file, '{"Router":{"config":{"strict":true},"plugins":{"routes":{"Segment":"Hschulz\\\\Router\\\\Route\\\\SegmentedRoute","Static":"Hschulz\\\\Router\\\\Route\\\\StaticRoute"},"controller":{"home":""}},"routes":[{"name":"default","type":"Static","controller":"home","action":"show","path":"/"},{"name":"home","scheme":"http","domain":"localhost","port":"88","path":"/","type":"Segment","controller":"home","action":"show","may_end":true,"methods":["GET","POST","PUT","DELETE","HEAD","OPTIONS"]},{"name":"meep","path":"/derp/herp/merp","type":"Static","controller":"home","action":"show","may_end":true,"scheme":"http","domain":"localhost","port":"88","methods":["POST"]},{"name":"imprint","path":"/imprint","type":"Static","controller":"home","action":"show","may_end":true,"scheme":"http","domain":"localhost","port":"88"}]}}');

        $this->config = new JSONConfigurationManager($this->file, 'integration');

        $this->config['Router']['routes'][1]['segments'][] = [
            'name' => 'test',
            'path' => ':argument',
            'type' => 'Segment',
            'controller' => '',
            'action' => ''
        ];
    }

    protected function tearDown(): void
    {
        $this->dispatcher = null;
        $this->config = null;
        $this->file = '';
    }

    public function testCanBeCreatedWithEventManager(): void
    {
        $router = new EventRouter($this->dispatcher);
        $router->setConfiguationHandler($this->config);

        $this->assertInstanceOf(EventDispatcher::class, $router->getDispatcher());
    }

    public function testCanAddRoutes(): void
    {
        $router = new EventRouter($this->dispatcher);

        $router->addRoutes([new StaticRoute('/unit/test'), new StaticRoute('/unit/')]);

        $this->assertEquals(2, count($router->getRoutes()));
    }

    public function testCanNotAddInvalidValueAsRoute(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $router = new EventRouter($this->dispatcher);

        $router->addRoutes(['test']);
    }

    public function testCanDeleteRoute(): void
    {
        $router = new EventRouter($this->dispatcher);

        $router->addRoute(new StaticRoute('/'));

        $routes = $router->getRoutes();

        $this->assertTrue($router->deleteRoute($routes[0]));

        $this->assertEmpty($router->getRoutes());
    }

    public function testCanSetConfiguration(): void
    {
        $router = new EventRouter($this->dispatcher);

        $router->setConfiguationHandler($this->config);

        $this->assertEquals(5, count($router->getRoutes()));

        $this->assertEquals($this->config, $router->getConfigurationHandler());
    }

    public function testCanGetRouteOnEvent(): void
    {
        $router = new EventRouter($this->dispatcher);
        $router->setConfiguationHandler($this->config);

        $request = new Request();
        $request->setRequestUri('/');

        $event = new RoutingEvent(RoutingEvent::EVENT_GET_ROUTE, [], $request);

        $result = $this->dispatcher->dispatch($event);

        $route = $result->getRoute();

        $this->assertNotNull($route);
        $this->assertEquals(RoutingEvent::EVENT_GET_ROUTE, $result->getName());
        $this->assertInstanceOf(StaticRoute::class, $route);
    }

    public function testCanNotGetUnknownRouteOnEvent() {

        $router = new EventRouter($this->dispatcher);
        $router->setConfiguationHandler($this->config);

        $request = new Request();
        $request->setRequestUri('/integration/test');

        $event = new RoutingEvent(RoutingEvent::EVENT_GET_ROUTE, [], $request);

        $result = $this->dispatcher->dispatch($event);

        $route = $result->getRoute();

        $this->assertNull($route);
        $this->assertEquals(RoutingEvent::EVENT_GET_ROUTE, $event->getName());
    }
}
